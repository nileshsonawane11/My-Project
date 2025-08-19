
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Scoreboard</title>
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
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --primary-light-10: rgba(250, 192, 31, 0.1);
            --primary-dark-10: rgba(248, 57, 0, 0.1);
            --background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
            --text-dark: #2d3748;
            --text-light: #4a5568;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --border-light: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.2s ease-in-out;
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
            color: #ffffff;
            font-size: 4.5rem;
            font-weight: bold;
            margin-bottom: 30px;
            -webkit-text-stroke: 1px #ffa400;
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
            background-image: url("https://i.ibb.co/LDKBzsf4/cool-basketball-wallpaper-41592-42568-hd-wallpapers.jpg");
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
        }

        .log {
            height: 90px;
            width: 100%;
            margin-top: 10px;
            background-color: white;
            border: 1px solid #eee;
            color: #333;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
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
            height: max-content;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            background-color: white;
            margin-top: 25px;
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

        .rule { 
            width: 80%;
            padding: 16px;
            background-color: var(--card-bg);
            transition: var(--transition);
        }

        .input-fields {
        width: 100%;
        position: relative;
        margin-bottom: 24px;
    }
    
    .input-fields label {
        position: absolute;
        bottom: 22px;
        left: 14px;
        font-size: 16px;
        transition: var(--transition);
        color: var(--text-muted);
        pointer-events: none;
    }
    
    .input-fields input:valid ~ label,
    .input-fields input:focus ~ label {
        transform: translateY(-28px);
        font-size: 14px;
        color: var(--primary-dark);
        font-weight: 600;
    }
    .container6 input[type="text"],
    .container6 input[type="email"],
    .container6 input[type="password"],
    .container6 input[type="number"],
    .container6 input[type="tel"],
    .container6 input[type="datetime"],
    .container6 input[type="time"],
    .container6 input[type="date"],
    .container6 select {
        border: none;
        border-bottom: 2px solid var(--border-light);
        margin: 8px 0;
        padding: 10px 15px;
        font-size: 16px;
        width: 100%;
        outline: none;
        height: 48px;
        background: transparent;
        transition: var(--transition);
    }
    
    .team1-poi,
    .team2-poi {
        display: flex;
        flex-direction: column;
        align-items: center;
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
    <div class="blur-container"></div>
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
                <div class="undo-seyup"><button class="undo-btn" id='undo-btn' onclick="process_undo()">Yes I’m certain</button></div>
                <div class="undo-seyup"><p class="undo-cancel" onclick="document.querySelector('#undo').close();">Cancel</p></div>
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
                    <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="white"/>
                </svg>
            </div>
        </div>

        <div class="for-exit">
            <div class="exit" onclick="end_halfs()">
                <div class="for-icon1">
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.36196 6.62029L11.672 1.04729C11.7606 0.954302 11.8101 0.830761 11.8101 0.70229C11.8101 0.573819 11.7606 0.450279 11.672 0.357291L11.666 0.35129C11.623 0.306055 11.5713 0.270036 11.5139 0.245422C11.4566 0.220808 11.3949 0.208115 11.3325 0.208115C11.2701 0.208115 11.2083 0.220808 11.151 0.245422C11.0937 0.270036 11.0419 0.306055 10.999 0.35129L5.99896 5.59929L1.00096 0.35129C0.95799 0.306055 0.906263 0.270036 0.84893 0.245422C0.791597 0.220808 0.729857 0.208115 0.667463 0.208115C0.60507 0.208115 0.543329 0.220808 0.485996 0.245422C0.428663 0.270036 0.376937 0.306055 0.333963 0.35129L0.327963 0.357291C0.239318 0.450279 0.189867 0.573819 0.189867 0.70229C0.189867 0.830761 0.239318 0.954302 0.327963 1.04729L5.63796 6.62029C5.68466 6.6693 5.74082 6.70832 5.80305 6.73498C5.86528 6.76164 5.93227 6.77539 5.99996 6.77539C6.06766 6.77539 6.13465 6.76164 6.19688 6.73498C6.2591 6.70832 6.31527 6.6693 6.36196 6.62029Z" fill="black"/>
                    </svg>
                </div>
                <div class="exit-text">End 1<sup>st</sup> quarter</div>
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
                        <div class="team-logo">
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                        </div>
                        <div class="team-data">
                            <label class="team1_name">Team1</label>
                        </div>
                    </div>
                </div>

                <div class="left">
                    <div class="score-team-data">

                        <div class="team-logo">
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                    </div>
                        <div class="team-data">
                            <label class="team2_name">Team2</label>
                            <label class="set"></label>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
    

    <div class="container2">
        <div class="image"></div>
        <div class="current-set"><div class="index">1<sup>st</sup> quarter</div></div>
        <div class="blur-container"></div>

        <div class="buttons">
            <div class="point-buttons">
                <div class="team-btn">
                    <label class="team-name">Team1</label>
                    <button class="team1-button team-buttons" data-team="">Point</button>
                </div>
                <div class="team-btn">
                    <label class="team-name">Team2</label>
                    <button class="team2-button team-buttons" data-team="">Point</button>
                </div>
            </div>

            <div class="serve-button">
                <div class="team-btn">
                    <div class="undo-button">
                        <button class="undo">Undo</button>
                    </div>
                </div>
            </div>

            <div class="history">

                <div class="log">
                        <div class="point-to-update">
                            <div class="point-to">
                                <label class="point-text">Point - </label>
                                <label class="to_team-name">to Team1</label>
                            </div>
                            <div class="last-update">0-0</div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="slide-wrapper">
        <div class="slide-container">
            <div class="container3">
                <div class="current-server">
                    <label class="curr-ser">Who scored the Goal?</label>
                    <label class="tap">Select a Player who scored</label>
                </div>

                <div class="players-info">
                    <label class="player-cnt">Players()</label>

                    <div class="player-replace" data-player-id =''>
                        <div class="player1-name">Mahatma Gandhi</div>
                    </div>
                </div>
            </div>         
        </div>
    </div>

    <div class="container6">
            <div class="current-server">
                <label class="curr-ser">Penalty Shootout</label>
                <label class="tap">Enter the results of penalty Shootout</label>
            </div>
            <div class="teams-info">
                <div class="team1-poi">
                    <div class="team1-info teams-info" data-team="">
                        <div class="teams-logo">
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                        </div>
                        <div class="teams-name">Team1</div>
                        
                    </div>
                    <div class="rule">
                            <div class="input-fields event-time">
                            <input type="number" id="team1-goals"  required><label for="team1-goals" id="time">No. of Goals</label></div>
                    </div>
                </div>
                <div class="team2-poi">
                    <div class="team2-info teams-info" data-team="">
                        <div class="teams-logo">
                                <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                        </div>
                        <div class="teams-name">Team2</div>
                        
                    </div>
                    <div class="rule">
                            <div class="input-fields event-time">
                            <input type="number" id="team2-goals"  required><label for="team2-goals" id="time">No. of Goals</label></div>
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
    let back_decision = null;
    let match = null;
    let end_half = false;
    is_complete = false;
    
    let get_score = () => {
        let data = {
            'match_id': match,
            'point_taken_by': point_taken_by,
            'Isend_half' : end_half,
            'chaser_player': chaser,
            'out_player' : out_player,
            ...(exit_inn == true ? {'exit' : exit_inn} : {}),
            ...(undo == true ? {'undo' : undo} : {}),
            ...(is_complete == true ? {'complete' : is_complete} : {})
        }

        console.log(data);

        // fetch('./Backend/update-kho-kho-logs.php',{
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json'
        //     },
        //     body: JSON.stringify(data)
        // })
        // .then(response => response.json())
        // .then((data) => {
        //     console.log(data);
        //     if(data.status == 200){
        //             if(data.field == 'is_complete'){

        //                 match_dialogue.showModal();
        //                 match_dialogue.classList.add('shake');
        //                 navigator.vibrate([200,100,200]);
        //                 document.querySelectorAll('button:not(.undo, .undo-btn)').forEach(btn => {
        //                     btn.disabled = true;
        //                     btn.style.pointerEvents = 'none';
        //                 });

        //             }else if(data.field == 'is_tie'){

        //                 start_dialogue.showModal();
        //                 start_dialogue.classList.add('shake');
        //                 navigator.vibrate([200,100,200]);

        //             }else if(data.field == 'empty'){

        //                 let el = document.getElementById('error-'+data.field);
        //                 el.innerHTML = data.message;
        //                 el.style.display = 'block';
        //                 el.style.color = 'red';
        //                 undo = false;
        //                 setTimeout(() => {
        //                     el.innerHTML = null;
        //                     el.style.display = 'none';
        //                 }, 2000);
        //             }else{
        //                 //Bypass reload
        //                 window.removeEventListener("beforeunload", preventReload);
        //                 location.reload();
        //             }
                    
        //         }
        // })
        // .catch(error => {
        //     console.log(error);
        // })
    }

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
            // if(current_half != 1){
            //     end_half = true;
            //     raider = null;
            //     get_score();
            // }else{
                let half_container = document.querySelector('#half_completed');
                half_container.showModal();
                half_container.classList.add('shake');
                navigator.vibrate([1000,50,100,50,100]);
            // }
            
        }

    document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const point1 = document.querySelector('.team1-button');
    const point2 = document.querySelector('.team2-button');
    const team_btns = document.querySelectorAll('.team-buttons');
    const tech_point = document.querySelector('.tech-point');

    // Get existing containers
    const container3 = document.querySelector('.container3');
    const container6 = document.querySelector('.container6');
    const slideContainer = document.querySelector('.slide-container');
    const slideWrapper = document.querySelector('.slide-wrapper');
    const exit_inning = document.querySelector('.exit');
    const undo_logs = document.querySelector('.undo');
    
    // Wrap containers in sliding parent
    if (slideContainer && container3  ) {
    slideContainer.appendChild(container3);
    } else {
        console.warn("One or more containers not found in the DOM.");
    }
    
    const player1Names = document.querySelectorAll('.container3 .player-replace');
    const inButton = document.querySelector('.in');
    const aceButton = document.querySelector('.ace');
    const errorButton = document.querySelector('.error');


    // Current slide position (0=container3, 1=container4)
    let currentSlide = 0;


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
                const team = event.currentTarget.getAttribute('data-team');
                point_taken_by = team;
                    currentSlide = 0;
                    slideWrapper.style.transform = 'translateY(0)';        
        });
    });
    
    // Event listeners
    player1Names.forEach(player => {
        player.addEventListener('click', (el) => {
            console.log(player.innerText);
            slideWrapper.style.transform = 'translateY(600px)';  
            chaser = player.getAttribute('data-player-id');
        })
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

   
</script>
</body>

</html>