<?php
// Sambung DB
$conn = new mysqli("localhost", "root", "", "jppkk_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM tbluser WHERE ID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('User berjaya dipadam');
                window.location.href='user-maintenance.php';
              </script>";
    } else {
        echo "<script>
                alert('Ralat: Tidak dapat padam user');
                window.location.href='user-maintenance.php';
              </script>";
    }
}
$conn->close();
?>
