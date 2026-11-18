<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a client
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user_role'] !== 'client') {
    header("Location: login.php"); // Redirect if not logged in or not a client
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture survey responses
    $user_id = $_SESSION['user_id']; // Assuming the user ID is stored in session
    $question1 = $_POST['question1'];
    $question2 = $_POST['question2'];
    $question3 = $_POST['question3'];
    $question4 = $_POST['question4'];

    // Prepare and execute the SQL query to insert the survey responses
    $query = "INSERT INTO survey_responses (user_id, question1, question2, question3, question4) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $user_id, $question1, $question2, $question3, $question4);

    if ($stmt->execute()) {
        echo "<p>Thank you for submitting your feedback!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If form is not submitted, redirect to client dashboard
    header("Location: client dashboard.php");
    exit();
}
?>
