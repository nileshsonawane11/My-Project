<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);
header('Content-Type: application/json');
session_start();
include("../config.php");

$match_id = $_GET['match_id'] ?? '';

$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));

if (!$row) {
    echo json_encode(["error" => "Match not found"]);
    exit();
}

$score_log = json_decode($row['score_log'], true);

// Function to get team_id → team_name map
function createTeamMap($conn, $team_ids) {
    if (empty($team_ids)) return [];

    $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
    $types = str_repeat('s', count($team_ids));

    $stmt = $conn->prepare("SELECT t_id, t_name FROM teams WHERE t_id IN ($placeholders)");
    if (!$stmt) return [];

    $stmt->bind_param($types, ...$team_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['t_id']] = $row['t_name'];
    }
    return $map;
}

// Function to get player_id → player_name for given team_ids
function createPlayerMap($conn, $team_ids) {
    if (empty($team_ids)) return [];

    $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
    $types = str_repeat('s', count($team_ids));

    $stmt = $conn->prepare("SELECT user_id, player_name FROM players WHERE team_id IN ($placeholders)");
    if (!$stmt) return [];

    $stmt->bind_param($types, ...$team_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['user_id']] = $row['player_name'];
    }
    return $map;
}

// Extract team IDs from score log
$team_ids = [];
if (isset($score_log['team1'])) $team_ids[] = $score_log['team1'];
if (isset($score_log['team2'])) $team_ids[] = $score_log['team2'];

// Create team and player maps
$score_log['team_map'] = createTeamMap($conn, $team_ids);
$score_log['player_map'] = createPlayerMap($conn, $team_ids);
$score_log['sessionEmail'] = $_SESSION['email'] ?? '';

// Output updated score log
echo json_encode($score_log);
exit();
?>
