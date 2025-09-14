<?php
session_start();
require '../db.php';
require '../email_helper.php'; // Tambah ini

if (!isset($_POST['id'], $_POST['action'])) {
    header("Location: ../admin.php?section=regis");
    exit;
}

$id     = intval($_POST['id']);
$action = $_POST['action'];
$role   = $_POST['role'] ?? 'user';

// Dapatkan emel & nama pengguna dari DB
$stmt = $conn->prepare("SELECT email, name FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($email, $name);
$stmt->fetch();
$stmt->close();

if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $id);
    $stmt->execute();

    sendDecisionEmail($email, $name, 'approve'); // <-- hantar emel lulus

    $_SESSION['success'] = "User approved.";

} elseif ($action === 'deny') {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    sendDecisionEmail($email, $name, 'deny'); // <-- hantar emel tolak

    $_SESSION['success'] = "User denied.";
}

header("Location: ../admin.php?section=regis");
exit;
