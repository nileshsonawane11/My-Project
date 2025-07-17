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
        }
        body{
            background-color: #ffffffff;
        }
        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            --card-bg: #ffffff;
            --text-dark: #333333;
            --text-light: #666666;
            --border-color: #e0e0e0;
        }
        .nav-bar{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            /* box-shadow: 0px -5px 20px black; */
            top: 0;
            width: 100%;
            background-color: white;
            z-index: 999;
        }
        .nav-content{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 6px 20px;
        }
        .items,.list{
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 5px;
            }
        html, body {
            height: 100%;
            margin: 0;
        }
        .swiper {
            width: 100%;
        }
        .swiper-slide {
            box-sizing: border-box;
            width: 100%;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: column;
            background: #ffffff;
            gap: 20px;
        }
        .ad{
            height: 150px;
            width: 100%;
            position: relative;
            background : #f0f0f0;
        }
        .ad2{
            height: 60px;
            width: 100%;
            background : #f0f0f0;
        }
        .hide-ad{
            position: absolute;
            top: 15px;
            right: 15px;
            width: 25px;
            background: #d9d9d9;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
        }
        .match_score{
            height: max-content;
            width: 100%;
            background: white;
            /* padding: 15px; */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-line{
            width: 100%;
            height: 40px;
            background: #ffffff;
            box-shadow: 0 0px 2px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu {
            display: flex;
            position: relative;
            border-bottom: 1px solid #ddd;
        }

        .menu-items {
            cursor: pointer;
            height: 40px;
            width: 75px;
            align-items: center;
            justify-content: center;
            display: flex;
            transition: color 0.3s ease;
        }

        .menu-items.active {
        color: #ff6800;
        }

        .menu-line-indicator {
            position: absolute;
            bottom: 0;
            height: 2px;
            width: 75px;
            background: #ff6800;
            transition: left 0.3s ease;
            left: 0;
        }

        .menu{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-around;
            width: 100%;
        }
        .game-info {
            height: 150px;
            padding: 10px;
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
            margin: 10px 0;
        }
        .match-data {
            display: flex;
            gap: 5px;
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
        }
        .team-score {
            font-size: 18px;
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
            height: 40px;
            width: 40px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            overflow: hidden;
            object-fit: cover;
            align-items: center;
            justify-content: center;
        }
        .start-btn {
            width: 68px;
            height: 38px;
            border-radius: 18px;
            border: none;
            color: white;
            font-weight: 600;
            background: var(--background);
            box-shadow: 0 4px 10px rgba(248, 57, 0, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
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
            border: solid 1px #f8f8f8;
            border-radius: 20px;
            font-size: 1.2rem;
            margin: 10px 0;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table .title {
            font-weight: 550;
            background: #f8f8f8;
            width: 110px;
            color: #626262;
        }

        table .data {
            text-align: left;
            color: #333;
        }
        .innings{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
            overflow: hidden;
            border-radius: 10px;
            border: 1px solid #bab8b8;
        }
        .inning-data{
            width: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 25px;
            padding: 15px;
            box-sizing: border-box;
        }
        .inning{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            border: 1px solid #627385;
            border-radius: 5px;
            overflow: hidden;
        }
        .inn-info{
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
            padding: 5px;
            height: -webkit-fill-available;
            justify-content: center;
            position: relative;
            font-size: 18px;
            font-weight: 600;
        }
        .inn-info label,
        .inn-no{
            color: #595959a6;
            font-size: 15px;
        }
        .stat{
            background: #d5d5d5;
            width: 100%;
            height: 125px;
            display: flex;
            align-items: center;
            padding: 0px 20px;
            box-sizing: border-box;
        }
        .skew{
            position: absolute;
            height: 100%;
            width: 127%;
            transform: skew(-15deg, 0deg);
            transform-origin: top left;
            background: #e9f3ff73;
            z-index: -1;
        }
        .weather{
            width: 100%;
            border: 1px solid black;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 15px;
            overflow: hidden;
        }
        .weather-info{
            height: 100%;
            padding: 10px;
            display: flex;
            justify-items: center;
            flex-direction: column;
            justify-content: center;
        }
        .weather-info label{
            font-size: 15px;
            text-align: center;
            color: #dddddd
        }
        .weather-data{
            display: flex;
            flex-direction: row;
            align-items: center;
            height: 80px;
            padding: 5px;
            background: linear-gradient(180deg, #11569d, #447ab0);
            color: white;
            justify-content: space-evenly;
        }
        .weather-title {
            background: #eef7ff;
            height: 25px;
        }
        .comm{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 15px;
            overflow: hidden;
        }
        .comm-name{
            height: 40px;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            padding: 0 15px;
            font-size: 20px;
            background: #00000017;
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
            justify-content: center;
            align-items: flex-start;
            margin: 15px 0px;
        }
        .ball-runs{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 15px;
            padding: 5px;
        }
        .commentry{
            padding: 5px;
        }
        .run{
            height: 25px;
            width: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid black;
            border-radius: 50%;
        }
        .line{
            border: 1px solid #0000001f;
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
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #0000001a;
        }
        .team-container{
            width: 100%;
            height: 30px;
            display: flex;
            background: #f0f0f0;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
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
            gap: 15px;
            box-sizing: border-box;
        }
        .dt{
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .dt.not-out{
            background: #E9F7F2;
        }
        .dt1{
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            justify-content: space-between;
            padding: 5px 15px;
        }
        .dt2{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
            padding: 0px 15px;
            box-sizing: border-box;
        }
        .not-out .dt2{
            color: #2E7D32;
        }
        .inning-batsman{
            width: 50%;
        }
        .match-not-start{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 15px;
            font-size: 18px;
            color: #666666;
            padding: 35px;
            border: 1px solid #0000001c;
            box-sizing: border-box;
            text-align: center;
        }
        .error-text{
            width: 230px;
        }
        .bowl-data{
            display: flex;
            width: 100%;
            gap: 5px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #0000001a;
        }
        .table-head{
            background: #F7F7FA;
        }
        .playername{
            color: #0B65F0;
        }
        .fall-of-wickets{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            padding: 5px 15px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #0000001a;
        }
        .fall-of-wickets .dt{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 5px 15px;
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
            border: 1px solid #00000024;
            overflow: hidden;
        }
        .row{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            padding: 15px 15px;
            font-size: 16px;
        }
        .teamimg{
            height: 25px;
            width: 25px;
            background: #d9d9d9;
            border-radius: 50%;
        }
        .t1{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .t2{
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .teams{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 5px 15px;
        }
        .teams .playername{
            text-wrap: auto;
            width: 110px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            font-size: 15px;
            
        }
        .teams .playername .role{
            color: #8e8e8eff;
        }
        .squad-border{
            border: 1px solid #ebebeb;
            height: 100%;
        }
        .playerimg{
            height: 35px;
            width: 35px;
            background: #d9d9d9;
            border-radius: 50%;
        }
        .team2 .squad-player{
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }
        .team1 .squad-player{
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row-reverse;
            gap: 10px;
        }
        .team2{
            display: flex;
            flex-direction: column;
            gap: 15px;
            justify-content: center;
            align-items: flex-end;
            text-align: right;
        }
        .team1{
            display: flex;
            flex-direction: column;
            gap: 15px;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
        }
        .popup-container{
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
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
            border: 1px solid black;
            background: white;
            border-radius: 10px;
            flex-direction: column;
            overflow: hidden;
        }
        .exit img{
            height: 15px;
            cursor: pointer;
        }
        .fed-head{
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            width: 100%;
            padding: 5px 15px;
        }
        .fed-body{
            height: 100%;
            width: 100%;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 10px 15px;
            background: #f5f5f596;
        }
        .feedback-container{
            height: 80px;
            min-width: 300px;
            max-width: 300px;
            resize: none;
            border-radius: 5px;
            border: 1px solid #00000013;
            background: #ededed;
        }
        .fed-btns{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-direction: row;
        }
        .fed-btns button{
            width: 50%;
            background: transparent;
            border: none;
            color: #666666;
            font-weight: 500;
            font-size: 14px;
            border-radius: 20px;
            line-height: 16px;
            padding: 10px 16px;
            cursor: pointer;
        }
        .fed-btns .submit{
            background: gray;
            color: white;
        }
        .feedback-cta-container{
            display: inline-flex;
            align-items: center;
            width: 100%;
            justify-content: center;
        }
        .feedback-cta-holder{
            padding: 5px;
            border: 1px solid hsl(148, 57%, 50%);
            border-radius: 4px;
            background: #E9F7F2;
        }
        .feedback-cta-button{
            font-weight: 600;
            font-size: 14px;
            line-height: 18px;
            color: #37C77A;
            border: none;
            background: none;
            cursor: pointer;
        }
        .cmc-report-issue-button-wrapper{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #cmc-report-issue-button{
            padding: 5px 12px;
            border: 1px solid #c83737;
            border-radius: 4px;
            background: #f7e9e9;
            font-weight: 600;
            font-size: 14px;
            line-height: 18px;
            color: #c73737;
            font-family: 'Poppins-cmc', -apple-system, BlinkMacSystemFont, "Segoe UI", "Liberation Sans", sans-serif;
            cursor: pointer;
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
                font-size: 25px;
                color: black;
                text-align: left;
                width: 130px;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0rem;
                text-align: left;
            }
            .txt-strike{
                font-weight: 200;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .swiper-slide{
                width: 382px;
            }
            .stat{
                height: 30px;
            }
        }

        @media(min-width: 601px) {
            .logo-img img{
                height: 35px;
                padding: 4px 0;
            }
            .txt-strike{
                font-weight: 200;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .logo-name {
                font-size: 25px;
                color: black;
                text-align: left;
                width: 130px;
                font-weight: 400;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0;
            }
            .swiper-slide{
                width: 382px;
            }
            .weather-data{
                height: 100px;
            }
            .stat{
                height: 35px;
            }
            .weather-title{
                height: 35px;
            }
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
                <div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
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
                <div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
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
                        <img src="./assets/images/teams/team1_logo.png" alt="Team 1 Name" onerror="this.style.opacity='0'">
                        Team 1 Name
                    </div>
                    <div class="score">100/2 (10.0)</div>
                </div>

                <div class="info team-score">
                    <div class="team">
                        <img src="./assets/images/teams/team2_logo.png" alt="Team 2 Name" onerror="this.style.opacity='0'">
                        Team 2 Name
                    </div>
                    <div class="score">98/3 (9.2)</div>
                </div>

                <div class="info">
                    <p>14 Jul 2025 | 3:00 PM</p>
                </div>
                <div class="info">
                    <p>CRR : 123</p>
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
                <table>
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
                <div class="innings">
                    <div class="stat">VENUE STATS</div>
                    <div class="inning-data">
                        <div class="inning">
                            <div class="inn-no inn-info"><div class="skew"></div> 1st Inning</div>
                            <div class="runs inn-info">453 <label for="">Avg Runs</label></div>
                            <div class="wicket inn-info">9 <label for="">Avg Wickets</label></div>
                            <div class="SR inn-info">55.00 <label for="">Avg SR</label></div>
                        </div>
                        <div class="inning">
                            <div class="inn-no inn-info"><div class="skew"></div> 2nd Inning</div>
                            <div class="runs inn-info">453 <label for="">Avg Runs</label></div>
                            <div class="wicket inn-info">9 <label for="">Avg Wickets</label></div>
                            <div class="SR inn-info">55.00 <label for="">Avg SR</label></div>
                        </div>
                    </div>
                </div>
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
                                <div class="ball">50.0</div>
                                <div class="run">4</div>
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
                    <div class="error-img"><img src="https://staticg.sportskeeda.com/cricket_images/scorecard/cmc-pre-scorecard-icon.svg" alt=""></div>
                    <span class="error-text">Match has not started yet</span>
                </div>

                <section id="team1">
                    <div class="bat-data">
                        <div class="team-container">
                            <div class="team-name"><lable class="name">Team 1</lable><label for="" class="score">258/6(2.1)</label></div>
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

                            <div class="dt">
                                <div class="dt1">
                                    <div class="inning-batsman playername">Tammy Beaumont</div>
                                    <div class="player-runs">5</div>
                                    <div class="player-balls">7</div>
                                    <div class="player-fours">0</div>
                                    <div class="player-sixs">0</div>
                                    <div class="player-SR">71.43</div>
                                </div>
                                <div class="dt2">lbw b Goud</div>
                            </div>

                            <div class="dt not-out">
                                <div class="dt1">
                                    <div class="inning-batsman playername">Tammy Beaumont</div>
                                    <div class="player-runs">5</div>
                                    <div class="player-balls">7</div>
                                    <div class="player-fours">0</div>
                                    <div class="player-sixs">0</div>
                                    <div class="player-SR">71.43</div>
                                </div>
                                <div class="dt2">Not Out</div>
                            </div>

                            <div class="dt">
                                <div class="dt1">
                                    <div class="inning-batsman">Extras</div>
                                    <div class="player-runs">13</div>
                                    <div class="player-balls">(B: 0, LB: 3, NB: 0, W: 10)</div>
                                    <div class="player-fours"></div>
                                    <div class="player-sixs"></div>
                                    <div class="player-SR"></div>
                                </div>
                            </div>

                            <div class="dt">
                                <div class="dt1">
                                    <div class="inning-batsman">TOTAL</div>
                                    <div class="player-runs">258/6</div>
                                    <div class="player-balls"></div>
                                    <div class="player-fours">23</div>
                                    <div class="player-sixs">0</div>
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
                                    <div class="player-EXT">EXT</div>
                                </div>
                            </div>
                            <div class="dt">
                                <div class="dt1">
                                    <div class="inning-batsman playername">Amanjot Kaur</div>
                                    <div class="player-runs">10</div>
                                    <div class="player-balls">0</div>
                                    <div class="player-fours">58</div>
                                    <div class="player-sixs">6</div>
                                    <div class="player-SR">2.11</div>
                                    <div class="player-EXT">6</div>
                                </div>
                            </div>
                            <div class="dt">
                                <div class="dt1">
                                    <div class="inning-batsman playername">Amanjot Kaur</div>
                                    <div class="player-runs">10</div>
                                    <div class="player-balls">0</div>
                                    <div class="player-fours">58</div>
                                    <div class="player-sixs">6</div>
                                    <div class="player-SR">2.11</div>
                                    <div class="player-EXT">6</div>
                                </div>
                            </div>
                            <div class="dt">
                                <div class="dt1">
                                    <div class="inning-batsman playername">Amanjot Kaur</div>
                                    <div class="player-runs">10</div>
                                    <div class="player-balls">0</div>
                                    <div class="player-fours">58</div>
                                    <div class="player-sixs">6</div>
                                    <div class="player-SR">2.11</div>
                                    <div class="player-EXT">6</div>
                                </div>
                            </div>
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

                        <div class="dt">
                                <div class="inning-batsman playername">Amy Jones</div>
                                <div class="out-by">b Goud</div>
                                <div class="score-and-over">
                                    <div class="score">1-8</div>
                                    <div class="overs">1.2</div>
                                </div>
                        </div>

                        <div class="dt">
                                <div class="inning-batsman playername">Tammy Beaumont</div>
                                <div class="out-by">lbw b Goud</div>
                                <div class="score-and-over">
                                    <div class="score">2-20</div>
                                    <div class="overs">3.6</div>
                                </div>
                        </div>
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
    const feedback_page = document.querySelectorAll('.cmc-report-issue-button');

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

    feedback_page.forEach(selector => {
        selector.addEventListener('click', () => {
            document.querySelector('.popup-container').style.display = 'flex';
        });
    })
    </script>
</body>
</html>