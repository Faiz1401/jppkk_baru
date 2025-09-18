<?php
session_start();
include 'db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // ✅ Semak kalau email kosong
    if (empty($email)) {
        $_SESSION['error'] = "Sila masukkan email anda.";
        header("Location: forgot_password.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT ID FROM tbluser WHERE EMAIL = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user   = $res->fetch_assoc();
        $userId = $user['ID'];

        // Padam token lama
        $conn->query("DELETE FROM tblpass WHERE IDUSER = $userId");

        // Token baru
        $token  = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Simpan token
        $stmt2 = $conn->prepare("INSERT INTO tblpass (IDUSER, RESET_TOKEN, RESET_EXPIRE) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $userId, $token, $expire);
        $stmt2->execute();

        // Email link reset
        $resetLink = "http://localhost/jppkk_baru/reset_password.php?token=" . urlencode($token);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'faizrazak1401@gmail.com'; 
            $mail->Password   = 'rtmjoiaavkfapext';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('faizrazak1401@gmail.com', 'Support System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
            $mail->Body    = "<p>Klik link ini untuk reset password anda (sah 1 jam):</p>
                              <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $_SESSION['success'] = "Link reset password telah dihantar ke email anda.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal menghantar email: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "Email tidak wujud dalam sistem.";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-lg p-4 border-0 rounded-4" style="max-width:400px; width:100%;">
    <h3 class="text-center text-primary mb-4">🔑 Lupa Password</h3>
    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">Masukkan Email Anda</label>
            <input type="email" name="email" class="form-control" required placeholder="contoh: user@email.com">
        </div>
        <button type="submit" class="btn btn-primary w-100">📩 Hantar Link Reset</button>
        <p style="margin-top:20px; font-size:1rem;">
            Kembali ke 
            <a href="index.php" style="color: #5157f4ff; font-weight:bold; text-decoration: underline;">
                Log Masuk
            </a>
        </p>
    </form>
</div>

<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berjaya!',
    text: '<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>',
    confirmButtonColor: '#3085d6'
}).then(() => {
    window.location = "index.php"; // ✅ Redirect ke login selepas klik OK
});
</script>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
    confirmButtonColor: '#d33'
});
</script>
<?php endif; ?>

</body>
</html>
