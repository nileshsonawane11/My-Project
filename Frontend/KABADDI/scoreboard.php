<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    include '../../config.php';

    $match_id = $_GET['match_id'] ?? '';// Simulating empty value

    if (empty($match_id)) {
        header("Location: ../../dashboard.php?update=Live&sport=CRICKET"); // Change 'index.php' to your actual file
        exit();
    }else{
        $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `matches` WHERE match_id = '$match_id'"));
        $score_log = json_decode($row['score_log'] ?? '{}', true);
    }

    // Detect current half and last raid
    $current_half = $score_log['current_half'] ?? '';
    $half_data = $score_log['halves'][$current_half] ?? '';
    $last_raid = null;

    if (!empty($half_data['raids'])) {
        $last_raid = end($half_data['raids']);
    }

    if (empty($last_raid)) {
        $last_half_number = array_key_last($score_log['halves'] ?? []);
        $raids_array = $score_log['halves'][$last_half_number]['raids'] ?? [];
        $last_raid = end($raids_array);
    }

    $current_raid = $score_log['current_raid_team'] ?? '';

    $team1_role = null;
    $team2_role = null;
    if($current_raid == ($score_log['team1'] ?? '')){
        $team1_role = 'Raiding';
        $team2_role = 'Defending';

        $raid_team = $score_log['team1'] ?? '';
        $defence_team = $score_log['team2'] ?? '';
    }else{
        $team1_role = 'Defending';
        $team2_role = 'Raiding';

        $raid_team = $score_log['team2'] ?? '';
        $defence_team = $score_log['team1'] ?? '';
    }

    function formatMatchTime($matchDate, $startTime) {
        if (empty($matchDate) || empty($startTime)) {
            return "Not Scheduled";
        }

        $currentDate = date('Y-m-d');
        $yesterday   = date('Y-m-d', strtotime('-1 day'));
        $tomorrow    = date('Y-m-d', strtotime('+1 day'));

        if ($matchDate === $currentDate) {
            return "Today, " . date('h:i A', strtotime($startTime));
        } elseif ($matchDate === $yesterday) {
            return "Yesterday, " . date('h:i A', strtotime($startTime));
        } elseif ($matchDate === $tomorrow) {
            return "Tomorrow, " . date('h:i A', strtotime($startTime));
        } else {
            return date('d-m-Y', strtotime($matchDate)) . ", " . date('h:i A', strtotime($startTime));
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <title>Document</title>
</head>
<style>
        *{
            margin: 0px;
            padding: 0px;
            user-select: none;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body{
            background-color: #f9f9f9;
        }
        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            --card-bg: #ffffff;
            --text-dark: #333333;
            --text-light: #666666;
            --border-color: #e0e0e0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }
        .nav-bar{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            width: 100%;
            background-color: white;
            z-index: 999;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid rgba(248, 57, 0, 0.1);
        }
        .nav-content{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 8px 20px;
        }
        .items,.list{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        html, body {
            height: 100%;
            margin: 0;
        }
        .swiper {
            width: 100%;
            background: #f9f9f9;
        }
        .swiper-slide {
            box-sizing: border-box;
            width: 100%;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: column;
            background: transparent;
            gap: 16px;
        }
        .ad{
            height: 150px;
            width: 100%;
            position: relative;
            background: linear-gradient(135deg, rgba(250,192,31,0.1), rgba(248,57,0,0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 14px;
            border-bottom: 1px solid rgba(248, 57, 0, 0.1);
        }
        .ad2{
            height: 60px;
            width: 100%;
            background: linear-gradient(135deg, rgba(250,192,31,0.1), rgba(248,57,0,0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 14px;
            border-bottom: 1px solid rgba(248, 57, 0, 0.1);
        }
        .ad3{
            height: 300px;
            width: 600px;
            background: linear-gradient(135deg, rgba(250,192,31,0.1), rgba(248,57,0,0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 14px;
            border-bottom: 1px solid rgba(248, 57, 0, 0.1);
        }
        .hide-ad{
            position: absolute;
            top: 15px;
            right: 15px;
            width: 25px;
            background: rgba(248, 57, 0, 0.1);
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .hide-ad:hover {
            background: rgba(248, 57, 0, 0.2);
        }
        .match_score{
            height: max-content;
            width: 100%;
            background: white;
            padding: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
        }
        .menu-line{
            width: 100%;
            height: 48px;
            background: #ffffff;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 60px;
            z-index: 99;
        }
        .menu {
            display: flex;
            position: relative;
            width: 100%;
            max-width: 500px;
        }
        
        .menu-items {
            cursor: pointer;
            height: 48px;
            width: 25%;
            align-items: center;
            justify-content: center;
            display: flex;
            transition: all 0.3s ease;
            font-weight: 500;
            color: var(--text-light);
            font-size: 14px;
        }

        .menu-items.active {
            color: var(--primary-dark);
            font-weight: 600;
        }

        .menu-line-indicator {
            position: absolute;
            bottom: 0;
            height: 3px;
            width: 25%;
            background: var(--primary-dark);
            transition: left 0.3s ease;
            left: 0;
            border-radius: 3px 3px 0 0;
        }

        .menu{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-around;
            width: 100%;
        }
        .game-info {
            height: auto;
            padding: 16px;
            display: flex;
            font-size: 14px;
            gap: 15px;
            width: 100%;
            max-width: 500px;
            background: var(--card-bg);
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            margin: 5px 0;
            transition: transform 0.2s ease;
        }
        
        .match-data {
            display: flex;
            gap: 8px;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }
        .info {
            display: flex;
            justify-content: space-between;
            flex-direction: row;
            align-items: center;
            width: 100%;
            color: var(--text-light);
            font-size: 13px;
        }
        .team-score {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        .team {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
            gap: 10px;
        }
        
        .team img {
            height: 36px;
            width: 36px;
            background: #f5f5f5;
            border-radius: 50%;
            display: flex;
            overflow: hidden;
            object-fit: cover;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(248, 57, 0, 0.1);
        }
        .start-btn {
            width: 80px;
            height: 40px;
            border-radius: 20px;
            border: none;
            color: white;
            font-weight: 600;
            background: var(--background);
            box-shadow: 0 4px 10px rgba(248, 57, 0, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        
        .start-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(248, 57, 0, 0.3);
        }
        
        .start-btn:active {
            transform: translateY(0);
        }
        .table {
            width: 100%;
            border: 1px solid #f0f0f0;
            border-radius: var(--radius-md);
            font-size: 14px;
            margin: 10px 0;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        table .title {
            font-weight: 550;
            background: #fafafa;
            width: 110px;
            color: #666;
        }

        table .data {
            text-align: left;
            color: #333;
            font-weight: 500;
        }
        .innings{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
            overflow: hidden;
            border-radius: var(--radius-md);
            border: 1px solid #f0f0f0;
            box-shadow: var(--shadow-sm);
            background: white;
        }
        .inning-data{
            width: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 15px;
            padding: 15px;
            box-sizing: border-box;
        }
        .inning{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            border: 1px solid #f0f0f0;
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: white;
            box-shadow: var(--shadow-sm);
        }
        .inn-info{
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
            padding: 10px 5px;
            height: -webkit-fill-available;
            justify-content: center;
            position: relative;
            font-size: 16px;
            font-weight: 600;
        }
        .inn-info label,
        .inn-no{
            color: #888;
            font-size: 13px;
            font-weight: 400;
        }
        .stat{
            background: linear-gradient(90deg, rgba(250,192,31,0.1), rgba(248,57,0,0.1));
            width: 100%;
            height: 40px;
            display: flex;
            align-items: center;
            padding: 0px 20px;
            box-sizing: border-box;
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 15px;
        }
        .skew{
            position: absolute;
            height: 100%;
            width: 127%;
            transform: skew(-15deg, 0deg);
            transform-origin: top left;
            background: linear-gradient(90deg, rgba(250,192,31,0.05), rgba(248,57,0,0.05));
            z-index: -1;
        }
        
        .weather{
            width: 100%;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: var(--radius-md);
            overflow: hidden;
            background: white;
            box-shadow: var(--shadow-sm);
        }
        .weather-info{
            height: 100%;
            padding: 10px;
            display: flex;
            justify-items: center;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .weather-info label{
            font-size: 13px;
            text-align: center;
            color: #252525;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
        }
        .weather-data{
            display: flex;
            flex-direction: row;
            align-items: center;
            height: 80px;
            padding: 5px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            color: white;
            justify-content: space-evenly;
        }
        .weather-title {
            background: #fafafa;
            height: 40px;
            display: flex;
            align-items: center;
            padding-left: 15px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 15px;
        }
        .weather-icon{
            height: 30px;
            width: 30px;
        }
        .comm{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: var(--radius-md);
            overflow: hidden;
            background: white;
            box-shadow: var(--shadow-sm);
            border: 1px solid #f0f0f0;
        }
        .comm-name{
            height: 48px;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            padding: 0 20px;
            font-size: 16px;
            background: #fafafa;
            font-weight: 600;
            color: var(--text-dark);
        }
        .comm-data{
            height: max-content;
            box-sizing: border-box;
            padding: 10px;
            position: relative;
        }
        .ball-data{
            width: 100%;
            height: max-content;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: flex-start;
            margin: 15px 0px;
            gap: 10px;
        }
        .ball-runs{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 5px;
        }
        .commentry{
            padding: 5px;
            font-size: 14px;
            color: var(--text-dark);
            line-height: 1.5;
        }
        .run{
            height: 30px;
            width: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--primary-dark);
            border-radius: 15%;
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
            padding: 10px;
        }
        .ball {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 20px;
        }
        .line{
            border: 1px solid #f0f0f0;
            margin: 10px 0;
            width: 100%;
        }
        section{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 15px;
        }
        .bat-data{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid #f0f0f0;
            background: white;
            box-shadow: var(--shadow-sm);
        }
        .team-container{
            width: 100%;
            height: 40px;
            display: flex;
            background: #fafafa;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            font-weight: 600;
            color: var(--text-dark);
        }
        .team-name{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
        }
        .team-data{
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0px;
            box-sizing: border-box;
        }
        .dt{
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border-bottom: 1px solid #f0f0f0;
        }
        .dt:last-child {
            border-bottom: none;
        }
        .dt.not-out{
            background: rgba(46, 125, 50, 0.05);
        }
        .dt1{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            justify-content: space-between;
            padding: 10px 15px;
        }
        .dt2{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
            padding: 0px 15px 10px 15px;
            box-sizing: border-box;
            font-size: 13px;
            color: #666;
        }
        .not-out .dt2{
            color: #2E7D32;
        }
        .inning-batsman{
            width: 50%;
            text-align: left;
        }
        .match-not-start{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 15px;
            font-size: 16px;
            color: #666;
            padding: 40px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            text-align: center;
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
        }
        .error-text{
            width: 230px;
            font-size: 14px;
            color: #888;
        }
        .bowl-data{
            display: flex;
            width: 100%;
            gap: 0px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid #f0f0f0;
            background: white;
            box-shadow: var(--shadow-sm);
        }
        .table-head{
            background: #fafafa;
            font-weight: 600;
            color: var(--text-dark);
        }
        .playername{
            color: var(--primary-dark);
            font-weight: 500;
        }
        .fall-of-wickets{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            padding: 0px;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid #f0f0f0;
            background: white;
            box-shadow: var(--shadow-sm);
        }
        .fall-of-wickets .dt{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
        }
        .score-and-over{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        .fall-of-wickets .inning-batsman {
            width: 50%;
        }
        .teams-container{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-sizing: border-box;
            border: 1px solid #f0f0f0;
            overflow: hidden;
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
        }
        .row{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 1px solid #f0f0f0;
        }
        .teamimg{
            height: 28px;
            width: 28px;
            background: #f5f5f5;
            border-radius: 50%;
            border: 1px solid rgba(248, 57, 0, 0.1);
            overflow: hidden;
        }
        .teamimg img{
            height: 100%;
            width: 100%;
            object-fit:cover;
        }
        .t1, .t2{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .vs {
            color: var(--text-light);
            font-size: 14px;
        }
        .teams{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px;
            gap: 15px;
        }
        .teams .playername{
            text-wrap: auto;
            width: 110px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 14px;
        }
        .teams .playername .role{
            color: #888;
            font-size: 12px;
            font-weight: 400;
        }
        .squad-border{
            border: 1px solid #f0f0f0;
            height: 100%;
            align-self: stretch;
        }
        .playerimg{
            height: 40px;
            width: 40px;
            background: #f5f5f5;
            border-radius: 50%;
            border: 1px solid rgba(248, 57, 0, 0.1);
            flex-shrink: 0;
            overflow: hidden;
        }
        .playerimg img{
            height: 100%;
            width: 100%;
            object-fit:cover;
        }
        .team2 .squad-player{
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            width: 100%;
        }
        .team1 .squad-player{
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-direction: row-reverse;
            gap: 10px;
            width: 100%;
        }
        .team2{
            display: flex;
            flex-direction: column;
            gap: 15px;
            justify-content: center;
            align-items: flex-end;
            text-align: right;
            width: 50%;
        }
        .team1{
            display: flex;
            flex-direction: column;
            gap: 15px;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
            width: 50%;
        }
        .error-img2 {
            height: 74px;
            width: 74px;
            background-image: url("https://i.ibb.co/mVjZYxK2/Pngtree-hourglass-waiting-for-mouse-pointer-5453296.png");
            background-size: cover;
        }
        .popup-container{
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            top: 0;
            left: 0;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        #team-feedback{
            height: max-content;
            width: 350px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            border: none;
            background: white;
            border-radius: var(--radius-lg);
            flex-direction: column;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }
        .exit img{
            height: 15px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }
        .exit img:hover {
            opacity: 1;
        }
        .fed-head{
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            width: 100%;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        .fed-body{
            height: 100%;
            width: 100%;
            display: flex;
            gap: 15px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
            background: #fafafa;
        }
        .feedback-container{
            height: 100px;
            min-width: 300px;
            max-width: 300px;
            resize: none;
            border-radius: var(--radius-sm);
            border: 1px solid #f0f0f0;
            background: white;
            padding: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }
        .feedback-container:focus {
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 2px rgba(248, 57, 0, 0.1);
        }
        .fed-btns{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-direction: row;
            gap: 10px;
        }
        .fed-btns button{
            width: 50%;
            background: transparent;
            border: none;
            color: #666;
            font-weight: 500;
            font-size: 14px;
            border-radius: 20px;
            line-height: 16px;
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .fed-btns .cancel:hover {
            background: #f0f0f0;
        }
        .fed-btns .submit{
            background: var(--primary-dark);
            color: white;
        }
        .fed-btns .submit:hover {
            background: #e03400;
        }
        .feedback-cta-container{
            display: inline-flex;
            align-items: center;
            width: 100%;
            justify-content: center;
            margin-top: 10px;
        }
        .feedback-cta-holder{
            padding: 8px 12px;
            border: 1px solid hsl(148, 57%, 50%);
            border-radius: 20px;
            background: rgba(46, 125, 50, 0.05);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .feedback-cta-holder:hover {
            background: rgba(46, 125, 50, 0.1);
        }
        .feedback-cta-button{
            font-weight: 600;
            font-size: 14px;
            line-height: 18px;
            color: #2E7D32;
            border: none;
            background: none;
            cursor: pointer;
        }
        .cmc-report-issue-button-wrapper{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 15px 0;
        }
        #cmc-report-issue-button{
            padding: 8px 16px;
            border: 1px solid #c83737;
            border-radius: 20px;
            background: rgba(200, 55, 55, 0.05);
            font-weight: 600;
            font-size: 14px;
            line-height: 18px;
            color: #c73737;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        #cmc-report-issue-button:hover {
            background: rgba(200, 55, 55, 0.1);
        }

        .bowl-data table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        .bowl-data th,
        .bowl-data td {
            border: none !important;
            text-align: center;
            padding: 10px 12px;
            font-size: 14px;
            color: #333;
        }

        .bowl-data thead th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .bowl-data tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .bowl-data,
        .bowl-data * {
            box-sizing: border-box;
        }
          .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: 60%;
            height: 100%;
            background: #ffffff;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
            scroll-behavior: smooth;
            overflow-y: scroll;
            scrollbar-width: none;
            transform: translateX(100%);
            transition: all 0.3s ease;
            z-index: 99999;
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        #close-sidebar {
            text-align: right;
            font-size: 32px;
            font-weight: 300;
            margin: 15px 25px;
            cursor: pointer;
            color: var(--text-light);
        }
        
        .menu-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 0 5px;
        }
        #opacity-container {
            position: absolute;
            top: 0;
            left: 0;
            display: none;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            transition: all 0.3s ease-in-out;
        }
        .menu-item {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            font-size: 18px;
            gap: 15px;
            margin: 15px 0;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .menu-item:last-child:hover{
            background : none;
        }
        .menu-item:hover {
            background: rgba(250, 192, 31, 0.1);
        }
        .menu-item div {
            max-width: fit-content;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            text-align : right;
        }
        .login-btn {
            width: 130px;
            height: 40px;
            border-radius: 18px;
            border: none;
            color: white;
            font-weight: 600;
            background: var(--background);
            box-shadow: 0 4px 10px rgba(248, 57, 0, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
        }
         .update {
            color: #e74c3c;
            width: 100%;
            word-spacing: 2px;
            letter-spacing: 1px;
        }
        .date-time{
            font-weight: 700;
            max-width: 100px;
            font-size: 15px;
        }
        #toggle-button {
            padding: 8px 16px;
            background-color: #dddddd57;
            backdrop-filter: blur(5px);
            color: #000000;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            margin-top: 10px;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        #toggle-button:hover {
            background-color: #dddddd57;
            backdrop-filter: blur(5px);
        }
        a{
            text-decoration: none;
            color: black;
        }
        .trade-mark{
            position: absolute;
            top: 3px;
            right: 23px;
            font-size: 7px;
        }
        .l-name{
            position: relative;
        }
        #startMatchDialog {
            z-index: 9999;
            position: fixed;
            transform: translateX(-50%) translateY(-50%);
            top: 50%;
            left: 50%;
            width: 90%;
            max-width: 500px;
            border: none;
            height: max-content;
            background: white;
            flex-direction: column;
            transition: all 0.3s ease;
            justify-content: center;
            align-items: flex-start;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        #startMatchDialog::backdrop {
            position: fixed;
            inset: 0px;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(3px);
        }
        
        #content-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 25px
        }
        
        #matchPasswordForm {
            display: flex;
            justify-content: center;
            flex-direction: column;
            gap: 25px;
        }
        
        .form-data {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }
        
        .form-data label {
            font-size: 15px;
            line-height: 35px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .error {
            display: none;
            color: #e74c3c;
            width: 100%;
            font-size: 13px;
            margin: 5px 0;
        }
        
        .btns {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
            width: 100%;
        }
        
        #matchPassword {
            padding: 12px 15px;
            height: 45px;
            outline: none;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #f8f8f8;
            color: var(--text-dark);
            font-size: 15px;
        }
        
        #matchPassword:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(250, 192, 31, 0.2);
        }

        #title{
            font-size: 18px;
            font-weight: 500;
        }
        
        .btns>* {
            width: 110px;
            height: 40px;
            border-radius: 25px;
            border: solid 1px var(--primary-light);
            color: var(--primary-light);
            background: transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }
        
        .btns>*:last-child {
            background: var(--background);
            color: white;
            border: none;
        }
        
        .btns>*:last-child:hover {
            background: linear-gradient(135deg, #fac01fdb, #f83900cc);
        }
        
        .btns>*:first-child:hover {
            background: rgba(250, 192, 31, 0.1);
        }
        .date-time {
            font-weight: 700;
            max-width: 100px;
            font-size: 15px;
        }
        .strt-btn{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
            text-align: center;
        }
        @media(max-width: 600px) {
            .nav-content{
                display: flex;
                justify-content:space-between ;
                align-items: center;
                width: 100%;
            }
            .logo-img img{
                height: 35px;
                padding: 4px 0;
            }
            .logo-name {
                font-size: 22px;
                color: black;
                text-align: left;
                width: 130px;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0rem;
                text-align: left;
            }
            .txt-strike{
                font-weight: 600;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .txt-live {
                font-weight: 600;
            }
            .swiper-slide{
                width: 100%;
            }
            .stat{
                height: 40px;
            }
            .weather-data {
                flex-wrap: wrap;
                height: auto;
                padding: 15px 5px;
                gap: 15px;
            }
            .weather-info {
                min-width: 80px;
            }
            .ad3{
                height: 150px;
                width: 300px;
            }
        }

        @media(min-width: 601px) {
            .logo-img img{
                height: 38px;
                padding: 4px 0;
            }
            .txt-strike{
                font-weight: 600;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .txt-live {
                font-weight: 600;
            }
            .logo-name {
                font-size: 24px;
                color: black;
                text-align: left;
                width: 140px;
                font-weight: 400;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0;
            }
            .swiper-slide{
                width: 100%;
            }
            .weather-data{
                height: 90px;
            }
            .weather-title{
                height: 40px;
            }
            .stat{
                height: 40px;
            }
            .comm-data{
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
        }
        .txt-live {
            font-weight: 400;
        }
</style>
<body>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        window.swiper = new Swiper(".swiper", {
            speed: 300,
            slidesPerView: 1,
            on: {
                slideChange: () => {
                    menuItems.forEach(i => i.classList.remove('active'));
                    menuItems[swiper.activeIndex].classList.add('active');
                    moveIndicator(swiper.activeIndex);
                }
            }
        });
    });
    </script>

<dialog id="startMatchDialog">
            <div id="content-wrapper">
                <div class="top-container">
                    <p id="title">Enter Password to Start Match</p>
                </div>

                <div class="body-container">
                <form id="matchPasswordForm">
                    <div class="form-data">
                        <label for="matchPassword">Password:</label>
                        <input type="text" name="" id="match_id" hidden>
                        <input type="text" id="matchPassword" name="matchPassword" required>
                        <div id="error-matchpassword" class="error"></div>
                    </div>

                    <div class='btns'>
                        <button id='submit-btn' type="button" onclick="closeDialog()">Cancel</button>
                        <button id='cancel-btn' type="submit">Start Match</button>
                    </div>
                </form>
                </div>
            </div>
        </dialog>

    <div id="opacity-container" onclick="hide()"></div>

        <div class="sidebar">
            <div id="close-sidebar">&times;</div>
            <div class="menu-list">
               
                <div class="menu-item"><div onclick="shareContent()"><p>Share</p><svg width="24" height="19" viewBox="0 0 24 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.25 9L14.5 0.25V5.25C5.75 6.5 2 12.75 0.75 19C3.875 14.625 8.25 12.625 14.5 12.625V17.75L23.25 9Z" fill="black"/>
                    </svg></div>
                </div>

                <div class="menu-item"><div onclick="window.location.href=`../../privacy-policy.php`"><p>Privacy Policy</p><svg width="22" height="28" viewBox="0 0 22 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 14V6.18625C21.0001 6.08758 20.9807 5.98987 20.9429 5.8987C20.9052 5.80754 20.8498 5.72473 20.78 5.655L16.845 1.72C16.7045 1.57931 16.5138 1.50018 16.315 1.5H1.75C1.55109 1.5 1.36032 1.57902 1.21967 1.71967C1.07902 1.86032 1 2.05109 1 2.25V25.75C1 25.9489 1.07902 26.1397 1.21967 26.2803C1.36032 26.421 1.55109 26.5 1.75 26.5H12.25M6 11.5H16M6 6.5H11M6 16.5H9.75" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg></div>
                    </div>

                    <div class="menu-item"><div><p>Follow Us</p><svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.375 1.75C20.5462 1.75 19.7513 2.07924 19.1653 2.66529C18.5792 3.25134 18.25 4.0462 18.25 4.875C18.25 5.32125 18.3475 5.745 18.5175 6.13125L16.0738 8.88C15.2428 8.30862 14.2585 8.00186 13.25 8C12.325 8 11.47 8.27 10.7263 8.7075L7.88375 5.86625L7.855 5.895C8.1 5.3975 8.25 4.84375 8.25 4.25C8.25 3.50832 8.03007 2.7833 7.61801 2.16661C7.20596 1.54993 6.62029 1.06928 5.93506 0.785453C5.24984 0.501625 4.49584 0.427362 3.76841 0.572057C3.04098 0.716751 2.3728 1.0739 1.84835 1.59835C1.3239 2.1228 0.966751 2.79098 0.822057 3.51841C0.677362 4.24584 0.751625 4.99984 1.03545 5.68506C1.31928 6.37029 1.79993 6.95596 2.41661 7.36801C3.0333 7.78007 3.75832 8 4.5 8C5.09375 8 5.64625 7.85 6.145 7.605L6.11625 7.63375L8.95875 10.475C8.49978 11.2376 8.25494 12.1099 8.25 13C8.25 14.2463 8.725 15.3738 9.48125 16.2513L6.26 19.4713C5.89873 19.3275 5.5138 19.2525 5.125 19.25C3.4025 19.25 2 20.6513 2 22.375C2 24.0988 3.4025 25.5 5.125 25.5C6.8475 25.5 8.25 24.0988 8.25 22.375C8.25 21.9738 8.1675 21.5925 8.02875 21.2388L11.5763 17.6913C12.1025 17.8788 12.66 18 13.25 18C16.0075 18 18.25 15.7575 18.25 13C18.25 12.205 18.0463 11.4638 17.715 10.795L20.3612 7.81625C20.6812 7.92625 21.0188 8 21.375 8C23.0988 8 24.5 6.59875 24.5 4.875C24.5 3.15125 23.0988 1.75 21.375 1.75ZM13.25 15.5C11.8713 15.5 10.75 14.3788 10.75 13C10.75 11.6213 11.8713 10.5 13.25 10.5C14.6288 10.5 15.75 11.6213 15.75 13C15.75 14.3788 14.6288 15.5 13.25 15.5Z" fill="black"/>
                        </svg></div>
                    </div>

                    <div class="menu-item"><div onclick="window.location.href=`../../feedback.php`"><p>Feedback</p><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 16.75C13.3542 16.75 13.6513 16.63 13.8913 16.39C14.1313 16.15 14.2508 15.8533 14.25 15.5C14.2492 15.1467 14.1292 14.85 13.89 14.61C13.6508 14.37 13.3542 14.25 13 14.25C12.6458 14.25 12.3492 14.37 12.11 14.61C11.8708 14.85 11.7508 15.1467 11.75 15.5C11.7492 15.8533 11.8692 16.1504 12.11 16.3913C12.3508 16.6321 12.6475 16.7517 13 16.75ZM11.75 11.75H14.25V4.25H11.75V11.75ZM0.5 25.5V3C0.5 2.3125 0.745 1.72417 1.235 1.235C1.725 0.745833 2.31333 0.500833 3 0.5H23C23.6875 0.5 24.2763 0.745 24.7663 1.235C25.2563 1.725 25.5008 2.31333 25.5 3V18C25.5 18.6875 25.2554 19.2763 24.7663 19.7663C24.2771 20.2563 23.6883 20.5008 23 20.5H5.5L0.5 25.5ZM4.4375 18H23V3H3V19.4062L4.4375 18Z" fill="black"/>
                        </svg></div>
                    </div>

                    <div class="menu-item"><div onclick="window.location.href=`../../about-us.php`"><p>About Us</p><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13 0.5C6.09647 0.5 0.5 6.09641 0.5 13C0.5 19.9035 6.09647 25.5 13 25.5C19.9036 25.5 25.5 19.9036 25.5 13C25.5 6.09641 19.9036 0.5 13 0.5ZM13 23C7.48602 23 3.00002 18.514 3.00002 13C3.00002 7.48596 7.48596 3.00002 13 3.00002C18.5141 3.00002 23 7.48596 23 13C23 18.514 18.5141 23 13 23ZM14.5653 8C14.5653 8.90633 13.9054 9.56252 13.0127 9.56252C12.0839 9.56252 11.4402 8.90627 11.4402 7.98266C11.4402 7.09496 12.1013 6.43754 13.0127 6.43754C13.9054 6.43754 14.5653 7.09496 14.5653 8ZM11.7528 11.75H14.2527V19.25H11.7528V11.75Z" fill="black"/>
                        </svg></div>
                    </div>

                    <div class="menu-item"><div onclick="window.location.href=`../../support.php`"><p>Support</p><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.375C20.4375 16.375 18.9375 16.125 17.5375 15.6625C17.4125 15.625 17.275 15.6 17.15 15.6C16.825 15.6 16.5125 15.725 16.2625 15.9625L13.5125 18.7125C9.96717 16.9093 7.08361 14.0301 5.275 10.4875L8.025 7.725C8.375 7.3875 8.475 6.9 8.3375 6.4625C7.86415 5.02237 7.62363 3.51593 7.625 2C7.625 1.3125 7.0625 0.75 6.375 0.75H2C1.3125 0.75 0.75 1.3125 0.75 2C0.75 13.7375 10.2625 23.25 22 23.25C22.6875 23.25 23.25 22.6875 23.25 22V17.625C23.25 16.9375 22.6875 16.375 22 16.375ZM3.2875 3.25H5.1625C5.25 4.35 5.4375 5.4375 5.7375 6.4875L4.2375 8C3.725 6.4875 3.4 4.9 3.2875 3.25ZM20.75 20.7125C19.1 20.6 17.5 20.275 16 19.7625L17.5 18.2625C18.5625 18.5625 19.65 18.75 20.75 18.825V20.7125ZM12 0.75V13.25L15.75 9.5H23.25V0.75H12ZM20.75 7H14.5V3.25H20.75V7Z" fill="black"/>
                        </svg></div>
                    </div>
                    <?php
                        if(!isset($_SESSION['user'])){
                    ?>
                    <div class="menu-item">
                        <button class="login-btn" onclick="window.location.href=`../../front-page.php`">Sign In</button>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>

    <div class="popup-container">
        <div id="team-feedback" open>
            <div class="fed-head"><span class="logo"><div class="items">
                <div class="logo-img"><img src="../../assets/images/logo.png" alt=""></div>
                <div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
            </div></span><span class="exit"><img src="https://staticg.sportskeeda.com/skm/assets/close.png" alt=""></span></div>
            <form class="fed-body">
                <textarea name="" id="" class="feedback-container"></textarea>
                <div class="fed-btns">
                    <button type="button" class="cancel" onclick="document.querySelector('.popup-container').style.display = 'none'">Cancel</button>
                    <button class="submit" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <nav class="nav-bar">
        <div class="nav-content">
            <a href="javascript:history.back()">
                <div class="items">
                    <div class="logo-img"><img src="../../assets/images/logo.png" alt=""></div>
                    <div class="l-name"><div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
                    <sup class="trade-mark">TM</sup></div>
                </div>
            </a>
            <div class="items">
                <div id='commentaryIcon'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mic-on">
                    <path d="M12 1a4 4 0 0 1 4 4v6a4 4 0 0 1-8 0V5a4 4 0 0 1 4-4z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="23"/>
                    <line x1="8" y1="23" x2="16" y2="23"/>
                    </svg>
                </div>
                <a href="" class="menu-bar"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAGZJREFUSEvtlrENACAMw8pnnMZpfAYTC1W3CDOEA2JhUpUW0GkQNwx+Zt6qj+ohdp7yKtVLDE6c78DiC+c4t/o46WLX8877rlzYOGGqxU/scYryB4KVCwNja9GtlhvwWpQrrQIx1Rt3TwofeC3yFwAAAABJRU5ErkJggg=="/></a>
            </div>
        </div>
    </nav>    

    <div class="ad">
        <div class="hide-ad">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"><path d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z" fill="black" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        </div>
        Advertisement (412px x 150px)
    </div>

    <?php
        $sql2 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_1']}'";
        $query = mysqli_query($conn, $sql2) or die("Error: ");
        $team1 = mysqli_fetch_assoc($query);

        $sql3 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_2']}'";
        $query3 = mysqli_query($conn, $sql3) or die("Error: ");
        $team2 = mysqli_fetch_assoc($query3);
    ?>
    <div class="match_score">
        <div class="game-info" data-match_id="<?php echo $row['match_id']?>">
            <div class="match-data">
                <div class="info">
                    <p><?php echo (!empty($row['match_name']) ? $row['match_name'] : "Match 1 | No Tournament") ?></p>
                </div>

                <div class="info team-score">
                    <div class="team">
                        <?php
                            if (!empty($team1['t_logo'])) {
                                echo "<img src='../../assets/images/teams/{$team1['t_logo']}' alt='{$team1['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }

                            $astr = (!empty($current_serve_team) &&  $current_serve_team == $team1['t_id'] && !empty($score_log['winner'])) ? '*' : '';

                            echo $team1['t_name'].' '.$astr;
                        ?>
                    </div>
                    <div class="score"><?php echo $row['score_team_1'] ?></div>
                </div>

                <div class="info team-score">
                    <div class="team">
                        <?php
                            if (!empty($team2['t_logo'])) {
                                echo "<img src='./assets/images/teams/{$team2['t_logo']}' alt='{$team2['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }

                            $astr = (!empty($current_serve_team) &&  $current_serve_team == $team2['t_id'] && !empty($score_log['winner'])) ? '*' : '';

                            echo $team2['t_name'].' '.$astr;
                        ?>
                    </div>
                    <div class="score"><?php echo $row['score_team_2'] ?></div>
                </div>

                <?php 
                    if(empty($row['toss_winner'])){
                        echo "<div class='info'><p>" . formatMatchTime($row['match_date'], $row['start_time']) . "</p></div>";
                    }else if($row['status'] == 'Live'){
                        

                        $team = '';
                        if($row['toss_winner'] == $team1['t_id']){
                            $team = $team1['t_name'];
                        }else{
                            $team = $team2['t_name'];
                        }

                        echo "<div class='info update'><p>" . $team . " Elected To ". $row['toss_decision'] ."</p></div>";
                    }else if($row['status'] == 'Completed'){
                        $winner = $score_log['winner'];
                        $winner_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `teams` WHERE t_id = '$winner'"))['t_name'];
                        // If match is not completed and no winner is declared
                        if (!empty($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])){
                            echo "<div class='info update'><p>Match Tied (".$winner_name." Won The Match)</p></div>";
                        }else{
                            echo "<div class='info update'><p>".$winner_name." Won The Match</p></div>";
                        }
                        
                    }
                ?>
                
                <!-- OR if toss declared -->
                <!--
                <div class="info update">
                    <p>Team 1 Name Elected To Bat</p>
                </div>
                -->
            </div>

            <div class="strt-btn">
                
                    <?php
                        $scorers = [];

                        if (!empty($row['scorers'])) {
                            $decoded = json_decode($row['scorers'], true); // decode as array
                            if (is_array($decoded)) {
                                $scorers = $decoded;
                            }
                        }

                        $scorer_emails = isset($scorers[0]) ? explode(",", $scorers[0]) : [];
                        $session_email = $_SESSION['email'] ?? '';

                        if ($scorer_emails && in_array($session_email, $scorer_emails) && $row['status'] == 'Live') {
                            echo "<div class='info strt-btn'>";
                            if(empty($score_log)){
                                echo "<span class='date-time'>".formatMatchTime($row['match_date'], $row['start_time'])."</span>";
                            }
                            echo "<button class='start-btn' onclick='openDialog(this, event)'>Start</button></div>";
                        }
                    ?>
            </div>
        </div>
    </div>

    <div class="menu-line">
        <div class="menu">
            <div class="menu-items active">Info</div>
            <!-- <div class="menu-items">Fantasy</div> -->
            <div class="menu-items">Live</div>
            <div class="menu-items">Scoreboard</div>
            <div class="menu-items">Squad</div>
            <div class="menu-line-indicator"></div>
        </div>
    </div>

    <div class="ad2">
        Advertisement (412px x 60px)
    </div>

    <div class="swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="title">Date & Time</td>
                            <td class="data"><?php echo formatMatchTime($row['match_date'], $row['start_time']); ?></td>
                        </tr>
                        <tr>
                            <td class="title">Venue</td>
                            <td class="data"><?php echo $row['venue']; ?></td>
                        </tr>
                        <tr>
                            <td class="title">Match</td>
                            <td class="data"><?php echo $team1['t_name'].' VS '.$team2['t_name']; ?></td>
                        </tr>
                        <?php
                           // Safely decode each role's JSON string into an array
                            $umpires = [];
                            $scorers = [];
                            $commentators = [];

                            if (!empty($row['umpires'])) {
                                $decoded = json_decode($row['umpires'], true);
                                if (is_array($decoded)) {
                                    $umpires = $decoded;
                                }
                            }

                            if (!empty($row['scorers'])) {
                                $decoded = json_decode($row['scorers'], true);
                                if (is_array($decoded)) {
                                    $scorers = $decoded;
                                }
                            }

                            if (!empty($row['commentators'])) {
                                $decoded = json_decode($row['commentators'], true);
                                if (is_array($decoded)) {
                                    $commentators = $decoded;
                                }
                            }

                            // Combine all for querying
                            $all_emails = array_merge($umpires, $scorers, $commentators);

                            // Prepare and execute query if emails are present
                            if (!empty($all_emails)) {
                                $placeholders = implode(',', array_fill(0, count($all_emails), '?'));
                                $sql = "SELECT * FROM users WHERE email IN ($placeholders)";
                                
                                $stmt = $conn->prepare($sql);
                                $types = str_repeat("s", count($all_emails));
                                $stmt->bind_param($types, ...$all_emails);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                // Build a map of email => user data
                                $user_map = [];
                                while ($user = $result->fetch_assoc()) {
                                    $user_map[$user['email']] = $user;
                                }

                                // Group by role
                                $umpire_users = [];
                                foreach ($umpires as $email) {
                                    if (isset($user_map[$email])) {
                                        $umpire_users[] = $user_map[$email];
                                    }
                                }

                                $scorer_users = [];
                                foreach ($scorers as $email) {
                                    if (isset($user_map[$email])) {
                                        $scorer_users[] = $user_map[$email];
                                    }
                                }

                                $commentator_users = [];
                                foreach ($commentators as $email) {
                                    if (isset($user_map[$email])) {
                                        $commentator_users[] = $user_map[$email];
                                    }
                                }
                            }


                        ?>
                        <tr>
                            <td class="title">Umpires</td>
                            <td class="data">
                                <?php foreach ($umpire_users as $u): ?>
                                    <?= htmlspecialchars($u['fname']) ?> <?= htmlspecialchars($u['lname']) ?><br>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="title">Scorers</td>
                            <td class="data">
                                <?php foreach ($scorer_users as $s): ?>
                                    <?= htmlspecialchars($s['fname']) ?> <?= htmlspecialchars($s['lname']) ?><br>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="title">Referee</td>
                            <td class="data">
                                <?php foreach ($commentator_users as $c): ?>
                                    <?= htmlspecialchars($c['fname']) ?> <?= htmlspecialchars($c['lname']) ?><br>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="title">Toss</td>
                            <td class="data">
                                <?php
                                    if(empty($row['toss_winner'])){
                                        echo "<div class='info'><p>Match has not started yet</p></div>";
                                    }else{

                                        $team = '';
                                        if($row['toss_winner'] == $team1['t_id']){
                                            $team = $team1['t_name'];
                                        }else{
                                            $team = $team2['t_name'];
                                        }

                                        echo "<div class='info update'><p>" . $team . " Elected To ". $row['toss_decision'] ."</p></div>";
                                    }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                 <?php
                    $apiKey = "76604801ccb3576d81ddd1bca09b978a";
                    $location = $row['venue'];
                    $geo_url = "http://api.openweathermap.org/geo/1.0/direct?q=".urlencode($location)."&limit=1&appid={$apiKey}";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $geo_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $geo_response = curl_exec($ch);
                    curl_close($ch);

                    $geo_data = json_decode($geo_response, true);

                    if (!empty($geo_data)) {
                        $lat = $geo_data[0]['lat'];
                        $lon = $geo_data[0]['lon'];

                        // Now fetch weather using lat & lon
                        $weather_url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $weather_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $weather_response = curl_exec($ch);
                        curl_close($ch);

                        // echo $weather_response;

                        $weather_data = json_decode($weather_response, true);

                        // Convert timestamp to human-readable time
                        $updatedTime = date("d M H:i", $weather_data['dt']);

                        // Extract values
                        $location = $weather_data['name'] . ', ' . $weather_data['sys']['country'];
                        $temp = $weather_data['main']['temp'] . '°C';
                        $condition = $weather_data['weather'][0]['main'];
                        $humidity = $weather_data['main']['humidity'] . '%';
                        $wind = $weather_data['wind']['speed'] . ' kmph';
                        $iconCode = $weather_data['weather'][0]['icon'];
                        $iconURL = "https://openweathermap.org/img/wn/{$iconCode}@2x.png";


                        if ($weather_data['cod'] == 200) {
                    
                        $HTML = <<<HTML
                            <div class="weather">
                                <div class="weather-info weather-title">Weather Update</div>
                                <div class="weather-data">
                                    <div class="weather-info weather-dt">$location<label>Updated: $updatedTime</label></div>
                                    <div class="weather-info weather-dt">$temp<label><img src='{$iconURL}' alt='$condition' class='weather-icon'>$condition</label></div>
                                    <div class="weather-info weather-dt">$humidity<label><img src='{$iconURL}' alt='$condition' class='weather-icon'>$condition</label></div>
                                    <div class="weather-info weather-dt">$wind<label>Wind</label></div>
                                </div>
                            </div>
                            HTML;

                            echo $HTML;
                        } else {
                            echo "Weather data unavailable. " . $weather_data['message'];
                        }
                    } else {
                        echo "Could not find location.";
                    }

                ?>

                <div class="ad3">
                    Advertisement (600px x 300px)
                </div>
            </div>

            <div class="swiper-slide">

                <?php
                    $HTML = <<<HTML
                            <div class="match-not-start">
                                <div class="error-img"><img src="https://staticg.sportskeeda.com/cricket_images/cmc-pre-live-icon.svg" alt=""></div>
                                <span class="error-text">This match has not started yet, stay tuned for live updates!</span>
                            </div>
                            HTML;
                        
                    if($row['status'] == 'Upcoming' || empty($row['toss_winner'])){
                        echo $HTML;
                    }
                    
                ?>
                <div class="comm">
                    <?php
                        $all_raids = [];

                        // 1. Merge balls from 1st and 2nd innings
                        $total_halves = $score_log['total_halves'] ?? '';
                        for ($inning_key = 1; $inning_key <= $total_halves; $inning_key++) {
                            if (
                                isset($score_log['halves'][$inning_key]['raids']) &&
                                is_array($score_log['halves'][$inning_key]['raids'])
                            ) {
                                foreach ($score_log['halves'][$inning_key]['raids'] as $raid) {
                                    $raid['current Half'] = $inning_key; // Add the inning key to each raid
                                    $all_raids[] = $raid;
                                }
                            }
                        }
                        $team1_id = $score_log['team1'] ?? '';
                        $team2_id = $score_log['team2'] ?? '';

                        // Get Team 1 Player Name
                                $team1_name = '';
                                if (!empty($team1)) {
                                    $team1_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id = '$team1_id' LIMIT 1"));
                                    $team1_name = $team1_row['t_name'] ?? "";
                                }

                                // Get Team 2 Player Name
                                $team2_name = '';
                                if (!empty($team2)) {
                                    $team2_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id = '$team2_id' LIMIT 1"));
                                    $team2_name = $team2_row['t_name'] ?? "";
                                }
                        // $all_raidsd = array_reverse($all_raids);
                    ?>
                    <div class="comm-name">Commentary</div>
                    <div class="comm-data">
                        <hr class="line">
                        <?php foreach ($all_raids as $raids): ?>
                            <?php
                                $def_points = $raids['def points'] ?? '';
                                $last_score = $raids['last score'] ?? '';
                                $raid_points = $raids['raid points'] ?? '';
                                $team1_points = $raids['team1_points'] ?? '';
                                $team2_points = $raids['team2_points'] ?? '';
                                $curr_raid_team = $raids['current_raid_team'] ?? '';
                                $raider = $raids['raider'] ?? '';
                                $current_Half = $raids['current Half'] ?? '';
                                $comment = '';

                                // Get Curr Serve team name
                                $curr_raid_team_name = '';
                                if (!empty($curr_raid_team)) {
                                    $curr_raid_team_id = trim($curr_raid_team);
                                    $curr_raid_team_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id = '$curr_raid_team_id' LIMIT 1"));
                                    $curr_raid_team_name = $curr_raid_team_row['t_name'] ?? "";
                                }

                                // Get Serve Player Name
                                $raider_player_name = '';
                                if (!empty($raider)) {
                                    $raider_player_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$raider' LIMIT 1"));
                                    $raider_player_name = $raider_player_row['player_name'] ?? "";
                                }

                                // Get defence Team Name
                                $defence_team_name = '';
                                if (!empty($defence_team)) {
                                    $defence_team_id = $defence_team;
                                    $defence_team_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id = '$defence_team_id' LIMIT 1"));
                                    $defence_team_name = $defence_team_row['t_name'] ?? "";
                                }

                                // Get defence Team Name
                                $raid_team_name = '';
                                if (!empty($raid_team)) {
                                    $raid_team_id = $raid_team;
                                    $raid_team_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id = '$raid_team_id' LIMIT 1"));
                                    $raid_team_name = $raid_team_row['t_name'] ?? "";
                                }

                               // Commentary generation (Raider Optional, Always Result-Based, Never Empty)
                                if (!empty($curr_raid_team_name)) {
                                    if (!empty($raider_player_name)) {
                                        if ($raid_points > 0 && $def_points == 0) {
                                            $comment = "$raider_player_name from $curr_raid_team_name scores $raid_points point(s) in a successful raid.";
                                        } elseif ($def_points > 0 && $raid_points == 0) {
                                            $comment = "$raider_player_name from $curr_raid_team_name is tackled! Defence gets $def_points point(s).";
                                        } elseif ($raid_points > 0 && $def_points > 0) {
                                            $comment = "$raider_player_name from $curr_raid_team_name earns $raid_points point(s), but defence also scores $def_points point(s).";
                                        } else {
                                            $comment = "$raider_player_name from $curr_raid_team_name returns empty-handed. No points scored.";
                                        }
                                    } else {
                                        if ($raid_points > 0 && $def_points == 0) {
                                            $comment = "$curr_raid_team_name scores $raid_points point(s) from a successful raid.";
                                        } elseif ($def_points > 0 && $raid_points == 0) {
                                            $comment = "$curr_raid_team_name's raid is unsuccessful. Defence scores $def_points point(s).";
                                        } elseif ($raid_points > 0 && $def_points > 0) {
                                            $comment = "$curr_raid_team_name's raid results in $raid_points point(s), but defence gains $def_points point(s) too.";
                                        } else {
                                            $comment = "$curr_raid_team_name attempts a raid but no points are scored.";
                                        }
                                    }
                                } else {
                                    // Fallback if no team info
                                    if ($raid_points > 0 && $def_points == 0) {
                                        $comment = "Raid successful. $raid_points point(s) awarded.";
                                    } elseif ($def_points > 0 && $raid_points == 0) {
                                        $comment = "Raid failed. Defence scores $def_points point(s).";
                                    } elseif ($raid_points > 0 && $def_points > 0) {
                                        $comment = "Both teams score! Raid: $raid_points point(s), Defence: $def_points point(s).";
                                    } else {
                                        $comment = "A raid attempt took place. No points recorded.";
                                    }
                                }

                                // Add current score if available
                                if ($team1_points !== '' && $team2_points !== '') {
                                    $comment .= " <i>Current Score: $team1_points - $team2_points</i>";
                                }
                            ?>
                        <div class="ball-data">
                            <div class="ball-runs">
                                <div class="ball"><?php echo $current_Half; ?></div>
                                <div class="run"><?php echo "$team1_points - $team2_points"; ?></div>
                            </div>
                            <div class="commentry">
                                <?php echo $comment; ?>
                            </div>
                        </div>

                        <hr class="line">
                        <?php endforeach; ?>
                        <!-- Optional "Show More" Button -->
                        <button id="toggle-button" style="margin-top: 10px; justify-content: center;align-items: center;">Show More</button>
                    </div>
                </div>

                <div class="ad3">
                    Advertisement (600px x 300px)
                </div>
                
                <div class="feedback-cta-container">
                    <div class="feedback-cta-holder">
                        <button class="feedback-cta-button" data-feedback-page="cmc-feedback"           data-feedback-sheet="" data-feedback-section="Playing-XI"       data-feedback-tab="">Any feedback on our Squad section?
                        </button>
                    </div>
                </div>

                <div class="cmc-report-issue-button-wrapper">
                    <button id="cmc-report-issue-button" class="cmc-report-issue-button">Report an Issue</button>
                </div>
            </div>

            <div class="swiper-slide">
                <?php
                    $HTML = <<<HTML
                        <div class="match-not-start">
                            <div class="error-img2"></div>
                            <span class="error-text">Match has not started yet</span>
                        </div>
                        HTML;
                        
                    if($row['status'] == 'Upcoming' || empty($row['toss_winner'])){
                        echo $HTML;
                    }
                    
                ?>
                <section id="team1">
                    <div class="bowl-data">
                        <?php
                            // Assume $score_log is already available as given

                            $team1_name = $team1['t_name'] ?? 'Team1';
                            $team2_name = $team2['t_name'] ?? 'Team2';

                            $total_halves = isset($score_log['total_halves']) ? (int)$score_log['total_halves'] : 2;
                            $halves = $score_log['halves'] ?? [];

                            // Calculate full result totals
                            $team1_total = 0;
                            $team2_total = 0;
                            foreach ($halves as $half_data) {
                                $team1_total += $half_data['team1_points'] ?? 0;
                                $team2_total += $half_data['team2_points'] ?? 0;
                            }

                            // Determine Win/Loss/Draw
                            if ($team1_total > $team2_total) {
                                $team1_result = '-';
                                $team2_result = '-';
                            } elseif ($team2_total > $team1_total) {
                                $team1_result = '-';
                                $team2_result = '-';
                            } else {
                                $team1_result = $team2_result = 'Draw';
                            }

                            // Start table
                            echo '<table class="table-score">';
                            echo '<thead>';
                            echo '<tr class="table-head">';
                            echo '<th>Teams</th>';
                            echo '<th>FR</th>';

                            // Reverse order half headers (2nd, 1st)
                            for ($i = $total_halves; $i >= 1; $i--) {
                                $suffix = ($i == 1) ? '1<sup>st</sup>' : ($i == 2 ? '2<sup>nd</sup>' : "{$i}<sup>th</sup>");
                                echo "<th>$suffix</th>";
                            }
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            // Team 1 row
                            echo '<tr>';
                            echo "<td>$team1_name</td>";
                            echo "<td>$team1_result</td>";
                            for ($i = $total_halves; $i >= 1; $i--) {
                                $score = $halves[$i]['team1_points'] ?? 0;
                                echo "<td>$score</td>";
                            }
                            echo '</tr>';

                            // Team 2 row
                            echo '<tr>';
                            echo "<td>$team2_name</td>";
                            echo "<td>$team2_result</td>";
                            for ($i = $total_halves; $i >= 1; $i--) {
                                $score = $halves[$i]['team2_points'] ?? 0;
                                echo "<td>$score</td>";
                            }
                            echo '</tr>';

                            echo '</tbody>';
                            echo '</table>';
                        ?>
                    </div>

                </section>

                <div class="ad3">
                    Advertisement (600px x 300px)
                </div>
                
                <div class="feedback-cta-container">
                    <div class="feedback-cta-holder">
                        <button class="feedback-cta-button" data-feedback-page="cmc-feedback"           data-feedback-sheet="" data-feedback-section="Playing-XI"       data-feedback-tab="">Any feedback on our Squad section?
                        </button>
                    </div>
                </div>

                <div class="cmc-report-issue-button-wrapper">
                    <button id="cmc-report-issue-button" class="cmc-report-issue-button">Report an Issue</button>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="teams-container">
                    <div class="row">
                        <div class="t1">
                            <div class="teamimg">
                                <?php
                                    if (!empty($team1['t_logo'])) {
                                        echo "<img src='../../assets/images/teams/{$team1['t_logo']}' alt='{$team1['t_name']}' onerror=\"this.style.opacity='0'\">";
                                    }else{
                                        echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                                    }
                                ?>
                            </div>
                            <?php
                                echo $team1['t_name'];
                            ?>
                        </div>
                        <span class="vs">vs</span>
                        <div class="t2">
                            <div class="teamimg">
                                 <?php
                                    if (!empty($team2['t_logo'])) {
                                        echo "<img src='../../assets/images/teams/{$team2['t_logo']}' alt='{$team1['t_name']}' onerror=\"this.style.opacity='0'\">";
                                    }else{
                                        echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                                    }
                                ?>
                            </div>
                            <?php
                                echo $team2['t_name'];
                            ?>
                        </div>
                    </div>

                    <div class="teams">
                        <?php
                            $teams = [$team1, $team2];
                            foreach ($teams as $index => $team) {
                                $team_id = $team['t_id'];
                                $result = mysqli_query($conn, "SELECT * FROM `players` WHERE `team_id` = '$team_id'");
                                $teamClass = $index === 0 ? 'team1' : 'team2';
                                echo "<div class='$teamClass'>";
                                if (mysqli_num_rows($result)) {
                                    $count = 0;
                                    while ($row2 = mysqli_fetch_assoc($result)) {
                                        $user_id = $row2['user_id'];

                                        // First check users table
                                        $user_query = mysqli_query($conn, "SELECT * FROM `users` WHERE `user_id` = '$user_id'");
                                        $user_data = mysqli_fetch_assoc($user_query);

                                        if (!$user_data) {
                                            // Fallback to players table
                                            $player_query = mysqli_query($conn, "SELECT * FROM `players` WHERE `user_id` = '$user_id'");
                                            $player_data = mysqli_fetch_assoc($player_query);

                                            if ($player_data) {
                                                $row = [
                                                    'fname' => $player_data['player_name'] ? explode(' ', $player_data['player_name'])[0] : 'Player',
                                                    'lname' => $player_data['player_name'] ? (explode(' ', $player_data['player_name'])[1] ?? '') : '',
                                                    'user_photo' => $player_data['photo']
                                                ];
                                            } else {
                                                // If no record at all
                                                $row = [
                                                    'fname' => 'Unknown',
                                                    'lname' => 'Player',
                                                    'user_photo' => null
                                                ];
                                            }
                                        } else {
                                            // Use users table data
                                            $row = [
                                                'fname' => $user_data['fname'],
                                                'lname' => $user_data['lname'],
                                                'user_photo' => $user_data['user_photo']
                                            ];
                                        }

                                        // Photo fallback
                                        $src = !empty($row['user_photo']) ? "../../assets/images/users/{$row['user_photo']}" : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";

                                        // Output player block
                                        echo "
                                            <div class='squad-player'>
                                                <div class='playername'>{$row['fname']} {$row['lname']}<span class='role'>all</span></div>
                                                <div class='playerimg'><img src='$src' alt='{$row['fname']} {$row['lname']}' onerror=\"this.style.opacity='0'\"></div>
                                            </div>
                                        ";
                                    }
                                }
                                echo "</div>"; // Close team div
                                if ($index === 0) echo "<div class='squad-border'></div>"; // Border between two teams
                            }
                        ?>
                    </div>
                </div>

                <div class="ad3">
                    Advertisement (600px x 300px)
                </div>

                <div class="feedback-cta-container">
                    <div class="feedback-cta-holder">
                        <button class="feedback-cta-button" data-feedback-page="cmc-feedback"           data-feedback-sheet="" data-feedback-section="Playing-XI"       data-feedback-tab="">Any feedback on our Squad section?
                        </button>
                    </div>
                </div>

                <div class="cmc-report-issue-button-wrapper">
                    <button id="cmc-report-issue-button" class="cmc-report-issue-button">Report an Issue</button>
                </div>
            </div>

        </div>
    </div>

    <script>
    const menuItems = document.querySelectorAll('.menu-items');
    const indicator = document.querySelector('.menu-line-indicator');
    const close_fed_container = document.querySelector('.exit');
    const feedback = document.querySelectorAll('.feedback-cta-holder');
    const feedback_page = document.querySelector('.cmc-report-issue-button');
    const close_ad = document.querySelector('.hide-ad');
    const ad_container = document.querySelector('.ad');
    const matchId = <?php echo json_encode($match_id); ?>
    
    //Menu Bar
        const menu_bar = document.querySelector('.menu-bar');
        const side = document.querySelector('.sidebar');
        const closeside = document.querySelector('#close-sidebar');
        const opacity = document.querySelector('#opacity-container');

        menu_bar.addEventListener('click', function (e) {
            e.preventDefault();
            side.classList.add('active');
            opacity.style.display = 'block';
        });

        closeside.addEventListener('click',function(){
            side.classList.remove('active');
            opacity.style.display = 'none';
        });

        //hide sidebar 
        let hide = ()=>{
            side.classList.remove('active');
            opacity.style.display = 'none';
        }

        close_ad.addEventListener('click', () => {
            ad_container.style.display = 'none';
        });

    const swiper = new Swiper(".swiper", {
        speed: 300,
        slidesPerView: 1,
        on: {
        slideChange: () => {
            // Update active tab based on swiper index
            menuItems.forEach(i => i.classList.remove('active'));
            menuItems[swiper.activeIndex].classList.add('active');
            moveIndicator(swiper.activeIndex);
        }
        }
    });

    menuItems.forEach((item, index) => {
        item.addEventListener('click', () => {
        swiper.slideTo(index);
        });
    });

    function moveIndicator(index) {
        const targetItem = menuItems[index];
        indicator.style.left = targetItem.offsetLeft + 'px';
    }

    // Set initial position
    window.addEventListener('load', () => {
        moveIndicator(0);
    });

    close_fed_container.addEventListener('click', () => {
        document.querySelector('.popup-container').style.display = 'none';
    });

    feedback.forEach(selector => {
        selector.addEventListener('click', () => {
            document.querySelector('.popup-container').style.display = 'flex';
        });
    })

    feedback.forEach(selector => {
        selector.addEventListener('click', () => {
            document.querySelector('.popup-container').style.display = 'flex';
        });
    });

    // Open dialog for password
        function openDialog(button, event) {
            if (event) event.stopPropagation();
            const dialog = document.getElementById("startMatchDialog");
            dialog.showModal();

            const match_to_start = button.closest('.game-info').getAttribute('data-match_id');
            console.log("Match : " + match_to_start);

            document.getElementById("match_id").value = match_to_start;
        }

        // Close dialog of password
        function closeDialog() {
            const dialog = document.getElementById("startMatchDialog");
            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });
            document.getElementById("matchPasswordForm").reset();
            dialog.close();
        }

        
        // Variefy match password
        document.getElementById("matchPasswordForm").addEventListener("submit", function(e) {
            e.preventDefault();
            password = document.getElementById("matchPassword").value;
            match_id = document.getElementById("match_id").value;

            let formdata = new FormData();
            formdata.append('password', password.trim());
            formdata.append('match_id', match_id);

            fetch('../../Backend/ckeck-match-password.php',{
                method : 'POST',
                body : formdata
            })
            .then(response => response.json())
            .then((data)=>{
                console.log(data);
                document.querySelectorAll('[id^="error-"]').forEach((el) => {
                    el.innerHTML = '';
                    el.style.display = 'none';
                });
                if(data.status != 200){
                    let err = document.getElementById(`error-${data.field}`);
                    err.innerHTML = data.message;
                    err.style.display = 'block';
                }else{
                    window.location.href = `./match_toss.php?match_id=${match_id}`;
                    document.getElementById("matchPasswordForm").reset();
                    closeDialog();
                }
            })
            .catch();
        });

        function stopCommentary() {
                window.speechSynthesis.cancel();
            }

    let commentaryEnabled = true;

        //allow and deny voice commentry
        document.getElementById('commentaryIcon').addEventListener('click', function() {
            commentaryEnabled = commentaryEnabled ? false : true;

            if (commentaryEnabled) {
                console.log("Commentary enabled",commentaryEnabled);
                //  change icon color to active
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mic-on"><path d="M12 1a4 4 0 0 1 4 4v6a4 4 0 0 1-8 0V5a4 4 0 0 1 4-4z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>';
            } else {
                console.log("Commentary disabled",commentaryEnabled);
                stopCommentary();
                //  change icon color to muted
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mic-on"><path d="M12 1a4 4 0 0 1 4 4v6a4 4 0 0 1-8 0V5a4 4 0 0 1 4-4z"/><line x1="4" y1="4" x2="20" y2="20"stroke="#d6d6d65b"stroke-width="6"stroke-linecap="round" /><line x1="4" y1="4" x2="20" y2="20"stroke="red"stroke-width="2"stroke-linecap="round" /><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>';
            }
        });

    //speech
    function speakText(text) {
        const speech = new SpeechSynthesisUtterance(text);
        speech.lang = "en-US"; // Language (e.g., 'hi-IN' for Hindi)
        speech.pitch = 1;
        speech.rate = 1;
        speech.volume = 1;
        window.speechSynthesis.speak(speech);
    }

    let lastDataString = '';
    let showAllBalls = false;
    //AJAX
    function fetchScoreboard() {
        fetch(`../../API/VOLLEYBALL_api.php?match_id=${matchId}`)
        .then(response => response.json())
        .then(data => {
            // Update scoreboard elements
            // console.log(data);
            if (typeof data === 'string') {
                data = JSON.Parse(data);
            }
            // console.log(data);
            const currentDataString = JSON.stringify(data);
            if (currentDataString !== lastDataString) {
                lastDataString = currentDataString;

                update_commentry(data);
                update_scoreboard(data);
            }
        })
        .catch(error => {
            console.error('Error fetching scoreboard:', error);
        });
    }

    // Fetch every 5 seconds
setInterval(fetchScoreboard, 1500);

function update_commentry(data) {
    let allRaids = [];
    const container = document.querySelector('.comm-data');
    let toggleButton = document.getElementById('toggle-button');

    if (container) {
        container.innerHTML = '';
    }

    if (!data || Object.keys(data).length === 0) {
        container.innerHTML = '<p>No commentary available yet</p>';
        toggleButton.style.display = 'none';
        return;
    }

    const totalHalves = parseInt(data.total_halves) || 0;

    for (let halfKey = 1; halfKey <= totalHalves; halfKey++) {
        const half = data.halves?.[halfKey];
        if (half?.raids && Array.isArray(half.raids)) {
            half.raids.forEach(raid => {
                allRaids.push({ ...raid, half: halfKey });
            });
        }
    }

    const teamMap = data.team_map || {};
    const playerMap = data.player_map || {};

    allRaids = allRaids.reverse();

    const fragment = document.createDocumentFragment();

    allRaids.forEach((raid, index) => {
        const defPoints = raid['def points'] ?? '';
        const raidPoints = raid['raid points'] ?? '';
        const team1Points = raid['team1_points'] ?? '';
        const team2Points = raid['team2_points'] ?? '';
        const currRaidTeam = raid['current_raid_team'] ?? '';
        const raider = raid['raider'] ?? '';
        const half = raid['current Half'] ?? '';

        const currRaidTeamName = teamMap[currRaidTeam] || '';
        const raiderPlayerName = playerMap[raider] || '';

        let comment = '';

        if (currRaidTeamName) {
            if (raiderPlayerName) {
                if (raidPoints > 0 && defPoints == 0) {
                    comment = `${raiderPlayerName} from ${currRaidTeamName} scores ${raidPoints} point(s) in a successful raid.`;
                } else if (defPoints > 0 && raidPoints == 0) {
                    comment = `${raiderPlayerName} from ${currRaidTeamName} is tackled! Defence gets ${defPoints} point(s).`;
                } else if (raidPoints > 0 && defPoints > 0) {
                    comment = `${raiderPlayerName} from ${currRaidTeamName} earns ${raidPoints} point(s), but defence also scores ${defPoints} point(s).`;
                } else {
                    comment = `${raiderPlayerName} from ${currRaidTeamName} returns empty-handed. No points scored.`;
                }
            } else {
                if (raidPoints > 0 && defPoints == 0) {
                    comment = `${currRaidTeamName} scores ${raidPoints} point from a successful raid.`;
                } else if (defPoints > 0 && raidPoints == 0) {
                    comment = `${currRaidTeamName}'s raid is unsuccessful. Defence scores ${defPoints} point.`;
                } else if (raidPoints > 0 && defPoints > 0) {
                    comment = `${currRaidTeamName}'s raid results in ${raidPoints} point, but defence gains ${defPoints} point too.`;
                } else {
                    comment = `${currRaidTeamName} attempts a raid but no points are scored.`;
                }
            }
        } else {
            if (raidPoints > 0 && defPoints == 0) {
                comment = `Raid successful. ${raidPoints} point awarded.`;
            } else if (defPoints > 0 && raidPoints == 0) {
                comment = `Raid failed. Defence scores ${defPoints} point.`;
            } else if (raidPoints > 0 && defPoints > 0) {
                comment = `Both teams score! Raid: ${raidPoints} point, Defence: ${defPoints} point.`;
            } else {
                comment = `A raid attempt took place. No points recorded.`;
            }
        }

        if (team1Points !== '' && team2Points !== '') {
            comment += ` <i>Current Score: ${team1Points} - ${team2Points}</i>`;
        }

        if (index === 0 && typeof commentaryEnabled !== 'undefined' && commentaryEnabled && !data.winner) {
            let cleanText = comment.replace(/<[^>]*>/g, '').replace(/(\d+)\s*-\s*(\d+)/g, '$1 to $2').replace(/_/g, ' ').replace(/\s+/g, ' ').trim();
            speakText(cleanText);
        }

        const ballDiv = document.createElement('div');
        ballDiv.className = 'ball-data';
        ballDiv.style.display = (index >= 7 && !showAllBalls) ? 'none' : 'flex';
        ballDiv.innerHTML = `
            <div class="ball-runs">
                <div class="ball">${half}</div>
                <div class="run">${team1Points} - ${team2Points}</div>
            </div>
            <div class="commentry">
                ${comment}
            </div>`;

        fragment.appendChild(ballDiv);

        const line = document.createElement('hr');
        line.className = 'line';
        line.style.display = (index >= 7 && !showAllBalls) ? 'none' : 'block';
        fragment.appendChild(line);
    });

    container.appendChild(fragment);
    container.appendChild(toggleButton);
    initShowMoreButton();
}

function update_scoreboard(data) {
    const container = document.querySelector('.bowl-data');
    if (!data || !container) return;

    const team1_id = data.team1;
    const team2_id = data.team2;
    const team1_name = data.team_map?.[team1_id] || "Team 1";
    const team2_name = data.team_map?.[team2_id] || "Team 2";
    let winner_team = '';
    if(data.match_completed){
        winner_team = data.winner;
    }
     

    const total_halves = parseInt(data.total_halves) || 0;
    const halves = data.halves || {};

    if (Object.keys(halves).length === 0) return;

    let html = `<table class="table-score">
                    <thead>
                        <tr class="table-head">
                            <th>Teams</th>
                            <th>F</th>`;

    for (let i = total_halves; i >= 1; i--) {
        html += `<th>${i}</th>`;
    }

    html += `</tr>
            </thead>
            <tbody>`;

    // Team 1 row
    html += `<tr>
                <td>${team1_name}</td>
                <td>${winner_team == data.team1 ? 'Win' : 'Lose'}</td>`;
    for (let i = total_halves; i >= 1; i--) {
        let score = halves[i]?.team1_points ?? '';
        html += `<td>${score}</td>`;
    }
    html += `</tr>`;

    // Team 2 row
    html += `<tr>
                <td>${team2_name}</td>
                <td>${winner_team == data.team2 ? 'Win' : 'Lose'}</td>`;
    for (let i = total_halves; i >= 1; i--) {
        let score = halves[i]?.team2_points ?? '';
        html += `<td>${score}</td>`;
    }
    html += `</tr>
            </tbody>
        </table>`;

    container.innerHTML = html;

    // Update match live score and current serve info
    const matchInfoContainer = document.querySelector('.match-data');
    if (matchInfoContainer && data.current_half && data.halves[data.current_half]) {
        const current_half = data.halves[data.current_half];
        const current_raid_team = data.current_raid_team;

        const scores = matchInfoContainer.querySelectorAll('.score');
        if (scores.length >= 2) {
            scores[0].innerHTML = `${data.team1_score} ( ${current_half.team1_points} )`;
            scores[1].innerHTML = `${data.team2_score} ( ${current_half.team2_points} )`;
        }

        const teams = matchInfoContainer.querySelectorAll('.team');
        const team_key = current_raid_team === team1_id ? 0 : 1;
        const other_team_key = team_key === 0 ? 1 : 0;

        const team_name_key = team_key === 0 ? team1_name : team2_name;
        const other_team_name_key = other_team_key === 0 ? team1_name : team2_name;

        // Add * to the serving team
        let teamDiv = teams[team_key];
        if (teamDiv && !data.winner) {
            for (let node of teamDiv.childNodes) {
                if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '') {
                    node.nodeValue = `${team_name_key} *`;
                    break;
                }
            }
        }

        // Remove * from the other team
        let otherTeamDiv = teams[other_team_key];
        if (otherTeamDiv && !data.winner) {
            for (let node of otherTeamDiv.childNodes) {
                if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '') {
                    node.nodeValue = `${other_team_name_key}`;
                    break;
                }
            }
        }
    }
}

function initShowMoreButton() {
        const container = document.querySelector('.comm-data');
        let toggleButton = document.getElementById('toggle-button');
        const ballElements = container.querySelectorAll('.ball-data');
        
        
        if (ballElements.length <= 7) {
            toggleButton.style.display = 'none';
            return;
        }
        
        toggleButton.style.display = 'block';
        toggleButton.textContent = showAllBalls ? 'Show Less' : 'Show More';
        
        toggleButton.onclick = function() {
            showAllBalls = !showAllBalls;
            
            const allElements = container.children;
            for (let i = 10; i < allElements.length; i++) {
                // Skip the toggle button
                if (allElements[i].id === 'toggle-button') continue;

                allElements[i].style.display = showAllBalls ? 'flex' : 'none';
            }
            
            toggleButton.textContent = showAllBalls ? 'Show Less' : 'Show More';
        };
    }

        function shareContent() {
            if (navigator.share) {
                navigator.share({
                    title: 'LiveStrike',
                    text: 'Check out this awesome real-time score tracking!',
                    url: window.location.href
                })
                .then(() => console.log('Successfully shared'))
                .catch((error) => console.error('Error sharing:', error));
            } else {
                alert('Sharing not supported on this browser.');
            }
        }
    </script>
</body>
</html>