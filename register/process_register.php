<?php
include 'db_connection.php';

// function generate temp password
function generateTempPass($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect data
    $no_ic      = $_POST['noIC'] ?? null;
    $name       = $_POST['name'] ?? null;
    $gred       = $_POST['gred'] ?? null;
    $jantina    = $_POST['gender'] ?? null;
    $agama      = $_POST['religion'] ?? null;
    $institusi  = $_POST['institusi'] ?? null;
    $bidang     = $_POST['field'] ?? null;        // IDBIDANGBK (dropdown)
    $subbidang  = $_POST['subField'] ?? null;
    $jabatan    = $_POST['department'] ?? null;
    $program    = $_POST['program'] ?? null;
    $tarikh_l   = $_POST['appointmentDate'] ?? null;
    $tarikh_p   = $_POST['retirementDate'] ?? null;
    $email      = $_POST['email'] ?? null;
    $phone      = $_POST['phone'] ?? null;

    // validate required
    if (!$no_ic || !$name || !$jantina || !$agama || !$institusi || !$bidang || !$email || !$phone) {
        echo "<script>
            alert('⚠ Please fill in all required fields!');
            window.history.back();
        </script>";
        exit;
    }

    // generate temp password
    $temp_pass = generateTempPass();

    // insert tanpa simpan password, status & bukti
    $sql = "INSERT INTO tbluser 
        (NO_IC, NAMA, GRED, JANTINA, AGAMA, INSTITUSI, BIDANG_PENGAJIAN, SUB_BIDANG, JABATAN_UNIT, PROGRAM, TARIKH_LANTIKAN, TARIKH_PENCEN, EMAIL, PHONE, TEMP_PASS)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssss", 
        $no_ic, $name, $gred, $jantina, $agama, $institusi, $bidang, $subbidang, $jabatan, $program, $tarikh_l, $tarikh_p, $email, $phone, $temp_pass
    );

    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Registration successful! Your temporary password is: $temp_pass');
            window.location.href='login.php';
        </script>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
