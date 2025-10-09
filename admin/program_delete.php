<?php
session_start();
include '../db_connection.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM tblprogram WHERE IDPROGRAM = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['msg'] = "🗑️ Program berjaya dipadam!";
    } else {
        $_SESSION['msg'] = "❌ Ralat: ".$stmt->error;
    }
}
header("Location: program_manage.php");
exit;
