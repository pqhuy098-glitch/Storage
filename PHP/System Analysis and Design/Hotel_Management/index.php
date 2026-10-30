<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'models/HotelUser.php';
require_once 'models/Room.php';
require_once 'models/Booking.php';
require_once 'models/Customer.php';
require_once 'models/Review.php';

require_once 'controllers/SearchReserveRoomController.php';
require_once 'views/SearchReserveRoomUI.php';
require_once 'controllers/CancelBookingController.php';
require_once 'views/CancelBookingUI.php';
require_once 'controllers/RoomServiceController.php';
require_once 'views/RoomServiceUI.php';
require_once 'controllers/CheckInController.php';
require_once 'views/CheckInUI.php';
require_once 'controllers/CheckOutController.php';
require_once 'views/CheckOutUI.php';
require_once 'controllers/ReviewController.php';
require_once 'views/ReviewUI.php';

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=Hotel;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $controller = new SearchReserveRoomController($pdo);
    $ui = new SearchReserveRoomUI($controller);

    $cancelController = new CancelBookingController($pdo);
    $cancelUI = new CancelBookingUI($cancelController);

    $checkInController = new CheckInController($pdo);
    $checkInUI = new CheckInUI($checkInController);

    $serviceController = new RoomServiceController($pdo);
    $serviceUI = new RoomServiceUI($serviceController);

    $checkOutController = new CheckOutController($pdo);
    $checkOutUI = new CheckOutUI($checkOutController);

    $reviewController = new ReviewController($pdo);
    $reviewUI = new ReviewUI($reviewController);

    $isLoggedIn = $controller->checkLoginStatus();
    $loggedUser = $_SESSION['user_email'] ?? '';
    $userRole = $_SESSION['user_role'] ?? '';

    $action = $_GET['action'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="main-container">
    <nav>
        <a href="index.php" class="nav-brand">Hotel Management</a>
        <div class="nav-links">
            <?php if ($isLoggedIn && $userRole === 'Receptionist'): ?>
                <a href="index.php?action=openCheckIn">Check-in</a>
                <a href="index.php?action=openCheckOut">Check-out</a>
                <span class="user-badge">Staff: <?= htmlspecialchars($loggedUser) ?></span>
                <a href="index.php?action=logout">Logout</a>
            
            <?php elseif ($isLoggedIn): ?>
                <a href="index.php">Home</a>
                <a href="index.php?action=history">History</a>
                <a href="index.php?action=roomServices">Room Services</a>
                <a href="index.php?action=reviewHistory">My Reviews</a>
                <span class="user-badge">Account: <?= htmlspecialchars($loggedUser) ?></span>
                <a href="index.php?action=logout">Logout</a>
            
            <?php else: ?>
                <a href="index.php">Home</a>
                <a href="index.php?action=loginForm">Login</a>
                <a href="index.php?action=registerForm">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php
    switch ($action) {

        // --- Search & Reserve ---
        case 'search':
            $checkIn = $_GET['checkIn'] ?? '';
            $checkOut = $_GET['checkOut'] ?? '';
            $guests = (int)($_GET['guests'] ?? 1);
            
            $_SESSION['last_search'] = [
                'checkIn' => $checkIn,
                'checkOut' => $checkOut,
                'guests' => $guests
            ];
            $ui->enterSearchCriteria($checkIn, $checkOut, $guests);
            break;

        case 'selectRoom':
            $roomNumber = $_GET['roomNumber'] ?? '';
            $ui->selectRoom($roomNumber);
            break;

        case 'bookNow':
            $roomNumber = $_GET['roomNumber'] ?? '';
            $ui->clickBookNow($roomNumber);
            break;

        case 'loginForm':
            $ui->registerLoginPage();
            break;

        case 'doLogin':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $ui->enterCredentials($email, $password);
            }
            break;

        case 'registerForm':
            echo '<h3>Create a New Account</h3>
                  <form method="POST" action="index.php?action=doRegister">
                      <div class="form-group"><label>Full Name:</label><input type="text" name="fullName" required placeholder="John Doe"></div>
                      <div class="form-group"><label>Email Address:</label><input type="email" name="email" required placeholder="name@example.com"></div>
                      <div class="form-group"><label>Password:</label><input type="password" name="password" required placeholder="Create a password"></div>
                      <div class="form-group"><label>National ID / Passport:</label><input type="text" name="nationalID" required placeholder="ID Card Number"></div>
                      <div class="form-group"><label>Phone Number:</label><input type="text" name="phoneNumber" required placeholder="+1 234 567 890"></div>
                      <button type="submit">Register Account</button>
                  </form>';
            break;

        case 'doRegister':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ui->enterRegistrationInfo(
                    $_POST['fullName'] ?? '',
                    $_POST['email'] ?? '',
                    $_POST['password'] ?? '',
                    $_POST['nationalID'] ?? '',
                    $_POST['phoneNumber'] ?? ''
                );
            }
            break;

        case 'confirmBooking':
            $email = $_SESSION['user_email'] ?? '';
            $customerID = $_SESSION['customerID'] ?? '';

            if (empty($customerID) && !empty($email)) {
                $stmt = $pdo->prepare("SELECT customerID FROM Customer WHERE email = ?");
                $stmt->execute([$email]);
                $cust = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($cust) {
                    $customerID = $cust['customerID'];
                    $_SESSION['customerID'] = $customerID;
                }
            }

            $roomNumber = $_GET['roomNumber'] ?? $_SESSION['selected_roomNumber'] ?? '';
            $checkIn = $_SESSION['last_search']['checkIn'] ?? date('Y-m-d');
            $checkOut = $_SESSION['last_search']['checkOut'] ?? date('Y-m-d', strtotime('+1 day'));
            $guests = $_SESSION['last_search']['guests'] ?? 1;

            if (!empty($customerID) && !empty($roomNumber)) {
                $ui->confirmBooking($customerID, $roomNumber, $checkIn, $checkOut, $guests);
            } else {
                $ui->displayErrorMessage("Missing booking information.");
                echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
            }
            break;

        // --- History & Cancellation ---
        case 'history':
            $cancelUI->openHistory();
            break;

        case 'cancelDetails':
            $bookingID = $_GET['bookingID'] ?? '';
            $cancelUI->selectBooking($bookingID);
            break;

        case 'doCancel':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $bookingID = $_GET['bookingID'] ?? '';
                $cancelUI->confirmCancellation($bookingID);
            }
            break;

        // --- Receptionist: Check-in / Check-out ---
        case 'receptionistPage':
            $checkInUI->displayReceptionistPage();
            break;

        case 'openCheckIn':
            $checkInUI->openCheckInPage();
            break;

        case 'doLoginReceptionist':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $checkInUI->enterCredentials($email, $password);
            }
            break;

        case 'doCheckInSearch':
            $info = $_GET['customerInfo'] ?? '';
            $checkInUI->enterCustomerInfo($info);
            break;

        case 'confirmCheckInAction':
            $bookingID = $_GET['bookingID'] ?? '';
            $checkInUI->confirmCheckIn($bookingID);
            break;

        case 'openCheckOut':
            $checkOutUI->openCheckOutPage();
            break;

        case 'doLoginCheckOut':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $checkOutUI->enterCredentials($email, $password);
            }
            break;

        case 'doCheckOutSearch':
            $info = $_GET['customerInfo'] ?? '';
            $checkOutUI->enterCustomerInfo($info);
            break;

        case 'processCheckOut':
            $bookingID = $_GET['bookingID'] ?? '';
            $checkOutUI->confirmCheckOut($bookingID);
            break;

        // --- Room Services ---
        case 'roomServices':
            $serviceUI->openServiceRequestSection();
            break;

        case 'selectServiceRoom':
            $roomNumber = $_GET['roomNumber'] ?? '';
            $serviceUI->selectRoom($roomNumber);
            break;

        case 'addServiceItem':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $serviceID = $_GET['serviceID'] ?? '';
                $quantity = (int)($_POST['quantity'] ?? 1);
                $serviceController->addService($serviceID, $quantity);
                $roomNumber = $_SESSION['active_service_roomNumber'] ?? '';
                header("Location: index.php?action=selectServiceRoom&roomNumber=" . urlencode($roomNumber));
                exit;
            }
            break;

        case 'removeServiceItem':
            $serviceID = $_GET['serviceID'] ?? '';
            $serviceController->removeService($serviceID);
            $roomNumber = $_SESSION['active_service_roomNumber'] ?? '';
            header("Location: index.php?action=selectServiceRoom&roomNumber=" . urlencode($roomNumber));
            exit;

        case 'confirmRoomService':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $bookingID = $_GET['bookingID'] ?? '';
                if ($serviceController->confirmServiceRequest($bookingID)) {
                    $serviceUI->displaySuccessMessage();
                    echo '<br><a href="index.php?action=history" class="btn">View History</a>';
                } else {
                    $serviceUI->displayErrorMessage("Failed to confirm service request.");
                }
            }
            break;

        // --- Review & Feedback Module ---
        case 'reviewHistory':
            $reviewUI->openHistory();
            break;

        case 'openReviewForm':
            $bookingID = $_GET['bookingID'] ?? '';
            $reviewUI->selectBooking($bookingID);
            break;

        case 'doSubmitReview':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $bookingID = $_GET['bookingID'] ?? '';
                $customerID = $_SESSION['customerID'] ?? '';
                $roomQuality = (int)($_POST['roomQuality'] ?? 5);
                $staffAttitude = (int)($_POST['staffAttitude'] ?? 5);
                $generalAmenities = (int)($_POST['generalAmenities'] ?? 5);
                $comment = $_POST['comment'] ?? '';

                $reviewUI->enterRatings($roomQuality, $staffAttitude, $generalAmenities);
                $reviewUI->enterComment($comment);

                $reviewController->submitReview($bookingID, $customerID, $roomQuality, $staffAttitude, $generalAmenities, $comment);
                $reviewUI->submitReview();
            }
            break;

        case 'doUpdateReview':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $bookingID = $_GET['bookingID'] ?? '';
                $existingReview = $reviewController->getReview($bookingID);
                
                if ($existingReview) {
                    $roomQuality = (int)($_POST['roomQuality'] ?? 5);
                    $staffAttitude = (int)($_POST['staffAttitude'] ?? 5);
                    $generalAmenities = (int)($_POST['generalAmenities'] ?? 5);
                    $comment = $_POST['comment'] ?? '';

                    $reviewUI->enterRatings($roomQuality, $staffAttitude, $generalAmenities);
                    $reviewUI->enterComment($comment);

                    $reviewController->updateReview($existingReview['reviewID'], $roomQuality, $staffAttitude, $generalAmenities, $comment);
                    $reviewUI->submitUpdatedReview();
                }
            }
            break;

        // --- Utility ---
        case 'logout':
            session_destroy();
            header("Location: index.php");
            exit;

        case 'home':
        default:
            $ui->openHomePage();
            break;
    }
    ?>
</div>

</body>
</html>
<?php
} catch (PDOException $e) {
    echo "<div style='font-family: sans-serif; color: red; padding: 20px;'>Database connection failed: " . $e->getMessage() . "</div>";
}
?>