<?php
include('config.php'); // Include the database connection

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 16; // Number of products per page
$offset = ($page - 1) * $limit;

// Query to fetch product data
$sql = "SELECT product_id, `name`, `description`, `image`, `price` FROM product LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['product_id'];
        $name = $row['name'];
        $price = $row['price'];
        $image = $row['image'];
        $description = $row['description'];
        
        $images = explode(",", $image);
        echo '
        <div class="cardmain">
            <div class="cards">
                <a href="carddetail.php?productid=' . $id . '">
                    <img src="' . $images[0] . '" alt="Product Image">
                    <h4>' . $name . '</h4>
                  
                    <span class="price">$' . number_format($price, 2) . '</span>
                    <button type="button" onclick="location.href=\'carddetail.php?productid=' . $id . '\'">View Details</button>
                </a>
            </div>
        </div>';
    }
} else {
    // No more products to load
    echo '';
}

$conn->close();
?>
