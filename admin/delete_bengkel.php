<?php
include '../db_connection.php'; 

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM tblbengkel WHERE IDBENGKEL = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
          Swal.fire({icon:'success',title:'Berjaya!',text:'Bengkel berjaya dipadam!'})
          .then(()=>{ window.location='bengkel.php'; });
        </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
          Swal.fire({icon:'error',title:'Ralat!',text:'Ralat padam: " . addslashes($stmt->error) . "'})
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
