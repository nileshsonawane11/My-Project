<?php
session_start();
include '../config.php';

$selectedteam = $_POST['selectedteam'];
$selecteddecision = $_POST['selecteddecision'];
$isfreehit = $_POST['isfreehit'];
$iswide = $_POST['iswide'];
$issuperover = $_POST['issuperover'];
$overs = $_POST['overs'];
$team1_players = $_POST['team1_players'];
$team2_players = $_POST['team2_players'];

$match_id = $_POST['match_id'];
$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `matches` WHERE `match_id` = '$match_id'"));

$decision_t1_id = $selectedteam;
$t2_id = ($row['team_1'] == $selectedteam) ? $row['team_2'] : $row['team_1'];;
$q = mysqli_query($conn,"SELECT * FROM teams WHERE t_id IN ('$decision_t1_id', '$t2_id')");

$teams = [];
while($row3 = mysqli_fetch_assoc($q)) {
    $teams[$row3['t_id']] = $row3['t_name'];  // Map by team ID
}

$bat  = '';
$bowl = '';
if ($selecteddecision == "BAT") {
    $bat  = $selectedteam;
    $bowl = ($row['team_1'] == $bat) ? $row['team_2'] : $row['team_1'];
} else if ($selecteddecision == "BOWL") {
    $bowl = $selectedteam;
    $bat  = ($row['team_1'] == $bowl) ? $row['team_2'] : $row['team_1'];
}

$score_log = [
  "match_id" => $match_id,
  "team1" => $row['team_1'],
  "team2" => $row['team_2'],
  "team1_players" => $team1_players,
  "team2_players" => $team2_players,
  "team1_score" => null,
  "team2_score" => null,
  "team1_Wickets" => null,
  "team2_Wickets" => null,
  "toss_decision" => $teams[$decision_t1_id]. " won the toss and elected to ". $selecteddecision,
  "inline" => $teams[$decision_t1_id]. " won the toss and elected to ". $selecteddecision,
  "overs" => $overs,
  "freehit" => $isfreehit,
  "wide" => $iswide,
  'issuperallow' => $issuperover,
  "super_over" => false,
  "innings" => [
    "1st" => [
      "batting_team" => $bat,
      "bowling_team" => $bowl,
      "openers" => [
        "current_striker" => [
          "id"=> null,
          "balls_faced"=> 0,
          "style"=> null,
          "runs"=> 0,
          "fours"=> 0,
          "sixes"=> 0,
          "out_status"=> "not out"
        ],
        "current_non_striker" => [
          "id"=> null,
          "balls_faced"=> 0,
          "style"=> null,
          "runs"=> 0,
          "fours"=> 0,
          "sixes"=> 0,
          "out_status"=> "not out"
        ]
      ],
      "current_bowler" => [
        "id"=> null,
        "style"=> null,
        "overs_bowled"=> "0.0",
        "runs_conceded"=> 0,
        "wickets"=> 0,
        "maidens" => 0,
        "Extras" => [
          "NB" => 0,
          "W" => 0,
          "total_extras" => 0
        ]
      ],
      "balls" => [],
      "batmans" => [],
      "bowlers" => [],
      "total_runs" => 0,
      "wickets" => 0,
      "Extras" => [
        "B" => 0,
        "NB" => 0,
        "LB" => 0,
        "W" => 0,
        "total_extras" => 0
      ],
      "overs_completed" => "0.0",
      "completed" => false   
    ],
    "2nd" => [
      "batting_team" => $bowl,
      "bowling_team" => $bat,
      "openers" => [
        "current_striker" => [
          "id"=> null,
          "balls_faced"=> 0,
          "style"=> null,
          "runs"=> 0,
          "fours"=> 0,
          "sixes"=> 0,
          "out_status"=> "not out"
        ],
        "current_non_striker" => [
          "id"=> null,
          "balls_faced"=> 0,
          "style"=> null,
          "runs"=> 0,
          "fours"=> 0,
          "sixes"=> 0,
          "out_status"=> "not out"
        ]
      ],
      "current_bowler" => [
        "id"=> null,
        "style"=> null,
        "overs_bowled"=> "0.0",
        "runs_conceded"=> 0,
        "wickets"=> 0,
        "Extras" => [
          "NB" => 0,
          "W" => 0,
          "total_extras" => 0
        ]
      ],
      "balls" => [],
      "batmans" => [],
      "bowlers" => [],
      "total_runs" => 0,
      "wickets" => 0,
      "Extras" => [
        "B" => 0,
        "NB" => 0,
        "LB" => 0,
        "W" => 0,
        "total_extras" => 0
      ],
      "overs_completed" => "0.0",
      "completed" => false,
    ]
    ],
  'super_over_innings' => []
];
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

if(empty($team1_players) || empty($team2_players)){
    echo json_encode(['status'=>409,'message'=>'Select Playing 11s','field'=>'team']);
    exit();
}

if (empty($bat) || empty($bowl)) {
    echo json_encode(['status'=>409,'message'=>'Bat And Bowl Empty','field'=>'empty']);
    exit();
}

if (empty($overs) || $overs == 'Null') {
    echo json_encode(['status'=>409,'message'=>'please Select Overs','field'=>'over']);
    exit();
}


$stmt = $conn->prepare("
    UPDATE matches 
    SET toss_winner = ?, toss_decision = ?, score_log = ?, history = ?
    WHERE match_id = ?
");

$stmt->bind_param(
    "sssss",
    $selectedteam,
    $selecteddecision,
    $json,
    $history_json,
    $match_id
);

$stmt->execute();

if ($stmt->affected_rows >= 0) {
    echo json_encode([
        'status' => 200,
        'message' => $teams[$decision_t1_id] . ' elected to ' . $selecteddecision,
        'field' => 'team'
    ]);
    exit();
}

?>