<?php
session_start();
include 'db_connection.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Query join tbluser + tbljawatan
$stmt = $conn->prepare("
    SELECT u.*, j.ROLE 
    FROM tbluser u
    LEFT JOIN tbljawatan j ON u.IDJAWATAN = j.IDJAWATAN
    WHERE u.NO_IC = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // 1. Login guna TEMP_PASS
    if (!empty($user['TEMP_PASS']) && $password === $user['TEMP_PASS']) {
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['NO_IC'];
        $_SESSION['role'] = $user['ROLE']; 
        $_SESSION['require_password_change'] = true;

        header("Location: change_password.php");
        exit();
    }

    // 2. Login guna PASSWORD biasa
    if (!empty($user['PASSWORD']) && password_verify($password, $user['PASSWORD'])) {
        if ($user['STATUS'] == 0) {
            $_SESSION['login_error'] = "Akaun anda sedang menunggu pengesahan admin.";
            header("Location: index.php");
            exit();
        }

        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['NO_IC'];
        $_SESSION['role'] = $user['ROLE']; 
        $_SESSION['require_password_change'] = false;

        // Redirect ikut role
        switch ($user['ROLE']) {
            case 'AD01': // User Biasa
                header("Location: admin/index.php");
                break;
            case 'ST01': // Pensyarah
                header("Location: dashboard_pensyarah.php");
                break;
            case 'ST02': // Admin
                header("Location: admin_dashboard.php");
                break;
            default: // fallback kalau role tak dikenali
                header("Location: dashboard.php");
        }
        exit();
    }

    // 3. Password salah
    $_SESSION['login_error'] = "Kata Laluan salah!";
    header("Location: index.php");
    exit();

} else {
    // User tak jumpa
    $_SESSION['login_error'] = "User tidak wujud dalam sistem!";
    header("Location: index.php");
    exit();
}
?>
