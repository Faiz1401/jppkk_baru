<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: ../register_event.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Helper function to sanitize and uppercase input
function getUpper($conn, $key) {
    return strtoupper(trim(mysqli_real_escape_string($conn, $_POST[$key] ?? '')));
}

$event_id            = intval($_POST['event_id']);
$program             = getUpper($conn, 'program');
$kod_program         = getUpper($conn, 'kod_program');
$kod_kursus          = getUpper($conn, 'kod_kursus');
$bil_kursus          = getUpper($conn, 'bil_kursus');
$peringkat_kelulusan = getUpper($conn, 'peringkat_kelulusan');
$justifikasi         = getUpper($conn, 'justifikasi');
$bidang              = getUpper($conn, 'bidang');
$kategori_permohonan = getUpper($conn, 'kategori_permohonan');
$kelulusan_jk3p2k    = getUpper($conn, 'kelulusan_jk3p2k');
$kelulusan_mlk       = getUpper($conn, 'kelulusan_mlk');
$pegawai_bidang      = getUpper($conn, 'pegawai_bidang');
$semester            = getUpper($conn, 'semester');

// Insert into regis table
$sql = "INSERT INTO regis (
            user_id, event_id, program, kod_program, kod_kursus, bil_kursus,
            peringkat_kelulusan, justifikasi, bidang, kategori_permohonan,
            kelulusan_jk3p2k, kelulusan_mlk, pegawai_bidang, semester
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iissssssssssss",
    $user_id, $event_id, $program, $kod_program, $kod_kursus, $bil_kursus,
    $peringkat_kelulusan, $justifikasi, $bidang, $kategori_permohonan,
    $kelulusan_jk3p2k, $kelulusan_mlk, $pegawai_bidang, $semester
);

if ($stmt->execute()) {
    $_SESSION['success'] = "Registration submitted successfully.";
} else {
    $_SESSION['error'] = "Failed to submit: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: register_event.php");
exit;
