<?php

class CheckInUI {
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
        
            header("Location: index.php?action=openCheckIn");
            exit;
        } 
        else {
            $this->displayErrorMessage("Email or password is not correct.");
        }
    }

    public function displayReceptionistPage(): void {
        if (!$this->controller->checkReceptionistPermission('')) {
            echo "<h3>Receptionist Login</h3>";
            echo '<form method="POST" action="index.php?action=doLoginReceptionist">
                      <div class="form-group"><label>Staff Email:</label><input type="email" name="email" required placeholder="receptionist1@hotel.com"></div>
                      <div class="form-group"><label>Password:</label><input type="password" name="password" required placeholder="Password"></div>
                      <button type="submit">Staff Sign In</button>
                  </form>';
            return;
        }

        echo "<h2>Receptionist Dashboard</h2>";
        echo "<p>Welcome back! You are logged in as Hotel Staff.</p>";
        echo '<p><a href="index.php?action=openCheckIn" class="btn">Go to Check-in Page</a></p>';
    }

    public function openCheckInPage(): void {
        if (!$this->controller->checkReceptionistPermission('')) {
            $this->displayReceptionistPage();
            return;
        }

        echo "<h3>Guest Check-in Management</h3>";
        echo '<form method="GET" action="index.php" class="search-form-inline">
                  <input type="hidden" name="action" value="doCheckInSearch">
                  <div class="form-row">
                      <div class="form-group-inline" style="flex: 2;">
                          <label>Customer Email or National ID:</label>
                          <input type="text" name="customerInfo" required placeholder="Enter email or national ID">
                      </div>
                      <div class="form-group-button">
                          <button type="submit">Search Customer</button>
                      </div>
                  </div>
              </form>';
        echo '<br><a href="index.php?action=receptionistPage" class="back-link">&larr; Back to Dashboard</a>';
    }

    public function enterCustomerInfo(string $info): void {
        $bookings = $this->controller->findBookingsForCheckIn($info);
        $this->displayBookings($bookings);
    }

    public function displayBookings(array $bookings): void {
        echo "<h3>Check-in Search Results</h3>";
        if (empty($bookings)) {
            $this->displayErrorMessage("No matching booking was found.");
            echo '<br><a href="index.php?action=openCheckIn" class="back-link">&larr; Back to Check-in Search</a>';
            return;
        }

        usort($bookings, function($a, $b) {
            $statusA = $this->controller->validateCheckIn($a->bookingID) === "Valid" ? 0 : 1;
            $statusB = $this->controller->validateCheckIn($b->bookingID) === "Valid" ? 0 : 1;
            return $statusA - $statusB;
        });

        echo '<div class="room-grid">';
        foreach ($bookings as $booking) {
            $validationMsg = $this->controller->validateCheckIn($booking->bookingID);
            
            echo '<div class="room-card">
                    <div class="room-card-header">
                        <h4>Booking: ' . htmlspecialchars($booking->bookingID) . '</h4>
                        <span class="room-price">Room ' . htmlspecialchars($booking->roomNumber) . '</span>
                    </div>
                    <div class="room-card-body">
                        <p><strong>Check-in Date:</strong> ' . htmlspecialchars($booking->checkInDate) . '</p>
                        <p><strong>Check-out Date:</strong> ' . htmlspecialchars($booking->checkOutDate) . '</p>
                        <p><strong>Current Status:</strong> ' . htmlspecialchars($booking->status) . '</p>';
            
            if ($validationMsg !== "Valid") {
                echo '<p style="color: red; font-weight: bold;">Note: ' . htmlspecialchars($validationMsg) . '</p>';
            }

            echo '</div>
                    <div class="room-card-footer">';

            if ($validationMsg === "Valid") {
                echo '<button onclick="location.href=\'?action=confirmCheckInAction&bookingID=' . urlencode($booking->bookingID) . '\'">Confirm Check-in</button>';
            } else {
                echo '<button disabled style="background-color: var(--text-muted); cursor: not-allowed;">Cannot Check-in</button>';
            }

            echo '</div>
                </div>';
        }
        echo '</div>';
        echo '<br><a href="index.php?action=openCheckIn" class="back-link">&larr; Back to Check-in Search</a>';
    }

    public function confirmCheckIn(string $bookingID): void {
        try {
            $this->controller->confirmCheckIn($bookingID);
            $this->displaySuccessMessage();
            echo '<br><a href="index.php?action=openCheckIn" class="btn">Back to Check-in</a>';
        } catch (Exception $e) {
            $this->displayErrorMessage($e->getMessage());
            echo '<br><a href="index.php?action=openCheckIn" class="back-link">&larr; Back to Check-in</a>';
        }
    }

    public function displaySuccessMessage(): void {
        echo "<div style='color: green;'>Success! Check-in confirmed successfully. Room status has been updated to 'InUse'.</div>";
    }

    public function displayErrorMessage(string $message): void {
        echo "<div style='color: red;'>Notice: {$message}</div>";
    }

}