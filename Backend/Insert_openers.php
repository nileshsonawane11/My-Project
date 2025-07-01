<?php
ob_start();
session_start();
error_reporting(0);
header('Content-Type: application/json');
include '../config.php';

    $data = file_get_contents('php://input');
    $data = json_decode($data, true);

    $match_id = $data['match_id'];
    $current_innings = $data['current_innings'];
    $striker = explode(",",($data['striker']));
    $non_striker = explode(",",($data['non_striker']));
    $bowler = explode(",",($data['bowler']));

    // Get match row
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));

    // Decode JSON
    $score_log = json_decode($row['score_log'], true);

    // Add 1 run and 1 ball to striker
    $score_log['innings'][$current_innings]['openers']['current_striker']['id'] = $striker[0];
    $score_log['innings'][$current_innings]['openers']['current_non_striker']['id'] = $non_striker[0];

    $score_log['innings'][$current_innings]['openers']['current_striker']['style'] = $striker[1];
    $score_log['innings'][$current_innings]['openers']['current_non_striker']['style'] = $non_striker[1];

    $score_log['innings'][$current_innings]['current_bowler']['id'] = $bowler[0];
    $score_log['innings'][$current_innings]['current_bowler']['style'] = $bowler[1];

    $new_json = json_encode($score_log);
    mysqli_query($conn, "UPDATE matches SET score_log = '$new_json' WHERE match_id = '$match_id'");

    echo json_encode(['status' => 200, 'message' => 'Data Updated Successfully']);
    exit();
?>