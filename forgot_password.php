<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php'; // Include database connection
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error_message = "Please enter your email address.";
    } else {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $reset_token = bin2hex(random_bytes(32)); // Generate a secure token
                $expiration = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expiration time

                // Store the reset token and expiration time in the database
                $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiration = ? WHERE email = ?");
                $stmt->bind_param("sss", $reset_token, $expiration, $email);
                if ($stmt->execute()) {
                    // Generate the reset link
                    $reset_link = "http://localhost/FINAL%20PROJECT/reset_password.php?token=" . $reset_token;

                    // Send the email using PHPMailer
                    $mail = new PHPMailer(true);

                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // Use Gmail's SMTP server
                        $mail->SMTPAuth = true;
                        $mail->Username = 'your-email@gmail.com'; // Your email address
                        $mail->Password = 'your-app-password'; // Your email app password or your password if not using 2FA
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Recipients
                        $mail->setFrom('no-reply@yourdomain.com', 'Your Website Name');
                        $mail->addAddress($email);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Request';
                        $mail->Body = "Click the link below to reset your password:<br><br>
                                       <a href='" . $reset_link . "'>" . $reset_link . "</a>";
                        $mail->AltBody = "Click the link below to reset your password:\n\n" . $reset_link;

                        $mail->send();
                        $success_message = "A password reset link has been sent to your email.";
                    } catch (Exception $e) {
                        $error_message = "Failed to send the reset email. Mailer Error: " . $mail->ErrorInfo;
                    }
                } else {
                    $error_message = "Error storing reset token: " . $stmt->error;
                }
            } else {
                $error_message = "No user found with that email address.";
            }
        } else {
            $error_message = "Error preparing statement: " . $conn->error;
        }
    }

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        /* Add your styles here */
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
        .form-container button {
            width: 100%;
            padding: 14px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST">
                <h2>Forgot Password</h2>
                <label for="email">Enter your email:</label>
                <input type="email" id="email" name="email" required>
                <button type="submit">Send Reset Link</button>
                <p>Remembered your password? <a href="login.php">Log in</a></p>
            </form>
        </div>
    </div>
</body>
</html>
