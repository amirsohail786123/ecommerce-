<?php
include('config.php'); // Include the database connection

// Check if the product ID is set in the GET request
if (isset($_GET['productid'])) {
    $productid = $_GET['productid'];

    // Fetch related products
    $relatedSql = "SELECT * FROM product WHERE product_id != ? AND category = (SELECT category FROM product WHERE product_id = ?) ";
    $relatedStmt = $conn->prepare($relatedSql);
    $relatedStmt->bind_param("ss", $productid, $productid);
    $relatedStmt->execute();
    $relatedResult = $relatedStmt->get_result();
   
    // Check if there are related products
    if ($relatedResult->num_rows > 0) {
        echo "<h2 class='r2' style='text-align:center;'>Related Products</h2>";
        echo "<div class='related-products'>"; // Start a row for related products

        while ($relatedProduct = $relatedResult->fetch_assoc()) {
            $relatedName = htmlspecialchars($relatedProduct['name'] ?? 'No Name');
            $price = htmlspecialchars($relatedProduct['Price'] ?? 'No price');
            $relatedDescription = htmlspecialchars($relatedProduct['description'] ?? '');
            $relatedImages = htmlspecialchars($relatedProduct['image'] ?? 'default.jpg');
            $relatedProductId = htmlspecialchars($relatedProduct['product_id'] ?? '0');

            // Handle multiple images
            $relatedImagePaths = explode(',', $relatedImages);

            // Create HTML for the related product
            echo '<div class="cardsmain">
                    <div class="cards">
                        <a href="carddetail.php?productid=' . $relatedProductId . '">
                            <img src="' . $relatedImagePaths[0] . '" alt="Product Image">
                            <h4>' . $relatedName . '</h4>
                            <span>' . $price . '</span>
                            <span>' . $relatedDescription . '</span>
                            <button type="button" onclick="location.href=\'carddetail.php?productid=' . $relatedProductId . '\'">View Details</button>
                        </a>
                    </div>
                </div>';
        }

        echo "</div>"; // End the row for related products
    }

    // Close the statement and connection
    $relatedStmt->close();
    $conn->close();
} else {
    echo "No product ID specified.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Related Products</title>
    
    <style>
        /* General Styles */
        .r2 {
            font-size: 3em; /* Size of the text */
            font-weight: bold; /* Make text bold */
            color: black; /* Vibrant color */
            line-height: 1.2; /* Height of the line */
            margin: 20px 0; /* Margin above and below the <h1> */
            font-family: 'Montserrat', sans-serif; /* Modern, clean font family */
        }

        /* Main Card Container */
        .related-products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            gap: 10px;
           
            padding: 20px;
        }

     
        /* General Styles */
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
            margin: 10px;
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
            .cards{
                width: 180px;
                height: 280px;
               
            
            }
            .cards img{
                width: 100%;
                height: 150px;
            }
          
        }
      
        @media screen and (max-width: 870px) {
            .cards{
                width: 180px;
                height: 280px;
               
            
            }
            .cards img{
                width: 100%;
                height: 150px;
            }
          
        }
        @media screen and (max-width: 480px) {

            .cards{
                width: 130px;
                height: 190px;
                border: 2px solid #f0f0f0;
               
               
              
              
                
            }
            .cards img{
                width: 100%;
                height: 100px;
              

            }
            .cards button{
               font-size: 10px; 
              padding: 3px;
            
            }
          .cards h4{
            font-size: 10px;
            font-style: bold;
          }
         
          .cards span{
            font-size: 10px;

          }
        }
        @media screen and (max-width: 360px) {

            .cards{
                width: 120px;
                height: 190px;
                border: 2px solid #f0f0f0;
                
            }
            .cards img{
                width: 100%;
                height: 100px;
              

    
        }}
        </style>
</head>
<body>
    <div class="related-products">
        <!-- Related products will be displayed here by PHP code -->
    </div>
</body>
</html>
