<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../config.php';

$team1 = $_POST['team1'];
$team2 = $_POST['team2'];
$game = $_POST['game'];
$game_date = $_POST['dateInput'];
$game_time = $_POST['timeInput'];
$game_location = $_POST['city'];
$game_type = $_POST['s_type'];
$game_over = $_POST['over'];
$ground = $_POST['ground'];
$Umpires = array_filter($_POST['Umpires'] ?? []);
$Scorers = array_filter($_POST['Scorers'] ?? []);
$Commentators = array_filter($_POST['Commentators'] ?? []);
$admin_id = $_SESSION['user'];
$date = Date('d-m-Y / h:i:s A');

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

$game = $sportList[$game] ?? null;

if(empty($game_location) || empty($ground) || empty($game_time) || empty($game_date) || empty($game_over)){
    echo json_encode(['status' => 409,'field' => 'empty','message' => 'All (*) Marked Fields Are Required']);
    exit();
}

if (!empty($game_date) && !empty($game_time)) {
        // Combine date and time into one DateTime object
        $inputDateTime = strtotime($game_date . ' ' . $game_time);
        $currentDateTime = time();

        // Validate
        if ($inputDateTime > $currentDateTime) {
            // if(empty($Commentators)){
            //     $Commentators[] = $_SESSION['email'];
            // }

            // if(empty($Scorers)){
            //     $Scorers[] = $_SESSION['email'];
            // }

            // if(empty($Umpires)){
            //     $Umpires[] = $_SESSION['email'];
            // }

            // echo json_encode(['status' => 200,'field' => 'datetime','message' => 'Date and Time are valid (in the future)']);

            $input = uniqid(microtime(true) . bin2hex(random_bytes(5)). ($team1.$team2) . $date, true);
            $id = hash('sha256', $input);

            $UmpiresJSON = json_encode($Umpires);
            $ScorersJSON = json_encode($Scorers);
            $CommentatorsJSON = json_encode($Commentators);

            $checkSql = "SELECT * FROM matches WHERE sport_id = '$game' AND (
                    (team_1 = '$team1' AND team_2 = '$team2') 
                    OR 
                    (team_1 = '$team2' AND team_2 = '$team1')
                    ) AND match_date = '$game_date' AND start_time = '$game_time'";
            $checkResult = mysqli_query($conn, $checkSql);

            if (mysqli_num_rows($checkResult) > 0) {
                echo json_encode(['status' => 409,'field' => 'datetime','message' => 'Match already exists!']);
                exit();
            } else {
                $pass = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 6);
                $sql = "INSERT INTO matches (match_id, sport_id, match_date, status, venue, team_1, team_2, start_time, umpires, scorers, commentators, password, created_by, overs) 
                        VALUES ('$id', '$game', '$game_date', 'Upcoming', '$game_location', '$team1', '$team2', '$game_time', '$UmpiresJSON', '$ScorersJSON', '$CommentatorsJSON', '$pass','$admin_id', '$game_over')";

                $query = mysqli_query($conn, $sql);
                if ($query){
                    echo json_encode(['status' => 200,'field' => 'success','message' => 'Match Scheduled Successfully','pass'=>$pass]);
                    exit();
                }
            }
        } else {
            echo json_encode(['status' => 409,'field' => 'datetime','message' => 'Date and time must be in the future.']);
            exit();
        }
}

echo json_encode(['team1' => $team1 , 'team2' => $team2, 'game' => $game, 'game_date' => $game_date, 'game_time' => $game_time, 'game_location' => $game_location, 'game_type' => $game_type, 'ground' => $ground, 'Umpires' => $Umpires, 'Scorers' => $Scorers, 'Commentators' => $Commentators]);
exit();

?>