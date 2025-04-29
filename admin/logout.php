<?php
session_start(); // Start the session

// Destroy the session
session_destroy();

// Redirect to login page (change 'login.php' to your actual login page)
header("Location: http://localhost/cctv/index.php");
exit();
?>