<?php
/*
include 'db.php'; // Your database connection file

// Example client credentials
$clients = [
    ["Client One", "client1", "123-456-7892", "client1@example.com", "clientPass1"],
    ["Client Two", "client2", "123-456-7893", "client2@example.com", "clientPass2"],
    // Add more clients as needed
];

foreach ($clients as $client) {
    $fullname = $client[0];
    $username = $client[1];
    $contact = $client[2];
    $email = $client[3];
    $password = $client[4]; // Each client has a unique password

    // Hash the client's password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, contact, email, password, role) VALUES (?, ?, ?, ?, ?, 'client')");
    $stmt->bind_param("sssss", $fullname, $username, $contact, $email, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        echo "Client user {$fullname} created successfully.<br>";
    } else {
        echo "Error creating user {$fullname}: " . $stmt->error . "<br>";
    }

    $stmt->close();
}
$conn->close();
*/
?>
