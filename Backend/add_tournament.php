<?php
session_start();
include '../config.php';
error_reporting(0);
header("Content-Type: application/json");

$tournament_name = $_POST['tournament_name'];
$city = $_POST['city'];
$ground = $_POST['ground'];
$organizer_name = $_POST['organizer_name'];
$organizer_number = $_POST['organizer_number'];
$organizer_email = $_POST['organizer_email'];
$tournament_date = $_POST['tournament_date'];
$tournament_time = $_POST['tournament_time'];
$sports_type = $_POST['sports_type'];
$winning_prize = $_POST['winning_prize'] ?? '';
$tournament_format = $_POST['tournament_format'] ?? '';
$additional_details = $_POST['additional_details'] ?? '';
$logo = $_FILES['logo'] ?? null;
$game = $_POST['game'] ?? '';
$sports = [
    'CRICKET'         => 1,
    'VOLLEYBALL'      => 2,
    'KABADDI'         => 3,
    'KHO-KHO'         => 4,
    'FOOTBALL'        => 5,
    'TENNIS'          => 6,
    'TABLE-TENNIS'    => 7,
    'CHESS'           => 8,
    'WEIGHT-LIFTING'  => 9,
    'BASKETBALL'      => 10
];
$game = $sports[$game];

if(empty($tournament_name) || empty($city) || empty($ground) || empty($organizer_name) || empty($organizer_number) || empty($organizer_email) || empty($tournament_date) || empty($tournament_time)){
    echo json_encode(['status' => 409, 'message' => 'All (*) marked fields are required', 'field' => 'exist']);
    exit();
}

if(empty($tournament_format)){
    echo json_encode(['status' => 409, 'message' => 'Tournament Format Required', 'field' => 'exist']);
    exit();
}

if (!empty($tournament_date) && !empty($tournament_time)) {
        // Combine date and time into one DateTime object
        $inputDateTime = strtotime($tournament_date . ' ' . $tournament_time);
        $currentDateTime = time();

        // Validate
        if ($inputDateTime > $currentDateTime){

            $user_id = $_SESSION['user'];

            // Check for duplicate tournament for this user
            $check = mysqli_query($conn, "SELECT * FROM tournaments WHERE tournament_name = '$tournament_name' AND tournament_date = '$tournament_date' AND sports_type = '$sports_type' AND created_by = '$user_id'");

            if(mysqli_num_rows($check) > 0){
                echo json_encode(['status' => 409, 'message' => 'You have already created this tournament for this date and sport.', 'field' => 'exist']);
                exit();
            }

            $input = uniqid(microtime(true) . bin2hex(random_bytes(5)). ($team1.$team2) . $date, true);
            $id = hash('sha256', $input);

            $logo_name = null;
            if($logo){
                $logo_name = $id.'-'.$logo['name'];
            }

            $query = mysqli_query($conn, "INSERT INTO `tournaments`(`tournament_id`,`sport_id`, `tournament_name`, `city`, `ground`, `organizer_name`, `organizer_number`, `organizer_email`, `tournament_date`, `tournament_time`, `sports_type`, `winning_prize`, `tournament_format`, `additional_details`, `logo`,`created_by`) VALUES ('$id','$game','$tournament_name','$city','$ground','$organizer_name','$organizer_number','$organizer_email','$tournament_date','$tournament_time','$sports_type','$winning_prize','$tournament_format','$additional_details','$logo_name','$user_id')");

            if($query){
                if(!empty($logo)){
                    $tmp_name = $logo['tmp_name'];
                    move_uploaded_file($tmp_name, "../assets/images/tournaments/$logo_name");
                }
                echo json_encode(['status' => 200, 'message' => 'Tournament added successfully', 'id' => $id,'field' => 'empty']);
                exit();
            }

        }else{
            echo json_encode(['status' => 409,'field' => 'datetime','message' => 'Date and time must be in the future.']);
            exit();
        }
}
?>