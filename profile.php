<?php
session_start();
include_once 'db.php';  

// Check if the user is logged in and user_id is set in the session
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$userId = $_SESSION['user_id']; // Use the correct variable name
$conn = getDbConnection();  // Get the database connection using the function
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists in the database
    if (!$user) {
        // If no user is found with the provided ID, unset the session and redirect to login
        session_unset();
        session_destroy();
        header("Location: login.php"); // Redirect to login page
        exit();
    }
} else {
    die("Failed to prepare SQL statement."); // Handle SQL prepare error
}

// Fetch reviews for the user
$reviewsQuery = "SELECT rating, review_text, created_at, tags, sentiment FROM reviews WHERE user_id = ? ORDER BY created_at DESC";
$reviewsStmt = $conn->prepare($reviewsQuery);

if ($reviewsStmt) {
    $reviewsStmt->bind_param("i", $userId);
    $reviewsStmt->execute();
    $reviewsResult = $reviewsStmt->get_result();
} else {
    die("Failed to prepare reviews SQL statement."); // Handle SQL prepare error
}

// Close the statement
$stmt->close();

// Initialize the message variable
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Update profile information
    $fullname = trim($_POST['name']); // Sanitize input
    $email = trim($_POST['email']); // Sanitize input
    $password = trim($_POST['password']); // Sanitize input

    // Hash the password if it is being updated
    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['password'];

    // Prepare and execute the update query
    $updateQuery = "UPDATE users SET fullname = ?, email = ?, password = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);

    if ($updateStmt) { // Ensure the update statement was prepared successfully
        $updateStmt->bind_param("sssi", $fullname, $email, $hashedPassword, $userId);

        if ($updateStmt->execute()) {
            $message = "Profile updated successfully!";
            $_SESSION['fullname'] = $fullname; // Update session variable if needed
        } else {
            $message = "Error updating profile. Please try again.";
        }

        // Close the prepared statement
        $updateStmt->close();
    } else {
        die("Failed to prepare update SQL statement."); // Handle SQL prepare error
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            color: #333;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
            font-size: 1.2em;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            font-size: 1.1em;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1em;
            width: 100%;
        }
        button {
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }

        /* Review History Section */
        .reviews-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .reviews-section {
            margin-top: 30px;
        }
        .review {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .review h3 {
            margin: 0;
            color: #333;
        }
        .review p {
            margin: 10px 0;
            color: #555;
        }
        .review span {
            font-size: 0.9em;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Profile Update Form -->
        <form action="profile.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">New Password (optional):</label>
            <input type="password" name="password" id="password">

            <button type="submit" name="update_profile">Update Profile</button>
        </form>
    </div>

    <!-- Review History Section -->
    <div class="reviews-container">
        <h2>Review History</h2>
        <?php if ($reviewsResult->num_rows > 0): ?>
            <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                <div class="review">
                    <h3>Rating: <?php echo htmlspecialchars($review['rating']); ?></h3>
                    <p>
                        <?php echo htmlspecialchars($review['review_text']); ?>
                    </p>
                    <span>Reviewed on: <?php echo date("F j, Y, g:i a", strtotime($review['created_at'])); ?></span>
                    <br>
                    <strong>Tags: </strong>
                    <?php 
                        // Check if tags are available, else display a fallback message
                        echo htmlspecialchars($review['tags'] ?? 'No tags available'); 
                    ?>
                    <br>
                    <strong>Sentiment: </strong><?php echo htmlspecialchars($review['sentiment']); ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
