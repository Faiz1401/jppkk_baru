<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; 
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = basename($_FILES["bukti"]["name"]);
        $target_file = $target_dir . $filename;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Jenis fail dibenarkan
        $allowed_types = array("jpg", "jpeg", "png", "pdf");
        if (!in_array($file_type, $allowed_types)) {
            $error = "Jenis fail tidak dibenarkan. Hanya PDF, JPG, JPEG & PNG.";
        } else {
            if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
                // Update DB dengan nama fail + set status pending
                $stmt = $conn->prepare("UPDATE tbluser SET BUKTI_PENGESAHAN = ?, STATUS = 0 WHERE ID = ?");
                $stmt->bind_param("si", $filename, $user_id);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Gagal simpan ke DB: " . $stmt->error;
            }

                            $stmt->close();
                        } else {
                            $error = "Ralat ketika muat naik fail.";
                        }
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
            <label for="file" class="form-label fw-semibold">Pilih Fail (PDF/JPG/PNG)</label>
            <input type="file" class="form-control" name="bukti" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>
        <button type="submit" class="btn btn-success w-100 mt-2">🚀 Hantar Bukti</button>
    </form>
</div>

<?php if (isset($success) && $success) { ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Bukti berjaya dimuat naik',
    text: 'Sila tunggu admin mengesahkan akaun anda.',
    confirmButtonText: 'OK',
    confirmButtonColor: '#3085d6'
}).then(() => {
    window.location = 'index.php';
});
</script>
<?php } ?>

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
