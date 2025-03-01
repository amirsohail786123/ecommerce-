<?php
session_start();
session_unset();
session_destroy();
 // Redirect to home or login page after logout
exit();
?>
