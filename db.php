<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_feedback";

// Create main database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check main connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function getDbConnection() {
    global $servername, $username, $password, $dbname;

    
    static $newConn = null;
    if ($newConn === null) {
        $newConn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($newConn->connect_error) {
            die("Connection failed: " . $newConn->connect_error);
        }
    }

    return $newConn;
}

// Function to delete a comment
function deleteComment($conn, $comment_id) {
    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("i", $comment_id); 

    // Execute the statement
    if ($stmt->execute()) {
        echo "Comment deleted successfully.";
    } else {
        echo "Error deleting comment: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}


// $conn->close();
?>
