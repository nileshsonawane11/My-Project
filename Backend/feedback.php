<?php
ob_start();
session_start();
error_reporting(1);
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$type = $data['type'];
$rating = $data['rating'];
$message = $data['message'];
$date = date('d/m/y');

$sql = "INSERT INTO feedback(`user_email`, `user_name`, `type`, `rating`, `message`, `date`) VALUES ('$email','$name','$type','$rating','$message','$date')";
$query = $conn->query($sql);
if($query){
    echo json_encode(['data'=> $data,'message'=>'Feedback submitted successfully.']);
}
?>