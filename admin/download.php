<?php
session_start();
include '../db_connection.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Dapatkan nama fail dari DB
$stmt = $conn->prepare("SELECT BUKTI_PENGESAHAN FROM tbluser WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($filename);
$stmt->fetch();
$stmt->close();

if ($filename) {
    $file_path = "../uploads/" . $filename;
echo "Nama fail dalam DB: " . $filename . "<br>";
echo "Path penuh: " . realpath($file_path) . "<br>";

    if (file_exists($file_path)) {
        // Set header untuk download
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($file_path) . "\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "❌ Fail tidak dijumpai di server.";
    }
} else {
    echo "❌ Tiada fail untuk user ini.";
}
