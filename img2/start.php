<?php
session_start(); // Start the session

// Access session variables
echo 'Username: ' . $_SESSION['username'];
echo 'Email: ' . $_SESSION['email'];
?>