<?php
session_start();
include 'db.php';

// Debugging: Check the contents of the session
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Ensure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute a statement to find the review and its owner
    $stmt = $conn->prepare("SELECT user_id FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();

    if ($review) {
        // Check if the user is an admin or the owner of the review
        if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_id'] === $review['user_id']) {
            // Prepare and execute the delete statement
            $deleteStmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
            $deleteStmt->bind_param("i", $id);

            if ($deleteStmt->execute()) {
                // Successful deletion, redirect to the appropriate dashboard
                if ($_SESSION['user_role'] === 'admin') {
                    header("Location: admin dashboard.php"); // Admin dashboard
                } else {
                    header("Location: client dashboard.php"); // Client dashboard
                }
                exit; 
            } else {
                // Display error if the deletion fails
                echo "Error deleting review: " . $deleteStmt->error;
            }

            $deleteStmt->close();
        } else {
            // User is not authorized to delete this review
            echo "You are not authorized to delete this review.";
        }
    } else {
        // Review not found
        echo "Review not found.";
    }

    $stmt->close();
} else {
    // If ID is not set, redirect to the appropriate dashboard
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin dashboard.php"); // Admin dashboard
    } else {
        header("Location: client dashboard.php"); // Client dashboard
    }
    exit;
}
?>
