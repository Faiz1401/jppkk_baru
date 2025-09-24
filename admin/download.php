<?php
$conn = new mysqli("localhost", "root", "", "jppkk_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT BUKTI_PENGESAHAN FROM tbluser WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($fileData);
$stmt->fetch();

if ($fileData) {
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=bukti_$id.pdf");
    echo $fileData;
} else {
    echo "Fail tidak dijumpai!";
}

$stmt->close();
$conn->close();
?>
