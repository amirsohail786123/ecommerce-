<?php
session_start();
include ('navbardesign.php');
// Include the database connection
include 'config.php';


// Fetch products uploaded within the last 24 hours
$query = "SELECT * FROM product WHERE `created-at` >= NOW() - INTERVAL 1 DAY";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Cards</title>
    <style>
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
    <div class="maincard">

    <?php
    // Check if any products were uploaded in the last 24 hours
    if (mysqli_num_rows($result) > 0) {
        // Display products uploaded in the last 24 hours
        while ($row = mysqli_fetch_assoc($result)) {
            $productid = $row['product_id']; // Store product ID
            echo '<div class="cardmain">';
            echo '<a href="carddetail.php?productid=' . $productid . '" class="cards">'; // Pass product ID in the URL
            
            // Handle multiple images separated by commas
            $images = explode(',', $row['image']); // Assuming 'image' column contains comma-separated image URLs
            echo '<img src="' . trim($images[0]) . '" alt="Product Image">';
            
            echo '<h4>' . $row['name'] . '</h4>';
            echo '<span>' . $row['description'] . '</span>';
            echo '<div class="price">$' . $row['price'] . '</div>';
            echo '<button>View Details</button>';
            echo '</a>';
            echo '</div>';
        }
    } else {
        // If no products were uploaded in the last 24 hours, display older products
        $query = "SELECT * FROM product ORDER BY `created-at` DESC";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $productid = $row['product_id']; // Store product ID
            echo '<div class="cardmain">';
            echo '<a href="carddetail.php?productid=' . $productid . '" class="cards">'; // Pass product ID in the URL
            
            // Handle multiple images separated by commas
            $images = explode(',', $row['image']); // Assuming 'image' column contains comma-separated image URLs
            echo '<img src="' . trim($images[0]) . '" alt="Product Image">';
            
            echo '<h4>' . $row['name'] . '</h4>';
            echo '<span>' . $row['description'] . '</span>';
            echo '<div class="price">$' . $row['price'] . '</div>';
            echo '<button>View Details</button>';
            echo '</a>';
            echo '</div>';
        }
    }
    ?>

    </div>
</body>
</html>
<?php
include ('foooter.php');
?>