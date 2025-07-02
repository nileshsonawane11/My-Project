<?php
ob_start();
session_start();
error_reporting(0);
header('Content-Type: application/json');
include '../config.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);

$Run = $data['Run'];
$Shot_type = $data['Shot Type'];
$Shot_side = $data['Shot Side'];
$match_id = $data['Match id'];
$Wicket_Type = $data['Wicket Type'];
$Extra = $data['Extra'];
$Ball_Type = $data['Ball Type'];
$Freehit = $data['Freehit'];
$Bowler = $data['Bowler'];
$Wicket_By = $data['Wicket By'];
$Out_Player = $data['Out Player'];
$New_Player = $data['New Player'];
$Striker = $data['Striker'];
$TotalScore = $data['TotalScore'];
$Wickets = $data['Wickets'];
$Inning = $data['Inning'];
$Commentary = $data['Commentary'];

$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));
// Decode JSON
$score_log = json_decode($row['score_log'], true);

if(!empty($data)){
    $score_log['innings'][$Inning]['balls'] = $data;

    $json = json_encode($score_log);
    $query = mysqli_query($conn,"UPDATE `matches` SET `score_log` = '$json' WHERE `match_id` = '$match_id'");
    if($query){
        echo json_encode(['status'=>200,'message'=>'data inserted successfully','field'=>'sucess']);
        exit();
    }
}

echo json_encode($data);
exit();

?>