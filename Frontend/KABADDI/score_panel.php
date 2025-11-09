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
    $current_raid = null;

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
    if ($score_log['match_completed']) {
        header('Location: ../../dashboard.php?update=live&sport=VOLLEYBALL');
        exit();
    }

   // Detect current half and last raid
    $current_half = $score_log['current_half'];
    $half_data = $score_log['halves'][$current_half];
    $last_raid = null;

    if (!empty($half_data['raids'])) {
        $last_raid = end($half_data['raids']);
    }

    if (empty($last_raid)) {
        $last_half_number = array_key_last($score_log['halves']);
        $raids_array = $score_log['halves'][$last_half_number]['raids'] ?? [];
        $last_raid = end($raids_array);
    }

    $current_raid = $score_log['current_raid_team'];

    $team1_role = null;
    $team2_role = null;
    if($current_raid == $score_log['team1']){
        $team1_role = 'Raiding';
        $team2_role = 'Defending';
    }else{
        $team1_role = 'Defending';
        $team2_role = 'Raiding';
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabaddi Scoreboard</title>
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
            --primary-color: rgba(209, 34, 31, 1);
            --primary-light: rgba(209, 34, 31, 0.8);
            --primary-dark: rgba(160, 25, 23, 1);
            --primary-transparent: rgba(212, 53, 50, 0.15);
            --background: #ffffff;
            --text-color: #000000;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
            --border-color: #dddddd;
            --special-color: #e3e3e3;
        }

        [data-theme="dark"] {
            --background: #121212;
            --text-color: #ffffff;
            --light-bg: #1e1e1e;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            --border-color: #333333;
            --primary-transparent: rgba(210, 31, 28, 0.29);
            --special-color: #3f3f3f;
        }

        body {
            min-height: 100vh;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: var(--transition);
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('https://images.unsplash.com/photo-1605135693932-f2d4d1f3e9e8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: -1;
        }

        svg path {
            fill: var(--text-color);
        }

        #commentaryIcon svg {
            stroke: var(--text-color);
        }

        .container0 {
            position: relative;
            height: max-content;
            width: 100%;
            max-width: 800px;
            background: var(--background);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: auto;
            scroll-behavior: smooth;
        }
        html {
            scroll-behavior: smooth;
            }

            


        .return {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
            padding: 20px 5%;
            background-color: var(--background);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: var(--transition);
            z-index: 11;
        }

        .return svg {
            cursor: pointer;
            fill: var(--primary-color);
            transition: var(--transition);
        }

        .for-exit {
            display: flex;
            justify-content: center;
            background-color: var(--background);
        }

        .exit {
            width: 179px;
            height: 50px;
            background-color: var(--background);
            border-radius: 85px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            font-weight: bold;
            transition: var(--transition);
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
            padding-bottom: 20px;
            background-color: var(--background);
            position: relative;
            z-index: 2;
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
            background-color: var(--background);
            border-radius: 50%;
            border: 3px solid var(--primary-light);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .team-logo img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .score1, .score2 {
            color: var(--primary-color);
            font-size: 4.5rem;
            font-weight: 400;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
            transition: var(--transition);
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
            color: var(--text-color);
            transition: var(--transition);
        }

        .container1 {
            position: relative;
            z-index: 100;
        }

        .container2 {
    position: relative;        /* activate proper stacking */
    width: 100%;
    min-height: calc(100vh - 120px); /* adaptive height instead of fixed */
    background-color: var(--background);
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    transition: var(--transition);
}


        .scoreboard {
            width: 100%;
            display: flex;
            align-items: flex-start;
        }

        .blur-container {
            top: 0;
            position: absolute;
            height: 100%;
            width: 100%;
            background-color: transparent;
            filter: blur(3px);
            z-index: 1;
            background-position: center;
            background-image: url("https://i.ibb.co/Vpk3fcvs/kabaddi-m.jpg");
            background-size: cover;
            -webkit-mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            -webkit-mask-size: 100% 100%;
            mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            opacity: 0.4;
            transition: var(--transition);
        }

        [data-theme="dark"] .blur-container {
            opacity: 0.25;
            filter: blur(3px) brightness(0.6);
        }

        .scoring {
            border-bottom: 3px solid var(--primary-light);
            padding-bottom: 6px;
            color: var(--text-color);
            transition: var(--transition);
        }

        .current-set {
            position: relative;
            z-index: 5;
            width: 100%;
            height: 55px;
            background-color: var(--primary-light);
            align-content: center;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 22px;
            border-bottom: 3px solid var(--background);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: var(--transition);
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
            padding: 30px;
        }

        .team-button {
            width: 150px;
            height: 70px;
            background-color: var(--background);
            color: var(--primary-color);
            border: none;
            border-radius: 15px;
            font-weight: bold;
            font-size: 19px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .team-button:active {
            transform: translateY(1px);
        }

        .serve, .raider {
            width: 220px;
            height: 70px;
            background-color: var(--primary-color);
            color: white;
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            position: relative;
            z-index: 4;
            font-size: 1.2rem;
            font-weight: bold;
            transition: var(--transition);
        }

        .raider {
            background-color: white;
            color: var(--primary-color);
            border-radius: 15px;
            border: none;
            font-weight: bold;
            transition: var(--transition);
        }

        .team-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .raid-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .serve-timer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .serve-undo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .optional {
            color: var(--text-color);
            font-size: 0.9rem;
            opacity: 0.7;
            transition: var(--transition);
        }

        .raider-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .team-name {
            color: var(--text-color);
            font-weight: bold;
            font-size: 0.8rem;
            transition: var(--transition);
        }

        button {
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
        }

        .history {
            width: 100%;
            height: max-content;
            margin-top: 20px;
            padding: 10px;
            border-radius: 10px 10px 0 0;
            transition: var(--transition);
        }

        .log {
            position: relative;
            z-index: 5;
            height: 125px;
            width: 100%;
            margin-top: 10px;
            background-color: var(--background);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .serving {
            height: 40px;
            width: 100%;
            border-bottom: 1px solid var(--border-color);
            align-content: center;
            padding: 8px;
            font-weight: bold;
            font-size: 1.3rem;
            color: var(--primary-color);
            background-color: var(--special-color);
            transition: var(--transition);
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
            background-color: var(--light-bg);
            transition: var(--transition);
        }

        .point-text {
            font-weight: bold;
            font-size: 1.1rem;
            color: var(--text-color);
            opacity: 0.8;
            transition: var(--transition);
        }

        .last-update {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
            transition: var(--transition);
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
            background: var(--background);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            border-top: 2px solid var(--primary-light);
            transition: var(--transition);
        }

        /* Container parent for horizontal sliding */
        .slide-container {
            display: flex;
            width: 300%;
            height: 100%;
            transition: transform 0.5s ease;
        }

        /* Individual containers */
        .container3, .container4, .container5 {
            flex: 0 0 33.33%;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background: var(--background);
            color: var(--text-color);
            transition: var(--transition);
        }

        .current-server {
            width: 100%;
            height: 48px;
            border-top: 2px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 15px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .curr-ser {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .tap {
            font-size: 0.8rem;
            color: var(--text-color);
            opacity: 0.7;
            transition: var(--transition);
        }

        .players-info {
            width: 100%;
            height: 492px;
            padding-top: 35px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .player-replace {
            width: 95%;
            height: 50px;
            background-color: var(--background);
            display: flex;
            align-items: center;
            padding-left: 15px;
            font-size: 1.1rem;
            margin: 10px auto;
            border-radius: 10px;
            transition: var(--transition);
            border: 1px solid var(--border-color);
            border-left: 2px solid var(--primary-color);
        }

        .t1-points {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
        }

        .player-cnt {
            padding-left: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--text-color);
            transition: var(--transition);
        }

        .player-rel {
            width: 100%;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 15px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .replace {
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--text-color);
            opacity: 0.7;
            transition: var(--transition);
        }

        .player-replace {
            display: flex;
            justify-content: space-between;
        }

        .serve-result {
            width: 100%;
            height: 48px;
            border-top: 2px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 15px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .ser-res {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .outcome {
            font-size: 0.8rem;
            color: var(--text-color);
            opacity: 0.7;
            transition: var(--transition);
        }

        .outcomes {
            width: 100%;
            height: max-content;
            display: grid;
            align-items: center;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 15px;
            justify-content: center;
            justify-items: center;
            background-color: var(--background);
            transition: var(--transition);
        }

        .score-point, .score-point2 {
            width: 90%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--background);
            border-radius: 15px;
            font-size: 2rem;
            font-weight: bold;
            transition: var(--transition);
            border: 2px solid var(--primary-light);
            color: var(--primary-color);
            flex-direction: column;
            gap: 5px;
        }

        .tech-score-point, .tech-score-point2 {
            width: 90%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--background);
            border-radius: 15px;
            font-size: 2rem;
            font-weight: bold;
            transition: var(--transition);
            border: 2px solid var(--primary-light);
            color: var(--primary-color);
            flex-direction: column;
            gap: 5px;
        }

        .point-type {
            color: var(--text-color);
            opacity: 0.5;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            padding: 3px;
            transition: var(--transition);
        }

        .replace {
            font-weight: 600;
            font-size: 15px;
            color: var(--primary-color);
            width: 80px;
            height: 40px;
            background-color: transparent;
            border: none;
            outline: none;
            transition: var(--transition);
        }

        .point-assign {
            width: 100%;
            height: 48px;
            border-top: 2px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 15px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .poi-ass {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .who {
            font-size: 0.8rem;
            color: var(--text-color);
            opacity: 0.7;
            transition: var(--transition);
        }

        .teams-info {
            width: 100%;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            background-color: var(--background);
            transition: var(--transition);
        }

        .team1-info, .team2-info {
            width: 160px;
            height: 200px;
            border: 3px solid var(--primary-light);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .teams-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--background);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.5rem;
            border: 2px solid var(--primary-light);
            overflow: hidden;
            transition: var(--transition);
        }

        .teams-logo img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .picture {
            height: 50px;
            width: 50px;
            background-color: transparent;
            border-radius: 50%;
            background-size: cover;
        }

        .picture img {
            height: 100%;
            object-fit: cover;
        }

        .undo {
            position: relative;
            z-index: 5;
            width: 110px;
            height: 50px;
            background-color: var(--background);
            color: var(--primary-color);
            border-radius: 15px;
            outline: none;
            border: 2px solid var(--primary-color);
            font-weight: bold;
            transition: var(--transition);
        }

        .undo-btn {
            margin-top: 20px;
            font-size: 13px;
        }

        .score-teamlogo {
            display: flex;
            justify-content: space-evenly;
            align-items: end;
            padding: 25px;
            gap: 20px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .timer {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .warning {
            color: var(--primary-color);
            padding: 20px;
            font-weight: 600;
            font-size: 17px;
            transition: var(--transition);
        }

        #match_completed, #start_second, #half_completed {
            position: fixed;
            transform: translateX(-50%) translateY(-50%);
            top: 50%;
            left: 50%;
            width: 300px;
            border: none;
            height: max-content;
            background: var(--special-color);
            transition: all 0.5s ease-in-out;
            align-items: flex-start;
            padding: 20px;
            z-index: 99;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            flex-direction: column;
            justify-content: center;
            align-items: center;
            scrollbar-width: none;
        }

        #match_completed::backdrop, #start_second::backdrop, #half_completed::backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.15);
        }

        .undo-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .undo-txt {
            font-size: 25px;
            font-weight: bold;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .undo-warn {
            font-size: 18px;
            color: var(--text-color);
            opacity: 0.7;
            letter-spacing: 1px;
            text-align: center;
            transition: var(--transition);
        }

        .undo-btn, .super-over-btn {
            height: 40px;
            width: 130px;
            color: white;
            outline: none;
            border: none;
            background: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .undo-cancel, .complete-cancel {
            color: var(--text-color);
            opacity: 0.7;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
        }

        .outcomes.tech-info {
            max-height: 0;
            overflow: hidden;
            display: grid;
            transition: max-height 0.4s ease, opacity 0.3s ease;
            opacity: 0;
            font-weight: bold;
            border-bottom: 2px solid var(--primary-light);
        }

        .outcomes.tech-info.active {
            max-height: 300px;
            opacity: 1;
        }

        @keyframes shake {
            0% { transform: translateX(-50%) translateY(-50%) translateX(0); }
            25% { transform: translateX(-50%) translateY(-50%) translateX(-10px); }
            50% { transform: translateX(-50%) translateY(-50%) translateX(10px); }
            75% { transform: translateX(-50%) translateY(-50%) translateX(-10px); }
            100% { transform: translateX(-50%) translateY(-50%) translateX(0); }
        }

        .shake {
            animation: shake 0.4s;
        }

        .opacity-container {
            height: 100%;
            width: 100%;
            position: absolute;
            background: rgba(0, 0, 0, 0.6);
            z-index: 5;
            display: none;
        }

        button:disabled {
            background: var(--border-color);
            color: var(--text-color);
            opacity: 0.5;
            cursor: not-allowed;
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
                font-size: 19px;
            }
            
            .serve, .raider {
                width: 220px;
                height: 60px;
            }
        }

        @media (min-width: 601px) {
            

            .team-data {
                display: flex;
                flex-direction: column;
            }
            
            .score1, .score2 {
                font-size: 5rem;
            }
            
            .team-button {
                width: 180px;
                height: 80px;
                font-size: 19px;
            }
            
            .tech-button .team-button {
                width: 120px;
                height: 50px;
                font-size: 17px;
            }
            
            .serve, .raider {
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
                align-items: flex-end;
            }
            
            .left {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                flex-direction: row-reverse;
                width: 100%;
                align-items: flex-end;
            }
            
            .container2 {
                width: 100%;
            }
        }

        .tech-button.team-button {
            width: 110px;
            height: 50px;
            font-size: 17px;
        }
    </style>
</head>
<body>
    <div class="container0">

        <div class="opacity-container"></div>

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
                        <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="#F83900"/>
                    </svg>
                </div>
            </div>

            <div class="for-exit">
                <div class="exit" onclick="end_halfs()">
                    <!-- <div class="for-icon1">
                        <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.36196 6.62029L11.672 1.04729C11.7606 0.954302 11.8101 0.830761 11.8101 0.70229C11.8101 0.573819 11.7606 0.450279 11.672 0.357291L11.666 0.35129C11.623 0.306055 11.5713 0.270036 11.5139 0.245422C11.4566 0.220808 11.3949 0.208115 11.3325 0.208115C11.2701 0.208115 11.2083 0.220808 11.151 0.245422C11.0937 0.270036 11.0419 0.306055 10.999 0.35129L5.99896 5.59929L1.00096 0.35129C0.95799 0.306055 0.906263 0.270036 0.84893 0.245422C0.791597 0.220808 0.729857 0.208115 0.667463 0.208115C0.60507 0.208115 0.543329 0.220808 0.485996 0.245422C0.428663 0.270036 0.376937 0.306055 0.333963 0.35129L0.327963 0.357291C0.239318 0.450279 0.189867 0.573819 0.189867 0.70229C0.189867 0.830761 0.239318 0.954302 0.327963 1.04729L5.63796 6.62029C5.68466 6.6693 5.74082 6.70832 5.80305 6.73498C5.86528 6.76164 5.93227 6.77539 5.99996 6.77539C6.06766 6.77539 6.13465 6.76164 6.19688 6.73498C6.2591 6.70832 6.31527 6.6693 6.36196 6.62029Z" fill="#F83900"/>
                        </svg>
                    </div> -->
                    <div class="exit-text">
                    <?php
                        echo 'End Half '. $current_half;
                    ?>
                    </div>
                </div>
            </div>

            <div class="score-teamlogo">
                <div class="score2"><?php echo $score_log['team1_score'];?></div>
                <div class="score1"><?php echo $score_log['team2_score']; ?></div>
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
                                <label class="team1_pos"><?php echo $team1_role; ?></label>
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
                                <label class="team2_pos"><?php echo $team2_role; ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container2">
            
            <div class="image"></div>
                <div class="current-set">Half No. <?php echo $current_half; ?></div>
            <div class="blur-container"></div>

            <div class="buttons">

                <div class="serve-button">
                    <div class="raid-btn">
                        
                        <div class="raider-info">
                                <button class="raider">Select Raider</button>
                        </div>
                        <div class="serve-undo">
                            <div class="serve-timer">
                                <label class="team-name timer"></label>
                                <button class="serve">Start Raid Timer</button>
                            </div>
                            <div id="error-empty" class="error"></div>
                            <button class="undo">Undo</button>
                        </div>
                    </div>
                </div>

                <div class="history">
                    <?php
                        $result2 = mysqli_query($conn, "SELECT score_log FROM matches WHERE match_id = '$match_id'");
                        $row2 = mysqli_fetch_assoc($result2);
                        $score_log = json_decode($row2['score_log'], true);

                        $raids = $score_log['halves'][$current_half]['raids'];
                        $last_three_raids = array_reverse(array_slice($raids, -3));
                        $team1_id = null;
                        $team2_id = null;
                        $raiding = null;
                        $defence = null;
                        // Print them nicely
                        foreach ($last_three_raids as $raid) {

                            if($raid['current_raid_team'] == $score_log['team1']){
                                $team1_id = $raid['current_raid_team'];
                                $team2_id = $score_log['team2'];
                                $raiding = $raid['current_raid_team'];
                                $defence = $score_log['team2'];
                            }else{
                                $team1_id = $score_log['team1'];
                                $team2_id = $raid['current_raid_team'];
                                $raiding = $raid['current_raid_team'];
                                $defence = $score_log['team1'];
                            }
                            

                            // Prepare query with IN (?, ?)
                            $stmt = $conn->prepare("SELECT * FROM teams WHERE t_id IN (?,?,?,?)");
                            $stmt->bind_param("ssss",$team1_id, $team2_id, $raiding, $defence);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Store names in associative array
                            $team_names = [];
                            while ($row = $result->fetch_assoc()) {
                                $team_names[$row['t_id']] = $row['t_name'];
                            }
                        
                    ?>
                    <div class="log">
                        <div class="serving"><?php echo $team_names[$raiding]; ?> raiding</div>
                        <div class="point-to-update">
                            <div class="point-to">
                                <div class="t1-points">
                                    <label class="point-text">Point - <?php echo $raid['raid points']; ?></label>
                                    <label class="to_team-name">to <?php echo $team_names[$raiding]; ?></label>
                                </div>
                                <div class="t1-points">
                                    <label class="point-text">Point - <?php echo $raid['def points']; ?></label>
                                    <label class="to_team-name">to <?php echo $team_names[$defence]; ?></label>
                                </div>
                            </div>
                            <div class="last-update"><?php echo $raid['last score']; ?></div>
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
                        <label class="curr-ser">Current Raider</label>
                        <label class="tap">Tap to choose the raiding player</label>
                    </div>
                    <?php
                        $query = "SELECT * FROM `players` WHERE `team_id` = '$current_raid'";
                        $result = mysqli_query($conn, $query);
                        $count = mysqli_num_rows($result);
                    ?>   
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

                <div class="container5">
                     <div class="serve-result">
                        <label class="ser-res">Defence Result</label>
                        <label class="outcome">Outcome of the Raid</label>
                    </div>
                    <div class="outcomes tech-info">
                        <label for="" class="tech-p">Technical Point?</label>
                        <div class="tech-score-point2" onclick="deftakepoint(this)">0 <label for="" class="point-type">No Point</label></div>
                        <div class="tech-score-point2" onclick="deftakepoint(this)">1 <label for="" class="point-type">+1 Tech.</label></div>
                    </div>
                    <div class="outcomes">
                        <div class="score-point2">0<label for="" class="point-type">No Point</label></div>
                        <div class="score-point2">1<label for="" class="point-type">Tackel</label></div>
                        <div class="score-point2">2<label for="" class="point-type">Sup-Tackel</label></div>
                        <div class="score-point2">3<label for="" class="point-type">Tackel + All Out</label></div>
                    </div>
                </div>

                <div class="container4">
                    <div class="serve-result">
                        <label class="ser-res">Raid Result</label>
                        <label class="outcome">Outcome of the Raid</label>
                    </div>
                    <div class="outcomes tech-info">
                        <label for="" class="tech-p">Technical Point?</label>
                        <div class="tech-score-point" onclick="getraidtechpoint(this)">0 <label for="" class="point-type">No Point</label></div>
                        <div class="tech-score-point" onclick="getraidtechpoint(this)">1 <label for="" class="point-type">+1 Tech.</label></div>
                    </div>
                    <div class="outcomes">
                        <div class="score-point" >0 <label for="" class="point-type">No point</label></div>
                        <div class="score-point" >1 <label for="" class="point-type">Touch</label></div>
                        <div class="score-point" >2 <label for="" class="point-type">Touch / Extra</label></div>
                        <div class="score-point" >3 <label for="" class="point-type">Touch / Extra</label></div>
                        <div class="score-point" >4 <label for="" class="point-type">Touch / Extra</label></div>
                        <div class="score-point" >5 <label for="" class="point-type">Touch / Extra</label></div>
                        <div class="score-point" >6 <label for="" class="point-type">Touch / Extra</label></div>
                        <div class="score-point" >7 <label for="" class="point-type">Touch / Extra</label></div>
                        <div class="score-point" >8 <label for="" class="point-type">Touch + Bonus</label></div>
                        <div class="score-point" >9 <label for="" class="point-type">Touch + All Out</label></div>
                        <div class="score-point" >10 <label for="" class="point-type">Touch + Bonus + All Out</label></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <audio id="beep-sound" src="../../assets/sounds/preview.mp3" preload="auto"></audio>
    <script>
        const back_decision = <?php echo json_encode($back_decision) ?>;
        let current_raid = <?php echo json_encode($current_raid) ?>;
        let current_half = <?php echo json_encode($current_half) ?>;
        let match_id = <?php echo json_encode($match_id) ?>;
        let undo_button = document.querySelector('.undo');
        let match_dialogue = document.querySelector('#match_completed');
        let start_dialogue = document.querySelector('#start_second');
        let opacity = document.querySelector('.opacity-container');
        let start_next_btn = document.querySelector('.start-next-btn');
        let complete_btn = document.querySelector('.complete-match-btn');
        let raider = null;
        let raid_tech_point = null;
        let def_tech_point = null;
        let raid_points = null;
        let def_points = null;
        let point_type = null;
        let is_complete = false;
        let is_start = false;
        let undo = false;
        let end_half = false;
        let get_score = () => {


            let data = {
                'current_raid_team' : current_raid,
                'raider': raider,
                'undo': undo,
                'Isend_half' : end_half,
                'match_id': match_id,
                ...(is_complete ? { 'is_complete': is_complete } : {}),
                ...(is_start ? { 'is_start': is_start } : {}),
                'raid_tech_point': raid_tech_point,
                'def_tech_point': def_tech_point,
                'raid_points': raid_points,
                'def_points': def_points
            };

            console.log(data);

            fetch('./Backend/update-kabaddi-logs.php',{
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

        document.querySelectorAll('[id^="error-"]').forEach((el) => {
        el.innerHTML = '';
        el.style.display = 'none';
        });

        undo_button.addEventListener('click', () => {
                undo = true;
                raider = null;
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

        let proceed_end_half = () => {
            end_half = true;
            raider = null;
            get_score();
        }

        let end_halfs = () => {
            if(current_half != 1){
                end_half = true;
                raider = null;
                get_score();
            }else{
                let half_container = document.querySelector('#half_completed');
                half_container.showModal();
                half_container.classList.add('shake');
                navigator.vibrate([1000,50,100,50,100]);
            }
            
        }
            // DOM Elements
            const raiderBtn = document.querySelector('.raider');
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
            
            const playerNames = document.querySelectorAll('.player-replace');
            const scorepoint = document.querySelectorAll('.score-point');
            const scorepoint2 = document.querySelectorAll('.score-point2');
            const tech_info = document.querySelectorAll('.tech-info');

            // Current slide position (0=container3, 1=container4, 2=container5)
            let currentSlide = 0;

            // Open modal
            raiderBtn.addEventListener('click', () => {
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
                player.addEventListener('click', (el) => {
                    goToSlide(1);
                    getplayername(player);
                    // player.style.backgroundColor = "var(--text-color)";
                    raider=player.innerText;
                })
            });
            
            let getplayername = (el) => {
                raider = el.innerText;
            }

            
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



            // let score_point = () => {
                scorepoint.forEach(selector => {
                    selector.addEventListener("click", () => {
                        if (slideWrapper) {
                            raid_points = parseInt(selector.innerText);
                            setTimeout(() => {
                                const tech_info = container4.querySelector('.outcomes.tech-info');
                                if (tech_info) {
                                    tech_info.classList.add("active");
                                }
                            }, 300);


                            selector.style.border = "2px solid var(--text-color)";
                            cancelRaidTimer();
                         
                        }
                    });
                    
                });
            // };

                scorepoint2.forEach(selector => {
                    selector.addEventListener("click", () => {
                        if (slideWrapper) {
                            def_points = parseInt(selector.innerText);
                            setTimeout(() => {
                                const tech_info = container5.querySelector('.outcomes.tech-info');
                                if (tech_info) {
                                    tech_info.classList.add("active");
                                }
                            }, 300);


                            selector.style.border = "2px solid var(--text-color)";
                            cancelRaidTimer();

                        
                        }
                    });
                    
                });

                let getraidtechpoint = (el) => {
                    setTimeout(() => {
                        if (el && el.textContent) {
                            goToSlide(2);
                            raid_tech_point = parseInt(el.textContent);
                        } else {
                            console.warn("Invalid element passed to getraidtechpoint:", el);
                        }
                    }, 300);
                     raid_tech_point = parseInt(el.textContent);
                            el.style.border = "2px solid var(--text-color)";
                            cancelRaidTimer();
                }


                let deftakepoint = (el) => {
                    setTimeout(() => {
                        if (el && el.textContent) {
                            
                            slideWrapper.style.transform = 'translateY(600px)';

                        } else {
                            console.warn("Invalid element passed to getraidtechpoint:", el);
                        }
                    }, 300);
                    def_tech_point = parseInt(el.textContent);
                            el.style.border = "2px solid var(--text-color)";
                            cancelRaidTimer();


                    get_score();
                    
                }

            
            let raidTimer;
            let timeLeft = 30;

            serveBtn.addEventListener('click', () => {
                const isStarting = serveBtn.innerText === 'Start Raid Timer';

                if (isStarting) {
                    serveBtn.innerText = 'Cancel';
                    startRaidTimer();
                } else {
                    serveBtn.innerText = 'Start Raid Timer';
                    document.querySelector('.timer').innerText = '';
                    cancelRaidTimer();
                }
            });

            function startRaidTimer() {
                const beepSound = document.getElementById("beep-sound");
                beepSound.src = '../../assets/sounds/preview.mp3';
                timeLeft =30;

                raidTimer = setInterval(() => {
                    timeLeft--;
                    document.querySelector('.timer').innerText = `00:${timeLeft < 10 ? '0' + timeLeft : timeLeft}`;

                    if (timeLeft <= 5 && timeLeft > 0) {
                        beepSound.currentTime = 0; // reset to start
                        beepSound.play();
                        navigator.vibrate(100);
                    }

                    if (timeLeft <= 0) {
                        clearInterval(raidTimer);
                        document.querySelector('.timer').innerText = '';
                        serveBtn.innerText = 'Start Raid Timer';
                        

                        // Set long beep sound source
                        beepSound.src = '../../assets/sounds/mixkit-censorship-beep-long-1083.wav';

                        // Play the long beep
                        beepSound.currentTime = 0;
                        beepSound.play();
                        navigator.vibrate(500);

                        // Stop (pause) it after 3 seconds
                        setTimeout(() => {
                        beepSound.pause();
                        beepSound.currentTime = 0;
                        }, 1500);
                    }
                }, 1000);
            }

            function cancelRaidTimer() {
                clearInterval(raidTimer);
                document.querySelector('.timer').innerText = '';
                serveBtn.innerText = 'Start Raid Timer';
            }

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

        // Click outside to dismiss feature
document.addEventListener('click', (e) => {

    // If the slide-out container is currently visible
    const isOpen = slideWrapper.style.transform !== 'translateY(600px)';

    // Check if the clicked element is *NOT* inside the slide wrapper
    if (isOpen && !slideWrapper.contains(e.target) && !raiderBtn.contains(e.target)) {
        slideWrapper.style.transition = 'transform 0.5s ease';
        slideWrapper.style.transform = 'translateY(600px)';
        cancelRaidTimer(); // optional if you want timer to stop when dismissed
    }

});

        // Disable right-click
  //document.addEventListener('contextmenu', event => event.preventDefault());

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