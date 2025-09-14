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

    // Compare password
    if (password_verify($password, $user['PASSWORD']) || $password === $user['TEMP_PASS']) {
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['name'] = $user['NAMA'];

        // Redirect to change password if temp_pass
        if ($password === $user['TEMP_PASS']) {
            header("Location: change_password.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $_SESSION['login_error'] = "IC Number atau Password salah.";
    }
} else {
    $_SESSION['login_error'] = "IC Number tidak wujud.";
}
header("Location: index.php");
exit();
?>
