<?php
require_once('../../../tcpdf_min/tcpdf.php'); 
include '../../../config.php';

$match_id = $_GET['match_id'] ?? '';
if (!$match_id) die("Match ID required.");

// Fetch match
$match = $conn->query("SELECT * FROM matches WHERE match_id='$match_id'")->fetch_assoc();
if (!$match) die("Match not found.");

$score_log = json_decode($match['score_log'], true);

// Team IDs
$team1_id = $score_log['team1'];
$team2_id = $score_log['team2'];

// Team Names
$team1_name = $conn->query("SELECT t_name FROM teams WHERE t_id='$team1_id'")->fetch_assoc()['t_name'] ?? 'Team 1';
$team2_name = $conn->query("SELECT t_name FROM teams WHERE t_id='$team2_id'")->fetch_assoc()['t_name'] ?? 'Team 2';

// Winner Name
$winner_name = $conn->query("SELECT t_name FROM teams WHERE t_id='{$score_log['winner']}'")->fetch_assoc()['t_name'] ?? '-';
// ==============================
// ✅ EXTRA MATCH OVERVIEW DATA
// ==============================

// Creator Name
$creator = $conn->query("SELECT fname,lname FROM users WHERE user_id='{$match['created_by']}'")->fetch_assoc();
$creator_name = $creator ? $creator['fname'].' '.$creator['lname'] : 'Unknown';

// Helper: Resolve user by email
function getUserName($conn, $email){
    $row = $conn->query("SELECT fname,lname FROM users WHERE email='$email'")->fetch_assoc();
    return $row ? $row['fname'].' '.$row['lname'] : $email;
}

// Umpires, Scorers, Commentators
$umpires = $match['umpires'] ? implode(', ', array_map(fn($e)=>getUserName($conn,$e), json_decode($match['umpires'], true))) : '-';
$scorers = $match['scorers'] ? implode(', ', array_map(fn($e)=>getUserName($conn,$e), json_decode($match['scorers'], true))) : '-';
$commentators = $match['commentators'] ? implode(', ', array_map(fn($e)=>getUserName($conn,$e), json_decode($match['commentators'], true))) : '-';

// Tournament Name
$tournament_name = '-';
if(!empty($match['tournament'])){
    $tournament_name = $conn->query("SELECT tournament_name FROM tournaments 
        WHERE tournament_id='{$match['tournament']}'")->fetch_assoc()['tournament_name'] ?? '-';
}

// Toss Winner
$toss_winner_name = '-';
if(!empty($match['toss_winner'])){
    $toss_winner_name = $conn->query("SELECT t_name FROM teams 
        WHERE t_id='{$match['toss_winner']}'")->fetch_assoc()['t_name'] ?? '-';
}

// PDF Init
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetCreator('LiveStrike');
$pdf->SetTitle('Match Report');
$pdf->SetMargins(10,10,10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

// Style
$style = <<<CSS
<style>
h1 {text-align:center;color:#004085;}
h3 {text-align:center;color:red;}
table {border-collapse:collapse;width:100%;margin-bottom:10px;}
th, td {border:1px solid #000;padding:6px;text-align:center;}
th {background-color:#343a40;color:white;}
.green {background-color:#d4edda;}
</style>
CSS;

$html = $style;
$html .= "<h1>Match Report</h1>";
$html .= "<h3>$team1_name VS $team2_name</h3>";
// ==============================
// ✅ MATCH OVERVIEW TABLE
// ==============================

$html .= "<h3>Match Overview</h3>";
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

        case 'score_team_1': 
            $val = $score_log['team1_score'] ?? '-'; 
            break;

        case 'score_team_2': 
            $val = $score_log['team2_score'] ?? '-'; 
            break;

        case 'created_by': 
            $val = $creator_name; 
            break;

        case 'umpires': 
            $val = $umpires; 
            break;

        case 'scorers': 
            $val = $scorers; 
            break;

        case 'commentators': 
            $val = $commentators; 
            break;

        case 'tournament': 
            $val = $tournament_name; 
            break;

        case 'toss_winner': 
            $val = $toss_winner_name; 
            break;

        case 'winner': 
            $val = $winner_name; 
            break;

        default: 
            $val = $match[$f] ?? '-';
    }

    $row_class = ($f == 'winner') ? 'green' : '';
    $html .= "<tr class='$row_class'>
                <td>$label</td>
                <td>".htmlspecialchars($val)."</td>
              </tr>";
}

$html .= "</table><br>";

// Match Summary
$html .= "<h3>Players of Both Teams</h3>";
$html .= "<table>
<tr>
    <th>$team1_name</th>
    <th>$team2_name</th>
</tr>";

// Fetch players
$team1_players = $conn->query("SELECT player_name FROM players WHERE team_id='$team1_id' ORDER BY player_name ASC");
$team2_players = $conn->query("SELECT player_name FROM players WHERE team_id='$team2_id' ORDER BY player_name ASC");

// Store in arrays
$t1 = []; $t2 = [];
while($row = $team1_players->fetch_assoc()) $t1[] = $row['player_name'];
while($row = $team2_players->fetch_assoc()) $t2[] = $row['player_name'];

$max = max(count($t1), count($t2));

for($i = 0; $i < $max; $i++){
    $p1 = $t1[$i] ?? '-';
    $p2 = $t2[$i] ?? '-';

    $html .= "<tr>
        <td>".htmlspecialchars($p1)."</td>
        <td>".htmlspecialchars($p2)."</td>
    </tr>";
}

$html .= "</table><br><div></div>";
$html .= "<table>
<tr><th>Total Sets</th><th>Points Required</th><th>Winner</th></tr>
<tr>
<td>{$score_log['total_sets']}</td>
<td>{$score_log['points_required']}</td>
<td class='green'>$winner_name</td>
</tr></table>";

// Sets Summary
$html .= "<h3>Set Summary</h3>";
$html .= "<table><tr><th>Set</th><th>$team1_name</th><th>$team2_name</th><th>Winner</th></tr>";

foreach ($score_log['sets'] as $set_no => $set) {
    $set_winner = ($set['winner_team'] == $team1_id) ? $team1_name : $team2_name;

    $html .= "<tr>
        <td>Set $set_no</td>
        <td>{$set['team1_points']}</td>
        <td>{$set['team2_points']}</td>
        <td class='green'>$set_winner</td>
    </tr>";
}
$html .= "</table>";

// RALLY BY RALLY LOG
foreach ($score_log['sets'] as $set_no => $set) {

    $html .= "<h3>Set $set_no — Rally Log</h3>";
    $html .= "<table>
    <tr>
        <th>Rally</th>
        <th>$team1_name</th>
        <th>$team2_name</th>
        <th>Rally Winner</th>
        <th>Current Score</th>
        <th>Next Serve</th>
    </tr>";

    foreach ($set['rallies'] as $i => $rally) {

        $rally_winner = ($rally['winner_team'] == $team1_id) ? $team1_name : $team2_name;
        $serve_team   = ($rally['Curr_serve'] == $team1_id) ? $team1_name : $team2_name;

        $html .= "<tr>
            <td>".($i+1)."</td>
            <td>{$rally['team1_points']}</td>
            <td>{$rally['team2_points']}</td>
            <td>$rally_winner</td>
            <td>{$rally['last score']}</td>
            <td>$serve_team</td>
        </tr>";
    }

    $html .= "</table>";
}

// Sets Won Summary
$html .= "<h3>Final Sets Won</h3>";
$html .= "<table>
<tr><th>$team1_name</th><th>$team2_name</th></tr>
<tr>
<td>{$score_log['sets_won']['team1']}</td>
<td>{$score_log['sets_won']['team2']}</td>
</tr></table>";

// Output PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Match_Report_{$team1_name}_vs_{$team2_name}.pdf", "I");
?>
