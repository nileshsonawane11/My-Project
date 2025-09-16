<?php
ob_start();
session_start();
error_reporting(1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../../../config.php';

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is empty
if(empty($data)){
    echo json_encode(['status'=>400,'message'=>'Empty request']);
    exit();
}

// Validate and sanitize input - FIXED: Handle different parameter names
$match_id = $data['match_id'] ?? null;
$action = $data['action'] ?? null; 
$player_id = $data['player_id'] ?? $data['point_taken_by'] ?? $data['player'] ?? null; // FIXED: Multiple fallbacks
$team = $data['team'] ?? null; 
$points = $data['points'] ?? null; 
$quarter_action = $data['quarter_action'] ?? null; 
$undo = $data['undo'] ?? false;
$total_quarters = $data['total_quarters'] ?? null;
$is_complete = $data['is_complete'] ?? false;
$Isend_quarter = $data['Isend_quarter'] ?? false; 

if(!$match_id) {
    echo json_encode(['status'=>400,'message'=>'Match ID required']);
    exit();
}

// Fetch match
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
$current_quarter = $score_log['current_quarter'];

// Check if match is already completed
if(isset($score_log['completed']) && $score_log['completed'] === true) {
    echo json_encode(['status'=>400,'message'=>'Match is already completed']);
    exit();
}

function saveHistorySnapshot($conn, $match_id, $score_log) {
    $result = $conn->query("SELECT history FROM matches WHERE match_id = '$match_id'");
    $row = $result->fetch_assoc();
    $history = json_decode($row['history'], true) ?: [];
    
    $history[] = $score_log;
    
    if (count($history) > 20) {
        array_shift($history);
    }
    
    $stmt = $conn->prepare("UPDATE matches SET history = ?, score_log = ? WHERE match_id = ?");
    $stmt->bind_param("sss", 
        json_encode($history),
        json_encode($score_log),
        $match_id
    );
    
    return $stmt->execute();
}

// Handle undo operation
if ($undo == true && $action == null) {
    $conn->begin_transaction();
    
    try {
        $result = $conn->query("SELECT history, score_log FROM matches WHERE match_id = '$match_id'");
        $row = $result->fetch_assoc();
        $history = json_decode($row['history'], true) ?: [];
        
        if (count($history) < 2) {
            throw new Exception("Not enough history to undo");
        }
        
        $previousState = $history[count($history) - 2];
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
            "status" => 500,
            "message" => $e->getMessage()
        ]);
    }
    
    exit();
}

// Handle game completion
if(isset($is_complete) && $is_complete){
    $score_log['completed'] = true;
    $score_log['quarters'][$current_quarter]['quarter_completed'] = true;
    $score_log['completed_at'] = date('Y-m-d H:i:s');
    
    if($score_log['team1_score'] > $score_log['team2_score']){
        $score_log['winner'] = $score_log['team1'];
    }else if($score_log['team2_score'] > $score_log['team1_score']){
        $score_log['winner'] = $score_log['team2'];
    }else{
        $score_log['winner'] = 'tie';
    }

    try{
        $json = json_encode($score_log);
        $status = 'Completed';
        $score_team_1 = $score_log['team1_score'];
        $score_team_2 = $score_log['team2_score'];
        
        $stmt = $conn->prepare("UPDATE matches SET score_log = ?, status = ?, score_team_1 = ?, score_team_2 = ? WHERE match_id = ?");
        $stmt->bind_param("sssss", $json, $status, $score_team_1, $score_team_2, $match_id);
        $stmt->execute();
            
        $conn->commit();
        echo json_encode(['status' => 200, 'message' => 'Game completed successfully']);
        exit();

    }catch(Exception $e){
        echo json_encode(['status' => 500, 'message' => 'Failed to update match: ' . $e->getMessage()]);
        exit();
    }
}

// Handle quarter transitions - FIXED: Handle frontend 'Isend_quarter' parameter
if((isset($quarter_action) && $quarter_action == 'next_quarter') || (isset($Isend_quarter) && $Isend_quarter == true)) {
    $total_quarters = $score_log['total_quarters'] ?? 4;
    
    // DEBUG: Log current state
    error_log("Current quarter: " . $current_quarter . ", Total quarters: " . $total_quarters);
    
    if($current_quarter < $total_quarters){
        $score_log['quarters'][$current_quarter]['quarter_completed'] = true;
        $score_log['current_quarter'] += 1;
        $new_quarter = $score_log['current_quarter'];
        
        $score_log['quarters'][$new_quarter] = [
            'team1_points' => 0,
            'team2_points' => 0,
            'quarter_completed' => false,
            'events' => []
        ];
        
        if(!saveHistorySnapshot($conn, $match_id, $score_log)) {
            echo json_encode(['status' => 500, 'message' => 'Failed to save quarter transition']);
            exit();
        }
        
        echo json_encode([
            'status' => 200, 
            'message' => 'Moved to quarter ' . $new_quarter . ' of ' . $total_quarters,
            'new_quarter' => $new_quarter,
            'total_quarters' => $total_quarters
        ]);
        exit();
        
    }else if($current_quarter == $total_quarters){
        $score_log['quarters'][$current_quarter]['quarter_completed'] = true;
        
        if($score_log['team1_score'] == $score_log['team2_score']){
            $score_log['current_quarter'] = 'OT1';
            $score_log['quarters']['OT1'] = [
                'team1_points' => 0,
                'team2_points' => 0,
                'quarter_completed' => false,
                'events' => []
            ];
            
            if(!saveHistorySnapshot($conn, $match_id, $score_log)) {
                echo json_encode(['status' => 500, 'message' => 'Failed to save overtime transition']);
                exit();
            }
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Game tied! Starting overtime'
            ]);
            exit();
        }else{
            $score_log['completed'] = true;
            $score_log['completed_at'] = date('Y-m-d H:i:s');
            
            if($score_log['team1_score'] > $score_log['team2_score']){
                $score_log['winner'] = $score_log['team1'];
            }else{
                $score_log['winner'] = $score_log['team2'];
            }

            try{
                $json = json_encode($score_log);
                $status = 'Completed';
                $score_team_1 = $score_log['team1_score'];
                $score_team_2 = $score_log['team2_score'];
                
                $stmt = $conn->prepare("UPDATE matches SET score_log = ?, status = ?, score_team_1 = ?, score_team_2 = ? WHERE match_id = ?");
                $stmt->bind_param("ssiis", $json, $status, $score_team_1, $score_team_2, $match_id);
                $stmt->execute();
                    
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Game completed successfully!',
                    'winner' => $score_log['winner'],
                    'final_score' => [
                        'team1' => $score_log['team1_score'],
                        'team2' => $score_log['team2_score']
                    ],
                    'game_completed' => true
                ]);
                exit();

            }catch(Exception $e){
                echo json_encode(['status' => 500, 'message' => 'Failed to complete game: ' . $e->getMessage()]);
                exit();
            }
        }
    }
}

// FIXED: Handle scoring with proper team detection
if($points && $player_id) {
    // Auto-detect action type based on points if not provided
    if(!$action) {
        switch($points) {
            case 1:
                $action = 'free_throw';
                break;
            case 2:
                $action = 'field_goal';
                break;
            case 3:
                $action = 'three_pointer';
                break;
            default:
                echo json_encode(['status' => 400, 'message' => 'Invalid points value']);
                exit();
        }
    }
    
    // FIXED: Proper team detection from frontend data
    if(!$team) {
        $point_taken_by_value = $data['point_taken_by'] ?? null;
        
        // Handle different possible team identifiers from frontend
        if($point_taken_by_value) {
            // If frontend sends exact team names, map them to team1/team2
            if($point_taken_by_value == $score_log['team1'] || $point_taken_by_value == 'team1') {
                $team = 'team1';
            } elseif($point_taken_by_value == $score_log['team2'] || $point_taken_by_value == 'team2') {
                $team = 'team2';
            } else {
                // If frontend sends team1/team2 directly
                if(strpos($point_taken_by_value, 'team1') !== false || $point_taken_by_value === '1') {
                    $team = 'team1';
                } elseif(strpos($point_taken_by_value, 'team2') !== false || $point_taken_by_value === '2') {
                    $team = 'team2';
                } else {
                    // Try to determine from player - check if player belongs to team1 or team2
                    // This is a fallback - you might want to create a players table for this
                    $team = 'team1'; // Default fallback
                }
            }
        } else {
            echo json_encode(['status' => 400, 'message' => 'Team information missing']);
            exit();
        }
    }
    
    $valid_points = [1, 2, 3];
    if(!in_array($points, $valid_points)){
        echo json_encode(['status' => 400, 'message' => 'Invalid points value']);
        exit();
    }
    
    if($team != 'team1' && $team != 'team2'){
        echo json_encode(['status' => 400, 'message' => 'Invalid team']);
        exit();
    }
    
    $valid_actions = ['field_goal', 'three_pointer', 'free_throw', 'technical_free_throw'];
    if(!in_array($action, $valid_actions)){
        echo json_encode(['status' => 400, 'message' => 'Invalid action type']);
        exit();
    }
    
    if($team == 'team1'){
        $score_log['team1_score'] += $points;
        $score_log['quarters'][$current_quarter]['team1_points'] += $points;
        $scoring_team = $team1;
    } else {
        $score_log['team2_score'] += $points;
        $score_log['quarters'][$current_quarter]['team2_points'] += $points;
        $scoring_team = $team2;
    }
    
    if(!isset($score_log['player_stats'][$player_id])){
        $score_log['player_stats'][$player_id] = [
            'points' => 0,
            'field_goals' => 0,
            'three_pointers' => 0,
            'free_throws' => 0,
            'technical_free_throws' => 0
        ];
    }
    
    $score_log['player_stats'][$player_id]['points'] += $points;
    $score_log['player_stats'][$player_id][$action] += 1;

    $event = [
        'timestamp' => date('Y-m-d H:i:s'),
        'quarter' => $current_quarter,
        'action' => $action,
        'player_id' => $player_id,
        'team' => $scoring_team,
        'points' => $points,
        'team1_score' => $score_log['team1_score'],
        'team2_score' => $score_log['team2_score'],
        'last score' => $score_log['team1_score'].' - '.$score_log['team2_score']
    ];
    
    $score_log['quarters'][$current_quarter]['events'][] = $event;
    $score_log['all_events'][] = $event;
    
    $match_message = "Point scored! " . $scoring_team . " +{$points} points. Score: " . 
                    $team1 . ": " . $score_log['team1_score'] . ", " . 
                    $team2 . ": " . $score_log['team2_score'];
    
    // Save to DB
    $score_team_1 = $score_log['team1_score'];
    $score_team_2 = $score_log['team2_score'];

    saveHistorySnapshot($conn, $match_id, $score_log);

    try {
        $update_stmt = $conn->prepare("UPDATE matches SET score_team_1 = ?, score_team_2 = ? WHERE match_id = ?");
        $update_stmt->bind_param("sss", $score_team_1, $score_team_2, $match_id);
        $update_stmt->execute();
        $update_stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 500,
            'message' => 'Database update error: ' . $e->getMessage()
        ]);
        exit();
    }

    // Prepare response
    $response = [
        'status' => 200,
        'message' => $match_message,
        'score_log' => $score_log,
        'current_quarter_info' => [
            'quarter' => $current_quarter,
            'team1_points' => $score_log['quarters'][$current_quarter]['team1_points'],
            'team2_points' => $score_log['quarters'][$current_quarter]['team2_points'],
            'quarter_completed' => $score_log['quarters'][$current_quarter]['quarter_completed'] ?? false,
            'events_in_quarter' => count($score_log['quarters'][$current_quarter]['events'] ?? [])
        ],
        'game_info' => [
            'team1_name' => $score_log['team1'],
            'team2_name' => $score_log['team2'],
            'team1_total_score' => $score_log['team1_score'],
            'team2_total_score' => $score_log['team2_score'],
            'game_completed' => $score_log['completed'] ?? false,
            'winner' => $score_log['winner'] ?? null,
            'total_events' => count($score_log['all_events'] ?? [])
        ],
        'latest_event' => $event,
        'current_quarter_events' => $score_log['quarters'][$current_quarter]['events'] ?? []
    ];

    $stmt->close();
    $conn->close();

    echo json_encode($response);
    exit();
    
} else {
    // FIXED: Better error message with details
    $missing = [];
    if(!$points) $missing[] = 'points';
    if(!$player_id) $missing[] = 'player_id/point_taken_by/player';
    
    echo json_encode([
        'status' => 400, 
        'message' => 'Missing required parameters: ' . implode(', ', $missing),
        'received_data' => $data // HELPFUL: Show what was actually received
    ]);
    exit();
}
?>