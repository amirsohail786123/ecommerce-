<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Example</title>
   
</head>
<body>
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h2>About Us</h2>
                <p>Support local businesses and enjoy the convenience of having your favorite products delivered straight to your home. Explore our offerings today and experience the exceptional service and quality that GQshops is proud to deliver to Gojra. Happy shopping!

</p>
            </div>
            <div class="footer-section">
                <h2>Quick Links</h2>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Shop</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h2>Contact Us</h2>
                <p>Email: <a href="GQshops@gmail.com">GQshops@gmail.com</a></p>
                <p>Phone: +92 307 10 15 493</p>
                <p>Address: NEW plot Base Line Street No 9 Gojra</p>
            </div>
            <div class="footer-section social-media">
                <h2>Follow Us</h2>
                <a href="#" class="social-icon">Facebook</a>
                <a href="#" class="social-icon">Twitter</a>
                <a href="#" class="social-icon">Instagram</a>
            </div>
        </div>
        
    </footer>

    <style>
   body {
  
    font-family: Arial, sans-serif;
}
h2:hover{
    transition: 0.5s ease;
    color: black;
}

footer {
    font-size: 20px;
    background-color: #2874f0; /* Updated background color */
    color: #fff;
 
    text-align: center;
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    margin-bottom: 20px;
}

.footer-section {
    flex: 1;
    margin: 10px;
}

.footer-section h2 {

    padding-bottom: 10px;
}

.footer-section p,
.footer-section a {
    color: #ddd;
   
    text-decoration: none;
}

.footer-section a:hover {
    transition: 0.5s ease;
    text-decoration: underline;
    color: #000; /* Updated hover color for links */
}

.social-media {
    text-align: center;
}

.social-icon {
    margin: 0 10px;
    color: #ddd;
    text-decoration: none;
    font-weight: bold; /* Added bold styling for social icons */
}

.social-icon:hover {
    transition: 0.5s ease;
    color: #000; /* Updated hover color for social icons */
}

.footer-bottom {
    background-color: #1a60d0; /* Slightly darker shade for footer bottom */
    padding: 10px;
    text-align: center;
    font-size: 0.9em;
}
    </style>
</body>
</html>
