<?php 
session_start();
include 'db.php'; 

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: home.php"); 
    exit();
}

// Check if the logged-in user is a client
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'client') {
    header("Location: admin dashboard.php"); // Redirect to admin dashboard if not a client
    exit();
}

// Fixed review IDs to display (replace these with the actual review IDs)
$review_ids = [20, 21, 22]; 

// Fetch the 3 fixed reviews from the database
$query = "SELECT r.review_text AS comment, r.rating, r.created_at AS timestamp, u.name, r.profile_image
          FROM reviews AS r 
          INNER JOIN users AS u ON r.user_id = u.id
          WHERE r.id IN (" . implode(',', $review_ids) . ")"; 
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: red;
            padding: 10px 20px;
        }
        .logo img {
            height: 150px;
            width: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 10px; 
        }
        .navbar {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
            flex-grow: 1;
            justify-content: space-around;
        }
        .navbar li {
            margin: 0 10px;
        }
        .navbar a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background-color: green;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .navbar a:hover {
            background-color: darkgreen;
        }
        .login-button {
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-button:hover {
            background-color: darkgreen;
        }
        .welcome-message {
            text-align: center;
            padding: 20px;
        }
        .notification-area {
            background-color: #f4f4f4;
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            width: 80%;
        }
        .review-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }
        .review {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            width: 30%;
            text-align: center;
        }
        .review img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .review h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .review p {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .review .rating {
            color: greenyellow;
            font-size: 18px;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            margin: 20px;
        }
        .search-bar input {
            padding: 10px;
            width: 70%;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: darkgreen;
        }
        .dish-section {
            display: flex;
            justify-content: space-around;
            padding: 20px;
            flex-wrap: wrap;
        }
        .dish {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            width: 30%;
            margin-bottom: 20px;
        }
        .dish img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .dish p {
            font-size: 16px;
            color: #666;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .review {
                width: 80%;
            }
            .dish {
                width: 80%;
            }
        }
        .footer-section {
            background-color: red;
            color: white;
            padding: 20px 0;
        }

        .call-to-action-container {
            max-width: 80%;
            margin: 20px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .survey-section {
    background-color: #f4f4f4;
    padding: 20px;
    margin: 20px 0;
    border-radius: 10px;
}

.survey-section h2 {
    text-align: center;
    margin-bottom: 20px;
}

.survey-section form {
    max-width: 600px;
    margin: 0 auto;
}

.survey-section label {
    font-size: 16px;
    display: block;
    margin-bottom: 5px;
}

.survey-section select, .survey-section textarea, .survey-section input[type="radio"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
}

.survey-submit {
    background-color: green;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.survey-submit:hover {
    background-color: darkgreen;
}


    </style>
</head>
<body>
    <header>
        <div class="nav container">
            <div class="logo">
                <img src="IMAGES/restaurant7.jpg" alt="Logo">
            </div>
            <button class="login-button" onclick="window.location.href='home.php'">Log out</button>
            <ul class="navbar">
                <li><a href="client dashboard.php">Home</a></li>
                <li><a href="profile.php">Manage Profile</a></li>
                <li><a href="About us.php">About Us</a></li>
                <li><a href="feedback.php">Review</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </header>
    <div class="welcome-message">
        <h2>Welcome to Our Feedback System</h2>
        <p>We value your feedback! Your opinions help us improve our service and provide a better experience for all our customers. Thank you for helping us be the best we can be.</p>
    </div>

    <div class="notification-area">
        <h3>Notifications</h3>
        <p>You have new messages regarding your feedback submissions!</p>
    </div>

    <!-- Review Section -->
    <div class="review-section">
        <?php 
        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $formattedTimestamp = date("F j, Y, g:i a", strtotime($review['timestamp']));
                echo '<div class="review">';
                echo '<img src="' . htmlspecialchars($review['profile_image']) . '" alt="Profile Picture">';
                echo '<h3>' . htmlspecialchars($review['name']) . '</h3>';
                echo '<p>' . htmlspecialchars($review['comment']) . '</p>';
                echo '<div class="rating">Rating: ' . htmlspecialchars($review['rating']) . '</div>';
                echo '<p><small>Posted on: ' . $formattedTimestamp . '</small></p>';
                echo '</div>';
            }
        } else {
            echo "<p>No reviews found.</p>";
        }
        ?>
    </div>
    <!-- Survey Section -->
<div class="survey-section">
    <h2>Your Opinion Matters! Please fill out our quick survey.</h2>
    <form action="submit_survey.php" method="POST">
        <label for="question1">How would you rate our service?</label><br>
        <select id="question1" name="question1" required>
            <option value="Excellent">Excellent</option>
            <option value="Good">Good</option>
            <option value="Average">Average</option>
            <option value="Poor">Poor</option>
        </select><br><br>

        <label for="question2">What did you like most about our service?</label><br>
        <textarea id="question2" name="question2" rows="4" required></textarea><br><br>

        <label for="question3">What can we improve?</label><br>
        <textarea id="question3" name="question3" rows="4" required></textarea><br><br>

        <label for="question4">Would you recommend us to others?</label><br>
        <input type="radio" id="yes" name="question4" value="Yes" required>
        <label for="yes">Yes</label><br>
        <input type="radio" id="no" name="question4" value="No">
        <label for="no">No</label><br><br>

        <button type="submit" class="survey-submit">Submit Survey</button>
    </form>
</div>

    <img src="IMAGES/client5.jpg" alt="" style="width: 40%; display: block; margin: 20px auto;">

    <div class="call-to-action-container">
        <p> Dear client, to leave your feedback, please click on the "Reviews" link in the navigation bar, rate our service, and provide your comments. We appreciate your input!</p>
        <p>You can read from the above sample reviews, which are some of the featured testimonials that have helped us a restaurant to build our credibility. Your feedback matters a lot. </p>
        <p>In case you are not able to give your review or send an email or you have any issue that cannot be raised via review, you can call us via the phone number provided in your contact on the nav bar</p>
        <p>Thank you!</p>
    </div>

    <!-- Footer Section -->
    <footer class="footer-section">
        <div class="container" style="display: flex; justify-content: space-between;">
            <div class="footer-col" style="flex: 1; text-align: center;">
                <h5>Follow Us</h5>
                <ul class="social-icons" style="list-style: none; padding: 0;">
                    <li style="display: inline; margin-right: 10px;">
                        <a href="https://instagram.com" target="_blank" style="color: white;">
                            <i class="fab fa-instagram"></i> Instagram
                        </a>
                    </li>
                    <li style="display: inline;">
                        <a href="https://twitter.com" target="_blank" style="color: white;">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                    </li>
                </ul>
            </div>
            <div class="footer-col" style="flex: 1; text-align: center;">
                <h5>Contact Us</h5>
                <p>Email: info@rhysrestaurant.com</p>
                <p>Phone: +254734979909</p>
            </div>
            <div class="footer-col" style="flex: 1; text-align: center;">
                <h5>Quick Links</h5>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="home.php" style="color: white; text-decoration: none;">Home</a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="feedback.php" style="color: white; text-decoration: none;">Feedback</a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>
