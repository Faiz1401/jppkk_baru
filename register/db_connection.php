<?php
$host = "127.0.0.1"; // guna IP lebih stabil dari localhost
$user = "root";
$pass = "";          // password kalau ada letak
$db   = "jppkk_test";       // confirmkan betul2 port kat my.ini/phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
