<?php
ob_start();
session_start();
error_reporting(1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../../../config.php';

/*ob_start();
session_start();
error_reporting(1);
ini_set('display_errors', 0);
ini_set('html_errors', 0);
header('Content-Type: application/json');
include '../../../config.php';*/
// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is empty
if(empty($data)){
    echo json_encode(['status'=>400,'message'=>'Empty request']);
    exit();
}

// Validate and sanitize input
$match_id = $data['match_id'] ?? null;
$action = $data['serve_action'] ?? null;
$serve_player = $data['serve_player'] ?? null;
$winner_team = $data['winner_team'] ?? null;
$current_serve = $score_log['current_serve'];
$team1 = $score_log['team1'];
$team2 = $score_log['team2'];

$opponent_team = ($current_serve === $team1) ? $team2 : $team1;

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

// Extract current match data
$team1 = $score_log['team1'];
$team2 = $score_log['team2'];
$current_set = $score_log['current_set'];
$points_required = $score_log['sets'][$current_set]['points_required'];
$total_sets = $score_log['total_sets'];
$exit = $data['end_set'] ?? false;  // Will be null if not sent
$undo = $data['undo'] ?? false;  // Will be null if not sent
$is_complete = $data['is_complete'] ?? false;

// Check if match is already completed
if(isset($score_log['completed']) && $score_log['completed'] === true) {
    echo json_encode(['status'=>400,'message'=>'Match is already completed']);
    exit();
}

function saveHistorySnapshot($conn, $match_id, $score_log) {
    // Fetch and update history
    $result = $conn->query("SELECT history FROM matches WHERE match_id = '$match_id'");
    $row = $result->fetch_assoc();
    $history = json_decode($row['history'], true) ?: [];
    
    $history[] = $score_log;
    
    if (count($history) > 15) {
        array_shift($history);
    }
    
    // Update both history and overs
    $stmt = $conn->prepare("UPDATE matches SET history = ?, score_log = ? WHERE match_id = ?");
    $stmt->bind_param("sss", 
        json_encode($history),
        json_encode($score_log),
        $match_id
    );
    
    return $stmt->execute();
}

if (($winner_team == null && $undo == true)) {
    global $match_id;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Fetch complete state
        $result = $conn->query("SELECT history, score_log FROM matches WHERE match_id = '$match_id'");
        $row = $result->fetch_assoc();
        $history = json_decode($row['history'], true) ?: [];
        
        if (count($history) < 2) {
            throw new Exception("Not enough history to undo");
        }
        
        // Get previous state
        $previousState = $history[count($history) - 2];
        
        // Update database
        $newHistory = array_slice($history, 0, -1);
        $stmt = $conn->prepare("UPDATE matches SET history = ?, score_log = ? WHERE match_id = ?");
        $stmt->bind_param("sss", 
            json_encode($newHistory),
            json_encode($previousState),
            $match_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update match state");
        }
        
        $conn->commit();
        echo json_encode([
            "status" => 200,
            "message" => "Undo successful"
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            "status" => 200,
            "message" => $e->getMessage(),
            'field' => 'empty'
        ]);
    }
    
    exit();
}

if(isset($is_complete) && $is_complete){
    $score_log['completed'] = true;
    $score_log['sets'][$current_set]['set_completed'] = true;
    if($score_log['team1_score'] > $score_log['team2_score']){
        $score_log['winner'] = $score_log['team1'];
    }else{
        $score_log['winner'] = $score_log['team2'];
    }

    try{
        $json = json_encode($score_log);
        $status = 'Completed';
        $sets_team1 = isset($score_log['sets_won']['team1']) ? $score_log['sets_won']['team1'] : 0;
        $sets_team2 = isset($score_log['sets_won']['team2']) ? $score_log['sets_won']['team2'] : 0;

        $score_team_1 = $score_log['sets'][$current_set]['team1_points'].' ('.$sets_team1.')';
        $score_team_2 = $score_log['sets'][$current_set]['team2_points'].' ('.$sets_team2.')';
        $stmt = $conn->prepare("UPDATE matches SET score_log = ?, status = ?, score_team_1 = ?, score_team_2 = ? WHERE match_id = ?");
        $stmt->bind_param("sssss", $json, $status, $score_team_1, $score_team_2, $match_id);
        $stmt->execute();
            
        $conn->commit();

        header('Content-Type: application/json');
        echo json_encode(['status' => 200, 'message' => 'Data updated successfully']);
        exit();

    }catch(Exception $e){
        header('Content-Type: application/json');
        echo json_encode(['status' => 500, 'message' => 'Failed to update match: ' . $e->getMessage()]);
        exit();
    }
}


if(isset($exit) && $exit){
    $team1_points = $score_log['sets'][$current_set]['team1_points'];
    $team2_points = $score_log['sets'][$current_set]['team2_points'];

    $winner = ($team1_points > $team2_points) ? $team1 : $team2;

    if($score_log[$current_set] == $score_log['total_innings']){
        if($team1_points == $team2_points){
            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_tie']);
            exit();
        }else{
            if($winner == $team1){
                $score_log['sets_won']['team1'] += 1;
                $score_log['sets'][$current_set]['set_completed'] = true;
            }else{
                $score_log['sets_won']['team2'] += 1;
                $score_log['sets'][$current_set]['set_completed'] = true;
            }

            saveHistorySnapshot($conn, $match_id, $score_log);

            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_complete']);
            exit();
        }

    }else{
        if($team1_points == $team2_points){
            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_tie']);
            exit();
        }else{
            if($winner == $team1){
                $score_log['sets_won']['team1'] += 1;
                $score_log['sets'][$current_set]['set_completed'] = true;
            }else{
                $score_log['sets_won']['team2'] += 1;
                $score_log['sets'][$current_set]['set_completed'] = true;
            }

            saveHistorySnapshot($conn, $match_id, $score_log);

            echo json_encode(['status' => 200, 'message' => 'Set updated successfully']);
            exit();
        }
    }
}

// Check which team won the point and increase score by 1
if ($winner_team == $team1) {
    $score_log['sets'][$current_set]['team1_points'] += 1;
    $score_log['team1_score'] += 1;
    $scoring_team = $team1;
} elseif ($winner_team == $team2) {
    $score_log['sets'][$current_set]['team2_points'] += 1;
    $score_log['team2_score'] += 1;
    $scoring_team = $team2;
} else {
    echo json_encode(['status' => 400, 'message' => 'Winner team does not match any team in match']);
    exit();
}

// Get updated scores for current set
$team1_points = $score_log['sets'][$current_set]['team1_points'];
$team2_points = $score_log['sets'][$current_set]['team2_points'];

// Store rally data 
$score_log['sets'][$current_set]['rallies'][] = [
    'team1_points' => $team1_points,
    'team2_points' => $team2_points,
    'action' => $action,
    'serve_player' => $serve_player,
    'winner_team' => $scoring_team,
    'last score' => $team1_points.' - '.$team2_points,
    'Curr_serve' => $score_log['current_serve']
];

$score_log['current_serve'] = $scoring_team;

// Check if any team meets required points
$team1_meets_points = $team1_points >= $points_required;
$team2_meets_points = $team2_points >= $points_required;

if ($team1_meets_points || $team2_meets_points) {
    // Check if winning team has 2 or more points lead
    $score_difference = abs($team1_points - $team2_points);
    
    if ($score_difference >= 2) {
        // Set is completed
        $score_log['sets'][$current_set]['set_completed'] = true;
        
        // Determine set winner
        if ($team1_points > $team2_points) {
            $score_log['sets_won']['team1'] += 1;
            $score_log['sets'][$current_set]['winner_team'] = $team1;
            $set_winner = $team1;
            $score_log['current_serve'] = $team2;

        } else {
            $score_log['sets_won']['team2'] += 1;
            $score_log['sets'][$current_set]['winner_team'] = $team2;
            $set_winner = $team2;
            $score_log['current_serve'] = $team1;

        }
        
        // Check if match is complete
        $sets_to_win = ceil($total_sets / 2);
        $team1_sets_won = $score_log['sets_won']['team1'];
        $team2_sets_won = $score_log['sets_won']['team2'];
        
        if (($team1_sets_won >= $sets_to_win || $team2_sets_won >= $sets_to_win)) {
            // Match completed
            // $score_log['completed'] = true;
            // $score_log['sets'][$current_set]['set_completed'] = true;
            // $score_log['winner_team'] = ($team1_sets_won > $team2_sets_won) ? $team1 : $team2;
            // $score_log['completed_at'] = date('Y-m-d H:i:s');
            
            // $match_complete_message = "MATCH COMPLETE!  Winner: " . $score_log['winner_team'];

            saveHistorySnapshot($conn, $match_id, $score_log);

            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_complete']);
            exit();

        } else {
            // Move to next set
            $score_log['sets'][$current_set]['set_completed'] = true;
            $score_log['current_set'] += 1;
            
            $match_complete_message = "Set " . ($current_set) . " completed! Winner: " . $set_winner . ". Moving to Set " . $next_set;
        }
    } else {
        // Extend set by 1 point
        $max_score = max($team1_points, $team2_points);
        $score_log['points_required'] = $max_score + 1;
        
        $match_complete_message = "Set extended! New required points: " . $score_log['points_required'];
    }
} else {
    $match_complete_message = "Point awarded to " . $scoring_team . ". Current Score - " . $team1 . ": " . $team1_points . ", " . $team2 . ": " . $team2_points;
}

$sets_team1 = isset($score_log['sets_won']['team1']) ? $score_log['sets_won']['team1'] : 0;
$sets_team2 = isset($score_log['sets_won']['team2']) ? $score_log['sets_won']['team2'] : 0;

$score_team_1 = $score_log['sets'][$current_set]['team1_points'].' ('.$sets_team1.')';
$score_team_2 = $score_log['sets'][$current_set]['team2_points'].' ('.$sets_team2.')';

// Save updated score_log back to database
saveHistorySnapshot($conn, $match_id, $score_log);
$updated_json = json_encode($score_log);

try {


    $update_stmt = $conn->prepare("UPDATE matches SET score_team_1 = ?, score_team_2 = ? WHERE match_id = ?");
    $update_stmt->bind_param("sss", $score_team_1, $score_team_2, $match_id);

    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update match data");
    }

    $update_stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 500,
        'message' => 'Database update error: ' . $e->getMessage()
    ]);
    exit();
}


// Prepare response with detailed rally information
$response = [
    'status' => 200,
    'message' => $match_complete_message,
    'score_log' => $score_log,
    'current_set_info' => [
        'set_number' => $current_set,
        'team1_points' => $team1_points,
        'team2_points' => $team2_points,
        'points_required' => $score_log['points_required'],
        'set_completed' => $score_log['sets'][$current_set]['set_completed'] ?? false,
        //'total_rallies_in_set' => count($score_log['sets'][$current_set]['rallies'])
        'total_rallies_in_set' => count($score_log['sets'][$current_set]['rallies'] ?? [])

    ],
    'match_info' => [
        //'sets_won_team1' => $score_l og['sets_won']['team1'],
        'sets_won_team1' => $score_log['sets_won']['team1'],

        'sets_won_team2' => $score_log['sets_won']['team2'],
        'match_completed' => $score_log['completed'] ?? false,
        'winner_team' => $score_log['winner_team'] ?? null,
        'total_rallies' => count($score_log['all_rallies'] ?? [])
    ],
    'latest_rally' => [
        'team1_points' => $team1_points,
        'team2_points' => $team2_points,
        'action' => $action,
        'serve_player' => $serve_player,
        'winner_team' => $scoring_team
    ],
    'current_set_rallies' => $score_log['sets'][$current_set]['rallies']
];

// Close database connections
$stmt->close();
//$update_stmt->close();
$conn->close();

// Return response
echo json_encode($response);
exit();
?>