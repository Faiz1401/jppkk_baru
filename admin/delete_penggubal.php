<?php
session_start();
include '../db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM tblpenggubal WHERE IDKETUA=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Ketua Penggubal berjaya dikeluarkan.";
    } else {
        $_SESSION['msg'] = "Ralat semasa keluarkan: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: penggubal.php");
exit;
