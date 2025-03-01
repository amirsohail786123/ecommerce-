<?php
// Start the session
session_start();

// Include configuration file
include('config.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a statement to select the user based on the email
    $stmt = $conn->prepare("SELECT `password` FROM `seller` WHERE `email` = ?");
    $stmt->bind_param('s', $email); // 's' denotes the type of the parameter (string)
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists
    if ($stmt->num_rows > 0) {
        // Bind the result to a variable
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session and redirect
            $_SESSION['seller_email'] = $email; // Set session variable for seller's email
            header('Location: seller_dashboard.php'); // Redirect to dashboard or other protected page
            exit(); // Ensure no further code is executed after redirect
        } else {
            // Incorrect password
            echo "<script>
            alert('Invalid password');
            window.location.href = 'sellerlogin.php'; // Redirect back to login page
            </script>";
        }
    } else {
        // Email does not exist
        echo "<script>
        alert('Email not found');
        window.location.href = 'sellerlogin.php'; // Redirect back to login page
        </script>";
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
    <title>Seller Login Page</title>
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
            height: 100vh;
            padding: 0 20px;
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
            max-width: 400px;
            box-sizing: border-box;
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

        .left input[type=email],
        .left input[type=password] {
            outline: none;
            border: none;
            border-bottom: 1px solid white;
            padding: 10px;
            color: white;
            background: transparent;
            font-size: 16px;
        }

        .left input[type=email]:focus,
        .left input[type=password]:focus {
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
            <h2>Seller Login</h2>
            <form action="sellerlogin.php" method="POST">
                <input type="email" placeholder="E-mail" name="email" id="email" required>
                <input type="password" placeholder="Password" name="password" id="password" required>
                <input type="submit" value="Login">
            </form>
            <div class="hr">
                <hr>
                <p>OR</p>
                <hr>
            </div>
            <div class="link">
                <span>Don't have an Account? <a href="sellersignup.php">Sign Up</a></span>
            </div>
        </div>
    </div>
</body>
</html>
