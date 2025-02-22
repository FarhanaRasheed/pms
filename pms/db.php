<?php
// Database configuration
$host = 'localhost';     // Database host
$username = 'root';      // Database username
$password = '';          // Database password
$database = 'pms'; // Database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";


?>
