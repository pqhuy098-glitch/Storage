<?php

class RoomServiceController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login(string $email, string $password): bool {
        $sql = "SELECT u.*, c.customerID 
            FROM HotelUser u 
            LEFT JOIN Customer c ON u.email = c.email 
            WHERE u.email = ? LIMIT 1";
            
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['logged_in'] = true;

            $_SESSION['customerID'] = $user['customerID'] ?? null; 
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function checkLoginStatus(): bool { 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in'])=== true;
    }

    public function getRoomsInUse(string $customerID): array {
        $sql = "SELECT r.* FROM Room r 
                JOIN Booking b ON r.roomNumber = b.roomNumber 
                WHERE b.customerID = ? AND b.status = 'CheckedIn'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$customerID]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Room');
    }

    public function selectRoom(string $customerID, string $roomNumber): ?Booking {
        $sql = "SELECT * FROM Booking 
                WHERE customerID = ? AND roomNumber = ? AND status = 'CheckedIn' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$customerID, $roomNumber]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Booking');
        $booking = $stmt->fetch();
        return $booking ? $booking : null;
    }

    public function getServicesByCategory(string $category): array {
        $sql = "SELECT * FROM HotelService WHERE category = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addService(string $serviceID, int $quantity = 1): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['cart_services'])) {
            $_SESSION['cart_services'] = [];
        }

        $stmt = $this->pdo->prepare("SELECT serviceName, price FROM HotelService WHERE serviceID = ?");
        $stmt->execute([$serviceID]);
        $srv = $stmt->fetch(PDO::FETCH_ASSOC);

        $serviceName = $srv ? $srv['serviceName'] : $serviceID;
        $unitPrice = $srv ? $srv['price'] : 0;

        if (isset($_SESSION['cart_services'][$serviceID])) {
            $_SESSION['cart_services'][$serviceID]['quantity'] += $quantity;
        } else {
            $_SESSION['cart_services'][$serviceID] = [
                'serviceID' => $serviceID,
                'name'      => $serviceName,
                'quantity' => $quantity,
                'unitPrice' => $unitPrice
            ];
        }
    }

    public function updateQuantity(string $serviceID, int $quantity): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($quantity <= 0) {
            $this->removeService($serviceID);
        } else if (isset($_SESSION['cart_services'][$serviceID])) {
            $_SESSION['cart_services'][$serviceID]['quantity'] = $quantity;
        }
    }

    public function removeService(string $serviceID): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['cart_services'][$serviceID])) {
            unset($_SESSION['cart_services'][$serviceID]);
        }
    }

    public function getServiceSummary(): array {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $items = $_SESSION['cart_services'] ?? [];
        $totalCost = 0;

        foreach ($items as $item) {
            $totalCost += $item['quantity'] * $item['unitPrice'];
        }

        return [
            'items' => $items,
            'totalCost' => $totalCost
        ];
    }

    public function confirmServiceRequest(string $bookingID): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $summary = $this->getServiceSummary();
        $items = $summary['items'];
        $totalCost = $summary['totalCost'];

        if (empty($items)) return false;

        try {
            $this->pdo->beginTransaction();

            $requestID = 'REQ' . time();
            $requestDate = date('Y-m-d H:i:s');

            $stmtReq = $this->pdo->prepare("INSERT INTO ServiceRequest (requestID, bookingID, requestDate, totalCost, isConfirmed) VALUES (?, ?, ?, ?, TRUE)");
            $stmtReq->execute([$requestID, $bookingID, $requestDate, $totalCost]);

            $stmtItem = $this->pdo->prepare("INSERT INTO ServiceRequestItem (requestID, serviceID, quantity, unitPrice) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$requestID, $item['serviceID'], $item['quantity'], $item['unitPrice']]);
            }

            $stmtUpdateBooking = $this->pdo->prepare("UPDATE Booking SET serviceCharge = serviceCharge + ?, totalAmount = totalAmount + ? WHERE bookingID = ?");
            $stmtUpdateBooking->execute([$totalCost, $totalCost, $bookingID]);

            $this->pdo->commit();

            unset($_SESSION['cart_services']);
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}