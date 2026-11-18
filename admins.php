<?php
/*
include 'db.php'; // Your database connection file

// Example admin credentials
$admins = [
    ["Admin One", "admin1", "123-456-7890", "admin1@example.com"],
    ["Admin Two", "admin2", "123-456-7891", "admin2@example.com"],
    // Add more admins as needed
];

foreach ($admins as $admin) {
    $fullname = $admin[0];
    $username = $admin[1];
    $contact = $admin[2];
    $email = $admin[3];

    // Hash the shared password for admins
    $hashed_password = password_hash("admin@007!", PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, contact, email, password, role) VALUES (?, ?, ?, ?, ?, 'admin')");
    $stmt->bind_param("sssss", $fullname, $username, $contact, $email, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        echo "Admin user {$fullname} created successfully.<br>";
    } else {
        echo "Error creating user {$fullname}: " . $stmt->error . "<br>";
    }

    $stmt->close();
}
$conn->close();
*/
?>
