<?php

class CancelBookingUI {
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

    public function displayLoginRequest(): void {
        echo "<p>You must be logged in to view your booking history and cancel bookings.</p>";
        echo '<h3>Customer Login</h3>
              <form method="POST" action="index.php?action=doLoginCancel">
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

    public function openHistory(): void {
        if (!$this->controller->checkLoginStatus()) {
            $this->displayLoginRequest();
            return;
        }

        $customerID = $_SESSION['customerID'] ?? '';
        if (empty($customerID)) {
            $this->displayErrorMessage("Customer profile not found for this account.");
            return;
        }

        $this->controller->autoCancelOverdueBookings();

        $bookings = $this->controller->getCustomerBookings($customerID);
        $this->displayBookingList($bookings);
    }

    public function displayBookingList(array $bookings): void {
        echo "<h3>Booking History & Cancellation</h3>";
        
        if (empty($bookings)) {
            echo "<p>You have no booking records yet.</p>";
            echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
            return;
        }

        echo '<div class="room-grid">';
        foreach ($bookings as $booking) {
            $statusClass = '';
            if ($booking->status === 'AwaitingCheckIn') $statusClass = 'color: #d97706; font-weight: bold;';
            elseif ($booking->status === 'Cancelled') $statusClass = 'color: var(--error-color); font-weight: bold;';
            elseif ($booking->status === 'CheckedOut') $statusClass = 'color: var(--success-color); font-weight: bold;';

            echo '<div class="room-card">
                    <div class="room-card-header">
                        <h4>Booking: ' . htmlspecialchars($booking->bookingID) . '</h4>
                        <span class="room-price">' . number_format($booking->totalAmount, 0, ',', '.') . ' VND</span>
                    </div>
                    <div class="room-card-body">
                        <p><strong>Room Number:</strong> ' . htmlspecialchars($booking->roomNumber) . '</p>
                        <p><strong>Check-in:</strong> ' . htmlspecialchars($booking->checkInDate) . '</p>
                        <p><strong>Check-out:</strong> ' . htmlspecialchars($booking->checkOutDate) . '</p>
                        <p><strong>Status:</strong> <span style="' . $statusClass . '">' . htmlspecialchars($booking->status) . '</span></p>
                    </div>
                    <div class="room-card-footer">';
            
            if ($booking->status === 'AwaitingCheckIn') {
                $this->displayCancelButton($booking->bookingID);
            } else {
                echo '<button disabled style="background-color: var(--text-muted); cursor: not-allowed;">Unavailable</button>';
            }

            echo '</div>
                </div>';
        }
        echo '</div>';
        echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
    }

    public function displayCancelButton(string $bookingID): void {
        echo '<button style="background-color: var(--error-color);" onclick="location.href=\'?action=cancelDetails&bookingID=' . urlencode($bookingID) . '\'">Cancel Booking</button>';
    }

    public function selectBooking(string $bookingID): void {
        $booking = $this->controller->getBookingForCancellation($bookingID);
        if (!$booking) {
            $this->displayErrorMessage("Invalid booking or this booking cannot be cancelled.");
            echo '<br><a href="index.php?action=history" class="back-link">&larr; Back to History</a>';
            return;
        }

        $this->displayCancellationDetails($booking);
    }

    public function displayCancellationDetails($booking): void {
        echo "<h3>Confirm Cancellation</h3>";
        echo "<p>Are you sure you want to cancel the following booking?</p>";
        
        echo '<div style="background: #fafafa; border: 1px solid var(--border-color); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <p><strong>Booking ID:</strong> ' . htmlspecialchars($booking->bookingID) . '</p>
                <p><strong>Room Number:</strong> ' . htmlspecialchars($booking->roomNumber) . '</p>
                <p><strong>Check-in Date:</strong> ' . htmlspecialchars($booking->checkInDate) . '</p>
                <p><strong>Check-out Date:</strong> ' . htmlspecialchars($booking->checkOutDate) . '</p>
                <p><strong>Check-in Deadline:</strong> ' . htmlspecialchars($booking->checkInDeadline) . '</p>
                <p><strong>Total Amount:</strong> ' . htmlspecialchars($booking->totalAmount) . '$</p>
              </div>';

        echo '<form method="POST" action="index.php?action=doCancel&bookingID=' . htmlspecialchars($booking->bookingID) . '">
                  <button type="submit" style="background-color: var(--error-color);">Confirm Cancellation</button>
              </form>';
              
        echo '<br><a href="index.php?action=history" class="back-link">&larr; Back to History</a>';
    }

    public function confirmCancellation(string $bookingID): void {
        $success = $this->controller->cancelBooking($bookingID);
        if ($success) {
            $this->displaySuccessMessage();
            echo '<br><a href="index.php?action=history" class="btn">View History</a>';
        } else {
            $this->displayErrorMessage("Failed to cancel the booking. Please try again.");
            echo '<br><a href="index.php?action=history" class="back-link">&larr; Back to History</a>';
        }
    }

    public function updateHistoryDisplay($booking): void {
        echo "<p>Booking ID: {$booking->bookingID} has status updated to: <strong>{$booking->status}</strong></p>";
    }

    public function displaySuccessMessage(): void {
        echo "<div style='color: green;'>Success! The booking has been cancelled and the room has been released.</div>";
    }

    public function displayErrorMessage(string $message): void {
        echo "<div style='color: red;'>Error: {$message}</div>";
    }

}