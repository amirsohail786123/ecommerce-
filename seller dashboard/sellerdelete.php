<?php
session_start();
include('config.php');

// Check if the product_id is set in the URL
if (!isset($_GET['product_id'])) {
    die("Product ID is required.");
}

$product_id = $_GET['product_id'];

// Prepare and execute the SQL statement to delete the product
$sql = "DELETE FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('s', $product_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Product deleted successfully.";
    header('location:seller_dashboard.php');
} else {
    echo "Product not found or could not be deleted.";
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
</head>
<body>
    <h1>Delete Product</h1>
    <p>Product with ID <?php echo htmlspecialchars($product_id); ?> has been deleted.</p>
    <a href="seller_dashboard.php">Back to Product List</a>
</body>
</html>
