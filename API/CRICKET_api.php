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

// detect current inning
$current_innings = null;
$bat_team = null;
$bowl_team = null;
$bat_team_name = null;
$bowl_team_name = null;
$inning_type = null; // new variable

if (isset($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])) {
    foreach ($score_log['super_over_innings'] as $innings_name => $innings_data) {
        if (is_array($innings_data) && 
            isset($innings_data['completed']) && 
            $innings_data['completed'] == false) {
            $current_innings = $innings_name;
            $bat_team = $innings_data['batting_team'] ?? null;
            $bowl_team = $innings_data['bowling_team'] ?? null;
            $inning_type = 'super_over_innings';
            break;
        }
    }
}

// 2. If no active super over innings, check regular innings
if ($current_innings === null && isset($score_log['innings']) && is_array($score_log['innings'])) {
    foreach ($score_log['innings'] as $innings_name => $innings_data) {
        if (is_array($innings_data) && 
            isset($innings_data['completed']) && 
            $innings_data['completed'] == false) {
            $current_innings = $innings_name;
            $bat_team = $innings_data['batting_team'] ?? null;
            $bowl_team = $innings_data['bowling_team'] ?? null;
            $inning_type = 'innings';
            break;
        }
    }
}

// 3. If no active innings found but match isn't completed, use last inning
if ($current_innings === null && (!isset($score_log['match_completed']) || $score_log['match_completed'] != true)) {
    // Try super over innings first
    if (isset($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])) {
        $last_super_over = end($score_log['super_over_innings']);
        if (is_array($last_super_over)) {
            $current_innings = key($score_log['super_over_innings']);
            $is_super_over = true;
            $bat_team = $last_super_over['batting_team'] ?? null;
            $bowl_team = $last_super_over['bowling_team'] ?? null;
            $inning_type = 'super_over_innings';
        }
    }
    
    // If no super over, use last regular inning
    if ($current_innings === null && isset($score_log['innings']) && is_array($score_log['innings'])) {
        $last_regular_inning = end($score_log['innings']);
        if (is_array($last_regular_inning)) {
            $current_innings = key($score_log['innings']);
            $bat_team = $last_regular_inning['batting_team'] ?? null;
            $bowl_team = $last_regular_inning['bowling_team'] ?? null;
            $inning_type = 'innings';
        }
    }
}

// ✅ Fetch team names from DB if IDs exist
if ($bat_team !== null || $bowl_team !== null) {
    $team_ids = array_filter([$bat_team, $bowl_team]); // remove nulls
    if (!empty($team_ids)) {
        $ids_str = implode(",", array_map('intval', $team_ids));
        $query = "SELECT t_id, t_name FROM teams WHERE t_id IN ($ids_str)";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['t_id'] == $bat_team) {
                    $bat_team_name = $row['t_name'];
                }
                if ($row['t_id'] == $bowl_team) {
                    $bowl_team_name = $row['t_name'];
                }
            }
        }
    }
}

// Usage
$players = get_match_players($conn, $match_id);
$score_log['Players_map'] = $players;
$score_log['inning_type'] = $inning_type;
$score_log['bat_team'] = $bat_team_name;
$score_log['bowl_team'] = $bowl_team_name;
$score_log['bat'] = $bat_team;
$score_log['bowl'] = $bowl_team;
$score_log['current_innings']=$current_innings;
// Return as JSON
echo json_encode($score_log);

?>