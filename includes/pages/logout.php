<?php
session_start(); // Start session

// Clear all session variables
$_SESSION = [];

// Destroy session and cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Destroy session

// Redirect to login page
header("Location: login.php");
exit();
?>
