<?php
// Include database connection
include('db_connection.php');  // assuming your connection code is in db_connection.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];  // This will now represent the IC number
    $password = $_POST['password'];  // Password input from form

    // Sanitize user inputs to prevent SQL Injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Query the database to check if the IC number exists
    $sql = "SELECT * FROM tbluser WHERE NO_IC = '$username'";  // Check NO_IC instead of EMAIL
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // If a user is found, fetch the data
        $row = $result->fetch_assoc();
        
        // Verify password using password_verify() if you are storing hashed passwords
        if (password_verify($password, $row['PASSWORD'])) {
            // Successful login if the password matches
            session_start();
            $_SESSION['user_id'] = $row['ID'];
            $_SESSION['username'] = $row['NO_IC'];  // IC number as the session username
            header("Location: dashboard.php");  // Redirect to another page after successful login
            exit();
        } else {
            echo "Invalid IC number or password!";
        }
    } else {
        // If no user is found with the provided IC number
        echo "Invalid IC number or password!";
    }

    // Close the database connection
    $conn->close();
}
?>
