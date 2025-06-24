<?php
session_start();
include '../config.php';
$responses = [];
$matches = json_decode($_POST['matches'], true);
$tournament = $_POST['tournament'];
$admin_id = $_SESSION['user'];
$date = date('Y-m-d h:i:s');

$get_tour = mysqli_query($conn,"SELECT * FROM tournaments WHERE tournament_id = '$tournament'");
$tournament = mysqli_fetch_assoc($get_tour);

$sport = $tournament['sport_id'];
$venue = $tournament['city'];


foreach($matches as $match){
    $team1 = $match['team1'];
    $team2 = $match['team2'];
    $match_name = $match['match_no'].' | '.$tournament['tournament_name'];

    $input = uniqid(microtime(true) . bin2hex(random_bytes(5)). json_encode($match) . $date, true);
    $id = hash('sha256', $input);

    $checkSql = "SELECT * FROM matches 
               WHERE sport_id = '$sport' 
               AND (
                    (team_1 = '$team1' AND team_2 = '$team2') 
                    OR 
                    (team_1 = '$team2' AND team_2 = '$team1')
               )";
        $checkResult = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
                 $responses[] = ['status' => 409,'field' => 'datetime','message' => 'Match already exists!'];
        } else {
            $query = "INSERT INTO matches(match_id, sport_id, match_name, status, venue, team_1, team_2, created_by) values('$id','$sport','$match_name','Upcoming','$venue','$team1','$team2','$admin_id')";
            $result = mysqli_query($conn, $query);
            if($result){
                $responses[] = ['status' => 200,'field' => 'datetime','message' => 'Match added successfully'];
            }else{
                $responses[] = ['status' => 409,'field' => 'datetime','message' => 'Failed to add match'];
            }
        }
}
echo json_encode($responses);
exit();
// print_r($matches);
// print_r($tournament);
?>