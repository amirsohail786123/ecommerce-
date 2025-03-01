<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Gallery</title>
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
    <h1 style="text-align:center; margin:60px;">Product Listing</h1>
    <div class="maincard" id="product-list">
        <!-- Products will be loaded here -->
    </div>
    <div class="loader" id="loader">Loading... Please Wait.</div>

    <script>
        let page = 1; // Current page number
        const loader = document.getElementById('loader');

        function loadProducts() {
            loader.style.display = 'block'; // Show loader
            fetch('load_more.php?page=' + page)
                .then(response => response.text())
                .then(data => {
                    if (data.trim() !== '') {
                        document.getElementById('product-list').innerHTML += data;
                        page++;
                    } else {
                        loader.style.display = 'none'; // No more products
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => loader.style.display = 'none'); // Hide loader
        }

        window.addEventListener('scroll', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
                loadProducts();
            }
        });

        // Initial load
        loadProducts();
    </script>
</body>
</html>
