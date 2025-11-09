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
            $rallies = $score_log['sets'][$last_set_number]['rallies'] ?? [];
            $last_rally = is_array($rallies) ? end($rallies) : null;

        }
    }

    $current_serve_team = $score_log['current_serve'] ?? '';

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

    //increment cviews count
$page = 'Cricket'; // change per page
$today = date('Y-m-d');

// Calculate seconds until midnight
$midnight = strtotime('tomorrow') - time();

// Unique cookie name for each page & match per day
$cookie_name = "viewed_{$page}_{$match_id}_{$today}";

// Check if cookie not set (first view today)
if (!isset($_COOKIE[$cookie_name])) {

    // Set cookie to expire automatically at midnight
    setcookie($cookie_name, '1', time() + $midnight, "/");

    // Increment page view count safely
    if (!isset($score_log['page_views'])) {
        $score_log['page_views'] = 1;
    } else {
        $score_log['page_views'] = (int)$score_log['page_views'] + 1;
    }

    // Convert back to JSON
    $json = json_encode($score_log);

    // Update database
    $stmt = $conn->prepare("UPDATE matches SET score_log = ? WHERE match_id = ?");
    $stmt->bind_param("ss", $json, $match_id);
    $stmt->execute();

    $conn->commit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        // Apply stored theme instantly before the page renders
        (function() {
            const theme = localStorage.getItem('theme') ||
                            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            
            // Apply theme attributes early to avoid white flash
            document.documentElement.setAttribute('data-theme', theme);
            document.body?.setAttribute('data-theme', theme);

            // Wait for the logo to exist, then update it
            const checkLogo = setInterval(() => {
                const logo = document.querySelector('.logo-img img');
                if (logo) {
                    logo.src = theme === 'dark'
                        ? "../../assets/images/toggle-logo.png"
                        : "../../assets/images/logo.png";
                    clearInterval(checkLogo);
                }
            }, 50);
        })();
    </script>
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
    
    :root {
        --primary-color: rgba(209, 34, 31, 1);
        --primary-light: rgba(209, 34, 31, 0.8);
        --primary-dark: rgba(160, 25, 23, 1);
        --primary-transparent: rgba(209, 34, 31, 0.1);
        --background: #ffffff;
        --text-dark: #000000;
        --text-light: #666666;
        --border-color: #e0e0e0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --light-bg: #f9f9f9;
        --nav-fill: #ffffffff;
        --invert: invert(0);
    }

    [data-theme="dark"] {
        --background: #121212;
        --text-dark: #ffffff;
        --text-light: #a0a0a0;
        --border-color: #333333;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.3);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.3);
        --light-bg: #1e1e1e;
        --nav-fill: #222222;
        --invert: invert(1);
    }
    
    body{
        background-color: var(--light-bg);
        transition: opacity 0.5s ease, background-color 0.3s ease;
        color: var(--text-dark);
    }
    
    svg path {
        fill: var(--text-dark);
    }

    .menu-bar img {
        filter: var(--invert);
    }
    .pp svg path{
        fill: none;
        stroke: var(--text-dark);
    }
    body.fade-out {
        opacity: 0;
    }
    
    .nav-bar{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        width: 100%;
        height: 61px;
        background-color: var(--nav-fill);
        z-index: 999;
        box-shadow: var(--shadow-sm);
        border-bottom: 1px solid var(--primary-transparent);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    .nav-content{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 10px 20px 10px 0px;
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
        background: var(--light-bg);
        transition: background 0.3s ease;
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
        background: rgba(20, 20, 20, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-light);
        font-size: 14px;
        border-bottom: 1px solid var(--primary-transparent);
    }
    
    .ad2{
        height: 60px;
        width: 100%;
        background: rgba(20, 20, 20, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-light);
        font-size: 14px;
        border-bottom: 1px solid var(--primary-transparent);
    }
    
    .ad3{
        height: 300px;
        width: 600px;
        background: rgba(20, 20, 20, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-light);
        font-size: 14px;
        border-bottom: 1px solid var(--primary-transparent);
    }
    
    .hide-ad{
        position: absolute;
        top: 15px;
        right: 15px;
        width: 25px;
        background: var(--text-light);
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .hide-ad:hover {
        background: var(--primary-transparent);
    }
    
    .match_score{
        height: max-content;
        width: 100%;
        background: var(--background);
        padding: 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
        flex-direction: column;
        transition: background 0.3s ease;
    }
    
    .menu-line{
        width: 100%;
        height: 48px;
        background: var(--background);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        position: sticky;
        top: 55px;
        z-index: 99;
        transition: background 0.3s ease;
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
        color: var(--primary-color);
        font-weight: 600;
    }

    .menu-line-indicator {
        position: absolute;
        bottom: 0;
        height: 3px;
        width: 25%;
        background: var(--primary-color);
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
        background: var(--background);
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        margin: 5px 0;
        border-radius: var(--radius-md);
        transition: transform 0.2s ease, background 0.3s ease;
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
        transition: color 0.3s ease;
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
        background: var(--light-bg);
        border-radius: 50%;
        display: flex;
        overflow: hidden;
        object-fit: cover;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--primary-transparent);
        transition: background 0.3s ease;
    }
    
    .start-btn {
        width: 80px;
        height: 40px;
        border-radius: 20px;
        border: none;
        color: white;
        font-weight: 600;
        background: var(--primary-color);
        box-shadow: 0 4px 10px rgba(209, 34, 31, 0.2);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }
    
    .start-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(209, 34, 31, 0.3);
        background: var(--primary-dark);
    }
    
    .start-btn:active {
        transform: translateY(0);
    }
    
    table {
        width: 100%;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 14px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: border-color 0.3s ease;
        background-color: var(--nav-fill);
    }

    table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--border-color);
        transition: border-color 0.3s ease;
        text-align: center;
    }

    table .title {
        font-weight: 550;
        background: var(--light-bg);
        width: 110px;
        color: var(--text-light);
        transition: background 0.3s ease, color 0.3s ease;
    }

    table .data {
        text-align: left;
        color: var(--text-dark);
        font-weight: 500;
        transition: color 0.3s ease;
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
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        background: var(--background);
        transition: background 0.3s ease, border-color 0.3s ease;
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
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: var(--background);
        box-shadow: var(--shadow-sm);
        transition: background 0.3s ease, border-color 0.3s ease;
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
        color: var(--text-dark);
        transition: color 0.3s ease;
    }
    
    .inn-info label,
    .inn-no{
        color: var(--text-light);
        font-size: 13px;
        font-weight: 400;
        transition: color 0.3s ease;
    }
    
    .stat{
        background: linear-gradient(90deg, var(--primary-transparent), rgba(209, 34, 31, 0.1));
        width: 100%;
        height: 40px;
        display: flex;
        align-items: center;
        padding: 0px 20px;
        box-sizing: border-box;
        font-weight: 600;
        color: var(--primary-color);
        font-size: 15px;
        transition: color 0.3s ease;
    }
    
    .skew{
        position: absolute;
        height: 100%;
        width: 127%;
        transform: skew(-15deg, 0deg);
        transform-origin: top left;
        background: linear-gradient(90deg, var(--primary-transparent), rgba(209, 34, 31, 0.05));
        z-index: 1;
    }
    
    .weather{
        width: 100%;
        border: 1px solid var(--border-color);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-radius: var(--radius-md);
        overflow: hidden;
        background: var(--background);
        box-shadow: var(--shadow-sm);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    
    .playerz{
        min-width: 30px;
        max-width: 30px;
        text-align : center;
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
        background: var(--light-bg);
        height: 40px;
        display: flex;
        align-items: center;
        padding-left: 15px;
        font-weight: 600;
        color: var(--text-dark);
        font-size: 15px;
        transition: background 0.3s ease, color 0.3s ease;
    }
    
    .comm{
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-radius: var(--radius-md);
        overflow: hidden;
        background: var(--background);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    
    .comm-name{
        height: 48px;
        display: flex;
        align-items: center;
        box-sizing: border-box;
        padding: 0 20px;
        font-size: 16px;
        background: var(--light-bg);
        font-weight: 600;
        color: var(--text-dark);
        transition: background 0.3s ease, color 0.3s ease;
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
        transition: color 0.3s ease;
    }
    
    .run{
        height: 25px;
        width: 52px;
        padding: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--primary-color);
        border-radius: 15%;
        color: var(--primary-color);
        font-weight: 600;
        font-size: 13px;
        flex-shrink: 0;
        transition: border-color 0.3s ease, color 0.3s ease;
    }
    
    .ball {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 14px;
        transition: color 0.3s ease;
    }
    
    .line{
        border: 1px solid var(--border-color);
        margin: 10px 0;
        transition: border-color 0.3s ease;
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
        border: 1px solid var(--border-color);
        background: var(--background);
        box-shadow: var(--shadow-sm);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    
    .team-container{
        width: 100%;
        height: 40px;
        display: flex;
        background: linear-gradient(90deg, var(--primary-transparent), rgba(209, 34, 31, 0.1));
        align-items: center;
        padding: 0 20px;
        box-sizing: border-box;
        font-weight: 600;
        color: var(--text-dark);
        transition: color 0.3s ease;
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
        border-bottom: 1px solid var(--border-color);
        transition: border-color 0.3s ease;
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
        color: var(--text-light);
        transition: color 0.3s ease;
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
        color: var(--text-light);
        padding: 40px;
        border: 1px solid var(--border-color);
        box-sizing: border-box;
        text-align: center;
        background: var(--background);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }
    
    .error-text{
        width: 230px;
        font-size: 14px;
        color: var(--text-light);
        transition: color 0.3s ease;
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
        border: 1px solid var(--border-color);
        background: var(--background);
        box-shadow: var(--shadow-sm);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    
    .table-head{
        background: var(--light-bg);
        font-weight: 600;
        color: var(--text-dark);
        transition: background 0.3s ease, color 0.3s ease;
    }
    
    .playername{
        color: var(--primary-color);
        font-weight: 500;
        transition: color 0.3s ease;
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
        border: 1px solid var(--border-color);
        background: var(--background);
        box-shadow: var(--shadow-sm);
        transition: background 0.3s ease, border-color 0.3s ease;
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
        border: 1px solid var(--border-color);
        overflow: hidden;
        background: var(--background);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: background 0.3s ease, border-color 0.3s ease;
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
        border-bottom: 1px solid var(--border-color);
        transition: color 0.3s ease, border-color 0.3s ease;
    }
    
    .teamimg{
        height: 28px;
        width: 28px;
        background: var(--light-bg);
        border-radius: 50%;
        border: 1px solid var(--primary-transparent);
        overflow: hidden;
        transition: background 0.3s ease;
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
        transition: color 0.3s ease;
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
        color: var(--text-light);
        font-size: 12px;
        font-weight: 400;
        transition: color 0.3s ease;
    }
    
    .squad-border{
        border: 1px solid var(--border-color);
        align-self: stretch;
        transition: border-color 0.3s ease;
    }
    
    .playerimg{
        height: 40px;
        width: 40px;
        background: var(--light-bg);
        border-radius: 50%;
        border: 1px solid var(--primary-transparent);
        flex-shrink: 0;
        overflow: hidden;
        transition: background 0.3s ease;
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
        background: var(--background);
        border-radius: var(--radius-lg);
        flex-direction: column;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        transition: background 0.3s ease;
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
        border-bottom: 1px solid var(--border-color);
        transition: border-color 0.3s ease;
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
        background: var(--light-bg);
        transition: background 0.3s ease;
    }
    
    .feedback-container{
        height: 100px;
        min-width: 300px;
        max-width: 300px;
        resize: none;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
        background: var(--background);
        padding: 12px;
        font-size: 14px;
        outline: none;
        transition: all 0.2s ease;
        color: var(--text-dark);
    }
    
    .feedback-container:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px var(--primary-transparent);
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
        color: var(--text-light);
        font-weight: 500;
        font-size: 14px;
        border-radius: 20px;
        line-height: 16px;
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .fed-btns .cancel:hover {
        background: var(--border-color);
    }
    
    .fed-btns .submit{
        background: var(--primary-color);
        color: white;
    }
    
    .fed-btns .submit:hover {
        background: var(--primary-dark);
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
        border: 1px solid #2E7D32;
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
        color: var(--primary-color);
        width: 100%;
        word-spacing: 2px;
        letter-spacing: 1px;
        transition: color 0.3s ease;
    }
    
    .date-time{
        font-weight: 700;
        max-width: 100px;
        font-size: 15px;
        color: var(--text-dark);
        transition: color 0.3s ease;
    }
    
    #toggle-button {
        padding: 8px 16px;
        background-color: rgba(221, 221, 221, 0.34);
        backdrop-filter: blur(5px);
        color: var(--text-dark);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    #toggle-button:hover {
        background-color: rgba(221, 221, 221, 0.5);
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
        background: var(--background);
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
        transition: color 0.3s ease;
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
        color: var(--text-dark);
    }
    
    .menu-item:last-child:hover{
        background : none;
    }
    
    .menu-item:hover {
        background: var(--primary-transparent);
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
        background: var(--primary-color);
        box-shadow: 0 4px 10px rgba(209, 34, 31, 0.2);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    a{
        text-decoration: none;
        color: var(--text-dark);
        transition: color 0.3s ease;
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
        background: var(--background);
        flex-direction: column;
        transition: all 0.3s ease;
        justify-content: center;
        align-items: flex-start;
        padding: 30px;
        border-radius: 15px;
        box-shadow: var(--shadow-lg);
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
        transition: color 0.3s ease;
    }

    .error {
        display: none;
        color: var(--primary-color);
        width: 100%;
        font-size: 13px;
        margin: 5px 0;
        transition: color 0.3s ease;
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
        background: var(--light-bg);
        color: var(--text-dark);
        font-size: 15px;
        transition: border-color 0.3s ease, background 0.3s ease, color 0.3s ease;
    }
    
    #matchPassword:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-transparent);
    }

    #title{
        font-size: 18px;
        font-weight: 500;
        color: var(--text-dark);
        transition: color 0.3s ease;
    }
    
    .btns>* {
        width: 110px;
        height: 40px;
        border-radius: 25px;
        border: solid 1px var(--primary-color);
        color: var(--primary-color);
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 600;
    }
    
    .btns>*:last-child {
        background: var(--primary-color);
        color: white;
        border: none;
    }
    
    .btns>*:last-child:hover {
        background: var(--primary-dark);
    }
    
    .btns>*:first-child:hover {
        background: var(--primary-transparent);
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
        color: var(--text-dark);
        transition: color 0.3s ease;
    }

    @media(max-width: 600px) {
        .nav-content{
            display: flex;
            justify-content:space-between ;
            align-items: center;
            width: 100%;
        }
        
        .logo-name {
            font-size: 22px;
            color: var(--text-dark);
            text-align: left;
            width: 130px;
            overflow: hidden;
            white-space: nowrap;
            margin-bottom: 0rem;
            text-align: left;
            transition: color 0.3s ease;
        }
        
        .txt-strike{
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .txt-live {
            font-weight: 600;
            color: var(--text-dark);
            transition: color 0.3s ease;
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
        .txt-strike{
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .txt-live {
            font-weight: 600;
            color: var(--text-dark);
            transition: color 0.3s ease;
        }
        
        .logo-name {
            font-size: 24px;
            color: var(--text-dark);
            text-align: left;
            width: 140px;
            font-weight: 400;
            overflow: hidden;
            white-space: nowrap;
            margin-bottom: 0;
            transition: color 0.3s ease;
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
        color: var(--text-dark);
        transition: color 0.3s ease;
    }
    .logo-img {
        height: 90px;
        width: 100px;
    }
    .logo-img img{
            height: 100%;
            width: 100%;
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
                <div class="">Feedback</div>
                <!-- <div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div> -->
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
<<<<<<< HEAD
                    <!-- <sup class="trade-mark">TM</sup></div> -->
=======
                    <!-- <div class="l-name"><div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
                    <sup class="trade-mark">TM</sup></div> -->

>>>>>>> origin/master
                </div>
            </a>
            
            <div class="items">
                <div id='commentaryIcon'>
<<<<<<< HEAD
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.0625 4.24502C14.0625 3.15283 12.7016 2.65439 11.9961 3.4872L8.80391 7.26064C8.65721 7.43375 8.47457 7.57283 8.26869 7.66822C8.06281 7.7636 7.83862 7.81301 7.61172 7.81299H4.6875C3.8587 7.81299 3.06384 8.14223 2.47779 8.72828C1.89174 9.31433 1.5625 10.1092 1.5625 10.938V14.063C1.5625 14.8918 1.89174 15.6866 2.47779 16.2727C3.06384 16.8587 3.8587 17.188 4.6875 17.188H7.61172C7.83868 17.1881 8.06291 17.2376 8.26879 17.3331C8.47468 17.4286 8.65729 17.5679 8.80391 17.7411L11.9961 21.5138C12.7008 22.3466 14.0625 21.8481 14.0625 20.756V4.24502ZM16.7445 7.16924C16.8291 7.11108 16.9243 7.07016 17.0247 7.04881C17.1251 7.02747 17.2287 7.02612 17.3297 7.04484C17.4306 7.06355 17.5268 7.10198 17.6129 7.15791C17.699 7.21384 17.7732 7.28618 17.8312 7.3708C18.9758 9.03486 19.5797 10.7489 19.5797 12.5005C19.5797 14.252 18.9758 15.9661 17.8312 17.631C17.7736 17.7166 17.6996 17.79 17.6134 17.8469C17.5272 17.9038 17.4306 17.943 17.3292 17.9623C17.2277 17.9816 17.1235 17.9807 17.0224 17.9594C16.9214 17.9382 16.8255 17.8971 16.7404 17.8387C16.6553 17.7802 16.5827 17.7054 16.5267 17.6186C16.4707 17.5319 16.4324 17.4349 16.4141 17.3332C16.3958 17.2316 16.3979 17.1274 16.4201 17.0265C16.4424 16.9257 16.4844 16.8303 16.5437 16.7458C17.5477 15.2849 18.018 13.8739 18.018 12.5005C18.018 11.127 17.5477 9.71611 16.5437 8.25595C16.4265 8.0852 16.3819 7.87489 16.4197 7.67126C16.4575 7.46763 16.5746 7.28734 16.7453 7.17002M20.0711 4.12314C19.9968 4.05235 19.9093 3.99688 19.8136 3.95991C19.7179 3.92293 19.6158 3.90517 19.5132 3.90764C19.4107 3.9101 19.3096 3.93275 19.2157 3.97428C19.1219 4.01582 19.0372 4.07543 18.9664 4.1497C18.8956 4.22398 18.8401 4.31148 18.8032 4.40719C18.7662 4.50291 18.7484 4.60497 18.7509 4.70755C18.7534 4.81013 18.776 4.91122 18.8175 5.00505C18.8591 5.09887 18.9187 5.1836 18.993 5.25439C20.8578 7.03017 21.8273 9.73799 21.8273 12.5013C21.8273 15.2645 20.8578 17.9724 18.993 19.7481C18.9187 19.8189 18.8591 19.9037 18.8175 19.9975C18.776 20.0913 18.7534 20.1924 18.7509 20.295C18.7484 20.3976 18.7662 20.4996 18.8032 20.5953C18.8401 20.6911 18.8956 20.7785 18.9664 20.8528C19.0372 20.9271 19.1219 20.9867 19.2157 21.0282C19.3096 21.0698 19.4107 21.0924 19.5132 21.0949C19.6158 21.0974 19.7179 21.0796 19.8136 21.0426C19.9093 21.0056 19.9968 20.9502 20.0711 20.8794C22.3078 18.7489 23.3891 15.5974 23.3891 12.5013C23.3891 9.40517 22.3078 6.25283 20.0703 4.12236" fill="black"/>
                    </svg>

=======
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.0625 4.24502C14.0625 3.15283 12.7016 2.65439 11.9961 3.4872L8.80391 7.26064C8.65721 7.43375 8.47457 7.57283 8.26869 7.66822C8.06281 7.7636 7.83862 7.81301 7.61172 7.81299H4.6875C3.8587 7.81299 3.06384 8.14223 2.47779 8.72828C1.89174 9.31433 1.5625 10.1092 1.5625 10.938V14.063C1.5625 14.8918 1.89174 15.6866 2.47779 16.2727C3.06384 16.8587 3.8587 17.188 4.6875 17.188H7.61172C7.83868 17.1881 8.06291 17.2376 8.26879 17.3331C8.47468 17.4286 8.65729 17.5679 8.80391 17.7411L11.9961 21.5138C12.7008 22.3466 14.0625 21.8481 14.0625 20.756V4.24502ZM16.7445 7.16924C16.8291 7.11108 16.9243 7.07016 17.0247 7.04881C17.1251 7.02747 17.2287 7.02612 17.3297 7.04484C17.4306 7.06355 17.5268 7.10198 17.6129 7.15791C17.699 7.21384 17.7732 7.28618 17.8312 7.3708C18.9758 9.03486 19.5797 10.7489 19.5797 12.5005C19.5797 14.252 18.9758 15.9661 17.8312 17.631C17.7736 17.7166 17.6996 17.79 17.6134 17.8469C17.5272 17.9038 17.4306 17.943 17.3292 17.9623C17.2277 17.9816 17.1235 17.9807 17.0224 17.9594C16.9214 17.9382 16.8255 17.8971 16.7404 17.8387C16.6553 17.7802 16.5827 17.7054 16.5267 17.6186C16.4707 17.5319 16.4324 17.4349 16.4141 17.3332C16.3958 17.2316 16.3979 17.1274 16.4201 17.0265C16.4424 16.9257 16.4844 16.8303 16.5437 16.7458C17.5477 15.2849 18.018 13.8739 18.018 12.5005C18.018 11.127 17.5477 9.71611 16.5437 8.25595C16.4265 8.0852 16.3819 7.87489 16.4197 7.67126C16.4575 7.46763 16.5746 7.28734 16.7453 7.17002M20.0711 4.12314C19.9968 4.05235 19.9093 3.99688 19.8136 3.95991C19.7179 3.92293 19.6158 3.90517 19.5132 3.90764C19.4107 3.9101 19.3096 3.93275 19.2157 3.97428C19.1219 4.01582 19.0372 4.07543 18.9664 4.1497C18.8956 4.22398 18.8401 4.31148 18.8032 4.40719C18.7662 4.50291 18.7484 4.60497 18.7509 4.70755C18.7534 4.81013 18.776 4.91122 18.8175 5.00505C18.8591 5.09887 18.9187 5.1836 18.993 5.25439C20.8578 7.03017 21.8273 9.73799 21.8273 12.5013C21.8273 15.2645 20.8578 17.9724 18.993 19.7481C18.9187 19.8189 18.8591 19.9037 18.8175 19.9975C18.776 20.0913 18.7534 20.1924 18.7509 20.295C18.7484 20.3976 18.7662 20.4996 18.8032 20.5953C18.8401 20.6911 18.8956 20.7785 18.9664 20.8528C19.0372 20.9271 19.1219 20.9867 19.2157 21.0282C19.3096 21.0698 19.4107 21.0924 19.5132 21.0949C19.6158 21.0974 19.7179 21.0796 19.8136 21.0426C19.9093 21.0056 19.9968 20.9502 20.0711 20.8794C22.3078 18.7489 23.3891 15.5974 23.3891 12.5013C23.3891 9.40517 22.3078 6.25283 20.0703 4.12236" fill="black"/></svg>
>>>>>>> origin/master
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

                            $astr = (!empty($current_serve_team) &&  $current_serve_team == $team1['t_id']) ? '*' : '';

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

                            $astr = (!empty($current_serve_team) &&  $current_serve_team == $team2['t_id']) ? '*' : '';

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
                    <p id='run_rate'></p>
                    <p>Views : <?php echo $score_log['page_views']; ?></p>
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

                        // $scorer_emails = isset($scorers[0]) ? explode(",", $scorers[0]) : [];
                        $session_email = $_SESSION['email'] ?? '';

                        if ($scorers && in_array($session_email, $scorers) && $row['status'] == 'Live') {
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

    <div class="ad2">Advertisement (412px x 60px)
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
                    }
                    
                ?>

                <div class="comm">
                    <?php
                        $all_serves = [];

                        // 1. Merge balls from 1st and 2nd innings
                        $total_sets = $score_log['total_sets'] ?? '';
                        for ($inning_key = 1; $inning_key <= $total_sets; $inning_key++) {
                            if (
                                isset($score_log['sets'][$inning_key]['rallies']) &&
                                is_array($score_log['sets'][$inning_key]['rallies'])
                            ) {
                                foreach ($score_log['sets'][$inning_key]['rallies'] as $rally) {
                                    $rally['inning'] = $inning_key; // Add the inning key to each rally
                                    $all_serves[] = $rally;
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
                        // $all_serves = array_reverse($all_serves);
                    ?>
                    <div class="comm-name">Commentary</div>
                    <div class="comm-data">
                        <hr class="line">
                        <?php foreach ($all_serves as $serves): ?>
                            <?php
                                $winner = $serves['winner_team'] ?? '';
                                $last_score = $serves['last score'] ?? '';
                                $action = $serves['action'] ?? '';
                                $team1_points = $serves['team1_points'] ?? '';
                                $team2_points = $serves['team2_points'] ?? '';
                                $curr_serve = $serves['Curr_serve'] ?? '';
                                $serve_player = $serves['serve_player'] ?? '';
                                $inning = $serves['inning'] ?? '';
                                $comment = '';

                                // Get Curr Serve team name
                                $curr_serve_name = '';
                                if (!empty($curr_serve)) {
                                    $curr_serve_id = trim($curr_serve);
                                    $curr_serve_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id = '$curr_serve_id' LIMIT 1"));
                                    $curr_serve_name = $curr_serve_row['t_name'] ?? "";
                                }

                                // Get Serve Player Name
                                $serve_player_name = '';
                                if (!empty($serve_player)) {
                                    $serve_player_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT player_name FROM players WHERE user_id = '$serve_player' LIMIT 1"));
                                    $serve_player_name = $serve_player_row['player_name'] ?? "";
                                }

                                // Get Winner Team Name
                                $winner_name = '';
                                if (!empty($winner)) {
                                    $winner_id = $winner;
                                    $winner_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id = '$winner_id' LIMIT 1"));
                                    $winner_name = $winner_row['t_name'] ?? "";
                                }

                                // Commentary generation
                                if (!empty($serve_player_name)) {
                                    $comment = "$serve_player_name serves the ball. Point awarded to $winner_name.";
                                } else if (!empty($action)) {
                                    $action = strtolower(trim($action));
                                    switch ($action) {
                                        case 'ace':
                                            $comment = "ACE! $serve_player_name delivers a powerful serve. Point to $winner_name.";
                                            break;
                                        case 'service error':
                                            $comment = "Service Error by $serve_player_name. Point to $winner_name.";
                                            break;
                                        default:
                                            $comment = ucfirst($action) . " by $serve_player_name. Point to $winner_name.";
                                            break;
                                    }
                                } else {
                                    $comment = "$curr_serve_name serves. Point awarded to $winner_name.";
                                    if ($team1_points !== '' && $team2_points !== '') {
                                        $comment .= " <i>Current Score: $team1_points - $team2_points</i>";
                                    }
                                }
                            ?>
                            <div class="ball-data">
                                <div class="ball-runs">
                                    <div class="ball"><?php echo $inning; ?></div>
                                    <div class="run"><?php echo "$team1_points - $team2_points"; ?></div>
                                </div>
                                <div class="commentry">
                                    <?php echo $comment; ?>
                                </div>
                            </div>
                            <hr class="line">
                        <?php endforeach; ?>

                        <!-- Optional "Show More" Button -->
                        <button id="toggle-button" style="margin-top: 10px;justify-content: center;align-items: center;">Show More</button>
                    </div>
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
                            // Assume $data contains your decoded JSON
                            $total_sets = isset($score_log['total_sets']) ? (int)$score_log['total_sets'] : 0;
                            $sets = $score_log['sets'] ?? [];
                            $sets_won = $score_log['sets_won'] ?? [];

                            if(!empty($sets)){
                            
                            // Build table header
                            echo '<table class="table-score">';
                            echo '<thead>';
                            echo '<tr class="table-head">';
                            echo '<th>Teams</th>';
                            echo '<th>F</th>';

                            // Show set numbers in reverse order (3 to 1)
                            for ($i = $total_sets; $i >= 1; $i--) {
                                echo "<th>$i</th>";
                            }

                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            // Team 1 row
                            echo '<tr>';
                            echo "<td>$team1_name</td>";
                            echo "<td>{$sets_won['team1']}</td>";

                            for ($i = $total_sets; $i >= 1; $i--) {
                                $score = isset($sets[$i]['team1_points']) ? $sets[$i]['team1_points'] : '';
                                echo "<td>$score</td>";
                            }
                            echo '</tr>';

                            // Team 2 row
                            echo '<tr>';
                            echo "<td>$team2_name</td>";
                            echo "<td>{$sets_won['team2']}</td>";

                            for ($i = $total_sets; $i >= 1; $i--) {
                                $score = isset($sets[$i]['team2_points']) ? $sets[$i]['team2_points'] : '';
                                echo "<td>$score</td>";
                            }
                            echo '</tr>';

                            echo '</tbody>';
                            echo '</table>'; 
                            }

                            // Optional: team name lookup if needed

                        ?>
                    </div>

                </section>
                
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

    <script>
    const menuItems = document.querySelectorAll('.menu-items');
    const indicator = document.querySelector('.menu-line-indicator');
    const close_fed_container = document.querySelector('.exit');
    const feedback = document.querySelectorAll('.feedback-cta-holder');
    const close_ad = document.querySelector('.hide-ad');
    const ad_container = document.querySelector('.ad');
    const feedback_page = document.querySelector('.cmc-report-issue-button');
    const matchID = <?php echo json_encode($match_id); ?>;
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
                this.innerHTML = '<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.0625 4.24502C14.0625 3.15283 12.7016 2.65439 11.9961 3.4872L8.80391 7.26064C8.65721 7.43375 8.47457 7.57283 8.26869 7.66822C8.06281 7.7636 7.83862 7.81301 7.61172 7.81299H4.6875C3.8587 7.81299 3.06384 8.14223 2.47779 8.72828C1.89174 9.31433 1.5625 10.1092 1.5625 10.938V14.063C1.5625 14.8918 1.89174 15.6866 2.47779 16.2727C3.06384 16.8587 3.8587 17.188 4.6875 17.188H7.61172C7.83868 17.1881 8.06291 17.2376 8.26879 17.3331C8.47468 17.4286 8.65729 17.5679 8.80391 17.7411L11.9961 21.5138C12.7008 22.3466 14.0625 21.8481 14.0625 20.756V4.24502ZM16.7445 7.16924C16.8291 7.11108 16.9243 7.07016 17.0247 7.04881C17.1251 7.02747 17.2287 7.02612 17.3297 7.04484C17.4306 7.06355 17.5268 7.10198 17.6129 7.15791C17.699 7.21384 17.7732 7.28618 17.8312 7.3708C18.9758 9.03486 19.5797 10.7489 19.5797 12.5005C19.5797 14.252 18.9758 15.9661 17.8312 17.631C17.7736 17.7166 17.6996 17.79 17.6134 17.8469C17.5272 17.9038 17.4306 17.943 17.3292 17.9623C17.2277 17.9816 17.1235 17.9807 17.0224 17.9594C16.9214 17.9382 16.8255 17.8971 16.7404 17.8387C16.6553 17.7802 16.5827 17.7054 16.5267 17.6186C16.4707 17.5319 16.4324 17.4349 16.4141 17.3332C16.3958 17.2316 16.3979 17.1274 16.4201 17.0265C16.4424 16.9257 16.4844 16.8303 16.5437 16.7458C17.5477 15.2849 18.018 13.8739 18.018 12.5005C18.018 11.127 17.5477 9.71611 16.5437 8.25595C16.4265 8.0852 16.3819 7.87489 16.4197 7.67126C16.4575 7.46763 16.5746 7.28734 16.7453 7.17002M20.0711 4.12314C19.9968 4.05235 19.9093 3.99688 19.8136 3.95991C19.7179 3.92293 19.6158 3.90517 19.5132 3.90764C19.4107 3.9101 19.3096 3.93275 19.2157 3.97428C19.1219 4.01582 19.0372 4.07543 18.9664 4.1497C18.8956 4.22398 18.8401 4.31148 18.8032 4.40719C18.7662 4.50291 18.7484 4.60497 18.7509 4.70755C18.7534 4.81013 18.776 4.91122 18.8175 5.00505C18.8591 5.09887 18.9187 5.1836 18.993 5.25439C20.8578 7.03017 21.8273 9.73799 21.8273 12.5013C21.8273 15.2645 20.8578 17.9724 18.993 19.7481C18.9187 19.8189 18.8591 19.9037 18.8175 19.9975C18.776 20.0913 18.7534 20.1924 18.7509 20.295C18.7484 20.3976 18.7662 20.4996 18.8032 20.5953C18.8401 20.6911 18.8956 20.7785 18.9664 20.8528C19.0372 20.9271 19.1219 20.9867 19.2157 21.0282C19.3096 21.0698 19.4107 21.0924 19.5132 21.0949C19.6158 21.0974 19.7179 21.0796 19.8136 21.0426C19.9093 21.0056 19.9968 20.9502 20.0711 20.8794C22.3078 18.7489 23.3891 15.5974 23.3891 12.5013C23.3891 9.40517 22.3078 6.25283 20.0703 4.12236" fill="black"/></svg>';
            } else {
                console.log("Commentary disabled",commentaryEnabled);
                stopCommentary();
                //  change icon color to muted
                this.innerHTML = '<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.5036 4.41266C14.5036 3.51089 13.4149 3.05919 12.7776 3.69707L8.98097 7.49682C8.8307 7.64726 8.65227 7.76659 8.4559 7.84799C8.25952 7.92938 8.04904 7.97124 7.83649 7.97118H4.79777C3.93972 7.97118 3.11682 8.31232 2.51009 8.91956C1.90336 9.52679 1.5625 10.3504 1.5625 11.2091V14.4471C1.5625 15.3059 1.90336 16.1295 2.51009 16.7367C3.11682 17.3439 3.93972 17.6851 4.79777 17.6851H7.83649C8.04904 17.685 8.25952 17.7269 8.4559 17.8083C8.65227 17.8897 8.8307 18.009 8.98097 18.1594L12.7776 21.9592C13.4149 22.5971 14.5036 22.1454 14.5036 21.2436V4.41266ZM17.167 9.82734C17.3187 9.67559 17.5244 9.59034 17.7388 9.59034C17.9533 9.59034 18.159 9.67559 18.3107 9.82734L20.1653 11.6835L22.0199 9.82734C22.1725 9.67989 22.3768 9.5983 22.5888 9.60014C22.8009 9.60199 23.0038 9.68712 23.1537 9.8372C23.3037 9.98729 23.3887 10.1903 23.3906 10.4026C23.3924 10.6148 23.3109 10.8193 23.1636 10.972L21.309 12.8281L23.1636 14.6843C23.3109 14.837 23.3924 15.0414 23.3906 15.2537C23.3887 15.4659 23.3037 15.669 23.1537 15.819C23.0038 15.9691 22.8009 16.0543 22.5888 16.0561C22.3768 16.058 22.1725 15.9764 22.0199 15.8289L20.1653 13.9727L18.3107 15.8289C18.1581 15.9764 17.9538 16.058 17.7417 16.0561C17.5297 16.0543 17.3268 15.9691 17.1769 15.819C17.0269 15.669 16.9418 15.4659 16.94 15.2537C16.9381 15.0414 17.0197 14.837 17.167 14.6843L19.0216 12.8281L17.167 10.972C17.0154 10.8202 16.9302 10.6143 16.9302 10.3997C16.9302 10.185 17.0154 9.97915 17.167 9.82734Z" fill="black"/></svg>';
            }
        });

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

    let lastDataString = '';
    let showAllBalls = false;
    //AJAX
    function fetchScoreboard() {
        fetch(`../../API/VOLLEYBALL_api.php?match_id=${matchID}`)
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

function update_commentry(data){
    // console.log(data);
    let allServes = [];
    const container = document.querySelector('.comm-data');
    let toggleButton = document.getElementById('toggle-button');
    
    // Clear existing content
    if(container){
        container.innerHTML = '';
    }

    if (!data || Object.keys(data).length === 0) {
        container.innerHTML = '<p>No commentary available yet</p>';
        toggleButton.style.display = 'none';
        return;
    }

    const totalSets = parseInt(data.total_sets) || 0;

    // Merge all rallies with inning info
    for (let inningKey = 1; inningKey <= totalSets; inningKey++) {
        const set = data.sets?.[inningKey];
        if (set?.rallies && Array.isArray(set.rallies)) {
            set.rallies.forEach(rally => {
                allServes.push({ ...rally, inning: inningKey });
            });
        }
    }

    const teamMap = data.team_map || {};
    const playerMap = data.player_map || {};

    allServes =  allServes.reverse();

    // Create document fragment for better performance
    const fragment = document.createDocumentFragment();

    allServes.forEach((serves, index) => {
        const winner = serves.winner_team || '';
        const action = serves.action || '';
        const team1Points = serves.team1_points ?? '';
        const team2Points = serves.team2_points ?? '';
        const currServe = serves.Curr_serve || '';
        const servePlayer = serves.serve_player || '';
        const inning = serves.inning || '';

        let comment = '';

        const currServeName = teamMap[currServe] || '';
        const servePlayerName = playerMap[servePlayer] || '';
        const winnerName = teamMap[winner] || '';

        if (servePlayerName) {
            comment = `${servePlayerName} serves the ball. Point awarded to ${winnerName}.`;
        } else if (action) {
            const lowerAction = action.toLowerCase().trim();
            switch (lowerAction) {
                case 'ace':
                    comment = `ACE! ${servePlayerName} delivers a powerful serve. Point to ${winnerName}.`;
                    break;
                case 'service error':
                    comment = `Service Error by ${servePlayerName}. Point to ${winnerName}.`;
                    break;
                default:
                    comment = `${action.charAt(0).toUpperCase() + action.slice(1)} by ${servePlayerName}. Point to ${winnerName}.`;
                    break;
            }
        } else {
            comment = `${currServeName} serves. Point awarded to ${winnerName}.`;
            if (team1Points !== '' && team2Points !== '') {
                comment += ` <i>Current Score: ${team1Points} - ${team2Points}</i>`;
            }
        }

        if (index === 0 && commentaryEnabled && !data.winner) {
            let cleanText = comment.replace(/<[^>]*>/g, '').replace(/(\d+)\s*-\s*(\d+)/g, '$1 to $2').replace(/_/g, ' ').replace(/\s+/g, ' ').trim();
            speakText(cleanText);
        }
            
         // Create ball element
        const ballDiv = document.createElement('div');
        ballDiv.className = 'ball-data';
        ballDiv.style.display = (index >= 7 && !showAllBalls) ? 'none' : 'flex';
        ballDiv.innerHTML = `
                                <div class="ball-runs">
                                    <div class="ball">${inning}</div>
                                    <div class="run">${team1Points} - ${team2Points}</div>
                                </div>
                                <div class="commentry">
                                    ${comment}
                                </div>`;

        fragment.appendChild(ballDiv);
        
        // Create line element
        const line = document.createElement('hr');
        line.className = 'line';
        line.style.display = (index >= 7 && !showAllBalls) ? 'none' : 'block';
        fragment.appendChild(line);
        
    })
        
        container.appendChild(fragment);
        
        container.appendChild(toggleButton);
        initShowMoreButton();
}

function update_scoreboard(data){
    console.log(data);
    const container = document.querySelector('.bowl-data');
    if (!data || !container) return;

    const score_log = data;
    const team1_id = data.team1;
    const team2_id = data.team2;
    const team1_name = data.team_map?.[team1_id] || "Team 1";
    const team2_name = data.team_map?.[team2_id] || "Team 2";

    const total_sets = parseInt(score_log.total_sets) || 0;
    const sets = score_log.sets || {};
    const sets_won = score_log.sets_won || {};

    if (Object.keys(sets).length === 0) return;

    // Start building the table HTML
    let html = `<table class="table-score">
                    <thead>
                        <tr class="table-head">
                            <th>Teams</th>
                            <th>F</th>`;

    // Set headers from total_sets down to 1
    for (let i = total_sets; i >= 1; i--) {
        html += `<th>${i}</th>`;
    }

    html += `</tr>
            </thead>
            <tbody>
                <tr>
                    <td>${team1_name}</td>
                    <td>${sets_won.team1 ?? ''}</td>`;

    // Team 1 scores
    for (let i = total_sets; i >= 1; i--) {
        let score = sets[i]?.team1_points ?? '';
        html += `<td>${score}</td>`;
    }

    html += `</tr>
            <tr>
                <td>${team2_name}</td>
                <td>${sets_won.team2 ?? ''}</td>`;

    // Team 2 scores
    for (let i = total_sets; i >= 1; i--) {
        let score = sets[i]?.team2_points ?? '';
        html += `<td>${score}</td>`;
    }

    html += `</tr>
            </tbody>
        </table>`;

    container.innerHTML = html;

    const matchInfoContainer = document.querySelector('.match-data');
    if (matchInfoContainer) {
        const currentSet = data.sets[data.current_set];
        const curr_serve = data.current_serve;
        matchInfoContainer.querySelectorAll('.score')[0].innerHTML = `${currentSet.team1_points} ( ${data.sets_won.team1} )`;
        matchInfoContainer.querySelectorAll('.score')[1].innerHTML = `${currentSet.team2_points} ( ${data.sets_won.team2} )`;

        let team_key = curr_serve == data.team1 ? 0 : 1;
        let other_team_key = team_key === 0 ? 1 : 0;

        let team_name_key = curr_serve == data.team1 ? team1_name : team2_name;
        let other_team_name_key = team_key === 0 ? team2_name : team1_name;

        let teams = matchInfoContainer.querySelectorAll('.team');

        // Update current server team
        let teamDiv = teams[team_key];
        if (teamDiv) {
            for (let node of teamDiv.childNodes) {
                if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '') {
                    node.nodeValue = `${team_name_key} *`;
                    break;
                }
            }
        }

        // Remove * from the other team
        let otherTeamDiv = teams[other_team_key];
        if (otherTeamDiv) {
            for (let node of otherTeamDiv.childNodes) {
                if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '') {
                    node.nodeValue = other_team_name_key;
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
            for (let i = 10; i < allElements.length; i++) { // Start from index 10 (6th ball + line)
                allElements[i].style.display = showAllBalls ? 'flex' : 'none';
            }
            
            toggleButton.textContent = showAllBalls ? 'Show Less' : 'Show More';
        };
    }

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

        // Disable right-click
  document.addEventListener('contextmenu', event => event.preventDefault());

  // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
  document.onkeydown = function(e) {
    if(e.keyCode == 123) return false; // F12
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
    if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
  }
    // Make function globally accessible
  window.openDialog = function(button, event) {
      if (event) event.stopPropagation();
      const dialog = document.getElementById("startMatchDialog");
      dialog.showModal();

      const match_to_start = button.closest('.game-info').getAttribute('data-match_id');
      console.log("Match : " + match_to_start);

      document.getElementById("match_id").value = match_to_start;
  }

  // Close dialog of password
        window.closeDialog = function() {
            const dialog = document.getElementById("startMatchDialog");
            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });
            document.getElementById("matchPasswordForm").reset();
            dialog.close();
        }

         window.shareContent = function() {
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


       function initializeTheme() {
    document.body.classList.add('no-theme-transition');

    const checkLogo = setInterval(() => {
        
        if (logo) {
            clearInterval(checkLogo);

            const currentTheme = localStorage.getItem('theme') ||
                                 (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            setTheme(currentTheme, false);
            

            // Remove transition blocker after short delay
            setTimeout(() => document.body.classList.remove('no-theme-transition'), 100);

            // Sync across tabs
            window.addEventListener('storage', e => {
                if (e.key === 'theme') setTheme(e.newValue, false);
            });

            // Listen for manual theme change
            window.addEventListener('themeChanged', e => setTheme(e.detail, false));
        }
    }, 50);
};
    </script>
</body>
</html>