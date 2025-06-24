<?php
session_start();
include '../config.php';

$password = $_POST['password'];
$match_id = $_POST['match_id'];

if(empty($password)){
    echo json_encode(['status'=>400,'message'=>'Password field is empty','field'=>'matchpassword']);
    exit();
}else{
    $query = mysqli_query($conn,"SELECT `password` FROM `matches` WHERE `match_id` = '$match_id'");
    $row = mysqli_fetch_assoc($query);
    if(empty($row['password'])){
        echo json_encode(['status'=>400,'message'=>'Password not set to match','field'=>'matchpassword']);
        exit();
    }else{
        if($row['password'] == $password){
            echo json_encode(['status'=>200,'message'=>'Password Matched','field'=>'matchpassword']);
            exit();
        }else{
            echo json_encode(['status'=>400,'message'=>'Password Not Matched','field'=>'matchpassword']);
            exit();
        }
    }
   
}

echo json_encode(['status'=>400,'message'=>'Password Matched','data'=>$password.' '.$match_id,'field'=>'matchpassword']);
exit();
?>