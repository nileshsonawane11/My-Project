<?php
header('Content-Type: application/json');
session_start();
include './config.php';
$data = file_get_contents('php://input');
$data = json_decode($data, true);

$role = mysqli_real_escape_string($conn, $data['role']);
$fname = mysqli_real_escape_string($conn, $data['fname']);
$lname = mysqli_real_escape_string($conn, $data['lname']);
$email = mysqli_real_escape_string($conn, $data['email']);
$password = mysqli_real_escape_string($conn, $data['password']);
$password2 = mysqli_real_escape_string($conn, $data['password2']);
$phone = mysqli_real_escape_string($conn, $data['phone']);
$otp = mysqli_real_escape_string($conn, $data['otp']);
$date = date('d-m-Y H:i:s'); // ✅ correct MySQL DATETIME format



if($fname == '' || $lname == '' || $email == '' || $password == '' || $password2 == ''  ||$otp == ''){
    echo json_encode(['status' => 400, 'message' => 'All fields are required', 'field' => 'empty']);
    exit();
}


if ($password !== $password2) {
    echo json_encode(['status' => 400, 'message' => 'Passwords do not match', 'field' => 'password']);
    exit();
}else if(!preg_match('/[A-Z]/', $password) || 
!preg_match('/[a-z]/', $password) || 
!preg_match('/[0-9]/', $password) || 
!preg_match('/[\W]/', $password)){
    echo json_encode(['status' => 400, 'message' => 'Password must include uppercase, lowercase, number, and special character', 'field' => 'password']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 409, 'message' => 'Enter Valid Email', 'field' => 'email']);
    exit();
}

$recipient_email = $_SESSION['recipient_email'];
if($otp != $_SESSION['otpregistration'][$recipient_email]) {
    echo json_encode(['status' => 409, 'message' => 'Invalid OTP', 'field' => 'otp']);
    exit();
}elseif(strlen($otp) < 4 || strlen($otp) > 4) {
    echo json_encode(['status' => 409, 'message' => 'Invalid OTP', 'field' => 'otp']);
    exit();
}


if (!preg_match('/^[0-9]{10}$/', $phone)) {
    echo json_encode(['status' => 409, 'message' => 'Enter Valid Phone Number', 'field' => 'phone']);
    exit();
}

$password = md5($password);

error_log("email=$email, phone=$phone");

$sql1 = "SELECT * FROM users WHERE email='$email' OR phone='$phone' LIMIT 1";
$result1 = mysqli_query($conn, $sql1);

if (mysqli_num_rows($result1) > 0) {
    while ($row = mysqli_fetch_assoc($result1)) {
        if ($row['email'] === $email) {
            echo json_encode(['status' => 409, 'message' => 'Email already Used', 'field' => 'email']);
            exit();
        }
    }
}

if(strlen($phone)>10 or strlen($phone)<10){
    echo json_encode(['status' => 409, 'message' => 'Phone must be 10 digits long', 'field' => 'phone']);
    exit();
}


$input = uniqid(microtime(true) . bin2hex(random_bytes(5)). $email . $date, true);
$id = hash('sha256', $input);


$sql = "INSERT INTO users (user_id,role, fname, lname, phone, date, password, email)
        VALUES ('$id','$role', '$fname', '$lname', '$phone', '$date', '$password', '$email')";

if (mysqli_query($conn, $sql)) {
    $_SESSION['user_id'] = $id;
    $_SESSION['role'] = $role;
    $_SESSION['email'] = $email;

    echo json_encode(['status' => 200, 'message' => 'User registered successfully', 'field' => 'success']);
} else {
    echo json_encode(['status' => 500, 'message' => 'Database error', 'field' => 'database']);
}

mysqli_close($conn);
?>
