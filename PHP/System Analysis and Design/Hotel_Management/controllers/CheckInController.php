<?php

class CheckInController {
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

    public function findBookingsForCheckIn(string $info): array {
        $sql = "SELECT b.* FROM Booking b
                JOIN Customer c ON b.customerID = c.customerID
                WHERE c.email = ? OR c.nationalID = ? AND b.status = 'CheckedOut'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$info, $info]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Booking');
    }

    public function validateCheckIn(string $bookingID): string {
        $stmt = $this->pdo->prepare("SELECT * FROM Booking b WHERE bookingID = ? LIMIT 1 ");
        $stmt->execute([$bookingID]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            return "No matching booking was found";
        }

        if ($booking['status'] === 'Cancelled') {
            return "This booking has been cancelled";
        }

        $currentDate = date('Y-m-d');
        if ($currentDate < $booking['checkInDate']) {
            return "This booking has not reached its check-in date yet";
        }

        if ($booking['status'] !== 'AwaitingCheckIn') {
            return "Checked out";
        }

        return "Valid";
    }

    public function confirmCheckIn(string $bookingID): void {
        $validation = $this->validateCheckIn($bookingID);
        if ($validation !== "Valid") {
            throw new Exception($validation);
        }

        $stmt = $this->pdo->prepare("SELECT roomNumber FROM Booking WHERE bookingID = ?");
        $stmt->execute([$bookingID]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        $roomNumber = $booking['roomNumber'];

        try {
            $this->pdo->beginTransaction();

            $updateBooking = $this->pdo->prepare("UPDATE Booking SET status = 'CheckedIn' WHERE bookingID = ?");
            $updateBooking->execute([$bookingID]);

            $updateRoom = $this->pdo->prepare("UPDATE Room SET status = 'InUse' WHERE roomNumber = ?");
            $updateRoom->execute([$roomNumber]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

}