<?php
require_once('../../../tcpdf_min/tcpdf.php'); 
include '../../../config.php'; // DB connection

$match_id = $_GET['match_id'] ?? '';
if (!$match_id) die("Match ID required.");

// Fetch match info
$match = $conn->query("SELECT * FROM matches WHERE match_id='$match_id'")->fetch_assoc();
if (!$match) die("Match not found.");
$score_log = json_decode($match['score_log'], true);

// Resolve team names
$team1_id = $match['team_1'];
$team2_id = $match['team_2'];
$team1_name = $conn->query("SELECT t_name FROM teams WHERE t_id='$team1_id'")->fetch_assoc()['t_name'] ?? 'Team 1';
$team2_name = $conn->query("SELECT t_name FROM teams WHERE t_id='$team2_id'")->fetch_assoc()['t_name'] ?? 'Team 2';

// Resolve created_by
$creator = $conn->query("SELECT fname,lname FROM users WHERE user_id='{$match['created_by']}'")->fetch_assoc();
$creator_name = $creator ? $creator['fname'].' '.$creator['lname'] : 'Unknown';

// Helper function to get user name from email
function getUserName($conn, $email){
    $row = $conn->query("SELECT fname,lname FROM users WHERE email='$email'")->fetch_assoc();
    return $row ? $row['fname'].' '.$row['lname'] : $email;
}

// Resolve umpires, scorers, commentators
$umpires = $match['umpires'] ? implode(', ', array_map(fn($e)=>getUserName($conn,$e), json_decode($match['umpires'], true))) : '-';
$scorers = $match['scorers'] ? implode(', ', array_map(fn($e)=>getUserName($conn,$e), json_decode($match['scorers'], true))) : '-';
$commentators = $match['commentators'] ? implode(', ', array_map(fn($e)=>getUserName($conn,$e), json_decode($match['commentators'], true))) : '-';

// Fetch tournament name
$tournament_name = '-';
if(!empty($match['tournament'])){
    $tournament_name = $conn->query("SELECT tournament_name FROM tournaments WHERE tournament_id='{$match['tournament']}'")->fetch_assoc()['tournament_name'] ?? '-';
}

// Resolve toss winner and match winner (IDs to names)
$toss_winner_name = '-';
if(!empty($match['toss_winner'])){
    $toss_winner_name = $conn->query("SELECT t_name FROM teams WHERE t_id='{$match['toss_winner']}'")->fetch_assoc()['t_name'] ?? $match['toss_winner'];
}

$winner_name = '-';
if(!empty($score_log['winner'])){
    $winner_name = $conn->query("SELECT t_name FROM teams WHERE t_id='{$score_log['winner']}'")->fetch_assoc()['t_name'] ?? $score_log['winner'];
}

// Initialize PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('LiveStrike');
$pdf->SetAuthor('LiveStrike Admin');
$pdf->SetTitle('Match Report: ' . $match['match_name']);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

// CSS Styling (TCPDF supports inline CSS only)
$style = <<<CSS
<style>
h1 {text-align:center;color:#004085;font-weight:bold;}
h4 {font-weight:bold;color:red;text-align:center;}
table {border-collapse:collapse;width:100%; margin-bottom:10px;font-size:10pt;}
th, td {border:1px solid #dee2e6;padding:6px;text-align:left;}
th {background-color:#007bff;color:white;font-weight:bold;}
tr:nth-child(even){background-color:#f2f2f2;}
.bold {font-weight:bold;}
.green {background-color:#d4edda;}
.red {background-color:#f8d7da;}
</style>
CSS;

$html = $style;
$html .= "<h1>Match Report</h1><br><h4>(".$team1_name." VS ".$team2_name.")</h4>";

// Match overview
$html .= "<table><tr><th>Field</th><th>Value</th></tr>";
$fields = [
    'match_name'=>'Match Name',
    'match_date'=>'Date',
    'status'=>'Status',
    'venue'=>'Venue',
    'tournament'=>'Tournament',
    'round'=>'Round',
    'start_time'=>'Start Time',
    'score_team_1'=>"Score ({$team1_name})",
    'score_team_2'=>"Score ({$team2_name})",
    'toss_winner'=>'Toss Winner',
    'toss_decision'=>'Toss Decision',
    'winner'=>'Winner',
    'created_by'=>'Created By',
    'umpires'=>'Umpires',
    'scorers'=>'Scorers',
    'commentators'=>'Commentators'
];

foreach($fields as $f=>$label){
    $val = '-';
    switch($f){
        case 'score_team_1': $val = $match['score_team_1']; break;
        case 'score_team_2': $val = $match['score_team_2']; break;
        case 'created_by': $val = $creator_name; break;
        case 'umpires': $val = $umpires; break;
        case 'scorers': $val = $scorers; break;
        case 'commentators': $val = $commentators; break;
        case 'tournament': $val = $tournament_name; break;
        case 'toss_winner': $val = $toss_winner_name; break;
        case 'winner': $val = $winner_name; break;
        default: $val = $match[$f] ?? '-';
    }
    $row_class = ($f=='winner') ? 'green' : '';
    $html .= "<tr class='$row_class'><td>$label</td><td>".htmlspecialchars($val)."</td></tr>";
}
$html .= "</table><br>";

// Players table for both teams
$html .= "<hr><h4>Players in Match</h4>";
$html .= "<table><tr><th>{$team1_name}</th><th>{$team2_name}</th></tr>";

$team1_players = $conn->query("SELECT player_name FROM players WHERE team_id='$team1_id' ORDER BY player_name ASC");
$team2_players = $conn->query("SELECT player_name FROM players WHERE team_id='$team2_id' ORDER BY player_name ASC");

$team1_list = []; $team2_list = [];
while($row = $team1_players->fetch_assoc()) $team1_list[] = $row['player_name'];
while($row = $team2_players->fetch_assoc()) $team2_list[] = $row['player_name'];
$max_rows = max(count($team1_list), count($team2_list));

for($i=0;$i<$max_rows;$i++){
    $t1 = $team1_list[$i] ?? '-';
    $t2 = $team2_list[$i] ?? '-';
    $html .= "<tr><td>".htmlspecialchars($t1)."</td><td>".htmlspecialchars($t2)."</td></tr>";
}
$html .= "</table><br>";

// Player of the Match logic
$players_details=[];

// Loop through innings to gather player stats
foreach($score_log['innings'] as $inning){
    // Batting
    foreach($inning['batmans'] as $b){
        $id = $b['id'];
        if(!isset($players_details[$id])) $players_details[$id] = [];
        $players_details[$id]['batting'] = [
            'runs'=>$b['runs'],
            'balls'=>$b['balls_faced'],
            'fours'=>$b['fours'],
            'sixes'=>$b['sixes'],
            'out_status'=>$b['out_status']
        ];
    }
    // Bowling
    foreach($inning['bowlers'] as $b){
        $id = $b['id'];
        if(!isset($players_details[$id])) $players_details[$id] = [];
        $players_details[$id]['bowling'] = [
            'overs'=>$b['overs_bowled'],
            'runs_conceded'=>$b['runs_conceded'],
            'wickets'=>$b['wickets'],
            'maidens'=>$b['maidens']
        ];
    }
}

// Determine top player by runs + wickets (simple heuristic)
$top_player_id = null; $top_score = -1;
foreach($players_details as $id=>$stats){
    $score = ($stats['batting']['runs'] ?? 0) + (($stats['bowling']['wickets'] ?? 0)*10);
    if($score > $top_score){ $top_score = $score; $top_player_id = $id; }
}

// Fetch player info
if($top_player_id){
    $p = $conn->query("SELECT * FROM players WHERE user_id='$top_player_id'")->fetch_assoc();
    $p_name = $p['player_name'] ?? 'Unknown';
    $p_team = $conn->query("SELECT t_name FROM teams WHERE t_id='".$p['team_id']."'")->fetch_assoc()['t_name'] ?? 'Unknown';
    $p_photo = !empty($p['photo']) ? "../../assets/images/users/".$p['photo'] : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";

    $bat = $players_details[$top_player_id]['batting'] ?? [];
    $bowl = $players_details[$top_player_id]['bowling'] ?? [];

    $html .= "<hr><h4>Player Of The Match</h4>";
    $html .= "<table><tr><th>Photo</th><th>Name</th><th>Team</th><th>Runs</th><th>Balls</th><th>Fours</th><th>Sixes</th><th>Overs</th><th>Wickets</th><th>Runs Conceded</th><th>Maidens</th></tr>";
    $html .= "<tr>";
    $html .= "<td><img src='$p_photo' width='50' height='50'></td>";
    $html .= "<td>$p_name</td>";
    $html .= "<td>$p_team</td>";
    $html .= "<td>".($bat['runs'] ?? '-')."</td>";
    $html .= "<td>".($bat['balls'] ?? '-')."</td>";
    $html .= "<td>".($bat['fours'] ?? '-')."</td>";
    $html .= "<td>".($bat['sixes'] ?? '-')."</td>";
    $html .= "<td>".($bowl['overs'] ?? '-')."</td>";
    $html .= "<td>".($bowl['wickets'] ?? '-')."</td>";
    $html .= "<td>".($bowl['runs_conceded'] ?? '-')."</td>";
    $html .= "<td>".($bowl['maidens'] ?? '-')."</td>";
    $html .= "</tr></table><br>";
}

// Loop through innings (same as your existing code for batting, bowling, balls log)
foreach(['1st','2nd'] as $inning){
    if(!isset($score_log['innings'][$inning])) continue;
    $batting_team_id = $score_log['innings'][$inning]['batting_team'];
    $batting_team_name = $conn->query("SELECT t_name FROM teams WHERE t_id='$batting_team_id'")->fetch_assoc()['t_name'] ?? $batting_team_id;
    $html .= "<hr><h4 style='color:red;'>Inning: $inning (Batting Team: $batting_team_name)</h4>";

    // Batsmen
    $html .= "<b>Batsmen:</b><br><br>";
    $html .= "<table><tr><th>Player Name</th><th>Runs</th><th>Balls</th><th>Fours</th><th>Sixes</th><th>Status</th></tr>";
    foreach($score_log['innings'][$inning]['batmans'] as $b){
        $player = $conn->query("SELECT player_name FROM players WHERE user_id='{$b['id']}'")->fetch_assoc()['player_name'] ?? $b['id'];
        $html .= "<tr><td>".htmlspecialchars($player)."</td><td>{$b['runs']}</td><td>{$b['balls_faced']}</td><td>{$b['fours']}</td><td>{$b['sixes']}</td><td>{$b['out_status']}</td></tr>";
    }
    $html .= "</table><br>";

    // Bowlers
    $html .= "<br><b>Bowlers:</b><br><br>";
    $html .= "<table><tr><th>Player Name</th><th>Style</th><th>Overs</th><th>Runs Conceded</th><th>Wickets</th></tr>";
    foreach($score_log['innings'][$inning]['bowlers'] as $b){
        $player = $conn->query("SELECT player_name FROM players WHERE user_id='{$b['id']}'")->fetch_assoc()['player_name'] ?? $b['id'];
        $html .= "<tr><td>".htmlspecialchars($player)."</td><td>{$b['style']}</td><td>{$b['overs_bowled']}</td><td>{$b['runs_conceded']}</td><td>{$b['wickets']}</td></tr>";
    }
    $html .= "</table><br>";

    // Ball log
    $html .= "<br><b>Balls Log:</b><br><br>";
    $html .= "<table><tr><th>Ball</th><th>Run</th><th>Shot Type</th><th>Bowler</th><th>Striker</th><th>Wicket</th><th>Commentary</th></tr>";

    $previous_striker_id = null; // to store striker of previous ball

    foreach($score_log['innings'][$inning]['balls'] as $index=>$ball){
        $bowler = $conn->query("SELECT player_name FROM players WHERE user_id='{$ball['Bowler']}'")->fetch_assoc()['player_name'] ?? $ball['Bowler'];
        
        // If ball type is Wicket, take previous striker
        if(!empty($ball['Wicket Type']) && $previous_striker_id){
            $striker_id = $previous_striker_id;
        } else {
            $striker_id = $ball['Striker'];
        }
        
        $striker = $conn->query("SELECT player_name FROM players WHERE user_id='{$striker_id}'")->fetch_assoc()['player_name'] ?? $striker_id;

        // Wicket column
        $wicket_str = '-';
        if(!empty($ball['Wicket Type'])){
            $wicket_str = $ball['Wicket Type'] . " b " . $bowler; // short bowler name
            if(!empty($ball['Fielder'])){
                $fielder = $conn->query("SELECT player_name FROM players WHERE user_id='{$ball['Fielder']}'")->fetch_assoc()['player_name'] ?? '';
                if($fielder) $wicket_str .= " c ".substr($fielder,0,8);
            }
        }

        $html .= "<tr>";
        $html .= "<td>".($index+1)."</td><td>".($ball['Run'] ?? '-')."</td><td>".($ball['Shot Type'] ?? '-')."</td>";
        $html .= "<td>".htmlspecialchars($bowler)."</td><td>".htmlspecialchars($striker)."</td>";
        $html .= "<td>".htmlspecialchars($wicket_str)."</td>";
        $html .= "<td>".($ball['Commentary'] ?? '-')."</td>";
        $html .= "</tr>";

        // Update previous striker
        $previous_striker_id = $ball['Striker'] ?? '';
    }
    $html .= "</table><br>";

}

// Output PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Match Report: ('.$team1_name.' - '.$team2_name.').pdf', 'I');
?>
