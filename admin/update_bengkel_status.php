<?php
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id     = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';

    if (!empty($id) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE tblbengkel SET STATUS = ? WHERE IDBENGKEL = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo "Status berjaya dikemaskini!";
        } else {
            echo "Ralat kemaskini: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Data tidak lengkap!";
    }
} else {
    echo "Akses tidak sah!";
}

$conn->close();
?>
