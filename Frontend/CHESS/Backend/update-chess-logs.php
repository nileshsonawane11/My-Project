<?php
session_start();
include '../../../config.php';

// Read JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['status' => 400, 'message' => 'Invalid JSON']);
    exit();
}

$match_id = $data['match_id'] ?? null;
$team     = $data['team'] ?? null;

// Fetch match using prepared statement
$stmt = $conn->prepare("SELECT * FROM matches WHERE match_id = ?");
$stmt->bind_param("s", $match_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo json_encode(['status'=>404,'message'=>'Match not found']);
    exit();
}

$row = $result->fetch_assoc();
$score_log = json_decode($row['score_log'], true);

if(is_array($team)){

    $score_log['winner'] = 'Draw';
    $score_log['result'] = '1/2 - 1/2';

    $team1_score = '0.5';
    $team2_score = '0.5';

}else{

    $score_log['winner'] = $team;
    $score_log['result'] = $team == $score_log['white'] ? '1 - 0' : '0 - 1';

    $team1_score = $team == $row['team_1'] ? '1' : '0';
    $team2_score = $team == $row['team_2'] ? '1' : '0';

}

$score_log['completed'] = true;
$score_log['match_completed'] = true;
$updated_json = json_encode($score_log);

try {

    $status = 'Completed';
    $update_stmt = $conn->prepare("UPDATE matches SET score_team_1 = ?, score_team_2 = ?,`status` = ?,score_log = ? WHERE match_id = ?");
    $update_stmt->bind_param("sssss", $team1_score, $team2_score,$status,$updated_json, $match_id);

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

// Close database connections
$stmt->close();
//$update_stmt->close();
$conn->close();

// Return response
echo json_encode(['status'=>200,'message'=>'Data Updated Successfully']);
exit();
?>