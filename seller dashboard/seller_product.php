<?php
session_start(); // Start the session

// Include the database connection
include('config.php'); // Make sure this file contains your database connection details

// Function to sanitize inputs
function sanitize_input($value) {
    return htmlspecialchars(strip_tags(trim($value)));
}

// Function to get user IP address
function get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Function to get device info
function get_device_info() {
    return $_SERVER['HTTP_USER_AGENT'];
}

// Function to generate a unique product ID
function generateUniqueProductId() {
    return uniqid('prod_', true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = '';

    // Retrieve and sanitize form data
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $price = sanitize_input($_POST['price']);
    $color = sanitize_input($_POST['color']);
    $size = sanitize_input($_POST['size']);
    $category = sanitize_input($_POST['category']);
    $ip_address = get_user_ip();
    $device_info = get_device_info();

    // Ensure the seller email is set in the session
    if (!isset($_SESSION['seller_email'])) {
        $message = "Seller email is not set in the session.";
    } else {
        $seller_email = $_SESSION['seller_email'];

        // Fetch the seller_email from the database
        $sql = "SELECT email FROM seller WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $seller_email);
        $stmt->execute();
        $stmt->bind_result($db_email);
        $stmt->fetch();
        $stmt->close();

        // Check if the email from the session matches the email in the database
        if (empty($db_email) || $seller_email !== $db_email) {
            $message = "Seller email does not match or seller not found.";
        } else {
            // Handle file uploads
            $images = $_FILES['images'];
            $uploaded_image_urls = [];

            if (isset($images['name']) && count($images['name']) >= 4) {
                $upload_dir = 'uploads/';

                // Ensure the directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Loop through each uploaded image
                for ($i = 0; $i < count($images['name']); $i++) {
                    if ($images['error'][$i] === UPLOAD_ERR_OK) {
                        $image_name = basename($images['name'][$i]);
                        $upload_file = $upload_dir . $image_name;

                        if (move_uploaded_file($images['tmp_name'][$i], $upload_file)) {
                            $uploaded_image_urls[] = $upload_file;
                        } else {
                            $message = "Failed to upload file: " . $image_name . "<br>";
                        }
                    } else {
                        $message = "Error uploading file: " . $images['name'][$i] . "<br>";
                    }
                }

                if (empty($message)) {
                    // Convert array of image URLs to a comma-separated string
                    $image_urls = implode(',', $uploaded_image_urls);

                    // Prepare SQL to insert the product
                    $sql = "INSERT INTO product (product_id, `name`, `description`, price, `color`, `size`, `category`, `image`, `upload-ip`, `upload-device`, user_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);

                    // Generate a unique product ID
                    $product_id = generateUniqueProductId();

                    // Bind parameters
                    $stmt->bind_param('sssisssssss', $product_id, $name, $description, $price, $color, $size, $category, $image_urls, $ip_address, $device_info, $seller_email);

                    // Execute the statement
                    if ($stmt->execute()) {
                        $message = "Product successfully uploaded!";
                    } else {
                        $message = "Error: " . $stmt->error;
                    }

                    // Close the statement
                    $stmt->close();
                }
            } else {
                $message = "Please upload at least 4 images.";
            }
        }
    }
    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
            padding: 20px 0;
        }

        form {
            max-width: 700px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            width: calc(100% - 16px);
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group:last-of-type {
            margin-bottom: 0;
        }

        .error {
            color: red;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            font-size: 1.1em;
            margin-top: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Handle form submission using Ajax
            $('#productForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                // Validate the form fields
                var isValid = true;
                var message = '';

                // Validate Name
                if ($('#name').val().trim() === '') {
                    isValid = false;
                    message = 'Product Name is required.';
                }

                // Validate Description
                if ($('#description').val().trim() === '') {
                    isValid = false;
                    message = 'Description is required.';
                }

                // Validate Price
                if ($('#price').val().trim() === '' || isNaN($('#price').val())) {
                    isValid = false;
                    message = 'Please enter a valid price.';
                }

                // Validate Color
                if ($('#color').val() === '') {
                    isValid = false;
                    message = 'Please select a color.';
                }

                // Validate Size
                if ($('#size').val() === '') {
                    isValid = false;
                    message = 'Please select a size.';
                }

                // Validate Category
                if ($('#category').val() === '') {
                    isValid = false;
                    message = 'Please select a category.';
                }

                // Validate Images
                var files = $('#images')[0].files;
                if (files.length < 4) {
                    isValid = false;
                    message = 'Please upload at least 4 images.';
                }

                if (!isValid) {
                    $('#errorMessage').text(message);
                    return;
                }

                // Ajax form submission
                var formData = new FormData(this); // Form data with files
                $.ajax({
                    url: 'seller_product.php', // Submit to the same page
                    type: 'POST',
                    data: formData,
                    contentType: false, // Don't set content type
                    processData: false, // Don't process data
                    success: function(response) {
                    // Show success message in alert after product is uploaded successfully
                    alert('Product uploaded successfully!');
                    $('#productForm')[0].reset(); // Reset the form fields after success
                },
                });
            });
        });
    </script>
</head>
<body>

<h1>Upload Product</h1>

<?php if (!empty($message)): ?>
    <div class="success" id="message">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<form id="productForm" method="POST" enctype="multipart/form-data">
    <label for="name">Product Name:</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" rows="4" cols="50"></textarea><br><br>

    <label for="price">Price:</label>
    <input type="number" id="price" name="price" step="0.01" required><br><br>

    <label for="color">Color:</label>
    <select id="color" name="color" required>
    <option value="">Select Color</option>
    <option value="red">Red</option>
    <option value="blue">Blue</option>
    <option value="green">Green</option>
    <option value="yellow">Yellow</option>
    <option value="orange">Orange</option>
    <option value="purple">Purple</option>
    <option value="pink">Pink</option>
    <option value="brown">Brown</option>
    <option value="black">Black</option>
    <option value="white">White</option>
    <option value="gray">Gray</option>
    <option value="cyan">Cyan</option>
    <option value="magenta">Magenta</option>
    <option value="violet">Violet</option>
    <option value="indigo">Indigo</option>
    <option value="beige">Beige</option>
    <option value="turquoise">Turquoise</option>
    <option value="gold">Gold</option>
    <option value="silver">Silver</option>
</select>
<br><br>

    <label for="size">Size:</label>
    <select id="size" name="size" required>
    <option value="">Select Size</option>
    <option value="small">Small</option>
    <option value="medium">Medium</option>
    <option value="large">Large</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
    <option value="11">11</option>
    <option value="12">12</option>
</select>
<br><br>

    <label for="category">Category:</label>
    <select id="category" name="category" required>
    <option value="">Select Category</option>
    <option value="clothing">Clothing</option>
    <option value="electronics">Electronics</option>
    <option value="furniture">Furniture</option>
    <option value="home_appliances">Home Appliances</option>
    <option value="books">Books</option>
    <option value="toys">Toys</option>
    <option value="sports">Sports</option>
    <option value="beauty">Beauty</option>
    <option value="health">Health</option>
    <option value="automotive">Automotive</option>
    <option value="groceries">Groceries</option>
    <option value="music">Music</option>
    <option value="games">Games</option>
    <option value="art">Art</option>
    <option value="office_supplies">Office Supplies</option>
    <option value="pets">Pets</option>
    <option value="tools">Tools</option>
    <option value="garden">Garden</option>
    <option value="baby_products">Baby Products</option>
    <option value="food_beverages">Food & Beverages</option>
</select>
<br><br>

    <label for="images">Upload Images:</label>
    <input type="file" id="images" name="images[]" accept="image/*" multiple required><br><br>

    <input type="submit" value="Upload Product">
</form>

<!-- Show messages here -->
<div id="message"></div> <!-- Success message -->
<div id="errorMessage" style="color: red;"></div> <!-- Error message -->


</body>
</html>
