<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Prepare the SQL query to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM usertable WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['email'] = $email;
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid email or password';
        }
    } else {
        $_SESSION['error'] = 'Invalid email or password';
    }
    
    $stmt->close();
}

// Display error if it exists
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
} else {
    $error_message = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            background-color: #7428f0;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 0 20px;
        }

        .left {
            backdrop-filter: blur(5px);
            box-shadow: 0px 0px 10.2px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 2% 4%;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .left:hover {
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
            border-color: rgba(255, 255, 255, 0.8);
        }

        .left h2 {
            color: white;
            font-size: 30px;
            margin: 0 0 20px 0;
            text-align: center;
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
            border-radius: 5px;
            transition: border-color 0.3s ease;
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
            transition: background-color 0.3s ease, color 0.3s ease;
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

        .forgot {
            color: white;
            text-decoration: underline;
        }

        .forgot:hover {
            color: #dce6e8;
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

        .signup {
            font-size: 16px;
            text-align: center;
        }

        .signup a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .signup a:hover {
            color: #dce6e8;
        }

        ::placeholder {
            color: white;
            opacity: 0.8;
        }

        .link {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .link button {
            background-color: transparent;
            color: white;
            border: 1px solid white;
            border-radius: 20px;
            height: 40px;
            width: 100%;
            max-width: 200px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .link button:hover {
            background-color: white;
            color: black;
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

            .signup {
                font-size: 14px;
            }

            .link button {
                font-size: 14px;
                height: 35px;
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h2>Login</h2>
            <?php
            if (isset($error_message) && $error_message) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
            <form action="login.php" method="post">
                <input type="email" placeholder="E-mail" name="email" id="email" required>
                <input type="password" placeholder="Password" name="password" id="pass" required>
                <a class="forgot" href="forgot.php">Forgot Password?</a>
                <input type="submit" value="Login">
            </form>
            <div class="hr">
                <hr>
                <p>OR</p>
                <hr>
            </div>
            <p class="signup">Not Have an Account? <a href="signup.php">SignUp</a></p>
            <h3>If You are a Seller Login Here.</h3>
            <div class="link">
                <button onclick="redirectToSellerlogin()">Login as a Seller</button>
            </div>
        </div>
    </div>
    <script>
        function redirectToSellerlogin() {
            window.location.href = 'http://localhost/project%20forntened/seller%20dashboard/sellerlogin.php';
        }
    </script>
</body>
</html>
