<?php
session_start();
require '../db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password_hash'])) {
  $_SESSION['login_error'] = "Invalid email or password.";
  header("Location: ../index.php");
  exit;
}

// Logged in successfully
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = strtoupper($user['name']);
$_SESSION['role'] = $user['role']; // Optional: for access control

header("Location: ../admin.php");
exit;
