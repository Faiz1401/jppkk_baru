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
            unset($_SESSION['require_password_change']);
            session_destroy(); // paksa login semula

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berjaya!',
            html: '<b>Anda telah kemaskini password anda.</b><br>Sila login semula untuk teruskan.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            background: '#fefefe',
            color: '#333',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then(() => {
            window.location = 'index.php';
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-lg p-4" style="width: 400px;">
        <h3 class="text-center mb-4">Tukar Password</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Sahkan Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update</button>
        </form>
    </div>

    <?php if (isset($error)) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $error; ?>',
                confirmButtonText: 'Cuba Lagi'
            });
        </script>
    <?php } ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
