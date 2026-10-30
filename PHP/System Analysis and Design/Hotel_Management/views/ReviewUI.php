<?php

class ReviewUI {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function enterCredentials(string $email, string $password): void {
        $success = $this->controller->login($email, $password);
        if ($success) {
            header("Location: index.php?action=reviewHistory");
            exit;
        } else {
            $this->displayErrorMessage("Invalid email or password.");
        }
    }

    public function openHistory(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $customerID = $_SESSION['customerID'] ?? '';
        if (empty($customerID)) {
            $this->displayErrorMessage("Customer not logged in.");
            return;
        }
        $bookings = $this->controller->getBookingHistory($customerID);
        $this->displayBookingList($bookings);
    }

    public function displayBookingList(array $bookings): void {
        echo '<div style="max-width: 900px; margin: 20px auto; font-family: sans-serif;">';
        echo '<h2 style="color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px;">Booking History & Reviews</h2>';

        if (empty($bookings)) {
            echo '<p style="color: #666; font-style: italic;">No bookings found.</p>';
            echo '</div>';
            return;
        }

        echo '<div style="display: grid; gap: 15px; margin-top: 20px;">';
        foreach ($bookings as $booking) {
            $bookingID = $booking->bookingID ?? $booking['bookingID'];
            $status = $booking->status ?? $booking['status'];
            $roomNumber = $booking->roomNumber ?? $booking['roomNumber'];
            $checkIn = $booking->checkInDate ?? $booking['checkInDate'];
            $checkOut = $booking->checkOutDate ?? $booking['checkOutDate'];

            echo '<div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
            echo '<h4>Booking: ' . htmlspecialchars($bookingID) . ' (Room ' . htmlspecialchars($roomNumber) . ')</h4>';
            echo '<p><strong>Status:</strong> ' . htmlspecialchars($status) . ' | <strong>Check-in:</strong> ' . htmlspecialchars($checkIn) . ' | <strong>Check-out:</strong> ' . htmlspecialchars($checkOut) . '</p>';

            $existingReview = $this->controller->getReview($bookingID);
            if ($existingReview) {
                $this->displayExistingReview($existingReview);
                echo '<div style="margin-top: 10px;">';
                $this->displayEditReviewButton($bookingID);
                echo '</div>';
            } elseif ($status === 'CheckedOut' && $this->controller->canReview($bookingID)) {
                echo '<div style="margin-top: 10px;">';
                $this->displayReviewButton($bookingID);
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
        echo '<br><a href="index.php" style="color: #3498db; text-decoration: none;">&larr; Back to Home</a>';
        echo '</div>';
    }

    public function displayReviewButton(string $bookingID): void {
        echo '<a href="index.php?action=openReviewForm&bookingID=' . urlencode($bookingID) . '" style="background: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">Write Review</a>';
    }

    public function selectBooking(string $bookingID): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $existingReview = $this->controller->getReview($bookingID);
        
        echo '<div style="max-width: 600px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); font-family: sans-serif;">';
        echo '<h2>' . ($existingReview ? 'Edit Review' : 'Submit Review') . ' for Booking: ' . htmlspecialchars($bookingID) . '</h2>';

        $action = $existingReview ? "doUpdateReview&bookingID=" . urlencode($bookingID) : "doSubmitReview&bookingID=" . urlencode($bookingID);
        echo '<form method="POST" action="index.php?action=' . $action . '">';

        $rq = $existingReview['roomQualityRating'] ?? 5;
        $sa = $existingReview['staffAttitudeRating'] ?? 5;
        $ga = $existingReview['generalAmenitiesRating'] ?? 5;
        $comment = $existingReview['comment'] ?? '';

        echo '<div style="margin-bottom: 15px;"><label><strong>Room Quality (1-5):</strong></label><br>';
        echo '<select name="roomQuality" style="width: 100%; padding: 8px; margin-top: 5px;">';
        for($i=5; $i>=1; $i--) echo "<option value='$i' " . ($rq == $i ? 'selected' : '') . ">$i Stars</option>";
        echo '</select></div>';

        echo '<div style="margin-bottom: 15px;"><label><strong>Staff Attitude (1-5):</strong></label><br>';
        echo '<select name="staffAttitude" style="width: 100%; padding: 8px; margin-top: 5px;">';
        for($i=5; $i>=1; $i--) echo "<option value='$i' " . ($sa == $i ? 'selected' : '') . ">$i Stars</option>";
        echo '</select></div>';

        echo '<div style="margin-bottom: 15px;"><label><strong>General Amenities (1-5):</strong></label><br>';
        echo '<select name="generalAmenities" style="width: 100%; padding: 8px; margin-top: 5px;">';
        for($i=5; $i>=1; $i--) echo "<option value='$i' " . ($ga == $i ? 'selected' : '') . ">$i Stars</option>";
        echo '</select></div>';

        echo '<div style="margin-bottom: 15px;"><label><strong>Comment:</strong></label><br>';
        echo '<textarea name="comment" rows="4" style="width: 100%; padding: 8px; margin-top: 5px;">' . htmlspecialchars($comment) . '</textarea></div>';

        echo '<button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">' . ($existingReview ? 'Update Review' : 'Submit Review') . '</button>';
        echo '</form>';
        echo '<br><a href="index.php?action=reviewHistory" style="color: #666; text-decoration: none;">&larr; Back to History</a>';
        echo '</div>';
    }

    public function enterRatings(int $roomQuality, int $staffAttitude, int $generalAmenities): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['temp_review_ratings'] = [
            'roomQuality' => $roomQuality,
            'staffAttitude' => $staffAttitude,
            'generalAmenities' => $generalAmenities
        ];
    }

    public function enterComment(string $comment): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['temp_review_comment'] = trim($comment);
    }

    public function submitReview(): void {
        $this->displaySuccessMessage();
    }

    public function displayEditReviewButton(string $bookingID): void {
        echo '<a href="index.php?action=openReviewForm&bookingID=' . urlencode($bookingID) . '" style="background: #f39c12; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">Edit Review</a>';
    }

    public function clickEditReview(string $bookingID): void {
        $this->selectBooking($bookingID);
    }

    public function submitUpdatedReview(): void {
        $this->displaySuccessMessage();
    }

    public function displayViewReview(string $bookingID): void {
        $review = $this->controller->getReview($bookingID);
        if ($review) {
            $this->displayExistingReview($review);
        }
    }

    public function displayExistingReview($review): void {
        $r = is_array($review) ? $review : (array)$review;
        echo '<div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-top: 10px; font-size: 0.9rem; border: 1px solid #e9ecef;">';
        echo '<strong style="color: #27ae60;">Your Review:</strong> Room Quality: ⭐ ' . $r['roomQualityRating'] . ' | Staff Attitude: ⭐ ' . $r['staffAttitudeRating'] . ' | Amenities: ⭐ ' . $r['generalAmenitiesRating'] . '<br>';
        echo '<p style="margin: 5px 0 0 0; color: #555; font-style: italic;">"' . htmlspecialchars($r['comment']) . '"</p>';
        echo '</div>';
    }

    public function displaySuccessMessage(): void {
        echo '<div style="max-width: 600px; margin: 40px auto; background: #e8f5e9; color: #2e7d32; padding: 20px; border-radius: 6px; text-align: center; font-weight: bold; font-family: sans-serif;">';
        echo 'Success! Your review has been processed successfully.';
        echo '<br><br><a href="index.php?action=reviewHistory" style="background: #2e7d32; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">Return to Review History</a>';
        echo '</div>';
    }

    public function displayErrorMessage(string $message): void {
        echo '<div style="max-width: 600px; margin: 40px auto; background: #ffebee; color: #c62828; padding: 20px; border-radius: 6px; text-align: center; font-weight: bold; font-family: sans-serif;">';
        echo 'Notice: ' . htmlspecialchars($message);
        echo '<br><br><a href="index.php?action=reviewHistory" style="color: #c62828; text-decoration: underline;">Back to History</a>';
        echo '</div>';
    }
}