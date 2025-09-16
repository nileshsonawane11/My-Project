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

?>
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
            flex-direction: column;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 100px;
            align-items: center;
        }
        .team_section{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            align-items: center;
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
            overflow: hidden;
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

        .confirm-btn{
            display: none;
            height: 40px;
            width: 90px;
            background-color: black;
            color: white;
            border-radius: 12px;
        }

        .tie-btn{
            height: 50px;
            width: 110px;
            background-color: black;
            color: white;
            border-radius: 12px;
            font-size: 16px;
        }

        .logo img{
            height: 100%;
            object-fit: cover;
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
            <div class="team_section">
                <div class="teams">
                    <?php
                        $t_id1 = $score_log['white'];
                        $t_name1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$t_id1'"));
                    ?>
                    <div class="logo">
                        <?php if($t_name1['t_logo']) { ?>
                            <img src="../../assets/images/teams/<?php echo $t_name1['t_logo']; ?>" alt="">
                        <?php }else{ ?>
                            <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                        <?php } ?>
                    </div>
                    <div class="player-name">
                        <?php
                            echo $t_name1['t_name'];
                        ?>
                    </div>
                    <div class="image1"></div>
                    <button class="confirm-btn" data-team="<?php echo $score_log['white']; ?>">Confirm</button>
                </div>
                
                <div class="teams">
                    <?php
                        $t_id2 = $score_log['black'];
                        $t_name2 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$t_id2'"));
                    ?>
                    <div class="logo">
                        <?php if($t_name2['t_logo']) { ?>
                            <img src="../../assets/images/teams/<?php echo $t_name2['t_logo']; ?>" alt="">
                        <?php }else{ ?>
                            <img src="https://cdn-icons-png.flaticon.com/512/8140/8140303.png" alt="">
                        <?php } ?>
                    </div>
                    <div class="player-name">
                        <?php
                            echo $t_name2['t_name'];
                        ?>
                    </div>
                    <div class="image2"></div>
                    <button class="confirm-btn" data-team="<?php echo $score_log['black']; ?>">Confirm</button>
                </div>
            </div>

            <div class="tie-btns">
                <button class="tie-btn" onclick="get_tie(this)" data-teams='["<?php echo $score_log["white"]; ?>","<?php echo $score_log["black"]; ?>"]'>Tie</button>
            </div>
        </div>
    </div>

    <script>
        const teams = document.querySelectorAll('.teams');
        const confirm_btn = document.querySelectorAll('.confirm-btn');
        const tie_btn = document.querySelector('.tie-btn');
        const match_id = '<?php echo $match_id; ?>';

        let goBack = ()=>{
            window.history.back();
        }

        let get_score = (data) => {
            let params = {
                'match_id':match_id,
                'team':data
            }

            console.log(params);

            fetch('./Backend/update-chess-logs.php',{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(params)
            })
            .then(response => response.json())
            .then((data)=>{
                console.log(data);
                if(data.status == 200){
                    window.location.reload();
                }
            })
            .catch(error => console.log(error));

        }

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

           confirm_btn.forEach(btn => {
                btn.addEventListener("click", (e) => {
                    let parentTeam = btn.closest('.teams'); // or e.currentTarget.closest('.teams')
                    if (parentTeam) {
                        let playerNameEl = parentTeam.querySelector('.player-name');
                        if (playerNameEl) {
                            let team = btn.getAttribute('data-team');
                            get_score(team);
                        }
                    }
                });
            });

            let get_tie = (tie_btn) => {
                let parentTeam = tie_btn.closest('.sector'); 
                if (parentTeam) {
                    let playerNameEls = parentTeam.querySelectorAll('.teams .player-name');
                    const teams = JSON.parse(tie_btn.dataset.teams);
                    get_score(teams);
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
    </script>
</body>
</html>