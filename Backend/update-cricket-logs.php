<?php
ob_start();
session_start();
error_reporting(1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../config.php';

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

if(empty($data)){
    echo json_encode(['status'=>400,'message'=>'Empty request']);
    exit();
}

// Validate and sanitize input
$match_id = $data['Match id'] ?? null;
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

// Extract all input variables with null coalescing
$Run = (int)($data['Run'] ?? 0);
$Shot_type = $data['Shot Type'] ?? null;
$Shot_side = $data['Shot Side'] ?? null;
$Wicket_Type = $data['Wicket Type'] ?? null;
$Extra = (int)($data['Extra'] ?? 0);
$Ball_Type = $data['Ball Type'] ?? null;
$Freehit = $data['Freehit'] ?? null;
$Bowler = $data['Bowler'] ?? null;
$Wicket_By = $data['Wicket By'] ?? null;
$Out_Player = $data['Out Player'] ?? null;
$New_Player = $data['New Player'] ?? null;
$Striker = $data['Striker'] ?? null;
$TotalScore = (int)($data['TotalScore'] ?? 0);
$Wickets = (int)($data['Wickets'] ?? 0);
$Inning = $data['Inning'] ?? null;
$Inning_type = $data['Inning Type'] ?? null;
$Commentary = $data['Commentary'] ?? null;
$issuper_over = $data['super over'] ?? false;

if($issuper_over == true && $Ball_Type == null){
    $score_log['super_over'] = true;
    echo json_encode(['status'=>200,'message'=>'Super Over added to score log']);
    exit();
}

// Add current ball to log
$score_log[$Inning_type][$Inning]['balls'][] = $data;
$score_log[$Inning_type][$Inning]['total_runs'] += ($Run + $Extra);

// Update batsman stats - FIXED VERSION
if(!str_starts_with($Ball_Type, 'Wide')) {  // Always exclude wides completely
    
    // For No Balls: Count runs but not balls faced
    if(str_starts_with($Ball_Type, 'No')) {
        $score_log[$Inning_type][$Inning]['openers']['current_striker']['runs'] += $Run;
        
        if ($Run == 4) { 
            $score_log[$Inning_type][$Inning]['openers']['current_striker']['fours']++;
        } elseif ($Run == 6) {
            $score_log[$Inning_type][$Inning]['openers']['current_striker']['sixes']++;
        }
    } 
    // For all other valid deliveries
    else {
        $score_log[$Inning_type][$Inning]['openers']['current_striker']['balls_faced']++;
        $score_log[$Inning_type][$Inning]['openers']['current_striker']['runs'] += $Run;
        
        if ($Run == 4) { 
            $score_log[$Inning_type][$Inning]['openers']['current_striker']['fours']++;
        } elseif ($Run == 6) {
            $score_log[$Inning_type][$Inning]['openers']['current_striker']['sixes']++;
        }
    }
}

function checksuperover(&$score_log, $Inning_type, $Inning){
    if($score_log[$Inning_type]['1st']['total_runs'] == $score_log[$Inning_type]['2nd']['total_runs']){
        echo json_encode(['status'=>200,'message'=>'Super Over','is_super_over'=>true]);
        exit();
    }
}

function swap_batsmans(&$score_log, $Inning_type, $Inning) {
    $temp = $score_log[$Inning_type][$Inning]['openers']['current_striker'];
    $score_log[$Inning_type][$Inning]['openers']['current_striker'] = 
        $score_log[$Inning_type][$Inning]['openers']['current_non_striker'];
    $score_log[$Inning_type][$Inning]['openers']['current_non_striker'] = $temp;
}

// Batsman rotation logic
if((str_starts_with($Wicket_Type, 'Run') || str_starts_with($Wicket_Type, 'Obstructing')) && 
   strpos($Wicket_Type, '(Mankaded)') === false) {
    if(($Run % 2) == 0) {
        swap_batsmans($score_log, $Inning_type, $Inning);
    }
} elseif (!in_array($Ball_Type, ['Wide', 'No Ball'], true) && ($Run % 2) == 1) {
    swap_batsmans($score_log, $Inning_type, $Inning);
}

if(in_array($Ball_Type, ['Bye', 'Leg Bye'], true) && ($Extra % 2) == 1) {
    swap_batsmans($score_log, $Inning_type, $Inning);
}

if(in_array($Ball_Type, ['No Ball-Bye', 'No Ball-Leg Bye'], true) && ($Extra % 2) == 0) {
    swap_batsmans($score_log, $Inning_type, $Inning);
}

// Wicket processing
$process_wicket = true;
if ($Ball_Type === 'Free Hit') {
    $allowedOnFreeHit = [
        'Run Out (Striker)', 
        'Run Out (Non-Striker)',
        'Obstructing the Field (Striker)',
        'Obstructing the Field (Non-Striker)',
        'Run out (Mankaded)',
        'Retired Out(Non-Striker)',
        'Retired Out(Striker)',
        'Hit the Ball Twice'
    ];
    
    if (!in_array($Wicket_Type, $allowedOnFreeHit)) {
        $process_wicket = false;
    }
}

if (($Ball_Type === 'Wicket' || $Ball_Type === 'No Ball-Wicket' || 
    ($Ball_Type === 'Free Hit' && $process_wicket)) && $Wicket_Type) {
    
    $score_log[$Inning_type][$Inning]['wickets']++;
    if($score_log[$Inning_type][$Inning]['wickets'] >= 10 && $score_log[$Inning_type] == 'innings'){
        $score_log[$Inning_type][$Inning]['completed'] = true;
        checksuperover();
    }else if($score_log[$Inning_type][$Inning]['wickets'] >= 2 && $score_log[$Inning_type] == 'Super_Over_logs'){
        $score_log[$Inning_type][$Inning]['completed'] = true;
        checksuperover();
    }
    
    $out_player = null;
    $is_non_striker = (strpos($Wicket_Type, '(Non-Striker)') !== false || 
                      strpos($Wicket_Type, '(Mankaded)') !== false ||
                        strpos($Wicket_Type, 'Retired Out(Non-Striker)') !== false);
    
    $player_key = $is_non_striker ? 'current_non_striker' : 'current_striker';
    
    $out_player = $score_log[$Inning_type][$Inning]['openers'][$player_key];
    $out_player['out_status'] = "out";
    
    $score_log[$Inning_type][$Inning]['openers'][$player_key] = [
        "id"=> null,
        "balls_faced"=> 0,
        "style"=> null,
        "runs"=> 0,
        "fours"=> 0,
        "sixes"=> 0,
        "out_status"=> "not out"
    ];
    
    $score_log[$Inning_type][$Inning]['batmans'][] = $out_player;
}

// Over progression logic - FIXED VERSION
$validDeliveryTypes = ['Legal Delivery', 'Bye', 'Leg Bye', 'Wicket', 'Free Hit'];
if(in_array($Ball_Type, $validDeliveryTypes)) {
    list($over, $ball) = explode('.', $score_log[$Inning_type][$Inning]['current_bowler']['overs_bowled']);
    $ball = (int)$ball;
    $over = (int)$over;
    
    // Only increment ball count for valid deliveries (not wides/no balls)
    if(!in_array($Ball_Type, ['Wide', 'No Ball'], true)) {
        $ball++;
    }
    
    // Handle over completion
    if ($ball >= 6) {
        $over++;
        $ball = 0;
        swap_batsmans($score_log, $Inning_type, $Inning);
        
        // Reset free hit status at over change
        foreach($score_log[$Inning_type][$Inning]['balls'] as &$pastBall) {
            if(isset($pastBall['Freehit'])) {
                $pastBall['Freehit'] = false;
            }
        }
        
        // Rotate bowler
        $score_log[$Inning_type][$Inning]['bowlers'][] = $score_log[$Inning_type][$Inning]['current_bowler'];
        $score_log[$Inning_type][$Inning]['current_bowler'] = [
            "id"=> null,
            "style"=> null,
            "overs_bowled"=> "0.0",
            "runs_conceded"=> 0,
            "wickets"=> 0
        ];
    }
    
    // Update bowler stats
    $score_log[$Inning_type][$Inning]['current_bowler']['overs_bowled'] = "$over.$ball";
    $score_log[$Inning_type][$Inning]['overs_completed'] = "$over.$ball";

    if($score_log[$Inning_type][$Inning]['overs_completed'] >= $score_log['overs']){
        $score_log[$Inning_type][$Inning]['completed'] = true;
        checksuperover();
    }
    
    // Special case: Free hit should not carry over to next over
    if($ball === 0 && isset($data['Freehit'])) {
        $data['Freehit'] = false;
    }
}

// Update match record with prepared statement
$json = json_encode($score_log);
$stmt = $conn->prepare("UPDATE matches SET score_log = ? WHERE match_id = ?");
$stmt->bind_param("ss", $json, $match_id);

if($stmt->execute()){
    echo json_encode(['status'=>200,'message'=>'Data inserted successfully']);
} else {
    echo json_encode(['status'=>500,'message'=>'Failed to update match']);
}

exit();
?>