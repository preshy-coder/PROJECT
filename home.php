<?php
session_start(); // Start session to check if a user is already logged in

// Check if user is logged in and redirect accordingly
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin dashboard.php'); // Redirect to admin dashboard if logged in as admin
        exit;
    } else if ($_SESSION['role'] == 'client') {
        header('Location: client dashboard.php'); // Redirect to client dashboard if logged in as client
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        /* Global Styles */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.5;
}

header {
    background-color: red;
    color: white;
    text-align: center;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

header h1 {
    font-size: 36px;
    margin: 10px 0;
    font-weight: 700;
}

header p {
    font-size: 18px;
    color: #4CAF50;
    margin: 5px 0 20px;
}

.content {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* Button Styles */
.button-container {
    margin-top: 20px;
}

.button {
    font-size: 18px;
    padding: 12px 25px;
    background-color: #e60000;
    color: white;
    text-decoration: none;
    border-radius: 30px;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}

.button:hover {
    background-color: #4CAF50;
    transform: translateY(-3px);
}

.button:active {
    transform: translateY(1px);
}

/* Footer */
.footer {
    background-color: red;
    color: white;
    padding: 15px;
    text-align: center;
    margin-top: 40px;
    border-top: 5px solid #e60000;
}

.footer a {
    color: #4CAF50;
    text-decoration: none;
    font-weight: bold;
    margin: 0 10px;
}

.footer a:hover {
    text-decoration: underline;
}


    </style>
</head>
<body>

<!-- Header Section -->
<header>
    <h1>Welcome to Our Restaurant</h1>
    <p>Your favorite place for delicious food and unforgettable experiences</p>
</header>

<!-- Main Content -->
<div class="content">
    <img src="./IMAGES/restaurant6.jpeg" alt="Restaurant Image">
    
    <!-- Login Section -->
    <div class="button-container">
        <a href="login.php" class="button">Login</a>
    </div>

    <!-- About Us Section -->
    <div class="section">
        <h2>About Us</h2>
        <p>We provide the best dining experience with a wide variety of mouth-watering dishes. Join us for an unforgettable experience!</p>
    </div>

    <!-- Menu Section -->
    <div class="section">
        <h2>Our Menu</h2>
        <p>Explore our carefully crafted menu, made with the finest ingredients. </p>
        <a href="menu.php" class="button">View Menu</a>

    </div>

    <!-- Testimonials Section -->
    <div class="testimonials">
        <h2>What Our Clients Say</h2>
        <p>"The best food I’ve had in years!" – Dr Karev</p>
        <p>"Wonderful service and a great ambiance." – Meredith Grey</p>
    </div>

    <!-- Logout Section (Admin only) -->
    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <div class="button-container">
            <a href="logout.php" class="button">Logout</a>
        </div>
    <?php endif; ?>
</div>

<!-- Footer Section -->
<div class="footer">
    <p>&copy; 2024 Rhys Restaurant | Follow us on: 
        <a href="https://facebook.com" target="_blank">Facebook</a> | 
        <a href="https://instagram.com" target="_blank">Instagram</a>
    </p>
</div>

</body>
</html>
