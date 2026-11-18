<?php 
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: home.php"); 
    exit();
}

// Check if the logged-in user is an admin
if ($_SESSION['user_role'] !== 'admin') { 
    header("Location: client dashboard.php"); 
    exit();
}

// Include database connection
include 'db.php';

// Handling Filters
$date_filter = isset($_POST['date_filter']) ? $_POST['date_filter'] : '';
$rating_filter = isset($_POST['rating_filter']) ? $_POST['rating_filter'] : '';
$sentiment_filter = isset($_POST['sentiment_filter']) ? $_POST['sentiment_filter'] : '';

// Fetch client reviews from the database with filters
$query = "SELECT r.id, COALESCE(u.name, 'Unknown Client') AS client_name, r.review_text, r.rating, r.sentiment, r.created_at
          FROM reviews r
          LEFT JOIN users u ON r.user_id = u.id
          WHERE 1=1";

if ($date_filter) {
    $query .= " AND DATE(r.created_at) = ?";
}
if ($rating_filter) {
    $query .= " AND r.rating = ?";
}
if ($sentiment_filter) {
    $query .= " AND r.sentiment = ?";
}

$stmt = $conn->prepare($query);
if ($date_filter) $stmt->bind_param("s", $date_filter);
if ($rating_filter) $stmt->bind_param("s", $rating_filter);
if ($sentiment_filter) $stmt->bind_param("s", $sentiment_filter);

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error retrieving reviews: " . $conn->error);
}

// Profile Update Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; 

    // Prepare and execute the update statement
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security
    $stmt->bind_param("sssi", $name, $email, $hashed_password, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully.');</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
    $stmt->close();
}
// Fetch survey responses from the database
$survey_query = "SELECT id, user_id, question1, question2, question3, question4, submitted_at FROM survey_responses";
$survey_result = $conn->query($survey_query);

if (!$survey_result) {
    die("Error retrieving survey responses: " . $conn->error);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Navigation Bar */
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: red;
            padding: 15px 20px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            height: 50px;
        }

        .login-button {
            background-color: #b22222; 
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: green; 
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #fff;
            padding: 20px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            color: #dc143c;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #dc143c;
            background-color: #f9f9f9;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .sidebar a:hover {
            background-color: #ffe6e6;
        }

        /* Main Content */
        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

        h1 {
            color: #dc143c;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        /* Cards */
        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            color: #b22222;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .card p {
            margin: 10px 0;
            color: #555;
        }

        /* Client Reviews Management Section */
        .card table {
            width: 100%; 
            border-collapse: collapse; 
        }

        .card th, .card td {
            padding: 15px; 
            text-align: left; 
        }

        .card th {
            background-color: #dc143c; 
            color: white; 
        }

        .card tr:nth-child(even) {
            background-color: #f9f9f9; 
        }

        .card tr:hover {
            background-color: #ffe6e6; 
        }

        /* Profile Settings Form */
        .profile-form input[type="text"],
        .profile-form input[type="email"],
        .profile-form input[type="password"] {
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc;
            border-radius: 5px; 
        }

        .profile-form button {
            padding: 10px 15px;
            background-color: #dc143c; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }

        .profile-form button:hover {
            background-color: #b22222; 
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 10px;
            background-color: red;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Responsive Layout */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }
    
        }
    </style>
</head>

<body>
    <header class="nav">
        <div class="logo">
            <img src="IMAGES/restaurant7.jpg" alt="Logo">
        </div>
        <button class="login-button" onclick="window.location.href='logout.php'">Log Out</button>
    </header>

    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="#overview">Overview</a>
        <a href="#reviews">Reviews</a>
        <a href="#analytics">Analytics</a>
        <a href="#settings">Settings</a>
    </div>

    <div class="main-content">
        <h1>Admin Dashboard</h1>

        <!-- Overview Section -->
        <div class="card" id="overview">
            <h2>Overview</h2>
            <p>Total Reviews: <strong><?php echo $result->num_rows; ?></strong></p>
            <p>Average Rating: <strong><!-- Calculate and echo average rating here --></strong></p>
        </div>

        <!-- Filters Section -->
        <div class="card">
            <h2>Filter Reviews</h2>
            <form method="POST" action="">
                <label for="date_filter">Date:</label>
                <input type="date" id="date_filter" name="date_filter">

                <label for="rating_filter">Rating:</label>
                <select id="rating_filter" name="rating_filter">
                    <option value="">All</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>

                <label for="sentiment_filter">Sentiment:</label>
                <select id="sentiment_filter" name="sentiment_filter">
                    <option value="">All</option>
                    <option value="Positive">Positive</option>
                    <option value="Neutral">Neutral</option>
                    <option value="Negative">Negative</option>
                </select>

                <button type="submit">Apply Filters</button>
            </form>
        </div>

        <!-- Client Reviews Management Section -->
        <div class="card" id="reviews">
            <h2>Manage Client Reviews</h2>
            <table border="1" id="reviewsTable">
                <tr>
                    <th>Comment ID</th>
                    <th>Client Name</th>
                    <th>Review</th>
                    <th>Rating</th>
                    <th>Timestamp</th>
                    <th>Actions</th>
                </tr>
                <?php
                // Check if the result is valid and has data
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['review_text']); ?></td>
                            <td><?php echo htmlspecialchars($row['rating']); ?> ★</td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td> <!-- Display timestamp -->
                            <td>
                                <a href="edit_review.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a href="delete_review.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    // If no reviews found
                    echo "<tr><td colspan='6'>No reviews found.</td></tr>";
                }
                ?>
            </table>
            <button onclick="printReviews()">Print Reviews</button> <!-- Print button -->
        </div>
        <script>
    function printReviews() {
        const printContents = document.getElementById('reviewsTable').outerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<!-- Survey Responses Section -->
<div class="card" id="survey_responses">
    <h2>Survey Responses</h2>
    <table border="1">
        <tr>
            <th>Response ID</th>
            <th>User ID</th>
            <th>Question 1</th>
            <th>Question 2</th>
            <th>Question 3</th>
            <th>Question 4</th>
            <th>Submitted At</th>
        </tr>
        <?php
        if ($survey_result && $survey_result->num_rows > 0) {
            while ($survey = $survey_result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($survey['id']); ?></td>
                    <td><?php echo htmlspecialchars($survey['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($survey['question1']); ?></td>
                    <td><?php echo htmlspecialchars($survey['question2']); ?></td>
                    <td><?php echo htmlspecialchars($survey['question3']); ?></td>
                    <td><?php echo htmlspecialchars($survey['question4']); ?></td>
                    <td><?php echo htmlspecialchars($survey['submitted_at']); ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>
   

        <!-- Analytics Section -->
        <div class="card" id="analytics">
            <h2>Feedback Analysis</h2>
            <p>Sentiment Analysis: <strong>70% Positive, 20% Neutral, 10% Negative</strong></p>
            <div class="chart">[Sentiment Chart Area]</div>
            <p>Common Keywords: <strong>Service, Food Quality, Friendly Staff</strong></p>
        </div>

        <!-- Profile Settings Section -->
        <div class="card" id="settings">
            <h2>Profile Settings</h2>
            <form method="POST" class="profile-form">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="New Password" required>
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 Rhys Restaurant. All rights reserved.</p>
    </footer>

    <!-- JavaScript to dynamically insert new reviews -->
    <script>
        // Simulated review data; replace with AJAX fetch in real scenario
        const reviews = [
            { name: "Dahilia Nicholson", rating: 5, review: "Excellent service and great food! Highly recommend." },
            { name: "Harmon Smith", rating: 4, review: "Loved the ambiance and the staff were very friendly." },
            { name: "Michael Brown", rating: 5, review: "A wonderful experience overall. Will definitely come back!" }
        ];

        function displayReviews() {
            const reviewContainer = document.querySelector('.review-list');
            const template = document.getElementById('review-template');

            reviews.forEach(review => {
                const reviewClone = template.cloneNode(true);
                reviewClone.style.display = 'block';
                reviewClone.querySelector('.reviewer-name').textContent = review.name;
                reviewClone.querySelector('.review-text').textContent = review.review;
                reviewClone.querySelector('.rating').textContent = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
                reviewContainer.appendChild(reviewClone);
            });
        }

        document.addEventListener('DOMContentLoaded', displayReviews);
    </script>
</body>

</html>