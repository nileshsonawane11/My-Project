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
    if(empty($score_log['innings'][$current_inning]['current_runners'])) {
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
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
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

        .exit {
            width: 150px;
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

        .exit:hover {
            background-color: var(--primary-color);
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
            justify-content: space-between;
            background-color: var(--background);
            transition: var(--transition);
            z-index: 1;
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
            justify-content: center;
            align-items: center;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: var(--transition);
        }

        .score1, .score2 {
            color: var(--primary-color);
            font-size: 4.5rem;
            font-weight: bold;
            margin-bottom: 30px;
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

        .set {
            font-size: 1rem;
            color: var(--text-color);
            opacity: 0.7;
            transition: var(--transition);
        }

        .container2 {
            position: relative;
            width: 100%;
            height: 80vh;
            background: var(--background);
            transition: var(--transition);
            overflow: auto;
        }

        #error-empty {
            display: none;
        }

        .blur-container {
            top: 0;
            position: absolute;
            height: 100%;
            width: 100%;
            background-color: transparent;
            filter: blur(3px);
            z-index: 10;
            background-position: center;
            background-image: url("https://i.ibb.co/SDvv2YTd/179380-auhvxpvqhb-1662286409.jpg");
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
            border-bottom: 3px solid var(--primary-light);
            padding-bottom: 6px;
            color: var(--text-color);
            transition: var(--transition);
        }

        .current-set {
            position: relative;
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
            z-index: 101;
        }

        .buttons {
            position: absolute;
            width: 100%;
            height: 300px;
            background-color: transparent;
            z-index: 100;
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

        .team1-button, .team2-button {
            width: 160px;
            height: 90px;
            background-color: white;
            color: var(--primary-color);
            border: none;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .team1-button:hover, .team2-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            background-color: var(--primary-color);
            color: white;
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
            color: var(--text-color);
            font-weight: bold;
            font-size: 1.3rem;
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
            background-color: transparent;
            margin-top: 20px;
            padding: 10px;
            border-top: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .log {
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
            font-size: 1.8rem;
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

        .container5 {
            transform: translateY(600px);
            position: fixed;
            bottom: 0;
            height: 100%;
            width: 100%;
            height: 543px;
            overflow-y: auto;
            transition: transform 0.5s ease;
            z-index: 1000;
            background: var(--background);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            border-top: 2px solid var(--primary-light);
            transition: var(--transition);
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
            width: 200%;
            height: 100%;
            transition: transform 0.5s ease;
        }

        /* Individual containers */
        .container3, .container4 {
            flex: 0 0 50%;
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
            height: max-content;
            padding-top: 35px;
            background-color: var(--background);
            transition: var(--transition);
        }

        .player-replace:hover {
            background-color: var(--light-bg);
            transform: translateX(5px);
            border-left: 3px solid var(--primary-color);
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

        .assign-later {
            height: 50px;
            width: 155px;
            border-radius: 48px;
            background-color: var(--background);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            border: 2px solid var(--primary-color);
            transition: var(--transition);
        }

        .assign-later:hover {
            background-color: var(--primary-color);
            color: white;
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
            height: 490px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: var(--background);
            transition: var(--transition);
        }

        .in, .ace, .error {
            width: 85%;
            height: 100px;
            border: 2px solid var(--primary-light);
            margin-top: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            padding: 0 5%;
            transition: var(--transition);
            background-color: var(--background);
        }

        .in:hover, .ace:hover, .error:hover {
            background-color: var(--primary-transparent);
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
            color: var(--text-color);
            transition: var(--transition);
        }

        .extra1, .extra2, .extra3 {
            margin-left: auto;
            color: var(--text-color);
            opacity: 0.7;
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

        .team1-info:hover, .team2-info:hover {
            background-color: var(--primary-transparent);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .teams-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--background);
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.5rem;
            border: 2px solid var(--primary-light);
            overflow: hidden;
            transition: var(--transition);
        }

        .team-logo img, .teams-logo img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .undo {
            width: 220px;
            height: 70px;
            background-color: var(--primary-color);
            color: white;
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            position: relative;
            z-index: 4;
            font-size: 1.5rem;
            font-weight: bold;
            transition: var(--transition);
        }

        .undo:hover {
            background-color: var(--primary-dark);
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
            background-color: var(--background);
            transition: var(--transition);
            z-index: 1;
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

        .player-replace {
            width: 95%;
            height: 50px;
            display: flex;
            justify-content: space-between;
            background-color: var(--background);
            display: flex;
            align-items: center;
            padding-left: 15px;
            font-size: 1.1rem;
            margin: 10px auto;
            border-radius: 10px;
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .index {
            position: relative;
            z-index: 10;
        }

        .tech-point {
            width: 110px;
            height: 50px;
            background-color: var(--background);
            color: var(--primary-color);
            border-radius: 15px;
            outline: none;
            border: 2px solid var(--primary-color);
            font-weight: bold;
            transition: var(--transition);
            text-align: center;
            align-content: center;
        }

        .tech-point:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            background-color: var(--primary-color);
            color: white;
        }

        .teams-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--text-color);
            transition: var(--transition);
        }

        #match_completed,
        #start_second,
        #half_completed,
        #undo {
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

        #match_completed::backdrop,
        #start_second::backdrop,
        #half_completed::backdrop,
        #undo::backdrop {
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

        .undo-btn,
        .super-over-btn {
            height: 40px;
            width: 160px;
            font-size: 16px;
            color: white;
            outline: none;
            border: none;
            background: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .undo-btn:hover,
        .super-over-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .undo-cancel, .complete-cancel {
            color: var(--text-color);
            opacity: 0.7;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
        }

        .player-replace.selected {
            background: var(--primary-transparent);
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
            
            .team1-button, .team2-button {
                width: 140px;
                height: 85px;
                font-size: 1.3rem;
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

    <dialog id="half_completed">
            <div class="undo-container">
                <div class="undo-seyup">
                    <p class="undo-warn undo-txt">You really want to End the Set?</p>
                </div>
                <div class="undo-seyup">
                    <button class="undo-btn" onclick="proceed_end_set()">End Set</button>
                </div>
                <div class="undo-seyup">
                    <p class="continue-match-btn complete-cancel" onclick="
                        document.querySelector('#half_completed').close();
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
                    is_complete = false;">Continue Scoring</p>
                </div>
            </div>
        </dialog>

        <dialog id="start_second">
            <div class="undo-container">
                <div class="undo-seyup">
                    <p class="undo-warn undo-txt">Match tied! A winner is required. Please continue scoring.</p>
                </div>
                <div class="undo-seyup">
                    <button class="start-next-btn undo-btn" onclick="document.querySelector('#start_second').close();
                    is_complete = false;window.removeEventListener('beforeunload', preventReload);
                        location.reload();">Continue Scoring</button>
                </div>
                
            </div>
        </dialog>
        <dialog id="undo">
            <div class="undo-container">
                <div class="undo-seyup">
                    <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M72.4125 77.036C72.9522 77.5504 73.674 77.8295 74.4194 77.812C75.1648 77.7944 75.8727 77.4815 76.3875 76.9422C84.2325 68.7185 89.0625 57.431 89.0625 44.9997C89.0625 32.5685 84.2325 21.281 76.3875 13.0572C76.1324 12.7898 75.8271 12.5753 75.4891 12.4259C75.1511 12.2764 74.787 12.195 74.4175 12.1863C74.0481 12.1776 73.6805 12.2418 73.3359 12.3751C72.9912 12.5085 72.6762 12.7084 72.4088 12.9635C72.1413 13.2186 71.9268 13.5238 71.7774 13.8618C71.6279 14.1998 71.5466 14.564 71.5378 14.9334C71.5291 15.3029 71.5933 15.6705 71.7266 16.0151C71.86 16.3598 72.0599 16.6748 72.315 16.9422C79.185 24.1422 83.4375 34.0422 83.4375 44.9997C83.4375 55.9535 79.185 65.861 72.315 73.0572C71.8006 73.5969 71.5214 74.3188 71.539 75.0641C71.5566 75.8095 71.8694 76.5174 72.4088 77.0322M17.5875 77.036C18.1273 76.5217 18.4408 75.814 18.4591 75.0687C18.4774 74.3233 18.199 73.6011 17.685 73.061C10.815 65.8572 6.5625 55.9497 6.5625 44.9997C6.5625 34.0497 10.815 24.1385 17.685 16.9422C18.2002 16.4022 18.4797 15.6796 18.4622 14.9334C18.4446 14.1873 18.1313 13.4787 17.5912 12.9635C17.0512 12.4483 16.3286 12.1687 15.5825 12.1863C14.8363 12.2039 14.1277 12.5172 13.6125 13.0572C5.7675 21.281 0.9375 32.5685 0.9375 44.9997C0.9375 57.431 5.7675 68.7185 13.6125 76.9422C14.1273 77.4815 14.8352 77.7944 15.5806 77.812C16.326 77.8295 17.0478 77.5504 17.5875 77.036ZM40.335 31.4997C40.0725 28.6872 42.24 26.2497 45 26.2497C47.76 26.2497 49.9238 28.6835 49.665 31.4997L47.8125 47.0622C47.7563 47.772 47.4365 48.4351 46.916 48.9209C46.3955 49.4067 45.712 49.6801 45 49.6872C44.288 49.6801 43.6045 49.4067 43.084 48.9209C42.5635 48.4351 42.2437 47.772 42.1875 47.0622L40.335 31.4997ZM49.6875 59.0622C49.6875 60.3054 49.1936 61.4977 48.3146 62.3768C47.4355 63.2559 46.2432 63.7497 45 63.7497C43.7568 63.7497 42.5645 63.2559 41.6854 62.3768C40.8064 61.4977 40.3125 60.3054 40.3125 59.0622C40.3125 57.819 40.8064 56.6267 41.6854 55.7477C42.5645 54.8686 43.7568 54.3747 45 54.3747C46.2432 54.3747 47.4355 54.8686 48.3146 55.7477C49.1936 56.6267 49.6875 57.819 49.6875 59.0622Z" fill="url(#paint0_linear_789_687)"/>
                    <path d="M12.1875 45C12.1875 36.2976 15.6445 27.9516 21.7981 21.7981C27.9516 15.6445 36.2976 12.1875 45 12.1875C53.7024 12.1875 62.0484 15.6445 68.2019 21.7981C74.3555 27.9516 77.8125 36.2976 77.8125 45C77.8125 53.7024 74.3555 62.0484 68.2019 68.2019C62.0484 74.3555 53.7024 77.8125 45 77.8125C36.2976 77.8125 27.9516 74.3555 21.7981 68.2019C15.6445 62.0484 12.1875 53.7024 12.1875 45ZM45 17.8125C41.4297 17.8125 37.8943 18.5157 34.5958 19.882C31.2973 21.2483 28.3001 23.2509 25.7755 25.7755C23.2509 28.3001 21.2483 31.2973 19.882 34.5958C18.5157 37.8943 17.8125 41.4297 17.8125 45C17.8125 48.5703 18.5157 52.1057 19.882 55.4042C21.2483 58.7027 23.2509 61.6999 25.7755 64.2245C28.3001 66.7491 31.2973 68.7517 34.5958 70.118C37.8943 71.4843 41.4297 72.1875 45 72.1875C52.2106 72.1875 59.1258 69.3231 64.2245 64.2245C69.3231 59.1258 72.1875 52.2106 72.1875 45C72.1875 37.7894 69.3231 30.8742 64.2245 25.7755C59.1258 20.6769 52.2106 17.8125 45 17.8125Z" fill="url(#paint1_linear_789_687)"/>
                    <defs>
                    <linearGradient id="paint0_linear_789_687" x1="45" y1="12.1855" x2="45" y2="77.8127" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FAC01F"/>
                    <stop offset="1" stop-color="#F83900"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear_789_687" x1="45" y1="12.1875" x2="45" y2="77.8125" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FAC01F"/>
                    <stop offset="1" stop-color="#F83900"/>
                    </linearGradient>
                    </defs>
                    </svg>
                </div>
                <div class="undo-seyup"><p class="undo-txt">UNDO ?</p></div>
                <div class="undo-seyup"><p class="undo-warn">Cancel the last ball ?</p></div>
                <div class="error" id="error-empty"></div>
                <div class="undo-seyup"><button class="undo-btn" id='undo-btn' onclick="process_undo()">Yes I’m certain</button></div>
                <div class="undo-seyup"><p class="undo-cancel" onclick="document.querySelector('#undo').close();">Cancel</p></div>
            </div>
        </dialog>

        <dialog id="undo">
            <div class="undo-container">
                <div class="undo-seyup">
                    <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M72.4125 77.036C72.9522 77.5504 73.674 77.8295 74.4194 77.812C75.1648 77.7944 75.8727 77.4815 76.3875 76.9422C84.2325 68.7185 89.0625 57.431 89.0625 44.9997C89.0625 32.5685 84.2325 21.281 76.3875 13.0572C76.1324 12.7898 75.8271 12.5753 75.4891 12.4259C75.1511 12.2764 74.787 12.195 74.4175 12.1863C74.0481 12.1776 73.6805 12.2418 73.3359 12.3751C72.9912 12.5085 72.6762 12.7084 72.4088 12.9635C72.1413 13.2186 71.9268 13.5238 71.7774 13.8618C71.6279 14.1998 71.5466 14.564 71.5378 14.9334C71.5291 15.3029 71.5933 15.6705 71.7266 16.0151C71.86 16.3598 72.0599 16.6748 72.315 16.9422C79.185 24.1422 83.4375 34.0422 83.4375 44.9997C83.4375 55.9535 79.185 65.861 72.315 73.0572C71.8006 73.5969 71.5214 74.3188 71.539 75.0641C71.5566 75.8095 71.8694 76.5174 72.4088 77.0322M17.5875 77.036C18.1273 76.5217 18.4408 75.814 18.4591 75.0687C18.4774 74.3233 18.199 73.6011 17.685 73.061C10.815 65.8572 6.5625 55.9497 6.5625 44.9997C6.5625 34.0497 10.815 24.1385 17.685 16.9422C18.2002 16.4022 18.4797 15.6796 18.4622 14.9334C18.4446 14.1873 18.1313 13.4787 17.5912 12.9635C17.0512 12.4483 16.3286 12.1687 15.5825 12.1863C14.8363 12.2039 14.1277 12.5172 13.6125 13.0572C5.7675 21.281 0.9375 32.5685 0.9375 44.9997C0.9375 57.431 5.7675 68.7185 13.6125 76.9422C14.1273 77.4815 14.8352 77.7944 15.5806 77.812C16.326 77.8295 17.0478 77.5504 17.5875 77.036ZM40.335 31.4997C40.0725 28.6872 42.24 26.2497 45 26.2497C47.76 26.2497 49.9238 28.6835 49.665 31.4997L47.8125 47.0622C47.7563 47.772 47.4365 48.4351 46.916 48.9209C46.3955 49.4067 45.712 49.6801 45 49.6872C44.288 49.6801 43.6045 49.4067 43.084 48.9209C42.5635 48.4351 42.2437 47.772 42.1875 47.0622L40.335 31.4997ZM49.6875 59.0622C49.6875 60.3054 49.1936 61.4977 48.3146 62.3768C47.4355 63.2559 46.2432 63.7497 45 63.7497C43.7568 63.7497 42.5645 63.2559 41.6854 62.3768C40.8064 61.4977 40.3125 60.3054 40.3125 59.0622C40.3125 57.819 40.8064 56.6267 41.6854 55.7477C42.5645 54.8686 43.7568 54.3747 45 54.3747C46.2432 54.3747 47.4355 54.8686 48.3146 55.7477C49.1936 56.6267 49.6875 57.819 49.6875 59.0622Z" fill="url(#paint0_linear_789_687)"/>
                    <path d="M12.1875 45C12.1875 36.2976 15.6445 27.9516 21.7981 21.7981C27.9516 15.6445 36.2976 12.1875 45 12.1875C53.7024 12.1875 62.0484 15.6445 68.2019 21.7981C74.3555 27.9516 77.8125 36.2976 77.8125 45C77.8125 53.7024 74.3555 62.0484 68.2019 68.2019C62.0484 74.3555 53.7024 77.8125 45 77.8125C36.2976 77.8125 27.9516 74.3555 21.7981 68.2019C15.6445 62.0484 12.1875 53.7024 12.1875 45ZM45 17.8125C41.4297 17.8125 37.8943 18.5157 34.5958 19.882C31.2973 21.2483 28.3001 23.2509 25.7755 25.7755C23.2509 28.3001 21.2483 31.2973 19.882 34.5958C18.5157 37.8943 17.8125 41.4297 17.8125 45C17.8125 48.5703 18.5157 52.1057 19.882 55.4042C21.2483 58.7027 23.2509 61.6999 25.7755 64.2245C28.3001 66.7491 31.2973 68.7517 34.5958 70.118C37.8943 71.4843 41.4297 72.1875 45 72.1875C52.2106 72.1875 59.1258 69.3231 64.2245 64.2245C69.3231 59.1258 72.1875 52.2106 72.1875 45C72.1875 37.7894 69.3231 30.8742 64.2245 25.7755C59.1258 20.6769 52.2106 17.8125 45 17.8125Z" fill="url(#paint1_linear_789_687)"/>
                    <defs>
                    <linearGradient id="paint0_linear_789_687" x1="45" y1="12.1855" x2="45" y2="77.8127" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FAC01F"/>
                    <stop offset="1" stop-color="#F83900"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear_789_687" x1="45" y1="12.1875" x2="45" y2="77.8125" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FAC01F"/>
                    <stop offset="1" stop-color="#F83900"/>
                    </linearGradient>
                    </defs>
                    </svg>
                </div>
                <div class="undo-seyup"><p class="undo-txt">UNDO ?</p></div>
                <div class="undo-seyup"><p class="undo-warn">Cancel the last ball ?</p></div>
                <div class="error" id="error-empty"></div>
                <div class="undo-seyup"><button class="undo-btn" id='undo-btn' onclick="process_undo()">Yes I’m certain</button></div>
                <div class="undo-seyup"><p class="undo-cancel" onclick="exit_inn = false;document.querySelector('#undo').close();">Cancel</p></div>
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
                echo $current_inning;
                        $result2 = mysqli_query($conn, "SELECT score_log FROM matches WHERE match_id = '$match_id'");
                        $row2 = mysqli_fetch_assoc($result2);
                        $score_log = json_decode($row2['score_log'], true);

                        // Safely check for 'tags' array
                        $tags = $score_log['innings'][$current_inning]['tags'] ?? [];

                        if (!is_array($tags)) {
                            $tags = [];
                        }

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
                                <label class="to_team-name">to <?php echo $team_names[$team1_id] ?? ''; ?></label>
                            </div>
                            <div class="last-update"><?php echo $tags['last score']; ?></div>
                        </div>
                    </div>
                    <?php } ?>
            </div>
        </div>
        <div class="blur-container"></div>
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
                        $out_players = $score_log['innings'][$current_inning]['out_runners'];

                        if (!empty($out_players)) {
                            $out_ids = implode("','", $out_players);
                            $query = "SELECT * FROM players 
                                    WHERE team_id = '$defending_team'
                                    AND user_id NOT IN ('$out_ids')";
                        } else {
                            $query = "SELECT * FROM players 
                                    WHERE team_id = '$defending_team'";
                        }

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
    let half_dialogue = document.querySelector('#half_completed');
    let start_dialogue = document.querySelector('#start_second');
    let undo_dialogue = document.querySelector('#undo');
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

    let proceed_end_set = ()=>{
        exit_inn = true;
        get_score();
    }
    
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
                        half_dialogue.close();
                        match_dialogue.classList.add('shake');
                        navigator.vibrate([200,100,200]);
                        // document.querySelectorAll('button:not(.undo, .undo-btn)').forEach(btn => {
                        //     btn.disabled = true;
                        //     btn.style.pointerEvents = 'none';
                        // });

                    }else if(data.field == 'is_tie'){

                        start_dialogue.showModal();
                        half_dialogue.close();
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
        half_dialogue.classList.add('shake');
        navigator.vibrate([200,100,200]);
        half_dialogue.showModal();
    })

    undo_logs.addEventListener('click',()=>{
        console.log('undo..');
        undo_dialogue.classList.add('shake');
        navigator.vibrate([200,100,200]);
        undo_dialogue.showModal();
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
        if(is_empty_runners){
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

    function process_undo(){
        undo = true;
        get_score();
    }

    let cancel_end = () => {
        let cancel = document.querySelector('#start_second');
        cancel.close();
        end_half = false;
    }

    function proceed_end_inning(){
        exit_inn = true;
        get_score();
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

   // Disable right-click
//   document.addEventListener('contextmenu', event => event.preventDefault());

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