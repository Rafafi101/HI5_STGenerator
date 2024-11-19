<?php
// Database configuration
$dbHost     = '127.0.0.1:3307';
$dbUsername = 'root';
$dbPassword = '';
$dbName     = 'apc_capstone';

// Create database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
