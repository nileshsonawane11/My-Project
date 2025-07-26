
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
            background-image: url("https://i.ibb.co/xKhndDVn/doing-sport-concept-23-2151937746.jpg");
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
            <div class="exit">
                <div class="for-icon1">
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.36196 6.62029L11.672 1.04729C11.7606 0.954302 11.8101 0.830761 11.8101 0.70229C11.8101 0.573819 11.7606 0.450279 11.672 0.357291L11.666 0.35129C11.623 0.306055 11.5713 0.270036 11.5139 0.245422C11.4566 0.220808 11.3949 0.208115 11.3325 0.208115C11.2701 0.208115 11.2083 0.220808 11.151 0.245422C11.0937 0.270036 11.0419 0.306055 10.999 0.35129L5.99896 5.59929L1.00096 0.35129C0.95799 0.306055 0.906263 0.270036 0.84893 0.245422C0.791597 0.220808 0.729857 0.208115 0.667463 0.208115C0.60507 0.208115 0.543329 0.220808 0.485996 0.245422C0.428663 0.270036 0.376937 0.306055 0.333963 0.35129L0.327963 0.357291C0.239318 0.450279 0.189867 0.573819 0.189867 0.70229C0.189867 0.830761 0.239318 0.954302 0.327963 1.04729L5.63796 6.62029C5.68466 6.6693 5.74082 6.70832 5.80305 6.73498C5.86528 6.76164 5.93227 6.77539 5.99996 6.77539C6.06766 6.77539 6.13465 6.76164 6.19688 6.73498C6.2591 6.70832 6.31527 6.6693 6.36196 6.62029Z" fill="black"/>
                    </svg>
                </div>
                <div class="exit-text">End 1<sup>st</sup> Half</div>
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
    <div class="blur-container"></div>

    <div class="container2">
        <div class="image"></div>
        <div class="current-set"><div class="index">1<sup>st</sup> Half</div></div>
        <div class="blur-container"></div>

        <div class="buttons">
            <div class="point-buttons">
                <div class="team-btn">
                    <label class="team-name">Team1</label>
                    <button class="team1-button team-buttons" data-team="">Goal</button>
                </div>
                <div class="team-btn">
                    <label class="team-name">Team2</label>
                    <button class="team2-button team-buttons" data-team="">Goal</button>
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
    let current_inning = null;
    is_complete = false;
    
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