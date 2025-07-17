<?php
session_start();
include '../../../config.php';

$selectedteam = $_POST['selectedteam'];
$selecteddecision = $_POST['selecteddecision'];

$match_id = $_POST['match_id'];
$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `matches` WHERE `match_id` = '$match_id'"));

$raid  = '';
$defence = '';
if ($selecteddecision == "RAID") {
    $raid  = $selectedteam;
    $defence = ($row['team_1'] == $raid) ? $row['team_2'] : $row['team_1'];
} else if ($selecteddecision == "DEFENCE") {
    $defence = $selectedteam;
    $raid  = ($row['team_1'] == $defence) ? $row['team_2'] : $row['team_1'];
}

$score_log = [
    "match_id" => $match_id,
    "team1" => $row['team_1'],
    "team2" => $row['team_2'],
    "current_raid_team" => $raid,
    "team1_score" => 0,
    "team2_score" => 0,
    "total_halves" => 2,
    "current_half" => 1,
    "halves" => [],
    "match_completed" => false,
    "tie_breaker" => false, // in case match ties, can switch to true
    "winner" => null // 'team1' or 'team2' after result
];

// Generate half data dynamically
for ($i = 1; $i <= 2; $i++) {
    $score_log['halves'][$i] = [
        "team1_points" => 0,
        "team2_points" => 0,
        "raids" => [], // optional if you want to log raid-wise
        "half_completed" => false
    ];
}

$history_array = [$score_log];  // this makes it an array of one element
$history_json = json_encode($history_array);
$json = json_encode($score_log);

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

$query = mysqli_query($conn,"UPDATE `matches` SET `toss_winner` = '$selectedteam', `toss_decision` = '$selecteddecision',`score_log` = '$json',`history` = '$history_json' WHERE `match_id` = '$match_id'");
if($query){
    echo json_encode(['status' => 200,'message' => $selectedteam.' elected to '.$selecteddecision,'field' => 'team']);
    exit();
}
?>