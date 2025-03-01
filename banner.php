<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Slideshow</title>
    <style>
        * {box-sizing: border-box;}
        body {font-family: Verdana, sans-serif;}

        /* Slideshow container */
        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;
            height: 500px;
        }

        /* Hide the images by default */
        .mySlides {
            display: none;
        }

        /* Style the text */
        .text {
            color: #f2f2f2;
            font-size: 15px;
            padding: 8px 12px;
            position: absolute;
            bottom: 8px;
            width: 100%;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Style the number text */
        .numbertext {
            color: #f2f2f2;
            font-size: 12px;
            padding: 8px 12px;
            position: absolute;
            top: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Dots */
        .dot {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition:  2.3s ease;
        }

        .active {
            background-color: #717171;
        }

        /* Fading animation */
        .fade {
            animation-name: fade;
            animation-duration: 2.5s;
        }

        @keyframes fade {
            from {opacity: 0.4} 
            to {opacity: 1}
        }

        /* On smaller screens, decrease text size */
        @media only screen and (max-width: 300px) {
            .text {font-size: 11px}
        }
    </style>
</head>
<body>
    <div class="slideshow-container">

        <div class="mySlides fade">
            <div class="numbertext">1 / 5</div>
            <img src="img2/img15.jpg" style="width:100%">
            <div class="text">70% off on Every Product</div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">2 / 5</div>
            <img src="img2/img6.jpg" style="width:100%">
            <div class="text">New Arrivals Just for You</div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">3 / 5</div>
            <img src="img2/img4.jpg" style="width:100%">
            <div class="text">Exclusive Offers Ending Soon</div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">4 / 5</div>
            <img src="img2/img6.jpg" style="width:100%">
            <div class="text">Limited Time Offer!</div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">5 / 5</div>
            <img src="img2/img15.jpg" style="width:100%">
            <div class="text">Don't Miss Out!</div>
        </div>

        <!-- Dots for navigation -->
        <div style="text-align:center;">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>

    </div>

    <script>
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");

            // Hide all slides
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }

            // Remove active class from all dots
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }

            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}    

            slides[slideIndex-1].style.display = "block";  

            // Add active class to the current dot
            dots[slideIndex-1].className += " active";

            // Change image every 8 seconds
            setTimeout(showSlides, 2000); 
        }
    </script>
</body>
</html>
