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
            background-image: url('https://images.unsplash.com/photo-1605135693932-f2d4d1f3e9e8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
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
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
        }

        .return svg {
            cursor: pointer;
            fill: #F83900;
        }
        .for-exit{
            display: flex;
            justify-content: center;
        }

        .exit {
            width: 179px;
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
            background-color: rgba(255, 255, 255, 0.9);
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
        }

        .score1, .score2 {
            color: #F83900;
            font-size: 4.5rem;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
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

        .container2 {
            position: relative;
            width: 100%;
            height: 80vh;
            background-color: rgba(255, 255, 255, 0.65);
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
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
                z-index: -1;
                background-position: center;
                background-image: url("https://i.ibb.co/Vpk3fcvs/kabaddi-m.jpg");
            background-size: cover;
                -webkit-mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            -webkit-mask-size: 100% 100%;
            mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
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

        .raider {
            background-color: white;
            color: #F83900;
            border-radius: 30px;
            border: 2px solid #F83900;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .raider:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            background-color: #F83900;
            color: white;
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
            color: black;
            font-size: 0.9rem;
        }

        .raider-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
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
            margin-top: 20px;
            padding: 10px;
            border-top: 1px solid #eee;
            border-radius: 10px 10px 0 0;
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
            display: grid;
            align-items: center;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 15px;
            justify-content: center;
            justify-items: center;
            background-color: white;
        }

        .score-point {
            width: 90%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 15px;
            font-size: 2rem;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid #FAC01F;
            color: #F83900;
        }

        .score-point:hover {
            background-color: #FAC01F;
            color: white;
            transform: scale(1.05);
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

        .team1-info,
        .team2-info {
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

        .team1-info:hover,
        .team2-info:hover {
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
            padding: 25px;
            gap: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 0 0 15px 15px;
        }

        .timer {
            font-size: 2rem;
            font-weight: bold;
            color: #F83900;
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
                        <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="#F83900"/>
                    </svg>
                </div>
            </div>

            <div class="for-exit">
                <div class="exit">
                    <div class="for-icon1">
                        <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.36196 6.62029L11.672 1.04729C11.7606 0.954302 11.8101 0.830761 11.8101 0.70229C11.8101 0.573819 11.7606 0.450279 11.672 0.357291L11.666 0.35129C11.623 0.306055 11.5713 0.270036 11.5139 0.245422C11.4566 0.220808 11.3949 0.208115 11.3325 0.208115C11.2701 0.208115 11.2083 0.220808 11.151 0.245422C11.0937 0.270036 11.0419 0.306055 10.999 0.35129L5.99896 5.59929L1.00096 0.35129C0.95799 0.306055 0.906263 0.270036 0.84893 0.245422C0.791597 0.220808 0.729857 0.208115 0.667463 0.208115C0.60507 0.208115 0.543329 0.220808 0.485996 0.245422C0.428663 0.270036 0.376937 0.306055 0.333963 0.35129L0.327963 0.357291C0.239318 0.450279 0.189867 0.573819 0.189867 0.70229C0.189867 0.830761 0.239318 0.954302 0.327963 1.04729L5.63796 6.62029C5.68466 6.6693 5.74082 6.70832 5.80305 6.73498C5.86528 6.76164 5.93227 6.77539 5.99996 6.77539C6.06766 6.77539 6.13465 6.76164 6.19688 6.73498C6.2591 6.70832 6.31527 6.6693 6.36196 6.62029Z" fill="#F83900"/>
                        </svg>
                    </div>
                    <div class="exit-text">End 1st Half</div>
                </div>
            </div>

            <div class="score-teamlogo">
                <div class="score2">0</div>
                <div class="score1">0</div>
            </div>

            <div class="main-scoreboard">
                <div class="scoreboard">
                    <div class="right">       
                        <div class="score-team-data">
                            <div class="team-logo">T1</div>
                            <div class="team-data">
                                <label class="team1_name">Bengal Warriors</label>
                            </div>
                        </div>
                    </div>

                    <div class="left">
                        <div class="score-team-data">
                            <div class="team-logo">T2</div>
                            <div class="team-data">
                                <label class="team2_name">Patna Pirates</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container2">
            
            <div class="image"></div>
            <div class="current-set">1st Half</div>
            <div class="blur-container"></div>

            <div class="buttons">
                <div class="point-buttons">
                    <div class="team-btn">
                        <label class="team-name">Bengal Warriors</label>
                        <button class="team-button" data-team="">Point</button>
                    </div>
                    <div class="team-btn">
                        <label class="team-name">Patna Pirates</label>
                        <button class="team-button" data-team="">Point</button>
                    </div>
                </div>

                <div class="serve-button">
                    <div class="raid-btn">
                        <div class="serve-timer">
                            <label class="team-name timer"></label>
                            <button class="serve">Start Timer</button>
                        </div>
                        <div class="serve-undo">
                            <div class="raider-info">
                                <label class="optional">(Optional) </label>
                                <button class="serve raider">Select Raider</button>
                            </div>
                            <button class="undo">Undo</button>
                        </div>
                    </div>
                </div>

                <div class="history">
                    <div class="log">
                        <div class="serving">Patna Pirates raiding</div>
                        <div class="point-to-update">
                            <div class="point-to">
                                <label class="point-text">Point - 2</label>
                                <label class="to_team-name">to Patna Pirates</label>
                            </div>
                            <div class="last-update">12-14</div>
                        </div>
                    </div>

                    <div class="log">
                        <div class="serving">Bengal Warriors raiding</div>
                        <div class="point-to-update">
                            <div class="point-to">
                                <label class="point-text">Point - 1</label>
                                <label class="to_team-name">to Bengal Warriors</label>
                            </div>
                            <div class="last-update">12-12</div>
                        </div>
                    </div>

                    <div class="log">
                        <div class="serving">Patna Pirates raiding</div>
                        <div class="point-to-update">
                            <div class="point-to">
                                <label class="point-text">Point - 3</label>
                                <label class="to_team-name">to Patna Pirates</label>
                            </div>
                            <div class="last-update">11-12</div>
                        </div>
                    </div>
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
                    <div class="players-info">
                        <label class="player-cnt">Players(6)</label>
                        <div class="player-name">1. Maninder Singh</div>
                        <div class="player-name">2. Shrikant Jadhav</div>
                        <div class="player-name">3. Deepak Narwal</div>
                        <div class="player-name">4. Rinku Narwal</div>
                        <div class="player-name">5. Vaibhav Garje</div>
                        <div class="player-name">6. Shubham Shinde</div>
                        <div class="player-rel">
                            <div class="assign-later">Cancel</div>
                            <label for="" class="replace">Replace Player</label>
                        </div>
                    </div>
                </div>

                <div class="container5">
                    <div class="point-assign">
                        <label class="poi-ass">Point</label>
                        <label class="who">Which team scored the point</label>
                    </div>
                    <div class="teams-info">
                        <div class="team1-info" data-team="team1">
                            <div class="teams-logo">T1</div>
                            <div class="teams-name">Bengal Warriors</div>
                        </div>
                        <div class="team2-info" data-team="team2">
                            <div class="teams-logo">T2</div>
                            <div class="teams-name">Patna Pirates</div>
                        </div>
                    </div>
                </div>

                <div class="container4">
                    <div class="serve-result">
                        <label class="ser-res">Raid Result</label>
                        <label class="outcome">Outcome of the Raid</label>
                    </div>
                    <div class="outcomes">
                        <div class="score-point">0</div>
                        <div class="score-point">1</div>
                        <div class="score-point">2</div>
                        <div class="score-point">3</div>
                        <div class="score-point">4</div>
                        <div class="score-point">5</div>
                        <div class="score-point">6</div>
                        <div class="score-point">7</div>
                        <div class="score-point">8</div>
                        <div class="score-point">9</div>
                        <div class="score-point">10</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                slideContainer.appendChild(container5);
                slideContainer.appendChild(container4);
            } else {
                console.warn("One or more containers not found in the DOM.");
            }
            
            const playerNames = document.querySelectorAll('.player-name');
            const assignLater = document.querySelector('.assign-later');
            const scorepoint = document.querySelectorAll('.score-point');
            const teambtn = document.querySelectorAll('.team-button');

            // Current slide position (0=container3, 1=container5, 2=container4)
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
                player.addEventListener('click', () => {
                    goToSlide(1);
                    getplayername(player);
                })
            });
            
            let getplayername = (el) => {
                console.log(el.innerText)
            }

            let serveresult = (el) => {
                console.log(el.innerText);
            }

            assignLater.addEventListener('click', () => goToSlide(1));
            
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

            scorepoint.forEach(selector => {
                selector.addEventListener("click", () => {
                    if (slideWrapper) {
                        slideWrapper.style.transition = 'transform 0.5s ease';

                        setTimeout(() => {
                           slideWrapper.style.transform = 'translateY(600px)'; 
                        }, 300);
                        
                        serveresult(selector);
                        selector.style.border = "2px solid #F83900";
                        selector.style.backgroundColor = "#FAC01F";
                        selector.style.color = "white";
                        cancelRaidTimer();
                        setTimeout(() => {
                            slideContainer.style.transform = 'translateX(0)';
                            selector.style.border = "2px solid #FAC01F";
                            selector.style.backgroundColor = "white";
                            selector.style.color = "#F83900";
                        }, 600);
                        
                    }
                })
            })

            teambtn.forEach(selector => {
                selector.addEventListener("click", () => {
                    if (slideWrapper) {
                        slideWrapper.style.transition = 'transform 0.5s ease';
                        slideContainer.style.transform = 'translateX(-66.66%)';
                        setTimeout(() => {
                           slideWrapper.style.transform = 'translateY(0px)'; 
                        }, 300);
                        
                        serveresult(selector);
                        selector.style.border = "2px solid #F83900";
                        selector.style.backgroundColor = "#F83900";
                        selector.style.color = "white";
                        cancelRaidTimer();
                        setTimeout(() => {
                            selector.style.border = "2px solid #F83900";
                            selector.style.backgroundColor = "white";
                            selector.style.color = "#F83900";
                        }, 600);
                        
                    }
                })
            })

            document.querySelectorAll(".team1-info,.team2-info").forEach(team => {
                team.addEventListener("click",() => {
                    goToSlide(2);
                    console.log(team.getAttribute('data-team'));
                })
            })

            let raidTimer;
            let timeLeft = 30;

            serveBtn.addEventListener('click', () => {
                const isStarting = serveBtn.innerText === 'Start Timer';

                if (isStarting) {
                    serveBtn.innerText = 'Cancel';
                    startRaidTimer();
                } else {
                    serveBtn.innerText = 'Start Timer';
                    document.querySelector('.timer').innerText = '';
                    cancelRaidTimer();
                }
            });

            function startRaidTimer() {
                timeLeft = 30;

                raidTimer = setInterval(() => {
                    timeLeft--;
                    document.querySelector('.timer').innerText = `00:${timeLeft < 10 ? '0' + timeLeft : timeLeft}`;
                    console.log(`Time left: ${timeLeft}s`);

                    if (timeLeft <= 0) {
                        clearInterval(raidTimer);
                        document.querySelector('.timer').innerText = '';
                        serveBtn.innerText = 'Start Timer';
                        console.log('Raid over!');
                    }
                }, 1000);
            }

            function cancelRaidTimer() {
                clearInterval(raidTimer);
                document.querySelector('.timer').innerText = '';
                serveBtn.innerText = 'Start Timer';
                console.log('Raid over!');
                console.log('Raid cancelled.');
            }

            function goBack() {
                window.history.back();
            }
        });
    </script>
</body>
</html>