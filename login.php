<?php
session_start();
include 'db_connection.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT * FROM tbluser WHERE NO_IC = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Kalau user login pakai TEMP_PASS
    if (!empty($user['TEMP_PASS']) && $password === $user['TEMP_PASS']) {
        $_SESSION['user_id'] = $user['ID'];   // guna ID, bukan USER_ID
        $_SESSION['username'] = $user['NO_IC'];
        $_SESSION['require_password_change'] = true;
        header("Location: change_password.php");
        exit();
    }

    // Kalau user dah tukar password
    if (!empty($user['PASSWORD']) && password_verify($password, $user['PASSWORD'])) {
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['NO_IC'];
        $_SESSION['require_password_change'] = false;
        header("Location: dashboard.php");
        exit();
    } else {
        // Set error ke session untuk SweetAlert2 dalam index.php
        $_SESSION['login_error'] = "Password salah!";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['login_error'] = "User tak jumpa!";
    header("Location: index.php");
    exit();
}
