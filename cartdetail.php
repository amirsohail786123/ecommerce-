<?php
session_start();
include('navbardesign.php');
include('config.php');

// Initialize message variable
$message = '';

// Check if 'action' key is set in $_GET array
if (isset($_GET['action'])) {
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
    $productId = filter_input(INPUT_GET, 'product_id', FILTER_SANITIZE_STRING);
    $productQuantity = filter_input(INPUT_GET, 'product_quantity', FILTER_VALIDATE_INT);

    if ($action === 'update') {
        if ($productId && is_numeric($productQuantity) && $productQuantity > 0) {
            // Check if the product exists in the database
            $checkSql = "SELECT * FROM orders WHERE product_id = ? AND email = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param('ss', $productId, $_SESSION['email']);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows === 0) {
                $message = 'Product not found or you do not have permission to modify it.';
            } else {
                // Prepare and execute update query
                $sql = "UPDATE orders SET product_quantity = ? WHERE product_id = ? AND email = ?";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param('iss', $productQuantity, $productId, $_SESSION['email']);
                    if ($stmt->execute()) {
                        $message = 'Quantity updated successfully.';
                    } else {
                        $message = 'Error updating quantity: ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $message = 'Prepare failed: ' . $conn->error;
                }
            }
            $checkStmt->close();
        } else {
            $message = 'Invalid product ID or quantity.';
        }
    } elseif ($action === 'delete') {
        if ($productId) {
            $sql = "DELETE FROM orders WHERE product_id = ? AND email = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('ss', $productId, $_SESSION['email']);
                if ($stmt->execute()) {
                    $message = 'Product deleted successfully.';
                } else {
                    $message = 'Error deleting product: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = 'Prepare failed: ' . $conn->error;
            }
        } else {
            $message = 'Invalid product ID.';
        }
    } else {
        $message = 'Invalid action.';
    }

    // Redirect or refresh page after action
    echo "<script>alert('$message'); window.location.href='cartdetail.php';</script>";
}

// Fetch cart data only for the logged-in user
$sql = "SELECT product_id, product_name, product_price, product_quantity, image FROM orders WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();

$totalCartAmount = 0;



$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="boostrap/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="boostrap/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        /* Basic styles */
        body {
            background-color: white;
        }
        h1 {
            margin: 20px;
            text-align: center;
        }
        /* Cart container styles */
        #cart-container {
            width: 100%;
            margin: 0 auto;
        }
        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 2px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        /* Product image styling */
        .product-image {
            width: 100px;
            height: auto;
        }
        /* Cart summary styles */
        #cart-summary {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #fff;
            text-align: left;
        }
        /* Confirm order button styles */
        #confirm-order-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        #confirm-order-btn:hover {
            background-color: #45a049;
        }
        /* Update and Delete buttons */
        .update-btn, .delete-btn {
            color: white;
            border: none;
            text-decoration: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
        }
        .update-btn {
            background-color: #007bff;
        }
        .update-btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .thumbnail-images{
            flex-wrap: wrap;
        }
        
        /* Responsive styles */
        @media screen and (max-width: 1080px) {
            table, th, td {
                font-size: 14px;
            }
            body {
                width: fit-content;
            }
        }
        @media screen and (max-width: 768px){
            .update-btn, .delete-btn {
                padding: 2px ;
                font-size: 12px;
                border-radius: 3px;
            }
        }
    </style>
</head>
<body>
<h1>Shopping Cart</h1>
<form id="cart-form">
    <div id="cart-container">
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Sub-total</th>
                    <th>Action</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody id="cart-body">
                <?php  
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $total = $row["product_price"] * $row["product_quantity"];
                        $totalCartAmount += $total;
                        echo "<tr data-product-id='" . htmlspecialchars($row["product_id"], ENT_QUOTES, 'UTF-8') . "'>";
                        echo "<td><img src='" . htmlspecialchars($row["image"], ENT_QUOTES, 'UTF-8') . "' alt='Product Image' class='product-image'></td>";
                        echo "<td>" . htmlspecialchars($row["product_name"], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . number_format($row["product_price"], 2) . "</td>";
                        echo "<td>
                                <form method='GET' action='cartdetail.php' style='display:inline;'>
                                    <input type='hidden' name='action' value='update'>
                                    <input type='hidden' name='product_id' value='" . htmlspecialchars($row["product_id"], ENT_QUOTES, 'UTF-8') . "'>
                                    <input type='number' name='product_quantity' value='" . htmlspecialchars($row["product_quantity"], ENT_QUOTES, 'UTF-8') . "' min='1'>
                                    <button type='submit' class='update-btn'>Update</button>
                                </form>
                              </td>";
                        echo "<td class='item-subtotal'>" . number_format($total, 2) . "</td>";
                        echo "<td>
                                <form method='GET' action='cartdetail.php' style='display:inline;'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='product_id' value='" . htmlspecialchars($row["product_id"], ENT_QUOTES, 'UTF-8') . "'>
                                    <button type='submit' class='delete-btn'>Delete</button>
                                </form>
                              </td>";
                        echo "<td><input type='checkbox' name='item_select' value='" . htmlspecialchars($row["product_id"], ENT_QUOTES, 'UTF-8') . "'></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>0 results</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</form>
<?php
$shippingCharge = 100;
$totalAmountIncludingShipping = $totalCartAmount + $shippingCharge;
?>
<div id="cart-summary">
    <h3>Total Amount (Excluding Shipping): PKR<?php echo number_format($totalCartAmount, 2); ?></h3>
    <h3>Shipping Charge: PKR<?php echo number_format($shippingCharge, 2); ?></h3>
    <h2>Total Amount (Including Shipping): PKR<?php echo number_format($totalAmountIncludingShipping, 2); ?></h2>
    <h3>Total for Selected Items: <span id="selected-total">PKR0.00</span></h3>
    <form action="checkout.php" method="GET" style="display:inline;">
        <input type="hidden" name="product_id" id="selected-products">
        <input type="submit" value="Confirm Order" id="confirm-order-btn">
    </form>
</div>
<script>
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('input[name="item_select"]:checked').forEach(function(checkbox) {
            const row = checkbox.closest('tr');
            const subtotalText = row.querySelector('.item-subtotal').textContent.trim();
            const subtotalValue = parseFloat(subtotalText.replace('Rs', '').replace(',', ''));
            if (!isNaN(subtotalValue)) {
                total += subtotalValue;
            }
        });
        document.getElementById('selected-total').textContent = `Rs${total.toFixed(2)}`;
        const selectedItems = Array.from(document.querySelectorAll('input[name="item_select"]:checked')).map(function(checkbox) {
            return checkbox.value;
        }).join(',');
        document.getElementById('selected-products').value = selectedItems;
    }

    // Update total amount for selected items on page load
    updateTotal();

    // Update total when checkbox is changed
    document.querySelectorAll('input[name="item_select"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', updateTotal);
    });
</script>
<?php
include 'foooter.php'; // Ensure this file exists and is included correctly
?>
</body>
</html>
