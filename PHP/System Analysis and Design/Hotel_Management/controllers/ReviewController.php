<?php

class ReviewController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login(string $email, string $password): bool {
        $sql = "SELECT * FROM HotelUser WHERE email = ? AND password = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function getBookingHistory(string $customerID): array {
        $sql = "SELECT * FROM Booking b WHERE customerID = ? AND b.status = 'CheckedOut'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$customerID]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function canReview(string $bookingID): bool {
        $stmt = $this->pdo->prepare("SELECT status FROM Booking WHERE bookingID = ?");
        $stmt->execute([$bookingID]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking || $booking['status'] !== 'CheckedOut') {
            return false;
        }

        $revStmt = $this->pdo->prepare("SELECT reviewID FROM Review WHERE bookingID = ?");
        $revStmt->execute([$bookingID]);
        return $revStmt->rowCount() === 0;
    }

    public function getReview(string $bookingID): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM Review WHERE bookingID = ? LIMIT 1");
        $stmt->execute([$bookingID]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function submitReview(string $bookingID, string $customerID, int $roomQuality, int $staffAttitude, int $generalAmenities, string $comment): void {
        $reviewID = 'REV' . uniqid();
        $submittedAt = date('Y-m-d H:i:s');

        $sql = "INSERT INTO Review (reviewID, bookingID, customerID, roomQualityRating, staffAttitudeRating, generalAmenitiesRating, comment, submittedAt) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$reviewID, $bookingID, $customerID, $roomQuality, $staffAttitude, $generalAmenities, $comment, $submittedAt]);
    }

    public function updateReview(string $reviewID, int $roomQuality, int $staffAttitude, int $generalAmenities, string $comment): void {
        $sql = "UPDATE Review SET roomQualityRating = ?, staffAttitudeRating = ?, generalAmenitiesRating = ?, comment = ? WHERE reviewID = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$roomQuality, $staffAttitude, $generalAmenities, $comment, $reviewID]);
    }

}
