<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php'; // Include database connection

$error_message = "";
$success_message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the reset token is valid and not expired
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expiration > NOW()");
    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_password = trim($_POST['new_password']);
                $confirm_password = trim($_POST['confirm_password']);

                // Validation
                if (empty($new_password) || empty($confirm_password)) {
                    $error_message = "Please fill in both fields.";
                } elseif ($new_password !== $confirm_password) {
                    $error_message = "Passwords do not match.";
                } elseif (strlen($new_password) < 8) { // Enforce password strength
                    $error_message = "Password must be at least 8 characters long.";
                } else {
                    // Update the password in the database
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiration = NULL WHERE id = ?");
                    $stmt->bind_param("si", $hashed_password, $user['id']);

                    if ($stmt->execute()) {
                        $success_message = "Your password has been successfully reset. You can now <a href='login.php'>log in</a> with your new password.";
                    } else {
                        $error_message = "Error updating password: " . $conn->error;
                    }
                }
            }
        } else {
            $error_message = "Invalid or expired token.";
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
} else {
    // Redirect if no token provided
    header("Location: forgot_password.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
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
        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        .form-container input[type="password"]:focus {
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
        .error-message,
        .success-message {
            color: green; 
            text-align: center;
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- Display success or error message -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <!-- Reset Password Form -->
            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <h2>Reset Password</h2>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
