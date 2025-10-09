<?php
session_start();
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['KATEGORI'])) {
    $kategori     = $_POST['KATEGORI'];
    $siriNo       = $_POST['SIRIBENGKEL'];

    $tahun        = $_POST['TAHUN'] . "-01-01";
    $tarikhmula   = $_POST['TARIKHMULA'];
    $tarikhtamat  = $_POST['TARIKHTAMAT'];
    $lokasi       = $_POST['LOKASI'];
    $status       = $_POST['STATUS'];
    $justifikasi  = $_POST['JUSTIFIKASI'];

    $stmt = $conn->prepare("INSERT INTO tblbengkel 
        (KATEGORI, SIRIBENGKEL, TAHUN, TARIKHMULA, TARIKHTAMAT, LOKASI, STATUS, JUSTIFIKASI)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $kategori, $siriNo, $tahun, $tarikhmula, $tarikhtamat, $lokasi, $status, $justifikasi);


    if ($stmt->execute()) {
        $_SESSION['msg'] = "Bengkel berjaya didaftarkan!";
    } else {
        $_SESSION['msg'] = "Ralat simpan: " . $stmt->error;
    }

    $stmt->close();
    header("Location: bengkel.php");
    exit;
}
