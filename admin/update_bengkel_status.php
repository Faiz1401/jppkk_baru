<?php
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id     = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $status = $_POST['status'] ?? '';

    if ($id > 0 && !empty($status)) {
        $stmt = $conn->prepare("UPDATE tblbengkel SET STATUS = ? WHERE IDBENGKEL = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "✅ Status berjaya dikemaskini!";
            } else {
                echo "⚠️ Tiada perubahan dibuat (mungkin value sama).";
            }
        } else {
            echo "❌ Ralat kemaskini: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ Data tidak lengkap!";
    }
} else {
    echo "❌ Akses tidak sah!";
}

$conn->close();
?>
