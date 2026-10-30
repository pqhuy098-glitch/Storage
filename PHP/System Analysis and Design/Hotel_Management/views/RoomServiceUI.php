<?php

class RoomServiceUI {
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

    public function openServiceRequestSection(): void {
        if (!$this->controller->checkLoginStatus()) {
            $this->displayLoginRequest();
            return;
        }

        $customerID = $_SESSION['customerID'] ?? '';
        $rooms = $this->controller->getRoomsInUse($customerID);

        if (empty($rooms)) {
            $this->displayNoRoomInUseMessage();
            return;
        }

        $this->displayRoomsInUse($rooms);
    }

    public function displayRoomsInUse(array $rooms): void {
        echo "<h3>Select Room for Service Order</h3>";
        echo '<div class="room-grid">';
        foreach ($rooms as $room) {
            echo '<div class="room-card">
                    <div class="room-card-header">
                        <h4>Room ' . htmlspecialchars($room->roomNumber) . '</h4>
                        <span class="room-price">' . number_format($room->pricePerNight, 0, ',', '.') . ' VND<small>/night</small></span>
                    </div>
                    <div class="room-card-body">
                        <p><strong>Bed Type:</strong> ' . htmlspecialchars($room->bedType) . '</p>
                        <p><strong>Status:</strong> <span style="color: green; font-weight: bold;">In Use</span></p>
                    </div>
                    <div class="room-card-footer">
                        <button onclick="location.href=\'?action=selectServiceRoom&roomNumber=' . urlencode($room->roomNumber) . '\'">Order Service</button>
                    </div>
                </div>';
        }
        echo '</div>';
    }

    public function displayNoRoomInUseMessage(): void {
        echo "<div style='color: red;'>You currently have no checked-in rooms (In Use) to request services.</div>";
        echo '<br><a href="index.php" class="back-link">&larr; Back to Home</a>';
    }

    public function selectRoom(string $roomNumber): void {
        $customerID = $_SESSION['customerID'] ?? '';
        $booking = $this->controller->selectRoom($customerID, $roomNumber);

        if (!$booking) {
            $this->displayErrorMessage("Invalid room selected.");
            return;
        }

        $_SESSION['active_service_bookingID'] = $booking->bookingID;
        $_SESSION['active_service_roomNumber'] = $roomNumber;

        echo "<h3>Order Services for Room {$roomNumber}</h3>";
        
        $categories = ['Food', 'Beverage', 'Other'];
        foreach ($categories as $cat) {
            echo "<h4>Category: {$cat}</h4>";
            $services = $this->controller->getServicesByCategory($cat);
            $this->displayServicesByCategory($services);
        }

        $summary = $this->controller->getServiceSummary();
        $this->displayServiceInformation($summary);
    }

    public function displayServicesByCategory(array $services): void {
        echo '<div class="room-grid" style="margin-bottom: 25px;">';
        foreach ($services as $s) {
            echo '<div class="room-card">
                    <div class="room-card-header">
                        <h4>' . htmlspecialchars($s['serviceName']) . '</h4>
                        <span class="room-price">' . number_format($s['price'], 0, ',', '.') . ' VND</span>
                    </div>
                    <div class="room-card-body">
                        <p>Category: ' . htmlspecialchars($s['category']) . '</p>
                    </div>
                    <div class="room-card-footer">
                        <form method="POST" action="index.php?action=addServiceItem&serviceID=' . urlencode($s['serviceID']) . '" style="padding:0; background:none; border:none; margin:0;">
                            <div style="display:flex; gap:10px; align-items:center;">
                                <input type="number" name="quantity" value="1" min="1" style="width:70px;">
                                <button type="submit">Add Service</button>
                            </div>
                        </form>
                    </div>
                </div>';
        }
        echo '</div>';
    }

    public function addService(string $serviceID): void {
        $this->controller->addService($serviceID, 1);
    }

    public function enterQuantity(string $serviceID, int $quantity): void {
        $this->controller->updateQuantity($serviceID, $quantity);
    }

    public function displayServiceInformation(array $summary): void {
        echo '<div class="service-summary-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px;">';
        echo '<h3 style="margin-top: 0; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Selected Services Summary</h3>';
        
        $items = $summary['items'];
        $totalCost = $summary['totalCost'];

        if (empty($items)) {
            echo '<p style="color: #666; font-style: italic;">No services selected yet.</p>';
            echo '</div>';
            return;
        }

        echo '<div style="overflow-x: auto;">';
        echo '<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">';
        echo '<thead>
                <tr style="border-bottom: 2px solid #eee; text-align: left; color: #555; font-size: 0.9rem;">
                    <th style="padding: 10px;">Service Name</th>
                    <th style="padding: 10px; text-align: center;">Qty</th>
                    <th style="padding: 10px; text-align: right;">Unit Price</th>
                    <th style="padding: 10px; text-align: center;">Action</th>
                </tr>
              </thead>';
        echo '<tbody>';

        foreach ($items as $sID => $item) {
            echo '<tr style="border-bottom: 1px solid #f9f9f9; font-size: 0.95rem;">
                    <td style="padding: 12px 10px; font-weight: 500; color: #333;">' . htmlspecialchars($item['name']) . '</td>
                    <td style="padding: 12px 10px; text-align: center; color: #555;">' . htmlspecialchars($item['quantity']) . '</td>
                    <td style="padding: 12px 10px; text-align: right; color: #555;">' . number_format($item['unitPrice'], 0, ',', '.') . ' VND</td>
                    <td style="padding: 12px 10px; text-align: center;">
                        <a href="index.php?action=removeServiceItem&serviceID=' . urlencode($sID) . '" style="background: #ff4d4f; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-block; transition: background 0.2s;">Remove</a>
                    </td>
                  </tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        echo '<div style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px;">';
        echo '<span style="font-weight: 600; color: #444;">Estimated Total:</span>';
        echo '<span style="font-size: 1.1rem; font-weight: bold; color: var(--primary-color, #2c3e50);">' . number_format($totalCost, 0, ',', '.') . ' VND</span>';
        echo '</div>';

        $bookingID = $_SESSION['active_service_bookingID'] ?? '';
        echo '<form method="POST" action="index.php?action=confirmRoomService&bookingID=' . htmlspecialchars($bookingID) . '">
                <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; font-size: 1rem; font-weight: 600; border-radius: 6px; cursor: pointer; width: 100%; transition: background 0.2s;">Confirm Order</button>
              </form>';
        
        echo '</div>';
    }

    public function removeService(string $serviceID): void {
        $this->controller->removeService($serviceID);
    }

    public function displayEstimatedTotal(float $total): void {
        echo "<p style='font-size: 1.2rem; font-weight: bold; color: var(--primary-color);'>Estimated Total: {$total}$</p>";
    }

    public function confirmServices(): void {
        $bookingID = $_SESSION['active_service_bookingID'] ?? '';
        echo '<form method="POST" action="index.php?action=confirmRoomService&bookingID=' . htmlspecialchars($bookingID) . '">
                <button type="submit">Confirm Order</button>
              </form>';
    }

    public function displaySuccessMessage(): void {
        echo "<div style='color: green;'>Success! Your service request has been confirmed and added to your bill.</div>";
    }

    public function displayErrorMessage(string $message): void {
        echo "<div style='color: red;'>Error: {$message}</div>";
    }
}