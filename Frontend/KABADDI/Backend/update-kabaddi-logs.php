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
$current_raid_team = $data['current_raid_team'] ?? null;
$raider            = $data['raider'] ?? null;
$undo              = $data['undo'] ?? false;
$match_id          = $data['match_id'] ?? null;
$end_half          = $data['Isend_half'] ?? false;
$is_complete       = $data['is_complete'] ?? false;
$raid_tech_point   = $data['raid_tech_point'] ?? null;
$def_tech_point   = $data['def_tech_point'] ?? null;
$raid_points   = $data['raid_points'] ?? null;
$def_points   = $data['def_points'] ?? null;
$is_start          = $data['is_start'] ?? false;
$last_score        = null;
$points = (int)$points;


// echo json_encode($data);
// exit();
if(!$match_id) {
    echo json_encode(['status'=>400,'message'=>'Match ID required']);
    exit();
}

// Fetch match using prepared statement
$stmt = $conn->prepare("SELECT score_log FROM matches WHERE match_id = ?");
$stmt->bind_param("s", $match_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the row first
$row = $result->fetch_assoc();

// Then decode the JSON score log
$score_log = json_decode($row['score_log'], true);



if($result->num_rows === 0){
    echo json_encode(['status'=>404,'message'=>'Match not found']);
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

if (($raider == null && $undo == true)) {
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
    $score_log['match_completed'] = true;
    if($score_log['team1_score'] > $score_log['team2_score']){
        $score_log['winner'] = $score_log['team1'];
    }else{
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
}else if(isset($is_start) && $is_start){}

if($end_half == true){
    if ($score_log["current_half"] == 1) {
        $score_log["halves"][$score_log["current_half"]]['half_completed'] = true;
        $score_log["current_half"] += 1;
    }else if($score_log["current_half"] == 2){
        if($score_log['team1_score'] == $score_log['team2_score']){
            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_tie']);
            exit();
        }else{
            echo json_encode(['status' => 200, 'message' => 'Match Completed','field'=>'is_complete']);
            exit();
        }
    }
}else{
    update_score_log($conn, $raid_points, $def_points, $def_tech_point, $raid_tech_point, $current_raid_team, $raider, $undo, $match_id, $score_log);
}

function update_score_log($conn, $raid_points, $def_points, $def_tech_point, $raid_tech_point, $current_raid_team, $raider, $undo, $match_id, &$score_log) {

        if($score_log['current_raid_team'] == $score_log['team1']){
            $score_log['current_raid_team'] = $score_log['team2'];
        }else{
            $score_log['current_raid_team'] = $score_log['team1'];
        }

    $current_half = $score_log['current_half'];
    $total_raid_points = $raid_points + $raid_tech_point; 
    $total_def_points = $def_points + $def_tech_point;
    
    if ($current_raid_team == $score_log['team1']) {

        if ($total_raid_points > 0) {
            $score_log['team1_score'] += $total_raid_points;
            $score_log['halves'][$current_half]['team1_points'] += $total_raid_points;
        }

        if ($total_def_points > 0) {
            $score_log['team2_score'] += $total_def_points;
            $score_log['halves'][$current_half]['team2_points'] += $total_def_points;
        }

    } else if ($current_raid_team == $score_log['team2']) {

        if ($total_raid_points > 0) {
            $score_log['team2_score'] += $total_raid_points;
            $score_log['halves'][$current_half]['team2_points'] += $total_raid_points;
        }

        if ($total_def_points > 0) {
            $score_log['team1_score'] += $total_def_points;
            $score_log['halves'][$current_half]['team1_points'] += $total_def_points;
        }
    }


    $last_score = $score_log['team1_score'].'-'.$score_log['team2_score'];

    // if ($tech_point != null && !empty($score_log['halves'][$current_half]['raids'])) {
    //     $last_raid = end($score_log['halves'][$current_half]['raids']);
    //     $point_taken_by = $last_raid['point_taken_by'];
    // }

    $raid_log = [
        "current_raid_team" => $current_raid_team,
        "raider"            => $raider,
        "current Half"      => $current_half,
        "last score"        => $last_score,
        "raid points"       => $total_raid_points,
        "def points"       => $total_def_points
    ];

    $score_log['halves'][$current_half]['raids'][] = $raid_log;


}
    $team1_points = $score_log['team1_score'];
    $team2_points = $score_log['team2_score'];
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


?>