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
    <title>Volleyball Scoreboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select: none;
            scrollbar-width: none;
        }

        body {
            margin: 0;
            background: #f8f8f8;
            min-height: 100vh;
            color: #333;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: -1;
        }

        .container0 {
            position: relative;
            height: max-content;
        }

        .return {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
            padding: 20px 5%;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .return svg {
            cursor: pointer;
            fill: #F83900;
        }

        .exit {
            width: 150px;
            height: 50px;
            background-color: white;
            border-radius: 85px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #F83900;
            border: 2px solid #F83900;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .exit:hover {
            background-color: #F83900;
            color: white;
        }

        .exit-text {
            margin-left: 5px;
        }

        .for-icon1 {
            margin-right: 5px;
        }

        .main-scoreboard {
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 0 5%;
            align-items: center;
            height: 100%;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: white;
        }

        .right {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-direction: row;
            width: 100%;
            gap: 20px;
            align-items: flex-start;
        }

        .left {
            display: flex;
            align-items: center;
            flex-direction: row-reverse;
            width: 100%;
            gap: 20px;
            align-items: flex-start;
            justify-content: flex-end;
        }

        .score-team-data {
            display: flex;
            flex-direction: column;
            width: 100%;
            align-items: center;
            gap: 20px;
        }

        .team-logo {
            height: 80px;
            width: 80px;
            background-color: white;
            border-radius: 50%;
            border: 3px solid #FAC01F;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #F83900;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .score1, .score2 {
            color: #F83900;
            font-size: 4.5rem;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .score1 {
            display: flex;
            justify-content: end;
        }

        .score2 {
            display: flex;
            justify-content: start;
        }

        .team-data {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            gap: 5px;
        }

        .team1_name, .team2_name {
            font-size: 1.4rem;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        .set {
            font-size: 1rem;
            color: #777;
        }

        .container2 {
            position: relative;
            width: 100%;
            height: 80vh;
            background: rgba(255, 255, 255, 0.65);
        }

        .blur-container {
                top: 0;
                position: absolute;
                height: 100%;
                width: 100%;
                background-color: transparent;
                filter: blur(3px);
                z-index: -1;
                background-position: center;
                background-image: url("https://images.volleyballworld.com/image/upload/t_ratio10_16-size30-f_webp-c_fill/v1746718372/assets/v2/backgrounds/world_rankings_bkg.jpg");
            background-size: cover;
                -webkit-mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            -webkit-mask-size: 100% 100%;
            mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
        }

        .scoreboard {
            width: 100%;
            display: flex;
            align-items: flex-start;
        }

        .for-exit {
            display: flex;
            justify-content: center;
        }

        .scoring {
            border-bottom: 3px solid #FAC01F;
            padding-bottom: 6px;
        }

        .current-set {
            width: 100%;
            height: 55px;
            background-color: #FAC01F;
            align-content: center;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 22px;
            border-bottom: 3px solid white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .buttons {
            position: absolute;
            width: 100%;
            height: 300px;
            background-color: transparent;
        }

        .point-buttons {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            width: 100%;
            height: 50%;
        }

        .serve-button {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            width: 100%;
            height: 60%;
        }

        .team-button {
            width: 150px;
            height: 70px;
            background-color: white;
            color: #F83900;
            border: 2px solid #F83900;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .team-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            background-color: #F83900;
            color: white;
        }

        .team-button:active {
            transform: translateY(1px);
        }

        .serve {
            width: 220px;
            height: 70px;
            background-color: #F83900;
            color: white;
            border: 2px solid #F83900;
            border-radius: 15px;
            position: relative;
            z-index: 4;
            font-size: 1.5rem;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .serve:hover {
            background-color: #e03100;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .team-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .team-name {
            color: #333;
            font-weight: bold;
            font-size: 1.2rem;
        }

        button {
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
        }

        .history {
            width: 100%;
            height: max-content;
            background-color: transparent;
            margin-top: 20px;
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .log {
            height: 125px;
            width: 100%;
            margin-top: 10px;
            background-color: white;
            border: 1px solid #eee;
            color: #333;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .serving {
            height: 40px;
            width: 100%;
            border-bottom: 1px solid #eee;
            align-content: center;
            padding: 8px;
            font-weight: bold;
            font-size: 1.3rem;
            color: #F83900;
            background-color: #f9f9f9;
        }

        .point-to {
            height: 100%;
            width: 85%;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .point-to-update {
            height: 80px;
            width: 100%;
            display: flex;
            padding: 15px;
            align-items: center;
        }

        .point-text {
            font-weight: bold;
            font-size: 1.1rem;
            color: #555;
        }

        .last-update {
            font-size: 1.3rem;
            font-weight: bold;
            color: #F83900;
        }

        /* Slide container wrapper */
        .slide-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 543px;
            overflow: hidden;
            transform: translateY(600px);
            transition: transform 0.5s ease;
            z-index: 1000;
            background: white;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            border-top: 2px solid #FAC01F;
        }

        /* Container parent for horizontal sliding */
        .slide-container {
            display: flex;
            width: 300%;
            height: 100%;
            transition: transform 0.5s ease;
        }

        /* Individual containers */
        .container3,
        .container4,
        .container5 {
            flex: 0 0 33.33%;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background: white;
            color: #333;
        }

        .current-server {
            width: 100%;
            height: 48px;
            border-top: 2px solid #eee;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 15px;
            background-color: white;
        }

        .curr-ser {
            font-size: 1.1rem;
            font-weight: bold;
            color: #F83900;
        }

        .tap {
            font-size: 0.8rem;
            color: #777;
        }

        .players-info {
            width: 100%;
            height: 492px;
            padding-top: 35px;
            background-color: white;
        }

        .player-name {
            width: 95%;
            height: 50px;
            background-color: white;
            display: flex;
            align-items: center;
            padding-left: 15px;
            font-size: 1.1rem;
            margin: 10px auto;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }

        .player-name:hover {
            background-color: #f9f9f9;
            transform: translateX(5px);
            border-left: 3px solid #F83900;
        }

        .player-cnt {
            padding-left: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .player-rel {
            width: 100%;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 15px;
            background-color: white;
        }

        .assign-later {
            height: 50px;
            width: 155px;
            border-radius: 48px;
            background-color: white;
            color: #F83900;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            border: 2px solid #F83900;
            transition: all 0.3s ease;
        }

        .assign-later:hover {
            background-color: #F83900;
            color: white;
        }

        .replace {
            font-weight: bold;
            font-size: 1.2rem;
            color: #777;
        }

        .serve-result {
            width: 100%;
            height: 48px;
            border-top: 2px solid #eee;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 15px;
            background-color: white;
        }

        .ser-res {
            font-size: 1.1rem;
            font-weight: bold;
            color: #F83900;
        }

        .outcome {
            font-size: 0.8rem;
            color: #777;
        }

        .outcomes {
            width: 100%;
            height: 490px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background-color: white;
        }

        .in, .ace, .error {
            width: 85%;
            height: 100px;
            border: 2px solid #FAC01F;
            margin-top: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            padding: 0 5%;
            transition: all 0.3s ease;
        }

        .in:hover, .ace:hover, .error:hover {
            background-color: #fff9f0;
            transform: scale(1.02);
        }

        .picture {
            height: 65px;
            width: 75px;
            background-color: transparent;
            border-radius: 50%;
            overflow: hidden;
        }

        .picture img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .text {
            margin-left: 7%;
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .extra1, .extra2, .extra3 {
            margin-left: auto;
            color: #777;
        }

        .point-assign {
            width: 100%;
            height: 48px;
            border-top: 2px solid #eee;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 15px;
            background-color: white;
        }

        .poi-ass {
            font-size: 1.1rem;
            font-weight: bold;
            color: #F83900;
        }

        .who {
            font-size: 0.8rem;
            color: #777;
        }

        .teams-info {
            width: 100%;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            background-color: white;
        }

        .team1-info, .team2-info {
            width: 160px;
            height: 200px;
            border: 3px solid #FAC01F;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
            background-color: white;
            transition: all 0.3s ease;
        }

        .team1-info:hover, .team2-info:hover {
            background-color: #fff9f0;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .teams-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #F83900;
            font-weight: bold;
            font-size: 1.5rem;
            border: 2px solid #FAC01F;
            overflow: hidden;
        }

        .team-logo img,
        .teams-logo img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .undo {
            width: 110px;
            height: 50px;
            background-color: white;
            color: #F83900;
            border-radius: 15px;
            outline: none;
            border: 2px solid #F83900;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .undo:hover {
            background-color: #F83900;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .undo-btn {
            margin-top: 20px;
        }

        .score-teamlogo {
            display: flex;
            justify-content: space-evenly;
            align-items: end;
            padding: 5%;
            background-color: white;
        }

        @media (max-width: 450px) {
            .team-logo {
                height: 60px;
                width: 60px;
                font-size: 1.2rem;
            }
            
            .score1, .score2 {
                font-size: 3.5rem;
            }
            
            .team-button {
                width: 130px;
                height: 60px;
                font-size: 1.3rem;
            }
            
            .serve {
                width: 200px;
                height: 60px;
            }
        }

        @media (min-width: 601px) {
            .team-data {
                display: flex;
                flex-direction: column;
                padding: 8%;
            }
            
            .score1, .score2 {
                font-size: 5rem;
            }
            
            .team-button {
                width: 180px;
                height: 80px;
                font-size: 1.5rem;
            }
            
            .serve {
                width: 250px;
                height: 80px;
            }
            
            .team-logo {
                height: 100px;
                width: 100px;
                font-size: 2rem;
            }
        }

        @media (max-width: 600px) {
            .right {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                flex-direction: row;
                width: 100%;
                margin-bottom: 10%;
                align-items: flex-end;
            }
            
            .left {
                display: flex;
                justify-content: flex-end;
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

        <div class="score-teamlogo">
                    <div class="score2"><?php echo $score_log['team2_score']; ?></div>
                    <div class="score1"><?php echo $score_log['team1_score']; ?></div>
        </div>

        <div class="main-scoreboard">
            <div class="scoreboard">
                <?php
                    $t_id1 = $score_log['team1'];
                    $t_name1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$t_id1'"));
                ?>
                <div class="right">       
                    <div class="score-team-data">
                        <div class="team-logo">
                            <?php if($t_name1['t_logo']) { ?>
                                <img src="../../assets/images/teams/<?php echo $t_name1['t_logo']; ?>" alt="">
                            <?php }else{ ?>
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                            <?php } ?>
                        </div>
                        <div class="team-data">
                            <label class="team1_name">
                                <?php
                                    echo $t_name1['t_name'];
                                ?>
                            </label>
                            <label class="set">Sets : <?php echo $score_log['sets_won']['team1']; ?></label>
                        </div>
                    </div>
                </div>

                <div class="left">
                    <div class="score-team-data">
                        <?php
                            $t_id2 = $score_log['team2'];
                            $t_name2 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$t_id2'"));  
                        ?>
                        <div class="team-logo">
                        <?php if($t_name2['t_logo']) { ?>
                                <img src="../../assets/images/teams/<?php echo $t_name2['t_logo']; ?>" alt="">
                            <?php }else{ ?>
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                            <?php } ?>
                    </div>
                        <div class="team-data">
                            <label class="team2_name">
                                <?php
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
                    <div class="ace" data-team="<?php echo $score_log['team1']; ?>">
                        <div class="picture">
                            <img src="https://i.ibb.co/7tcyp6PP/ACE.jpg">
                        </div>
                        <div class="text">Ace</div>
                        <div class="extra2">+1 to team1</div>
                    </div>
                    <div class="error" data-team="<?php echo $score_log['team2']; ?>">
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
                    <div class="team1-info teams-info" data-team="<?php echo $score_log['team1']; ?>">
                        <div class="teams-logo">
                            <?php if($t_name1['t_logo']) { ?>
                                <img src="../../assets/images/teams/<?php echo $t_name1['t_logo']; ?>" alt="">
                            <?php }else{ ?>
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                            <?php } ?>
                        </div>
                        <div class="teams-name"><?php echo $t_name1['t_name']; ?></div>
                    </div>
                    <div class="team2-info teams-info" data-team="<?php echo $score_log['team2']; ?>">
                        <div class="teams-logo">
                            <?php if($t_name2['t_logo']) { ?>
                                <img src="../../assets/images/teams/<?php echo $t_name2['t_logo']; ?>" alt="">
                            <?php }else{ ?>
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                            <?php } ?>
                        </div>
                        <div class="teams-name"><?php echo $t_name2['t_name']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <script>
    let serve_player = null;
    let action = null;
    let winner_team = null;

    
    let get_score = () => {
         let data = {
            'match_id': '<?php echo $match_id; ?>',
            'winner_team': winner_team,
            'serve_player': serve_player,
            'serve_action' : action
        }

        console.log(data);

        fetch('./Backend/update-volleyball-logs.php',{
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then((data) => {
            console.log(data);
        })
        .catch(error => {
            console.log(error);
        })

        serve_player = null;
        action = null;
        winner_team = null;
    }

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
        serve_player = el.innerText;
    }

    let serveresult = (el) => {
        winner_team = el.getAttribute('data-team');
        get_score();
    }

    let getaction = (el) => {
        action = el.innerText;
    }

    assignLater.addEventListener('click', () => goToSlide(1));
    inButton.addEventListener('click', () => {
        goToSlide(2);
        getaction(inButton);
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

    const clickableSelectors = ['.ace', '.error', '.team1-info', '.team2-info'];

    clickableSelectors.forEach(selector => {
        document.querySelectorAll(selector).forEach(element => {
            element.addEventListener('click', () => {
                if (slideWrapper) {
                    slideWrapper.style.transition = 'transform 0.5s ease';
                    slideWrapper.style.transform = 'translateY(600px)';
                    if (element.matches('.ace, .error')) {
                        getaction(element);
                        console.log('Action immediately:', element.innerText); // use element here
                    }
                    // Use directly here
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
        winner_team = team.getAttribute('data-team');
        get_score();
    })
})

});

 const back_decision = '<?php echo $back_decision; ?>';

 // Disable F5 and Ctrl+R keyboard shortcuts
        window.addEventListener("keydown", function (e) {
            if (e.key === "F5" || (e.ctrlKey && e.key.toLowerCase() === "r")) {
                e.preventDefault();
                alert("Reload is disabled for the scorer!");
            }
        });

    //prevent from refesh page
        function preventReload(e) {
            e.preventDefault();
            e.returnValue = '';
        }
    window.addEventListener("beforeunload", preventReload);

    //go to prevoius page
    let goBack = () => {
        if(back_decision){
            window.location.href = '../../dashboard.php?update=Live&sport=CRICKET';
        }else{
            window.history.back();
        }
    }

   
</script>
</body>

</html>