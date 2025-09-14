<?php
$servername = "localhost";    // Database server (use localhost if running locally)
$username = "root";           // Your database username
$password = "";               // Your database password
$dbname = "jppkk_test";       // Your database name            // Custom port for MySQL connectionn

// Create connection using custom port
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
