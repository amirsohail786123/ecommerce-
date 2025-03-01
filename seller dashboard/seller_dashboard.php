<?php
session_start();
// Include the database connection
include('config.php');

// Check if the email is set in the session
if (!isset($_SESSION['seller_email'])) {
    header('location:sellerlogin.php'); // Redirect to login if no session exists
    exit(); // Always exit after redirection to prevent further code execution
}

$seller_email = $_SESSION['seller_email']; // Get the email from session

// Prepare and execute the SQL statement to fetch product details where user_id matches seller_email
$sql = "SELECT product_id, `image`, `name`, size, quantity, color, description, price FROM product WHERE `user_id` = ?";
$stmt = $conn->prepare($sql);

// Check for preparation errors
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show error if preparation fails
}

// Bind parameters (seller_email is a string, so bind as 's')
$stmt->bind_param('s', $seller_email);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check for execution errors
if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error)); // Show error if execution fails
}

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        /* Base Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1, h2 {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: black; 
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            flex-wrap: wrap;
            text-align: center;
            width: 100%;
            margin: 0 auto;
        }
        .link-buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .link-button {
            display: inline-block;
            width: 100%; 
            max-width: 300px; 
            margin-bottom: 20px;
            text-align: center;
            padding: 10px 20px;
            color: black; 
            background-color: #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
            border: 2px solid white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        }

        .link-button:hover {
            color: black; 
            background-color: transparent;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            margin-bottom: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2874f0;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        .order-status-button,
        .action-links a {
            display: inline-block;
            background-color: #2874f0;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            margin: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .order-status-button:hover,
        .action-links a:hover {
            background-color: #1c4f91;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Seller Dashboard</h1>
        <h2>Click for Upload Products and Check orders</h2>
        <a href="seller_product.php" class="link-button">Add Product</a> 
        <a href="seller_order.php" class="link-button">My Orders</a> 

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Color</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $images = explode(',', $row['image']); // Split the image URLs
                        $first_image = isset($images[0]) ? htmlspecialchars(trim($images[0])) : 'path/to/default/image.jpg'; // Fallback image

                        echo "<tr>";
                        echo "<td><img src='" . $first_image . "' alt='Product Image'></td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['size']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['color']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "<td class='action-links'>";
                        echo "<a href='sellerupdate.php?product_id=" . htmlspecialchars($row['product_id']) . "'><span>Update</span></a>  ";
                        echo "<a href='sellerdelete.php?product_id=" . htmlspecialchars($row['product_id']) . "' onclick='return confirm(\"Are you sure you want to delete this product?\")'><span>Delete</span></a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No products found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
