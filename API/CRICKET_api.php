<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);
header('Content-Type: application/json');
include("../config.php");

$match_id = $_GET['match_id'] ?? '';
$current_innings = $_GET['current_innings'] ?? '';

// Fetch the row first
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));

// Decode the JSON string from the 'score_log' column
$score_log = json_decode($row['score_log'], true); // `true` to get an array

$score_log['team1_full_score'] = $row['score_team_1'];
$score_log['team2_full_score'] = $row['score_team_2'];
$allBalls = $score_log['innings'][$current_innings]['balls'];

// 2. Get all unique player IDs involved in the innings
$playerIds = [];
$bowlerIds = [];
$fielderIds = [];
$players = [];

foreach ($allBalls as $ball) {
    if (!empty($ball['Striker'])) {
        $playerIds[] = $ball['Striker'];
    }
    if (!empty($ball['Bowler'])) {
        $bowlerIds[] = $ball['Bowler'];
    }
    if (!empty($ball['Wicket By'])) {
        $parts = explode(',', $ball['Wicket By']);
        $fielderIds[] = trim($parts[0]);
    }
}

// Remove duplicates
$playerIds = array_unique($playerIds);
$bowlerIds = array_unique($bowlerIds);
$fielderIds = array_unique(array_filter($fielderIds));

// 3. Create player maps
function createPlayerMap($conn, $ids) {
    if (empty($ids)) return [];
    
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    
    $query = "SELECT user_id, player_name FROM players WHERE user_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['user_id']] = $row['player_name'];
    }
    return $map;
}

$player_map = createPlayerMap($conn, $playerIds);
$bowler_map = createPlayerMap($conn, $bowlerIds);
$fielder_map = createPlayerMap($conn, $fielderIds);

$score_log['player_map'] = $player_map;
$score_log['bowler_map'] = $bowler_map;
$score_log['fielder_map'] = $fielder_map;

function get_match_players($conn, $match_id) {
    // Step 1: Get team IDs from matches table
    $query_match = "SELECT team_1, team_2 FROM matches WHERE match_id = ?";
    $stmt = $conn->prepare($query_match);
    $stmt->bind_param("s", $match_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
        return []; // no match found
    }

    $team1_id = $row['team_1'];
    $team2_id = $row['team_2'];

    // Step 2: Fetch players from both teams
    $query_players = "SELECT user_id, player_name FROM players WHERE team_id IN (?, ?)";
    $stmt2 = $conn->prepare($query_players);
    $stmt2->bind_param("ss", $team1_id, $team2_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    $players = [];
    while ($row2 = $res2->fetch_assoc()) {
        $players[$row2['user_id']] = $row2['player_name'];
    }

    return $players;
}

// Usage
$players = get_match_players($conn, $match_id);
$score_log['Players_map'] = $players;
$score_log['current_innings']=$current_innings;
// Return as JSON
echo json_encode($score_log);

?>