<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        --primary-light-10: rgba(250, 192, 31, 0.1);
        --primary-dark-10: rgba(248, 57, 0, 0.1);
        --background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        --text-dark: #2d3748;
        --text-light: #4a5568;
        --bg-light: #f8fafc;
        --card-bg: #ffffff;
        --border-light: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.4);
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
       .blur-container {
            top: 0;
            position: absolute;
            height: 100%;
            width: 100%;
            background-color: transparent;
            opacity: 0.5;
            z-index: -1;
            background-position: center;
            background-image: url("https://i.ibb.co/1YsBtk2x/desktop-wallpaper-playing-chess-and-mobile-background-chess-game.jpg");
            background-size: cover;
        }
        .container {
        height: 100vh;
        display: flex;
        position: relative;
        max-width: 1200px;
        min-height: 480px;
        align-items: center;
        justify-content: flex-start;
        flex-direction: column;
        gap: 30px;
        padding: 40px;
        }
        .return {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
        }
        .return svg {
            cursor: pointer;
            fill: var(--primary-dark);
            transition: var(--transition);
        }
        label {
            font-weight: bold;
            font-size: 1.2rem;
            display: inline-block;
        }
        .txt {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .sector {
            display: flex;
            width: 100%;
            flex-direction: row;
            align-items: center;
            justify-content: space-evenly;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 100px;
        }
        .teams {
            height: 380px;
            width: 44%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-around;
            background: rgba(255, 255, 255, 0.1); /* semi-transparent white */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); /* subtle shadow */
            backdrop-filter: blur(8px); /* glass effect */
            -webkit-backdrop-filter: blur(8px); /* Safari support */
            border: 1px solid rgba(255, 255, 255, 0.3); /* light border */
            border-radius: 12px; optional: rounded corners
            cursor: pointer;
            transition: var(--transition);
            padding: 12px;
        }

        .logo {
            height: 107px;
            width: 107px;
            border-radius: 50%;
            background-color: #d9d9d9;
        }

        .player-name {
            font-size: 22px;
            text-align: center;
            font-weight: 600;
            color: var(--text-dark);
            margin-top: 8px;
        }

        .image1 {
            height: 110px;
            width: 110px;
            background-color: transparent;
            background-image: url("https://i.ibb.co/nqSzLLht/Pngtree-black-and-white-chess-board-5983389.png");
            background-size: cover;
        }
        .image2 {
            height: 110px;
            width: 110px;
            background-color: transparent;
            background-image: url("https://i.ibb.co/MkmNjvWh/Pngtree-black-and-white-chess-board-5983391.png");
            background-size: cover;
            background-position-y: 450px;
        }

        .confirm-btn {
            display: none;
            height: 40px;
            width: 90px;
            background-color: black;
            color: white;
            border-radius: 12px;
        }
        @media (max-width:350px) {
            .logo {
                height: 80px;
                width: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="blur-container"></div>
    <div class="container">
        <div class="return">
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div></div>
        </div>
        <div class="txt">
            <label for="">Who won the Match?</label>
        </div>
        <div class="sector">
            <div class="teams">
                <div class="logo"></div>
                <div class="player-name">Player Name1</div>
                <div class="image1"></div>
                <button class="confirm-btn" style="display: none;">Confirm</button>
            </div>
            <div class="teams">
                <div class="logo"></div>
                <div class="player-name">Player Name2</div>
                <div class="image2"></div>
                <button class="confirm-btn">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        const teams = document.querySelectorAll('.teams');
        const confirm_btn = document.querySelectorAll('.confirm-btn');

        teams.forEach(team => {
            team.addEventListener('click', () => {
                // Reset all: border and hide confirm buttons
                teams.forEach(t => {
                    t.style.border = "2px solid transparent";
                    const btn = t.querySelector('.confirm-btn');
                    if (btn) btn.style.display = "none";
                });

                // Highlight clicked and show confirm button
                team.style.border = "2px solid black";
                const confirmBtn = team.querySelector('.confirm-btn');
                if (confirmBtn) confirmBtn.style.display = "block";
            });
        });

            confirm_btn.forEach(selector => {
                selector.addEventListener("click", () => {
                    const parentTeam = selector.closest('.teams'); // get the correct .teams div
                    if (parentTeam) {
                        const playerNameEl = parentTeam.querySelector('.player-name'); // get that team's player name
                        if (playerNameEl) {
                            console.log(playerNameEl.innerText); // ✅ prints only that one
                        }
                    }
                });
            });

    </script>
</body>
</html>