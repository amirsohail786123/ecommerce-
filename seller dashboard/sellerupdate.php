<?php
session_start();
include('config.php');

// Check if the product_id is set in the URL
if (!isset($_GET['product_id'])) {
    die("Product ID is required.");
}

$product_id = $_GET['product_id'];

// Prepare and execute the SQL statement to fetch the product details
$sql = "SELECT product_id, image, name, size, quantity, color, description, price FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('s', $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$product = $result->fetch_assoc();

if ($product === null) {
    die("Product not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];
    $color = $_POST['color'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Prepare and execute the SQL statement to update the product
    $sql_update = "UPDATE product SET `name` = ?, size = ?, quantity = ?, color = ?, `description` = ?, price = ? WHERE product_id = ?";
    $stmt_update = $conn->prepare($sql_update);

    if ($stmt_update === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt_update->bind_param('ssissis', $name, $size, $quantity, $color, $description, $price, $product_id);
    $stmt_update->execute();

    if ($stmt_update->affected_rows > 0) {
        echo "Product updated successfully.";
        header('location:seller_dashboard.php');
    } else {
        echo "No changes made to the product.";
    }

    $stmt_update->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
</head>
<body>
    <h1>Update Product</h1>
    <form action="" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        <br>
        <label for="size">Size:</label>
        <input type="text" id="size" name="size" value="<?php echo htmlspecialchars($product['size']); ?>" required>
        <br>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
        <br>
        <label for="color">Color:</label>
        <input type="text" id="color" name="color" value="<?php echo htmlspecialchars($product['color']); ?>" required>
        <br>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        <br>
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
        <br>
        <input type="submit" value="Update Product">
    </form>

    <style>
/* General body styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Container styling for the form */
.container {
    width: 90%;
    max-width: 700px;
    margin: 40px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    border: 2px solid #2874f0; /* Border color and width */
}

/* Heading styling */
h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

/* Form label styling */
label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

/* Input and textarea styling */
input[type="text"],
input[type="number"],
textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box; /* Ensure padding does not affect width */
    font-size: 16px;
}

/* Submit button styling */
input[type="submit"] {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #2874f0;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-bottom: 30px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #1c4f91;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow on hover */
}

/* Responsive styling */
@media (max-width: 600px) {
    .container {
        width: 95%;
    }

    input[type="text"],
    input[type="number"],
    textarea {
        font-size: 14px; /* Adjust font size for smaller screens */
    }

    input[type="submit"] {
        font-size: 14px; /* Adjust font size for smaller screens */
    }
}

</style>
</body>
</html>
