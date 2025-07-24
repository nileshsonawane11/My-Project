<?php
    session_start();
    include '../config.php';
    $id = $_SESSION['user'];

    $delete_time = date('Y-m-d H:i:s');
    $updateQuery = "UPDATE users SET delete_on = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ss", $delete_time, $id);
    if($stmt->execute()){
        echo json_encode(['status' => 200, 'message' => 'Account Deleted']);
        exit();
    }else{
        echo json_encode(['status' => 409, 'message' => 'Account Can not Deleted']);
        exit();
    }
?>