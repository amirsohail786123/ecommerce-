<?php
session_start();
include('navbardesign.php');
?>
<?php
// Start the session


// Include configuration and other files
include('config.php'); // Ensure $conn is initialized here


// Check if the user is logged in
if (!isset($_SESSION['email'])) {
 
    exit();
}

// Retrieve the user's email from the session
$email = $_SESSION['email'];

// Check if the connection is valid
if ($conn instanceof mysqli) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM usertable WHERE email = ?");
    
    if ($stmt) {
        // Bind parameters and execute the query
        $stmt->bind_param('s', $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                // User not found
                echo "<script>
                alert('User not found');
                window.location.href = 'login.php'; // Redirect to login if user not found
                </script>";
                exit();
            }

            // Fetch the user data
            $user = $result->fetch_assoc();

        } else {
            // Query execution failed
            echo "Error executing query: " . htmlspecialchars($stmt->error);
            exit();
        }

        // Close the statement
        $stmt->close();

    } else {
        // Statement preparation failed
        echo "Error preparing statement: " . htmlspecialchars($conn->error);
        exit();
    }

    // Close the connection
    $conn->close();

} else {
    echo "Database connection is not valid.";
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    

    <main>
        <section class="profile">
            <div class="profile-header">
                <h1>Profile</h1>
                <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Picture" class="profile-pic">
                <h2 class="username"><?php echo htmlspecialchars($user['fullname']); ?></h2>
                
            </div>
            <div class="profile-info">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                 </div>
            <div class="social-links">
                <a href="" target="_blank">LinkedIn</a>
                <a href="" target="_blank">GitHub</a>
                <a href="" target="_blank">Twitter</a>
            </div>
        </section>
    </main>

</body>
</html>

    <style>
 

        header {
            background: linear-gradient(135deg, #4a90e2, #50e3c2);
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        main {
            max-width: 900px;
            margin: 20px auto;
            padding: 30px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile {
            text-align: center;
        }

        .profile-header {
            margin-bottom: 20px;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e0e0e0;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.05);
            border-color: #4a90e2;
        }

        .username {
            font-size: 28px;
            font-weight: 600;
            margin: 10px 0 5px;
        }

        .profile-info p {
            font-size: 18px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .social-links {
            margin-top: 30px;
        }

        .social-links a {
            text-decoration: none;
            color: #4a90e2;
            margin: 0 15px;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: #007bff;
            text-decoration: underline;
        }

     
    </style>
</body>
</html>
<?php
include 'foooter.php';

?>