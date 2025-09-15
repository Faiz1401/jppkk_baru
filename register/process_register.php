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
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'faizrazak1401@gmail.com';
        $mail->Password   = 'rtmjoiaavkfapext'; // app password Gmail
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

// ============================
// Handle GET request -> return Gred JSON
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['jawatan_id'])) {
    $jawatan_id = intval($_GET['jawatan_id']);
    $sql = "SELECT IDGRED, GRED FROM tblgred WHERE JAWATAN_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $jawatan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $greds = [];
    while ($row = $result->fetch_assoc()) {
        $greds[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($greds);
    $stmt->close();
    $conn->close();
    exit;
}

// ============================
// Handle POST (Registration)
// ============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data
    $noIC           = $_POST['noIC'] ?? null;
    $name           = $_POST['name'] ?? null;
    $dob            = $_POST['dob'] ?? null;
    $retirementDate = $_POST['retirementDate'] ?? null;
    $email          = $_POST['email'] ?? null;
    $phone          = $_POST['phone'] ?? null;
    $religion       = $_POST['religion'] ?? null;
    $gender         = $_POST['gender'] ?? null;
    $institusi      = $_POST['institusi'] ?? null;
    $alamat         = $_POST['alamat'] ?? null;
    $alamatInstitusi= $_POST['alamatInstitusi'] ?? null;
    $jawatan_id     = $_POST['jawatan'] ?? null; 
    $gred_id        = $_POST['gred'] ?? null;

    // Validate required fields
    if (!$noIC || !$name || !$dob || !$retirementDate || !$email || !$phone || !$religion || !$gender || !$institusi || !$alamat || !$alamatInstitusi || !$jawatan_id || !$gred_id) {
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

    // ============================
    // Check IC already registered
    // ============================
    $check = $conn->prepare("SELECT NO_IC FROM tbluser WHERE NO_IC = ?");
    $check->bind_param("s", $noIC);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
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
                title: "Duplicate IC",
                text: "⚠ This IC number is already registered!"
            }).then(() => { window.history.back(); });
        </script>
        </body>
        </html>';
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Generate temp password
    $temp_pass = generateTempPass();

    // Insert user
    $sql = "INSERT INTO tbluser 
        (NO_IC, NAMA, TARIKH_LAHIR, TARIKH_PENCEN, EMAIL, PHONE, AGAMA, JANTINA, INSTITUSI, ALAMAT, ALAMAT_INSTITUSI, IDJAWATAN, GRED_ID, TEMP_PASS)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssss", 
        $noIC, $name, $dob, $retirementDate, $email, $phone, $religion, $gender, $institusi, $alamat, $alamatInstitusi, $jawatan_id, $gred_id, $temp_pass
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
