<?php
session_start();
include 'db_connection.php';

// Pastikan user log masuk selepas tukar password
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['bukti']['tmp_name'];
        $file_content = file_get_contents($file_tmp);

        // Update DB: simpan blob & status jadi Pending (0)
        $stmt = $conn->prepare("UPDATE tbluser SET BUKTI_PENGESAHAN = ?, STATUS = 0 WHERE ID = ?");
        $stmt->bind_param("si", $file_content, $user_id);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Bukti Pengesahan Dihantar',
                    text: 'Sila tunggu admin mengesahkan akaun anda.',
                    confirmButtonText: '⬅️ Kembali ke Login',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    window.location = 'index.php';
                });
            });
        </script>";
        exit();
        } else {
            $error = "Ralat semasa upload. Cuba lagi.";
        }
    } else {
        $error = "Sila pilih fail untuk upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Muat Naik Bukti Pengesahan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-lg p-4 border-0 rounded-4" style="max-width: 420px; width: 100%;">
    <h3 class="text-center mb-4 text-primary">📂 Muat Naik Bukti Pengesahan</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Fail (PDF/JPG/PNG)</label>
            <input type="file" class="form-control" name="bukti" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>
        <button type="submit" class="btn btn-success w-100 mt-2">🚀 Hantar Bukti</button>
    </form>
</div>

<?php if (isset($error)) { ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Ralat',
            text: '<?php echo $error; ?>',
            confirmButtonText: 'Cuba Lagi',
            confirmButtonColor: '#d33'
        });
    </script>
<?php } ?>

</body>
</html>
