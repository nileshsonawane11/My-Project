<?php
ob_start();
session_start();
error_reporting(1);
header('Content-Type: application/json');
include '../config.php';

$team_name      = $_POST['teamName'];
$city           = $_POST['city'];
$country_code   = $_POST['code'];
$coordinaterNo  = $_POST['coordinaterNo'];
$coordinaterName= $_POST['coordinaterName'];
$logo           = $_FILES['logo'];
$game           = $_POST['game'];
$date           = date('YmdHis'); // safer format without spaces or slashes

// Validate required fields
if ($team_name == '' || $city == '' || $coordinaterNo == '' || $coordinaterName == '') {
    echo json_encode(['status' => 400, 'message' => 'All fields are required', 'field' => 'empty']);
    exit();
}

// Validate phone number - only digits, length exactly 10
if (!preg_match('/^[0-9]{10}$/', $coordinaterNo)) {
    echo json_encode(['status' => 409, 'message' => 'Enter a valid 10-digit phone number', 'field' => 'coordinaterNo']);
    exit();
}

$adminID = $_SESSION['user'] ?? null; // handle if session user not set

if (!$adminID) {
    echo json_encode(['status' => 401, 'message' => 'Unauthorized: User not logged in']);
    exit();
}

// Check if team already exists for this admin
$sql = "SELECT * FROM teams WHERE t_name = '$team_name' AND t_admin_id = '$adminID' AND t_sport = '$game'";
$query = mysqli_query($conn, $sql);
if (mysqli_fetch_assoc($query)) {
    echo json_encode(['status' => 400, 'message' => 'Team Already Exists', 'field' => 'teamName']);
    exit();
}

$logo_file = ''; // default empt

// Generate unique team ID safely
$input = uniqid(microtime(true) . bin2hex(random_bytes(5)) . $adminID . $date, true);
$t_id = hash('sha256', $input);
$coordinaterNo = $country_code.$coordinaterNo;

if (isset($logo['name']) && $logo['name'] != '') {
    $image_name = $t_id . '-' . basename($logo['name']);
    $logo_path = '../assets/images/teams/' . $image_name;
    move_uploaded_file($logo['tmp_name'], $logo_path);
    $logo_file = $image_name;
}

$sql1 = "INSERT INTO teams (t_id, t_name, t_city, t_coordinatorNo, t_coordinatorName, t_logo, t_admin_id, t_sport)
         VALUES ('$t_id', '$team_name', '$city', '$coordinaterNo', '$coordinaterName', '$logo_file', '$adminID', '$game')";
$query1 = mysqli_query($conn, $sql1);

if ($query1) {
    echo json_encode(['status' => 200, 'message' => 'Team added successfully', 'field' => 'teamName']);
    exit();
} else {
    echo json_encode(['status' => 400, 'message' => 'Team not added: ' . mysqli_error($conn), 'field' => 'teamName']);
    exit();
}
?>
