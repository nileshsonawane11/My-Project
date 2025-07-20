<?php
session_start();
include '../../../config.php';
header('Content-Type: application/json');

// Read and decode incoming JSON
$data = json_decode(file_get_contents("php://input"), true);

// Extract values from POST body
$players = $data['player_ids'] ?? [];
$match_id = $data['match_id'] ?? '';
$inning = $data['Inning'] ?? 1;

if (empty($players) || empty($match_id)) {
    echo json_encode(['status' => 400, 'message' => 'Missing player list or match ID']);
    exit();
}

// Fetch match from database
$result = mysqli_query($conn, "SELECT * FROM `matches` WHERE `match_id` = '$match_id'");
if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['status' => 404, 'message' => 'Match not found']);
    exit();
}

$row = mysqli_fetch_assoc($result);

// Decode score_log as associative array
$score_log = json_decode($row['score_log'], true);
$history = json_decode($row['history'], true) ?: [];

// Convert players to indexed format: 1 => id1, 2 => id2, etc.
$indexed_players = [];
foreach ($players as $i => $id) {
    $indexed_players[$i + 1] = $id;
}

// Assign to current_runners in the correct inning
$score_log['innings'][$inning]['current_runners'] = $indexed_players;

// Optionally update history
$history[] = $score_log;
$history_json = json_encode($history);
$json = json_encode($score_log);

// Update the match record
$update_query = "UPDATE `matches` SET 
    `score_log` = '$json', 
    `history` = '$history_json' 
    WHERE `match_id` = '$match_id'";

if (mysqli_query($conn, $update_query)) {
    echo json_encode(['status' => 200, 'message' => 'Runners updated successfully']);
} else {
    echo json_encode(['status' => 500, 'message' => 'Database update failed']);
}
?>
