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

    //detect current inning
    $current_innings = null;
    $lastBall = null;

    // 1. First check super over innings if they exist
if (isset($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])) {
    foreach ($score_log['super_over_innings'] as $innings_name => $innings_data) {
        if (is_array($innings_data) && 
            isset($innings_data['completed']) && 
            $innings_data['completed'] == false) {
            
            $current_innings = $innings_name;
            $is_super_over = true;
            $bat_team = $innings_data['batting_team'] ?? null;
            $bowl_team = $innings_data['bowling_team'] ?? null;
            
            if (!empty($innings_data['balls']) && is_array($innings_data['balls'])) {
                $lastBall = end($innings_data['balls']);
                reset($innings_data['balls']);
            }
            break;
        }
    }
}

// 2. If no active super over innings, check regular innings
if ($current_innings === null && isset($score_log['innings']) && is_array($score_log['innings'])) {
    foreach ($score_log['innings'] as $innings_name => $innings_data) {
        if (is_array($innings_data) && 
            isset($innings_data['completed']) && 
            $innings_data['completed'] == false) {
            
            $current_innings = $innings_name;
            $bat_team = $innings_data['batting_team'] ?? null;
            $bowl_team = $innings_data['bowling_team'] ?? null;
            
            if (!empty($innings_data['balls']) && is_array($innings_data['balls'])) {
                $lastBall = end($innings_data['balls']);
                reset($innings_data['balls']);
            }
            break;
        }
    }
}

// 3. If no active innings found but match isn't completed, use last inning
if ($current_innings === null && (!isset($score_log['match_completed']) || $score_log['match_completed'] != true)) {
    // Try super over innings first
    if (isset($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])) {
        $last_super_over = end($score_log['super_over_innings']);
        if (is_array($last_super_over)) {
            $current_innings = key($score_log['super_over_innings']);
            $is_super_over = true;
            $bat_team = $last_super_over['batting_team'] ?? null;
            $bowl_team = $last_super_over['bowling_team'] ?? null;
            reset($score_log['super_over_innings']);
        }
    }
    
    // If no super over, use last regular inning
    if ($current_innings === null && isset($score_log['innings']) && is_array($score_log['innings'])) {
        $last_regular_inning = end($score_log['innings']);
        if (is_array($last_regular_inning)) {
            $current_innings = key($score_log['innings']);
            $bat_team = $last_regular_inning['batting_team'] ?? null;
            $bowl_team = $last_regular_inning['bowling_team'] ?? null;
            reset($score_log['innings']);
        }
    }
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

function getWicketBallDetails($balls, $player_id) {
    foreach ($balls as $ball) {
        if (
            isset($ball['Ball Type']) && $ball['Ball Type'] === 'Wicket' &&
            (isset($ball['Out Player']) && $ball['Out Player'] === $player_id)
        ) {
            $wicket_over = $ball['overs_completed'] ?? '-';
            $wicket_runs = ($ball['Wickets'] ?? '-') . '-' . ($ball['TotalScore'] ?? '-');
            return [$wicket_over, $wicket_runs];
        }
    }
    return [null, null]; // if not found
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
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
            transition: opacity 0.5s ease;
        }
        body.fade-out {
            opacity: 0;
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
            background: rgb(20 20 20 / 12%);
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
            background: rgb(20 20 20 / 12%);
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
            background: rgb(20 20 20 / 12%);
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
            background: rgb(153 153 153);
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
            flex-direction: column;
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
            top: 55px;
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
            border-radius: var(--radius-md);
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
            flex-wrap: wrap;
            gap: 20px;
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
        table {
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
            color: #b0bfff;
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
            background: linear-gradient(135deg, #5594c6, #0054a1);
            color: white;
            justify-content: space-evenly;
        }
        .weather-icon{
            height: 30px;
            width: 30px;
        }
        .weather-title {
            background: aliceblue;
            height: 40px;
            display: flex;
            align-items: center;
            padding-left: 15px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 15px;
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
        }
        .ball-data{
            width: 100%;
            height: max-content;
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
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
            height: 25px;
            width: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--primary-dark);
            border-radius: 50%;
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
        }
        .ball {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
        }
        .line{
            border: 1px solid #f0f0f0;
            margin: 10px 0;
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
            background: linear-gradient(90deg, rgba(250, 192, 31, 0.1), rgba(248, 57, 0, 0.1));
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
            flex-wrap: wrap;
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
        #fall-of-wickets{
            width: 100%;
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
            width: 20%;
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
        .t2{
            flex-direction: row-reverse;
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
        }
        .teams .playername{
            text-wrap: auto;
            width: 80%;
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
        }

        #toggle-button:hover {
            background-color: #dddddd57;
            backdrop-filter: blur(5px);
        }
        .out-by{
            width: 140px;
        }
        .batsmanStats{
            width: 100%;
        }
        #bowler-stats{
            width: 100%;
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
        .curr_players{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            flex-direction: column;
            gap: 20px;
        }
        .striker-non_striker,
        .curr-bowler{
            width: 100%;
            display: flex;
            gap: 10px;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }
        #striker{
            height: 18px;
        }
        .total_runs{
            font-weight: 700;
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
            .ball-data{
                width: 500px;
            }
            .comm-data{
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .line{
                width: 100%;
            }
            .sidebar{
                width: 30%;
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
                    <button class="submit" type="button">Submit</button>
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

                            $astr = (!empty($bat_team) &&  $bat_team == $team1['t_id']) ? '*' : '';

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

                            $astr = (!empty($bat_team) &&  $bat_team == $team2['t_id']) ? '*' : '';

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
                <div class="info">
                    <p id='run_rate'>CRR : 0.0</p>
                </div>
                <!-- OR if toss declared -->
                <!--
                <div class="info update">
                    <p>Team 1 Name Elected To Bat</p>
                </div>
                -->
            </div>

            <div class="strt-btn">
                <div class="info">
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
                            echo "<div class='info'>";
                            if(empty($score_log)){
                                echo "<span class='date-time'>".formatMatchTime($row['match_date'], $row['start_time'])."</span>";
                            }
                            echo "<button class='start-btn' onclick='openDialog(this, event)'>Start</button></div>";
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="curr_players">
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
                <table>
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
                <div class="innings">
                    <div class="stat">VENUE STATS</div>
                    <div class="inning-data">
                        <?php
                            foreach (['1st', '2nd'] as $inning_key) {
                            $inning = $score_log['innings'][$inning_key] ?? [];

                            if (!empty($inning['completed']) && $inning['completed'] == true && !empty($inning['batmans'])) {
                                $batmans = $inning['batmans'];
                                $total_runs = 0;
                                $wickets = 0;
                                $total_sr = 0;
                                $sr_count = 0;

                                foreach ($batmans as $batsman) {
                                    $total_runs += $batsman['runs'];

                                    if ($batsman['out_status'] != 'not out') {
                                        $wickets++;
                                    }

                                    if ($batsman['balls_faced'] > 0) {
                                        $total_sr += ($batsman['runs'] / $batsman['balls_faced']) * 100;
                                        $sr_count++;
                                    }
                                }

                                $batsman_count = count($batmans);
                                $avg_runs = $batsman_count > 0 ? round($total_runs / $batsman_count, 2) : '-';
                                $avg_sr = $sr_count > 0 ? round($total_sr / $sr_count, 2) : '-';
                            } else {
                                $avg_runs = '-';
                                $wickets = '-';
                                $avg_sr = '-';
                            }

                            echo '
                            <div class="inning">
                                <div class="inn-no inn-info"><div class="skew"></div> ' . ucfirst($inning_key) . ' Inning</div>
                                <div class="runs inn-info"><span class="avg-runs" >' . $avg_runs . '</span> <label for="">Avg Runs</label></div>
                                <div class="wicket inn-info"><span class="avg-wickets" >' . $wickets . '</span> <label for="">Avg Wickets</label></div>
                                <div class="SR inn-info"><span class="avg-sr" >' . $avg_sr . '</span> <label for="">Avg SR</label></div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="ad3">
                    Advertisement (600px x 300px)
                </div>

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
                    }else{
                    
                ?>

                <div class="comm">
                        <?php
                            $all_balls = [];

                           // 1. Merge balls from 1st and 2nd innings
                            foreach (['1st', '2nd'] as $inning_key) {
                                if (
                                    isset($score_log['innings'][$inning_key]['balls']) &&
                                    is_array($score_log['innings'][$inning_key]['balls'])
                                ) {
                                    $all_balls = array_merge($all_balls, $score_log['innings'][$inning_key]['balls']);
                                }
                            }

                            // 2. Merge balls from super over innings if any
                            if (!empty($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])) {
                                foreach (['1st', '2nd'] as $super_inning) {
                                    if (
                                        isset($score_log['super_over_innings'][$super_inning]['balls']) &&
                                        is_array($score_log['super_over_innings'][$super_inning]['balls'])
                                    ) {
                                        $all_balls = array_merge($all_balls, $score_log['super_over_innings'][$super_inning]['balls']);
                                    }
                                }
                            }

                            // 3. Extract unique striker IDs
                            $striker_ids = [];
                            foreach ($all_balls as $ball) {
                                if (!empty($ball['Striker'])) {
                                    $striker_ids[] = $ball['Striker'];
                                }
                            }
                            $striker_ids = array_unique($striker_ids);

                            // 4. Fetch player names from database
                            $player_map = [];
                            if (!empty($striker_ids)) {
                                $placeholders = implode(',', array_fill(0, count($striker_ids), '?'));
                                $sql = "SELECT * FROM players WHERE user_id IN ($placeholders)";
                                $stmt = $conn->prepare($sql);

                                $types = str_repeat("s", count($striker_ids)); // use 'i' if IDs are integers
                                $stmt->bind_param($types, ...$striker_ids);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($row2 = $result->fetch_assoc()) {
                                    $player_map[$row2['user_id']] = $row2['player_name'];
                                }
                            }

                            // 5. Optional: show latest first
                            $all_balls = ($all_balls);
                        ?>

                        <div class="comm-name">Commentary</div>
                        <div class="comm-data">
                            <hr class="line">

                            <?php foreach ($all_balls as $ball): ?>
                                <?php
                                    $run = (int)($ball['Run'] ?? 0);
                                    $type = strtolower($ball['Ball Type'] ?? '');
                                    $wicket = $ball['Wicket Type'] ?? null;
                                    $by = $ball['Wicket By'] ?? null;
                                    $shot = $ball['Shot Type'] ?? '';
                                    $side = $ball['Shot Side'] ?? '';
                                    $player_id = $ball['Striker'] ?? null;
                                    $striker = $player_map[$player_id] ?? 'Batter';
                                    $bowler_id = $ball['Bowler'] ?? '';
                                    $bowler_name = "";
                                    $fielder_name = "";
                                    $comment = '';

                                    if (!empty($bowler_id)) {
                                        $bowler_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$bowler_id' LIMIT 1"));
                                        $bowler_name = $bowler_row['player_name'] ?? "";
                                    }

                                    // Get fielder name (for catches/run outs etc.)
                                    if (!empty($by)) {
                                        $parts = explode(',', $by); // Split "id,position"
                                        $fielder_id = trim($parts[0]);
                                        if (!empty($fielder_id)) {
                                            $fielder_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$fielder_id' LIMIT 1"));
                                            $fielder_name = $fielder_row['player_name'] ?? "";
                                        }
                                    }

                                    // Generate the wicket comment
                                    if (!empty($wicket)) {
                                        if (!empty($fielder_name)) {
                                            $comment = "<span style='color:red;'>WICKET! $striker is out: $wicket by $fielder_name, bowled by $bowler_name.</span>";
                                        } else {
                                            $comment = "<span style='color:red;'>WICKET! $striker is out: $wicket by $bowler_name.</span>";
                                        }
                                    } elseif ($run == 4) {
                                        $comment = "<span style='color:#FF9800;'>FOUR! $striker hits a $shot through $side. Beautiful shot!</span>";
                                    } elseif ($run == 6) {
                                        $comment = "<span style='color:#4CAF50;'>SIX! $striker smashes a $shot over $side. Massive hit!</span>";
                                    } elseif ($type == 'wide ball' || $type === 'no ball') {
                                        $comment = "<span style='color:#FFC107;'>Extra: $type ball. One run added.</span>";
                                    } elseif ($run > 0) {
                                        $comment = "$striker plays a $shot to $side and gets $run run" . ($run > 1 ? "s" : "") . ".";
                                    } else {
                                        $comment = "$striker defends it. No run.";
                                    }

                                    if (!empty($ball['Commentary'])) {
                                        $comment .= " <i>(" . htmlspecialchars($ball['Commentary']) . ")</i>";
                                    }
                                ?>

                                <div class="ball-data">
                                    <div class="ball-runs">
                                        <div class="ball"><?= $ball['overs_completed'] ?? '-' ?></div>
                                        <div class="run"><?= $run ?></div>
                                    </div>
                                    <div class="commentry"><?= $comment ?></div>
                                </div>
                                <hr class="line">
                            <?php endforeach; ?>
                        </div>
                        <button id="toggle-button" style="margin-top: 10px;">Show More</button>
                </div><?php } ?>

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
                                <div class="error-img"><img src="https://staticg.sportskeeda.com/cricket_images/scorecard/cmc-pre-scorecard-icon.svg" alt=""></div>
                                <span class="error-text">Match has not started yet</span>
                            </div>
                            HTML;
                        
                    if($row['status'] == 'Upcoming' || empty($row['toss_winner'])){
                        echo $HTML;
                    }
                    
                ?>

                <?php
                    //get all players
                    $players = [];
                    $sql = "SELECT user_id, player_name FROM players WHERE team_id IN (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $score_log['team1'], $score_log['team2']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row2 = $result->fetch_assoc()) {
                        $players[$row2['user_id']] = $row2['player_name'];
                    }

                    if(!empty($row['toss_winner'])){
                    foreach (['1st', '2nd'] as $inning_key) {
                        $inning = $score_log['innings'][$inning_key] ?? '';
                        $batting_team = $inning['batting_team'] ?? '';
                        $bowling_team = $inning['bowling_team'] ?? '';
                        $balls = $inning['balls'] ?? '';
                        $bowlers = $inning['bowlers'] ?? '';
                        $openers = $inning['openers'] ?? '';
                        $batmans = $inning['batmans'] ?? '';
                        $wickets = $inning['wickets'] ?? '';
                        $total_runs = $inning['total_runs'] ?? '';
                        $overs_bowled = $inning['overs_completed'] ?? '';
                
                        $sql = "SELECT t_id, t_name FROM teams WHERE t_id IN (?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $batting_team, $bowling_team);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $team_names[$row['t_id']] = $row['t_name'];
                            }
                        }
                ?>

                <section id="team1" data-team = '<?php echo $batting_team; ?>'>
                    <div class="bat-data">
                        <div class="team-container">
                            <div class="team-name"><lable class="name"><?php echo $team_names[$batting_team]; ?></lable><label for="" class="score"><?php echo "$total_runs/$wickets ($overs_bowled)"; ?></label></div>
                        </div>
                        <div class="team-data">
                            <div class="dt">
                                <div class="dt1 table-head">
                                    <div class="inning-batsman">Batters</div>
                                    <div class="player-runs">R</div>
                                    <div class="player-balls">B</div>
                                    <div class="player-fours">4s</div>
                                    <div class="player-sixs">6s</div>
                                    <div class="player-SR">SR</div>
                                </div>
                                <div class="dt2"></div>
                            </div>
                        <div class="batsmanStats">

                        <?php
                            foreach ($batmans as $batsman) {
                                $name = $players[$batsman['id']] ?? "Unknown Player";
                                $runs = $batsman['runs'];
                                $balls = $batsman['balls_faced'];
                                $fours = $batsman['fours'];
                                $sixes = $batsman['sixes'];
                                $sr = ($balls > 0) ? round(($runs / $balls) * 100, 0) : 0;
                                $status = ucfirst($batsman['out_status']);
                                
                                // Optional: Add additional wicket info if available
                               $status = ucfirst($batsman['out_status']);

                                // Get names
                                $wicket_type = $batsman['wicket_type'] ?? '';
                                $wicket_by   = $batsman['wicket_by'] ?? '';
                                $bowler_id   = $batsman['bowler'] ?? '';
                                $bowler_name = $fielder_name = "";

                                // Get bowler name
                                if (!empty($bowler_id)) {
                                    $bowler_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$bowler_id' LIMIT 1"));
                                    $bowler_name = $bowler_row['player_name'] ?? "";
                                }

                                // Get fielder name (in case of catch or run out)
                                if (!empty($wicket_by)) {
                                    $parts = explode(',', $wicket_by); // handle "id,position"
                                    $fielder_id = trim($parts[0]);
                                    if (!empty($fielder_id)) {
                                        $fielder_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$fielder_id' LIMIT 1"));
                                        $fielder_name = $fielder_row['player_name'] ?? "";
                                    }
                                }

                                // Build status message
                                if (!empty($wicket_type)) {
                                    if ($wicket_type == "Caught" && $fielder_name && $bowler_name) {
                                        $status = "c {$fielder_name} b {$bowler_name}";
                                    } elseif ($wicket_type == "Run Out" && $bowler_name) {
                                        $status = "run out ({$bowler_name})";
                                    } elseif ($wicket_type == "Bowled" && $bowler_name) {
                                        $status = "b {$bowler_name}";
                                    } elseif ($wicket_type == "LBW" && $bowler_name) {
                                        $status = "lbw b {$bowler_name}";
                                    } else {
                                        $status = "{$wicket_type}" . (!empty($bowler_name) ? " b {$bowler_name}" : "");
                                    }
                                }

                                // Check if not out to add class
                                $notOutClass = ($batsman['out_status'] == 'not out') ? 'not-out' : '';
                                ?>
                                <div class="dt <?= $notOutClass ?>">
                                    <div class="dt1">
                                        <div class="inning-batsman playername"><?= htmlspecialchars($name) ?></div>
                                        <div class="player-runs"><?= $runs ?></div>
                                        <div class="player-balls"><?= $balls ?></div>
                                        <div class="player-fours"><?= $fours ?></div>
                                        <div class="player-sixs"><?= $sixes ?></div>
                                        <div class="player-SR"><?= $sr ?></div>
                                    </div>
                                    <div class="dt2"><?= $status ?></div>
                                </div>
                                <?php
                            }?></div>

                            <div class="dt" id="Extra-block">
                                <div class="dt1">
                                    <div class="inning-batsman">Extras</div>
                                    <div class="player-runs"><?php echo $inning['Extras']['total_extras'] ?? 0; $extras = $inning['Extras'];?></div>
                                    <div class="player-balls">(B: <?php echo $extras['B']; ?>, LB: <?php echo $extras['LB']; ?>, NB: <?php echo $extras['NB']; ?>, W: <?php echo $extras['W']; ?>)</div>
                                    <div class="player-fours"></div>
                                </div>
                            </div>

                            <div class="dt" id='total-team-score'>
                                <?php
                                    $total_fours = 0;
                                    $total_sixes = 0;

                                    foreach ($score_log['innings'][$inning_key]['balls'] as $ball) {
                                        $runs = $ball['Run'] ?? 0;

                                        if ($runs == 4) {
                                            $total_fours++;
                                        } elseif ($runs == 6) {
                                            $total_sixes++;
                                        }
                                    }
                                ?>
                                <div class="dt1">
                                    <div class="inning-batsman">TOTAL</div>
                                    <div class="player-runs total_runs"><?php echo $total_runs.'/'.$wickets; ?></div>
                                    <div class="player-balls"></div>
                                    <div class="player-fours"><?php echo $total_fours; ?></div>
                                    <div class="player-sixs"><?php echo $total_sixes; ?></div>
                                    <div class="player-SR"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bowl-data">
                        <div class="dt">
                                <div class="dt1 table-head">
                                    <div class="inning-batsman">Bowlers</div>
                                    <div class="player-runs">O</div>
                                    <div class="player-balls">M</div>
                                    <div class="player-fours">R</div>
                                    <div class="player-sixs">W</div>
                                    <div class="player-SR">ER</div>
                                </div>
                            </div>
                            <div id="bowler-stats">
                            <?php
                                $current_bowler = $inning['current_bowler'] ?? [];

                                foreach ($bowlers as $bowler_id => $data) {
                                    // If bowler_id matches current_bowler id, prefer current_bowler data
                                    if (!empty($current_bowler) && $current_bowler['id'] === $bowler_id) {
                                        $data = $current_bowler;
                                    }

                                    $bowler_name = $players[$bowler_id] ?? "Unknown Bowler";
                                    $overs = $data['overs_bowled'];
                                    $maidens = $data['maidens'];
                                    $runs = $data['runs_conceded'];
                                    $wickets = $data['wickets'];
                                    $EXT = $data['Extras']['total_extras'];
                                    $er = round($runs / max(1, floatval($overs)), 2);

                                    echo "<div class='dt'>
                                            <div class='dt1'>
                                                <div class='inning-batsman playername'>$bowler_name</div>
                                                <div class='player-runs'>$overs</div>
                                                <div class='player-balls'>$maidens</div>
                                                <div class='player-fours'>$runs</div>
                                                <div class='player-sixs'>$wickets</div>
                                                <div class='player-SR'>$er</div>
                                            </div>
                                        </div>";
                                }
                            ?></div>
                    </div>

                    <div class="fall-of-wickets">
                        <div class="dt table-head">
                                <div class="inning-batsman">Fall Of Wickets</div>
                                <div class="out-by"></div>
                                <div class="score-and-over">
                                    <div class="score">SCORE</div>
                                    <div class="overs">OVER</div>
                                </div>
                        </div>
                        <div id="fall-of-wickets">
                        <?php
                        $fall_number = 1;
                        foreach ($batmans as $batsman) {
                            if ($batsman['out_status'] !== 'not out') {
                                $name = $players[$batsman['id']] ?? "Unknown Player";
                                $bowler = $batsman['bowler'] ?? '';
                                $wicket_type = $batsman['wicket_type'] ?? '';
                                $wicket_by = $batsman['wicket_by'] ?? '';
                                list($wicket_over, $wicket_runs) = getWicketBallDetails($score_log['innings'][$inning_key]['balls'], $player_id);


                                if (!empty($bowler)) {
                                    $bowler_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$bowler' LIMIT 1"));
                                    $bowler_name = $bowler_row['player_name'] ?? "Unknown Bowler";
                                }

                                if (!empty($wicket_by)) {
                                    $parts = explode(',', $wicket_by); // Split at comma
                                    $fielder_id = trim($parts[0]); // Get player ID only

                                    if (!empty($fielder_id)) {
                                        $fielder_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$fielder_id' LIMIT 1"));
                                        $fielder_name = $fielder_row['player_name'] ?? "Unknown Fielder";
                                    }
                                }
                                // Build status like "lbw b Goud"
                                $out_by = !empty($bowler_name) && !empty($fielder_name) ? "c {$fielder_name} b {$bowler_name}" : "b {$bowler_name}";

                                // Fallbacks
                                if (empty($wicket_runs)) $wicket_runs = "-";
                                if (empty($wicket_over)) $wicket_over = "-";
                                ?>
                                <div class="dt">
                                    <div class="inning-batsman playername"><?= htmlspecialchars($name) ?></div>
                                    <div class="out-by"><?= htmlspecialchars($out_by) ?></div>
                                    <div class="score-and-over">
                                        <div class="score"><?= $wicket_runs ?></div>
                                        <div class="overs"><?= $wicket_over ?></div>
                                    </div>
                                </div>
                                <?php
                                $fall_number++;
                            }
                            }
                            ?></div>
                    </div>
                </section>
                <?php } }?>

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
    const matchID = <?php echo json_encode($match_id); ?>;
    let current_innings = <?php echo json_encode($current_innings); ?>;
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

    close_ad.addEventListener('click', () => {
        ad_container.style.display = 'none';
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

    //show more
document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector(".comm-data");
    const button = document.getElementById("toggle-button");

    // ✅ Exit early if container or button is not found
    if (!container || !button) return;

    const allBlocks = Array.from(container.querySelectorAll(".ball-data"));
    const allLines = Array.from(container.querySelectorAll(".line"));

    let visibleCount = allBlocks.length > 7 ? 7 : allBlocks.length;
    let expanded = false;

    function updateView() {
        const total = allBlocks.length;

        allBlocks.forEach((el, i) => {
            el.style.display = (i >= total - visibleCount) ? "flex" : "none";
        });

        allLines.forEach((el, i) => {
            el.style.display = (i >= total - visibleCount) ? "block" : "none";
        });

        if (visibleCount >= total && total > 7) {
            button.textContent = "Show Less";
            container.style.height = "max-content";
            expanded = true;
        } else {
            button.textContent = "Show More";
            container.style.height = "max-content";
            expanded = false;
        }
    }

    updateView();

    button.addEventListener("click", function () {
        visibleCount = expanded ? 7 : allBlocks.length;
        updateView();
    });
});

let previousData = null;

function fetchScoreboard() {
    fetch(`../../API/CRICKET_api.php?match_id=${matchID}&current_innings=${current_innings}`)
        .then(response => response.json())
        .then(data => {
            // Update scoreboard elements
            // console.log(data);
            if (typeof data === 'string') {
                data = JSON.Parse(data);
            }
            const innings = (data?.super_over_innings && Object.keys(data.super_over_innings).length > 0)
            ? 'super_over_innings'
            : 'innings';

            const balls = data?.[innings]?.[current_innings]?.['balls'];

            // if (!balls || balls.length === 0) {
            //     window.location.reload();
            //     return;
            // }
            let latestBall = null;

            if (Array.isArray(balls) && balls.length > 0) {
                latestBall = balls[balls.length - 1];
                // Continue processing...
            }

            const speech = latestBall?.['Commentary'] || '';
            const ball_type = latestBall?.['Ball Type'] || '';

            // Only update if data has changed
            if (data !== null && !isEqual(previousData, data, speech, ball_type)) {
                previousData = deepClone(data); // Store copy for next comparison
                updateMatchUI(data);
                showUpdateIndicator();
                
                console.log(current_innings);

                 current_innings = null
                // 1. Check active super over innings first
                if (data?.super_over_innings && typeof data.super_over_innings === 'object') {
                    for (const [innings_name, innings_data] of Object.entries(data.super_over_innings)) {
                        if (innings_data?.completed === false) {
                            current_innings = innings_name;
                            break;
                        }
                    }
                }

                // 2. If no active super over, check regular innings
                if (current_innings === null && data?.innings && typeof data.innings === 'object') {
                    for (const [innings_name, innings_data] of Object.entries(data.innings)) {
                        if (innings_data?.completed === false) {
                            current_innings = innings_name;
                            break;
                        }
                    }
                }
                
                // 3. If no active innings but match not completed, use last inning
                if (
                    current_innings === null &&
                    (!('match_completed' in data) || data.match_completed !== true)
                ) {
                    // Try last super over
                    if (data?.super_over_innings && typeof data.super_over_innings === 'object') {
                        const superOverEntries = Object.entries(data.super_over_innings);
                        if (superOverEntries.length > 0) {
                            current_innings = superOverEntries[superOverEntries.length - 1][0];
                        }
                    }

                    // Or last regular inning
                    if (current_innings === null && data?.innings && typeof data.innings === 'object') {
                        const inningsEntries = Object.entries(data.innings);
                        if (inningsEntries.length > 0) {
                            current_innings = inningsEntries[inningsEntries.length - 1][0];
                        }
                    }
                }
                console.log(current_innings);
                updatebatsman(data);
                
            }

        });
}

// Fetch every 5 seconds
setInterval(fetchScoreboard, 1500);

//speech
function speakText(text) {
    console.log('called')
      const speech = new SpeechSynthesisUtterance(text);
      speech.lang = "en-US"; // Language (e.g., 'hi-IN' for Hindi)
      speech.pitch = 1;
      speech.rate = 1;
      speech.volume = 1;
      window.speechSynthesis.speak(speech);
    }

// Deep comparison of objects (excluding circular references)
function isEqual(obj1, obj2, text,ball_type) {
    // Simple cases
    if (obj1 === obj2) return true;
    if (typeof obj1 !== 'object' || typeof obj2 !== 'object' || obj1 === null || obj2 === null) {
        return obj1 === obj2;
    }

    // Compare keys
    const keys1 = Object.keys(obj1);
    const keys2 = Object.keys(obj2);
    if (keys1.length !== keys2.length){
        
        if((ball_type !== null || ball_type != '') && commentaryEnabled){
            speakText(text);
        }
        
        // smoothReload();
        return false;
    } 

    // Deep compare each value
    for (const key of keys1) {
        if (!keys2.includes(key)) return false;
        if (!isEqual(obj1[key], obj2[key])){
            
            if((ball_type !== null || ball_type != '') && commentaryEnabled){
                speakText(text);
            }
            // smoothReload();
            
            return false;
        } 
    }

    return true;
}

// Safe deep clone
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

// Visual update indicator
function showUpdateIndicator() {
    const indicator = document.getElementById('refresh-indicator');
    if (indicator) {
        indicator.classList.add('active');
        setTimeout(() => indicator.classList.remove('active'), 1000);
    }
}

function updateMatchUI(matchData) {
    let total_runs = 0;
    let wickets = 0;
    let total_sr = 0;
    let sr_count = 0;
    const current_innings_data = matchData?.innings?.[current_innings] ?? '';
    const ball = current_innings_data.balls;
    const avg_wickets = current_innings_data['wickets'];
    const batsmans = current_innings_data['batmans'];

    if(current_innings_data == ''){
        return;
    }
    // Calculate batting stats
    batsmans.forEach(batsman => {
        total_runs += batsman.runs;

        if (batsman.out_status !== 'not out') {
            wickets++;
        }

        if (batsman.balls_faced > 0) {
            total_sr += (batsman.runs / batsman.balls_faced) * 100;
            sr_count++;
        }
    });

    const avg_runs = batsmans.length > 0 ? Math.round((current_innings_data.total_runs / batsmans.length) * 100) / 100 : '-';
    const avg_sr = sr_count > 0 ? Math.round((total_sr / sr_count) * 100) / 100 : '-';
    

    document.querySelectorAll('.score')[0].innerText = matchData.team1_full_score;
    document.querySelectorAll('.score')[1].innerText = matchData.team2_full_score;
    
    document.querySelector('#run_rate').innerText = 'CRR : ' + (ball[ball.length - 1]?.RR || '0.00');

    if(current_innings == '1st'){
        console.log('update 1st inning : ',avg_runs ,avg_wickets, avg_sr)
        document.querySelectorAll('.avg-runs')[0].innerText = avg_runs;
        document.querySelectorAll('.avg-wickets')[0].innerText = avg_wickets;
        document.querySelectorAll('.avg-sr')[0].innerText = avg_sr;
    }else if(current_innings == '2nd'){
        document.querySelectorAll('.avg-runs')[1].innerText = avg_runs;
        document.querySelectorAll('.avg-wickets')[1].innerText = avg_wickets;
        document.querySelectorAll('.avg-sr')[1].innerText = avg_sr;
    }
    
    updateCommentary(matchData);
}

let showAllBalls = false; // Track the current display state globally

function updateCommentary(data) {
    const container = document.querySelector('.comm-data');
    const toggleButton = document.getElementById('toggle-button');
    
    // Clear existing content
    if(container){
        container.innerHTML = '';
    }

    if (!data || Object.keys(data).length === 0) {
        container.innerHTML = '<p>No commentary available yet</p>';
        toggleButton.style.display = 'none';
        return;
    }

let balls = [];

const regular = data.innings;
const superOvers = data.super_over_innings;

// Collect all balls from all innings in correct match order
balls = [
    ...(regular?.['1st']?.balls || []),
    ...(regular?.['2nd']?.balls || []),
    ...(superOvers?.['1st']?.balls || []),
    ...(superOvers?.['2nd']?.balls || [])
];

// Optional: reverse to show latest first
//balls = balls.reverse();

    if (balls.length === 0 && container) {
        container.innerHTML = '<p>No commentary available yet</p>';
        toggleButton.style.display = 'none';
        return;
    }

    // Process balls in reverse order
    const reversedBalls = [...balls].reverse();
    
    // Create document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    reversedBalls.forEach((ball, index) => {
        const run = parseInt(ball.Run) || 0;
        const type = (ball['Ball Type'] || '').toLowerCase();
        const striker = data.player_map?.[ball.Striker] || 'Batter';
        const bowlerName = data.bowler_map?.[ball.Bowler] || '';
        
        // Generate commentary
        let comment = '';
        if (ball['Wicket Type']) {
            const fielderId = ball['Wicket By']?.split(',')[0]?.trim();
            const fielderName = data.fielder_map?.[fielderId] || '';
            comment = `<span style="color:red">WICKET! ${striker} is out: ${ball['Wicket Type']} by ${fielderName || bowlerName}${fielderName ? ', bowled by ' + bowlerName : ''}.</span>`;
        } 
        else if (run === 4) comment = `<span style="color:#FF9800">FOUR! ${striker} hits a ${ball['Shot Type'] || ''} through ${ball['Shot Side'] || ''}.</span>`;
        else if (run === 6) comment = `<span style="color:#4CAF50">SIX! ${striker} smashes a ${ball['Shot Type'] || ''} over ${ball['Shot Side'] || ''}.</span>`;
        else if (type === 'wide ball' || type === 'no ball') comment = `<span style="color:#FFC107">Extra: ${type} ball.</span>`;
        else if (run > 0) comment = `${striker} scores ${run} run${run > 1 ? 's' : ''} with a ${ball['Shot Type'] || ''} to ${ball['Shot Side'] || ''}.`;
        else comment = `${striker} defends. No run.`;
        
        // Add custom commentary if available
        if (ball.Commentary) comment += ` <i>(${ball.Commentary.replace(/[&<>'"]/g, tag => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[tag]))})</i>`;
        
        // Create ball element
        const ballDiv = document.createElement('div');
        ballDiv.className = 'ball-data';
        ballDiv.style.display = (index >= 7 && !showAllBalls) ? 'none' : 'flex';
        ballDiv.innerHTML = `
            <div class="ball-runs">
                <div class="ball">${ball.overs_completed || '-'}</div>
                <div class="run">${run}</div>
            </div>
            <div class="commentry">${comment}</div>
        `;
        
        fragment.appendChild(ballDiv);
        
        // Create line element
        const line = document.createElement('hr');
        line.className = 'line';
        line.style.display = (index >= 7 && !showAllBalls) ? 'none' : 'block';
        fragment.appendChild(line);
    });

    container.appendChild(fragment);
    initShowMoreButton();
    updatebatsman(data);
}

let updatebatsman = (data) => {
    if(!data['innings']){
        return;
    }
    console.log(data)
    let bat_team = data['innings'][current_innings]['batting_team'];
    let section = document.querySelector(`section[data-team="${bat_team}"]`);
    let batmans = data['innings'][current_innings]['batmans'];
    const ids = [...new Set([
        ...batmans.map(player => player.id),
        ...batmans.map(player => player.bowler).filter(Boolean),
        ...batmans.map(player => (player.wicket_by || '').split(',')[0].trim()).filter(Boolean)
    ])].join(',');

    fetch(`../../API/get_player.php?ids=${ids}`)
        .then(res => res.json())
        .then(players => {
            const container = section.querySelector('.batsmanStats');
            container.innerHTML = ''; // clear previous if needed

            batmans.forEach(batsman => {
                const name = players[batsman.id] || 'Unknown Player';
                const runs = batsman.runs;
                const balls = batsman.balls_faced;
                const fours = batsman.fours;
                const sixes = batsman.sixes;
                const sr = (balls > 0) ? ((runs / balls) * 100).toFixed(2) : 0;
                let status = batsman.out_status.charAt(0).toUpperCase() + batsman.out_status.slice(1);

                const wicket_type = batsman.wicket_type || '';
                const wicket_by = batsman.wicket_by || '';
                const bowler_id = batsman.bowler || '';
                const bowler_name = players[bowler_id] || '';
                
                let fielder_name = '';
                if (wicket_by) {
                    const fielder_id = wicket_by.split(',')[0].trim();
                    if (fielder_id) {
                        fielder_name = players[fielder_id] || '';
                    }
                }

                // Build status message
                if (wicket_type) {
                    if (wicket_type === "Caught" && fielder_name && bowler_name) {
                        status = `c ${fielder_name} b ${bowler_name}`;
                    } else if (wicket_type === "Run Out" && bowler_name) {
                        status = `run out (${bowler_name})`;
                    } else if (wicket_type === "Bowled" && bowler_name) {
                        status = `b ${bowler_name}`;
                    } else if (wicket_type === "LBW" && bowler_name) {
                        status = `lbw b ${bowler_name}`;
                    } else {
                        status = `${wicket_type}${bowler_name ? ' b ' + bowler_name : ''}`;
                    }
                }

                const notOutClass = (batsman.out_status === 'not out') ? 'not-out' : '';

                const div = document.createElement('div');
                div.className = `dt ${notOutClass}`;
                div.innerHTML = `
                    <div class="dt1">
                        <div class="inning-batsman playername">${name}</div>
                        <div class="player-runs">${runs}</div>
                        <div class="player-balls">${balls}</div>
                        <div class="player-fours">${fours}</div>
                        <div class="player-sixs">${sixes}</div>
                        <div class="player-SR">${sr}</div>
                    </div>
                    <div class="dt2">${status}</div>
                `;

                container.appendChild(div);
            });
        })
        .catch(error => console.error('Error fetching player names:', error));
    
        let extra = data['innings'][current_innings]['Extras'];
        let total_ex = extra['total_extras'];
        let B   = extra['B'] ;
        let LB  = extra['LB'];
        let NB  = extra['NB'];
        let W   = extra['W'];

        let Extras = `
                        <div class="dt1">
                            <div class="inning-batsman">Extras</div>
                            <div class="player-runs">${total_ex}</div>
                            <div class="player-balls">(B: ${B}, LB: ${LB}, NB: ${NB}, W: ${W})</div>
                            <div class="player-fours"></div>
                        </div>`;

        section.querySelector('#Extra-block').innerHTML = Extras;

        let totalFours = 0;
        let totalSixes = 0;

        let balls = data.innings[current_innings]?.balls || [];
        let total_runs = data.innings[current_innings]['total_runs'];
        let wickets = data.innings[current_innings]['wickets'];
        let over = data.innings[current_innings]['overs_completed'];

       section.querySelector('.score').innerHTML = `${total_runs} / ${wickets} ( ${over} )`;

        balls.forEach(ball => {
            const runs = parseInt(ball.Run) || 0;

            if (runs === 4) {
                totalFours++;
            } else if (runs === 6) {
                totalSixes++;
            }
        });

        let Total = `
                    <div class="dt1">
                        <div class="inning-batsman">TOTAL</div>
                        <div class="player-runs total_runs">${total_runs} / ${wickets}</div>
                        <div class="player-balls"></div>
                        <div class="player-fours">${totalFours}</div>
                        <div class="player-sixs">${totalSixes}</div>
                        <div class="player-SR"></div>
                    </div>`;

        console.log(Total);
        section.querySelector('#total-team-score').innerHTML = Total;

        //Bowler Stats
        const targetContainer = section.querySelector('#bowler-stats');
        const innings = data['innings'] || {};
        const superOverInnings = data['super_over_innings'] || {};

        // Step 1: Collect all bowlers from all innings
        const allBowlerInnings = {
            [current_innings]: innings[current_innings]?.bowlers || {}
        };

        const currentBowlers = {
            [current_innings]: innings[current_innings]?.current_bowler || {}
        };

        // Step 2: Extract all unique bowler IDs
        const bowlerIdsSet = new Set();

        for (const inningKey in allBowlerInnings) {
            const bowlers = allBowlerInnings[inningKey];
            for (const bowlerId in bowlers) {
                if (bowlerId) bowlerIdsSet.add(bowlerId);
            }

            const currentId = currentBowlers[inningKey]?.id;
            if (currentId) bowlerIdsSet.add(currentId);
        }

        const bowlerIds = Array.from(bowlerIdsSet).join(',');

        // Step 3: Fetch names and render UI
        fetch(`../../API/get_player.php?ids=${bowlerIds}`)
            .then(res => res.json())
            .then(playerMap => {
                targetContainer.innerHTML = '';

                for (const inningKey in allBowlerInnings) {
                    const bowlerStats = allBowlerInnings[inningKey];
                    const currentBowler = currentBowlers[inningKey];

                    for (const bowlerId in bowlerStats) {
                        let bowlerData = bowlerStats[bowlerId];

                        // Prefer current_bowler data if ID matches
                        if (currentBowler.id && currentBowler.id === bowlerId) {
                            bowlerData = currentBowler;
                        }

                        const bowlerName = playerMap[bowlerId] || 'Unknown Bowler';
                        const overs = bowlerData.overs_bowled || '0.0';
                        const maidens = bowlerData.maidens || 0;
                        const runs = bowlerData.runs_conceded || 0;
                        const wickets = bowlerData.wickets || 0;
                        const EXT = bowlerData.Extras.total_extras || 0;

                        const oversFloat = parseFloat(overs) || 0;
                        const economyRate = (oversFloat > 0) ? (runs / oversFloat).toFixed(2) : '0.00';

                        const div = document.createElement('div');
                        div.className = 'dt';
                        div.innerHTML = `
                            <div class="dt1">
                                <div class="inning-batsman playername">${bowlerName}</div>
                                <div class="player-runs">${overs}</div>
                                <div class="player-balls">${maidens}</div>
                                <div class="player-fours">${runs}</div>
                                <div class="player-sixs">${wickets}</div>
                                <div class="player-SR">${calculateEconomy(overs, runs)}</div>
                                <div class="player-EXT">${EXT}</div>
                            </div>
                        `;
                        targetContainer.appendChild(div);
                        console.log(div)
                    }
                }
            })
            .catch(err => console.error("Error fetching bowler names:", err));

        //Fall Of Wickets
        // 1. Collect all unique player IDs (batsman, bowler, fielder)
        const batsmanIds = batmans
            .filter(b => b.out_status !== 'not out')
            .map(b => b.id);

        const bowlerIds2 = batmans
            .filter(b => b.out_status !== 'not out')
            .map(b => b.bowler)
            .filter(Boolean);

        const fielderIds = batmans
            .filter(b => b.out_status !== 'not out' && b.wicket_by)
            .map(b => b.wicket_by.split(',')[0].trim())
            .filter(Boolean);

        const allIds = [...new Set([...batsmanIds, ...bowlerIds2, ...fielderIds])];
        const idsParam = allIds.join(',');

    // 2. Fetch player names using your API
    fetch(`../../API/get_player.php?ids=${idsParam}`)
    .then(res => res.json())
    .then(players => {
        // 3. Render Fall of Wickets block
        let fall_number = 1;
        const fallOfWicketsContainer = section.querySelector('#fall-of-wickets');
        fallOfWicketsContainer.innerHTML = '';

        batmans.forEach(batsman => {
            if (batsman.out_status !== 'not out') {
                const playerId = batsman.id;
                const name = players[playerId] || 'Unknown Player';

                const bowlerId = batsman.bowler || '';
                const wicketType = batsman.wicket_type || '';
                const wicketBy = batsman.wicket_by || '';

                const bowlerName = players[bowlerId] || '';
                let fielderName = '';
                if (wicketBy) {
                    const fielderId = wicketBy.split(',')[0].trim();
                    fielderName = players[fielderId] || '';
                }

                let outBy = '';
                if (wicketType === "Caught" && fielderName && bowlerName) {
                    outBy = `c ${fielderName} b ${bowlerName}`;
                } else if (wicketType === "Run Out" && bowlerName) {
                    outBy = `run out (${bowlerName})`;
                } else if (wicketType === "Bowled" && bowlerName) {
                    outBy = `b ${bowlerName}`;
                } else if (wicketType === "LBW" && bowlerName) {
                    outBy = `lbw b ${bowlerName}`;
                } else {
                    outBy = bowlerName ? `b ${bowlerName}` : 'Unknown';
                }

                // Find ball where wicket happened
                let wicket_over = '-';
                let wicket_runs = '-';

                for (let ball of balls) {
                    if (
                        ball['Ball Type'] === 'Wicket' &&
                        ball['Out Player'] === playerId
                    ) {
                        let wickets = ball['Wickets'] ?? '-';
                        let totalScore = ball['TotalScore'] ?? '-';
                        wicket_over = ball['overs_completed'] ?? '-';
                        wicket_runs = `${totalScore}-${wickets}`;
                        break;
                    }
                }

                // Create HTML
                const div = document.createElement('div');
                div.className = 'dt';
                div.innerHTML = `
                    <div class="inning-batsman playername">${name}</div>
                    <div class="out-by">${outBy}</div>
                    <div class="score-and-over">
                        <div class="score">${wicket_runs}</div>
                        <div class="overs">${wicket_over}</div>
                    </div>
                `;

                fallOfWicketsContainer.appendChild(div);
                fall_number++;
            }
        });
    })
    .catch(err => console.error('Error fetching player names:', err));
    
    let inning = (data?.super_over_innings && Object.keys(data.super_over_innings).length > 0)
            ? 'super_over_innings'
            : 'innings';
    let openers = data[inning][current_innings]?.openers;
    let curr_bowler = data[inning][current_innings]?.current_bowler;
    console.log(curr_bowler)
    if (openers && curr_bowler) {
        const strikerId = openers.current_striker?.id;
        const nonStrikerId = openers.current_non_striker?.id;
        const current_bowlerId = curr_bowler.id;

        let currentBowlerName = '';
        let nonStrikerName = '';
        let strikerName = '';
        // Combine IDs for API call
        const ids = [strikerId, nonStrikerId, current_bowlerId].filter(Boolean).join(',');

        // Fetch player names
        fetch(`../../API/get_player.php?ids=${ids}`)
            .then(res => res.json())
            .then(players => {
                strikerName = players[strikerId] || 'Striker';
                nonStrikerName = players[nonStrikerId] || 'Non-Striker';
                currentBowlerName = players[current_bowlerId] || 'Current Bowler';

                let curr_players = document.querySelector('.curr_players');
                let HTMLData = `
                    <div class="striker-non_striker">
                        <div class="dt">
                            <div class="dt1 table-head">
                                <div class="inning-batsman">Batters</div>
                                <div class="player-runs">R</div>
                                <div class="player-balls">B</div>
                                <div class="player-fours">4s</div>
                                <div class="player-sixs">6s</div>
                                <div class="player-SR">SR</div>
                            </div>
                            <div class="dt ">
                                <div class="dt1">
                                    <div class="inning-batsman playername">${strikerName} <svg id="striker" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.9641 6.85855L17.7261 10.5918C17.9948 10.8584 18.1299 11.1702 18.1312 11.5271C18.1326 11.8841 18 12.1969 17.7333 12.4656L6.40028 23.8859C6.13362 24.1546 5.83299 24.2896 5.49838 24.2909C5.16377 24.2922 4.86211 24.1595 4.5934 23.8928L0.83141 20.1596C0.562696 19.8929 0.427613 19.57 0.426158 19.1908C0.424703 18.8116 0.557304 18.4876 0.823964 18.2189L12.0903 6.86574C12.357 6.59702 12.6688 6.46198 13.0257 6.46061C13.3826 6.45924 13.6954 6.59189 13.9641 6.85855ZM23.4521 2.94063L18.7189 7.7103L16.8379 5.84368L21.5711 1.07402C21.8155 0.827697 22.1274 0.703808 22.5066 0.702353C22.8858 0.700898 23.1986 0.822391 23.4449 1.06683C23.6912 1.31127 23.8151 1.6231 23.8166 2.00232C23.818 2.38154 23.6965 2.69431 23.4521 2.94063ZM4.74339 2.14268C4.74339 2.14268 5.12042 1.47201 4.51156 2.57213C4.39223 2.78774 4.74339 2.14268 4.74339 2.14268C4.74339 2.14268 4.07831 3.21752 4.07418 2.14525C4.08373 4.62457 4.74339 2.14268 4.74339 2.14268Z" fill="black"></path>
                                    </svg></div>
                                    <div class="player-runs">${openers.current_striker.runs}</div>
                                    <div class="player-balls">${openers.current_striker.balls_faced}</div>
                                    <div class="player-fours">${openers.current_striker.fours}</div>
                                    <div class="player-sixs">${openers.current_striker.sixes}</div>
                                    <div class="player-SR">${openers.current_striker.balls_faced > 0 ? ((openers.current_striker.runs/openers.current_striker.balls_faced)*100).toFixed(0) : 0}</div>
                                </div>
                                <div class="dt2"></div>
                            </div>
                            <div class="dt ">
                                <div class="dt1">
                                    <div class="inning-batsman playername">${nonStrikerName}</div>
                                    <div class="player-runs">${openers.current_non_striker.runs}</div>
                                    <div class="player-balls">${openers.current_non_striker.balls_faced}</div>
                                    <div class="player-fours">${openers.current_non_striker.fours}</div>
                                    <div class="player-sixs">${openers.current_non_striker.sixes}</div>
                                    <div class="player-SR">${openers.current_non_striker.balls_faced > 0 ? ((openers.current_non_striker.runs/openers.current_non_striker.balls_faced)*100).toFixed(0) : 0}</div>
                                </div>
                                <div class="dt2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="curr-bowler">
                        <div class="dt">
                            <div class="dt1 table-head">
                                <div class="inning-batsman">Bowler</div>
                                <div class="player-runs">O</div>
                                <div class="player-balls">M</div>
                                <div class="player-fours">R</div>
                                <div class="player-sixs">W</div>
                                <div class="player-SR">ER</div>
                            </div>
                        </div>
                        <div class='dt'>
                            <div class='dt1'>
                                <div class='inning-batsman playername'>${currentBowlerName}</div>
                                <div class='player-runs'>${curr_bowler.overs_bowled}</div>
                                <div class='player-balls'>${curr_bowler.maidens}</div>
                                <div class='player-fours'>${curr_bowler.runs_conceded}</div>
                                <div class='player-sixs'>${curr_bowler.wickets}</div>
                                <div class='player-SR'>
                                    ${calculateEconomy(curr_bowler.overs_bowled, curr_bowler.runs_conceded)}
                                    </div>
                            </div>
                        </div>
                    </div>`;
                    curr_players.innerHTML = HTMLData;
            })
            .catch(err => console.error('Error loading openers:', err));
        function calculateEconomy(overs_bowled, runs_conceded) {
            const parts = overs_bowled.toString().split('.');
            const fullOvers = parseInt(parts[0]) || 0;
            const extraBalls = parseInt(parts[1]) || 0;

            const totalBalls = fullOvers * 6 + extraBalls;

            return totalBalls > 0 ? ((runs_conceded / totalBalls) * 6).toFixed(2) : '0.00';
        }
    }
}

    



function initShowMoreButton() {
    const container = document.querySelector('.comm-data');
    const toggleButton = document.getElementById('toggle-button');
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
        for (let i = 10; i < allElements.length; i++) { // Start from index 10 (6th ball + line)
            allElements[i].style.display = showAllBalls ? 'flex' : 'none';
        }
        
        toggleButton.textContent = showAllBalls ? 'Show Less' : 'Show More';
    };
}


 function smoothReload() {
    //   document.body.classList.add("fade-out");
      setTimeout(() => {
        location.replace(location.href); // reload after fade out
      }, 800);
    }

    window.addEventListener("pageshow", function () {
    //   document.body.classList.remove("fade-out");
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