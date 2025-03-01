<?php
session_start();
include('config.php');

$user_email = $_SESSION['seller_email'];

if (!$user_email) {
    header('Location: sellerlogin.php');
    exit();
}

// Fetch product_id from the URL
$product_id = isset($_GET['product_id']) ? trim($_GET['product_id']) : '';

if (empty($product_id)) {
    die("No product ID provided.");
}

// Debugging information
echo "Received Product ID: " . htmlspecialchars($product_id) . "<br>";
echo "User Email from Session: " . htmlspecialchars($user_email) . "<br>";

// Validate that product IDs are for the logged-in user
$sql = "SELECT product_id FROM orders WHERE seller = ? AND product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('ss', $user_email, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

// Debugging: print the number of rows found
echo "Number of rows found: " . $result->num_rows . "<br>";

if ($result->num_rows === 0) {
    die("No valid product found for the user.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_status = $_POST['order_status'];

    if (empty($order_status)) {
        $message = "<p class='error-message'>Order status is missing.</p>";
    } else {
        // Prepare the update query
        $update_sql = "UPDATE orders SET order_status = ? WHERE product_id = ? AND seller = ?";
        $update_stmt = $conn->prepare($update_sql);

        if ($update_stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $update_stmt->bind_param("sss", $order_status, $product_id, $user_email);

        if ($update_stmt->execute()) {
            $message = "<p class='success-message'>Order status updated successfully!</p>";
        } else {
            $message = "<p class='error-message'>Failed to update order status: " . htmlspecialchars($update_stmt->error) . "</p>";
        }
        $update_stmt->close();
    }
}

// Close connections
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Order Status</h1>
        <?php
        if (isset($message)) {
            echo $message;
        }
        ?>
        <form method="post" action="">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="order_status">Order Status:</label>
            <select name="order_status" id="order_status" required>
                <option value="confirmed"> user Confirmed</option>
                <option value="Out of Stock">Out of Stock</option>
            </select>

            <button type="submit">Update Status</button>
        </form>
    </div>
</body>
</html>
