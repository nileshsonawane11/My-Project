<?php
header('Content-Type: application/json');
session_start();
include 'config.php';
$data = file_get_contents('php://input');
$data = json_decode($data, true);

$email = mysqli_real_escape_string($conn, $data['email']);
$password = mysqli_real_escape_string($conn, $data['password']);
$check = $data['checkbox'];
$password = md5($password);

$sql1 = "SELECT * FROM users WHERE email='$email' and password='$password' LIMIT 1";
$result1 = mysqli_query($conn, $sql1);

if (mysqli_num_rows($result1) > 0) {
    $row = mysqli_fetch_assoc($result1);

            if($check){
                setcookie('user', $row['user_id'], time() + (60 * 720), "/");   
                setcookie('email', $row['email'], time() + (60 * 720), "/");
            }
            $_SESSION['user']=$row['user_id'];
            $_SESSION['email']=$row['email'];
            $_SESSION['role']=$row['role'];
            $_SESSION['name']=$row['fname'].' '.$row['lname'];
            echo json_encode(['status' => 200, 'message' => 'login successfully', 'field' => 'success']);
            exit();
}else{
    echo json_encode(['status' => 409, 'message' => 'Invalid Credentials', 'field' => 'invalid']);
    exit();
}

mysqli_close($conn);
?>