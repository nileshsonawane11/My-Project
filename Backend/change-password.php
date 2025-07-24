<?php
    include '../config.php';

    $user_id  = $_POST['user_id'] ?? '';
    $current  = $_POST['current'] ?? '';
    $newPass  = $_POST['newPass'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if($current == '' || $newPass == '' || $confirm == ''){
        echo json_encode(['status' => 400, 'message' => 'All fields are required', 'field' => 'empty']);
        exit();
    }

    $current_hash = md5($current);

    $sql1 = "SELECT password FROM users WHERE user_id='$user_id' LIMIT 1";
    $result1 = mysqli_query($conn, $sql1);

    if (mysqli_num_rows($result1) > 0) {
        $row = mysqli_fetch_assoc($result1);

        // Check if new password matches current password
        if ($current_hash != $row['password']) {
            echo json_encode(['status' => 400, 'message' => 'Current password is incorrect', 'field' => 'empty']);
            exit();
        }
    }

    if($current == $newPass) {
        echo json_encode(['status' => 400, 'message' => 'Password cannot be same as current', 'field' => 'empty']);
        exit();
    }

    if($newPass !== $confirm){
        echo json_encode(['status' => 400, 'message' => 'Passwords do not match', 'field' => 'empty']);
        exit();
    }else if(!preg_match('/[A-Z]/', $newPass) || 
    !preg_match('/[a-z]/', $newPass) || 
    !preg_match('/[0-9]/', $newPass) || 
    !preg_match('/[\W]/', $newPass)){
        echo json_encode(['status' => 400, 'message' => 'Password must include uppercase, lowercase, number, and special character', 'field' => 'empty']);
        exit();
    }

    $newPass = md5($newPass);

    $sql = "UPDATE users SET password='$newPass' WHERE user_id='$user_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo json_encode(['status' => 200, 'message' => 'Password updated successfully', 'field' => 'empty']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'An error occurred while updating password', 'field' => 'empty']);
    }
?>