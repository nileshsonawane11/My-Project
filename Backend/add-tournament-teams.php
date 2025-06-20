<?php
session_start();
include '../config.php';
$teams = json_decode($_POST['teams'], true);
$tournament = mysqli_real_escape_string($conn,$_POST['tournament']);

// print_r($teams);

if(!isset($_POST['teams']) || empty($_POST['teams'][0])){
    echo json_encode(['status'=>409,'message'=>'No teams selected','field'=>'empty']);
    exit();
}

if(count($teams) < 2){
    echo json_encode(['status'=>409,'message'=>'At least two teams required','field'=>'empty']);
    exit();
}

foreach($teams as $team){
    // Check if record already exists
    $check = mysqli_query($conn, "SELECT * FROM `tournament_teams` WHERE `tournament_id` = '$tournament' AND `team_id` = '$team'");

    if(mysqli_num_rows($check) == 0){
        // Insert if not exists
        mysqli_query($conn, "INSERT INTO `tournament_teams`(`tournament_id`, `team_id`) VALUES ('$tournament', '$team')");
    }
}

    echo json_encode(['status'=>200,'message'=>'Teams Added Successfully','field'=>'empty']);
    exit();
?>