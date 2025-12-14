<?php
require_once('../../../tcpdf_min/tcpdf.php'); 
include '../../../config.php';

$match_id = $_GET['match_id'] ?? '';
if (!$match_id) die("Match ID required.");

// ===============================
// ✅ FETCH MATCH & SCORE LOG
// ===============================
$match_q = $conn->query("SELECT * FROM matches WHERE match_id='$match_id'");
$match = $match_q->fetch_assoc();
if (!$match) die("Match not found.");

$score_log = json_decode($match['score_log'], true);
if (!$score_log) die("Score log corrupted.");

// ===============================
// ✅ TEAM DETAILS
// ===============================
$team1_id = $score_log['team1'];
$team2_id = $score_log['team2'];

$team1_name = $conn->query("SELECT t_name FROM teams WHERE t_id='$team1_id'")->fetch_assoc()['t_name'] ?? 'Team 1';
$team2_name = $conn->query("SELECT t_name FROM teams WHERE t_id='$team2_id'")->fetch_assoc()['t_name'] ?? 'Team 2';

// ✅ Winner
$winner_name = '-';
if(!empty($score_log['winner'])){
    $winner_name = $conn->query("SELECT t_name FROM teams WHERE t_id='{$score_log['winner']}'")->fetch_assoc()['t_name'] ?? '-';
}

// ===============================
// ✅ SAFE DEFAULT VALUES (FIXED)
// ===============================
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

$toss_decision_name = $match['toss_decision'] ?? '';

// ===============================
// ✅ PDF INIT
// ===============================
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetCreator('LiveStrike');
$pdf->SetTitle('Kabaddi Match Report');
$pdf->SetMargins(10,10,10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

// ===============================
// ✅ STYLE
// ===============================
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
$html .= "<h1>Kabaddi Match Report</h1>";
$html .= "<h3>$team1_name VS $team2_name</h3>";

// ==============================
// ✅ MATCH OVERVIEW (EXACT REQUIRED FORMAT)
// ==============================
$html .= "<table><tr><th>Field</th><th>Value</th></tr>";

$fields = [
    'match_name'   => 'Match Name',
    'match_date'   => 'Date',
    'status'       => 'Status',
    'venue'        => 'Venue',
    'tournament'   => 'Tournament',
    'round'        => 'Round',
    'start_time'   => 'Start Time',
    'score_team_1' => "Score ({$team1_name})",
    'score_team_2' => "Score ({$team2_name})",
    'toss_winner'  => 'Toss Winner',
    'toss_decision'=> 'Toss Decision',
    'winner'       => 'Winner',
    'created_by'   => 'Created By',
    'umpires'      => 'Umpires',
    'scorers'      => 'Scorers',
    'commentators' => 'Commentators'
];

foreach($fields as $f => $label){

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

        case 'toss_decision': 
            $val = $toss_decision_name; 
        break;

        case 'winner': 
            $val = $winner_name; 
        break;

        case 'status':
            $val = $score_log['match_completed'] ? 'Completed' : 'Running';
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

// ===============================
// ✅ HALF-WISE SUMMARY
// ===============================
$html .= "<h3>Half Summary</h3>";
$html .= "<table>
<tr>
<th>Half</th>
<th>$team1_name</th>
<th>$team2_name</th>
<th>Status</th>
</tr>";

foreach($score_log['halves'] as $half_no => $half){
    $status = $half['half_completed'] ? 'Completed' : 'Completed';

    $html .= "<tr>
        <td>Half $half_no</td>
        <td>{$half['team1_points']}</td>
        <td>{$half['team2_points']}</td>
        <td>$status</td>
    </tr>";
}
$html .= "</table><br>";
// Players table for both teams
$html .= "<hr><h3>Players in Match</h3>";
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

// ===============================
// ✅ RAID BY RAID LOG
// ===============================
foreach($score_log['halves'] as $half_no => $half){

    $html .= "<h3>Half $half_no - Raid Log</h3>";
    $html .= "<table>
    <tr>
        <th>Raid</th>
        <th>Raider</th>
        <th>Raid Team</th>
        <th>Raid Pts</th>
        <th>Def Pts</th>
        <th>$team1_name</th>
        <th>$team2_name</th>
        <th>Score</th>
    </tr>";

    foreach($half['raids'] as $i => $raid){

        $raid_team = ($raid['current_raid_team'] == $team1_id) ? $team1_name : $team2_name;

        $html .= "<tr>
            <td>".($i+1)."</td>
            <td>{$raid['raider']}</td>
            <td>$raid_team</td>
            <td>{$raid['raid points']}</td>
            <td>{$raid['def points']}</td>
            <td>{$raid['team1_points']}</td>
            <td>{$raid['team2_points']}</td>
            <td>{$raid['last score']}</td>
        </tr>";
    }

    $html .= "</table>";
}

// ===============================
// ✅ FINAL RESULT
// ===============================
$html .= "<h3>Final Result</h3>";
$html .= "<table>
<tr>
<th>$team1_name</th>
<th>$team2_name</th>
</tr>
<tr>
<td>{$score_log['team1_score']}</td>
<td>{$score_log['team2_score']}</td>
</tr>
</table>";

// ===============================
// ✅ OUTPUT PDF
// ===============================
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Kabaddi_Match_Report_{$team1_name}_vs_{$team2_name}.pdf", "I");
?>
