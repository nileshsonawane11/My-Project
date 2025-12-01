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
            <div class="error-existe"></div>
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

                        // ✅ Add BYE if odd number of teams
                        if (count($teams) % 2 != 0) {
                            $teams[] = "BYE";
                        }

                        $byeGiven = []; // Track teams who already got BYE once

                        for ($i = 0; $i < count($teams) - 1; $i++) {
                            for ($j = $i + 1; $j < count($teams); $j++) {
                                $team1 = $teams[$i];
                                $team2 = $teams[$j];

                                $team1_no = $i + 1;
                                $team2_no = $j + 1;

                                // ✅ Skip BYE vs BYE
                                if ($team1 == "BYE" && $team2 == "BYE") continue;

                                // ✅ Handle BYE (only once per team)
                                if ($team1 == "BYE" && !in_array($team2, $byeGiven)) {
                                    $team2_query = mysqli_query($conn,"SELECT * FROM `teams` WHERE `t_id` = '$team2'");
                                    $team2_row = mysqli_fetch_assoc($team2_query);

                                    echo '
                                    <div class="match-container">
                                        <h4 class="match-head">Match ' . $matchNo++ . '</h4>
                                        <div class="team-container">
                                            <div class="teams left-side">
                                                ' . (!empty($team2_row['t_logo']) ? 
                                                    '<div class="logo"><img src="../assets/images/teams/' . $team2_row['t_logo'] . '" alt=""></div>' 
                                                    : '<div class="logo"></div>'
                                                ) . '
                                                <div class="tname">' . $team2_row['t_name'] . '</div>
                                            </div>
                                            <label for="" class="vs">VS</label>
                                            <div class="teams right-side">
                                                <div class="logo"></div>
                                                <div class="tname">BYE</div>
                                            </div>
                                        </div>
                                        <div class="team-no">
                                            <div class="t-num">(Team ' . $team2_no . ')</div>
                                            <div class="t-num">(Auto Win)</div>
                                        </div>
                                    </div>
                                    ';
                                    $byeGiven[] = $team2;
                                    continue;
                                }

                                if ($team2 == "BYE" && !in_array($team1, $byeGiven)) {
                                    $team1_query = mysqli_query($conn,"SELECT * FROM `teams` WHERE `t_id` = '$team1'");
                                    $team1_row = mysqli_fetch_assoc($team1_query);

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
                                                <div class="logo"></div>
                                                <div class="tname">BYE</div>
                                            </div>
                                        </div>
                                        <div class="team-no">
                                            <div class="t-num">(Team ' . $team1_no . ')</div>
                                            <div class="t-num">(Auto Win)</div>
                                        </div>
                                    </div>
                                    ';
                                    $byeGiven[] = $team1;
                                    continue;
                                }

                                // ✅ Normal Match (unchanged)
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
    $matches = [];
    $totalTeams = count($teams);

    // helper: fetch team row
    function getTeamRow($conn, $team) {
        return mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM teams WHERE t_id='$team' OR t_name='$team' LIMIT 1"));
    }

    if ($totalTeams < 2) {
        echo "<div class='match-container'><div class='tname'>Not enough teams to create a knockout bracket.</div></div>";
    } else {

        // ------------------------------------------------
        // STEP 1: Next Power of 2 + BYEs
        // ------------------------------------------------
        $nextPow = 1;
        while ($nextPow < $totalTeams) $nextPow <<= 1;
        $byesNeeded = $nextPow - $totalTeams;

        $byeTeams = array_slice($teams, 0, $byesNeeded);
        $qualifyingTeams = array_slice($teams, $byesNeeded);

        echo "<div class='match-container'><div class='tname'>Total Teams: <strong>$totalTeams</strong></div>";
        echo "<div class='tname'>Next Power of 2: <strong>$nextPow</strong> — BYEs needed: <strong>$byesNeeded</strong></div></div>";

        if (!empty($byeTeams)) {
            echo "<h3>BYEs (advance to next round)</h3>";
            foreach ($byeTeams as $bt) {
                $br = getTeamRow($conn, $bt);
                echo "<div class='match-container'><div class='team-container'>
                        <div class='teams left-side'>" 
                        . (!empty($br['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$br['t_logo'].'"></div>' : '<div class="logo"></div>') .
                        '<div class="tname">'.($br['t_name'] ?? $bt).'</div></div>
                        <label class="vs">VS</label>
                        <div class="teams right-side"><div class="logo"></div><div class="tname">BYE</div></div>
                    </div></div>';
            }
        }

        // ------------------------------------------------
        // STEP 2: Round 1 (Qualifiers)
        // ------------------------------------------------
        $round = 1;
        $matchCounter = 1;  // RESET EVERY ROUND
        $currentRoundTeams = [];

        if (!empty($qualifyingTeams)) {
            echo "<h3>Round $round (Qualifiers)</h3>";
            $qTeams = array_values($qualifyingTeams);

            for ($i = 0; $i < count($qTeams); $i += 2) {

                $t1 = $qTeams[$i];
                $t2 = $qTeams[$i+1] ?? "BYE";

                $t1_row = ($t1 !== "BYE") ? getTeamRow($conn, $t1) : null;
                $t2_row = ($t2 !== "BYE") ? getTeamRow($conn, $t2) : null;

                echo '
                <div class="match-container">
                    <h4 class="match-head">Match '.$matchCounter.'</h4>
                    <div class="team-container">
                        <div class="teams left-side">' 
                            . (!empty($t1_row['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$t1_row['t_logo'].'"></div>' : '<div class="logo"></div>')
                            . '<div class="tname">'.($t1_row['t_name'] ?? $t1).'</div></div>
                        <label class="vs">VS</label>
                        <div class="teams right-side">'
                            . (!empty($t2_row['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$t2_row['t_logo'].'"></div>' : '<div class="logo"></div>')
                            . '<div class="tname">'.($t2_row['t_name'] ?? $t2).'</div></div>
                    </div>
                </div>';

                if ($t1 !== "BYE" && $t2 !== "BYE" && !preg_match('/^Winner of/i', $t1) && !preg_match('/^Winner of/i', $t2)) {
                    $matches[] = [
                        'round' => $round,
                        'match_no' => "Match $matchCounter",
                        'team1' => $t1,
                        'team2' => $t2
                    ];
                }

                if ($t1 === "BYE") $currentRoundTeams[] = $t2;
                elseif ($t2 === "BYE") $currentRoundTeams[] = $t1;
                else $currentRoundTeams[] = "Winner of Match $matchCounter";

                $matchCounter++;
            }
        }

        // Add BYE teams directly into next round
        foreach ($byeTeams as $bt) $currentRoundTeams[] = $bt;

        // ------------------------------------------------
        // STEP 3: NEXT ROUNDS (Match numbers reset EVERY ROUND)
        // ------------------------------------------------
        $round++;

        while (count($currentRoundTeams) > 1) {

            $numTeams = count($currentRoundTeams);

            $label = ($numTeams == 2) ? "Final" :
                    (($numTeams == 4) ? "Semifinals" : "Round $round");

            echo "<h3>" 
                . (($label == "Final") ? "🏆 Final" : (($label == "Semifinals") ? "⚔️ Semifinals" : $label)) 
                . "</h3>";

            $matchCounter = 1;  // RESET FOR NEW ROUND
            $nextRoundTeams = [];

            for ($i = 0; $i < $numTeams; $i += 2) {

                $t1 = $currentRoundTeams[$i];
                $t2 = $currentRoundTeams[$i+1] ?? "BYE";

                $t1_row = (!preg_match('/^Winner of/i', $t1) && $t1 !== "BYE") ? getTeamRow($conn, $t1) : null;
                $t2_row = (!preg_match('/^Winner of/i', $t2) && $t2 !== "BYE") ? getTeamRow($conn, $t2) : null;

                $disp1 = ($t1 === "BYE") ? "BYE" : ($t1_row['t_name'] ?? $t1);
                $disp2 = ($t2 === "BYE") ? "BYE" : ($t2_row['t_name'] ?? $t2);

                if ($label == "Final") {
                    $matchTitle = "Final";
                }
                else if ($label == "Semifinals") {
                    $matchTitle = "Semifinal " . $matchCounter;
                }
                else {
                    $matchTitle = "Match " . $matchCounter;
                }

                echo '
                <div class="match-container">
                    <h4 class="match-head">'.$matchTitle.'</h4>
                    <div class="team-container">
                        <div class="teams left-side">' 
                            . (!empty($t1_row['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$t1_row['t_logo'].'"></div>' : '<div class="logo"></div>')
                            . '<div class="tname">'.$disp1.'</div></div>
                        <label class="vs">VS</label>
                        <div class="teams right-side">'
                            . (!empty($t2_row['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$t2_row['t_logo'].'"></div>' : '<div class="logo"></div>')
                            . '<div class="tname">'.$disp2.'</div></div>
                    </div>
                </div>';


                if ($t1 === "BYE") $nextRoundTeams[] = $t2;
                elseif ($t2 === "BYE") $nextRoundTeams[] = $t1;
                else $nextRoundTeams[] = "Winner of $matchTitle";

                $matchCounter++;
            }

            $currentRoundTeams = $nextRoundTeams;
            $round++;
        }

        // ------------------------------------------------
        // STEP 4: FINAL WINNER SLOT
        // ------------------------------------------------
        if (count($currentRoundTeams) === 1) {
            echo '<div class="match-container"><div class="tname">Champion Slot: <strong>' . $currentRoundTeams[0] . '</strong></div></div>';
        }
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
        let matches = <?php echo json_encode($matches) ?? []; ?>;
        let teams = '<?php echo json_encode($team_ids) ?>';
        let error = document.querySelector('.error-existe');

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
                        error.innerText = item.message;
                    }
                });

            })
            .catch(error=>console.log(error));
        }

        
        console.log(matches);

        // Disable right-click
  //document.addEventListener('contextmenu', event => event.preventDefault());

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