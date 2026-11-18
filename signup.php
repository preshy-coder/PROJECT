<?php
session_start();
include 'db.php'; // Database connection

// Initialize the error message variable
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and validate
    $fullname = $_POST['fullname'] ?? null;
    $username = $_POST['username'] ?? null;
    $contact = $_POST['contact'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $userType = $_POST['userType'] ?? null;

    // Ensure all fields are filled
    if (!$fullname || !$username || !$contact || !$email || !$password || !$userType) {
        $error_message = "Please fill in all required fields."; // Set the error message
    } else {
        // Check if the username already exists
        $check_stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_message = "Username already taken. Please choose a different username.";
            $check_stmt->close();
            $conn->close();
            exit(); // Stop further execution
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $stmt = $conn->prepare("INSERT INTO users (fullname, username, contact, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $username, $contact, $email, $hashed_password, $userType);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit(); // Ensure that the script stops executing after the redirect
        } else {
            $error_message = "Error: " . $stmt->error; // Set the error message
        }

        // Close statements and connection
        $stmt->close();
        $check_stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-image: url(restaurant6.jpeg);
            background-repeat: no-repeat;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }
        .sign {
            background-color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .sign h2 {
            margin-bottom: 10px;
            color: #333;
            font-size: 20px;
        }
        .sign form input,
        .sign form select {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .sign form .btn {
            width: 100%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .sign form .btn:hover {
            background-color: darkred;
        }
        .sign p {
            margin-top: 10px;
            color: #333;
            font-size: 13px;
        }
        .sign p a {
            color: red;
            text-decoration: none;
        }
        .sign p a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: green; 
            text-align: center;
            margin-bottom: 10px; 
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="sign">
        <h2>Sign Up</h2>
        <!-- Display error message if it exists -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="signup.php" method="post">
            <input type="text" id="fullname" name="fullname" placeholder="Fullname" required>
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="text" id="contact" name="contact" placeholder="Contact" required>
            <input type="email" id="email" name="email" placeholder="Enter Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <label for="role">Role:</label>
            <select id="userType" name="userType" required>
                <option value="admin">Admin</option>
                <option value="client">Client</option>
            </select>
            <button type="submit" class="btn">Submit</button>
        </form>
        <p>Already have an account? <a href="login.php" class="login">Log In</a></p>
    </div>
</body>
</html>
