
<?php
session_start(); // Start the session

// Set session variables
$_SESSION['username'] = 'john_doe';
$_SESSION['email'] = 'john@example.com';

echo $_SESSION['username'];
?>
