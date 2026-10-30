<?php

class CancelBookingController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function checkLoginStatus(): bool { 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in'])=== true;
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

    public function getCustomerBookings(string $customerID): array {
        $sql = "SELECT * FROM Booking WHERE customerID = ? ORDER BY bookingDate DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$customerID]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Booking');
    }

    public function getBookingForCancellation(string $bookingID): ?Booking {
        $sql = "SELECT * FROM Booking WHERE bookingID = ? AND status = 'AwaitingCheckIn' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingID]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Booking');
        $booking = $stmt->fetch();
        return $booking ? $booking : null;
    }

    public function cancelBooking(string $bookingID): bool {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT roomNumber, status FROM Booking WHERE bookingID = ? FOR UPDATE");
            $stmt->execute([$bookingID]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$booking || $booking['status'] !== 'AwaitingCheckIn') {
                $this->pdo->rollBack();
                return false;
            }

            $roomNumber = $booking['roomNumber'];

            $updateBooking = $this->pdo->prepare("UPDATE Booking SET status = 'Cancelled' WHERE bookingID = ?");
            $updateBooking->execute([$bookingID]);

            $updateRoom = $this->pdo->prepare("UPDATE Room SET status = 'Available' WHERE roomNumber = ?");
            $updateRoom->execute([$roomNumber]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function autoCancelOverdueBookings(): void {
        $currentTime = date('Y-m-d H:i:s');
        
        $sql = "SELECT bookingID, roomNumber FROM Booking 
                WHERE status = 'AwaitingCheckIn' AND checkInDeadline < ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$currentTime]);
        $overdueBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($overdueBookings as $b) {
            try {
                $this->pdo->beginTransaction();

                $updateBooking = $this->pdo->prepare("UPDATE Booking SET status = 'Cancelled' WHERE bookingID = ?");
                $updateBooking->execute([$b['bookingID']]);

                $updateRoom = $this->pdo->prepare("UPDATE Room SET status = 'Available' WHERE roomNumber = ?");
                $updateRoom->execute([$b['roomNumber']]);

                $this->pdo->commit();
            } catch (Exception $e) {
                $this->pdo->rollBack();
            }
        }
    }
}