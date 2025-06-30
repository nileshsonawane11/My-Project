<?php
session_start();
include '../config.php';

$selectedteam = $_POST['selectedteam'];
$selecteddecision = $_POST['selecteddecision'];
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
  "overs" => $row['overs'],
  "freehit" => true,
  "wide" => true,
  "innings" => [
    "1st" => [
      "batting_team" => $bat,
      "bowling_team" => $bowl,
      "openers" => [
        "striker_id" => [
          "id"=> null,
          "balls_faced"=> 0,
          "style"=> null,
          "runs"=> 0,
          "fours"=> 0,
          "sixes"=> 0,
          "out_status"=> "not out"
        ],
        "non_striker_id" => [
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
        "wickets"=> 0
      ],
      "balls" => [],
      "batmans" => [],
      "bowlers" => [],
      "total_runs" => 0,
      "wickets" => 0,
      "overs_completed" => "0.0",
      "completed" => false   
    ],
    "2nd" => [
      "batting_team" => $bowl,
      "bowling_team" => $bat,
      "openers" => [
        "striker_id" => [
          "id"=> null,
          "balls_faced"=> 0,
          "style"=> null,
          "runs"=> 0,
          "fours"=> 0,
          "sixes"=> 0,
          "out_status"=> "not out"
        ],
        "non_striker_id" => [
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
        "wickets"=> 0
      ],
      "balls" => [],
      "batmans" => [],
      "bowlers" => [],
      "total_runs" => 0,
      "wickets" => 0,
      "overs_completed" => "0.0",
      "completed" => false
    ]
  ]
];

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

if (empty($bat) || empty($bowl)) {
    echo json_encode(['status'=>409,'message'=>'Bat And Bowl Empty','field'=>'empty']);
    exit();
}


$query = mysqli_query($conn,"UPDATE `matches` SET `toss_winner` = '$selectedteam', `toss_decision` = '$selecteddecision',`score_log` = '$json' WHERE `match_id` = '$match_id'");
if($query){
    echo json_encode(['status'=>200,'message'=>$selectedteam.' elected to '.$selecteddecision,'field'=>'team']);
    exit();
}
?>