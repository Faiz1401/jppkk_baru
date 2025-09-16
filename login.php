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

    // 1. Kalau user login pakai TEMP_PASS
    if (!empty($user['TEMP_PASS']) && $password === $user['TEMP_PASS']) {
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['NO_IC'];
        $_SESSION['require_password_change'] = true;
        header("Location: change_password.php");
        exit();
    }

    // 2. Kalau user login pakai PASSWORD biasa
    if (!empty($user['PASSWORD']) && password_verify($password, $user['PASSWORD'])) {
        // Semak status
        if ($user['STATUS'] == 0) {
            $_SESSION['login_error'] = "Akaun anda sedang menunggu pengesahan admin.";
            header("Location: index.php");
            exit();
        }

        // Kalau dah disahkan
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['NO_IC'];
        $_SESSION['require_password_change'] = false;
        header("Location: dashboard.php");
        exit();
    }

    // 3. Password salah
    $_SESSION['login_error'] = "Password salah!";
    header("Location: index.php");
    exit();

} else {
    // User tak jumpa
    $_SESSION['login_error'] = "User tidak wujud dalam sistem!";
    header("Location: index.php");
    exit();
}
?>
