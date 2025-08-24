<?php
error_reporting(1);
header('Content-Type: application/json');
require '../config.php';

// Expecting comma-separated IDs in ?ids=...
$idsParam = $_GET['ids'] ?? '';
if (empty($idsParam)) {
    echo json_encode(['error' => 'Player IDs required']);
    exit;
}

$idArray = explode(',', $idsParam);

// Sanitize and prepare placeholders for SQL
$idArray = array_filter($idArray);
$placeholders = implode(',', array_fill(0, count($idArray), '?'));

$types = str_repeat('s', count($idArray)); // all strings

$stmt = $conn->prepare("SELECT user_id, player_name FROM players WHERE user_id IN ($placeholders)");
$stmt->bind_param($types, ...$idArray);
$stmt->execute();
$result = $stmt->get_result();

$names = [];
while ($row = $result->fetch_assoc()) {
    $names[$row['user_id']] = $row['player_name'];
}

echo json_encode($names);
?>
