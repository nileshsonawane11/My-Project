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

// Now you can access values like this:
$match_id = $data['match_id'] ?? null;
$current_inning = $data['current_inning'] ?? null;
$point_taken_by = $data['point_taken_by'] ?? null;
$chaser_player = $data['chaser_player'] ?? null;
$out_player = $data['out_player'] ?? null;
$exit = $data['exit'] ?? null;  // Will be null if not sent
$undo = $data['undo'] ?? null;  // Will be null if not sent
$is_complete = $data['complete'];
$type = null;

$result = mysqli_query($conn, "SELECT * FROM `matches` WHERE `match_id` = '$match_id'");
$match = mysqli_fetch_assoc($result);
$score_log = json_decode($match['score_log'],true);

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

if (($undo == true)) {
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
            "message" => "Undo successful",
            "overs" => $overs
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
    if($score_log['team1_score'] > $score_log['team2_score']){
        $score_log['winner'] = $score_log['team1'];
    }else if($score_log['team1_score'] < $score_log['team2_score']){
        $score_log['winner'] = $score_log['team2'];
    }

    try{
        $score_team1 = $score_log['team1_score'];
        $score_team2 = $score_log['team2_score'];
        $json = json_encode($score_log);
        $status = 'Completed';

        $stmt = $conn->prepare("UPDATE matches SET score_log = ?, status = ?, score_team_1 = ?, score_team_2 = ? WHERE match_id = ?");
        $stmt->bind_param("sssss", $json, $status, $score_team1, $score_team2, $match_id);
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

if($exit == true){
    if ($score_log['current_inning'] < $score_log['total_innings']) {
        $score_log['current_inning'] += 1;
    }else if($score_log['current_inning'] == $score_log['total_innings']){
        if($score_log['team1_score'] == $score_log['team2_score']){
            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_tie']);
            exit();
        }else{
            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_complete']);
            exit();
        }
    }
}else{
    
    if($score_log['team1'] == $point_taken_by){
        $score_log['innings'][$current_inning]['team1_points'] += 1;
        $score_log['team1_score'] += 1;
    }else if($score_log['team2'] == $point_taken_by){
        $score_log['innings'][$current_inning]['team2_points'] += 1;
        $score_log['team2_score'] += 1;
    }

    if($out_player != null){
    // Reference to runners
        $current_runners = &$score_log['innings'][$current_inning]['current_runners'];

        // Find player in current_runners
        $key = array_search($out_player, $current_runners);

        if ($key !== false) {
            // Remove from current_runners without reindexing
            unset($current_runners[$key]);

            // Add to out_runners
            $score_log['innings'][$current_inning]['out_runners'][] = $out_player;
        }
    }else{
        $type = 'Technical';
    }

    if (isset($score_log['innings'][$current_inning]['out_runners']) &&
        count($score_log['innings'][$current_inning]['out_runners']) >= 9) {
        
        // Reset out_runners
        $score_log['innings'][$current_inning]['out_runners'] = [];
    }
}

$team1_points = $score_log['team1_score'];
$team2_points = $score_log['team2_score'];
$last_score = $score_log['team1_score'].'-'.$score_log['team2_score'];

$score_log['innings'][$current_inning]['tags'][] = [
    'type' => $type,
    'chaser_player' => $chaser_player,
    'point_taken_by' => $point_taken_by,
    'out_player' => $out_player,
    'last score' => $last_score,
    'team1_points' => $team1_points,
    'team2_points' => $team2_points,
    'current_inning' => $score_log['current_inning']
];

saveHistorySnapshot($conn, $match_id, $score_log);
try{
    // Update team score
    $stmt = $conn->prepare("UPDATE matches SET score_team_1 = ?, score_team_2 = ? WHERE match_id = ?");
    $stmt->bind_param("sss", $team1_points,$team2_points, $match_id);
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

echo json_encode($data);
exit();

?>