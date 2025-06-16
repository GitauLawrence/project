<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sellam_real_estate';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>