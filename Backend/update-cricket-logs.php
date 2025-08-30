<?php
ob_start();
session_start();
error_reporting(1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../config.php';
define('MAX_REGULAR_WICKETS', 10);
define('MAX_SUPER_OVER_WICKETS', 2);
define('MAX_UNDO_HISTORY',10);
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

// Check if match is already completed
if(isset($score_log['match_completed']) && $score_log['match_completed'] == true){
    echo json_encode(['status'=>400,'message'=>'Match already completed']);
    exit();
}

// Initialize undo history if not exists
if(!isset($score_log['undo_history'])) {
    $score_log['undo_history'] = [];
}

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
$iscomplete =$data['Is Match Complete'] ?? null;
$undo = $data['Undo'] ?? null;
$run_type = $data['Run Type'] ?? null;

// Then in your updateBall function, modify the completion handling:
if (isset($iscomplete) && $iscomplete == true) {
    // User confirmed they want to end the match
    $score_log['match_completed'] = true;
    $score_log[$Inning_type]['2nd']['completed'] = true;
    
    // Update database
    $json = json_encode($score_log);
    $stmt = $conn->prepare("UPDATE matches SET score_log = ?, status = 'Completed' WHERE match_id = ?");
    $winner_team_id = ($score_log['winner'] == $score_log['team1']) ? $score_log['team1'] : $score_log['team2'];
    $stmt->bind_param("ss", $json, $match_id);
    $stmt->execute();
    
    echo json_encode([
        'status' => 200,
        'message' => 'Match officially completed',
        'winner' => $winner_team_id
    ]);
    exit();
}

if($issuper_over == true && $Ball_Type == null){
    $score_log['super_over'] = true;
    echo json_encode(['status'=>200,'message'=>'Super Over added to score log']);
    exit();
}

function saveHistorySnapshot($conn, $match_id, $score_log) {
    // Calculate legal deliveries for accurate over count
    $legalBalls = array_filter($score_log['innings']['1st']['balls'] ?? [], function($ball) {
        return strpos($ball['Ball Type'], 'No Ball') === false && 
               strpos($ball['Ball Type'], 'Wide') === false;
    });
    $legalBallCount = count($legalBalls);
    $overs = floor($legalBallCount / 6) . '.' . ($legalBallCount % 6);
    
    // Fetch and update history
    $result = $conn->query("SELECT history FROM matches WHERE match_id = '$match_id'");
    $row = $result->fetch_assoc();
    $history = json_decode($row['history'], true) ?: [];
    
    $history[] = $score_log;
    
    if (count($history) > 15) {
        array_shift($history);
    }
    
    // Update both history and overs
    $stmt = $conn->prepare("UPDATE matches SET history = ?, score_log = ?, overs = ? WHERE match_id = ?");
    $stmt->bind_param("ssss", 
        json_encode($history),
        json_encode($score_log),
        $overs,
        $match_id
    );
    
    return $stmt->execute();
}

if (($Ball_Type == null && $undo == true)) {
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
        
        // Calculate proper overs count
        $legalBalls = array_filter($previousState['innings']['1st']['balls'] ?? [], function($ball) {
            return strpos($ball['Ball Type'], 'No Ball') === false && 
                   strpos($ball['Ball Type'], 'Wide') === false;
        });
        $overs = floor(count($legalBalls) / 6) . '.' . (count($legalBalls) % 6);
        // Prepare score string for database
        $score_log = $previousState;
        if($Inning_type == 'super_over_innings'){
            $team_over = $score_log['super_over_innings'][$Inning]['overs_completed'];
            $team_runs1 = $score_log['super_over_innings'][$Inning]['total_runs'];
            $team_wickets1 = $score_log['super_over_innings'][$Inning]['wickets'];

            $team_over1 = $score_log['innings'][$Inning]['overs_completed'];
            $team_runs = $score_log['innings'][$Inning]['total_runs'];
            $team_wickets = $score_log['innings'][$Inning]['wickets'];
            $score = $team_runs1.'/'.$team_wickets1.' - '.$team_runs.'/'.$team_wickets.' ('.$team_over1.'ov)';
        }else{
            $team_over = $score_log['innings'][$Inning]['overs_completed'];
            $team_runs = $score_log['innings'][$Inning]['total_runs'];
            $team_wickets = $score_log['innings'][$Inning]['wickets'];
            $score = $team_runs.'/'.$team_wickets.' ('.$team_over.'ov)';
        }

        $battingTeamId = $score_log[$Inning_type][$Inning]['batting_team'];
        $score_field = ($battingTeamId == $score_log['team1']) ? 'score_team_1' : 'score_team_2';
        
        // Update database
        $newHistory = array_slice($history, 0, -1);
        $stmt = $conn->prepare("UPDATE matches SET history = ?,$score_field = ?, score_log = ?, overs = ? WHERE match_id = ?");
        $stmt->bind_param("sssss", 
            json_encode($newHistory),
            $score,
            json_encode($previousState),
            $overs,
            $match_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update match state");
        }
        $conn->commit();
        $update = json_decode(get_data($match_id, $Inning),true);
        
        echo json_encode([
            "status" => 200,
            "message" => "Undo successful",
            "Data" => $update
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            "status" => 400,
            "message" => $e->getMessage()
        ]);
    }
    
    exit();
}

function calculateRR($runs, $overs_string) {
    // Convert "10.3" to decimal (e.g., 10.3 -> 10 + 3/6 = 10.5 overs)
    $parts = explode('.', $overs_string);
    $overs = (int)$parts[0];
    $balls = isset($parts[1]) ? (int)$parts[1] : 0;

    $decimal_overs = $overs + ($balls / 6);

    if ($decimal_overs == 0) return 0;

    return round($runs / $decimal_overs, 2);
}


handleMatchProgress($score_log);

if($Ball_Type != null){
    updateBall($score_log, $Inning_type, $Inning, $Ball_Type, $data, $match_id);
}


function updateBall(&$score_log, $Inning_type, $Inning, $Ball_Type, $data, $match_id) {
    global $conn;
    
    // Extract all data fields with defaults
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
    $run_type = $data['Run Type'] ?? null;

    // Add current ball to log
    $score_log[$Inning_type][$Inning]['total_runs'] += ($Run + $Extra);

    // Update batsman stats
    $current_striker = &$score_log[$Inning_type][$Inning]['openers']['current_striker'];

    if (!str_starts_with($Ball_Type, 'Wide')) {
        if (str_starts_with($Ball_Type, 'No')) {
            $current_striker['runs'] += $Run;
            $current_striker['balls_faced']++;

            if ($Run == 4) {
                $current_striker['fours']++;
            } elseif ($Run == 6) {
                $current_striker['sixes']++;
            }
        } else {
            $current_striker['balls_faced']++;
            $current_striker['runs'] += $Run;

            if ($Run == 4) {
                $current_striker['fours']++;
            } elseif ($Run == 6) {
                $current_striker['sixes']++;
            }
        }

        // 🔁 Now sync striker data with batsman list by ID
        foreach ($score_log[$Inning_type][$Inning]['batmans'] as $index => $batsman) {
            if ($batsman['id'] === $current_striker['id']) {
                $score_log[$Inning_type][$Inning]['batmans'][$index] = $current_striker;
                break;
            }
        }
    }

    // Batsman rotation logic
    if (
        (str_starts_with($Wicket_Type, 'Run Out') &&
        $Wicket_Type !== 'Run Out (Mankaded)' &&
        !str_contains($Wicket_Type, 'Returning')) ||
        str_starts_with($Wicket_Type, 'Obstructing')
    ) {
        if(($Run % 2) == 0 ) {
            swap_batsmans($score_log, $Inning_type, $Inning);
        }
    } elseif (!in_array($Ball_Type, ['Wide', 'No Ball'], true)) {

        if($run_type == 'FULL RUN' && ($Run % 2) == 1){
            swap_batsmans($score_log, $Inning_type, $Inning);
        }else if($run_type == 'HALF RUN' && ($Run % 2) == 0){
            swap_batsmans($score_log, $Inning_type, $Inning);
        }

    }

    if(in_array($Ball_Type, ['Bye', 'Leg Bye'], true) && ($Extra % 2) == 1) {
        swap_batsmans($score_log, $Inning_type, $Inning);
    }

    if(in_array($Ball_Type, ['No Ball-Bye', 'No Ball-Leg Bye'], true) && ($Extra % 2) == 0) {
        swap_batsmans($score_log, $Inning_type, $Inning);
    }

    // Wicket processing
    $process_wicket = true;
    if (($Ball_Type === 'Free Hit') || ($Freehit === true || $Freehit === 'true')) {
        $allowedOnFreeHit = [
            'Run Out (Striker End)', 
            'Run Out (Non-Striker End)',
            'Obstructing the Field (Striker End)',
            'Obstructing the Field (Non-Striker End)',
            'Run out (Mankaded)',
            'Retired Out(Non-Striker)',
            'Retired Out(Striker)',
            'Hit the Ball Twice'
        ];
        
        if (!in_array($Wicket_Type, $allowedOnFreeHit)) {
            $process_wicket = false;
        }
    }

    $is_regular_innings = ($Inning_type === 'innings');
    $is_super_over = ($Inning_type === 'super_over_innings');
    $wicket_limit = $is_regular_innings ? MAX_REGULAR_WICKETS : MAX_SUPER_OVER_WICKETS;

    if (($Ball_Type === 'Wicket' || $Ball_Type === 'No Ball-Wicket' || 
        ($Ball_Type === 'Free Hit' && $process_wicket)) && $Wicket_Type) {
        
        // Increment wickets
        $score_log[$Inning_type][$Inning]['wickets']++;
        if (!strpos($Wicket_Type, "Run out")) {
            $score_log[$Inning_type][$Inning]['current_bowler']['wickets']++;
        }

        $is_non_striker = (strpos($Wicket_Type, '(Non-Striker End)') !== false || 
                         strpos($Wicket_Type, '(Mankaded)') !== false ||
                         strpos($Wicket_Type, '(Non-Striker Returning)') !== false ||
                         strpos($Wicket_Type, 'Retired Out(Non-Striker)') !== false);
        
        $player_key = $is_non_striker ? 'current_non_striker' : 'current_striker';
        
        $out_player = $score_log[$Inning_type][$Inning]['openers'][$player_key];
        $out_player['out_status'] = "out";
        $out_player['wicket_type'] = $Wicket_Type;
        $out_player['wicket_by'] = $Wicket_By;
        $out_player['bowler'] = $Bowler;
        $data['Out Player'] = $out_player['id'];
        
        // Update existing batsman in the batmans array by matching ID
        foreach ($score_log[$Inning_type][$Inning]['batmans'] as $index => $batsman) {
            if ($batsman['id'] === $out_player['id']) {
                $score_log[$Inning_type][$Inning]['batmans'][$index] = $out_player;
                break;
            }
        }
        
        if ($score_log[$Inning_type][$Inning]['wickets'] < $wicket_limit) {
            $score_log[$Inning_type][$Inning]['openers'][$player_key] = [
                "id"=> $New_Player ?? null,
                "balls_faced"=> 0,
                "style"=> null,
                "runs"=> 0,
                "fours"=> 0,
                "sixes"=> 0,
                "out_status"=> "not out"
            ];
        }
    }

     // Update team-specific scores and wickets
    $battingTeamId = $score_log[$Inning_type][$Inning]['batting_team'];
    if ($battingTeamId == $score_log['team1']) {
        $score_log['team1_score'] = $score_log[$Inning_type][$Inning]['total_runs'];
        $score_log['team1_Wickets'] = $score_log[$Inning_type][$Inning]['wickets'];
    } else {
        $score_log['team2_score'] = $score_log[$Inning_type][$Inning]['total_runs'];
        $score_log['team2_Wickets'] = $score_log[$Inning_type][$Inning]['wickets'];
    }

    // Over progression logic
    $validDeliveryTypes = ['Legal Delivery', 'Bye', 'Leg Bye', 'Wicket', 'Free Hit'];
    if(in_array($Ball_Type, $validDeliveryTypes)) {
        // Get overs and balls for current bowler
        list($over, $ball) = explode('.', $score_log[$Inning_type][$Inning]['current_bowler']['overs_bowled']);
        $over = (int)$over;
        $ball = (int)$ball;

        // Get innings overs
        list($inning_over, $inning_ball) = explode('.', $score_log[$Inning_type][$Inning]['overs_completed']);
        $inning_over = (int)$inning_over;
        $inning_ball = (int)$inning_ball;

        // Increment ball if legal delivery
        if (!in_array($Ball_Type, ['Wide', 'No Ball', 'No Ball-Bye', 'No Ball-Leg Bye'], true)) {
            $ball++;
            $inning_ball++;
        }

        // Add runs conceded
        $score_log[$Inning_type][$Inning]['current_bowler']['runs_conceded'] += ($Run + $Extra);

        // If bowler over complete
        if ($ball >= 6) {
            $over++;
            $ball = 0;

            // Swap batsman at over end
            swap_batsmans($score_log, $Inning_type, $Inning);

            // Reset freehit flags
            foreach ($score_log[$Inning_type][$Inning]['balls'] as &$pastBall) {
                if (isset($pastBall['Freehit'])) {
                    $pastBall['Freehit'] = false;
                }
            }
            unset($pastBall);

            // Check if maiden over
            $runs_in_over = 0;
            $legal_deliveries = 0;
            $current_bowler = $score_log[$Inning_type][$Inning]['current_bowler']['id'];

            // Traverse balls in reverse to get latest legal deliveries by this bowler
            for ($i = count($score_log[$Inning_type][$Inning]['balls']) - 1; $i >= 0; $i--) {
                $pastBall = $score_log[$Inning_type][$Inning]['balls'][$i];

                // Only count legal deliveries by current bowler
                if ($pastBall['Bowler'] === $current_bowler && $pastBall['Ball Type'] === 'Legal Delivery') {
                    $runs_in_over += (int)$pastBall['Run'];
                    if (!empty($pastBall['Extra'])) {
                        $runs_in_over += (int)$pastBall['Extra'];
                    }
                    $legal_deliveries++;

                    // Stop when 6 legal deliveries found
                    if ($legal_deliveries == 6) {
                        break;
                    }
                }
            }

            // If exactly 6 legal deliveries and runs = 0 → it's a maiden
            if ($legal_deliveries == 6 && $runs_in_over == 0) {
                $score_log[$Inning_type][$Inning]['current_bowler']['maidens']++;
            }

            $score_log[$Inning_type][$Inning]['current_bowler']['overs_bowled'] = "$over.$ball";
            
            // Log completed bowler
            $bowler_id = $score_log[$Inning_type][$Inning]['current_bowler']['id'];

            // Update cumulative stats for this bowler in 'bowlers' array
            if (!isset($score_log[$Inning_type][$Inning]['bowlers'][$bowler_id])) {
                // If first over by this bowler, add new record
                $score_log[$Inning_type][$Inning]['bowlers'][$bowler_id] = $score_log[$Inning_type][$Inning]['current_bowler'];
            } else {
                // If already exists, update cumulative stats
                $score_log[$Inning_type][$Inning]['bowlers'][$bowler_id]['overs_bowled'] = $score_log[$Inning_type][$Inning]['current_bowler']['overs_bowled'];
                $score_log[$Inning_type][$Inning]['bowlers'][$bowler_id]['runs_conceded'] = $score_log[$Inning_type][$Inning]['current_bowler']['runs_conceded'];
                $score_log[$Inning_type][$Inning]['bowlers'][$bowler_id]['wickets'] = $score_log[$Inning_type][$Inning]['current_bowler']['wickets'];
                $score_log[$Inning_type][$Inning]['bowlers'][$bowler_id]['maidens'] = $score_log[$Inning_type][$Inning]['current_bowler']['maidens'];
            }

            // Reset current bowler
            $score_log[$Inning_type][$Inning]['current_bowler'] = [
                "id" => null,
                "style" => null,
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
        } else {
            $score_log[$Inning_type][$Inning]['current_bowler']['overs_bowled'] = "$over.$ball";
        }


        // Check if innings over complete
        if ($inning_ball >= 6) {
            $inning_over++;
            $inning_ball = 0;
        }

        // Update innings overs completed
        $score_log[$Inning_type][$Inning]['overs_completed'] = "$inning_over.$inning_ball";
    }

    if (str_contains($Ball_Type, 'No')) {
        $score_log[$Inning_type][$Inning]['Extras']['NB'] += (intval($Extra) + intval($Run));
        $score_log[$Inning_type][$Inning]['Extras']['total_extras'] += (intval($Extra) + intval($Run));

        $score_log[$Inning_type][$Inning]['current_bowler']['Extras']['NB'] += (intval($Extra) + intval($Run));
        $score_log[$Inning_type][$Inning]['current_bowler']['Extras']['total_extras'] += (intval($Extra) + intval($Run));
    }

    // Wide Ball
    if (str_contains($Ball_Type, 'Wide')) {
        $score_log[$Inning_type][$Inning]['Extras']['W'] += (intval($Extra) + intval($Run));
        $score_log[$Inning_type][$Inning]['Extras']['total_extras'] += (intval($Extra) + intval($Run));

        $score_log[$Inning_type][$Inning]['current_bowler']['Extras']['NB'] += (intval($Extra) + intval($Run));
        $score_log[$Inning_type][$Inning]['current_bowler']['Extras']['total_extras'] += (intval($Extra) + intval($Run));
    }

    // Bye
    if (str_contains($Ball_Type, 'Bye')) {
        $score_log[$Inning_type][$Inning]['Extras']['B'] += (intval($Extra) + intval($Run));
        $score_log[$Inning_type][$Inning]['Extras']['total_extras'] += (intval($Extra) + intval($Run));
    }

    // Leg Bye
    if (str_contains($Ball_Type, 'Leg Bye') || str_contains($Ball_Type, 'LB')) {
        $score_log[$Inning_type][$Inning]['Extras']['LB'] += (intval($Extra) + intval($Run));
        $score_log[$Inning_type][$Inning]['Extras']['total_extras'] += (intval($Extra) + intval($Run));
    }

    // Check for innings completion conditions
    $innings_completed = false;
    
    // Check for all out
    if ($score_log[$Inning_type][$Inning]['wickets'] >= $wicket_limit) {
        $score_log[$Inning_type][$Inning]['completed'] = true;
        $innings_completed = true;
    }
    
    // Check for regular innings overs completion
    if ($is_regular_innings && isset($score_log['overs']) && 
        (float)$score_log[$Inning_type][$Inning]['overs_completed'] >= (float)$score_log['overs']) {
        $score_log[$Inning_type][$Inning]['completed'] = true;
        $innings_completed = true;
    }
    
    // Check for super over completion
    if ($is_super_over && (float)$score_log[$Inning_type][$Inning]['overs_completed'] >= 1.0) {
        $score_log[$Inning_type][$Inning]['completed'] = true;
        $innings_completed = true;
    }

    // Prepare score string for database
    if($Inning_type == 'super_over_innings'){
        $team_over = $score_log['super_over_innings'][$Inning]['overs_completed'];
        $team_runs1 = $score_log['super_over_innings'][$Inning]['total_runs'];
        $team_wickets1 = $score_log['super_over_innings'][$Inning]['wickets'];

        $team_over1 = $score_log['innings'][$Inning]['overs_completed'];
        $team_runs = $score_log['innings'][$Inning]['total_runs'];
        $team_wickets = $score_log['innings'][$Inning]['wickets'];
        $score = $team_runs1.'/'.$team_wickets1.' - '.$team_runs.'/'.$team_wickets.' ('.$team_over1.'ov)';
    }else{
        $team_over = $score_log['innings'][$Inning]['overs_completed'];
        $team_runs = $score_log['innings'][$Inning]['total_runs'];
        $team_wickets = $score_log['innings'][$Inning]['wickets'];
        $score = $team_runs.'/'.$team_wickets.' ('.$team_over.'ov)';
    }
    

    $battingTeamId = $score_log[$Inning_type][$Inning]['batting_team'];
    $score_field = ($battingTeamId == $score_log['team1']) ? 'score_team_1' : 'score_team_2';

    $data['RR'] = calculateRR($team_runs, $team_over);
    $data['overs_completed'] = $team_over;
    $score_log[$Inning_type][$Inning]['balls'][] = $data;
    saveHistorySnapshot($conn, $match_id, $score_log);
    // Update database - single transaction for all updates
    $conn->begin_transaction();
    
    try {
        
        // Update team score
        $stmt = $conn->prepare("UPDATE matches SET $score_field = ? WHERE match_id = ?");
        $stmt->bind_param("ss", $score, $match_id);
        $stmt->execute();
        
        $conn->commit();

        // Call handleMatchProgress for every ball update
        handleMatchProgress($score_log);
        
        // Only call handleMatchProgress after all database updates are complete
        if ($innings_completed) {
            handleMatchProgress($score_log);
        }
        $update = json_decode(get_data($match_id, $Inning),true);
        header('Content-Type: application/json');
        echo json_encode(['status' => 200, 'message' => 'Data updated successfully','Data'=>$update]);
        exit();
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 500, 'message' => 'Failed to update match: ' . $e->getMessage()]);
        exit();
    }
}

function swap_batsmans(&$score_log, $Inning_type, $Inning) {
        $temp = $score_log[$Inning_type][$Inning]['openers']['current_striker'];
        $score_log[$Inning_type][$Inning]['openers']['current_striker'] = 
            $score_log[$Inning_type][$Inning]['openers']['current_non_striker'];
        $score_log[$Inning_type][$Inning]['openers']['current_non_striker'] = $temp;
}

function handleMatchProgress(&$score_log) {
    global $conn;
    global $match_id;
    global $Inning;
    // First check regular innings progress
    if (isset($score_log['innings']['1st']['completed']) && $score_log['innings']['1st']['completed']) {
        $team1Runs = (int)($score_log['innings']['1st']['total_runs'] ?? 0);
        $team2Runs = (int)($score_log['innings']['2nd']['total_runs'] ?? 0);
        
        // Check if team2 has won during their innings (before completion)
        if (isset($score_log['innings']['2nd']) && $score_log['innings']['2nd']['completed'] == false) {
            if ($team2Runs > $team1Runs) {
                // Update database
                $score_log['winner'] = $score_log['innings']['2nd']['batting_team'];

                saveHistorySnapshot($conn, $match_id, $score_log);
                $update = json_decode(get_data($match_id, $Inning),true);

                $response = [
                    'status' => 200,
                    'message' => 'Team 2 Won the match by chasing the target',
                    'is_complete' => true, // Ask user to confirm
                    'action_required' => 'confirm_match_end',
                    'team1_runs' => $team1Runs,
                    'team2_runs' => $team2Runs,
                    'Data' => $update,
                    'wickets_remaining' => MAX_REGULAR_WICKETS - ($score_log['innings']['2nd']['wickets'] ?? 0),
                    'overs_remaining' => isset($score_log['overs']) ? $score_log['overs'] - (float)$score_log['innings']['2nd']['overs_completed'] : null
                ];
                echo json_encode($response);
                exit();
            }
            
            // Check if team2 is all out before reaching target
            if (($score_log['innings']['2nd']['wickets'] ?? 0) >= MAX_REGULAR_WICKETS) {
                // Update database
                $score_log['winner'] = $score_log['innings']['1st']['batting_team'];
                saveHistorySnapshot($conn, $match_id, $score_log);
                $update = json_decode(get_data($match_id, $Inning),true);

                $response = [
                    'status' => 200,
                    'message' => 'Team 1 Won the match as Team 2 is all out',
                    'is_complete' => true,
                    'action_required' => 'confirm_match_end',
                    'team1_runs' => $team1Runs,
                    'team2_runs' => $team2Runs,
                    'Data' => $update,
                    'wickets_lost' => $score_log['innings']['2nd']['wickets'] ?? 0
                ];
                echo json_encode($response);
                exit();
            }
        }
    }

    // Check if regular innings are completed
    $regularInningsCompleted = (
        isset($score_log['innings']['1st']['completed'], $score_log['innings']['2nd']['completed']) &&
        $score_log['innings']['1st']['completed'] == true &&
        $score_log['innings']['2nd']['completed'] == true
    );

    if ($regularInningsCompleted) {
    $team1Runs = ($score_log['innings']['1st']['total_runs'] ?? 0);
    $team2Runs = ($score_log['innings']['2nd']['total_runs'] ?? 0);
    $isSuperAllow = filter_var($score_log['issuperallow'], FILTER_VALIDATE_BOOL);
    // Check for tie and super over allowed
        if ((int)$team1Runs == (int)$team2Runs) {
            if ($isSuperAllow === true) {
                // Initialize super over if not already done
                if (empty($score_log['super_over_innings'])) {
                    // Determine which team batted second in regular innings
                    $regularSecondInningsTeam = $score_log['innings']['2nd']['batting_team'];
                    
                    // The team that batted second in regular innings bats first in super over
                    $superOverFirstBattingTeam = $regularSecondInningsTeam;
                    $superOverSecondBattingTeam = ($superOverFirstBattingTeam == $score_log['team1']) 
                        ? $score_log['team2'] 
                        : $score_log['team1'];
                    
                    // Initialize super over innings
                    $score_log['super_over_innings'] = [
                        '1st' => makeNewInnings($superOverFirstBattingTeam, $superOverSecondBattingTeam),
                        '2nd' => makeNewInnings($superOverSecondBattingTeam, $superOverFirstBattingTeam)
                    ];
                    
                    // Add super over to score_log
                    $score_log['super_overs'] = []; 
                    $score_log['super_over'] = true;
                    // Save to database
                    saveHistorySnapshot($conn, $match_id, $score_log);
                    $update = json_decode(get_data($match_id, $Inning),true);
                    
                    $response = [
                        'status' => 200,
                        'message' => 'Match tied! Starting Super Over...',
                        'is_complete' => false,
                        'Data' => $update,
                        'action_required' => 'start_super_over'
                    ];
                    echo json_encode($response);
                    exit();
                }
                // If super over innings already exist, do nothing (let the super over continue)
            } else {
                // Super over not allowed, declare tie
                $score_log['winner'] = 'tie';
                saveHistorySnapshot($conn, $match_id, $score_log);
                $update = json_decode(get_data($match_id, $Inning),true);

                $response = [
                    'status' => 200,
                    'message' => 'Match Tied',
                    'is_complete' => true,
                    'action_required' => 'confirm_match_end',
                    'Data' => $update,
                    'team1_runs' => $team1Runs,
                    'team2_runs' => $team2Runs
                ];
                echo json_encode($response);
                exit();
            }
        } else {
            // Not a tie - determine winner
            if ($team1Runs > $team2Runs) {
                $winner = $score_log['innings']['1st']['batting_team'];
                $result_message = 'Team 1 Won the match';
            } else {
                $winner = $score_log['innings']['2nd']['batting_team'];
                $result_message = 'Team 2 Won the match';
            }

            $score_log['winner'] = $winner;
            saveHistorySnapshot($conn, $match_id, $score_log);
            $update = json_decode(get_data($match_id, $Inning),true);

            $response = [
                'status' => 200,
                'message' => $result_message,
                'is_complete' => true,
                'action_required' => 'confirm_match_end',
                'Data' => $update,
                'team1_runs' => $team1Runs,
                'team2_runs' => $team2Runs
                
            ];
            echo json_encode($response);
            exit();
        }
    }

    // Check super over progress
    if (isset($score_log['super_over_innings'])) {
        // Check if team2 has won during super over (before completion)
        if (isset($score_log['super_over_innings']['1st']['total_runs']) && 
            isset($score_log['super_over_innings']['2nd']) && 
            !$score_log['super_over_innings']['2nd']['completed']) {
            
            $team1Runs = (int)($score_log['super_over_innings']['1st']['total_runs'] ?? 0);
            $team2Runs = (int)($score_log['super_over_innings']['2nd']['total_runs'] ?? 0);
            
            if ($team2Runs > $team1Runs) {
                // $score_log['super_over_innings']['2nd']['completed'] = true;
                $score_log['winner'] = $score_log['super_over_innings']['2nd']['batting_team'];
                saveHistorySnapshot($conn, $match_id, $score_log);
                $update = json_decode(get_data($match_id, $Inning),true);

                $response = [
                    'status' => 200,
                    'message' => 'Team 2 Won the match by chasing the target',
                    'is_complete' => true, // Ask user to confirm
                    'action_required' => 'confirm_match_end',
                    'team1_runs' => $team1Runs,
                    'team2_runs' => $team2Runs,
                    'Data' => $update,
                    'wickets_remaining' => MAX_REGULAR_WICKETS - ($score_log['innings']['2nd']['wickets'] ?? 0),
                    'overs_remaining' => isset($score_log['overs']) ? $score_log['overs'] - (float)$score_log['innings']['2nd']['overs_completed'] : null
                ];
                echo json_encode($response);
                exit();
            }
            
            // Check if team2 is all out before reaching target
            if (($score_log['super_over_innings']['2nd']['wickets'] ?? 0) >= MAX_SUPER_OVER_WICKETS) {
                // $score_log['super_over_innings']['2nd']['completed'] = true;
                $score_log['winner'] = ($team1Runs > $team2Runs) ? $score_log['super_over_innings']['1st']['batting_team'] : $score_log['super_over_innings']['2nd']['batting_team'];
                saveHistorySnapshot($conn, $match_id, $score_log);
                $update = json_decode(get_data($match_id, $Inning),true);

                $response = [
                    'status' => 200,
                    'message' => 'Team 2 Won the match by chasing the target',
                    'is_complete' => true, // Ask user to confirm
                    'action_required' => 'confirm_match_end',
                    'team1_runs' => $team1Runs,
                    'team2_runs' => $team2Runs,
                    'Data' => $update,
                    'wickets_remaining' => MAX_REGULAR_WICKETS - ($score_log['innings']['2nd']['wickets'] ?? 0),
                    'overs_remaining' => isset($score_log['overs']) ? $score_log['overs'] - (float)$score_log['innings']['2nd']['overs_completed'] : null
                ];
                echo json_encode($response);
                exit();
            }
        }

        $superOverCompleted = (
            isset($score_log['super_over_innings']['1st']['completed']) && 
            $score_log['super_over_innings']['1st']['completed'] == true &&
            isset($score_log['super_over_innings']['2nd']['completed']) && 
            $score_log['super_over_innings']['2nd']['completed'] == true
        );

        if ($superOverCompleted) {
            $team1Runs = (int)($score_log['super_over_innings']['1st']['total_runs'] ?? 0);
            $team2Runs = (int)($score_log['super_over_innings']['2nd']['total_runs'] ?? 0);
            
            $score_log['super_overs'][] = $score_log['super_over_innings'];
            
            if ($team1Runs == $team2Runs && ($score_log['issuperallow'] ?? false)) {
                // Prepare for another super over
                $score_log['super_over_innings'] = [
                    '1st' => makeNewInnings($score_log['team1'], $score_log['team2']),
                    '2nd' => makeNewInnings($score_log['team2'], $score_log['team1'])
                ];

                // Save to database
                saveHistorySnapshot($conn, $match_id, $score_log);
                $update = json_decode(get_data($match_id, $Inning),true);
                
                $response = [
                    'status' => 200,
                    'message' => 'Match tied! Starting New Super Over...',
                    'is_complete' => false,
                    'Data' => $update,
                    'action_required' => 'start_super_over'
                ];
                echo json_encode($response);
                exit();

            } else {
                $score_log['winner'] = ($team1Runs > $team2Runs) ? $score_log['super_over_innings']['1st']['batting_team'] : $score_log['super_over_innings']['2nd']['batting_team'];
                saveHistorySnapshot($conn, $match_id, $score_log);
                $update = json_decode(get_data($match_id, $Inning),true);

                $response = [
                    'status' => 200,
                    'message' => 'Team 2 Won the match by chasing the target',
                    'is_complete' => true, // Ask user to confirm
                    'action_required' => 'confirm_match_end',
                    'team1_runs' => $team1Runs,
                    'team2_runs' => $team2Runs,
                    'Data' => $update,
                    'wickets_remaining' => MAX_REGULAR_WICKETS - ($score_log['innings']['2nd']['wickets'] ?? 0),
                    'overs_remaining' => isset($score_log['overs']) ? $score_log['overs'] - (float)$score_log['innings']['2nd']['overs_completed'] : null
                ];
                echo json_encode($response);
                exit();
            }
        }
    }
}

function makeNewInnings($battingTeam, $bowlingTeam) {
    return [
        'batting_team' => $battingTeam,
        'bowling_team' => $bowlingTeam,
        'openers' => [
            'current_striker' => [
                'id' => null,
                'balls_faced' => 0,
                'style' => null,
                'runs' => 0,
                'fours' => 0,
                'sixes' => 0,
                'out_status' => 'not out'
            ],
            'current_non_striker' => [
                'id' => null,
                'balls_faced' => 0,
                'style' => null,
                'runs' => 0,
                'fours' => 0,
                'sixes' => 0,
                'out_status' => 'not out'
            ]
        ],
        "current_bowler" => [
        "id"=> null,
        "style"=> null,
        "overs_bowled"=> "0.0",
        "runs_conceded"=> 0,
        "wickets"=> 0,
        "maidens" => 0,
        "Extras" => [
            "NB" => 0,
            "W" => 0,
            "total_extras" => 0
            ]
        ],
        "balls" => [],
        "batmans" => [],
        "bowlers" => [],
        "total_runs" => 0,
        "wickets" => 0,
        "Extras" => [
            "B" => 0,
            "NB" => 0,
            "LB" => 0,
            "W" => 0,
            "total_extras" => 0
        ],
        "overs_completed" => "0.0",
        "completed" => false,
        'is_super_over' => true
    ];
}

function get_data($match_id, $Inning){
    $_GET['match_id'] = $match_id;
    $_GET['current_innings'] = $Inning;

    ob_start();
    include '../API/CRICKET_api.php';  // executes with $_GET values
    $response = ob_get_clean();

    return $response;
}
?>