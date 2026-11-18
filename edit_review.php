<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the review data
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();

    if (!$review) {
        echo "Review not found.";
        exit;
    }

    // Check if the user is an admin or the owner of the review
    if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_id'] !== $review['user_id']) {
        echo "You are not authorized to edit this review.";
        exit;
    }

    // Update the review if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $updated_review = $_POST['review_text']; // Ensure this matches your input field name

        // Update the review text in the database
        $update_stmt = $conn->prepare("UPDATE reviews SET review_text = ? WHERE id = ?");
        $update_stmt->bind_param("si", $updated_review, $id);

        if ($update_stmt->execute()) {
            // Redirect to the appropriate dashboard after update
            if ($_SESSION['user_role'] === 'admin') {
                header("Location: admin dashboard.php"); // Admin dashboard
            } else {
                header("Location: client dashboard.php"); // Client dashboard
            }
            exit;
        } else {
            echo "Error updating review: " . $update_stmt->error; // Provide error feedback
        }

        $update_stmt->close();
    }

    $stmt->close();
} else {
    header("Location: client dashboard.php"); // Redirect to client dashboard if no ID is set
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Review</title>
</head>
<body>
    <h1>Edit Review</h1>
    <form method="POST">
        <label for="review_text">Review:</label><br>
        <textarea name="review_text" id="review_text" rows="4" cols="50"><?php echo htmlspecialchars($review['review_text']); ?></textarea><br><br>
        <input type="submit" value="Update Review">
    </form>
</body>
</html>
