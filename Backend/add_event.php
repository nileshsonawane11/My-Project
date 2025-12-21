<?php
session_start();
include '../config.php';
error_reporting(0);
header("Content-Type: application/json");

$e_name           = $_POST['e_name'] ?? '';
$s_date           = $_POST['s_date'] ?? '';
$e_date           = $_POST['e_date'] ?? '';
$location         = $_POST['location'] ?? '';
$organizer        = $_POST['organizer'] ?? '';
$coordinator_name = $_POST['coordinator_name'] ?? '';
$coordinator_no   = $_POST['coordinator_no'] ?? '';
$email            = $_POST['email'] ?? '';
$event_type       = $_POST['event_type'] ?? '';
$sponsor          = $_POST['sponsor'] ?? '';
$description      = $_POST['description'] ?? '';

$owner = $_SESSION['user'];
$input = uniqid(microtime(true) . bin2hex(random_bytes(5)). ($e_name.$s_date.$e_date) . date('d-m-Y'), true);
$id = hash('sha256', $input);

// 2. Image upload
$photo_name = "";

if (isset($_FILES['e_photo']) && $_FILES['e_photo']['error'] == 0) {

    $folder = "../assets/images/events/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $ext = pathinfo($_FILES['e_photo']['name'], PATHINFO_EXTENSION);
    $photo_name = $id . "." . $ext;

    move_uploaded_file(
        $_FILES['e_photo']['tmp_name'],
        $folder . $photo_name
    );
}

// 3. Basic validation
if (
    empty($e_name) || empty($s_date) || empty($e_date) ||
    empty($location) || empty($organizer) || empty($coordinator_name) || 
    empty($email) || empty($event_type)
) {
    echo json_encode(['status' => 409, 'message' => 'All (*) marked fields are required', 'field' => 'err']);
    exit();
}

// 4. Date validation
if (strtotime($s_date) > strtotime($e_date)) {
    echo json_encode(['status' => 409, 'message' => 'Start date cannot be greater than end date', 'field' => 'err']);
    exit();
}

// 5. Insert query
$query = "INSERT INTO events (
            id, e_photo, e_name, s_date, e_date, location, organizer,
            coordinator_name, coordinator_no, email,
            event_type, sponsor, description, owner
          ) VALUES (
            '$id', '$photo_name', '$e_name', '$s_date', '$e_date', '$location', '$organizer',
            '$coordinator_name', '$coordinator_no', '$email',
            '$event_type', '$sponsor', '$description', '$owner'
          )";

// 6. Execute query
if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 200, 'message' => 'Success', 'field' => 'err']);
    exit();
} else {
    echo json_encode(['status' => 400, 'message' => 'Try Later', 'field' => 'err']);
    exit();
}

// 7. Close connection
mysqli_close($conn);
?>
