<?php
    session_start();
    include '../config.php';

    if(!isset($_SESSION['user'])){
        header('location: ./front-page.php');
        exit();
    }
    if($_SESSION['role'] == "User"){
        header('location: ../dashboard.php?update="live"&sport="CRICKET"');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && isset($_GET['mem'])) {
    $id = $_POST['delete_id'];
    $type = $_GET['mem']; // 'Player' or 'Staff'

    // Determine table and column
    if ($type === 'Player') {
        $table = 'players';
        $column = 'user_id';
    } elseif ($type === 'Staff') {
        $table = 'staff';
        $column = 'staff_id';
    } else {
        echo 'invalid_type';
        exit;
    }

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$column` = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>Team Info</title>
<style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
        }
        
        /* Theme Variables */
        :root {
            --primary-color: #d1221fff;
            --primary-light: rgba(209, 34, 31, 0.8);
            --primary-dark: rgba(160, 25, 23, 1);
            --background: #ffffff;
            --card-bg: #ffffff;
            --text-dark: #000000;
            --text-light: #333333;
            --border-color: #e0e0e0;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --svg-fill: #000000;
            --hover-bg: rgba(209, 34, 31, 0.08);
            --input-bg: #ffffff;
            --menu-bg: #F9F9F9;
            --event-bg: #EDEDED;
            --inactive-text: #a4a2a2;
            --orange: #d1221fff;
        }

        /* Dark theme variables */
        [data-theme="dark"] {
            --background: #121212;
            --card-bg: #1e1e1e;
            --text-dark: #ffffff;
            --text-light: #e0e0e0;
            --border-color: #333333;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            --svg-fill: #ffffff;
            --hover-bg: rgba(209, 34, 31, 0.15);
            --input-bg: #2a2a2a;
            --menu-bg: #2d2d2d;
            --event-bg: #3d3d3d;
            --inactive-text: #777777;
            --orange: #ff0000ff;
        }
        
        body{
            height: -webkit-fill-available;
            background: var(--card-bg);
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .return{
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
            padding: 40px 0 0 40px;
        }
        .return svg{
            cursor: pointer;
            fill: var(--svg-fill);
        }

        svg path {
            fill: var(--text-dark);
        }
        .txt{
            line-height: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            padding-left: 40px;
            color: var(--text-dark);
        }
        .content{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 30px;
            box-shadow: 0 6px 5px -5px rgba(0, 0, 0, 0.3);
        }
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
        }
        .logo-name{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
        }
        .logo-img{
            height: 120px;
            width: 120px;
            border-radius: 50%;
            background: var(--hover-bg);
            overflow: hidden;
        }
        .logo-img img{
            height: 100%;
            width: 100%;
            object-fit:cover;
        }
        .logo-info{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: var(--text-dark);
        }
        .other-info{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            line-height: 25px;
            color: var(--text-dark);
        }
        .menubar{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .info-section{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            flex-direction: row;
        }
        .dropdown{
            width: 340px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 17px;
            justify-content: flex-start;
            max-height: 60px;
            background: var(--menu-bg);
            border: solid var(--border-color) 1px;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.5s ease-in-out;
            color: var(--text-dark);
        }
        .dropdown.active{
            max-height: 200px;
        }
        .menu{
            display: flex;
            justify-content: space-between;
            align-items: center;
            align-content: center;
            flex-direction: row;
            width: 100%;
            height: 60px;
            padding:0 10px;
            min-height: 60px;
        }
        .drop-icons{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: row;
            gap: 10px;
            height: 100%;
        }
        .data{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 15px;
            padding: 0px 0px 28px 60px;
        }
        .list{
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            color: var(--text-dark);
        }
        .slider{
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotateX(180deg);
            cursor: pointer;
            transition: transform 0.4s ease-in-out;
        }
        .slider svg {
            fill: var(--svg-fill);
        }
        .slider.active{
            transform: rotateX(0deg);
        }
        .no-event img{
            width: 300px;
            height: auto;
            margin: 20px;
        }
        .no-event{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            width: 100%;
            color: var(--text-dark);
        }
        .Events{
            width: 100%;
            height: max-content;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            flex-direction: column;
            gap: 20px;
        }
        .new-btn{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .data-info{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 40px;
        }
        .event{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 5px;
            width: 100%;
            color: var(--text-dark);
        }
        .schedule-time{
            padding-left: 30px;
            color: var(--text-dark);
        }
        .opponent{
            font-size: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            padding: 0 15px;
            background: var(--event-bg);
            height: 50px;
            gap: 20px;
            color: var(--text-dark);
        }
        .sec{
            padding-bottom: 10px;
            cursor: pointer;
            color: var(--text-dark);
        }
        .sec.active{
            border-bottom: solid 5px var(--inactive-text);
        }
        .members{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mem-container{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            height: 30px;
            border: solid 1px var(--orange);
            overflow: hidden;
            border-radius: 80px;
            color: var(--orange);
        }
        .mem-type{
            width: 120px;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .mem-type.active{
            background: var(--orange);
            color: white;
        }
        .mem-info{
            width: 100%;
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            justify-content: center;
        }
        .add-member{
            font-size: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
            padding: 0 30px;
            height: 50px;
            gap: 20px;
            color: var(--orange);
        }

        .add-member svg path {
            fill: var(--orange);
        }
        .add-icon{
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .add-icon svg {
            fill: var(--svg-fill);
        }
        .mem{
            display: flex;
            width: 90%;
            border-radius: 20px;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            height: 80px;
            background: var(--event-bg);
            padding-left: 15px;
        }
        .mem-list{
                width: 100%;
                height: max-content;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                gap: 30px;
                overflow: hidden;
        }
        .mem-img{
            width: 60px;
            height: 60px;
            background: var(--hover-bg);
            border-radius: 50%;
            margin-left: 10px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .mem-img img{
            margin-left: 0px;
        }
        .mem-head{
            width: 100%;
            text-align: left;
            margin-left : 15px;
            color: var(--text-dark);
        }
        .list-items{
            cursor: pointer;
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            color: var(--text-dark);
        }
        .error{
            display: none;
            color: var(--primary-color); 
            width:100%;
            font-size:12px;
            margin: 5px;
            transition : all 0.3 ease-in-out;
        }
        #playerMenu {
        position: absolute;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        padding: 8px;
        z-index: 9999;
        display: none;
        min-width: 120px;
        box-shadow: var(--shadow);
        color: var(--text-dark);
        }
        #playerMenu div {
        padding: 5px;
        cursor: pointer;
        }
        #playerMenu div:hover {
        background-color: var(--hover-bg);
        }

        @media (min-width:601px) {
                    .container{
                        display: flex;
                        background-color: var(--card-bg);
                        position: relative;
                        width: 90%;
                        max-width: 100%;
                        min-height: 480px;
                        align-items: center;
                        justify-content: flex-start;
                        flex-direction: column;
                        gap: 30px;
                        
                    }
                    .container2{
                        gap: 40px;
                        width: 70%;
                    }
                    
                    .new-btn button{
                        background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                        color: #fff;
                        font-size: 12px;
                        padding: 10px 45px;
                        border: 1px solid transparent;
                        border-radius: 8px;
                        font-weight: 600;
                        letter-spacing: 0.5px;
                        text-transform: uppercase;
                        cursor: pointer;
                        height: 40px;
                        width: 300px;
                    }
                    .Events{
                        display: grid;
                        justify-content: start;
                        align-items: center;
                        justify-items: start;
                        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                        gap: 20px;
                    }
                    .opponent{
                        border-radius: 15px;
                        box-shadow: var(--shadow);
                    }
                }

        @media(max-width:601px){
                    .container{
                        display: flex;
                        background-color: var(--card-bg);
                        position: relative;
                        width: 768px;
                        z-index: 0;
                        max-width: 100%;
                        align-items: flex-start;
                        justify-content: flex-start;
                        flex-direction: column;
                        flex-wrap: wrap;
                        gap: 30px;
                        scrollbar-width: none;
                        
                    }
                    .container2{
                        width: 100%;
                        gap: 20px;
                        height: 60%;
                        justify-content: space-between;
                    }
                    
                    .new-btn button{
                        background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                        color: #fff;
                        font-size: 12px;
                        padding: 10px 45px;
                        border: 1px solid transparent;
                        border-radius: 8px;
                        font-weight: 600;
                        letter-spacing: 0.5px;
                        text-transform: uppercase;
                        cursor: pointer;
                        width: 60%;
                        height: 45px;
                    }
                    
                }
</style>
</head>
<body>
    <div class="container">
        <div class="return" >
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div></div>
        </div>

        
            <div class="container2">
                <div class="txt">
                    <div>
                        <label for="">My Teams</label>
                        <h4>Manage Teams</h4>
                    </div>
                    <div>
                    
                    </div>
                </div>
                <?php
                    $team = $_GET['t'];
                    $sql = "SELECT * FROM teams WHERE t_id = '$team'";
                    $query = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($query);
                    if($row['t_logo']){
                        $src = "../assets/images/teams/".$row['t_logo'];
                    }else{
                        $src = "https://cdn-icons-png.flaticon.com/512/8140/8140303.png";
                    }
                ?>
                <div class="content">
                    <div class="logo-name">
                        <div class="logo-img">
                            <img src="<?php echo $src; ?>" alt="">
                        </div>
                        <div class="logo-info">
                            <h4><?php echo $row['t_name'].' ('.$row['t_sport'].')'; ?></h4>
                            <div class="other-info">
                                <label for="coordinator" class=""><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg>
                                <?php echo $row['t_coordinatorName'].' '.'('.$row['t_coordinatorNo'].')'; ?></label>
                                <label for="place" class=""><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg>
                                <?php echo $row['t_city'] ?></label>
                            </div>
                        </div>
                        
                    </div>
                    <div class="menubar">
                            <div class="dropdown">
                                <div class="menu">
                                        <div class="drop-icons"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                                        </svg><label for="">Complete Team Setup</label></div>
                                        <div class="drop-icons"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-3-circle-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-8.082.414c.92 0 1.535.54 1.541 1.318.012.791-.615 1.36-1.588 1.354-.861-.006-1.482-.469-1.54-1.066H5.104c.047 1.177 1.05 2.144 2.754 2.144 1.653 0 2.954-.937 2.93-2.396-.023-1.278-1.031-1.846-1.734-1.916v-.07c.597-.1 1.505-.739 1.482-1.876-.03-1.177-1.043-2.074-2.637-2.062-1.675.006-2.59.984-2.625 2.12h1.248c.036-.556.557-1.054 1.348-1.054.785 0 1.348.486 1.348 1.195.006.715-.563 1.237-1.342 1.237h-.838v1.072h.879Z"/>
                                        </svg><div class="slider" onclick="open_dropdown()"><svg width="18" height="10" viewBox="0 0 18 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.5 1L9 8.5L16.5 1" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg></div>
                                        </div>
                                </div>

                                <?php
                                    $sql_count_staff = "SELECT COUNT(*) as count FROM staff WHERE team = '$team'";
                                    $query_count_staff = mysqli_query($conn, $sql_count_staff);
                                    $count_staff_data = mysqli_fetch_assoc($query_count_staff);
                                    $current_staff_count = $count_staff_data['count'];

                                    $sql_count_player = "SELECT COUNT(*) as count FROM players WHERE team_id = '$team'";
                                    $query_count_player = mysqli_query($conn, $sql_count_player);
                                    $count_player_data = mysqli_fetch_assoc($query_count_player);
                                    $current_player_count = $count_player_data['count'];

                                    $sql_max_players = "SELECT s.Total_players FROM sports s join teams t ON s.sport_name =  t.t_sport WHERE t.t_id = '$team'";
                                    $query_max_players = mysqli_query($conn,$sql_max_players);
                                    $Max_players = mysqli_fetch_assoc($query_max_players);
                                    $team_size = $Max_players['Total_players'];
                                ?>
                                <div class="data">
                                        <div class="list"><div onclick="add_member()" class="list-items"><svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.5 0.0634766C8.19271 0.0634766 8.85677 0.152018 9.49219 0.329102C10.1276 0.506185 10.7266 0.756185 11.2891 1.0791C11.8516 1.40202 12.3568 1.79264 12.8047 2.25098C13.2526 2.70931 13.6432 3.21712 13.9766 3.77441C14.3099 4.33171 14.5625 4.92806 14.7344 5.56348C14.9062 6.19889 14.9948 6.86556 15 7.56348C15 8.25618 14.9115 8.92025 14.7344 9.55566C14.5573 10.1911 14.3073 10.79 13.9844 11.3525C13.6615 11.915 13.2708 12.4202 12.8125 12.8682C12.3542 13.3161 11.8464 13.7067 11.2891 14.04C10.7318 14.3734 10.1354 14.626 9.5 14.7979C8.86458 14.9697 8.19792 15.0583 7.5 15.0635C6.80729 15.0635 6.14323 14.9749 5.50781 14.7979C4.8724 14.6208 4.27344 14.3708 3.71094 14.0479C3.14844 13.7249 2.64323 13.3343 2.19531 12.876C1.7474 12.4176 1.35677 11.9098 1.02344 11.3525C0.690104 10.7952 0.4375 10.1989 0.265625 9.56348C0.09375 8.92806 0.00520833 8.26139 0 7.56348C0 6.87077 0.0885417 6.20671 0.265625 5.57129C0.442708 4.93587 0.692708 4.33691 1.01562 3.77441C1.33854 3.21191 1.72917 2.70671 2.1875 2.25879C2.64583 1.81087 3.15365 1.42025 3.71094 1.08691C4.26823 0.753581 4.86458 0.500977 5.5 0.329102C6.13542 0.157227 6.80208 0.0686849 7.5 0.0634766ZM7.5 14.0635C8.09896 14.0635 8.67448 13.9854 9.22656 13.8291C9.77865 13.6729 10.2943 13.4541 10.7734 13.1729C11.2526 12.8916 11.6927 12.5531 12.0938 12.1572C12.4948 11.7614 12.8333 11.3239 13.1094 10.8447C13.3854 10.3656 13.6042 9.84733 13.7656 9.29004C13.9271 8.73275 14.0052 8.15723 14 7.56348C14 6.96452 13.9219 6.389 13.7656 5.83691C13.6094 5.28483 13.3906 4.76921 13.1094 4.29004C12.8281 3.81087 12.4896 3.37077 12.0938 2.96973C11.6979 2.56868 11.2604 2.23014 10.7812 1.9541C10.3021 1.67806 9.78385 1.45931 9.22656 1.29785C8.66927 1.13639 8.09375 1.05827 7.5 1.06348C6.90104 1.06348 6.32552 1.1416 5.77344 1.29785C5.22135 1.4541 4.70573 1.67285 4.22656 1.9541C3.7474 2.23535 3.30729 2.57389 2.90625 2.96973C2.50521 3.36556 2.16667 3.80306 1.89062 4.28223C1.61458 4.76139 1.39583 5.27962 1.23438 5.83691C1.07292 6.39421 0.994792 6.96973 1 7.56348C1 8.16243 1.07812 8.73796 1.23438 9.29004C1.39062 9.84212 1.60938 10.3577 1.89062 10.8369C2.17188 11.3161 2.51042 11.7562 2.90625 12.1572C3.30208 12.5583 3.73958 12.8968 4.21875 13.1729C4.69792 13.4489 5.21615 13.6676 5.77344 13.8291C6.33073 13.9906 6.90625 14.0687 7.5 14.0635ZM8 7.06348H12V8.06348H8V12.0635H7V8.06348H3V7.06348H7V3.06348H8V7.06348Z" fill="black"/>
                                        </svg><label for="">Add a Staff Member (<?php echo $current_staff_count; ?>/1)</label></div>
                                        </div>
                                        <div class="list"><div onclick="add_player()" class="list-items"><svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.5 0.0634766C8.19271 0.0634766 8.85677 0.152018 9.49219 0.329102C10.1276 0.506185 10.7266 0.756185 11.2891 1.0791C11.8516 1.40202 12.3568 1.79264 12.8047 2.25098C13.2526 2.70931 13.6432 3.21712 13.9766 3.77441C14.3099 4.33171 14.5625 4.92806 14.7344 5.56348C14.9062 6.19889 14.9948 6.86556 15 7.56348C15 8.25618 14.9115 8.92025 14.7344 9.55566C14.5573 10.1911 14.3073 10.79 13.9844 11.3525C13.6615 11.915 13.2708 12.4202 12.8125 12.8682C12.3542 13.3161 11.8464 13.7067 11.2891 14.04C10.7318 14.3734 10.1354 14.626 9.5 14.7979C8.86458 14.9697 8.19792 15.0583 7.5 15.0635C6.80729 15.0635 6.14323 14.9749 5.50781 14.7979C4.8724 14.6208 4.27344 14.3708 3.71094 14.0479C3.14844 13.7249 2.64323 13.3343 2.19531 12.876C1.7474 12.4176 1.35677 11.9098 1.02344 11.3525C0.690104 10.7952 0.4375 10.1989 0.265625 9.56348C0.09375 8.92806 0.00520833 8.26139 0 7.56348C0 6.87077 0.0885417 6.20671 0.265625 5.57129C0.442708 4.93587 0.692708 4.33691 1.01562 3.77441C1.33854 3.21191 1.72917 2.70671 2.1875 2.25879C2.64583 1.81087 3.15365 1.42025 3.71094 1.08691C4.26823 0.753581 4.86458 0.500977 5.5 0.329102C6.13542 0.157227 6.80208 0.0686849 7.5 0.0634766ZM7.5 14.0635C8.09896 14.0635 8.67448 13.9854 9.22656 13.8291C9.77865 13.6729 10.2943 13.4541 10.7734 13.1729C11.2526 12.8916 11.6927 12.5531 12.0938 12.1572C12.4948 11.7614 12.8333 11.3239 13.1094 10.8447C13.3854 10.3656 13.6042 9.84733 13.7656 9.29004C13.9271 8.73275 14.0052 8.15723 14 7.56348C14 6.96452 13.9219 6.389 13.7656 5.83691C13.6094 5.28483 13.3906 4.76921 13.1094 4.29004C12.8281 3.81087 12.4896 3.37077 12.0938 2.96973C11.6979 2.56868 11.2604 2.23014 10.7812 1.9541C10.3021 1.67806 9.78385 1.45931 9.22656 1.29785C8.66927 1.13639 8.09375 1.05827 7.5 1.06348C6.90104 1.06348 6.32552 1.1416 5.77344 1.29785C5.22135 1.4541 4.70573 1.67285 4.22656 1.9541C3.7474 2.23535 3.30729 2.57389 2.90625 2.96973C2.50521 3.36556 2.16667 3.80306 1.89062 4.28223C1.61458 4.76139 1.39583 5.27962 1.23438 5.83691C1.07292 6.39421 0.994792 6.96973 1 7.56348C1 8.16243 1.07812 8.73796 1.23438 9.29004C1.39062 9.84212 1.60938 10.3577 1.89062 10.8369C2.17188 11.3161 2.51042 11.7562 2.90625 12.1572C3.30208 12.5583 3.73958 12.8968 4.21875 13.1729C4.69792 13.4489 5.21615 13.6676 5.77344 13.8291C6.33073 13.9906 6.90625 14.0687 7.5 14.0635ZM8 7.06348H12V8.06348H8V12.0635H7V8.06348H3V7.06348H7V3.06348H8V7.06348Z" fill="black"/>
                                        </svg><label for="">Add Your Players (<?php echo $current_player_count; ?>/<?php echo $team_size; ?>)</label></div>
                                        </div>
                                        <div class="list"><div onclick="add_event()" class="list-items"><svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.5 0.0634766C8.19271 0.0634766 8.85677 0.152018 9.49219 0.329102C10.1276 0.506185 10.7266 0.756185 11.2891 1.0791C11.8516 1.40202 12.3568 1.79264 12.8047 2.25098C13.2526 2.70931 13.6432 3.21712 13.9766 3.77441C14.3099 4.33171 14.5625 4.92806 14.7344 5.56348C14.9062 6.19889 14.9948 6.86556 15 7.56348C15 8.25618 14.9115 8.92025 14.7344 9.55566C14.5573 10.1911 14.3073 10.79 13.9844 11.3525C13.6615 11.915 13.2708 12.4202 12.8125 12.8682C12.3542 13.3161 11.8464 13.7067 11.2891 14.04C10.7318 14.3734 10.1354 14.626 9.5 14.7979C8.86458 14.9697 8.19792 15.0583 7.5 15.0635C6.80729 15.0635 6.14323 14.9749 5.50781 14.7979C4.8724 14.6208 4.27344 14.3708 3.71094 14.0479C3.14844 13.7249 2.64323 13.3343 2.19531 12.876C1.7474 12.4176 1.35677 11.9098 1.02344 11.3525C0.690104 10.7952 0.4375 10.1989 0.265625 9.56348C0.09375 8.92806 0.00520833 8.26139 0 7.56348C0 6.87077 0.0885417 6.20671 0.265625 5.57129C0.442708 4.93587 0.692708 4.33691 1.01562 3.77441C1.33854 3.21191 1.72917 2.70671 2.1875 2.25879C2.64583 1.81087 3.15365 1.42025 3.71094 1.08691C4.26823 0.753581 4.86458 0.500977 5.5 0.329102C6.13542 0.157227 6.80208 0.0686849 7.5 0.0634766ZM7.5 14.0635C8.09896 14.0635 8.67448 13.9854 9.22656 13.8291C9.77865 13.6729 10.2943 13.4541 10.7734 13.1729C11.2526 12.8916 11.6927 12.5531 12.0938 12.1572C12.4948 11.7614 12.8333 11.3239 13.1094 10.8447C13.3854 10.3656 13.6042 9.84733 13.7656 9.29004C13.9271 8.73275 14.0052 8.15723 14 7.56348C14 6.96452 13.9219 6.389 13.7656 5.83691C13.6094 5.28483 13.3906 4.76921 13.1094 4.29004C12.8281 3.81087 12.4896 3.37077 12.0938 2.96973C11.6979 2.56868 11.2604 2.23014 10.7812 1.9541C10.3021 1.67806 9.78385 1.45931 9.22656 1.29785C8.66927 1.13639 8.09375 1.05827 7.5 1.06348C6.90104 1.06348 6.32552 1.1416 5.77344 1.29785C5.22135 1.4541 4.70573 1.67285 4.22656 1.9541C3.7474 2.23535 3.30729 2.57389 2.90625 2.96973C2.50521 3.36556 2.16667 3.80306 1.89062 4.28223C1.61458 4.76139 1.39583 5.27962 1.23438 5.83691C1.07292 6.39421 0.994792 6.96973 1 7.56348C1 8.16243 1.07812 8.73796 1.23438 9.29004C1.39062 9.84212 1.60938 10.3577 1.89062 10.8369C2.17188 11.3161 2.51042 11.7562 2.90625 12.1572C3.30208 12.5583 3.73958 12.8968 4.21875 13.1729C4.69792 13.4489 5.21615 13.6676 5.77344 13.8291C6.33073 13.9906 6.90625 14.0687 7.5 14.0635ZM8 7.06348H12V8.06348H8V12.0635H7V8.06348H3V7.06348H7V3.06348H8V7.06348Z" fill="black"/>
                                        </svg><label for="">Add Events (0/10)</label></div>
                                </div>
                                
                            </div>
                        </div>
                </div>
                <div class="info-section">
                    <div class="sec schedule active">Schedule</div>
                    <div class="sec team">Team</div>
                    <div class="sec stats">Stats</div>
                </div>
            </div>
        
            <div class="data-info">
                
            </div>
    </div>


    <div id="playerMenu" style="display:none; position:absolute; background:#fff; border:1px solid #000; padding:10px; z-index:1000;">
        <div id="editBtn">✏️ Edit</div>
        <div id="deleteBtn">🗑️ Delete</div>
    </div>
    
    <script>
        const staff_count = <?php echo $current_staff_count; ?>;
        const player_count = <?php echo $current_player_count; ?>;
        const team_size = <?php echo $team_size; ?>;
        console.log(staff_count)
        const urlParams = new URLSearchParams(window.location.search);
        const team = urlParams.get('t');
        const slider = document.querySelector(".slider");
        const dropdown = document.querySelector(".dropdown");

        let open_dropdown = ()=>{
            slider.classList.toggle("active");
            dropdown.classList.toggle("active");
        }

        let goBack = ()=>{
            window.history.back();
        }
        
        //Default section
        let defaultsec = document.querySelector('.schedule').textContent.trim();
        let defaultmem = 'Players';
        console.log(defaultsec,defaultmem);
        loadsection(defaultsec,defaultmem);

        //update Section
        const sec = document.querySelectorAll('.sec');
        

        sec.forEach(section => {
                section.addEventListener('click', () => {
                defaultsec = section.textContent.trim();
                
                console.log(defaultsec,defaultmem);

                loadsection(defaultsec,defaultmem);

                document.querySelector('.sec.active').classList.remove('active');
                section.classList.add('active');
            });
        });

        //member type
        document.addEventListener('click', function(e){
            if(e.target.classList.contains('mem-type')){
                defaultmem = e.target.textContent.trim();
                console.log(defaultsec, defaultmem);
                loadsection(defaultsec, defaultmem);
            }
        });


        //load section
        function loadsection(sec,mem) {
            let params = new URLSearchParams(window.location.search);
            params.set('sec', sec);
            params.set('mem',mem);
            let newUrl = window.location.pathname + '?' + params.toString();
            window.history.replaceState({}, '', newUrl);
            let t = params.get('t');

            let data = {
                'sec' : sec,
                'mem' : mem,
                'team': t
            }

            console.log(data)
            loadinfo(data);

        }

        //load members

        //load information based on section
        function loadinfo(data){
            fetch('../Backend/manage_team_sections.php', {
                method: 'POST',
                body: JSON.stringify(data)
            })
            .then(response => response.text())
            .then(data => {
                // console.log(data);
                document.querySelector('.data-info').innerHTML = data;
                attachEditListeners();
            })
            .catch(err => {
                console.log(err);
            });
        }

        //add event
        let add_event = (event)=>{
            window.location.href = `./add-event.php?t=${team}`;
        }

        //add staff
        let add_member = ()=>{
            if(staff_count == 1){
                console.log("staff Limit Reach")
                let staff_full = document.querySelector('#error-staff_full');
                staff_full.style.opacity = "1";
                staff_full.innerHTML = "Limit of 1 staff member reached";
                setTimeout(()=>{
                    staff_full.style.opacity = "0";
                }, 5000);
            }else{
                window.location.href = `./add-staff.php?t=${team}`;
            }
            
        }

        //add player
        let add_player = ()=>{
            if(player_count >= team_size){
                console.log("players Limit Reach")
                let staff_full = document.querySelector('#error-player_full');
                staff_full.style.display = "block";
                staff_full.innerHTML = `Limit of ${team_size} players reached for this team.`;
                setTimeout(()=>{
                    staff_full.style.display = "none";
                }, 5000);
            }else{
                window.location.href = `./add-player.php?t=${team}`;
            }
        }
 
function attachEditListeners() {
  const players = document.querySelectorAll('.mem');

  players.forEach(player => {
    // Desktop right-click
    player.addEventListener('contextmenu', function(e) {
      e.preventDefault();
      showPlayerMenu(this, this.dataset.name); // 👈 pass the element
    });

    // Android long press
    let timer;

    player.addEventListener('touchstart', function(e) {
      timer = setTimeout(() => {
        showPlayerMenu(this, this.dataset.name); // 👈 pass the element
      }, 600);
    });

    player.addEventListener('touchend', () => clearTimeout(timer));
    player.addEventListener('touchmove', () => clearTimeout(timer));
  });
}


function showPlayerMenu(targetElement, name) {
  const menu = document.getElementById('playerMenu');
  menu.style.display = 'block';

  // Temporarily set position to measure size
  menu.style.left = '0px';
  menu.style.top = '0px';
  const menuWidth = menu.offsetWidth;
  const menuHeight = menu.offsetHeight;

  // Get position of the clicked .mem element
  const rect = targetElement.getBoundingClientRect();

  // Calculate position above the element
  let x = rect.right - menuWidth / 1 - 100;
  let y = rect.top; // 8px gap

  // Clamp X and Y within screen
  x = Math.max(10, Math.min(x, window.innerWidth - menuWidth - 10));
  y = Math.max(10, y); // prevent going off top

  // Apply position relative to document
  menu.style.left = `${x + window.scrollX}px`;
  menu.style.top = `${y + window.scrollY}px`;

    // Save both name and id
  menu.dataset.player = name;
  menu.dataset.id = targetElement.dataset.id; // 👈 get data-id
}

// Delete action
document.getElementById('deleteBtn').addEventListener('click', function () {
  const menu = document.getElementById('playerMenu');
  const id = menu.dataset.id;
  const name = menu.dataset.player;

  // Send to same file using fetch
  fetch(window.location.href, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `delete_id=${encodeURIComponent(id)}`
  })
  .then(res => res.text())
  .then(result => {
    if (result === 'success') {
      document.querySelector(`.mem[data-id="${id}"]`)?.remove();
    }
    menu.style.display = 'none';
  })
  .catch(err => {
    alert('Error: ' + err.message);
    menu.style.display = 'none';
  });
});



// Click outside to hide
document.addEventListener('click', (e) => {
  const menu = document.getElementById('playerMenu');
  if (!menu.contains(e.target)) {
    menu.style.display = 'none';
  }
});

// Menu actions
document.getElementById('editBtn').addEventListener('click', function() {
  const name = document.getElementById('playerMenu').dataset.player;
});

document.getElementById('deleteBtn').addEventListener('click', function() {
  const name = document.getElementById('playerMenu').dataset.player;
});

// Initialize
document.addEventListener('DOMContentLoaded', attachEditListeners);

// Disable right-click
  document.addEventListener('contextmenu', event => event.preventDefault());

  // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
  document.onkeydown = function(e) {
    if(e.keyCode == 123) return false; // F12
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
    if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
  }
  // Theme management for this page
    function initializeTheme() {
        // Check for saved theme preference or use system preference
        const currentTheme = localStorage.getItem('theme') || 
                            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        // Set the initial theme
        if (currentTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
        }
        
        // Listen for theme changes from other tabs/pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') {
                if (e.newValue === 'dark') {
                    document.body.setAttribute('data-theme', 'dark');
                } else {
                    document.body.removeAttribute('data-theme');
                }
            }
        });
        
        // Listen for custom events if your dashboard dispatches them
        window.addEventListener('themeChanged', function(e) {
            if (e.detail === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
            } else {
                document.body.removeAttribute('data-theme');
            }
        });
    }

    // Initialize theme when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
    });

    // Function to programmatically change theme if needed
    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        }
    }

    // Function to get current theme
    function getCurrentTheme() {
        return document.body.getAttribute('data-theme') || 'light';
    }
    </script>
</body>
</html>