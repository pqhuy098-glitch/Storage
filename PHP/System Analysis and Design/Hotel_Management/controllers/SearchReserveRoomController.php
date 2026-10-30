<?php
session_start();

class SearchReserveRoomController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function validateDates($checkIn, $checkOut): bool {
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');

        if ($checkIn < $currentDate) {
            return false;
        }

        if ($checkIn === $currentDate && $currentTime >= '20:00:00') {
            return false;
        }

        if ($checkOut <= $checkIn) {
            return false;
        }

        return true;
    }

    public function searchRooms($checkIn, $checkOut, int $guests): array{
        if (!$this->validateDates($checkIn, $checkOut)){
            return [];
        }

        $sql = "SELECT * FROM Room 
                WHERE maxGuests >= :guests 
                AND roomNumber NOT IN (
                    SELECT roomNumber FROM Booking 
                    WHERE status IN ('AwaitingCheckIn', 'CheckedIn') 
                    AND :checkIn < checkOutDate AND :checkOut > checkInDate
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'guests' => $guests,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut
        ]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Room');
    }

    public function getRoomDetails(string $roomNumber): ?Room{
        $stmt = $this->pdo->prepare("SELECT * FROM Room WHERE roomNumber = ? LIMIT 1");
        $stmt->execute([$roomNumber]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Room');
        $room = $stmt->fetch();
        return $room ? $room : null;
    }

    public function getRoomReviews(string $roomNumber): array {
        $sql = "SELECT r.* FROM Review r 
                JOIN Booking b ON r.bookingID = b.bookingID 
                WHERE b.roomNumber = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomNumber]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Review');
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

    public function registerCustomer(string $fullName, string $email, string $password, string $nationalID, string $phoneNumber): ?Customer {
        try {
            $this->pdo->beginTransaction();

            $stmtUser = $this->pdo->prepare("INSERT INTO HotelUser (fullName, email, password, role) VALUES (?, ?, ?, 'Customer')");
            $stmtUser->execute([$fullName, $email, $password]);

            $customerID = 'CUST' . time();

            $stmtCust = $this->pdo->prepare("INSERT INTO Customer (customerID, email, nationalID, phoneNumber) VALUES (?, ?, ?, ?)");
            $stmtCust->execute([$customerID, $email, $nationalID, $phoneNumber]);

            $this->pdo->commit();

            return new Customer($fullName, $email, $password, 'Customer', $customerID, $nationalID, $phoneNumber);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return null;
        }
    }

    public function getCustomerInfo(string $customerID): ?Customer {
        $sql = "SELECT c.customerID, c.nationalID, c.phoneNumber, u.fullName, u.email, u.password, u.role 
                FROM Customer c 
                JOIN HotelUser u ON c.email = u.email 
                WHERE c.customerID = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$customerID]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Customer($data['fullName'], $data['email'], $data['password'], $data['role'], $data['customerID'], $data['nationalID'], $data['phoneNumber']);
        }
        return null;
    }

    public function prepareBookingConfirmation(string $customerID, string $roomNumber, $checkIn, $checkOut, int $guests): Booking {
        $room = $this->getRoomDetails($roomNumber);
        $nights = (strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24);
        
        $booking = new Booking();
        $booking->bookingID = 'BK' . time();
        $booking->bookingDate = date('Y-m-d H:i:s');
        $booking->checkInDate = $checkIn;
        $booking->checkOutDate = $checkOut;
        $booking->numberOfGuests = $guests;
        $booking->totalAmount = $nights * ($room ? $room->pricePerNight : 0);
        $booking->status = 'PendingConfirmation';
        
        return $booking;
    }

    public function confirmBooking(string $customerID, string $roomNumber, $checkIn, $checkOut, int $guests): ?Booking {
        $booking = $this->prepareBookingConfirmation($customerID, $roomNumber, $checkIn, $checkOut, $guests);
        $booking->status = 'AwaitingCheckIn';
        $booking->checkInDeadline = $checkIn . ' 20:00:00';

        $sql = "INSERT INTO Booking (bookingID, customerID, roomNumber, bookingDate, checkInDate, checkOutDate, checkInDeadline, numberOfGuests, totalAmount, status) 
                VALUES (:bookingID, :customerID, :roomNumber, :bookingDate, :checkInDate, :checkOutDate, :checkInDeadline, :numberOfGuests, :totalAmount, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            'bookingID' => $booking->bookingID,
            'customerID' => $customerID,
            'roomNumber' => $roomNumber,
            'bookingDate' => $booking->bookingDate,
            'checkInDate' => $booking->checkInDate,
            'checkOutDate' => $booking->checkOutDate,
            'checkInDeadline' => $booking->checkInDeadline,
            'numberOfGuests' => $booking->numberOfGuests,
            'totalAmount' => $booking->totalAmount,
            'status' => $booking->status
        ]);

        return $success ? $booking : null;
    }
}