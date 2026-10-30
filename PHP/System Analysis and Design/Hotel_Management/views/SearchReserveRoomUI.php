<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class SearchReserveRoomUI {
    private $controller;

    public function __construct($controller){
        $this->controller = $controller;
    }

    public function openHomePage(): void {
        echo "<h2>Hotel Management System</h2>";
        echo '<p>Find and book comfortable rooms for your stay. Please enter your schedule details below to check available rooms.</p>
              <form method="GET" action="index.php" class="search-form-inline">
                  <input type="hidden" name="action" value="search">
                  <div class="form-row">
                      <div class="form-group-inline">
                          <label>Check-in Date:</label>
                          <input type="date" name="checkIn" required min="' . date('Y-m-d') . '">
                      </div>
                      <div class="form-group-inline">
                          <label>Check-out Date:</label>
                          <input type="date" name="checkOut" required min="' . date('Y-m-d', strtotime('+1 day')) . '">
                      </div>
                      <div class="form-group-inline">
                          <label>Guests:</label>
                          <input type="number" name="guests" value="1" min="1" required>
                      </div>
                      <div class="form-group-button">
                          <button type="submit">Search Available Rooms</button>
                      </div>
                  </div>
              </form>';
    }

    public function enterSearchCriteria($checkIn, $checkOut, int $guests): void {
        if (!$this->controller->validateDates($checkIn, $checkOut)) {
            $this->displayErrorMessage("Invalid check-in/check-out dates (the check-in date cannot be in the past; for same-day bookings, the time must be before 8:00 PM; and the check-out date must be after the check-in date).");
            echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
            return;
        }

        $rooms = $this->controller->searchRooms($checkIn, $checkOut, $guests);
    
        if (empty($rooms)) {
            $this->displayNoRoomsMessage();
        } 
        else {
            $this->displaySearchResults($rooms);
        }
        echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
    }

    public function displaySearchResults(array $rooms): void {
        echo "<h3>Available Rooms</h3>";
        if (empty($rooms)) {
            echo "<p>Unfortunately, no available rooms match your requirements.</p>";
            return;
        }

        echo '<div class="room-grid">';
        foreach ($rooms as $room) {
            
            $roomNumberUpper = strtoupper(htmlspecialchars($room->roomNumber));
            
            echo '<div class="room-card">
                    <div class="room-card-header">
                        <h4>Room ' . $roomNumberUpper . '</h4>
                        <span class="room-price">' . number_format($room->pricePerNight, 0, ',', '.') . ' VND<small>/night</small></span>
                    </div>
                    <div class="room-card-body">
                        <p><strong>Bed Type:</strong> ' . htmlspecialchars($room->bedType) . '</p>
                        <p><strong>Max Guests:</strong> ' . htmlspecialchars($room->maxGuests) . '</p>
                    </div>
                    <div class="room-card-footer">
                        <button onclick="location.href=\'?action=selectRoom&roomNumber=' . urlencode($room->roomNumber) . '\'">More Details</button>
                    </div>
                </div>';
        }
        echo '</div>';
    }

    public function selectRoom(string $roomNumber): void {
        $room = $this->controller->getRoomDetails($roomNumber);
        $reviews = $this->controller->getRoomReviews($roomNumber);
        
        $this->displayRoomDetails($room, $reviews);

        if (isset($_SESSION['last_search'])) {
            $s = $_SESSION['last_search'];
            $backListUrl = "index.php?action=search&checkIn={$s['checkIn']}&checkOut={$s['checkOut']}&guests={$s['guests']}";
            echo '<br><a href="' . $backListUrl . '" class="back-link">&larr; Back to List</a>';
        } else {
            echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
        }
    }

    public function displayRoomDetails($room, array $reviews): void {
        if (!$room) {
            $this->displayErrorMessage("Room information not found.");
            return;
        }

        echo '<div style="max-width: 700px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; font-family: inherit;">';
        
        echo '<div style="color: var(--primary-color, #007bff); padding: 20px 25px;">';
        echo '<h3 style="margin: 0; font-size: 1.6rem;">Room Details: ' . htmlspecialchars($room->roomNumber) . '</h3>';
        
        $priceVND = is_numeric($room->pricePerNight) ? number_format((float)$room->pricePerNight, 0, ',', '.') : $room->pricePerNight;
        echo '<p style="margin: 8px 0 0 0; font-size: 1.2rem; font-weight: bold;">' . $priceVND . ' VND <span style="font-size: 0.9rem; font-weight: normal; opacity: 0.9;">/ night</span></p>';
        echo '</div>';

        echo '<div style="padding: 25px;">';
        
        echo '<h4 style="margin-top: 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;">Amenities</h4>';
        echo '<p style="color: #555; line-height: 1.5; margin-bottom: 25px;">' . nl2br(htmlspecialchars($room->amenities)) . '</p>';

        echo '<h4 style="color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px; margin-bottom: 15px;">Customer Reviews</h4>';
        
        if (empty($reviews)) {
            echo '<p style="color: #777; font-style: italic;">There are no reviews for this room yet.</p>';
        } else {
            echo '<div style="display: flex; flex-direction: column; gap: 12px;">';
            foreach ($reviews as $review) {
                echo '<div style="background: #f8f9fa; padding: 12px 15px; border-radius: 6px; border-left: 4px solid var(--primary-color, #007bff);">';
                echo '<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">';
                echo '<span style="font-weight: bold; color: #e67e22;">★ ' . htmlspecialchars($review->roomQualityRating) . '/5</span>';
                echo '</div>';
                echo '<p style="margin: 0; color: #444; font-size: 0.95rem;">' . htmlspecialchars($review->comment) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        }

        echo '</div>';

        echo '<div style="background: #f8f9fa; padding: 15px 25px; text-align: right; border-top: 1px solid #eee;">';
        echo '<button onclick="location.href=\'?action=bookNow&roomNumber=' . urlencode($room->roomNumber) . '\'" style="background-color: #28a745; color: white; border: none; padding: 12px 30px; font-size: 1rem; font-weight: bold; border-radius: 4px; cursor: pointer; transition: background 0.2s;">Book Now</button>';
        echo '</div>';

        echo '</div>';
    }

    public function clickBookNow(string $roomNumber): void {
        if (!$this->controller->checkLoginStatus()) {
            if (session_status() === PHP_SESSION_NONE) { 
                session_start(); 
            }
            $_SESSION['redirect_after_login'] = "index.php?action=bookNow&roomNumber=" . $roomNumber;
        
            $this->registerLoginPage();
    
        } 
        else {

            // echo "<pre>";
            // echo "SESSION:\n";
            // print_r($_SESSION);
            // echo "</pre>";
            $_SESSION['selected_roomNumber'] = $roomNumber;

            $checkIn = $_SESSION['last_search']['checkIn'] ?? date('Y-m-d');
            $checkOut = $_SESSION['last_search']['checkOut'] ?? date('Y-m-d', strtotime('+1 day'));
            $guests = $_SESSION['last_search']['guests'] ?? 1;

            $customerID = $_SESSION['customerID'];

            $booking = $this->controller->prepareBookingConfirmation($customerID, $roomNumber, $checkIn, $checkOut, $guests);
            $customer = $this->controller->getCustomerInfo($customerID);

            $this->displayBookingConfirmation($booking, $customer, $roomNumber);
        }
        echo '<br><a href="index.php?action=selectRoom&roomNumber=' . htmlspecialchars($roomNumber) . '" class="back-link">&larr; Back to Details</a>';
    }

    public function registerLoginPage(): void {
        echo "<p>You are not logged in. Please log in or register an account to continue.</p>";
        echo '<h3>Customer Login</h3>
              <form method="POST" action="index.php?action=doLogin">
                  <div class="form-group">
                      <label>Email Address:</label>
                      <input type="email" name="email" required placeholder="name@example.com">
                  </div>
                  <div class="form-group">
                      <label>Password:</label>
                      <input type="password" name="password" required placeholder="Enter your password">
                  </div>
                  <button type="submit">Sign In</button>
              </form>';
    }

    public function enterCredentials(string $email, string $password): void {
        $success = $this->controller->login($email, $password);
        if ($success) {
            
            if (isset($_SESSION['redirect_after_login'])) {
                $redirectUrl = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirectUrl);
                exit;
            }
        
            echo "<p>Login successful!</p>";
            echo '<br><a href="index.php" class="btn">Go to Dashboard / Home</a>';
        } 
        else {
            $this->displayErrorMessage("Email or password is not correct.");
        }
    }

    public function enterRegistrationInfo(string $fullName, string $email, string $password, string $nationalID, string $phoneNumber): void {
        $customer = $this->controller->registerCustomer($fullName, $email, $password, $nationalID, $phoneNumber);
        if ($customer) {
            $this->displaySuccessMessage();
            echo '<br><a href="index.php?action=loginForm" class="btn">Proceed to Login</a>';
        } 
        else {
            $this->displayErrorMessage("Registration failed. The email may already exist.");
        }
    }

    public function displayBookingConfirmation($booking, $customer): void {
        echo '<div style="max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; font-family: inherit;">';
        
        echo '<div style="color: var(--primary-color, #007bff); padding: 20px; text-align: center;">';
        echo '<h3 style="margin: 0; font-size: 1.5rem;">Booking Confirmation</h3>';
        echo '<p style="margin: 5px 0 0 0; font-size: 0.95rem; opacity: 0.9;">Please review your booking details below</p>';
        echo '</div>';

        echo '<div style="padding: 25px;">';
        
        echo '<h4 style="margin-top: 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;">Customer Information</h4>';
        echo '<p><strong>Full Name:</strong> ' . htmlspecialchars($customer->fullname) . '</p>';
        echo '<p><strong>Email:</strong> ' . htmlspecialchars($customer->email) . '</p>';
        echo '<p><strong>Phone Number:</strong> ' . htmlspecialchars($customer->phoneNumber) . '</p>';
        echo '<p><strong>National ID:</strong> ' . htmlspecialchars($customer->nationalID) . '</p>';

        echo '<br>';

        echo '<h4 style="margin-top: 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;">Booking Details</h4>';
        echo '<p><strong>Booking Reference:</strong> ' . htmlspecialchars($booking->bookingID) . '</p>';
        echo '<p><strong>Check-in Date:</strong> ' . htmlspecialchars($booking->checkInDate) . '</p>';
        echo '<p><strong>Check-out Date:</strong> ' . htmlspecialchars($booking->checkOutDate) . '</p>';

        echo '<hr style="border: 0; border-top: 1px dashed #ddd; margin: 20px 0;">';

        $totalAmountVND = is_numeric($booking->totalAmount) ? number_format((float)$booking->totalAmount, 0, ',', '.') : $booking->totalAmount;
        echo '<div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.2rem; font-weight: bold; color: #2c3e50;">';
        echo '<span>Total Amount:</span>';
        echo '<span style="color: var(--primary-color, #007bff);">' . $totalAmountVND . ' VND</span>';
        echo '</div>';

        echo '</div>';

        echo '<div style="background: #f8f9fa; padding: 15px 25px; text-align: right; border-top: 1px solid #eee;">';
        echo '<button onclick="location.href=\'?action=confirmBooking\'" style="background-color: #28a745; color: white; border: none; padding: 12px 25px; font-size: 1rem; font-weight: bold; border-radius: 4px; cursor: pointer; transition: background 0.2s;">Confirm Booking</button>';
        echo '</div>';

        echo '</div>';
    }

    public function confirmBooking($customerID, $roomNumber, $checkIn, $checkOut, int $guests): void {
        $booking = $this->controller->confirmBooking($customerID, $roomNumber, $checkIn, $checkOut, $guests);
        if ($booking) {
            $this->displaySuccessMessage();
            $this->displayBookingInHistory($booking);
        } 
        else {
            $this->displayErrorMessage("Unable to complete the booking. Please try again.");
        }

        if (isset($_SESSION['last_search'])) {
            $s = $_SESSION['last_search'];
            $backListUrl = "index.php?action=search&checkIn={$s['checkIn']}&checkOut={$s['checkOut']}&guests={$s['guests']}";
            echo '<br><a href="' . $backListUrl . '" class="back-link">&larr; Back to List</a>';
        } else {
            echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
        }
    }

    public function displaySuccessMessage(): void {
        echo "<div style='color: green;'>Success! Your transaction has been processed.</div>";
    }

    public function displayBookingInHistory($booking): void {
        echo "<h4>Recent booking history:</h4>";
        echo "<p>Order ID: {$booking->bookingID} - Status: {$booking->status}</p>";
    }

    public function displayErrorMessage(string $message): void {
        echo "<div style='color: red;'>Error: {$message}</div>";
    }

    public function displayNoRoomsMessage(): void {
        echo "<p>Unfortunately, no available rooms match your requirements.</p>";
    }
}