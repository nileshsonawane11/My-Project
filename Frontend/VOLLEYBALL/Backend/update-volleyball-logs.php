<?php
    ob_start();
session_start();
error_reporting(1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../../../config.php';

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

if(empty($data)){
    echo json_encode(['status'=>400,'message'=>'Empty request']);
    exit();
}

// Validate and sanitize input
$match_id = $data['match_id'] ?? null;
$action = $data['serve_action'] ?? null;
$serve_player = $data['serve_player'] ?? null;
$winner_team = $data['winner_team'] ?? null;

if(!$match_id) {
    echo json_encode(['status'=>400,'message'=>'Match ID required']);
    exit();
}

// Fetch match using prepared statement
$stmt = $conn->prepare("SELECT score_log FROM matches WHERE match_id = ?");
$stmt->bind_param("s", $match_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo json_encode(['status'=>404,'message'=>'Match not found']);
    exit();
}

$row = $result->fetch_assoc();
$score_log = json_decode($row['score_log'], true);

echo json_encode($data);
exit();

?>