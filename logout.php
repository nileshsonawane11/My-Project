<?php
session_start();

// Destroy all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Expire the 'user' cookie
if (isset($_COOKIE['user'])) {
    setcookie('user', '', time() - 3600, "/");
}

// Expire the 'email' cookie
if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, "/");
}

// Expire the PHPSESSID cookie too (optional but recommended)
if (isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', '', time() - 3600, "/");
}

// Redirect to login or homepage
echo json_encode(['status' => 200, 'message' => 'logout successfully', 'field' => 'success']);
exit();
?>
