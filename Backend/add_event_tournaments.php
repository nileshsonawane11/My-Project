<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config.php';

$event_id = $_GET['e'] ?? '';
$tournaments_raw = $_GET['tournaments'] ?? '';

if (empty($event_id) || empty($tournaments_raw)) {
    header("Location: ../Frontend/event-info.php?t=$event_id");
    exit;
}

// Clean IDs (important)
$selected_Tournaments = explode(',', $tournaments_raw);

print_r($selected_Tournaments);

// Prepare statement
$stmt = mysqli_prepare(
    $conn,
    "INSERT IGNORE INTO event_tournaments (event_id, tournament_id) VALUES (?, ?)"
);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

foreach ($selected_Tournaments as $tournament_id) {

    mysqli_stmt_bind_param($stmt, "ss", $event_id, $tournament_id);

    mysqli_stmt_execute($stmt);
}

mysqli_stmt_close($stmt);

// Redirect once
header("Location: ../Frontend/event-info.php?t=$event_id");
exit;
?>
