<?php
include('config.php'); // Include the database connection

// Check if the form is submitted
if(isset($_POST['search'])){
    $searchQuery = $_POST['search'] ?? '';
    $priceFilter = $_POST['price_filter'] ?? '';

    // Sanitize the search query
    $searchQuery = $conn->real_escape_string($searchQuery);

    // Base SQL query
    $sql = "SELECT product_id, `name`, `description`, `image`,price 
            FROM product 
            WHERE `name` LIKE '%$searchQuery%' OR `description` LIKE '%$searchQuery%'";

    // Append ORDER BY clause based on price filter
    if ($priceFilter == 'low_high') {
        $sql .= " ORDER BY `price` ASC";
    } elseif ($priceFilter == 'high_low') {
        $sql .= " ORDER BY `price` DESC";
    }

    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                $id = $row['product_id'];
                $price = $row['price'];
                $name = htmlspecialchars($row['name']);
                $description = htmlspecialchars($row['description']);
                $image = $row['image'];

                // Handle multiple images
                $images = explode(',', $image);
                $firstImage = $images[0] ?? 'default.jpg'; // Default image if none exists

                // Display product data in HTML
                echo '<div class="cardmain">
                    <div class="cards">
                        <a href="carddetail.php?productid=' . $id . '">
                            <img src="' . htmlspecialchars($firstImage) . '" alt="Product Image">
                            <h4>' . $name . '</h4>
                            <span>' . $price . 'pkr</span>
                            <span>' . $description . '</span>
                            <button type="button" onclick="location.href=\'carddetail.php?productid=' . $id . '\'">View Details</button>
                        </a>
                    </div>
                </div>';
            }
        } else {
            echo "No results found";
        }
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    
}

$conn->close();
?>
 <style>
    /* General Styles */
   .div_flex {
    display: flex;
    justify-content: center; /* Center items horizontally */
    align-items: center;     /* Center items vertically */
    flex-wrap: wrap;         /* Allow items to wrap to the next line if necessary */
    gap: 10px;               /* Optional: Add space between cards */
    padding: 10px;           /* Optional: Add padding around the flex container */
}
body {
    margin: 0;
    padding: 0;
   
 
    background-color: #f4f4f4;
    font-family: Arial, sans-serif;
}


/* Main Card Container */
.cardmain {
   margin: 10px;
     padding: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Card Styling */
.cards {
    width: 250px;
    height: 370px; /* Fixed height to ensure consistent card size */
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
    transform: scale(1.05); /* Slight zoom effect on hover */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5); /* Enhanced shadow on hover */
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
    overflow: hidden; /* Hide overflow if text is too long */
    text-overflow: ellipsis; /* Add ellipsis for overflow text */
    white-space: nowrap; /* Prevent line break */
}

/* Card Description */
.cards span {
    display: block;
    margin: 0 10px 10px;
    font-size: 14px;
    color: #666;
    text-align: left;
    flex-grow: 1;
    overflow: hidden; /* Hide overflow if text is too long */
    text-overflow: ellipsis; /* Add ellipsis for overflow text */
    white-space: nowrap; /* Prevent line break */
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



@media screen and (max-width: 480px) {
   .cards{
   
    width: 200px;
    height: fit-content;
   }
   .cards img{
    width: 100%;
    height: 150px;
   }
}
          </style>
