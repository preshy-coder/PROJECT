<?php
session_start();
include 'db.php';

$discount_message = ''; // Variable for displaying discount message

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form inputs
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    $review = isset($_POST['review']) ? trim($_POST['review']) : '';
    $tags = !empty($_POST['tags']) ? implode(", ", array_map('htmlspecialchars', $_POST['tags'])) : '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $discount_code = isset($_POST['discount-code']) ? trim($_POST['discount-code']) : '';

    // Validate required fields
    if ($rating && !empty($review)) {
        // Set user_id to NULL for anonymous feedback
        $user_id = $anonymous ? 'NULL' : $_SESSION['user_id'];

        // Insert review into the database
        $sql = "INSERT INTO reviews (user_id, rating, review_text, tags) VALUES ($user_id, '$rating', '$review', '$tags')";
        
        // Execute the query and handle any potential errors
        if ($conn->query($sql) === TRUE) {
            // Points earned based on rating
            $points_earned = 0;
            if ($rating == 5) {
                $points_earned = 10;
            } elseif ($rating == 4) {
                $points_earned = 7;
            } elseif ($rating == 3) {
                $points_earned = 5;
            } elseif ($rating == 2) {
                $points_earned = 3;
            } elseif ($rating == 1) {
                $points_earned = 1;
            }

            // Update points for the user if they are not anonymous
            if ($user_id != 'NULL') {
                // Prepare the statement to update points
                $update_points = $conn->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                $update_points->bind_param("ii", $points_earned, $_SESSION['user_id']);
                $update_points->execute();
                $update_points->close();
            }

            echo "<p style='color:green;'>Thank you for your feedback! You earned $points_earned points.</p>";

            // Check if discount code is applied
            if ($discount_code) {
                $discount_code = mysqli_real_escape_string($conn, $discount_code);

                // Fetch discount details from the database
                $discount_query = "SELECT discount_percentage, min_order_value, expiration_date FROM discounts WHERE code = '$discount_code' AND status = 'active' AND expiration_date >= CURDATE()";
                $discount_result = $conn->query($discount_query);

                if ($discount_result->num_rows > 0) {
                    $discount = $discount_result->fetch_assoc();
                    $discount_percentage = $discount['discount_percentage'];
                    $min_order_value = $discount['min_order_value'];

                    // Assuming $_SESSION['total_price'] contains the current total order value
                    $order_value = $_SESSION['total_price'] ?? 0;

                    if ($order_value >= $min_order_value) {
                        // Apply discount if order meets the minimum value
                        $total_after_discount = $order_value * (1 - ($discount_percentage / 100));
                        $_SESSION['total_price_after_discount'] = $total_after_discount;
                        $discount_message = "Success! Discount applied: $discount_percentage% off! New total: $total_after_discount";
                    } else {
                        $discount_message = "Your order doesn't meet the minimum value for this discount.";
                    }
                } else {
                    $discount_message = "Invalid or expired discount code.";
                }
            }

        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Please provide a rating and review text.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
            color: green;
            text-align: center;
            padding: 20px;
        }
        /* Adjust first image to be smaller */
        img:first-of-type {
            display: block;
            margin: 0 auto 10px;
            max-width: 40%; /* Reduced size of first image */
            height: auto;
        }
        /* Style for the second image */
        img:last-of-type {
            display: block;
            margin: 20px auto;
            max-width: 40%; /* Keep the second image smaller */
            height: auto;
        }
        .star-rating {
            display: flex;
            direction: row-reverse;
            justify-content: center;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 2em;
            color: green;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating input:hover ~ label,
        .star-rating label:hover ~ label {
            color: gold;
        }
        #feedback-section {
            max-width: 600px;
            margin: 0 auto;
            text-align: left;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form label, form textarea, form select, form button, form input[type="checkbox"] {
            margin-bottom: 15px;
            font-size: 1em;
        }
        form textarea {
            resize: vertical;
        }
        form button {
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: darkred;
        }
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .tags label {
            background-color: lightgrey;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .tags input[type="checkbox"] {
            display: none;
        }
        .tags input[type="checkbox"]:checked + label {
            background-color: green;
            color: white;
        }
        .feedback-message {
            font-size: 1.2em;
            color: green;
            margin-top: 20px;
            font-weight: bold;
        }
        .discount-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .discount-section input {
            padding: 10px;
            font-size: 1em;
            width: 70%;
            margin-right: 10px;
        }
        .discount-section button {
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .discount-section button:hover {
            background-color: darkgreen;
        }
        #anonymous {
            float: right;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <img src="./IMAGES/review3.jpeg" alt="Feedback Image">
    <div id="feedback-section">
        <h2>We value your feedback!</h2>
        <p>Please rate your experience and leave a review below:</p>
        <form id="feedback-form" method="POST" onsubmit="submitFeedback(); return false;">
            <label for="rating">Rating:</label>
            <div class="star-rating">
                <input type="radio" id="5-stars" name="rating" value="5" />
                <label for="5-stars" class="star">&#9733;</label>
                <input type="radio" id="4-stars" name="rating" value="4" />
                <label for="4-stars" class="star">&#9733;</label>
                <input type="radio" id="3-stars" name="rating" value="3" />
                <label for="3-stars" class="star">&#9733;</label>
                <input type="radio" id="2-stars" name="rating" value="2" />
                <label for="2-stars" class="star">&#9733;</label>
                <input type="radio" id="1-star" name="rating" value="1" />
                <label for="1-star" class="star">&#9733;</label>
            </div>

            <label for="tags">Select Tags (Optional):</label>
            <div class="tags">
                <input type="checkbox" id="fast-service" name="tags[]" value="Fast Service">
                <label for="fast-service">Fast Service</label>

                <input type="checkbox" id="friendly-staff" name="tags[]" value="Friendly Staff">
                <label for="friendly-staff">Friendly Staff</label>

                <input type="checkbox" id="good-value" name="tags[]" value="Good Value">
                <label for="good-value">Good Value</label>

                <input type="checkbox" id="quality-products" name="tags[]" value="Quality Products">
                <label for="quality-products">Quality Products</label>
            </div>

            <label for="review">Review:</label>
            <textarea id="review" name="review" rows="4" required></textarea>

            <label for="anonymous">Submit Anonymously:</label>
            <input type="checkbox" id="anonymous" name="anonymous">

            <!-- Discount code section -->
            <div class="discount-section">
                <label for="discount-code">Have a Discount Code?</label>
                <input type="text" id="discount-code" name="discount-code" placeholder="Enter code here">
                <button type="submit">Apply Discount</button>
            </div>

            <button type="submit">Submit Feedback</button>
        </form>

        <div class="feedback-message">
            <?php if ($discount_message) echo $discount_message; ?>
        </div>
        <p> The code should be send to your Email from our end.thanks!</p>

        <img src="./IMAGES/review1.jpg" alt="Motivational Image">
    </div>
</body>