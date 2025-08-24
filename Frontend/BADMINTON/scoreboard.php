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
        --nav-fill: #2d2d2d;
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
        z-index: -1;
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


    <div class="popup-container">
        <div id="team-feedback" open>
            <div class="fed-head"><span class="logo"><div class="items">
                <div class="logo-img"><img src="https://i.ibb.co/gLY2MgSd/logo.png" alt=""></div>
            </div></span><span class="exit"><img src="https://staticg.sportskeeda.com/skm/assets/close.png" alt=""></span></div>
            <form class="fed-body">
                <textarea name="" id="" class="feedback-container"></textarea>
                <div class="fed-btns">
                    <button class="cancel">Cancel</button>
                    <button class="submit" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <nav class="nav-bar">
        <div class="nav-content">
            <div class="items">
                <div class="logo-img"><img src="https://i.ibb.co/gLY2MgSd/logo.png" alt=""></div>
            </div>
            <div class="items">
                <a href="" class="menu-bar"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAGZJREFUSEvtlrENACAMw8pnnMZpfAYTC1W3CDOEA2JhUpUW0GkQNwx+Zt6qj+ohdp7yKtVLDE6c78DiC+c4t/o46WLX8877rlzYOGGqxU/scYryB4KVCwNja9GtlhvwWpQrrQIx1Rt3TwofeC3yFwAAAABJRU5ErkJggg=="/></a>
            </div>
        </div>
    </nav>    

    <div class="ad">
        <div class="hide-ad">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"><path d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z" fill="black" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        </div>
    </div>

    <div class="match_score">
        <div class="game-info" data-match_id="MATCH_ID_HERE">
            <div class="match-data">
                <div class="info">
                    <p>Match 1 | No Tournament</p>
                </div>

                <div class="info team-score">
                    <div class="team">
                        <img src="" onerror="this.style.opacity='0'">
                        Team 1 Name
                    </div>
                    <div class="score">100/2 (10.0)</div>
                </div>

                <div class="info team-score">
                    <div class="team">
                        <img src="" alt="Team 2 Name" onerror="this.style.opacity='0'">
                        Team 2 Name
                    </div>
                    <div class="score">98/3 (9.2)</div>
                </div>

                <div class="info">
                    <p>14 Jul 2025 | 3:00 PM</p>
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
                    <button class="start-btn" onclick="openDialog(this)">Start</button>
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

    <div class="ad2">
    </div>

    <div class="swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="title">Date & Time</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Venue</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Match</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Umpires</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Scorers</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Referee</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Toss</td>
                            <td class="data"></td>
                        </tr>
                    </tbody>
                </table>
                <?php
                    $apiKey = "76604801ccb3576d81ddd1bca09b978a";
    $location = "Nashik";
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

        if ($weather_data['cod'] == 200) {
            echo "Temperature at {$location}: " . $weather_data['main']['temp'] . "°C";
        } else {
            echo "Weather data unavailable. " . $weather_data['message'];
        }
    } else {
        echo "Could not find location.";
    }

                ?>

                <div class="weather">
                    <div class="weather-info weather-title">Weather Update</div>
                    <div class="weather-data">
                        <div class="weather-info weather-dt">Harare,Zimbabwe <label for="">Updated: 16 Jul 00:00</label></div>
                        <div class="weather-info weather-dt">15.8°C .<label for="">Clouds</label></div>
                        <div class="weather-info weather-dt">70% <label for="">Clouds</label></div>
                        <div class="weather-info weather-dt">3.8 kmph <label for="">Wind</label></div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">

                <div class="match-not-start">
                    <div class="error-img"><img src="https://staticg.sportskeeda.com/cricket_images/cmc-pre-live-icon.svg" alt=""></div>
                    <span class="error-text">This match has not started yet, stay tuned for live updates!</span>
                </div>

                <div class="comm">
                    <div class="comm-name">Commentary</div>
                    <div class="comm-data">
                        <hr class="line">

                        <div class="ball-data">
                            <div class="ball-runs">
                                <div class="ball">2</div>
                                <div class="run">1</div>
                            </div>
                            <div class="commentry">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur quas, optio, magnam quo nobis dolorum labore minima laborum odio veritatis explicabo maxime similique, provident accusamus asperiores placeat eveniet? Saepe, corrupti.
                            </div>
                        </div>

                        <hr class="line">
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

                <div class="match-not-start">
                    <div class="error-img2"></div>
                    <span class="error-text">Match has not started yet</span>
                </div>

                <section id="team1">
                    <div class="bowl-data">
                        <table class="table-score">
                            <thead>
                                <tr class="table-head">
                                    <th>Teams</th>
                                    <th>F</th>
                                    <th>3</th>
                                    <th>2</th>
                                    <th>1</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Team1</td>
                                    <td>1</td>
                                    <td>13</td>
                                    <td>15</td>
                                    <td>10</td>
                                </tr>
                                <tr>
                                    <td>Team2</td>
                                    <td>2</td>
                                    <td>15</td>
                                    <td>10</td>
                                    <td>15</td>
                                </tr>
                            </tbody>
                        </table>
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
                                    <img src="" alt="">
                            </div>
                            Team 1
                        </div>
                        <span class="vs">vs</span>
                        <div class="t2">
                            <div class="teamimg">
                                    <img src="" alt="">
                            </div>
                            Team 2
                        </div>
                    </div>

                    <div class="teams">
                        <div class="team1">
                            <div class="squad-player">
                                <div class="playername">Richa Ghosh <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Smriti Mandhana <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Harleen Deol <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Deepti Sharma <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Harleen Deol <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="squad-border"></div>

                        <div class="team2">
                            <div class="squad-player">
                                <div class="playername">Richa Ghosh <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Smriti Mandhana <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Harleen Deol <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Deepti Sharma <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>

                            <div class="squad-player">
                                <div class="playername">Harleen Deol <span class="role">all</span>  </div>
                                <div class="playerimg">
                                    <img src="" alt="">
                                </div>
                            </div>
                        </div>
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
    </div>

    <script>
    const menuItems = document.querySelectorAll('.menu-items');
    const indicator = document.querySelector('.menu-line-indicator');
    const close_fed_container = document.querySelector('.exit');
    const feedback = document.querySelectorAll('.feedback-cta-holder');
    const feedback_page = document.querySelector('.cmc-report-issue-button');

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
    </script>
</body>
</html>