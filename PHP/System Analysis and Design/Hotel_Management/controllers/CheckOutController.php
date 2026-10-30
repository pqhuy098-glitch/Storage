<?php

class CheckOutController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login(string $email, string $password): bool {
        $sql = "SELECT u.*, r.staffID 
                FROM HotelUser u 
                LEFT JOIN Receptionist r ON u.email = r.email 
                WHERE u.email = ? LIMIT 1";
            
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            if ($user['role'] !== 'Receptionist') {
                return false;
            }
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['staffID'] = $user['staffID'] ?? null;
            return true;
        }
        return false;
    }

    public function checkReceptionistPermission(string $userID): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Receptionist';
    }

    public function findBookingsForCheckOut(string $info): array {
        $sql = "SELECT b.* FROM Booking b
                JOIN Customer c ON b.customerID = c.customerID
                WHERE (c.email = ? OR c.nationalID = ?) AND b.status = 'CheckedIn'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$info, $info]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Booking');
    }

    public function calculateFinalAmount(string $bookingID): float {
        $stmt = $this->pdo->prepare("SELECT * FROM Booking WHERE bookingID = ? LIMIT 1");
        $stmt->execute([$bookingID]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) return 0.0;

        $checkIn = new DateTime($booking['checkInDate']);
        $checkOut = new DateTime($booking['checkOutDate']);
        $days = $checkIn->diff($checkOut)->days;
        if ($days <= 0) $days = 1;

        $roomStmt = $this->pdo->prepare("SELECT pricePerNight FROM Room WHERE roomNumber = ?");
        $roomStmt->execute([$booking['roomNumber']]);
        $room = $roomStmt->fetch(PDO::FETCH_ASSOC);
        $pricePerNight = $room ? $room['pricePerNight'] : 0;

        $roomCost = $days * $pricePerNight;
        $serviceCharge = $booking['serviceCharge'] ?? 0;
        $lateFee = $booking['lateFee'] ?? 0;
        $earlyDeduction = $booking['earlyCheckOutDeduction'] ?? 0;

        $finalAmount = ($roomCost + $serviceCharge + $lateFee) - $earlyDeduction;
        return max(0, $finalAmount);
    }

    public function verifyPayment(string $bookingID): void {
        $stmt = $this->pdo->prepare("UPDATE Booking SET isPaid = TRUE WHERE bookingID = ?");
        $stmt->execute([$bookingID]);
    }

    public function confirmCheckOut(string $bookingID): void {
        $stmt = $this->pdo->prepare("SELECT roomNumber FROM Booking WHERE bookingID = ?");
        $stmt->execute([$bookingID]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        $roomNumber = $booking['roomNumber'] ?? '';

        try {
            $this->pdo->beginTransaction();

            $actualDate = date('Y-m-d H:i:s');
            $updateBooking = $this->pdo->prepare("UPDATE Booking SET status = 'CheckedOut', actualCheckOutDate = ?, isPaid = TRUE WHERE bookingID = ?");
            $updateBooking->execute([$actualDate, $bookingID]);

            if (!empty($roomNumber)) {
                $updateRoom = $this->pdo->prepare("UPDATE Room SET status = 'Available' WHERE roomNumber = ?");
                $updateRoom->execute([$roomNumber]);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}