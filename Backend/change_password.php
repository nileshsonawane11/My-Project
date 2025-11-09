<?php
include '../config.php';
ob_start();
session_start();
error_reporting(1);
header('Content-Type: application/json');

// Read JSON input from request body
$input = json_decode(file_get_contents("php://input"), true);

// Extract and sanitize values
$user_id  = $_SESSION['user'];
$current  = trim($input['current_password'] ?? '');
$newPass  = trim($input['new_password'] ?? '');
$confirm  = trim($input['confirm_password'] ?? '');

// Basic empty check
if ($current == '' || $newPass == '' || $confirm == '') {
    echo json_encode(['status' => 400, 'message' => 'All fields are required', 'field' => 'general']);
    exit();
}

// Get stored hash from DB
$sql = "SELECT password FROM users WHERE user_id='$user_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 400, 'message' => 'User not found', 'field' => 'general']);
    exit();
}

$row = mysqli_fetch_assoc($result);
$stored_hash = $row['password'];

// Check if password is hashed using password_hash or md5 (migration support)
$is_valid = false;
if (password_verify($current, $stored_hash)) {
    $is_valid = true;
} elseif (md5($current) === $stored_hash) {
    $is_valid = true;
}

if (!$is_valid) {
    echo json_encode(['status' => 400, 'message' => 'Current password is incorrect', 'field' => 'current_password']);
    exit();
}

// Prevent reuse of old password
if ($current === $newPass) {
    echo json_encode(['status' => 400, 'message' => 'New password cannot be same as current', 'field' => 'new_password']);
    exit();
}

// Confirm match
if ($newPass !== $confirm) {
    echo json_encode(['status' => 400, 'message' => 'Passwords do not match', 'field' => 'confirm_password']);
    exit();
}

// Strength check
if (
    strlen($newPass) < 8 ||
    !preg_match('/[A-Z]/', $newPass) ||
    !preg_match('/[a-z]/', $newPass) ||
    !preg_match('/[0-9]/', $newPass) ||
    !preg_match('/[\W]/', $newPass)
) {
    echo json_encode(['status' => 400, 'message' => 'Password must contain uppercase, lowercase, number, and special character', 'field' => 'new_password']);
    exit();
}

// Hash new password (bcrypt)
$newHash = md5($newPass);

// Update DB
$update_sql = "UPDATE users SET password='$newHash' WHERE user_id='$user_id'";
if (mysqli_query($conn, $update_sql)) {
    echo json_encode(['status' => 200, 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['status' => 400, 'message' => 'Database error while updating password', 'field' => 'general']);
}
?>
