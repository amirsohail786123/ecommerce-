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
    $role = $_POST['role']; // New field for role

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = 'uploads/userimage/';
        $imagePath = $uploadDir . $imageName;

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
        $stmt = $conn->prepare("INSERT INTO `usertable` (`id`, `fullname`, `email`, `password`, `image`, `role`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $user_id, $fullname, $email, $hashed_password, $imagePath, $role);

        if ($stmt->execute()) {
            // Successful signup
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role; // Store the role in the session
            header('Location: navbardesign.php');
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
    <title>SignUp Page</title>
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
        margin: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        height: calc(100vh - 60px); /* Full viewport height minus space for margins */
        padding: 20px 0; /* Add space above and below the form */
        box-sizing: border-box;
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
        text-align: center; /* Center align the heading */
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
        display: block; /* Ensure link is block-level for easier alignment */
        text-align: center; /* Center align the link */
        margin-top: 10px; /* Space above the link */
    }

    .left a:hover {
        color: #dce6e8; /* Light color on hover for better visibility */
        text-decoration: underline; /* Underline on hover for better UX */
    }

    .link {
        text-align: center;
        margin-top: 20px;
        font-size: 16px;
    }

    .link button {
        width: 100%;
        background-color: transparent; /* Background color for the button */
        color: white;
  
        border: 1px solid white;
        border-radius: 20px;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.3s;
        margin-bottom: 10px; /* Space below the button */
    }
    h3{
        justify-content: center;
        display: flex;
        align-items: center;
        text-align: center;
        font-size: 20px;
        font-weight: 600px;
    }

    .link button:hover {
        background-color: #dce6e8;
        color:black ; /* Darker background on hover */
    }
    .link span a{
        width: 90%;
        /* Background color for the button */
        color: white;
 
        border: 1px solid white;
        border-radius: 20px;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.3s;
        margin-bottom: 10px; /* Space below the button */
    }
    .link span a:hover{
        background-color: #dce6e8;
        color: black;

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

        .link span a {
            font-size: 14px;
        
            width: 85%;
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
            <h2>SignUp</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" placeholder="Full Name" name="fullname" id="fullname" required>
                <select name="gender" id="gender" required>
                    <option value="" disabled selected>Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
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
                               <span>Have an Account?<a href="login.php">Login</a></span>
            </div>
            <h3> If You are a Seller SignUp Here .</h3>
            <div class="link">
                <button onclick="redirectToSellerSignup()">Sign Up as Seller</button>
                     </div>
        </div>
        
    </div>
   
    <script>
           
        function redirectToSellerSignup() {
            window.location.href = 'http://localhost/project%20forntened/seller%20dashboard/sellersignup.php';
        }
    </script>
</body>
</html>
