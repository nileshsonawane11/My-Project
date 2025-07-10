<?php
    session_start();
    include '../../config.php';

    if(!isset($_SESSION['user'])){
        header('location: ../../front-page.php');
        exit();
    }
    if($_SESSION['role'] == "User"){
        header('location: ../../dashboard.php?update="live"&sport="CRICKET"');
        exit();
    }

    $match_id = '';
    $set_team1 = '';
    $set_team2 = '';
    $back_decision = false;

    $for = $_GET['for'] ?? '';
    $data = json_decode($_GET['data'] ?? '', true);

    if (empty($data)) {
        $match_id = $_GET['match_id'];
    } else {
        $match_id = $data['match_id'];
        $back_decision = true;
    }

    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));
    $score_log = json_decode($row['score_log'], true);

    // Redirect if no toss winner
    if (empty($row['toss_winner'])) {
        header('Location: ./match_toss.php?match_id=' . $match_id);
        exit();
    }

    // Redirect if match is completed
    if (!empty($score_log['completed'])) {
        header('Location: ../../dashboard.php?update=live&sport=VOLLEYBALL');
        exit();
    }

    // Detect current set
    $current_set = null;
    $last_rally = null;

    if (isset($score_log['sets']) && is_array($score_log['sets'])) {
        foreach ($score_log['sets'] as $set_number => $set_data) {
            if (isset($set_data['set_completed']) && $set_data['set_completed'] === false) {
                $current_set = $set_number;
                $set_team1 = $score_log['team1'];
                $set_team2 = $score_log['team2'];
                if (!empty($set_data['rallies'])) {
                    $last_rally = end($set_data['rallies']);
                    reset($set_data['rallies']);
                }
                break;
            }
        }
    }

    // If no active set, fallback to last one (even if completed)
    if ($current_set === null && isset($score_log['sets']) && is_array($score_log['sets'])) {
        $last_set_number = array_key_last($score_log['sets']);
        if (is_array($score_log['sets'][$last_set_number])) {
            $current_set = $last_set_number;
            $set_team1 = $score_log['team1'];
            $set_team2 = $score_log['team2'];
            $last_rally = end($score_log['sets'][$last_set_number]['rallies'] ?? []);
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
            scrollbar-width: none;
        }

        body{
            margin: auto;
        }
        .container0 {
            position: relative;
            height: max-content;
        }
        .return{
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
            padding-left: 9%;
            padding-right: 90px;
            padding-top: 40px;
        }
        .return svg{
            cursor: pointer;

            
        }
        .exit{
            width: 150px;
            height: 50px;
            background-color: #D9D9D9;
            border-radius: 85px;
            display: flex;
            justify-content:center;
            align-items: center;
        }
        .for-exit{
            display: flex;
            justify-content: center;
        }
        .exit-text{
            margin-left: 5px;
        }
        .for-icon1{
            margin-right: 5px;
        }
        .main-scoreboard {
                    width: 100%;
                    background-color: white;
                    margin-top: 20px;
                    display: flex;
                    flex-direction: column;
                    padding-right: 5%;
                    padding-left: 5%;
                    padding-top: 5%;
                    align-items: center;
                    height: 100%;
                    justify-content: space-between;
        }
        .right {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: row;
            width: 100%;
            gap: 20px;
            align-items: flex-end;
        }
        .left {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: row-reverse;
            width: 100%;
            gap: 20px;
            align-items: flex-end;
        }
        .score-team-data {
            display: flex;
            flex-direction: column;
            width: 80px;
        }
        .team-logo {
            height: 60px;
            width: 60px;
            background-color: #D9D9D9;
            border-radius: 50%;
        }
        .score1 {
            color: black;
            font-size: 3.8rem;
            margin-bottom: 30px;
            display: flex;
            justify-content: end;
        }
        .score2 {
            color: black;
            font-size: 3.8rem;
            margin-bottom: 30px;
            display: flex;
            justify-content: start;
        }
        .team-data {
            display: flex;
            flex-direction: column;
            padding: 5%;
        }
        .teams-name {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .container2 {
            position: relative;
            width: 100%;
            height: 80vh;
            /* filter: blur(3px); */

        }
        .scoreboard {
            width: 100%;
            display: flex;
        }
        
        .scoring {
            border-bottom: 3px solid black;
            padding-bottom: 6px;
        }
        .current-set {
            width: 100%;
            height: 55px;
            background-color: rgba(217, 217, 217, 0.98);
            align-content: center;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 22px;
            border-bottom: 3px solid white;
        }
        .blur-container {
                top: 0;
                position: absolute;
                height: 100%;
                width: 100%;
                background-color: transparent;
                filter: blur(3px);
                z-index: -1;
                background-image: url("https://images.volleyballworld.com/image/upload/t_ratio10_16-size30-f_webp-c_fill/v1746718372/assets/v2/backgrounds/world_rankings_bkg.jpg");
            background-size: cover;
                -webkit-mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            -webkit-mask-size: 100% 100%;
            mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
        }
        .buttons{
            position: absolute;
            width: 100%;
            height: 300px;
            /* background-color: black; */
            
        }
        .point-buttons{
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            width: 100%;
            height: 50%;
        }
        .serve-button{
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            width: 100%;
            height: 60%;
        }
        .team-button {
            width: 130px;
            height: 57px;
            background-color: white;
            color: black;
            border: 2px solid black;
            border-radius: 15px;
        }
        .serve {
            width: 220px;
            height: 57px;
            background-color: black;
            color: white;
            border: 2px solid white;
            border-radius: 15px;
            position: relative;
            z-index: 4;
        }
        .team-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }
        .team-name {
            color: white;
        }
        button {
            font-size: 1.3rem;
            font-weight: bold;
        }
        .history{
            width: 100%;
            height: max-content;
            background-color: transparent;
        }
        .log{
            height: 125px;
            width: 100%;
            margin-top: 10px;
            background-color: rgba(217, 217, 217, 0.9);
            border-top: 2px solid white;
            border-bottom: 2px solid white;
            color: white;
        }
        .serving{
            height: 40px;
            width: 100%;
            border-bottom: 1px solid white;
            align-content: center;
            padding: 8px;
            font-weight: bold;
            font-size: 1.3rem;
            color: white;
        }
        .point-to {
            height: 100%;
            width: 85%;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .point-to-update{
            height: 80px;
            width: 100%;
            display: flex;
            padding: 15px;
        }
        .point-text {
            font-weight: bold;
            font-size: 1.1rem;
        }
        .last-update {
            font-size: 1.4rem;
            font-weight: bold;
        }
        /* Slide container wrapper */
        .slide-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 543px;
            overflow: hidden; /* Only hide what overflows */
            transform: translateY(600px);
            transition: transform 0.5s ease;
            z-index: 1000;
            background-color: white;
        }

        /* Container parent for horizontal sliding */
        .slide-container {
            display: flex;
            width: 300%; /* 3 containers at 100% each */
            height: 100%;
            transition: transform 0.5s ease;
        }

        /* Individual containers */
        .container3, 
        .container4, 
        .container5 {
            flex: 0 0 33.33%;
            width: 100%; /* Each takes 1/3 of parent width */
            height: 100%;
            overflow-y: auto;
            background-color: white;
        }
        

        .current-server {
            width: 100%;
            height: 48px;
            border-top: 2px solid #D9D9D9;
            border-bottom: 1px solid #D9D9D9;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 8px;
        }
        .curr-ser {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .tap {
            font-size: 0.8rem;
            font-weight: light;
        }
        .players-info {
            width: 100%;
            height: 492px;
            background-color: white;
            padding-top: 35px;
        }
        .player-name {
            width: 100%;
            height: 43px;
            background-color: rgba(217, 217, 217, 0.31);
            display: flex;
            align-items: center;
            padding-left: 8px;
            font-size: 1.1rem;
            margin-top: 5px;
        }
        .player-cnt {
            padding-left: 8px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .player-rel {
            width: 100%;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 5px;
        }
        .assign-later {
            height: 50px;
            width: 155px;
            border-radius: 48px;
            background-color: black;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .replace {
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .serve-result {
            width: 100%;
            height: 48px;
            border-top: 2px solid #D9D9D9;
            border-bottom: 1px solid #D9D9D9;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 15px;
        }
        .ser-res {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .outcome {
            font-size: 0.8rem;
            font-weight: light;
        }
        .outcomes{
            width: 100%;
            height: 490px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .in,.ace,.error {
            width: 75%;
            height: 87px;
            border: 2.5px solid #D9D9D9;
            margin-top: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            padding-left: 5%;
            padding-right: 5%;
        }
        .text{
            margin-left: 7%;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .extra3 {
            margin-left: 39%;
        }
        .extra2 {
            margin-left: 45%;
        }
        .extra1 {
            margin-left: 58%;
        }
        
        .point-assign {
            width: 100%;
            height: 48px;
            border-top: 2px solid #D9D9D9;
            border-bottom: 1px solid #D9D9D9;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 8px;
        }
        .poi-ass {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .who {
            font-size: 0.8rem;
            font-weight: light;
        }
        .teams-info {
            width: 100%;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
        }
        .team1-info, .team2-info {
            width: 157px;
            height: 184px;
            border: 3px solid #D9D9D9;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 28px;
        }
        .teams-logo {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background-color: #D9D9D9;
        }
        .picture {
            height: 50px;
            width:50px;
            background-color: transparent;
            border-radius: 50%;
            background-size: cover;
        }
        .picture img {
            height: 100%;
            object-fit: cover;
        }
        .undo {
            width: 110px;
            height: 50px;
            background-color: white;
            color: green;
            border: 2px solid black;
            border-radius: 15px;
        }
        .undo-btn {
            margin-top: 20px;
        }
        @media (min-width:450px){
            .blur-container{
               background-position-y: -100px; 
            }
        }
        @media (min-width:601px){
            .team-data {
            display: flex;
            flex-direction: column;
            padding: 8%;
        }
        .score {
            color: black;
            font-size: 3.8rem;
            margin-bottom: 30px;
            padding-left: 20%;
            padding-right: 20%;
        }
        .blur-container{
            background-position-y: -200px;
        }
        .team-button {
            width: 180px;
            height: 80px;
        }
        .serve {
            width: 270px;
            height: 80px;
        }
        }
        @media (min-width:1000px){
            .blur-container{
               background-position-y: -400px; 
            }
        }
        @media (min-width:1300px){
            .blur-container{
               background-position-y: -600px; 
            }
        }
        @media (max-width:600px){
        .right {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: row;
            width: 100%;
            margin-bottom: 10%;
            align-items: flex-end;
        }
        .left {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: row-reverse;
            width: 100%;
            margin-bottom: 10%;
            align-items: flex-end;
        }
        .container2 {
            height: 600px;
            width: 100%;
        }
        }
    </style>
</head>
<body>
    <div class="container0">
    <div class="container1">
        <div class="return">
            <div>
                <svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
        </div>

        <div class="for-exit">
            <div class="exit">
                <div class="for-icon1">
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.36196 6.62029L11.672 1.04729C11.7606 0.954302 11.8101 0.830761 11.8101 0.70229C11.8101 0.573819 11.7606 0.450279 11.672 0.357291L11.666 0.35129C11.623 0.306055 11.5713 0.270036 11.5139 0.245422C11.4566 0.220808 11.3949 0.208115 11.3325 0.208115C11.2701 0.208115 11.2083 0.220808 11.151 0.245422C11.0937 0.270036 11.0419 0.306055 10.999 0.35129L5.99896 5.59929L1.00096 0.35129C0.95799 0.306055 0.906263 0.270036 0.84893 0.245422C0.791597 0.220808 0.729857 0.208115 0.667463 0.208115C0.60507 0.208115 0.543329 0.220808 0.485996 0.245422C0.428663 0.270036 0.376937 0.306055 0.333963 0.35129L0.327963 0.357291C0.239318 0.450279 0.189867 0.573819 0.189867 0.70229C0.189867 0.830761 0.239318 0.954302 0.327963 1.04729L5.63796 6.62029C5.68466 6.6693 5.74082 6.70832 5.80305 6.73498C5.86528 6.76164 5.93227 6.77539 5.99996 6.77539C6.06766 6.77539 6.13465 6.76164 6.19688 6.73498C6.2591 6.70832 6.31527 6.6693 6.36196 6.62029Z" fill="black"/>
                    </svg>
                </div>
                <div class="exit-text">End Set 1</div>
            </div>
        </div>

        <div class="main-scoreboard">
            <div class="scoreboard">
                <div class="right">
                    <div class="team-logo">
                        <img src="" alt="">
                    </div>
                    <div class="score-team-data">
                        <div class="score1"><?php echo $score_log['team1_score']; ?></div>
                        <div class="team-data">
                            <label class="teams-name">
                                <?php
                                    $t_id1 = $score_log['team1'];
                                    $t_name1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$t_id1'"));
                                    echo $t_name1['t_name'];
                                ?>
                            </label>
                            <label class="set">Sets : <?php echo $score_log['sets_won']['team1']; ?></label>
                        </div>
                    </div>
                </div>

                <div class="left">
                    <div class="team-logo">
                        <img src="" alt="">
                    </div>
                    <div class="score-team-data">
                        <div class="score2"><?php echo $score_log['team2_score']; ?></div>
                        <div class="team-data">
                            <label class="teams-name">
                                <?php
                                    $t_id2 = $score_log['team2'];
                                    $t_name2 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$t_id2'"));
                                    echo $t_name2['t_name'];
                                ?>
                            </label>
                            <label class="set">Sets : <?php echo $score_log['sets_won']['team2']; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>

    <div class="container2">
        <div class="image"></div>
        <div class="current-set">Set 1</div>
        <div class="blur-container"></div>

        <div class="buttons">
            <div class="point-buttons">
                <div class="team-btn">
                    <label class="team-name"><?php echo $t_name1['t_name']; ?></label>
                    <button class="team-button" data-team="<?php echo $score_log['team1']; ?>">Point</button>
                </div>
                <div class="team-btn">
                    <label class="team-name"><?php echo $t_name2['t_name']; ?></label>
                    <button class="team-button" data-team="<?php echo $score_log['team2']; ?>">Point</button>
                </div>
            </div>

            <div class="serve-button">
                <div class="team-btn">
                    <label class="team-name">For detail Scoring</label>
                    <button class="serve">Serve</button>
                    <div class="undo-btn">
                        <button class="undo">Undo</button>
                    </div>
                </div>
            </div>

            <div class="history">
                <div class="log">
                    <div class="serving">team-name serving</div>
                    <div class="point-to-update">
                        <div class="point-to">
                            <label class="point-text">Point</label>
                            <label class="to_team-name">to team-name</label>
                        </div>
                        <div class="last-update">0-0</div>
                    </div>
                </div>

                <div class="log">
                    <div class="serving">team-name serving</div>
                    <div class="point-to-update">
                        <div class="point-to">
                            <label class="point-text">Point</label>
                            <label class="to_team-name">to team-name</label>
                        </div>
                        <div class="last-update">0-0</div>
                    </div>
                </div>

                <div class="log">
                    <div class="serving">team-name serving</div>
                    <div class="point-to-update">
                        <div class="point-to">
                            <label class="point-text">Point</label>
                            <label class="to_team-name">to team-name</label>
                        </div>
                        <div class="last-update">0-0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="slide-wrapper">
        <div class="slide-container">
            <div class="container3">
                <div class="current-server">
                    <label class="curr-ser">Current Server</label>
                    <label class="tap">Tap to choose the serving player</label>
                </div>
                <div class="players-info">
                    <label class="player-cnt">Players(6)</label>
                    <div class="player-name">1. (player name)</div>
                    <div class="player-name">2. (player name)</div>
                    <div class="player-name">3. (player name)</div>
                    <div class="player-name">4. (player name)</div>
                    <div class="player-name">5. (player name)</div>
                    <div class="player-name">6. (player name)</div>
                    <div class="player-rel">
                        <div class="assign-later">Assign later</div>
                        <label for="" class="replace">Replace Player</label>
                    </div>
                </div>
            </div>

            <div class="container4">
                <div class="serve-result">
                    <label class="ser-res">Serve Result</label>
                    <label class="outcome">Outcome of the service</label>
                </div>
                <div class="outcomes">
                    <div class="in">
                        <div class="picture">
                            <img src="https://i.ibb.co/v43bFhr1/IN.jpg">
                        </div>
                        <div class="text">In</div>
                        <div class="extra1">
                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.6198 5.63755L1.0468 0.327549C0.953812 0.238904 0.830271 0.189453 0.7018 0.189453C0.57333 0.189453 0.449789 0.238904 0.356801 0.327549L0.3508 0.333549C0.305565 0.376522 0.269546 0.428248 0.244932 0.485581C0.220318 0.542914 0.207625 0.604655 0.207625 0.667049C0.207625 0.729442 0.220318 0.791183 0.244932 0.848516C0.269546 0.905849 0.305565 0.957575 0.3508 1.00055L5.5988 6.00055L0.3508 10.9986C0.305565 11.0415 0.269546 11.0933 0.244932 11.1506C0.220318 11.2079 0.207625 11.2697 0.207625 11.3321C0.207625 11.3944 0.220318 11.4562 0.244932 11.5135C0.269546 11.5709 0.305565 11.6226 0.3508 11.6656L0.356801 11.6716C0.449789 11.7602 0.57333 11.8096 0.7018 11.8096C0.830271 11.8096 0.953812 11.7602 1.0468 11.6716L6.6198 6.36155C6.66881 6.31485 6.70783 6.25869 6.73449 6.19646C6.76115 6.13424 6.7749 6.06725 6.7749 5.99955C6.7749 5.93185 6.76115 5.86486 6.73449 5.80264C6.70783 5.74041 6.66881 5.68425 6.6198 5.63755Z" fill="black"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ace">
                        <div class="picture">
                            <img src="https://i.ibb.co/7tcyp6PP/ACE.jpg">
                        </div>
                        <div class="text">Ace</div>
                        <div class="extra2">+1 to team1</div>
                    </div>
                    <div class="error">
                        <div class="picture">
                            <img src="https://i.ibb.co/Z6Kf65vb/ERROR.jpg">
                        </div>
                        <div class="text">Error</div>
                        <div class="extra3">+1 to team2</div>
                    </div>
                </div>
            </div>

            <div class="container5">
                <div class="point-assign">
                    <label class="poi-ass">Point</label>
                    <label class="who">Who scored the point</label>
                </div>
                <div class="teams-info">
                    <div class="team1-info teams-info">
                        <div class="teams-logo"></div>
                        <div class="teams-name">(team1 name)</div>
                    </div>
                    <div class="team2-info teams-info">
                        <div class="teams-logo"></div>
                        <div class="teams-name">(team2 name)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <script>
document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const serveBtn = document.querySelector('.serve');

    
    // Get existing containers
    const container3 = document.querySelector('.container3');
    const container4 = document.querySelector('.container4');
    const container5 = document.querySelector('.container5');
    const slideContainer = document.querySelector('.slide-container');
    const slideWrapper = document.querySelector('.slide-wrapper');
    
    // Wrap containers in sliding parent
    if (slideContainer && container3 && container4 && container5) {
    slideContainer.appendChild(container3);
    slideContainer.appendChild(container4);
    slideContainer.appendChild(container5);
} else {
    console.warn("One or more containers not found in the DOM.");
}
    
    const playerNames = document.querySelectorAll('.player-name');
    const assignLater = document.querySelector('.assign-later');
    const inButton = document.querySelector('.in');
    const aceButton = document.querySelector('.ace');
    const errorButton = document.querySelector('.error');


    // Current slide position (0=container3, 1=container4, 2=container5)
    let currentSlide = 0;

    // Open modal
    serveBtn.addEventListener('click', () => {
        currentSlide = 0;
        updateSlidePosition();
        slideWrapper.style.transform = 'translateY(0)';
    });

    // Update container position
    function updateSlidePosition() {
        slideContainer.style.transform = `translateX(-${currentSlide * 33.333}%)`;
    }

    // Navigation
    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
        updateSlidePosition();
    }

    // Event listeners
    playerNames.forEach(player => {
        player.addEventListener('click', () => {
            goToSlide(1);
            getplayername (player);
        })
    });
    
    let getplayername = (el) => {
        console.log(el.innerText)
    }

    let serveresult = (el) => {
        console.log(el.innerText);
    }

    assignLater.addEventListener('click', () => goToSlide(1));
    inButton.addEventListener('click', () => {
        goToSlide(2);
        serveresult(inButton);
    });
    
    // Drag to dismiss
    let startY = 0;
    const threshold = 60;

    slideWrapper.addEventListener('touchstart', (e) => {
        startY = e.touches[0].clientY;
        slideWrapper.style.transition = 'none';
    });

    slideWrapper.addEventListener('touchmove', (e) => {
        const diffY = e.touches[0].clientY - startY;
        if (diffY > 0) {
            slideWrapper.style.transform = `translateY(${diffY}px)`;
        }
    });

    slideWrapper.addEventListener('touchend', (e) => {
        const diffY = e.changedTouches[0].clientY - startY;
        slideWrapper.style.transition = 'transform 0.5s ease';
        
        if (diffY > threshold) {
            slideWrapper.style.transform = 'translateY(600px)';
        } else {
            slideWrapper.style.transform = 'translateY(0)';
        }
    });

    const clickableSelectors = ['.ace', '.error', '.team1-info','.team2-info'];

clickableSelectors.forEach(selector => {
    document.querySelectorAll(selector).forEach(element => {
        element.addEventListener('click', () => {
            if (slideWrapper) {
                slideWrapper.style.transition = 'transform 0.5s ease';
                slideWrapper.style.transform = 'translateY(600px)';
                serveresult(element);

                setTimeout(() => {
                    slideContainer.style.transform = 'translateX(0)';
                }, 300);
                
            }
        });
    });
});

document.querySelectorAll(".team-button").forEach(team => {
    team.addEventListener("click",() => {
        console.log(team.getAttribute('data-team'));
    })
})

});
</script>



</body>

</html>