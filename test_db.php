<?php
/*
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_feedback";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Uncomment the line below for debugging purposes during development
// echo "Connected successfully!";

// Optional: Test a simple query
$sql = "SELECT * FROM users LIMIT 1"; // Adjust the query according to your database structure
$result = $conn->query($sql);

if ($result) {
    // You can also fetch the results to check if the query works
    if ($result->num_rows > 0) {
        echo "Query executed successfully. Users found.";
    } else {
        echo "No users found in the database.";
    }
} else {
    echo "Error executing query: " . $conn->error;
}

// Close the connection
$conn->close();
*/
?>
