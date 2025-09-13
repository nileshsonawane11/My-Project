<?php
session_start();
include '../../../config.php';

$quarters = $_POST['quarters'];
$match_id = $_POST['match_id'];

$row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `matches` WHERE `match_id` = '$match_id'"));

$total_quarters = $quarters;

            $score_log = [
                "match_id" => $match_id,
                "team1" => $row['team_1'],
                "team2" => $row['team_2'],
                "team1_score" => 0,
                "team2_score" => 0,
                "total_quarters" => $total_quarters,
                "current_quarter" => 1,
                "quarters" => [],
                "completed" => false
            ];

            // Generate quarters dynamically
            for ($i = 1; $i <= $total_quarters; $i++) {
                $score_log['quarters'][$i] = [
                    "team1_points" => 0,
                    "team2_points" => 0,
                    "team1_fouls" => 0,
                    "team2_fouls" => 0,
                    "timeouts" => [
                        "team1" => 0,
                        "team2" => 0
                    ],
                    "quarter_completed" => false,
                    "events" => []
                ];
            }

$history_array = [$score_log];  // this makes it an array of one element
$history_json = json_encode($history_array);
$json = json_encode($score_log);

if(empty($quarters)){
    echo json_encode(['status'=>409,'message'=>'Please fill required points','field'=>'data_empty']);
    exit();
}

$query = mysqli_query($conn,"UPDATE `matches` SET `toss_winner` = '$match_id', `toss_decision` = Null,`score_log` = '$json',`history` = '$history_json' WHERE `match_id` = '$match_id'");
if($query){
    echo json_encode(['status'=>200,'message'=>'Toss completed successfully','field'=>'team']);
    exit();
}
?>