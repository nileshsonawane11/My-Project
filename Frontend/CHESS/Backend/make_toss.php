<?php
session_start();
include '../../../config.php';

$selectedteam = $_POST['selectedteam'];  // Player who won toss
$selecteddecision = $_POST['selecteddecision']; // "WHITE" or "BLACK"
$match_id = $_POST['match_id'];

$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `matches` WHERE `match_id` = '$match_id'"));

$white = '';
$black = '';

if(empty($selectedteam)){
    echo json_encode(['status'=>409,'message'=>'Please select Player','field'=>'team']);
    exit();
}

if ($selecteddecision == "WHITE") {
    $white = $selectedteam;
    $black = ($row['team_1'] == $white) ? $row['team_2'] : $row['team_1'];
} else if ($selecteddecision == "BLACK") {
    $black = $selectedteam;
    $white = ($row['team_1'] == $black) ? $row['team_2'] : $row['team_1'];
}

// Score log for Chess
$score_log = [
    "match_id" => $match_id,
    "white" => $white,
    "black" => $black,
    "moves" => [],          // All moves recorded in order
    "result" => null,       // "1-0", "0-1", or "½-½"
    "winner" => null,       // white / black / draw
    "completed" => false
];

$history_array = [$score_log];
$history_json = json_encode($history_array);
$json = json_encode($score_log);

// Validation
if(empty($selecteddecision) && empty($selectedteam)){
    echo json_encode(['status'=>409,'message'=>'Please select both Player and Decision','field'=>'empty']);
    exit();
}

if(empty($selecteddecision)){
    echo json_encode(['status'=>409,'message'=>'Please select Decision','field'=>'decision']);
    exit();
}

if (empty($white) && empty($black)) {
    echo json_encode(['status'=>409,'message'=>'White and Black not assigned','field'=>'empty']);
    exit();
}

// Update match with chess setup
$query = mysqli_query($conn,"
    UPDATE `matches` 
    SET `toss_winner` = '$selectedteam', 
        `toss_decision` = '$selecteddecision',
        `score_log` = '$json',
        `history` = '$history_json'
    WHERE `match_id` = '$match_id'
");

if($query){
    echo json_encode([
        'status'=>200,
        'message'=>$selectedteam.' selected '.$selecteddecision,
        'field'=>'player'
    ]);
    exit();
}
?>
