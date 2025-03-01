<?php
include 'config.php';


// Fetching unique categories and their images from the product table
$sql = "SELECT DISTINCT category, image FROM product WHERE image IS NOT NULL";
$result = $conn->query($sql);

$categories = []; // Array to store unique categories and their images

if ($result->num_rows > 0) {
    // Populate the categories array with unique entries
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        $images = explode(',', $row['image']); // Split images by comma

        // Store only the first image for each unique category
        if (!isset($categories[$category])) {
            $categories[$category] = $images[0]; // Store only the first image
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories and Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h1
{
    margin-top: 40px;
}
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 40px;
          margin-top: 40px;
        }

        .card {
            text-decoration: none;
            color: black;
            background-color: #ffffff;
            border-radius: 10px;
            width: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .product-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 200px;
            text-align: center;
        }

        .product-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .maincard {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Main Card Container */
        .cardmain {
            margin: 10px;
            padding: 10px;
        }

        /* Card Styling */
        .cards {
            width: 250px;
            height: max-content;
            background-color: #fff;
            border: 6px solid #f0f0f0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            text-align: center;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            text-decoration: none;
        }

        .cards:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
        }

        /* Card Image */
        .cards img {
            width: 100%;
            height: 200px;
            border-bottom: 3px solid #f0f0f0;
            object-fit: cover;
            transition: 0.3s ease;
        }

        /* Card Title */
        .cards h4 {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Card Description */
        .cards span {
            display: block;
            margin: 0 10px 10px;
            font-size: 14px;
            color: #666;
            text-align: left;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Price Styling */
        .price {
            font-weight: bold;
            color: #333;
            margin: 5px 0;
        }

        /* Card Button */
        .cards button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin: 10px;
        }

        .cards button:hover {
            background-color: #0056b3;
        }

        /* Remove link styling */
        .cards a {
            text-decoration: none;
            color: inherit;
        }

        /* Loader */
        .loader {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        /* Responsive Design */
        @media screen and (max-width: 1080px) {
            .cards {
                width: 180px;
                height: 280px;
            }

            .cards img {
                width: 100%;
                height: 150px;
            }
        }

        @media screen and (max-width: 870px) {
            .cards {
                width: 180px;
                height: 280px;
            }

            .cards img {
                width: 100%;
                height: 150px;
            }
        }

        @media screen and (max-width: 480px) {
            .cards {
                width: 130px;
                height: 190px;
                border: 2px solid #f0f0f0;
            }

            .cards img {
                width: 100%;
                height: 100px;
            }

            .cards button {
                font-size: 10px;
                padding: 3px;
            }

            .cards h4 {
                font-size: 10px;
                font-style: bold;
            }

            .cards span {
                font-size: 10px;
            }
        }

        @media screen and (max-width: 360px) {
            .cards {
                width: 120px;
                height: 190px;
                border: 2px solid #f0f0f0;
            }

            .cards img {
                width: 100%;
                height: 100px;
            }
        }
    </style>
</head>
<body>
<h1>Categories</h1>
    
    <form method="POST" action="">
        <div class="card-container">
    
        <?php
if (!empty($categories)) {
    // Get the unique keys (categories)
    $categoryKeys = array_keys($categories);

    // Loop through the categories using their keys
    for ($i = 0; $i < count($categoryKeys); $i++) {
        $category = $categoryKeys[$i];
        $image = $categories[$category]; // Get the corresponding image

        // Each card is a submit button for the selected category
        echo "<button type='submit' name='selected_category' value='" . htmlspecialchars($category) . "' class='card'>";
        echo "<img src='" . htmlspecialchars(trim($image)) . "' alt='" . htmlspecialchars($category) . "'>";
        echo "<div>" . htmlspecialchars($category) . "</div>";
        echo "</button>";
    }
} else {
    echo "<div>No categories found.</div>";
}
?>
        </div>
    </form>

    
    <!-- Container for displaying products -->
    <div class="product-container">
        <?php
        if (isset($_POST['selected_category'])) {
            $selectedCategory = $_POST['selected_category'];

            include 'config.php';

            // Query to fetch product data based on the selected category
            $sql = "SELECT product_id, `name`, `description`, `image`, `price` FROM product WHERE category = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $selectedCategory); // Bind the selected category to the prepared statement
            $stmt->execute();
            $result = $stmt->get_result();

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
                                <h4>' . htmlspecialchars($name) . '</h4>
                                <span class="price">$' . number_format($price, 2) . '</span>
                                <button type="button" onclick="location.href=\'carddetail.php?productid=' . $id . '\'">View Details</button>
                            </a>
                        </div>
                    </div>';
                }
            } else {
                echo "<p>No products found in this category.</p>";
            }

            $stmt->close();
            $conn->close();
        } else {
            
        }
        ?>
    </div>
</body>
</html>
