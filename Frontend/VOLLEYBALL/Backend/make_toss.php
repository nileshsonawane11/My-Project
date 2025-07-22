<?php
session_start();
include '../../../config.php';

$selectedteam = $_POST['selectedteam'];
$selecteddecision = $_POST['selecteddecision'];
$sets = $_POST['sets'];
$points = $_POST['points'];

$match_id = $_POST['match_id'];
$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `matches` WHERE `match_id` = '$match_id'"));

$serve  = '';
$court = '';
if ($selecteddecision == "SERVE") {
    $serve  = $selectedteam;
    $court = ($row['team_1'] == $serve) ? $row['team_2'] : $row['team_1'];
} else if ($selecteddecision == "COURT") {
    $bowl = $selectedteam;
    $serve  = ($row['team_1'] == $court) ? $row['team_2'] : $row['team_1'];
}

$score_log = [
    "match_id" => $match_id,
    "team1" => $row['team_1'],
    "team2" => $row['team_2'],
    "team1_score" => 0,
    "team2_score" => 0,
    "total_sets" => $sets,
    "points_required" => $points,
    'current_serve' => $serve,
    "sets_won" => [
        "team1" => 0,
        "team2" => 0
    ],
    "current_set" => 1,
    "sets" => [],
    "completed" => false
];

// Generate set data dynamically
for ($i = 1; $i <= $sets; $i++) {
    $score_log['sets'][$i] = [
        "team1_points" => 0,
        "team2_points" => 0,
        "serving_team" => null,  // 'team1' or 'team2'
        "set_completed" => false,
        "points_required" => $points,
        "rallies" => [] // optional
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

if (empty($serve) && empty($court)) {
    echo json_encode(['status'=>409,'message'=>'Serve And Court Empty','field'=>'empty']);
    exit();
}

if(empty($sets)){
    echo json_encode(['status'=>409,'message'=>'Please fill the sets','field'=>'data_empty']);
    exit();
}

if(empty($points)){
    echo json_encode(['status'=>409,'message'=>'Please fill required points','field'=>'data_empty']);
    exit();
}

$query = mysqli_query($conn,"UPDATE `matches` SET `toss_winner` = '$selectedteam', `toss_decision` = '$selecteddecision',`score_log` = '$json',`history` = '$history_json' WHERE `match_id` = '$match_id'");
if($query){
    echo json_encode(['status'=>200,'message'=>$selectedteam.' elected to '.$selecteddecision,'field'=>'team']);
    exit();
}
?>