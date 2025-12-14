<?php
require_once('../../../tcpdf_min/tcpdf.php'); 
include '../../../config.php';

$match_id = $_GET['match_id'] ?? '';
if (!$match_id) die("Match ID required.");

// Fetch match
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

// ✅ PLAYERS TABLE
$html .= "<h4>Players in Match</h4>";
$html .= "<table><tr><th>$team1_name</th><th>$team2_name</th></tr>";

$t1 = $conn->query("SELECT player_name FROM players WHERE team_id='$team1_id'");
$t2 = $conn->query("SELECT player_name FROM players WHERE team_id='$team2_id'");

$t1_list=[]; $t2_list=[];
while($r=$t1->fetch_assoc()) $t1_list[]=$r['player_name'];
while($r=$t2->fetch_assoc()) $t2_list[]=$r['player_name'];

$max = max(count($t1_list),count($t2_list));
for($i=0;$i<$max;$i++){
    $html .= "<tr><td>".($t1_list[$i] ?? '-')."</td><td>".($t2_list[$i] ?? '-')."</td></tr>";
}
$html .= "</table>";

// ✅ INNINGS LOG
foreach($score_log['innings'] as $inn_no=>$inning){

    $attack = $conn->query("SELECT t_name FROM teams WHERE t_id='{$inning['attack_team']}'")->fetch_assoc()['t_name'];
    $defence = $conn->query("SELECT t_name FROM teams WHERE t_id='{$inning['defence_team']}'")->fetch_assoc()['t_name'];

    $html .= "<h4>Inning $inn_no (Attack: $attack | Defence: $defence)</h4>";
    $html .= "<b>Score:</b> $team1_name ({$inning['team1_points']}) - $team2_name ({$inning['team2_points']})<br><br>";

    // ✅ TAGS TABLE
    $html .= "<table>
        <tr>
            <th>Type</th>
            <th>Chaser</th>
            <th>Out Player</th>
            <th>Point Goes To</th>
            <th>Score</th>
        </tr>";

    foreach($inning['tags'] as $tag){

        $chaser = !empty($tag['chaser_player'])
            ? ($conn->query("SELECT player_name FROM players WHERE user_id='{$tag['chaser_player']}'")->fetch_assoc()['player_name'] ?? 'N/A') : '-';

        $out = !empty($tag['out_player'])
            ? ($conn->query("SELECT player_name FROM players WHERE user_id='{$tag['out_player']}'")->fetch_assoc()['player_name'] ?? 'N/A') : '-';

        $point_team = !empty($tag['point_taken_by'])
            ? ($conn->query("SELECT t_name FROM teams WHERE t_id='{$tag['point_taken_by']}'")->fetch_assoc()['t_name'] ?? '-') : '-';

        $type = $tag['type'] ?? 'Normal';

        $html .= "<tr>
            <td>$type</td>
            <td>$chaser</td>
            <td>$out</td>
            <td>$point_team</td>
            <td>{$tag['last score']}</td>
        </tr>";
    }
    $html .= "</table><br>";
}

// ===============================
// ✅ FINAL RESULT
// ===============================
$team1_name = $conn->query("SELECT t_name FROM teams WHERE t_id='{$score_log['team1']}'")->fetch_assoc()['t_name'] ?? 'Team 1';
$team2_name = $conn->query("SELECT t_name FROM teams WHERE t_id='{$score_log['team2']}'")->fetch_assoc()['t_name'] ?? 'Team 2';

$html .= "<h4>Final Result</h4>";
$html .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;width:50%;text-align:center;font-size:12pt;'>
<tr>
<th>".htmlspecialchars($team1_name)."</th>
<th>".htmlspecialchars($team2_name)."</th>
<th>Winner</th>
</tr>
<tr>
<td>".($score_log['team1_score'] ?? 0)."</td>
<td>".($score_log['team2_score'] ?? 0)."</td>
<td>".(!empty($score_log['winner']) 
    ? $conn->query("SELECT t_name FROM teams WHERE t_id='{$score_log['winner']}'")->fetch_assoc()['t_name'] 
    : '-')."</td>
</tr>
</table><br>";

$pdf->writeHTML($html);
$pdf->Output("Kho-Kho-Match-Report.pdf","I");
?>
