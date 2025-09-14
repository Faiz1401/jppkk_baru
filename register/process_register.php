<?php
include 'db_connection.php';

// Function: generate temporary password
function generateTempPass($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $length);
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect data from POST
    $no_ic      = $_POST['noIC'] ?? null;
    $name       = $_POST['name'] ?? null;
    $gred       = $_POST['gred'] ?? null;
    $jantina    = $_POST['gender'] ?? null;
    $agama      = $_POST['religion'] ?? null;
    $institusi  = $_POST['institusi'] ?? null;
    $bidang     = $_POST['field'] ?? null;
    $subbidang  = $_POST['subField'] ?? null;
    $jabatan    = $_POST['department'] ?? null;
    $program    = $_POST['program'] ?? null;
    $tarikh_l   = $_POST['appointmentDate'] ?? null;
    $tarikh_p   = $_POST['retirementDate'] ?? null;
    $email      = $_POST['email'] ?? null;
    $phone      = $_POST['phone'] ?? null;

    // Validate required fields
    if (!$no_ic || !$name || !$gred || !$jantina || !$agama || !$institusi || !$bidang || !$email || !$phone) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Data',
                text: '⚠ Please fill in all required fields!'
            }).then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // Handle "new program" if selected
    if ($program === "new") {
        $kodProgram = $_POST['kodProgram'] ?? null;
        $namaProgram = $_POST['namaProgram'] ?? null;
        $jenisProgram = $_POST['jenisProgram'] ?? null;
        $bilKursus = $_POST['bilKursus'] ?? null;
        $necCode = $_POST['necCode'] ?? null;
        $akreditasi = $_POST['akreditasi'] ?? null;
        $versi = $_POST['versi'] ?? null;
        $tempoh = $_POST['tempoh'] ?? null;

        if ($kodProgram && $namaProgram) {
            $sqlProg = "INSERT INTO tblprogram (KODPROGRAM, NAMAPROGRAM, JENISPROGRAM, BILKURSUS, NEC_CODE, AKREDITASI, VERSI, TEMPOH_PENGAJIAN) 
                        VALUES (?,?,?,?,?,?,?,?)";
            $stmtProg = $conn->prepare($sqlProg);
            $stmtProg->bind_param("sssissss", 
                $kodProgram, $namaProgram, $jenisProgram, $bilKursus, $necCode, $akreditasi, $versi, $tempoh
            );

            if ($stmtProg->execute()) {
                $program = $kodProgram; // assign new program code
            } else {
                $errorMsg = addslashes($stmtProg->error);
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Program Error',
                        text: '❌ Error insert program: $errorMsg'
                    }).then(() => { window.history.back(); });
                </script>";
                exit;
            }
            $stmtProg->close();
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Program Data',
                    text: '⚠ Kod Program dan Nama Program wajib diisi!'
                }).then(() => { window.history.back(); });
            </script>";
            exit;
        }
    }

    // Generate temporary password
    $temp_pass = generateTempPass();

    // Insert user data
    $sql = "INSERT INTO tbluser 
        (NO_IC, NAMA, GRED, JANTINA, AGAMA, INSTITUSI, BIDANG_PENGAJIAN, SUB_BIDANG, JABATAN_UNIT, PROGRAM, TARIKH_LANTIKAN, TARIKH_PENCEN, EMAIL, PHONE, TEMP_PASS)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssss", 
        $no_ic, $name, $gred, $jantina, $agama, $institusi, $bidang, $subbidang, $jabatan, $program, $tarikh_l, $tarikh_p, $email, $phone, $temp_pass
    );

    if ($stmt->execute()) {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Registration</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Registration Successful!",
                html: "✅ Your temporary password is: <strong>'.$temp_pass.'</strong>",
                confirmButtonText: "Go to Login"
            }).then(() => { window.location.href="../index.php"; });
        </script>
        </body>
        </html>';
    } else {
        $errorMsg = addslashes($stmt->error);
                echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Registration</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Registration Successful!",
                html: "❌ Error insert user: <strong>'.$errorMsg.'</strong>",
                confirmButtonText: "Go to Login"
            }).then(() => { window.history.back(); });
        </script>
        </body>
        </html>';
    }

    $stmt->close();
    $conn->close();
}
?>
