<?php
// Start the session
session_start();

// Include configuration and other files
include('config.php');
include('uniqid.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $shop_address = $_POST['shop_address'];
    $shop_name = $_POST['shop_name'];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = 'uploads/seller_upload/'; // Updated directory name
        $imagePath = $uploadDir . $imageName;

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        // Move the uploaded file to the uploads directory
        if (!move_uploaded_file($imageTmpName, $imagePath)) {
            echo "<script>
            alert('Image upload failed');
            window.location.href = 'signup.php'; // Redirect back to signup page
            </script>";
            exit();
        }
    } else {
        // Set a default image or handle missing image
        $imagePath = 'uploads/default.png'; // Ensure you have a default image in the uploads directory
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM `usertable` WHERE `email` = ?");
    $stmt->bind_param('s', $email); // 's' denotes the type of the parameter (string)
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists
        echo "<script>
        alert('Email already exists');
        window.location.href = 'signup.php'; // Redirect back to signup page
        </script>";
    } else {
        // Email does not exist, proceed to insert new user
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate a unique user ID
        $user_id = generateUniqueNumber();

        // Prepare a statement to insert a new user
        $stmt = $conn->prepare("INSERT INTO `seller` (`seller_id`, `fullname`, `email`, `password`, `phone_number`, `shop_adress`, `shop_name`, `image`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssisss', $user_id, $fullname, $email, $hashed_password, $phone_number, $shop_address, $shop_name, $imagePath);

        if ($stmt->execute()) {
            // Successful signup
            $_SESSION['seller_email'] = $email; // Set session variable for seller's email
            header('Location: seller_dashboard.php');
            exit(); // Ensure no further code is executed after redirect
        } else {
            // Error during signup
            echo "<script>
            alert('Signup Error');
            window.location.href = 'signup.php'; // Redirect back to signup page
            </script>";
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Signup Page</title>
    <style>
        body {
            background-color: #7428f0;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .containerr {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 60px); /* Full viewport height minus space for margins */
            padding: 20px 0; /* Add space above and below the form */
        }

        .left {
            backdrop-filter: blur(5px);
            box-shadow: 0px 0px 10.2px black;
            border: 2px solid white;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 2% 4%;
            width: 100%;
            max-width: 500px; /* Adjust the width */
            height: auto; /* Adjust based on content */
            max-height: 600px; /* Optional: restrict height if needed */
            box-sizing: border-box;
            overflow-y: auto; /* Scroll if content exceeds max height */
        }

        .left h2 {
            color: white;
            font-size: 30px;
            margin: 0 0 20px 0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .left input[type=text],
        .left input[type=email],
        .left input[type=password],
        .left select {
            outline: none;
            border: none;
            border-bottom: 1px solid white;
            padding: 10px;
            color: white;
            background: transparent;
            font-size: 16px;
        }

        .left input[type=text]:focus,
        .left input[type=email]:focus,
        .left input[type=password]:focus,
        .left select:focus {
            border-bottom: 1px solid #dce6e8;
        }

        .left input[type=submit] {
            color: white;
            border-radius: 20px;
            background-color: transparent;
            border: 1px solid white;
            height: 40px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }

        .left input[type=submit]:hover {
            background-color: #dce6e8;
            color: black;
        }

        .left a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
        }

        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }

        .link a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        .link a:hover {
            color: #09083e;
        }

        .hr {
            display: flex;
            width: 100%;
            margin: 20px 0;
            align-items: center;
        }

        .hr hr {
            height: 1px;
            width: 100%;
            background: white;
            border: none;
        }

        ::placeholder {
            color: white;
        }

        @media (max-width: 600px) {
            .left {
                width: 90%;
                padding: 5%;
            }

            .left h2 {
                font-size: 24px;
            }

            .left input {
                font-size: 14px;
            }

            .left input[type=submit] {
                font-size: 14px;
                height: 35px;
            }

            .link {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="containerr">
        <div class="left">
            <h2>Seller Signup</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" placeholder="Full Name" name="fullname" id="fullname" required>
                <input type="text" placeholder="Phone Number" name="phone_number" id="phone_number" required>
                <input type="text" placeholder="Shop Address" name="shop_address" id="shop_address" required>
                <input type="text" placeholder="Shop Name" name="shop_name" id="shop_name" required>
                <input type="email" placeholder="E-mail" name="email" id="email" required>
                <input type="password" placeholder="Password" name="password" id="password" required>
                <input type="file" name="image" id="image" accept="image/*">
                <input type="submit" value="SignUp">
            </form>
            <div class="hr">
                <hr>
                <p>OR</p>
                <hr>
            </div>
            <div class="link">
             
                <span>Have an Account? <a href="http://localhost/project%20forntened/seller%20dashboard/sellerlogin.php">Login</a></span>
            </div>
        </div>
    </div>
</body>
</html>
