<?php
session_start();
include '../db_connection.php';

if (isset($_GET['delete'])) {
    $idToDelete = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM tblbengkel WHERE IDBENGKEL = ?");
    if ($stmt) {
        $stmt->bind_param("i", $idToDelete);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Bengkel berjaya dipadam!";
        } else {
            $_SESSION['msg'] = "Ralat padam: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['msg'] = "Ralat SQL: " . $conn->error;
    }

    header("Location: bengkel.php");
    exit;
} else {
    $_SESSION['msg'] = "Tiada ID bengkel dipilih.";
    header("Location: bengkel.php");
    exit;
}
