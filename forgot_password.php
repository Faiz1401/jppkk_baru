<?php
session_start();
include 'db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Cari user berdasarkan email
    $stmt = $conn->prepare("SELECT ID, NO_IC, EMAIL FROM tbluser WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $userId = $user['ID'];

        // 🔥 Padam token lama user ini dulu (elak banyak token aktif)
        $stmtDel = $conn->prepare("DELETE FROM tblpass WHERE IDUSER = ?");
        $stmtDel->bind_param("i", $userId);
        $stmtDel->execute();

        // Generate token baru
        $token = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Simpan token baru
        $stmt2 = $conn->prepare("INSERT INTO tblpass (IDUSER, RESET_TOKEN, RESET_EXPIRE) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $userId, $token, $expire);
        $stmt2->execute();

        // Hantar email guna PHPMailer
        $resetLink = "http://localhost/jppkk_baru/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'faizrazak1401@gmail.com'; // Ganti dengan email sendiri
            $mail->Password = 'rtmjoiaavkfapext';   // App password dari Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('faizrazak1401@gmail.com', 'Support System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password Anda';
            $mail->Body    = "
                <h3>Reset Password</h3>
                <p>Klik link berikut untuk reset password anda (sah 1 jam sahaja):</p>
                <a href='$resetLink'>$resetLink</a>
            ";

            $mail->send();
            $_SESSION['success'] = "Link reset password telah dihantar ke email anda.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal menghantar email. Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "Email tidak wujud dalam sistem.";
    }

    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f8f9fa; }
        .card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px; }
        input, button { width: 100%; padding: 0.6rem; margin-top: 0.5rem; border-radius: 6px; border: 1px solid #ccc; }
        button { background: #007bff; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #0069d9; }
    </style>
</head>
<body>

<div class="card">
    <h2 class="text-center">Lupa Password</h2>
    <form method="post" action="">
        <label>Email:</label>
        <input type="email" name="email" required placeholder="Masukkan email anda">
        <button type="submit">Hantar Link Reset</button>
    </form>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berjaya!',
            text: '<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>',
            confirmButtonColor: '#3085d6'
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
