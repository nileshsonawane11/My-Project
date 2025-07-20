<?php
session_start();
include '../../../config.php';

// Input validation
$selectedteam = $_POST['selectedteam'] ?? '';
$selecteddecision = $_POST['selecteddecision'] ?? '';
$innings = (int)$_POST['innings'] ?? '';
$match_id = $_POST['match_id'] ?? '';

if (empty($selecteddecision) && empty($selectedteam)) {
    echo json_encode(['status' => 409, 'message' => 'Please select both Team and Decision', 'field' => 'empty']);
    exit();
}
if (empty($selecteddecision)) {
    echo json_encode(['status' => 409, 'message' => 'Please select Decision', 'field' => 'decision']);
    exit();
}
if (empty($selectedteam)) {
    echo json_encode(['status' => 409, 'message' => 'Please select Team', 'field' => 'team']);
    exit();
}
if (empty($innings)) {
    echo json_encode(['status' => 409, 'message' => 'Missing Innings Count', 'field' => 'data_empty']);
    exit();
}

// Fetch match teams
$result = mysqli_query($conn, "SELECT * FROM `matches` WHERE `match_id` = '$match_id'");
if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 404, 'message' => 'Match not found', 'field' => 'empty']);
    exit();
}

$row = mysqli_fetch_assoc($result);
$run = '';
$chase = '';

// Determine run/chase team
if ($selecteddecision == "RUN") {
    $run = $selectedteam;
    $chase = ($row['team_1'] == $run) ? $row['team_2'] : $row['team_1'];
} elseif ($selecteddecision == "CHASE") {
    $chase = $selectedteam;
    $run = ($row['team_1'] == $chase) ? $row['team_2'] : $row['team_1'];
}

if (empty($run) || empty($chase)) {
    echo json_encode(['status' => 409, 'message' => 'Run/Chase team could not be determined', 'field' => 'team']);
    exit();
}

// Prepare score log
$score_log = [
    "match_id" => $match_id,
    "team1" => $row['team_1'],
    "team2" => $row['team_2'],
    "total_innings" => (int)$innings,
    "innings" => [],
    "current_inning" => 1,
    "completed" => false,
    "team1_score" => 0,
    "team2_score" => 0
];

// Create innings structure
for ($inning = 1; $inning <= $innings; $inning++) {
    $score_log['innings'][$inning] = [
        "attack_team" => ($inning % 2 != 1) ? $run : $chase,
        "defence_team" => ($inning % 2 != 1) ? $chase : $run,
        "current_runners" => [],
        "team1_points" => 0,
        "team2_points" => 0,
        "out_runners" => [],
        "tags" => [],
    ];
}

// Encode data
$history_array = [$score_log];
$history_json = json_encode($history_array);
$json = json_encode($score_log);

// Update match table
$update_query = "UPDATE `matches` SET 
    `toss_winner` = '$selectedteam',
    `toss_decision` = '$selecteddecision',
    `score_log` = '$json',
    `history` = '$history_json'
    WHERE `match_id` = '$match_id'";

if (mysqli_query($conn, $update_query)) {
    echo json_encode(['status' => 200, 'message' => "$selectedteam elected to $selecteddecision", 'field' => 'success']);
    exit();
} else {
    echo json_encode(['status' => 500, 'message' => 'Database update failed', 'field' => 'db']);
    exit();
}
?>
