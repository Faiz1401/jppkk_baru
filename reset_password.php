<?php
session_start();
include 'db_connection.php';

$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];

    // Semak token dalam tblpass
    $stmt = $conn->prepare("SELECT * FROM tblpass WHERE RESET_TOKEN = ? AND RESET_EXPIRE > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $userId = $row['IDUSER'];

        // Update password user
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE tbluser SET PASSWORD = ?, TEMP_PASS = NULL WHERE ID = ?");
        $stmt2->bind_param("si", $hashedPassword, $userId);
        $stmt2->execute();

        // Padam token (supaya sekali guna)
        $stmt3 = $conn->prepare("DELETE FROM tblpass WHERE IDPASS = ?");
        $stmt3->bind_param("i", $row['IDPASS']);
        $stmt3->execute();

        $_SESSION['success'] = "Password anda telah berjaya dikemaskini. Sila login semula.";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Token tidak sah atau telah luput.";
        header("Location: reset_password.php?token=$token");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <?php if (isset($_SESSION['error'])): ?>
        <div style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <h2>Reset Password</h2>
    <form method="post" action="">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label>Password Baru:</label>
        <input type="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
