<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
include 'db_connection.php';

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token       = $_POST['token'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    // 1) Dapatkan token
    $stmt = $conn->prepare("SELECT IDPASS, IDUSER, RESET_EXPIRE FROM tblpass WHERE RESET_TOKEN = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
        $_SESSION['error'] = "Token tidak sah. Sila mohon reset password semula.";
        header("Location: forgot_password.php");
        exit();
    }

    $row = $res->fetch_assoc();

    // 2) Semak expiry di PHP
    if (strtotime($row['RESET_EXPIRE']) <= time()) {
        // Padam token luput
        $del = $conn->prepare("DELETE FROM tblpass WHERE IDPASS = ?");
        $del->bind_param("i", $row['IDPASS']);
        $del->execute();

        $_SESSION['error'] = "Token telah luput. Sila mohon reset password semula.";
        header("Location: forgot_password.php");
        exit();
    }

    // 3) Validasi asas password
    if (strlen($newPassword) < 8) {
        $_SESSION['error'] = "Password mesti sekurang-kurangnya 8 aksara.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    // 4) Update password user
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $up = $conn->prepare("UPDATE tbluser SET PASSWORD = ?, TEMP_PASS = NULL WHERE ID = ?");
    $up->bind_param("si", $hash, $row['IDUSER']);
    $up->execute();

    // 5) Padam token (sekali guna)
    $del = $conn->prepare("DELETE FROM tblpass WHERE IDPASS = ?");
    $del->bind_param("i", $row['IDPASS']);
    $del->execute();

    $_SESSION['success'] = "Password anda telah berjaya dikemaskini. Sila login semula.";
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<?php
// Jika user datang dengan GET ?token=...
if ($token === '') {
    // Tiada token dalam URL -> arahkan minta semula
    $_SESSION['error'] = "Pautan reset tidak lengkap. Sila mohon reset password semula.";
    header("Location: forgot_password.php");
    exit();
}
?>

<div class="card shadow-lg p-4" style="max-width:420px;width:100%;">
    <h3 class="text-center text-primary mb-3">🔒 Reset Password</h3>
    <form method="post" action="">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" class="form-control" name="password" required placeholder="Min 8 aksara">
        </div>
        <button type="submit" class="btn btn-primary w-100">Kemaskini Password</button>
    </form>
</div>

<?php if (isset($_SESSION['error'])): ?>
<script>
Swal.fire({icon:'error',title:'Oops...',text:'<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',confirmButtonColor:'#d33'});
</script>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({icon:'success',title:'Berjaya!',text:'<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>',confirmButtonColor:'#3085d6'});
</script>
<?php endif; ?>

</body>
</html>
