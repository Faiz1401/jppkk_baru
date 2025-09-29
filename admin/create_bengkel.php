<?php
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['SIRI_KATEGORI'])) {
    $siriKategori = $_POST['SIRI_KATEGORI'];
    $siriNo       = $_POST['SIRI_NO'];
    $siribengkel  = $siriKategori . "-" . $siriNo;

    $tahun        = $_POST['TAHUN'] . "-01-01";
    $tarikhmula   = $_POST['TARIKHMULA'];
    $tarikhtamat  = $_POST['TARIKHTAMAT'];
    $lokasi       = $_POST['LOKASI'];
    $status       = $_POST['STATUS'];
    $justifikasi  = $_POST['JUSTIFIKASI'];

    $stmt = $conn->prepare("INSERT INTO tblbengkel 
        (SIRIBENGKEL, TAHUN, TARIKHMULA, TARIKHTAMAT, LOKASI, STATUS, JUSTIFIKASI)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $siribengkel, $tahun, $tarikhmula, $tarikhtamat, $lokasi, $status, $justifikasi);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
          Swal.fire({icon:'success',title:'Berjaya!',text:'Bengkel berjaya didaftarkan!'})
          .then(()=>{ window.location='bengkel.php'; });
        </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
          Swal.fire({icon:'error',title:'Ralat!',text:'Ralat simpan: " . addslashes($stmt->error) . "'})
          .then(()=>{ window.location='bengkel.php'; });
        </script>";
    }
    $stmt->close();
} else {
    header("Location: bengkel.php");
    exit;
}

$conn->close();
?>
