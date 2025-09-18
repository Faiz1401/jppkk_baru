<?php
session_start();
include 'db_connection.php';

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token        = $_POST['token'] ?? '';
    $newPass      = $_POST['password'] ?? '';
    $confirmPass  = $_POST['confirm_password'] ?? '';

    // Semak password sama tak
    if ($newPass !== $confirmPass) {
        $_SESSION['error'] = "Kata laluan dan pengesahan kata laluan tidak sama.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM tblpass WHERE RESET_TOKEN = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
        $_SESSION['error'] = "Token tidak sah. Mohon reset semula.";
        header("Location: forgot_password.php");
        exit();
    }

    $row = $res->fetch_assoc();

    // Semak expire
    if (strtotime($row['RESET_EXPIRE']) <= time()) {
        $conn->query("DELETE FROM tblpass WHERE IDUSER = " . $row['IDUSER']);
        $_SESSION['error'] = "Token telah luput. Mohon reset semula.";
        header("Location: forgot_password.php");
        exit();
    }

    // Validasi panjang password
    if (strlen($newPass) < 8) {
        $_SESSION['error'] = "Kata laluan mesti sekurang-kurangnya 8 aksara.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    // Update password
    $hash = password_hash($newPass, PASSWORD_DEFAULT);
    $stmt2 = $conn->prepare("UPDATE tbluser SET PASSWORD = ?, TEMP_PASS = NULL WHERE ID = ?");
    $stmt2->bind_param("si", $hash, $row['IDUSER']);
    $stmt2->execute();

    // Padam token
    $conn->query("DELETE FROM tblpass WHERE IDUSER = " . $row['IDUSER']);

    // SweetAlert Success (tak redirect)
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Berjaya!',
            html: '<b>Kata laluan anda telah dikemaskini.</b><br>Sila login semula.',
            confirmButtonText: 'Kembali ke Login',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location = \"index.php\";
        });
    });
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Reset Kata Laluan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<?php
if ($token === '') {
    $_SESSION['error'] = "Pautan reset tidak lengkap. Sila mohon reset kata laluan semula.";
    header("Location: forgot_password.php");
    exit();
}
?>

<div class="card shadow-lg p-4 border-0 rounded-4" style="max-width:420px;width:100%;">
    <h3 class="text-center text-primary mb-3">🔒 Reset Kata Laluan</h3>
    <form method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="mb-3">
            <label class="form-label">Kata Laluan Baru</label>
            <input type="password" class="form-control" name="password" required placeholder="Min 8 aksara">
        </div>
        <div class="mb-3">
            <label class="form-label">Sahkan Kata Laluan</label>
            <input type="password" class="form-control" name="confirm_password" required placeholder="Masukkan semula kata laluan">
        </div>
        <button type="submit" class="btn btn-primary w-100">Kemaskini Kata Laluan</button>
    </form>
</div>

<?php if (isset($_SESSION['error'])): ?>
<script>
Swal.fire({
    icon:'error',
    title:'Oops...',
    text:'<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
    confirmButtonColor:'#d33'
});
</script>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    icon:'success',
    title:'Berjaya!',
    text:'<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>',
    confirmButtonColor:'#3085d6'
});
</script>
<?php endif; ?>

</body>
</html>
