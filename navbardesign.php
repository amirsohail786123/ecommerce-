<?php
include('config.php');
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ecommerce Navbar Design</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="boostrap/css/bootstrap.min.css">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="boostrap/fontawesome-free-6.6.0-web/css/all.min.css">
    

    <style>
        /* Custom Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-name {
            position: relative;
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            overflow: hidden;
        }

        .brand-name::before,
        .brand-name::after {
            content: '';
            position: absolute;
            height: 2px;
            width: 0;
            background: white;
            transition: width 0.3s ease;
        }

        .brand-name::before {
            top: 0;
            left: 0;
        }

        .brand-name::after {
            bottom: 0;
            right: 0;
        }

        .brand-name:hover::before,
        .brand-name:hover::after {
            width: 100%;
        }

        .main-navbar {
            border-bottom: 1px solid #ccc;
        }

        .main-navbar .top-navbar {
            background-color: #2874f0;
            padding: 10px 0;
        }

        .main-navbar .top-navbar .brand-name {
            color: #fff;
        }

        .main-navbar .top-navbar .nav-link {
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .main-navbar .top-navbar .nav-link:hover {
            color: black;
        }

        .main-navbar .top-navbar .dropdown-menu {
            padding: 0;
            border-radius: 0;
        }

        .main-navbar .top-navbar .dropdown-menu .dropdown-item {
            padding: 8px 16px;
            border-bottom: 1px solid #ccc;
            font-size: 14px;
        }

        .main-navbar .top-navbar .dropdown-menu .dropdown-item i {
            width: 20px;
            text-align: center;
            color: #2874f0;
            font-size: 14px;
        }

        .main-navbar .navbar {
            padding: 0;
            background-color: #ddd;
        }

        .btn {
            background-color: #ddd;
        }

        .btn:hover {
            background-color: #ddd;
            border: none;
            outline: none;
        }

        .main-navbar .navbar .nav-item .nav-link {
            padding: 8px 20px;
            color: #000;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .main-navbar .navbar .nav-item .nav-link:hover {
            color: white;
        }

        .search-bar {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0px;
            color: black;
        }

        .search-bar::placeholder {
            color: black;
        }

        .search-bar .form-control {
            flex: 1;
            border-radius: 5px 0 0 5px;
            padding: 10px;
        }

        .search-bar .form-select {
            border-radius: 0;
            border-left: 1px solid #ddd;
            padding: 10px;
        }

        .search-bar .btn {
            background-color: #ddd;
            border: none;
            color: black;
            padding: 10px 15px;
            border-radius: 0 5px 5px 0;
            transition: background-color 0.3s ease;
        }

        .search-bar .btn:hover {
            background-color: #ddd;
        }

        @media only screen and (max-width: 1080px) {
            body {
                width: fit-content;
            }

            #wishlist {
                display: none;
            }

            .main-navbar .top-navbar .nav-link {
                font-size: 15px;
                padding: 8px 10px;
            }
        }

        @media only screen and (max-width: 853px) {
            .search-bar {
                margin-right: 10px;
            }

            .brand-name {
                font-size: 1.8rem;
            }

            .input-group ::placeholder {
                font-size: 13px;
                color: black;
            }

            .form-select {
                font-size: 14px;
                color: black;
            }

            .main-navbar .top-navbar .nav-link {
                font-size: 12px;
                padding: 8px 10px;
            }

            #wishlist {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="main-navbar shadow-sm sticky-top">
        <div class="top-navbar">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2 my-auto d-none d-sm-none d-md-block d-lg-block">
                        <h5 class="brand-name">GQ SHOPS</h5>
                    </div>
                    <div class="col-md-5 my-auto">
                        <form action="navbardesign.php" method="POST" class="search-bar">
                            <div class="input-group">
                                <input type="search" name="search" placeholder="Search product..." class="form-control" />
                                <select name="price_filter" class="form-select">
                                    <option value="">Select Range</option>
                                    <option value="low_high">Price: Low to High</option>
                                    <option value="high_low">Price: High to Low</option>
                                </select>
                                <button class="btn" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5 my-auto">
                        <ul class="nav justify-content-end">
                            <?php if (isset($_SESSION['email'])): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="profile.php">
                                        <i class="fa fa-user"></i> Profile
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="login.php">
                                        <i class="fa fa-sign-in"></i> Login
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="signup.php">
                                        <i class="fa fa-user-plus"></i> Signup
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="cartdetail.php">
                                    <i class="fa fa-shopping-cart"></i> Cart 
                                </a>
                            </li>
                            <li class="nav-item" id="wishlist">
                                <a class="nav-link" href="#">
                                    <i class="fa fa-heart" id="whishlist"></i> Wishlist 
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-user"></i> Username
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="profile.php"><i class="fa fa-user"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="myorders.php"><i class="fa fa-list"></i> My Orders</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fa fa-heart"></i> My Wishlist</a></li>
                                    <li><a class="dropdown-item" href="cartdetail.php"><i class="fa fa-shopping-cart"></i> My Cart</a></li>
                                    <li><a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand d-block d-sm-block d-md-none d-lg-none" href="#">
                    GQ SHOPS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link" href="newarrivals.php">New Arrivals</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Featured Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="category.php?cat=electronics">Electronics</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="category.php?cat=clothing">Garments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="category.php?cat=shoes">shoes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="category.php?cat=beauty">Beauty</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="div_flex">
        <?php include 'search.php'; 
        // include 'productcard.php'; 
        ?>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="boostrap/js/bootstrap.min.js"></script>
    <script src="boostrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap dropdowns
            var dropdownElList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
</body>

</html>
