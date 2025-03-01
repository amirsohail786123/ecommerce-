<?php
session_start();
include('navbardesign.php');
include('config.php'); // Ensure this includes your MySQLi connection ($conn)

ini_set('display_errors', 0); // Disable error display in production
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', 'path_to_error_log.log'); // Path to error log

function get_user_ip() {
    return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
}

function get_device_info() {
    return $_SERVER['HTTP_USER_AGENT'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $product_price = $_POST['product_price'] ?? '';
    $size = $_POST['product_size'] ?? '';
    $quantity = $_POST['product_quantity'] ?? '';
    $email = $_POST['email'] ?? '';

    $selected_image = $_POST['selected_image'] ?? '';

    // SQL query to get user_id associated with product_id
    $select = "SELECT user_id FROM product WHERE product_id = ?";
    $result = $conn->prepare($select);

    if ($result === false) {
        error_log('Database prepare failed: ' . $conn->error);
        exit;
    }











    

    $result->bind_param("s", $product_id);
    $res = $result->execute();

    if ($res === false) {
        error_log('Database execute failed: ' . $result->error);
        exit;
    }

    $result->bind_result($user_id);
    $result->fetch();
    $result->close();

    if (empty($email)) {
        echo "<script>alert('Email is required.');</script>";
    } else {
        $ip_address = get_user_ip();
        $device_info = get_device_info();

        // Check if the product already exists in the cart for this email
        $checkSql = "SELECT COUNT(*) FROM orders WHERE product_id = ? AND email = ?";
        $checkStmt = $conn->prepare($checkSql);

        if ($checkStmt === false) {
            error_log('Database prepare failed: ' . $conn->error);
            exit;
        }

        $checkStmt->bind_param("ss", $product_id, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $count = $checkResult->fetch_row()[0];
        $checkStmt->close();

        if ($count > 0) {
            echo "<script>alert('Product with this ID already exists in the cart for this email!');</script>";
            echo "<script>window.location.href = 'carddetail.php?productid={$product_id}';</script>";
        } else {
            $sql = "INSERT INTO orders (product_id, image, seller, email, product_name, product_price, product_size, product_quantity, product_buy_ip, device_info) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                error_log('Database prepare failed: ' . $conn->error);
                exit;
            }

            $stmt->bind_param("sssssisiss", $product_id, $selected_image, $user_id, $email, $product_name, $product_price, $size, $quantity, $ip_address, $device_info);

            try {
                if ($stmt->execute()) {
                    if (isset($_POST['add_to_cart'])) {
                        echo "<script>window.location.href = 'carddetail.php?productid={$product_id}';</script>";
                    } elseif (isset($_POST['buy-now'])) {
                        echo "<script>window.location.href = 'cartdetail.php?productid={$product_id}';</script>";
                    }
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    echo "<script>alert('Product with this ID already exists in the cart for this email!');</script>";
                    echo "<script>window.location.href = 'carddetail.php?productid={$product_id}';</script>";
                } else {
                    echo "<script>alert('SQL Error: " . $e->getMessage() . "');</script>";
                }
            }

            $stmt->close();
        }
    }

    $conn->close();
}

$productid = $_GET['productid'] ?? '';

if (!empty($productid)) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        exit;
    }

    $bindType = is_numeric($productid) ? "i" : "s";
    $stmt->bind_param($bindType, $productid);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        $name = htmlspecialchars($product['name'] ?? 'No Name');
        $price = htmlspecialchars($product['price'] ?? '0.00');
        $description = htmlspecialchars($product['description'] ?? 'No Description');
        $image = htmlspecialchars($product['image'] ?? 'default.jpg');
        $sizeOptions = explode(',', htmlspecialchars($product['size'] ?? ''));
        $colorOptions = explode(',', htmlspecialchars($product['color'] ?? ''));
        $quantity = htmlspecialchars($product['quantity'] ?? '1');

        $image_paths = explode(',', $image);

        // Fetch ratings and reviews
        $ratingSql = "SELECT rating, review, email FROM rating WHERE product_id = ?";
        $ratingStmt = $conn->prepare($ratingSql);

        if (!$ratingStmt) {
            error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            exit;
        }

        $ratingStmt->bind_param($bindType, $productid);
        $ratingStmt->execute();
        $ratingResult = $ratingStmt->get_result();
        $ratings_reviews = [];
        $totalRating = 0;
        $ratingCount = 0;

        while ($row = $ratingResult->fetch_assoc()) {
            $rating = intval($row['rating']);
            $ratings_reviews[] = [
                'rating' => $rating,
                'review' => htmlspecialchars($row['review']),
                'email' => htmlspecialchars($row['email'])
            ];
            $totalRating += $rating;
            $ratingCount++;
        }

        $averageRating = ($ratingCount > 0) ? ($totalRating / $ratingCount) : 0;

        $ratingStmt->close();

        // Display product information
        // Add your HTML code here to display $name, $price, $description, $image_paths, $sizeOptions, $colorOptions, $quantity, and $ratings_reviews
    } else {
        echo "<p>Product not found.</p>";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Product Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="boostrap/css/bootstrap.min.css">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="boostrap/fontawesome-free-6.6.0-web/css/all.min.css">
     <style>
    .rating-reviews .fa-star {
        color: #FFD700; /* Gold color for filled stars */
        font-size: 1.5em; /* Adjust size as needed */
    }
    .rating-reviews .fa-star:not(.text-warning) {
        color: #e4e5e9; /* Light grey for empty stars */
    }

    body {
        font-family: arial, 'Poppins', sans-serif;
        background-color: #f8f9fa;
    }
    h1 {
        font-size: 3em;
        font-weight: bold;
        color: black;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1.2;
        margin: 20px 0;
    }
    h4 {
        font-size: 3em;
        font-weight: bold;
        color: black;
        text-transform: uppercase;
        letter-spacing: 2px;
        line-height: 1.2;
        margin: 20px 0;
        font-family: 'Montserrat', sans-serif;
    }
    .container.product-details {
        margin-top: 20px;
        font-size: 20px;
    }
    .flexx {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .image-section {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .main-image {
        max-width: 100%;
        height: fit-content;
        border: 3px solid #f0f0f0;
        margin-bottom: 10px;
    }
    .thumbnail-images {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .thumbnail {
        cursor: pointer;
        border: 2px solid #ddd;
        padding: 2px;
        border-radius: 5px;
    }
    .thumbnail img {
        max-width: 100px;
        height: auto;
    }
    .details-section {
        flex: 1;
    }
    .product-name {
        font-size: 2.5em;
        margin: 0;
    }
    .product-price {
        font-size: 2em;
        color: #4CAF50;
        margin: 10px 0;
    }
    .product-size,
    .product-color,
    .quantity-input {
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
    }
    .quantity-input {
        width: auto;
    }
    .action-btn {
        display: inline-block;
        background: green;
        color: white;
        padding: 10px 20px;
        margin: 10px 0;
        border-radius: 5px;
        text-align: center;
        border: none;
        cursor: pointer;
    }
    .action-btn:hover {
        transition: 0.3s ease;
        background: darkgreen;
        color: black;
    }
    .product-description {
        margin-top: 20px;
    }
    .rating-reviews {
        margin-top: 20px;
    }
    .rating-reviews h3 {
        margin-bottom: 10px;
    }

    /* Media Query for screens up to 430px */
    @media screen and (max-width: 430px) {
        .main-image {
            width: 95vw; /* Ensure main image takes up full width */
        }
    }

    @media screen and (max-width: 768px) {
        .flexx {
            flex-direction: column;
        }
       
    }
    </style>
    <script>
        function selectImage(imagePath) {
            document.querySelector('.main-image').src = imagePath;
            document.querySelector('input[name="selected_image"]').value = imagePath;
        }
    </script>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class='container product-details'>
            <div class='flexx'>
                <div class='image-section'>
                    <img src='<?php echo htmlspecialchars($image_paths[0] ?? 'default.jpg'); ?>' class='main-image' alt='Product Image'>
                    <div class='thumbnail-images'>
                        <?php foreach ($image_paths as $path): ?>
                            <div class='thumbnail'>
                                <img src='<?php echo htmlspecialchars($path); ?>' alt='Product Image' onclick="selectImage('<?php echo htmlspecialchars($path); ?>')">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class='details-section'>
                    <h1 class='product-name'><?php echo $name; ?></h1>
                    <h4 class='product-price'><?php echo $price; ?></h4>
                    <select name='product_size' class='product-size'>
                        <option value=''>Select Size</option>
                        <?php foreach ($sizeOptions as $sizeOption): ?>
                            <option value='<?php echo htmlspecialchars($sizeOption); ?>'><?php echo htmlspecialchars($sizeOption); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type='number' name='product_quantity' value='<?php echo htmlspecialchars($quantity); ?>' min='1' class='quantity-input'>
                    <select name='product_color' class='product-color'>
                        <option value=''>Select Color</option>
                        <?php foreach ($colorOptions as $color): ?>
                            <option value='<?php echo htmlspecialchars($color); ?>'><?php echo htmlspecialchars($color); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type='hidden' name='product_id' value='<?php echo htmlspecialchars($productid); ?>'>
                    <input type='hidden' name='product_name' value='<?php echo htmlspecialchars($name); ?>'>
                    <input type='hidden' name='product_price' value='<?php echo htmlspecialchars($price); ?>'>
                    <input type='hidden' name='email' value='<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>'>
                    <input type='hidden' name='selected_image' value='<?php echo htmlspecialchars($image_paths[0] ?? 'default.jpg'); ?>'>
                    <button type='submit' name='add_to_cart' class='action-btn'>Add To Cart</button>
                    <button type='submit' name='buy-now' class='action-btn'>Buy Now</button>
                    <h3>Product Details</h3>
                    <p class='product-description'><?php echo $description; ?></p>
                    <div class="rating-reviews mt-4">
                        <h3>Ratings and Reviews:</h3>
                        <ul id="review-list">
                            <?php foreach ($ratings_reviews as $review): ?>
                                <li>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa fa-star <?php echo ($i <= $review['rating']) ? 'text-warning' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p><strong><?php echo htmlspecialchars($review['email']); ?></strong>: <?php echo htmlspecialchars($review['review']); ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p>Average Rating: <?php echo number_format($averageRating, 1); ?> / 5</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="related-products-section">
        <!-- Related products will be injected here -->
    </div>
    <?php include('relatedproduct.php'); ?>
    <?php include('foooter.php'); ?>
</body>
</html>


