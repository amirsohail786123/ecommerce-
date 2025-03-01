<?php
// Include navigation bar and database configuration
include 'navbardesign.php';
include 'config.php';

// Retrieve and validate product_id(s) from URL
$product_ids = isset($_GET['product_id']) ? trim($_GET['product_id']) : '';

if ($product_ids) {
    $product_ids_array = array_map('trim', explode(',', $product_ids));
    
    // Check if product_id exists in orders table
    $placeholders = implode(',', array_fill(0, count($product_ids_array), '?'));
    $query = "SELECT product_id FROM orders WHERE product_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $types = str_repeat('s', count($product_ids_array)); // For product IDs as strings
        $stmt->bind_param($types, ...$product_ids_array);
        $stmt->execute();
        $result = $stmt->get_result();

        $existing_product_ids = [];
        while ($row = $result->fetch_assoc()) {
            $existing_product_ids[] = $row['product_id'];
        }
        $stmt->close();

        $all_ids_valid = !array_diff($product_ids_array, $existing_product_ids);

        if ($all_ids_valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $mobile_no = isset($_POST['mobile_no']) ? trim($_POST['mobile_no']) : '';
            $ship_address = isset($_POST['ship_address']) ? trim($_POST['ship_address']) : '';

            // Update query with placeholders for product IDs
            $updateQuery = "UPDATE orders SET mobile_no = ?, ship_adress = ? WHERE product_id IN ($placeholders)";
            $stmt = $conn->prepare($updateQuery);

            if ($stmt) {
                // Bind parameters for mobile_no, ship_address and product_ids
                $stmt->bind_param('ss' . str_repeat('s', count($product_ids_array)), $mobile_no, $ship_address, ...$product_ids_array);

                if ($stmt->execute()) {
                    echo "<p class='success'>Orders Placed successfully.</p>";
                } else {
                    echo "<p class='error'>Error updating orders: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p class='error'>Error preparing statement: " . $conn->error . "</p>";
            }
        } elseif (!$all_ids_valid) {
            echo "<p class='error'>One or more product IDs are invalid.</p>";
        }
    } else {
        echo "<p class='error'>Error preparing statement: " . $conn->error . "</p>";
    }
} else {
    echo "<p class='error'>Invalid product ID(s).</p>";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Checkout</h1>
    </header>
    <main>
        <form action="" method="POST" id="order-form">
            <section>
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="mobile_no" required>
            </section>

            <section>
                <h2>Shipping Information</h2>
                <label for="address">Address:</label>
                <input type="text" id="address" name="ship_address" required>
            </section>

            <section>
                <h2>Payment Information</h2>
                <label for="payment">Cash on Delivery:</label>
                <input type="text" id="payment" name="payment" required>
            </section>

            <button type="submit">Place Order</button>
        </form>
    </main>
    
<!-- CSS -->
<style>
/* Basic reset */
body, h1, h2, p, form, input, select, button {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-color: #f4f4f4;
    color: #333;
}
header {
    background: #2874f0;
    color: #fff;
    padding: 1rem 0;
    text-align: center;
}
main {
    max-width: 800px;
    margin: 2rem auto;
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    padding-bottom: 50px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
form section {
    margin-bottom: 40px;
}
form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}
form input[type="text"],
form input[type="tel"],
form select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}
form button {
    padding: 0.7rem 1.5rem;
    background-color: #2874f0;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
form button:hover {
    background-color: skyblue;
}
.success {
    color: green;
}
.error {
    color: red;
}
footer {
    text-align: center;
    padding: 1rem 0;
    background: #2874f0;
    color: #fff;
    position: fixed;
    width: 100%;
    bottom: 0;
}
</style>

   
</body>
</html>
