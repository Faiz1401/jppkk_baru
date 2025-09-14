<?php
session_start();
require '../db.php';
require '../email_helper.php'; // ✅ Tambah ini

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if (in_array($action, ['approve', 'deny'])) {
        $new_status = $action === 'approve' ? 'approved' : 'denied';

        // ✅ 1. Dapatkan user_id dari regis table
        $stmt = $conn->prepare("SELECT user_id FROM regis WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if (!$user_id) {
            $_SESSION['error'] = "User tidak dijumpai.";
            header("Location: ../admin.php?section=applicants");
            exit;
        }

        // ✅ 2. Dapatkan nama & emel user dari users table
        $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($user_name, $user_email);
        $stmt->fetch();
        $stmt->close();

        // ✅ 3. Kemas kini status permohonan
        $stmt = $conn->prepare("UPDATE regis SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Applicant $new_status successfully.";

            // ✅ 4. Hantar emel kepada pemohon
            sendDecisionEmail($user_email, $user_name, $action);
        } else {
            $_SESSION['error'] = "Failed to update applicant status.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid action.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: ../admin.php?section=applicants");
exit;
