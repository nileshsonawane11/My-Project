<?php
session_start();
include '../config.php';

$selectedteam = $_POST['selectedteam'];
$selecteddecision = $_POST['selecteddecision'];
$match_id = $_POST['match_id'];

if(empty($selecteddecision) && empty($selectedteam)){
    echo json_encode(['status'=>409,'message'=>'Please select both Team and Decision','field'=>'empty']);
    exit();
}

if(empty($selecteddecision)){
    echo json_encode(['status'=>409,'message'=>'Please select Decision','field'=>'decision']);
    exit();
}

if(empty($selectedteam)){
    echo json_encode(['status'=>409,'message'=>'Please select Team','field'=>'team']);
    exit();
}

$query = mysqli_query($conn,"UPDATE `matches` SET `toss_winner` = '$selectedteam', `toss_decision` = '$selecteddecision' WHERE `match_id` = '$match_id'");
if($query){
    echo json_encode(['status'=>200,'message'=>$selectedteam.' elected to '.$selecteddecision,'field'=>'team']);
    exit();
}
?>