<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php'; 

// Initialize the error message variable
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $userType = strtolower(trim($_POST['userType'])); 

    // Check if email, password, or userType are empty
    if (empty($email) || empty($password) || empty($userType)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Prepare the SQL statement to fetch user info securely
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND LOWER(role) = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $userType);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Store essential user data in the session
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $user['id']; // Unique user identifier
                    $_SESSION['user_role'] = strtolower($user['role']); 

                    // Redirect based on user role
                    if ($_SESSION['user_role'] === 'admin') {
                        header("Location: admin dashboard.php"); 
                        exit();
                    } elseif ($_SESSION['user_role'] === 'client') {
                        header("Location: client dashboard.php"); 
                        exit();
                    }
                } else {
                    // Password does not match
                    $error_message = "Invalid password.";
                }
            } else {
                // No user found with the provided email and role
                $error_message = "No user found with this email and role.";
            }

            $stmt->close(); 
        } else {
            // Handle any errors with preparing the SQL statement
            $error_message = "Error preparing statement: " . $conn->error;
        }
    }

    $conn->close(); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients Feedback After Service Platform - Sign Up / Log In</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }
        .form-container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }
        .form-container h2 {
            text-align: center;
            font-size: 26px;
            color: red;
            margin-bottom: 25px;
        }
        .form-container label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            display: block;
        }
        .form-container input,
        .form-container select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        .form-container input[type="email"]:focus,
        .form-container input[type="password"]:focus,
        .form-container select:focus {
            border-color: #d32f2f;
            outline: none;
        }
        .form-container button {
            width: 100%;
            padding: 14px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-container button:hover {
            background-color: darkred;
        }
        .form-container p {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .form-container p a {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }
        .form-container p a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: green; 
            text-align: center;
            margin-bottom: 20px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- Display error message if it exists -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <!-- Log in Form -->
            <form action="login.php" method="POST">
                <h2>Log in</h2>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="userType">Role:</label>
                <select id="userType" name="userType" required>
                    <option value="admin">Admin</option>
                    <option value="client">Client</option>
                </select>

                <button type="submit">Log in</button>
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </form>
            <p><a href="forgot_password.php">Forgot Password?</a></p>
        </div>
    </div>
</body>
</html>