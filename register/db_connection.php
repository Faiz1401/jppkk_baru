<?php
// Define the database connection parameters
$host = 'localhost';  // Replace with your MySQL server host
$port = '4306';  // Specify the port number you are using
$dbname = 'jppkk_test';  // Replace with your actual database name
$username = 'root';  // Replace with your database username
$password = '';  // Replace with your database password

try {
    // Create the PDO connection with the defined parameters
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // If the connection is successful, this will run
} catch (PDOException $e) {
    // If the connection fails, this will display the error
    echo "Connection failed: " . $e->getMessage();
}
?>
