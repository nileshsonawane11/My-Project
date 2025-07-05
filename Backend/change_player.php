<?php
ob_start();
session_start();
error_reporting(1);
header('Content-Type: application/json');
include '../config.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);

$person = $data['person'];
$info = explode(',',$data['data']);
$player_id = $info[0];
$player_Style = $info[1];
$match_id = $data['match_id'];
$Inning = $data['Inning'];
$Inning_type = $data['Inning Type'] ?? null;

$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));

// Decode JSON
$score_log = json_decode($row['score_log'], true);

if($person == 'Bowler'){
    $score_log[$Inning_type][$Inning]['current_bowler']['id']=$player_id;
    $score_log[$Inning_type][$Inning]['current_bowler']['style']=$player_Style;

    $json = json_encode($score_log);
    $json = mysqli_real_escape_string($conn, $json);
    $query = mysqli_query($conn,"UPDATE `matches` SET `score_log` = '$json' WHERE `match_id` = '$match_id'");

}else if($person == 'Striker'){
    $score_log[$Inning_type][$Inning]['openers']['current_striker']['id']=$player_id;
    $score_log[$Inning_type][$Inning]['openers']['current_striker']['style']=$player_Style;

    $json = json_encode($score_log);
    $json = mysqli_real_escape_string($conn, $json);
    $query = mysqli_query($conn,"UPDATE `matches` SET `score_log` = '$json' WHERE `match_id` = '$match_id'");

}else if($person == 'Non-Striker'){
    $score_log[$Inning_type][$Inning]['openers']['current_non_striker']['id']=$player_id;
    $score_log[$Inning_type][$Inning]['openers']['current_non_striker']['style']=$player_Style;

    $json = json_encode($score_log);
    $json = mysqli_real_escape_string($conn, $json);
    $query = mysqli_query($conn,"UPDATE `matches` SET `score_log` = '$json' WHERE `match_id` = '$match_id'");
}

if($query){
    echo json_encode(['status'=>200,'message'=>'player Added','field'=>'success']);
    exit();
}

echo json_encode($data);
exit();

?>