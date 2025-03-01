<?php
session_start();
include 'config.php'; // Include your database configuration file

// Fetch product ID from the URL and ensure it's properly sanitized
$product_id = filter_input(INPUT_GET, 'product_id', FILTER_SANITIZE_STRING);

if (empty($product_id)) {
    echo "<script>alert('Product ID is missing.'); window.location.href='index.php';</script>";
    exit();
}

// Handle rating and review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['email'])) {
        echo "<script>alert('You need to log in to submit a rating and review.'); window.location.href='index.php';</script>";
        exit();
    }

    $email = $_SESSION['email'];
    $rating = intval($_POST['rating']);
    $review = isset($_POST['review']) ? $_POST['review'] : '';

    // Check if a rating and review already exist for this user and product
    $checkQuery = "SELECT * FROM rating WHERE product_id = ? AND email = ?";
    if ($stmt = $conn->prepare($checkQuery)) {
        $stmt->bind_param("ss", $product_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('You have already submitted a rating and review for this product.'); window.location.href='myorders.php';</script>";
        } else {
            // Insert the rating and review
            $query = "INSERT INTO rating (product_id, email, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("ssis", $product_id, $email, $rating, $review);

                if ($stmt->execute()) {
                    echo '<script type="text/javascript">
            alert("Your rating and review have been submitted.");
            window.location.href = "http://localhost/project%20forntened/myorders.php";
          </script>';
                   exit();
                } else {
                    error_log("SQL Error: " . $stmt->error, 0);
                }
                $stmt->close();
            } else {
                error_log("SQL Prepare Error: " . $conn->error, 0);
            }
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error, 0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Rating and Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
        }

        .stars {
            font-size: 30px;
            margin: 10px 0;
        }

        .star {
            cursor: pointer;
            margin: 0 5px;
        }

        .star.selected {
            color: gold;
        }

        textarea {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 10px 0;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submit Your Rating and Review</h1>
        <div class="stars" id="stars">
            <span class="star" data-value="1">★</span>
            <span class="star" data-value="2">★</span>
            <span class="star" data-value="3">★</span>
            <span class="star" data-value="4">★</span>
            <span class="star" data-value="5">★</span>
        </div>
        <textarea id="review" placeholder="Write your review here"></textarea>
        <button id="submit">Submit Rating and Review</button>
    </div>
    <script>
        const stars = document.querySelectorAll(".star");
        const submitBtn = document.getElementById("submit");
        const reviewText = document.getElementById("review");

        let selectedRating = 0;

        stars.forEach((star) => {
            star.addEventListener("click", () => {
                selectedRating = parseInt(star.getAttribute("data-value"));
                stars.forEach((s) => s.classList.remove("selected"));
                for (let i = 0; i < selectedRating; i++) {
                    stars[i].classList.add("selected");
                }
            });
        });

        submitBtn.addEventListener("click", () => {
            if (selectedRating === 0) {
                alert("Please select a rating before submitting.");
                return;
            }

            const formData = new FormData();
            formData.append("rating", selectedRating);
            formData.append("review", reviewText.value);

            fetch("rating.php?product_id=<?php echo urlencode($product_id); ?>", {
                method: "POST",
                body: formData
            }).then(response => response.text())
              .then(text => {
                alert('Your rating and review have been submitted.');
                reviewText.value = ""; // Clear the textarea
                stars.forEach((s) => s.classList.remove("selected")); // Clear the stars
                selectedRating = 0; // Reset the selected rating
              })
              .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
