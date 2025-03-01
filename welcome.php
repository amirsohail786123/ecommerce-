<?php

session_start();
if(isset($_SESSION['email'])){
    header('location: navbar.php');
} 
else{
    header('location: ./login.php');
}

?>
