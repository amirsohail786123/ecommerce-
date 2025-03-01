<?php 
session_start();
include 'navbardesign.php'; 


// Include the config file for database connection
include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <style>
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

<?php

if(isset($_GET['cat'])){
$cat = $_GET['cat'];
// Query to fetch products where category is 'electronics'
$sqlstmt = "SELECT * FROM product WHERE category = '$cat' ";
$result = $conn->query($sqlstmt);

if ($result->num_rows > 0) {
    echo '<div class="maincard">'; // Main card container
    
    while($row = $result->fetch_assoc()) {
        $product_id = $row["product_id"];
        
        // Assuming the 'image' column contains comma-separated image URLs
        $images = explode(',', $row["image"]); // Split the comma-separated image URLs

        echo '<div class="cardmain">
        <a href="carddetail.php?productid='.$row['product_id'].'" style="text-decoration:none;">
                <div class="cards">';

        // Loop through each image URL and display it
        
            echo '<img src="' . trim($images[0]) . '" alt="Product Image">';  // Display each image
        

        echo '
                    <h4>' . $row["name"] . '</h4> <!-- Product name -->
                    <span>' . $row["description"] . '</span> <!-- Product description -->
                    <div class="price">Price: $' . $row["price"] . '</div> <!-- Product price -->
                    <button>Add to Cart</button> <!-- Add to Cart button -->
                </div>
            </div>
            
            </a>'
            ;
    }

    echo '</div>'; // End of main card container
} else {
    echo "No Products Avaiable.";
}
}
// Close the connection

$conn->close();
?>

</body>
</html>
