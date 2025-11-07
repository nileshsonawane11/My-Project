<?php
session_start();

// Destroy all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Redirect to login or homepage
echo json_encode(['status' => 200, 'message' => 'logout successfully', 'field' => 'success']);
exit();

?>