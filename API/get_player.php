<?php
header('Content-Type: application/json');
require 'db_connection.php';

$playerId = $_GET['id'] ?? '';

if (empty($playerId)) {
    echo json_encode(['error' => 'Player ID required']);
    exit;
}

// Using prepared statement for security
$stmt = $conn->prepare("SELECT player_name FROM players WHERE user_id = ? LIMIT 1");
$stmt->bind_param("s", $playerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['player_name' => $row['player_name']]);
} else {
    echo json_encode(['player_name' => 'Unknown Player']);
}
?>