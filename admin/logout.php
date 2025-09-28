<?php
session_start();

// Padam semua session
$_SESSION = [];
session_unset();
session_destroy();

// Hapus juga cookie session (extra lock)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Kawal cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect ke login
header("Location: ../index.php?logout=1");
exit();
?>
