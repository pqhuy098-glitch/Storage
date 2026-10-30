<?php

class CheckOutUI {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
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

    public function displayReceptionistPage(): void {
        echo "<h3>Receptionist Dashboard - Check-out Section</h3>";
        echo '<p><a href="index.php?action=openCheckOut" class="btn">Go to Check-out Page</a></p>';
    }

    public function openCheckOutPage(): void {
        if (!$this->controller->checkReceptionistPermission('')) {
            echo "<h3>Receptionist Login for Check-out</h3>";
            echo '<form method="POST" action="index.php?action=doLoginCheckOut">
                      <div class="form-group"><label>Staff Email:</label><input type="email" name="email" required></div>
                      <div class="form-group"><label>Password:</label><input type="password" name="password" required></div>
                      <button type="submit">Sign In</button>
                  </form>';
            return;
        }

        echo "<h3>Guest Check-out & Billing</h3>";
        echo '<form method="GET" action="index.php">
                  <input type="hidden" name="action" value="doCheckOutSearch">
                  <div class="form-group">
                      <label>Customer Email or National ID:</label>
                      <input type="text" name="customerInfo" required placeholder="Enter email or ID">
                  </div>
                  <button type="submit">Search Checked-In Rooms</button>
              </form>';
    }

    public function enterCustomerInfo(string $info): void {
        $bookings = $this->controller->findBookingsForCheckOut($info);
        $this->displayBookings($bookings);
    }

    public function displayBookings(array $bookings): void {
        echo "<h3>Active Checked-In Bookings</h3>";
        if (empty($bookings)) {
            $this->displayErrorMessage("No active bookings found for check-out.");
            return;
        }

        echo '<div class="room-grid">';
        foreach ($bookings as $booking) {
            $checkIn = new DateTime($booking->checkInDate);
            $scheduledCheckOut = new DateTime($booking->checkOutDate);
            
            $actualOut = !empty($booking->actualCheckOutDate) 
                ? new DateTime($booking->actualCheckOutDate) 
                : new DateTime();

            $actualDays = $checkIn->diff($actualOut)->days;
            if ($actualDays <= 0) {
                $actualDays = 1;
            }

            $scheduledDays = $checkIn->diff($scheduledCheckOut)->days;

            $roomStmt = $GLOBALS['pdo']->prepare("SELECT pricePerNight FROM Room WHERE roomNumber = ?");
            $roomStmt->execute([$booking->roomNumber]);
            $room = $roomStmt->fetch(PDO::FETCH_ASSOC);
            $pricePerNight = (float)($room['pricePerNight'] ?? 0);

            $earlyDays = 0;
            $earlyDeduction = 0;

            if ($actualOut < $scheduledCheckOut && $actualDays < $scheduledDays) {
                $earlyDays = $scheduledDays - $actualDays;
                $earlyDeduction = $earlyDays * $pricePerNight;
            }

            $roomCost = $actualDays * $pricePerNight;

            $lateFee = (float)($booking->lateFee ?? 0);
            $serviceCharge = (float)($booking->serviceCharge ?? 0);

            $finalAmount = ($roomCost + $serviceCharge + $lateFee);

            echo '<div class="room-card">
                    <div class="room-card-header">
                        <h4>Booking: ' . htmlspecialchars($booking->bookingID) . '</h4>
                        <span class="room-price">Room ' . htmlspecialchars($booking->roomNumber) . '</span>
                    </div>
                    <div class="room-card-body">
                        <p><strong>Check-in:</strong> ' . htmlspecialchars($booking->checkInDate) . '</p>
                        <p><strong>Scheduled Check-out:</strong> ' . htmlspecialchars($booking->checkOutDate) . '</p>
                        <hr style="border: 0; border-top: 1px dashed #ddd; margin: 8px 0;">
                        <p><strong>Number of nights stayed:</strong> ' . $actualDays . ' nights</p>
                        <p><strong>Price per night:</strong> ' . number_format($pricePerNight, 0, ',', '.') . ' VND</p>
                        <p><strong>Total cost of stay:</strong> ' . number_format($roomCost, 0, ',', '.') . ' VND</p>
                        <p><strong>Service Charge:</strong> ' . number_format($serviceCharge, 0, ',', '.') . ' VND</p>';

            if ($earlyDays > 0) {
                echo '<hr style="border: 0; border-top: 1px dashed #ddd; margin: 8px 0;">';
                echo '<p style="color: green;"><strong>Number of early check-out nights:</strong> ' . $earlyDays . ' nights</p>';
                echo '<p style="color: green;"><strong>Early check-out deduction:</strong> -' . number_format($earlyDeduction, 0, ',', '.') . ' VND</p>';
            }

            echo '          <hr style="border: 0; border-top: 1px dashed #ddd; margin: 8px 0;">
                        <p style="font-size: 1.1rem; color: var(--primary-color);"><strong>Total Final Amount:</strong> ' . number_format($finalAmount, 0, ',', '.') . ' VND</p>
                    </div>
                    <div class="room-card-footer">
                        <button onclick="location.href=\'?action=processCheckOut&bookingID=' . urlencode($booking->bookingID) . '\'">Confirm Payment & Check-out</button>
                    </div>
                </div>';
        }
        echo '</div>';
    }

    public function displayFinalAmount($booking, float $amount): void {
        echo "<p>Final amount for Booking {$booking->bookingID}: <strong>{$amount}$</strong></p>";
    }

    public function confirmPayment(string $bookingID): void {
        $this->controller->verifyPayment($bookingID);
    }

    public function confirmCheckOut(string $bookingID): void {
        try {
            $this->controller->confirmCheckOut($bookingID);
            $this->displaySuccessMessage();
            echo '<br><a href="index.php?action=openCheckOut" class="btn">Back to Check-out</a>';
        } catch (Exception $e) {
            $this->displayErrorMessage("Check-out failed: " . $e->getMessage());
        }
    }

    public function displaySuccessMessage(): void {
        echo "<div style='color: green;'>Success! Check-out completed successfully. Room is now available.</div>";
    }

    public function displayErrorMessage(string $message): void {
        echo "<div style='color: red;'>Notice: {$message}</div>";
    }
}
