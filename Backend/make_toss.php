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


$query = mysqli_query($conn,"UPDATE `matches` SET `toss_winner` = '$selectedteam', `toss_decision` = '$selecteddecision',`score_log` = '$json',`history` = '$history_json' WHERE `match_id` = '$match_id'");
if($query){
    echo json_encode(['status'=>200,'message'=>$selectedteam.' elected to '.$selecteddecision,'field'=>'team']);
    exit();
}
?>