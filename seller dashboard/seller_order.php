<?php
session_start();
include('config.php');

// Check if the user is logged in and email is set in session
if (!isset($_SESSION['seller_email'])) {
    header('Location: sellerlogin.php');
    exit();
}

// Get the seller's email from the session
$seller_email = $_SESSION['seller_email'];

// Prepare the SQL query to fetch the order history
$sql = "SELECT * FROM orders WHERE seller = ?";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $seller_email);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="bootstrap/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        body {
            background-color: white;
        }
        h1 {
            margin: 30px;
            font-weight: 600;
            text-align: center;
            color: #333;
        }
        .order-table {
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th, .order-table td {
            border: 1px solid #ddd;
            text-align: center;
        }
        .order-table th {
            background-color: #f4f4f4;
            color: #333;
        }
        .order-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .order-table tr:hover {
            background-color: #f1f1f1;
        }
        .product-image {
            width: 100px;
            height: auto;
        }
        /* Responsive styles */
        @media screen and (max-width: 1080px) {
            body {
                width: fit-content;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Orders</h1>
        <?php
        if ($result->num_rows > 0) {
            echo "<table class='order-table'>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>DateTime</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";
            
            while ($row = $result->fetch_assoc()) {
                $order_product_id = htmlspecialchars($row['product_id']);
                echo "<tr>
                        <td><img src='" . htmlspecialchars($row['image']) . "' alt='Product Image' class='product-image'></td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['product_name']) . "</td>
                        <td>" . htmlspecialchars($row['product_size']) . "</td>
                        <td>" . htmlspecialchars($row['product_quantity']) . "</td>
                        <td>$" . htmlspecialchars(number_format($row['product_price'], 2)) . "</td>
                        <td>" . htmlspecialchars($row['order_status']) . "</td>
                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                        <td>
                            <a href='seller_orderconfirm.php?product_id=" . urlencode($row['product_id']) . "' class='confirm-link'>Confirm Order</a>
                        </td>
                    </tr>";
            }
            
            echo "</tbody>
                </table>";
        } else {
            echo "No orders found.";
        }
        ?>
    </div>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>
