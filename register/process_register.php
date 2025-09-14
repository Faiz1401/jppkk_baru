<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

include 'db_connection.php';

// Function generate temporary password
function generateTempPass($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $length);
}

// Function send email
function sendTempPassword($toEmail, $temp_pass, $name) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com'; // ganti dengan SMTP server sebenar
        $mail->SMTPAuth   = true;
        $mail->Username   = 'faizrazak1401@gmail.com'; // email dari mana
        $mail->Password   = 'lptofvkqvtiizatm'; // app password Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('faizrazak1401@gmail.com', 'JPPKK Registration');
        $mail->addAddress($toEmail, $name);
        $mail->addReplyTo($toEmail, $name);

        $mail->isHTML(true);
        $mail->Subject = 'JPPKK Registration Successful';
        $mail->Body    = "Hello $name,<br><br>
            ✅ Your temporary password is: <strong>$temp_pass</strong><br>
            Please wait for admin confirmation before you can log in.<br><br>
            Regards,<br>JPPKK Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data
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
                icon: "error",
                title: "Incomplete Data",
                text: "⚠ Please fill in all required fields!"
            }).then(() => { window.history.back(); });
        </script>
        </body>
        </html>';
        exit;
    }

    // Jika user tambah program baru
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
                $program = $kodProgram;
            } else {
                echo '<!DOCTYPE html>
                <html lang="en">
                <head><meta charset="UTF-8"><title>Error</title><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head>
                <body>
                <script>
                    Swal.fire({
                        icon: "error",
                        title: "Program Error",
                        text: "❌ Error insert program: '.$stmtProg->error.'"
                    }).then(() => { window.history.back(); });
                </script>
                </body></html>';
                exit;
            }
            $stmtProg->close();
        } else {
            echo '<!DOCTYPE html>
            <html lang="en">
            <head><meta charset="UTF-8"><title>Error</title><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head>
            <body>
            <script>
                Swal.fire({
                    icon: "warning",
                    title: "Incomplete Program Data",
                    text: "⚠ Kod Program dan Nama Program wajib diisi!"
                }).then(() => { window.history.back(); });
            </script>
            </body></html>';
            exit;
        }
    }

    // Generate temp password
    $temp_pass = generateTempPass();

    // Insert user
    $sql = "INSERT INTO tbluser 
        (NO_IC, NAMA, GRED, JANTINA, AGAMA, INSTITUSI, BIDANG_PENGAJIAN, SUB_BIDANG, JABATAN_UNIT, PROGRAM, TARIKH_LANTIKAN, TARIKH_PENCEN, EMAIL, PHONE, TEMP_PASS)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssss", 
        $no_ic, $name, $gred, $jantina, $agama, $institusi, $bidang, $subbidang, $jabatan, $program, $tarikh_l, $tarikh_p, $email, $phone, $temp_pass
    );

    if ($stmt->execute()) {
        // Send email to pendaftar
        sendTempPassword($email, $temp_pass, $name);

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
                html: "✅ Your registration has been sent to your email.<br>Please wait for admin confirmation.<br>Your temporary password is: <strong>'.$temp_pass.'</strong>",
                confirmButtonText: "Go to Login"
            }).then(() => { window.location.href="../index.php"; });
        </script>
        </body>
        </html>';
    } else {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Error</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: "error",
                title: "Registration Failed",
                text: "❌ Error insert user: '.$stmt->error.'"
            }).then(() => { window.history.back(); });
        </script>
        </body>
        </html>';
    }

    $stmt->close();
    $conn->close();
}
?>
