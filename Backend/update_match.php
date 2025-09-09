<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../config.php';

// Escape input data
function escape($conn, $str) {
    return mysqli_real_escape_string($conn, trim($str));
}

// POST data
$team1Name    = escape($conn, $_POST['team1Name'] ?? '');
$team2Name    = escape($conn, $_POST['team2Name'] ?? '');
$matchVenue   = escape($conn, $_POST['matchVenue'] ?? '');
$matchCity    = escape($conn, $_POST['matchCity'] ?? '');
$matchDate    = escape($conn, $_POST['matchDate'] ?? '');
$matchTime    = escape($conn, $_POST['matchTime'] ?? '');
$matchPass    = escape($conn, $_POST['matchPass'] ?? '');
$match_id     = escape($conn, $_POST['match_id'] ?? '');

$Umpires      = array_filter($_POST['Umpires'] ?? []);
$Scorers      = array_filter($_POST['Scorers'] ?? []);
$Commentators = array_filter($_POST['Commentators'] ?? []);

$team1Logo    = $_FILES['team1Logo'] ?? null;
$team2Logo    = $_FILES['team2Logo'] ?? null;
$admin_id     = $_SESSION['user'] ?? '';

// Fetch team IDs
$match_sql = "SELECT team_1, team_2, sport_id FROM matches WHERE match_id = '$match_id'";
$match_result = mysqli_query($conn, $match_sql);
if (!$match_result || mysqli_num_rows($match_result) === 0) {
    echo json_encode(['status' => 404, 'message' => 'Match not found']);
    exit;
}
$match_row = mysqli_fetch_assoc($match_result);
$team1_id = $match_row['team_1'];
$team2_id = $match_row['team_2'];
$sport_id = $match_row['sport_id'];

// Sports mapping
$sportList = [
    "CRICKET"         => 1,
    "VOLLEYBALL"      => 2,
    "KABADDI"         => 3,
    "KHO-KHO"         => 4,
    "FOOTBALL"        => 5,
    "TENNIS"          => 6,
    "TABLE-TENNIS"    => 7,
    "CHESS"           => 8,
    "WEIGHT-LIFTING"  => 9,
    "BASKETBALL"      => 10
];
$sportIdToName = array_flip($sportList);
$sports_name = $sportIdToName[$sport_id] ?? 'Unknown Sport';

// Handle logos
$uploadDir = "../assets/images/teams/";
$logo1Name = '';
$logo2Name = '';

if (!empty($team1Logo['name'])) {
    $ext = pathinfo($team1Logo['name'], PATHINFO_EXTENSION);
    $logo1Name = $team1_id . '.' . $ext;
    move_uploaded_file($team1Logo['tmp_name'], $uploadDir . $logo1Name);
}

if (!empty($team2Logo['name'])) {
    $ext = pathinfo($team2Logo['name'], PATHINFO_EXTENSION);
    $logo2Name = $team2_id . '.' . $ext;
    move_uploaded_file($team2Logo['tmp_name'], $uploadDir . $logo2Name);
}

// Update teams
if (!empty($team1_id)) {
    $sql1 = "UPDATE teams SET t_name = '$team1Name'";
    if (!empty($logo1Name)) {
        $sql1 .= ", t_logo = '$logo1Name'";
    }
    $sql1 .= " WHERE t_id = '$team1_id'";
    mysqli_query($conn, $sql1);
}

if (!empty($team2_id)) {
    $sql2 = "UPDATE teams SET t_name = '$team2Name'";
    if (!empty($logo2Name)) {
        $sql2 .= ", t_logo = '$logo2Name'";
    }
    $sql2 .= " WHERE t_id = '$team2_id'";
    mysqli_query($conn, $sql2);
}

// Update match
$update_fields = [];

if (!empty($Umpires)) {
    // explode into array if only one element with commas
    if (count($Umpires) === 1 && strpos($Umpires[0], ',') !== false) {
        $Umpires = explode(',', $Umpires[0]);
    }

    // clean spaces
    $Umpires = array_map('trim', $Umpires);
    $umpires_json = json_encode(array_values($Umpires));
    $update_fields[] = "umpires = '$umpires_json'";
}

if (!empty($Scorers)) {
    // explode into array if only one element with commas
    if (count($Scorers) === 1 && strpos($Scorers[0], ',') !== false) {
        $Scorers = explode(',', $Scorers[0]);
    }

    // clean spaces
    $Scorers = array_map('trim', $Scorers);
    $scorers_json = json_encode(array_values($Scorers));
    $update_fields[] = "scorers = '$scorers_json'";
}

if (!empty($Commentators)) {
    // explode into array if only one element with commas
    if (count($Commentators) === 1 && strpos($Commentators[0], ',') !== false) {
        $Commentators = explode(',', $Commentators[0]);
    }

    // clean spaces
    $Commentators = array_map('trim', $Commentators);
    $commentators_json = json_encode(array_values($Commentators));
    $update_fields[] = "commentators = '$commentators_json'";
}

// Validate datetime
if (!empty($matchDate) && !empty($matchTime)) {
    $inputDateTime = strtotime("$matchDate $matchTime");
    if ($inputDateTime > time()) {

        // Prepare full update SQL
        $updateMatch = "UPDATE matches SET
            match_date = '$matchDate',
            venue = '$matchVenue',
            start_time = '$matchTime',
            password = '$matchPass'"
            . (!empty($update_fields) ? ", " . implode(", ", $update_fields) : "") .
            " WHERE match_id = '$match_id'";

        if (mysqli_query($conn, $updateMatch)) {
            echo json_encode([
                'status' => 200,
                'field' => 'success',
                'message' => 'Match Scheduled Successfully',
                'pass' => $matchPass,
                'game' => $sports_name
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'field' => 'database',
                'message' => 'Database error: ' . mysqli_error($conn)
            ]);
        }

    } else {
        echo json_encode(['status' => 409, 'field' => 'datetime', 'message' => 'Date and time must be in the future.']);
    }
} else {
    echo json_encode(['status' => 400, 'field' => 'datetime', 'message' => 'Missing date or time.']);
}
?>
