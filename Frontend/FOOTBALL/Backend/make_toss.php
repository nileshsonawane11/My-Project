<?php
session_start();
include '../../../config.php';

$selectedteam = $_POST['selectedteam'];
$selecteddecision = $_POST['selecteddecision'];
$halves = $_POST['halves'];
$match_id = $_POST['match_id'];
$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `matches` WHERE `match_id` = '$match_id'"));

$kickout  = '';
$side = '';
if ($selecteddecision == "KICKOFF") {
    $kickout  = $selectedteam;
    $side = ($row['team_1'] == $kickout) ? $row['team_2'] : $row['team_1'];
} else if ($selecteddecision == "SIDE") {
    $side = $selectedteam;
    $kickout  = ($row['team_1'] == $side) ? $row['team_2'] : $row['team_1'];
}

if(empty($halves)){
    echo json_encode(['status'=>409,'message'=>'Please Enter Halves','field'=>'data_empty']);
    exit();
}

$score_log = [
    "match_id" => $match_id,
    "team1" => $row['team_1'],
    "team2" => $row['team_2'],
    "team1_score" => 0,
    "team2_score" => 0,
    "current_half" => 1,        // 1 = First Half, 2 = Second Half
    "total_halves" => $halves,        // standard football has 2 halves
    "halves" => [],
    "completed" => false
];

// Generate halves dynamically (first half, second half)
for ($i = 1; $i <= $score_log['total_halves']; $i++) {
    $score_log['halves'][$i] = [
        "team1_goals" => 0,
        "team2_goals" => 0,
        "team1_fouls" => 0,
        "team2_fouls" => 0,
        "yellow_cards" => [
            "team1" => [],
            "team2" => []
        ],
        "red_cards" => [
            "team1" => [],
            "team2" => []
        ],
        "substitutions" => [
            "team1" => [],
            "team2" => []
        ],
        "half_completed" => false,
        "events" => []   // each event (goal, foul, card, substitution) gets logged here
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