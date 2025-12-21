<?php
header('Content-Type: application/json');
session_start();
require '../config.php'; // adjust path if needed

// 🔐 Optional: check login / role
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>400,'success' => false, 'message' => 'Invalid request']);
    exit;
}

$tournament_id = $_POST['tournament_id'] ?? '';
$event_id      = $_POST['event_id'] ?? '';

if (empty($tournament_id)) {
    echo json_encode(['status'=>400,'success' => false, 'message' => 'Missing tournament ID']);
    exit;
}


// 🗑️ Delete tournament
$del = mysqli_query(
    $conn,
    "DELETE FROM event_tournaments WHERE tournament_id = '$tournament_id' and event_id = '$event_id'"
);

if (!$del) {
    echo json_encode([
        'status'=>400,
        'success' => false,
        'message' => 'Database error',
        'error'   => mysqli_error($conn)
    ]);
    exit;
}

// ✅ Success
echo json_encode([
    'status'=>200,
    'success' => true,
    'message' => 'Tournament deleted successfully'
]);
