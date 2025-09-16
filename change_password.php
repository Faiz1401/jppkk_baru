<?php
session_start();
include 'db_connection.php';

// Pastikan user sampai sini sebab ada temp pass
if (!isset($_SESSION['require_password_change']) || $_SESSION['require_password_change'] !== true) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    if ($new_password === $confirm_password) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE tbluser SET PASSWORD = ?, TEMP_PASS = NULL WHERE ID = ?");
        $stmt->bind_param("si", $hashed, $user_id);

        if ($stmt->execute()) {
            // Jangan terus session_destroy(), tunggu selepas alert
            unset($_SESSION['require_password_change']);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script src='https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password telah dikemaskini',
                        text: 'Sila login semula untuk teruskan.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        background: '#ffffff',
                        color: '#333',
                        showClass: { popup: 'animate__animated animate__fadeInDown' },
                        hideClass: { popup: 'animate__animated animate__fadeOutUp' }
                    }).then(() => {
                        window.location = 'upload_proof.php'; // Redirect ke upload bukti
                    });
                });
            </script>";
            exit();
        } else {
            $error = "Ralat semasa update password. Sila cuba lagi.";
        }
    } else {
        $error = "Password tidak sama. Sila pastikan kedua-dua padanan betul.";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Tukar Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-lg p-4 border-0 rounded-4" style="max-width: 420px; width: 100%;">
        <h3 class="text-center mb-4 text-primary">🔒 Tukar Password</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white">🔑</span>
                    <input type="password" class="form-control" name="new_password" required placeholder="Masukkan password baru">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Sahkan Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white">✅</span>
                    <input type="password" class="form-control" name="confirm_password" required placeholder="Sahkan password baru">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">💾 Update Password</button>
        </form>
    </div>

    <?php if (isset($error)) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $error; ?>',
                confirmButtonText: 'Cuba Lagi',
                confirmButtonColor: '#d33',
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        </script>
    <?php } ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
