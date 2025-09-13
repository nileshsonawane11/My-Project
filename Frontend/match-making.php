<?php
    session_start();
    include '../config.php';

    if(!isset($_SESSION['user'])){
        header('location: ./front-page.php');
        exit();
    }
    if($_SESSION['role'] == "User"){
        header('location: ../dashboard.php?update="live"&sport="CRICKET"');
        exit();
    }

    $tournament = $_GET['t'] ?? '';
    $teams = explode(',',$_GET['teams']) ?? '';
    $team_ids = $teams;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>Add Staff</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
            scrollbar-width: none;
        }
        
        /* Theme Variables */
        :root {
            --primary-color: rgba(209, 34, 31, 1);
            --primary-light: rgba(209, 34, 31, 0.8);
            --primary-dark: rgba(160, 25, 23, 1);
            --background: #ffffff;
            --card-bg: #ffffff;
            --text-dark: #000000;
            --text-light: #333333;
            --border-color: #e0e0e0;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --svg-fill: #000000;
            --hover-bg: rgba(209, 34, 31, 0.08);
            --input-bg: #ffffff;
            --gradient: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
            --team-bg: #EEEEEE;
            --logo-bg: #D9D9D9;
        }

        /* Dark theme variables */
        [data-theme="dark"] {
            --background: #121212;
            --card-bg: #1e1e1e;
            --text-dark: #ffffff;
            --text-light: #e0e0e0;
            --border-color: #333333;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            --svg-fill: #ffffff;
            --hover-bg: rgba(209, 34, 31, 0.15);
            --input-bg: #2a2a2a;
            --gradient: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
            --team-bg: #3d3d3d;
            --logo-bg: #4d4d4d;
        }
        
        body{
            height: max-content;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background-color: var(--background);
            color: var(--text-dark);
        }

        svg path {
            fill: var(--text-dark);
        }
        .return{
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
        }
        .return svg{
            cursor: pointer;
            fill: var(--svg-fill);
        }
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
            width: 100%;
            gap: 60px
        }
        .txt{
            line-height: 20px;
            color: var(--text-dark);
        }
        .matches{
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: max-content;
            gap: 80px;
        }
        .match-container{
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            flex-direction: column;
            gap: 20px
        }
        .team-container{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
        }
        .teams{
            height: 147px;
            width: 111px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-around;
            opacity: 0;
            background: var(--team-bg);
            box-shadow: var(--shadow);
            transition: all 0.5s ease-in-out;
        }
        .logo{
            height: 75px;
            width: 75px;
            background: var(--logo-bg);
            border-radius: 50%;
            overflow: hidden;
        }
        .logo img{
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .tname{
            font-size: 17px;
            text-wrap: auto;
            text-align: center;
            color: var(--text-dark);
        }
        .vs,.plus{
            font-size: 30px;
            transform: translate(0,20rem);
            transition: all 0.5s ease-in-out;
            opacity: 0;
            color: var(--text-dark);
        }
        .right-side{
            transform: translateX(150%) translateY(50%);
        }
        .left-side{
            transform: translateX(-150%) translateY(50%);
        }
        .show{
            opacity: 1;
            transform: translate(0,0);
        }
        .team-no{
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        .match-head,.t-num{
            transition: all 0.5s ease-in-out;
            transition-delay: 0.3s;
            opacity: 0;
            color: var(--text-dark);
        }
        .match-list{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 80px;
        }
        .pls{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .plus{
                bottom: 30px;
                height: 60px;
                border: none;
                cursor: pointer;
                display: flex;
                font-size: 30px;
                width: 60px;
                border-radius: 50%;
                justify-content: center;
                align-items: center;
                overflow:hidden;
                box-shadow: 0px 4px 0px rgba(131, 131, 131, 0.5);
                background: var(--gradient);
                transition: transform 0.5s ease;
                color: white;
            }
        .plus-icon{
            display: flex;
            align-items: center;
            justify-content: center;
             transition: transform 0.3s ease-out;
        }

        .plus:hover .plus-icon {
            transform: rotate(180deg);
        }
        .add-btn{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }


        @media (min-width:1001px){
            .matches{
                width: 75%;
            }
        }

        @media (min-width:601px) {
             .container{
                display: flex;
                background-color: var(--card-bg);
                box-shadow: var(--shadow);
                position: relative;
                width: 90%;
                max-width: 100%;
                min-height: 100vh;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                padding: 40px;
            }
            .container2{
                gap: 70px;
                width: 70%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],select{
                border: none;
                border-bottom: solid 1px var(--border-color);
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 16px;
                width: 100%;
                outline: none;
                height: 45px;
                background: var(--input-bg);
                color: var(--text-dark);
            }
            .add-btn button{
                background: var(--gradient);
                color: #fff;
                font-size: 12px;
                border: 1px solid transparent;
                border-radius: 48px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                cursor: pointer;
                height: 40px;
                width: 96px;
            }
            
        }

        @media(max-width: 601px) {
            .container{
                display: flex;
                background-color: var(--card-bg);
                box-shadow: var(--shadow);
                position: relative;
                width: 768px;
                z-index: 0;
                max-width: 100%;
                min-height: 100vh;
                padding: 40px 40px;
                align-items: flex-start;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px
            }
            
            .container input[type="text"],[type="email"],[type="password"],[type="number"],select{
                border: none;
                border-bottom: solid 1px var(--border-color);
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 15px;
                width: 100%;
                outline: none;
                height: 45px;
                background: var(--input-bg);
                color: var(--text-dark);
            }
            .add-btn button{
                background: var(--gradient);
                color: #fff;
                font-size: 12px;
                border: 1px solid transparent;
                border-radius: 48px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                cursor: pointer;
                height: 40px;
                width: 96px;
            }
            
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="return">
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div>
                <div class='add-btn'>
                    <button onclick='save(event)' type='submit' id='save'>save</button>
                </div>
            </div>
        </div>
        <div class="container2">
            <div class="txt">
                <label for="">Add a Tournament</label>
                <h4>Match Matching</h4>
            </div>
            <div class="match-list">
                <div class="matches">

                    <?php
                    $query = mysqli_query($conn,"SELECT * FROM `tournaments` WHERE `tournament_id` = '$tournament'");
                    $row = mysqli_fetch_assoc($query);
                    //$teams = ['Team A', 'Team B', 'Team C', 'Team D', 'Team E', 'Team F','Team G','Team H','Team I','Team J','Team K']; // Example team names
                    $format = $row['tournament_format']; // 'league' or 'knockout'

                    $matchNo = 1;

                    if ($format == 'league') {
                        echo "<h2>League Matches</h2>";
                        for ($i = 0; $i < count($teams) - 1; $i++) {
                            for ($j = $i + 1; $j < count($teams); $j++) {
                                $team1 = $teams[$i];
                                $team2 = $teams[$j];

                                $team1_no = $i + 1;
                                $team2_no = $j + 1;

                                $team1_query = mysqli_query($conn,"SELECT * FROM `teams` WHERE `t_id` = '$team1'");
                                $team1_row = mysqli_fetch_assoc($team1_query);

                                $team2_query = mysqli_query($conn,"SELECT * FROM `teams` WHERE `t_id` = '$team2'");
                                $team2_row = mysqli_fetch_assoc($team2_query);

                                $matches[] = [
                                    'match_no'  => "Match " . $matchNo,
                                    'team1'     => $team1,
                                    'team2'     => $team2
                                ];

                                echo '
                                <div class="match-container">
                                    <h4 class="match-head">Match ' . $matchNo++ . '</h4>
                                    <div class="team-container">
                                        <div class="teams left-side">
                                            ' . (!empty($team1_row['t_logo']) ? 
                                                '<div class="logo"><img src="../assets/images/teams/' . $team1_row['t_logo'] . '" alt=""></div>' 
                                                : '<div class="logo"></div>'
                                            ) . '
                                            <div class="tname">' . $team1_row['t_name'] . '</div>
                                        </div>
                                        <label for="" class="vs">VS</label>
                                        <div class="teams right-side">
                                            ' . (!empty($team2_row['t_logo']) ? 
                                                '<div class="logo"><img src="../assets/images/teams/' . $team2_row['t_logo'] . '" alt=""></div>' 
                                                : '<div class="logo"></div>'
                                            ) . '
                                            <div class="tname">' . $team2_row['t_name'] . '</div>
                                        </div>
                                    </div>
                                    <div class="team-no">
                                        <div class="t-num">(Team ' . $team1_no . ')</div>
                                        <div class="t-num">(Team ' . $team2_no . ')</div>
                                    </div>
                                </div>
                                ';
                            }
                        }
                    } else if ($format == 'knockout') {
                        echo "<h2>Knockout Matches</h2>";
                        $roundNo = 1;
                        $matchCounter = 1;
                        $semiCounter = 1;

                        shuffle($teams);

                        while (count($teams) > 1) {
                            

                            if (count($teams) % 2 != 0) {
                                $teams[] = "BYE";
                            }

                            if (count($teams) == 2) {
                                echo "<h3>🏆 Final</h3>";
                            } elseif (count($teams) == 4) {
                                echo "<h3>⚔️ Semifinals</h3>";
                            } else {
                                echo "<h3>Round $roundNo</h3>";
                            }

                            $nextRoundTeams = [];

                            for ($i = 0; $i < count($teams); $i += 2) {
                                $team1 = $teams[$i];
                                $team2 = $teams[$i + 1];

                                $team1_no = $i + 1;
                                $team2_no = $i + 2;

                                if (count($teams) == 4) {
                                    $matchTitle = "Semifinal " . ($semiCounter++);
                                } elseif (count($teams) == 2) {
                                    $matchTitle = "Final";
                                } else {
                                    $matchTitle = "Match " . ($matchCounter++);
                                }

                                $team1_query = mysqli_query($conn,"SELECT * FROM `teams` WHERE `t_id` = '$team1'");
                                $team1_row = mysqli_fetch_assoc($team1_query);

                                $team2_query = mysqli_query($conn,"SELECT * FROM `teams` WHERE `t_id` = '$team2'");
                                $team2_row = mysqli_fetch_assoc($team2_query);

                                if(isset($team2_row)){
                                    $matches[] = [
                                        'round'     => $roundNo,
                                        'match_no'  => $matchTitle,
                                        'team1'     => $team1,
                                        'team2'     => $team2
                                    ];
                                }

                                echo '
                                <div class="match-container">
                                    <h4 class="match-head">' . $matchTitle . '</h4>
                                    <div class="team-container">
                                        <div class="teams left-side">
                                            ' . (!empty($team1_row['t_logo']) ? 
                                                '<div class="logo"><img src="../assets/images/teams/' . $team1_row['t_logo'] . '" alt=""></div>' 
                                                : '<div class="logo"></div>'
                                            ) . '                                        
                                            <div class="tname">' . (isset($team1_row['t_name']) ? $team1_row['t_name'] : $team1) . '</div>
                                        </div>
                                        <label for="" class="vs">VS</label>
                                        <div class="teams right-side">
                                            ' . (!empty($team2_row['t_logo']) ? 
                                                '<div class="logo"><img src="../assets/images/teams/' . $team2_row['t_logo'] . '" alt=""></div>' 
                                                : '<div class="logo"></div>'
                                            ) . '
                                            <div class="tname">' . (isset($team2_row['t_name']) ? $team2_row['t_name'] : $team2) . '</div>
                                        </div>
                                    </div>
                                    <div class="team-no">
                                        <div class="t-num">(Team ' . $team1_no . ')</div>
                                        <div class="t-num">(Team ' . $team2_no . ')</div>
                                    </div>
                                </div>
                                ';

                                $nextRoundTeams[] = "Winner of " . $matchTitle;
                            }

                            $teams = $nextRoundTeams;
                            $roundNo++;
                        }
                    }
                ?>


                </div>
                <div class="pls">
                    <div class="plus" onclick="shuffle()">
                        <div class="plus-icon"><svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M27.9997 12.6418C27.5921 9.70879 26.2315 6.9912 24.1274 4.90759C22.0234 2.82398 19.2926 1.48996 16.3558 1.11102C13.419 0.732074 10.439 1.32923 7.87501 2.81051C5.31098 4.29178 3.30509 6.57499 2.16634 9.30843M1.33301 2.64176V9.30843H7.99967M1.33301 15.9751C1.74061 18.9081 3.10123 21.6257 5.20528 23.7093C7.30933 25.7929 10.0401 27.1269 12.9769 27.5058C15.9137 27.8848 18.8936 27.2876 21.4577 25.8064C24.0217 24.3251 26.0276 22.0419 27.1663 19.3084M27.9997 25.9751V19.3084H21.333" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        let tournament = <?php echo json_encode($tournament); ?>;
        let matches = <?php echo json_encode($matches); ?>;
        let teams = '<?php echo json_encode($team_ids) ?>';

        //got ot previous page
        let goBack = ()=>{
            window.history.back();
        }

        //make animation-transition
        window.onload = () => {
            document.querySelectorAll('.right-side').forEach(el => {
                el.classList.add('show');
            });

            document.querySelectorAll('.left-side').forEach(el => {
                el.classList.add('show');
            });

            document.querySelectorAll('.vs').forEach(el => {
                el.classList.add('show');
            });

            document.querySelectorAll('.plus').forEach(el => {
                el.classList.add('show');
            });

            document.querySelectorAll('.match-head').forEach(el => {
                el.style.opacity = '1';
            });

            document.querySelectorAll('.t-num').forEach(el => {
                el.style.opacity = '1';
            });
        };

        //Shuffle matches
        let shuffle = ()=>{
            window.location.reload();
        }

        //save matches
        let save = () => {
            let formdata = new FormData();
            formdata.append('matches',JSON.stringify(matches));
            formdata.append('tournament',tournament);
            formdata.append('teams', teams);

            //add teams in tournament
            fetch('../Backend/add-tournament-teams.php',{
                body : formdata,
                method : 'POST'
            })
            .then(response => response.json())
            .then((data)=>{
                console.log(data)
            })
            .catch(error => console.log(error));

            //add matche between teams
            fetch('../Backend/make_match.php',{
                method : 'POST',
                body : formdata
            })
            .then(response => response.json())
            .then((data)=>{
                console.log(data);

                data.forEach(item => {
                    if (item.status === 200) {
                        window.location.href = '../dashboard.php?update=Live&sport=CRICKET';
                    } else {
                        console.warn(item.match + ' — ' + item.message);
                    }
                });

            })
            .catch(error=>console.log(error));
        }

        
        console.log(matches);

        // Disable right-click
  document.addEventListener('contextmenu', event => event.preventDefault());

  // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
  document.onkeydown = function(e) {
    if(e.keyCode == 123) return false; // F12
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
    if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
  }

  // Theme management for this page
    function initializeTheme() {
        // Check for saved theme preference or use system preference
        const currentTheme = localStorage.getItem('theme') || 
                            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        // Set the initial theme
        if (currentTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
        }
        
        // Listen for theme changes from other tabs/pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') {
                if (e.newValue === 'dark') {
                    document.body.setAttribute('data-theme', 'dark');
                } else {
                    document.body.removeAttribute('data-theme');
                }
            }
        });
        
        // Listen for custom events if your dashboard dispatches them
        window.addEventListener('themeChanged', function(e) {
            if (e.detail === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
            } else {
                document.body.removeAttribute('data-theme');
            }
        });
    }

    // Initialize theme when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
    });

    // Function to programmatically change theme if needed
    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        }
    }

    // Function to get current theme
    function getCurrentTheme() {
        return document.body.getAttribute('data-theme') || 'light';
    }
    </script>
</body>
</html>