<?php
require_once("includes/auth.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Clear all session data */
$_SESSION = array();

/* Destroy session cookie if set */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* Destroy the session */
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

/* Redirect to AutoCare login/home page */
header("Location: index.php");
exit();
