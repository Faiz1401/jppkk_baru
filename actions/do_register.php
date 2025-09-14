<?php
session_start();
require '../db.php';

// Get form inputs
$full_name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// Normalize & format inputs
$full_name = strtoupper(trim($full_name));
$email = strtolower(trim($email)); // lowercase emails for consistency

// Format phone: Remove non-digits
$phone = preg_replace('/\D/', '', $phone);
if (strpos($phone, '60') === 0) {
    $phone = '+' . $phone;
} elseif (strpos($phone, '0') === 0) {
    $phone = '+60' . substr($phone, 1);
} elseif (strpos($phone, '+60') !== 0) {
    $phone = '+60' . $phone;
}

// ✅ Password match check
if ($password !== $confirm) {
    $_SESSION['register_error'] = "Passwords do not match.";
    header("Location: ../register.php");
    exit;
}

// ✅ Email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = "Invalid email format.";
    header("Location: ../register.php");
    exit;
}

// ✅ Password strength check (example: min 8 chars)
if (strlen($password) < 8) {
    $_SESSION['register_error'] = "Password must be at least 8 characters.";
    header("Location: ../register.php");
    exit;
}

// ✅ Phone number validation (expecting +60XXXXXXXXX format)
if (!preg_match('/^\+60\d{8,10}$/', $phone)) {
    $_SESSION['register_error'] = "Invalid Malaysian phone number format.";
    header("Location: ../register.php");
    exit;
}

// Hash the password
$hash = password_hash($password, PASSWORD_DEFAULT);

// ✅ Check for duplicate email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $_SESSION['register_error'] = "Email already exists.";
    header("Location: ../register.php");
    exit;
}

// ✅ Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("ssss", $full_name, $email, $phone, $hash);
$stmt->execute();

// ✅ Set success message
$_SESSION['register_success'] = "Account created successfully. Please log in.";

// ✅ Redirect to login
header("Location: ../index.php");
exit;
