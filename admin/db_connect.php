<?php
// Database connection settings
$host = 'localhost'; // or your database host
$username = 'root'; // your database username
$password = ''; // your database password
$database = 'cctv_db'; // your database name

// Create a new mysqli connection
$conn = new mysqli($host, $username, $password, $database);  // Changed $mysqli to $conn for consistency

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Using $conn for error checking
}
?>