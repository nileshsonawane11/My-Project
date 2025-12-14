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

    $current_serve_team = $score_log['current_serve'];
    
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
            background-image: url("https://images.volleyballworld.com/image/upload/t_ratio10_16-size30-f_webp-c_fill/v1746718372/assets/v2/backgrounds/world_rankings_bkg.jpg");
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
            width: 100%;
            height: 60%;
            margin-top: 5%;
        }

        .team-button {
            width: 150px;
            height: 70px;
            background-color: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .team-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            background-color: var(--primary-color);
            color: white;
        }

        .team-button:active {
            transform: translateY(1px);
        }

        .serve {
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

        .serve:hover {
            background-color: var(--primary-dark);
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
            color: var(--text-color);
            font-weight: bold;
            font-size: 1.2rem;
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
            font-size: 1.2rem;
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
        .container3,
        .container4,
        .container5 {
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

        .replace {
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--text-color);
            opacity: 0.7;
            transition: var(--transition);
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
            background-color: var(--background);
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

        .team-logo img,
        .teams-logo img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .undo {
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

        .undo:hover {
            background-color: var(--primary-color);
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
        #match_completed,
        #start_second,
        #half_completed,
        #undo{
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

        .player-replace.selected {
            border: 2px solid var(--primary-color);
            background-color: var(--primary-transparent);
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
                <div class="exit-text">End Set <?php echo $current_set; ?></div>
            </div>
        </div>

        <div class="score-teamlogo">
                    <div class="score2"><?php echo $score_log['sets'][$current_set]['team1_points']; ?></div>
                    <div class="score1"><?php echo $score_log['sets'][$current_set]['team2_points']; ?></div>
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
                                    if( $current_serve_team == $t_id1)
                                    {
                                        echo " 🏐";
                                    }
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
                                    if( $current_serve_team == $t_id2)
                                    {
                                        echo " 🏐";
                                    }
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
        <div class="current-set">Set <?php echo $current_set; ?></div>
        

        <div class="buttons">
            <!-- <div class="point-buttons">
                <div class="team-btn">
                    <label class="team-name"><?php echo $t_name1['t_name']; ?></label>
                    <button class="team-button" data-team="<?php echo $score_log['team1']; ?>">Point</button>
                </div>
                <div class="team-btn">
                    <label class="team-name"><?php echo $t_name2['t_name']; ?></label>
                    <button class="team-button" data-team="<?php echo $score_log['team2']; ?>">Point</button>
                </div>
            </div> -->

            <div class="serve-button">
                <div class="team-btn">
                    <!-- <label class="team-name">For detail Scoring</label> -->
                    <button class="serve">Serve</button>
                    <div >
                        <button class="undo" id='undo-button'>Undo</button>
                    </div>
                </div>
            </div>

            <div class="history">
                <?php
                        $result2 = mysqli_query($conn, "SELECT score_log FROM matches WHERE match_id = '$match_id'");
                        $row2 = mysqli_fetch_assoc($result2);
                        $score_log = json_decode($row2['score_log'], true);

                        $rallies = $score_log['sets'][$current_set]['rallies'];
                        $last_three_rallies = array_reverse(array_slice($rallies, -3));

                        // Print them nicely
                        foreach ($last_three_rallies as $rallies) {

                            $team1_id = $rallies['winner_team'];
                            $team2_id = $rallies['Curr_serve'];

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
                        <div class="serving"><?php echo $team_names[$team2_id]; ?> serving</div>
                        <div class="point-to-update">
                            <div class="point-to">
                                <label class="point-text">Point - <?php
                                    
                                    if (!empty($rallies['type']) && $rallies['type'] == 'Technical') {
                                        echo "1 (Technical)";
                                    }else{
                                        echo '1';
                                    }
                                    ?></label>
                                <label class="to_team-name">to <?php echo $team_names[$team1_id]; ?></label>
                            </div>
                            <div class="last-update"><?php echo $rallies['last score']; ?></div>
                        </div>
                    </div>
                    <?php } ?>
            </div>
            
        </div><div class="blur-container"></div>
    </div>

    <div class="slide-wrapper">
        <div class="slide-container">
            <div class="container3">
                    <div class="current-server">
                        <label class="curr-ser">Serving Player</label>
                        <label class="tap">Tap to choose the raiding player</label>
                    </div>
                    <?php
                        $query = "SELECT * FROM `players` WHERE `team_id` = '$current_serve_team'";
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
                            }else{

                            }
                        ?>
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
                        <div class="extra2">+1 to <?php
                                    if( $current_serve_team == $t_id1)
                                    {
                                        echo $t_name1['t_name'];
                                    }else{
                                        echo $t_name2['t_name'];
                                    }
                                ?></div>
                    </div>
                    <div class="error" data-team="<?php echo $score_log['team2']; ?>">
                        <div class="picture">
                            <img src="https://i.ibb.co/Z6Kf65vb/ERROR.jpg">
                        </div>
                        <div class="text">Error</div>
                        <div class="extra3">+1 to <?php
                                    if( $current_serve_team == $t_id1)
                                    {
                                        echo $t_name2['t_name'];
                                    }else{
                                        echo $t_name1['t_name'];
                                    }
                                    
                                ?></div>
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
    let match_dialogue = document.querySelector('#match_completed');
    let start_dialogue = document.querySelector('#half_completed');
    let complete_btn = document.querySelector('.complete-match-btn');
    let end_set_container = document.querySelector('#start_second');
    let serve_player = null;
    let action = null;
    let winner_team = null;
    let undo = false;
    let end_set = false;
    let is_complete = false;

    
    let get_score = () => {
         let data = {
            'match_id': '<?php echo $match_id; ?>',
            'winner_team': winner_team,
            'serve_player': serve_player,
            'serve_action' : action,
            ...(undo ? { 'undo': true } : {}),
            ...(end_set ? { 'end_set': true } : {}),
            ...(is_complete ? { 'is_complete': true } : {})
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
             if(data.status == 200){
                    if(data.field == 'is_complete'){

                        match_dialogue.showModal();
                        match_dialogue.classList.add('shake');
                        navigator.vibrate([200,100,200]);

                        document.querySelectorAll('button:not(.undo, .undo-btn)').forEach(btn => {
                            btn.disabled = true;
                            btn.style.pointerEvents = 'none';
                            btn.style.opacity = 0.5;
                        });

                    }else if(data.field == 'is_tie'){

                        start_dialogue.close();

                        end_set_container.showModal();
                        end_set_container.classList.add('shake');
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

        serve_player = null;
        action = null;
        winner_team = null;
    }

    let proceed_end_set = () => {
        end_set = true;
        get_score();
    }

    let complete_match = () => {
        is_complete = true;
        get_score();
    }

    let process_undo = () => {
        undo = true;
        get_score();
    }

    document.querySelector('.exit').addEventListener('click', () => {
        start_dialogue.showModal();
        start_dialogue.classList.add('shake');
    });


    // DOM Elements
    const serveBtn = document.querySelector('.serve');
    // Get existing containers
    const container3 = document.querySelector('.container3');
    const container4 = document.querySelector('.container4');
    const container5 = document.querySelector('.container5');
    const slideContainer = document.querySelector('.slide-container');
    const slideWrapper = document.querySelector('.slide-wrapper');
    const undo_btn = document.querySelector('#undo-button');
    
    // Wrap containers in sliding parent
    if (slideContainer && container3 && container4 && container5) {
    slideContainer.appendChild(container3);
    slideContainer.appendChild(container4);
    slideContainer.appendChild(container5);
    } else {
        console.warn("One or more containers not found in the DOM.");
    }
    
    const playerNames = document.querySelectorAll('.player-replace');
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
            console.log(player.innerText);
            goToSlide(1);
            getplayername (player);
        })
    });
    
    let getplayername = (el) => {
        serve_player = el.getAttribute('data-player-id');
    }

    let serveresult = (el) => {
        winner_team = el.getAttribute('data-team');
        get_score();
    }

    let getaction = (el) => {
        action = el.innerText;
    }

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
                dismissSlide();
            } else {
                slideWrapper.style.transform = 'translateY(0)';
            }
        });

        // Click outside to dismiss (for desktop PWA)
        document.addEventListener('click', (e) => {
            // Check if click is outside the slideWrapper and slideWrapper is currently visible
            if (!slideWrapper.contains(e.target) && 
                slideWrapper.style.transform !== 'translateY(600px)' &&
                slideWrapper.style.transform !== '' &&
                slideWrapper.getBoundingClientRect().top < window.innerHeight) {
                dismissSlide();
            }
        });

        // Common dismiss function
        function dismissSlide() {
            slideWrapper.style.transition = 'transform 0.5s ease';
            slideWrapper.style.transform = 'translateY(600px)';
            
            setTimeout(() => {
                if (slideContainer) {
                    slideContainer.style.transform = 'translateX(0)';
                }
            }, 300);
        }

        const clickableSelectors = ['.ace', '.error', '.team1-info', '.team2-info'];

        clickableSelectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(element => {
                element.addEventListener('click', () => {
                    slideWrapper.style.transition = 'transform 0.5s ease';
                    slideWrapper.style.transform = 'translateY(600px)';
                    if (element.matches('.ace, .error')) {
                        getaction(element);
                        console.log('Action immediately:', element.innerText);
                    }
                    // Use directly here
                    serveresult(element);

                    setTimeout(() => {
                        if (slideContainer) {
                            slideContainer.style.transform = 'translateX(0)';
                        }
                    }, 300);
                });
            });
        });


    document.querySelectorAll(".team-button").forEach(team => {
        team.addEventListener("click",() => {
            winner_team = team.getAttribute('data-team');
            get_score();
        })
    })

    undo_btn.addEventListener("click", () => {
        let undo_container = document.getElementById("undo");
        undo_container.showModal();
        undo_container.classList.add('shake');
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