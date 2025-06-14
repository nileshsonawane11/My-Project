<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include '../config.php';

$method = $_POST['using'] ?? '';
$team = $_POST['team'] ?? '';


        $sql = "SELECT t_sport FROM teams WHERE t_id = '$team'";
        $query = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($query);
        $sport = $row['t_sport'];

        $sql2 = "SELECT sport_id FROM sports WHERE sport_name = '$sport'";
        $query2 = mysqli_query($conn,$sql2);
        $row2 = mysqli_fetch_assoc($query2);
        $sport_id = $row2['sport_id'];


if($method == 'email'){
    $players = $_POST['players'] ?? '';


    if(empty($players)){
        echo json_encode(array('status' => 409, 'message' => 'No players selected','field'=>'empty'));
        exit();
    }

        $values = [];

    foreach($players as $player){
        
        $checkSql = "SELECT * FROM players WHERE user_id = '$player' AND sport_id = '$sport_id' AND team_id = '$team'";
        $checkQuery = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkQuery) > 0) {
            echo json_encode(['status' => 400, 'message' => 'Player already exists in the team','for' => $player,'field'=>'block']);
            exit();
        }else{
            $getinfo = "SELECT * FROM users WHERE user_id = '$player'";
            $getquery = mysqli_query($conn,$getinfo);
            $row = mysqli_fetch_assoc($getquery);

            $name = $row['fname'].' '.$row['lname'];
            $email = $row['email'];
            $phone = $row['phone'];
            $image = $row['user_photo'];
            $values[] = "('$player', '$name', '$email', '$phone', '$sport_id', '$team', '$image')";
        }
    }

    if(!empty($values)){
        $insertSql = "INSERT INTO players (user_id, player_name, p_email, p_phone, sport_id, team_id, photo) VALUES " . implode(", ", $values);
        $insertQuery = mysqli_query($conn, $insertSql);
        if($insertQuery){
            echo json_encode(array('status' => 200, 'message' => 'Player added to team successfully','field'=>'empty'));
            exit();
        }else{
            echo json_encode(array('status' => 409, 'message' => 'Player not added to team','field'=>'empty'));
            exit();
        }
    }else{
        echo json_encode(array('status' => 409, 'message' => 'No player Selected','field'=>'empty'));
        exit();
    }

    exit();

}else if($method == 'info'){
    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $jno = $_POST['jno'] ?? NULL;
    $image = $_FILES['image'] ?? '';


    if(empty($fname) || empty($lname) || empty($email) || empty($phone)){
        echo json_encode(array('status' => 409, 'message' => 'All (*) marked fields are required','field'=>'info_empty'));
        exit();
    }

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $query = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($query);
    if(mysqli_num_rows($query) > 0){
        $user_id = $row['user_id'];
        $name = $row['fname'].' '.$row['lname'];
        $email = $row['email'];
        $phone = $row['phone'];
        $image = $row['user_photo'];

        $checkSql = "SELECT * FROM players WHERE user_id = '$user_id' AND sport_id = '$sport_id' AND team_id = '$team'";
        $checkQuery = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkQuery) > 0) {
            echo json_encode(['status' => 409, 'message' => 'Player already exists in the team','for' => $user_id,'field'=>'email']);
            exit();
        }


        $insertSql = "INSERT INTO players (user_id, player_name, p_email, p_phone, sport_id, team_id, jersey_no, photo) VALUES ('$user_id','$name','$email','$phone','$sport_id','$team','$jno','$image')";
        $insertQuery = mysqli_query($conn, $insertSql);
        if($insertQuery){
            echo json_encode(array('status' => 200, 'message' => 'Player added to team successfully'));
            exit();
        }else{
            echo json_encode(array('status' => 409, 'message' => 'Player not added to team','field'=>'info_empty'));
            exit();
        }
        
    }else{
        $checkSql = "SELECT * FROM players WHERE p_email = '$email' AND sport_id = '$sport_id' AND team_id = '$team'";
        $checkQuery = mysqli_query($conn, $checkSql);
        $row = mysqli_fetch_assoc($checkQuery);

        if (mysqli_num_rows($checkQuery) > 0) {
            $user_id = $row['user_id'];
            echo json_encode(['status' => 409, 'message' => 'Player already exists in the team','for' => $user_id,'field'=>'email']);
            exit();
        }

        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            echo json_encode(['status' => 409, 'message' => 'Enter Valid Phone Number', 'field' => 'phone']);
            exit();
        }

        $date = Date('d-m-Y / h:i:s A');
        $input = uniqid(microtime(true) . bin2hex(random_bytes(5)). $email . $date, true);
        $user_id = hash('sha256', $input);
        $name = $fname.' '.$lname;


        $image_file = ''; // default empty

        if (isset($image['name']) && $image['name'] != '') {
            $image_name = $user_id . '-' . basename($image['name']);
            $image_path = '../assets/images/users/' . $image_name;
            move_uploaded_file($image['tmp_name'], $image_path);
            $image_file = $image_name;
        }

        $insertSql = "INSERT INTO players (user_id, player_name, p_email, p_phone, sport_id, team_id, jersey_no, photo) VALUES ('$user_id','$name','$email','$phone','$sport_id','$team','$jno','$image_file')";
        $insertQuery = mysqli_query($conn, $insertSql);
        if($insertQuery){
            echo json_encode(array('status' => 200, 'message' => 'Player added to team successfully'));
            exit();
        }else{
            echo json_encode(array('status' => 409, 'message' => 'Player not added to team','field'=>'info_empty'));
            exit();
        }
    }

}
?>
