<?php
    session_start();
    include '../../config.php';

    if (!isset($_SESSION['user'])) {
        header('location: ../../front-page.php');
        exit();
    }

    if ($_SESSION['role'] == "User") {
        header('location: ../../dashboard.php?update=live&sport=KHO-KHO');
        exit();
    }

    $match_id = '';
    $run = '';
    $chase = '';
    $back_decision = false;

    $for = $_GET['for'] ?? '';
    $data = json_decode($_GET['data'] ?? '', true);

    if (empty($data)) {
        $match_id = $_GET['match_id'] ?? '';
    } else {
        $match_id = $data['match_id'];
        $back_decision = true;
    }

    // Fetch match info
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));
    $score_log = json_decode($row['score_log'], true);

    // Redirect if no toss winner
    if (empty($row['toss_winner'])) {
        header('Location: ./match_toss.php?match_id=' . $match_id);
        exit();
    }

    // Redirect if match is completed
    if (!empty($score_log['completed']) && $score_log['completed'] === true) {
        header('Location: ../../dashboard.php?update=live&sport=KHO-KHO');
        exit();
    }

    // Determine current inning
    $current_inning = $score_log['current_inning'];
    $inning_data = $score_log['innings'][$current_inning] ?? null;

    // If inning data is not set, fallback to last available
   $inning_data = $score_log['innings'][$current_inning] ?? null;

    if (empty($inning_data)) {
        // Ensure innings is set and is an array
        if (!empty($score_log['innings']) && is_array($score_log['innings'])) {
            $last_inning_number = array_key_last($score_log['innings']);
            $inning_data = $score_log['innings'][$last_inning_number] ?? [];
        } else {
            $inning_data = []; // fallback if innings is missing
        }
    }

    // Set current attacking/defending teams
    $attacking_team = $inning_data['attack_team'] ?? '';
    $defending_team = $inning_data['defence_team'] ?? '';

    // For UI or match status tracking
    $team1 = $score_log['team1'];
    $team2 = $score_log['team2'];

    $total_innings = $score_log['total_innings'];

    $team1_score = $score_log['team1_score'];
    $team2_score = $score_log['team2_score'];

    // Determine roles for team1 and team2
    $team1_role = ($attacking_team === $team1) ? 'Chasing Team' : 'Running Team';
    $team2_role = ($attacking_team === $team2) ? 'Chasing Team' : 'Running Team';

    $is_empty = false;
    if (empty($score_log['innings'][$current_inning]['current_runners'])) {
        $is_empty = true;
    } else {
        $is_empty = false;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kho-kho Scoreboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select: none;
            scrollbar-width: none;
        }

        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
            --card-bg: #ffffff;
            --text-dark: #333333;
            --text-light: #f8f8f8;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body {
            margin: 0;
            background: #f8f8f8;
            min-height: 100vh;
            color: #333;
            position: relative;
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
            background: rgba(255, 255, 255, 0.1);
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
            background-color: white;
            background: rgba(255, 255, 255, 0.1);
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
            justify-content: flex-end;
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
            -webkit-text-stroke: 1px #ffffff;
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
            color: #ffffff;
        }

        .set {
            font-size: 1rem;
            color: #ffffff;
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
                background-image: url("https://i.ibb.co/SDvv2YTd/179380-auhvxpvqhb-1662286409.jpg");
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
            flex-direction: column;
            width: 100%;
            height: 60%;
        }

        .team1-button,
        .team2-button {
            width: 160px;
            height: 90px;
            background-color: white;
            color: #F83900;
            border: none;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .team-button:active {
            transform: translateY(1px);
        }

        .team-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .team-name {
            color: #ffffff;
            font-weight: bold;
            font-size: 1.3rem;
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
            font-size: 1.8rem;
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

        .container5 {
            display: none;
            position: fixed;
            bottom: 0;
            height: 100%;
            width: 100%;
            height: 543px;
            transform: translateY(0);
            overflow-y: auto;
            transition: transform 0.5s ease;
            z-index: 1000;
            background: white;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            border-top: 2px solid #FAC01F;
        }

        .container6 {
            position: fixed;
            bottom: 0;
            height: 100%;
            width: 100%;
            height: 543px;
            transform: translateY(600px);
            overflow-y: auto;
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
            width: 200%;
            height: 100%;
            transition: transform 0.5s ease;
        }

        /* Individual containers */
        .container3,
        .container4 {
            flex: 0 0 50%;
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
            height: max-content;
            padding-top: 35px;
            background-color: white;
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

        .teams-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            justify-content: flex-end;
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

        .undo-btn {
            margin-top: 20px;
        }

        .score-teamlogo {
            display: flex;
            justify-content: space-evenly;
            align-items: end;
            padding: 5%;
            background-color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .replace {
            font-weight: 600;
            font-size: 15px;
            color: #f83900;
            width: 80px;
            height: 40px;
            background-color: transparent;
            border: none;
            outline: none;
        }
        .player-replace {
            width: 95%;
            height: 50px;
            display: flex;
            justify-content: space-between;
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

        .player-replace.selected {
            border: 2px solid green;
            background-color: #eaffea;
        }

        .index {
            position: relative;
            z-index: 10;
        }
        .tech-point {
            width: 110px;
            height: 50px;
            background-color: white;
            color: #F83900;
            border-radius: 15px;
            outline: none;
            border: 2px solid #F83900;
            font-weight: bold;
            transition: all 0.3s ease;
            text-align: center;
            align-content: center;
        }
        
        .teams-name {
            font-size: 1.3rem;
            font-weight: bold;
        }

        #match_completed,
        #start_second,
        #half_completed{
            position: fixed;
            transform: translateX(-50%) translateY(-50%);
            top: 50%;
            left: 50%;
            width: 300px;
            border: none;
            height: max-content;
            background: var(--card-bg);
            transition: all 0.5s ease-in-out;
            align-items: flex-start;
            padding: 20px;
            z-index: 99;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            flex-direction: column;
            justify-content: center;
            align-items: center;
            scrollbar-width: none;
        }

        #match_completed::backdrop,
        #start_second::backdrop,
        #half_completed::backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.15);
        }

        .undo-container{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }
        .undo-txt{
            font-size: 25px;
            font-weight: bold;
            color: var(--primary-dark);
        }
        .undo-warn{
            font-size: 18px;
            color: #666;
            letter-spacing: 1px;
            text-align: center;
        }
         .undo-btn,
        .super-over-btn{
            height: 40px;
            width: 160px;
            font-size: 16px;
            color: white;
            outline: none;
            border: none;
            background: var(--background);
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .undo-btn:hover,
        .super-over-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .undo-cancel,.complete-cancel{
            color: #666;
            font-size: 15px;
            cursor: pointer;
        }
        @keyframes shake {
            0%   { transform: translateX(-50%) translateY(-50%) translateX(0); }
            25%  { transform: translateX(-50%) translateY(-50%) translateX(-10px); }
            50%  { transform: translateX(-50%) translateY(-50%) translateX(10px); }
            75%  { transform: translateX(-50%) translateY(-50%) translateX(-10px); }
            100% { transform: translateX(-50%) translateY(-50%) translateX(0); }
        }
        .shake {
            animation: shake 0.4s;
        }
        .opacity-container{
            height: 100%;
            width: 100%;
            position: absolute;
            background: rgba(0, 0, 0, 0.6);
            z-index: 5;
            display: none;
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
                width: 140px;
                height: 85px;
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
            
            
            .serve {
                width: 250px;
                height: 80px;
            }
            
            .team-logo {
                height: 100px;
                width: 100px;
                font-size: 2rem;
            }
            .exit:hover {
                background-color: #F83900;
                color: white;
            }
            .tech-point:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 12px rgba(0,0,0,0.15);
                background-color: #F83900;
                color: white;
            }
            .undo:hover {
                background-color: #F83900;
                color: white;
                transform: translateY(-3px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            .team1-info:hover, .team2-info:hover {
                background-color: #fff9f0;
                transform: scale(1.05);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            .player-replace:hover {
                background-color: #f9f9f9;
                transform: translateX(5px);
                border-left: 3px solid #F83900;
            }
            .team1-button:hover,
            .team2-button:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 12px rgba(0,0,0,0.15);
                background-color: #F83900;
                color: white;
            }
            .serve:hover {
                background-color: #e03100;
                transform: translateY(-3px);
                box-shadow: 0 6px 12px rgba(0,0,0,0.15);
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
        <dialog id="half_completed">
            <div class="undo-container">
                <div class="undo-seyup">
                    <p class="undo-warn undo-txt">You really want to End the half?</p>
                </div>
                <div class="undo-seyup">
                    <button class="undo-btn" onclick="proceed_end_half()">End Half</button>
                </div>
                <div class="undo-seyup">
                    <p class="continue-match-btn complete-cancel" onclick="
                        document.querySelector('#half_completed').close();
                        document.querySelector('.opacity-container').style.display = 'none';
                        is_complete = false;
                    ">Continue Scoring</p>
                </div>
            </div>
        </dialog>

        <dialog id="match_completed">
            <div class="undo-container">
                <div class="undo-seyup">
                    <p class="undo-warn undo-txt">Match Completed! Do you want to continue scoring or finish the match?</p>
                </div>
                <div class="undo-seyup">
                    <button class="complete-match-btn undo-btn" onclick='complete_match()'>Complete Match</button>
                </div>
                <div class="undo-seyup">
                    <p class="continue-match-btn complete-cancel" onclick="document.querySelector('#match_completed').close();
                    document.querySelector('.opacity-container').style.display = 'none';
                    is_complete = false;">Continue Scoring</p>
                </div>
            </div>
        </dialog>

        <dialog id="start_second">
            <div class="undo-container">
                <div class="undo-seyup">
                    <p class="undo-warn undo-txt">Match tied! Do you want to continue scoring or finish the match?</p>
                </div>
                <div class="undo-seyup">
                    <button class="start-next-btn undo-btn" onclick='complete_match()'>Complete Match</button>
                </div>
                <div class="undo-seyup">
                    <p class="continue-match-btn complete_match" onclick="cancel_end()">Continue Scoring</p>
                </div>
            </div>
        </dialog>
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
                <div class="exit-text">End Inning <?php echo $current_inning; ?></div>
            </div>
        </div>

        <div class="score-teamlogo">
                    <div class="score2"><?php echo $team1_score ?></div>
                    <div class="score1"><?php echo $team2_score ?></div>
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
                            <label class="set"><?php echo $team1_role; ?></label>
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
                            <label class="set"><?php echo $team2_role; ?></label>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
    <div class="blur-container"></div>

    <div class="container2">
        <div class="image"></div>
        <div class="current-set"><div class="index">Inning <?php echo $current_inning; ?></div></div>
        <div class="blur-container"></div>

        <div class="buttons">
            <div class="point-buttons">
                <div class="team-btn">
                    <label class="team-name"><?php echo $t_name1['t_name']; ?></label>
                    <button class="team1-button team-buttons" data-team="<?php echo $team1; ?>">Point</button>
                </div>
                <div class="team-btn">
                    <label class="team-name"><?php echo $t_name2['t_name']; ?></label>
                    <button class="team2-button team-buttons" data-team="<?php echo $team2; ?>">Point</button>
                </div>
            </div>

            <div class="serve-button">
                <div class="tech-point">Tech. point</div>
                <div class="team-btn">
                    <div class="undo-button">
                        <button class="undo">Undo</button>
                    </div>
                </div>
            </div>

            <div class="history">
                <?php
                        $result2 = mysqli_query($conn, "SELECT score_log FROM matches WHERE match_id = '$match_id'");
                        $row2 = mysqli_fetch_assoc($result2);
                        $score_log = json_decode($row2['score_log'], true);

                        $tags = $score_log['innings'][$current_inning]['tags'];
                        $last_three_tags = array_reverse(array_slice($tags, -3));

                        // Print them nicely
                        foreach ($last_three_tags as $tags) {

                            $team1_id = $tags['point_taken_by'];
                            $team2_id = $score_log['innings'][$current_inning]['attack_team'];

                            // Prepare query with IN (?, ?)
                            $stmt = $conn->prepare("SELECT * FROM teams WHERE t_id IN (?, ?)");
                            $stmt->bind_param("ss", $team1_id, $team2_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Store names in associative array
                            $team_names = [];
                            while ($row = $result->fetch_assoc()) {
                                $team_names[$row['t_id']] = $row['t_name'];
                            }
                        
                    ?>
                <div class="log">
                        <div class="serving"><?php echo $team_names[$team2_id]; ?> chasing</div>
                        <div class="point-to-update">
                            <div class="point-to">
                                <label class="point-text">Point - <?php
                                    
                                    if (!empty($tags['type']) && $tags['type'] == 'Technical') {
                                        echo "1 (Technical)";
                                    }else{
                                        echo '1';
                                    }
                                    ?></label>
                                <label class="to_team-name">to <?php echo $team_names[$team1_id]; ?></label>
                            </div>
                            <div class="last-update"><?php echo $tags['last score']; ?></div>
                        </div>
                    </div>
                    <?php } ?>
            </div>
        </div>
    </div>

    <div class="slide-wrapper">
        <div class="slide-container">
            <div class="container3">
                    <div class="current-server">
                        <label class="curr-ser">Who took the point?</label>
                        <label class="tap">Select the chaser who scored</label>
                    </div>
                    <?php
                        $query = "SELECT * FROM `players` WHERE `team_id` = '$attacking_team'";
                        $result = mysqli_query($conn, $query);
                        $count = mysqli_num_rows($result);
                    ?>
                    <div class="players-info">
                        <label class="player-cnt">Players(<?php echo $count; ?>)</label>
                        <?php
                            if($count > 0){
                                $index = 1;
                                while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <div class="player-replace" data-player-id ='<?php echo $row['user_id']; ?>'>
                            <div class="player1-name"><?php echo $index.'. '. $row['player_name']; ?></div>
                        </div>
                        <?php
                                    $index++;
                                }
                            }
                        ?>
                       </div>
                    </div>
                </div>

            <div class="container4">
                <div class="current-server">
                        <label class="curr-ser">Which player got OUT?</label>
                        <label class="tap">Select the Runner Who Got Out</label>
                    </div>
                    <div class="players-info">
                        <?php
                            $current_runners_data = $score_log['innings'][$current_inning]['current_runners'] ?? [];

                            // Remove null or empty values
                            $current_runners = array_filter($current_runners_data, function($v) {
                                return !is_null($v) && $v !== '';
                            });

                            $runner_ids = array_values($current_runners);
                            $runner_count = count($runner_ids);

                            // Fetch player names from DB
                            $players = [];

                            if (!empty($runner_ids)) {
                                // Sanitize and implode safely
                                $ids_str = "'" . implode("','", array_map(function($id) use ($conn) {
                                    return mysqli_real_escape_string($conn, $id);
                                }, $runner_ids)) . "'";

                                $query = "SELECT * FROM players WHERE user_id IN ($ids_str) AND team_id = '$defending_team'";
                                $result = mysqli_query($conn, $query);

                                while ($player = mysqli_fetch_assoc($result)) {
                                    $players[] = $player;
                                }
                            }

                            echo "<label class=\"player-cnt\">Players($runner_count)</label>";

                            $index = 1;
                            foreach($players as $player) {
                                $player_id = $player['user_id'];
                                $player_name = $player['player_name'];
                        ?>
                                <div class="player-replace" data-player-id = '<?php echo $player_id; ?>'>
                                    <div class="player2-name"><?php echo $index.'. '. $player_name; ?></div>
                                </div>
                        <?php
                                $index++;
                            }
                        ?>                                      
                        </div>
                    </div>
            </div>

            
        </div>
    </div>
        <div class="container5">
                    <div class="current-server">
                        <label class="curr-ser">Pick the Next 3 Runners</label>
                        <label class="tap">Select Next Batch of Runners</label>
                    </div>
                    <?php
                        $query = "SELECT * FROM `players` WHERE `team_id` = '$defending_team'";
                        $result = mysqli_query($conn, $query);
                        $count = mysqli_num_rows($result);
                    ?>
                    <div class="players-info">
                        <label class="player-cnt">Players(<?php echo $count; ?>)</label>
                        <?php
                            if($count > 0){
                                $index = 1;
                                while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <div class="player-replace" data-player-id ='<?php echo $row['user_id']; ?>'>
                            <div class="player3-name"><?php echo $index.'. '. $row['player_name']; ?></div>
                        </div>
                        <?php
                        $index++;
                          }
                            }
                        ?>
                        </div>
                    </div>
                </div>

        </div>

        <div class="container6"> 
                <div class="point-assign">
                    <label class="poi-ass">Technical Point</label>
                    <label class="who">Which team got the technical point?</label>
                </div>
                <div class="teams-info">
                    <div class="team1-info teams-info" data-team="<?php echo $team1; ?>">
                        <div class="teams-logo">                           
                            <?php if($t_name2['t_logo']) { ?>
                                <img src="../../assets/images/teams/<?php echo $t_name1['t_logo']; ?>" alt="">
                            <?php }else{ ?>
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                            <?php } ?>                           
                        </div>
                        <div class="teams-name">
                            <?php
                                echo $t_name1['t_name'];
                            ?>
                        </div>
                    </div>
                    <div class="team2-info teams-info" data-team="<?php echo $team2; ?>">
                        <div class="teams-logo">                                                      
                            <?php if($t_name2['t_logo']) { ?>
                                <img src="../../assets/images/teams/<?php echo $t_name2['t_logo']; ?>" alt="">
                            <?php }else{ ?>
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                            <?php } ?>                         
                        </div>
                        <div class="teams-name">
                            <?php
                                echo $t_name2['t_name'];
                            ?>
                        </div>
                    </div>
                </div>
        </div>
    
    <script>
    let match_dialogue = document.querySelector('#match_completed');
    let start_dialogue = document.querySelector('#start_second');
    let opacity = document.querySelector('.opacity-container');
    let start_next_btn = document.querySelector('.start-next-btn');
    let complete_btn = document.querySelector('.complete-match-btn');
    let chaser = null;
    let point_taken_by = null;
    let out_player = null;
    let exit_inn = false;
    let undo = false;
    let back_decision = <?php echo json_encode($back_decision); ?>;
    let is_empty_runners = <?php echo json_encode($is_empty); ?>;
    let chasing_team = <?php echo json_encode($attacking_team); ?>;
    let running_team = <?php echo json_encode($defending_team); ?>;
    let match = <?php echo json_encode($match_id); ?>;
    let current_inning = <?php echo json_encode($current_inning); ?>;
    is_complete = false;
    
    let get_score = () => {
        let data = {
            'match_id': match,
            'current_inning': current_inning,
            'point_taken_by': point_taken_by,
            'chaser_player': chaser,
            'out_player' : out_player,
            ...(exit_inn == true ? {'exit' : exit_inn} : {}),
            ...(undo == true ? {'undo' : undo} : {}),
            ...(is_complete == true ? {'complete' : is_complete} : {})
        }

        console.log(data);

        fetch('./Backend/update-kho-kho-logs.php',{
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then((data) => {
            console.log(data);
            if(data.status == 200){
                    if(data.field == 'is_complete'){

                        match_dialogue.showModal();
                        match_dialogue.classList.add('shake');
                        navigator.vibrate([200,100,200]);
                        document.querySelectorAll('button:not(.undo, .undo-btn)').forEach(btn => {
                            btn.disabled = true;
                            btn.style.pointerEvents = 'none';
                        });

                    }else if(data.field == 'is_tie'){

                        start_dialogue.showModal();
                        start_dialogue.classList.add('shake');
                        navigator.vibrate([200,100,200]);

                    }else if(data.field == 'empty'){

                        let el = document.getElementById('error-'+data.field);
                        el.innerHTML = data.message;
                        el.style.display = 'block';
                        el.style.color = 'red';
                        undo = false;
                        setTimeout(() => {
                            el.innerHTML = null;
                            el.style.display = 'none';
                        }, 2000);
                    }else{
                        //Bypass reload
                        window.removeEventListener("beforeunload", preventReload);
                        location.reload();
                    }
                    
                }
        })
        .catch(error => {
            console.log(error);
        })
    }

    document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const point1 = document.querySelector('.team1-button');
    const point2 = document.querySelector('.team2-button');
    const team_btns = document.querySelectorAll('.team-buttons');
    const tech_point = document.querySelector('.tech-point');

    // Get existing containers
    const container3 = document.querySelector('.container3');
    const container4 = document.querySelector('.container4');
    const container5 = document.querySelector('.container5');
    const container6 = document.querySelector('.container6');
    const slideContainer = document.querySelector('.slide-container');
    const slideWrapper = document.querySelector('.slide-wrapper');
    const exit_inning = document.querySelector('.exit');
    const undo_logs = document.querySelector('.undo');
    
    // Wrap containers in sliding parent
    if (slideContainer && container3 && container4 ) {
    slideContainer.appendChild(container3);
    slideContainer.appendChild(container4);
    } else {
        console.warn("One or more containers not found in the DOM.");
    }
    
    const player1Names = document.querySelectorAll('.container3 .player-replace');
    const player2Names = document.querySelectorAll('.container4 .player-replace');
    const inButton = document.querySelector('.in');
    const aceButton = document.querySelector('.ace');
    const errorButton = document.querySelector('.error');


    // Current slide position (0=container3, 1=container4)
    let currentSlide = 0;

    window.addEventListener('DOMContentLoaded', () => {
        team_btns.forEach(btn => {
                    if (btn.getAttribute('data-team') !== chasing_team) {
                        btn.disabled = true;
                        btn.style.pointerEvents = 'none'; // Optional: disable pointer events
                        btn.style.opacity = 0.5; // Optional: fade out other buttons
                    } else {
                        btn.disabled = false; // Optional: keep chaser button enabled
                    }
                });
    });

    exit_inning.addEventListener('click',()=>{
        exit_inn = true;
        get_score();
    })

    undo_logs.addEventListener('click',()=>{
        console.log('undo..');
        undo = true;
        get_score();
    });

    complete_btn.addEventListener('click', () => {
        is_complete = true;
        raider = null;
        get_score();
    });

    start_next_btn.addEventListener('click', () => {
        is_complete = true;
        raider = null;
        get_score();
    });

    let cancel_end = () => {
        let cancel = document.querySelector('#start_second');
        cancel.close();
        end_half = false;
    }

    // Open modal
    team_btns.forEach(selector => {
        selector.addEventListener('click', (event) => {
            if(select_runners()){

            }else{  
                const team = event.currentTarget.getAttribute('data-team');
                point_taken_by = team;
                if (chasing_team == team) {
                    currentSlide = 0;
                    updateSlidePosition();
                    slideWrapper.style.transform = 'translateY(0)';

                    // Disable all other buttons
                    team_btns.forEach(btn => {
                        if (btn.getAttribute('data-team') !== chasing_team) {
                            btn.disabled = true;
                            btn.style.pointerEvents = 'none'; // Optional: disable pointer events
                            btn.style.opacity = 0.5; // Optional: fade out other buttons
                        } else {
                            btn.disabled = false; // Optional: keep chaser button enabled
                        }
                    });
                }
            }
        });
    });
    
    let select_runners = () => {
        if(is_empty_runners == true){
            container5.style.transform = 'translateY(0)';
            container5.style.display = 'block';
            return true;
        }
        return false;
    }
    select_runners();

    // Update container position
    function updateSlidePosition() {
        slideContainer.style.transform = `translateX(-${currentSlide * 50}%)`;
    }

    // Navigation
    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
        updateSlidePosition();
    }

    // Event listeners
    player1Names.forEach(player => {
        player.addEventListener('click', (el) => {
            console.log(player.innerText);
            goToSlide(1);
            chaser = player.getAttribute('data-player-id');
        })
    });
    player2Names.forEach(player => {
        player.addEventListener('click', (el) => {
            goToSlide(1);
            console.log(player.innerText);
            out_player = player.getAttribute('data-player-id');

            // ✅ Slide back the wrapper to hide modal
            slideWrapper.style.transition = 'transform 0.5s ease';
            slideWrapper.style.transform = 'translateY(600px)';

            // ✅ Reset slide position to container3
            setTimeout(() => {
                slideContainer.style.transform = 'translateX(0)';
            }, 300); // Wait for the slideWrapper to finish sliding out

            get_score();
        });
    });

    tech_point.addEventListener('click', () => {
        if(select_runners()){
            
        }else{
            console.log("Technical Point!");
            container6.style.transform = 'translateY(0)';
        }
    });

    document.querySelectorAll('.teams-info').forEach(el => {
        el.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent event from bubbling up
            const teamId = e.currentTarget.getAttribute('data-team');
            point_taken_by = teamId;
            console.log('Technical : ', teamId);
            get_score();
        });
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

    let startYa = 0;
    const thresholda = 60;

    container5.addEventListener('touchstart', (e) => {
        startYa = e.touches[0].clientY;
        container5.style.transition = 'none';
    });

    container5.addEventListener('touchmove', (e) => {
        const diffY = e.touches[0].clientY - startYa;
        if (diffY > 0) {
            container5.style.transform = `translateY(${diffY}px)`;
        }
    });

    container5.addEventListener('touchend', (e) => {
        const diffY = e.changedTouches[0].clientY - startYa;
        container5.style.transition = 'transform 0.5s ease';
        
        if (diffY > thresholda) {
            container5.style.transform = 'translateY(600px)';
        } else {
            container5.style.transform = 'translateY(0)';
        }
    });
    
    

        document.querySelectorAll('.teams-info').forEach(element => {
            element.addEventListener('click', () => {
                    container6.style.transform = 'translateY(600px)';
            });
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

                    setTimeout(() => {
                        slideContainer.style.transform = 'translateX(0)';
                    }, 300);
                }
            });
        });
    });



});

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

document.addEventListener('DOMContentLoaded', () => {
    const playerBoxes = document.querySelectorAll('.container5 .player-replace');
    const maxAllowed = 3;
    const selectedPlayerIds = [];

    playerBoxes.forEach(box => {
        box.addEventListener('click', () => {
            const playerId = box.getAttribute('data-player-id');

            if (box.classList.contains('selected')) {
                // Deselect
                box.classList.remove('selected');
                const index = selectedPlayerIds.indexOf(playerId);
                if (index !== -1) selectedPlayerIds.splice(index, 1);
            } else {
                // Select if less than 3 selected
                if (selectedPlayerIds.length < maxAllowed) {
                    box.classList.add('selected');
                    selectedPlayerIds.push(playerId);

                    // ✅ Trigger fetch when 3 are selected
                    if (selectedPlayerIds.length === maxAllowed) {
                        console.log("Triggering fetch with:", selectedPlayerIds);

                        fetch('./Backend/add_runners.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                player_ids: selectedPlayerIds,
                                Inning : current_inning,
                                match_id: match // Replace or set dynamically
                            })
                        })
                        .then(res => res.json())
                        .then(response => {
                            console.log('Server response:', response);
                            // Optionally show message or disable further selection
                            if(response.status == 200){

                               let container5 = document.querySelector('.container5');
                                container5.style.transition = 'transform 0.5s ease';
                                container5.style.transform = 'translateY(600px)';

                                setTimeout(() => {
                                    //Bypass reload
                                    window.removeEventListener("beforeunload", preventReload);
                                    location.reload();
                                }, 600);
                                
                            }
                        })
                        .catch(err => {
                            console.error('Fetch error:', err);
                        });
                    }

                } else {
                    alert("Only 3 players can be selected.");
                }
            }

            console.log("Selected Player IDs:", selectedPlayerIds);
        });
    });
});

   
</script>
</body>

</html>