<?php
ob_start();
session_start();
error_reporting(1);
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$team_id = $data['team_id'];

$sql1 ="SELECT * FROM users WHERE email='$email'";
$query1 = mysqli_query($conn,$sql1);
$row = mysqli_fetch_assoc($query1);
$name = $row['fname'].' '.$row['lname'];
$staff_id = $row['user_id'];
$id = $staff_id.$team_id;

// First, check how many staff are already in this team
$sql_count = "SELECT COUNT(*) as count FROM staff WHERE team = '$team_id'";
$query_count = mysqli_query($conn, $sql_count);
$count_data = mysqli_fetch_assoc($query_count);
$current_count = $count_data['count'];

// Check if team is full (assuming max 1 staff per team - adjust as needed)
if ($current_count >= 1) {
    echo json_encode(['status' => 409, 'message' => 'Staff limit reached in this team']);
    exit();
}

// Then check if this staff already exists in any team
$sql_check = "SELECT 1 FROM staff WHERE id = '$id'";
$query_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($query_check) > 0) {
    echo json_encode(['status' => 409, 'message' => 'Staff already exists in another team']);
    exit();
}

// If all checks pass, insert the new staff
$sql_insert = "INSERT INTO staff (id, staff_id, name, email, team) 
              VALUES ('$id', '$staff_id', '$name', '$email', '$team_id')";
$query_insert = mysqli_query($conn, $sql_insert);

if (mysqli_affected_rows($conn) > 0) {
    echo json_encode(['status' => 200, 'message' => 'Staff added to team']);
} else {
    echo json_encode(['status' => 500, 'message' => 'Failed to add staff']);
}

// echo json_encode(['email'=>$email,'Team'=>$team_id]);

?>