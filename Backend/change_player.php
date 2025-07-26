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
function saveHistorySnapshot($conn, $match_id, $score_log) {
    // Fetch current history from DB
    $result = $conn->query("SELECT history FROM matches WHERE match_id = '$match_id'");
    $row = $result->fetch_assoc();
    $history = json_decode($row['history'], true);

    // Initialize if null
    if (!is_array($history)) $history = [];

    // Add new score_log snapshot to history
    $history[] = $score_log;

    // Maintain max 15 entries — remove oldest if more than 15
    if (count($history) > 15) {
        array_shift($history);  // remove oldest
    }

    // Encode to JSON
    $newHistoryJson = json_encode($history);
    $newScoreLogJson = json_encode($score_log);

    // Update history and score_log in DB
    $stmt = $conn->prepare("UPDATE matches SET history = ?, score_log = ? WHERE match_id = ?");
    $stmt->bind_param("sss", $newHistoryJson, $newScoreLogJson, $match_id);

    if ($stmt->execute()) {
        echo json_encode(['status'=>200,'message'=>'player Added','field'=>'success']);
        exit();
    } else {
        // Optionally log error somewhere or return error info
        echo json_encode(['status'=>400,'message'=>'player not Added','field'=>'error']);
        exit();
    }
}
// Decode JSON
$score_log = json_decode($row['score_log'], true);

if($person == 'Bowler'){

    // Reference bowler list
    $bowlers = &$score_log[$Inning_type][$Inning]['bowlers'];

    // Check if bowler already exists — if not, create new entry
    if (!isset($bowlers[$player_id])) {
        $score_log[$Inning_type][$Inning]['current_bowler'] = [
            "id" => $player_id,
            "style" => $player_Style,
            "overs_bowled" => "0.0",
            "runs_conceded" => 0,
            "wickets" => 0,
            "maidens" => 0,
            "Extras" => [
            "NB" => 0,
            "W" => 0,
            "total_extras" => 0
            ]
        ];

        $bowlers[$player_id] = [
            "id" => $player_id,
            "style" => $player_Style,
            "overs_bowled" => "0.0",
            "runs_conceded" => 0,
            "wickets" => 0,
            "maidens" => 0,
            "Extras" => [
            "NB" => 0,
            "W" => 0,
            "total_extras" => 0
            ]
        ];
    }

    // Now safely assign this bowler as current bowler
    $score_log[$Inning_type][$Inning]['current_bowler'] = [
        "id" => $bowlers[$player_id]['id'],
        "style" => $bowlers[$player_id]['style'],
        "overs_bowled" => $bowlers[$player_id]['overs_bowled'],
        "runs_conceded" => $bowlers[$player_id]['runs_conceded'],
        "wickets" => $bowlers[$player_id]['wickets'],
        "maidens" => $bowlers[$player_id]['maidens'],
            "Extras" => [
            "NB" => 0,
            "W" => 0,
            "total_extras" => 0
            ]
    ];

    saveHistorySnapshot($conn, $match_id, $score_log);

}else if($person == 'Striker'){
    $score_log[$Inning_type][$Inning]['openers']['current_striker']['id'] = $player_id;
    $score_log[$Inning_type][$Inning]['openers']['current_striker']['style'] = $player_Style;

    // Also add to batmans if not already present
    $already_present = false;
    foreach ($score_log[$Inning_type][$Inning]['batmans'] as $b) {
        if ($b['id'] === $player_id) {
            $already_present = true;
            break;
        }
    }
    if (!$already_present) {
        $score_log[$Inning_type][$Inning]['batmans'][] = $score_log[$Inning_type][$Inning]['openers']['current_striker'];
    }

    saveHistorySnapshot($conn, $match_id, $score_log);

} else if($person == 'Non-Striker'){
    $score_log[$Inning_type][$Inning]['openers']['current_non_striker']['id'] = $player_id;
    $score_log[$Inning_type][$Inning]['openers']['current_non_striker']['style'] = $player_Style;

    // Also add to batmans if not already present
    $already_present = false;
    foreach ($score_log[$Inning_type][$Inning]['batmans'] as $b) {
        if ($b['id'] === $player_id) {
            $already_present = true;
            break;
        }
    }
    if (!$already_present) {
        $score_log[$Inning_type][$Inning]['batmans'][] = $score_log[$Inning_type][$Inning]['openers']['current_non_striker'];
    }

    saveHistorySnapshot($conn, $match_id, $score_log);
}

// if($query){
//     echo json_encode(['status'=>200,'message'=>'player Added','field'=>'success']);
//     exit();
// }

echo json_encode($data);
exit();

?>