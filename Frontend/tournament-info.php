<?php
    session_start();
    if(!isset($_SESSION['user'])){
        header('location: ./front-page.php');
        exit();
    }

    if($_SESSION['role'] == "User"){
        header('location: ../dashboard.php?update="live"&sport="CRICKET"');
        exit();
    }

    include '../config.php';

    $tournament_id = $_GET['t'];

    if (empty($tournament_id)) {
        header("Location: ../dashboard.php?update=Live&sport=CRICKET"); // Change 'index.php' to your actual file
        exit();
    }

    if (isset($_POST['confirm']) && isset($_POST['match_id'])) {

        $delete = mysqli_query($conn, "DELETE FROM tournaments WHERE tournament_id = '$tournament_id'");

        if ($delete) {
            echo "<script>window.history.back();</script>";
        } else {
            echo "<script>alert('Failed to delete match');</script>";
        }
    }


    // Example: assume $conn is your database connection
    $query = mysqli_query($conn, "SELECT * FROM tournaments WHERE tournament_id = '$tournament_id'");
    $result = mysqli_fetch_assoc($query);

    if(mysqli_num_rows($query) == 0) {
        echo "<script>window.history.back();</script>";
        exit;
    }

    $format = $result['tournament_format'];
    $owner = $result['created_by'];
    $sport = $result['sport_id'];
    $venue = $result['city'];
    $tournament_name = $result['tournament_name'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Details</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select: none;
            scrollbar-width: none;
        }

        /* Theme Variables */
        :root {
            --primary-color: rgba(209, 34, 31, 1);
            --primary-light: rgba(209, 34, 31, 0.8);
            --primary-dark: rgba(160, 25, 23, 1);
            --primary-light-transparent: rgba(209, 34, 31, 0.1);
            --primary-dark-transparent: rgba(160, 25, 23, 0.1);
            --background: #ffffff;
            --card-bg: #ffffff;
            --text-color: #333333;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
            --svg-fill: #333333;
            --border-color: #ddd;
            --invert: invert(0);
        }

        /* Dark theme variables */
        [data-theme="dark"] {
            --background: #121212;
            --card-bg: #1e1e1e;
            --text-color: #ffffff;
            --light-bg: #2d3748;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            --svg-fill: #ffffff;
            --border-color: #4a5568;
            --primary-light-transparent: rgba(209, 34, 31, 0.2);
            --primary-dark-transparent: rgba(160, 25, 23, 0.2);
            --invert: invert(1);
        }

        :root {
    --primary-color: rgba(209,34,31,1);
    --primary-light: rgba(209,34,31,0.8);
    --primary-dark: rgba(160,25,23,1);
    --background: #fff;
    --card-bg: #fff;
    --text-dark: #000;
    --text-light: #333;
    --border-color: #e0e0e0;
    --shadow: 0 4px 15px rgba(0,0,0,0.1);
    --team-bg: #eee;
    --logo-bg: #d9d9d9;
    --gradient: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
}

[data-theme="dark"] {
    --background: #121212;
    --card-bg: #1e1e1e;
    --text-dark: #fff;
    --text-light: #e0e0e0;
    --border-color: #333;
    --shadow: 0 4px 15px rgba(0,0,0,0.3);
    --team-bg: #3d3d3d;
    --logo-bg: #4d4d4d;
}

        svg path {
            fill: var(--text-color);
        }
        body {
            min-height: 100vh;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 800px;
            background: var(--card-bg);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .header {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            padding: 20px;
            color: white;
            position: relative;
        }

        .return {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .return svg {
            cursor: pointer;
            transition: var(--transition);
            fill: white;
        }
        .return svg path{
            cursor: pointer;
            transition: var(--transition);
            fill: white;
        }

        .edit-toggle svg path {
            fill: none;
        }
        /* For <input type="date"> */
        input[type="date"]::-webkit-calendar-picker-indicator {
        filter: var(--invert);    /* makes it white */
        cursor: pointer;
        }

        /* For <input type="time"> */
        input[type="time"]::-webkit-calendar-picker-indicator {
        filter: var(--invert);    /* makes it white */
        cursor: pointer;
        }

        .return svg:hover {
            transform: translateX(-3px);
        }

        .edit-toggle svg {
            fill: none;
            stroke: white;
            transition: var(--transition);
        }

        .edit-toggle:hover svg {
            stroke: rgba(255,255,255,0.8);
        }

        /* Match Teams Section */
        .teams-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 30px;
            background: var(--card-bg);
        }

        .team {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 40%;
        }

        .team-logo-container {
            position: relative;
            width: 100px;
            height: 100px;
            margin-bottom: 15px;
        }

        .team-logo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--card-bg);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background: var(--card-bg);
        }

        .team-logo-edit {
            position: absolute;
            bottom: -5px;
            right: -5px;
            background: var(--card-bg);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .team-logo-edit:hover {
            background: var(--light-bg);
        }

        .team-logo-edit svg {
            width: 20px;
            height: 20px;
            fill: var(--svg-fill);
        }

        .team-name {
            font-size: 1.3rem;
            font-weight: 600;
            text-align: center;
            width: 100%;
            border: none;
            background: transparent;
            outline: none;
            text-align: center;
            color: var(--text-color);
        }

        .vs {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0 20px;
        }

        /* Match Details Section */
        .form-section {
            padding: 30px;
            background: var(--card-bg);
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            flex: 1;
            position: relative;
        }
        .pencil {
            position: absolute;
            top: 10px;
            right: 0;
            display: none;
        }
        .name-info {
            width: 90%;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--light-bg);
            color: var(--text-color);
        }

        .form-input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-light-transparent);
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background-color: var(--light-bg);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            color: var(--text-color);
        }

        /* Edit mode styles */
        .edit-mode .form-input,
        .edit-mode .form-select,
        .edit-mode .team-name {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        .edit-mode .form-input:focus,
        .edit-mode .form-select:focus,
        .edit-mode .team-name:focus {
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 3px var(--primary-dark-transparent);
        }

        /* Hide file inputs */
        .file-input {
            display: none;
        }
        .fixed-info {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }
        .logout-btn,
        .save-btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 12px 30px;
            background-color: var(--primary-dark);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin: 20px;
            box-shadow: 0 4px 12px rgba(209, 34, 31, 0.2);
        }

        .logout-btn:hover,
        .save-btn:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(209, 34, 31, 0.3);
        }
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(3px);
        }

        .popup-box {
            background: var(--card-bg);
            padding: 30px;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 400px;
            box-shadow: var(--card-shadow);
            animation: popIn 0.3s ease-out;
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .popup-message {
            margin-bottom: 20px;
            font-size: 1.1rem;
            line-height: 1.5;
            color: var(--text-color);
        }

        .popup-input {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--light-bg);
            color: var(--text-color);
        }

        .popup-input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-light-transparent);
        }

        .popup-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .popup-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .popup-btn.cancel {
            background-color: var(--light-bg);
            color: var(--text-color);
            border: none;
        }

        .popup-btn.cancel:hover {
            background-color: var(--border-color);
        }

        .popup-btn.confirm {
            background-color: var(--primary-dark);
            color: white;
            border: none;
        }

        .popup-btn.confirm:hover {
            background-color: var(--primary-color);
        }

        .password {
            display: flex;
            justify-content: center;
            position: relative;
        }
        #editToggle1 {
                position: absolute;
                top: 15%;
                right: 0;
                transform: translateY(0);
                position: absolute;
                    }
        .pass {
            border: none;
            outline: none;
            width: 100%;
            height: 100%;
            background-color: transparent;
            font-size: 20px;
            color: var(--text-color);
        }

        .pass-el {
            height: 35px;
            width: 120px;
            margin-top: 30px;
            position: relative;
        }
        .officials-frame{
            position: fixed;
            bottom: -100%;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            transition: bottom 0.8s ease;
            z-index: 999;
        }
        .officials-frame.active{
            bottom: 0;
        }
        .error{
            display: none;
            color: var(--primary-color); 
            width:100%;
            font-size:14px;
            margin: 5px;
        }

        .slots {
            height: 37px;
            width: 100px;
            background-color: #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 11px;
        }
        .matches {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 60px;
    align-items: center;
}

.match-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
}

.match-head {
    font-size: 18px;
    font-weight: 600;
}

.team-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
    max-width: 500px;
    gap: 20px;
}

.teams {
    width: 120px;
    height: 150px;
    background: var(--team-bg);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;
    box-shadow: var(--shadow);
    transition: all 0.5s ease-in-out;
}

.logo {
    width: 75px;
    height: 75px;
    background: var(--logo-bg);
    border-radius: 50%;
    overflow: hidden;
}

.logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.tname {
    font-size: 16px;
    text-align: center;
    word-wrap: break-word;
}

.vs {
    font-size: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.team-no {
    width: 100%;
    display: flex;
    padding: 0 30px;
    justify-content: space-between;
    max-width: 500px;
}

.t-num {
    font-size: 14px;
    color: var(--text-light);
}

/* Animations */
.show {
    opacity: 1;
    transform: translate(0,0);
    transition: all 0.5s ease-in-out;
}

/* Responsive */
@media (min-width:601px){
    .matches { width: 70%; }
    .team-container, .team-no { max-width: 600px; }
}

@media (max-width:600px){
    .matches { 
        width: 90%; 
    }
    .team-container, .team-no {
        max-width: 100%; 
        align-items: center; 
        gap: 10px; 
        padding: 0 20px;
     }
    .vs { 
        transform: translateY(0); 
    }
    .team-no{
        padding: 0 40px;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="return">
                <svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z"/>
                </svg>
                
                    <?php 
                        $sql = "SELECT * FROM matches WHERE tournament = '$tournament_id' ORDER BY match_name";
                        $query = mysqli_query($conn,$sql);
                        $count_matches = mysqli_num_rows($query);
                        if($count_matches == 0){
                    ?>
                        <div class="slots">
                            <div class="make-slots">Make Slots</div>
                        </div>
                    <?php } ?>
                
                <div class="edit-toggle" id="editToggle">
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23.3333 7.29182L27.7083 11.6668M29.7281 9.60328C30.3025 9.02892 30.6252 8.24992 30.6252 7.43766C30.6252 6.62539 30.3025 5.84639 29.7281 5.27203C29.1538 4.69767 28.3748 4.375 27.5625 4.375C26.7502 4.375 25.9712 4.69767 25.3969 5.27203L13.125 17.5002V21.8752H17.5L29.7281 9.60328Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <h2 style="text-align: center;">Tournament Details</h2>
            <p style="text-align: center; font-size: 1.2rem;">(<?php echo $result['tournament_name']; ?>)</p>
        </div>
        
        <!-- Teams Display -->
<?php
if ($format == 'knockout') {
    echo "<h2>Knockout Matches</h2>";

    // Fetch all teams
    $teams = [];
    $teamRes = mysqli_query($conn, "SELECT team_id FROM tournament_teams WHERE tournament_id='$tournament_id'");
    while ($t = mysqli_fetch_assoc($teamRes)) $teams[] = $t['team_id'];

    // Cache team details
    $teamCache = [];
    $teamDataRes = mysqli_query($conn, "SELECT * FROM teams");
    while ($tr = mysqli_fetch_assoc($teamDataRes)) $teamCache[$tr['t_id']] = $tr;

    // Fetch all existing matches
    $matchesRes = mysqli_query($conn, "SELECT * FROM matches WHERE tournament='$tournament_id' ORDER BY round ASC, match_id ASC");
    $allMatches = [];
    while ($m = mysqli_fetch_assoc($matchesRes)) $allMatches[] = $m;

    // Group by round
    $matchesByRound = [];
    foreach ($allMatches as $m) {
        $matchesByRound[$m['round']][] = $m;
    }

    ksort($matchesByRound);
    $roundNo = 1;
    $totalRounds = ceil(log(count($teams), 2));

    // ===== Main Tournament Loop =====
    while (true) {
        $roundMatches = $matchesByRound[$roundNo] ?? [];
        $roundTeams = [];

        // Round title logic
        $roundTitle = "Round $roundNo";
        if ($roundNo == $totalRounds - 1) $matchTitle = $roundTitle = "Semifinal";
        elseif ($roundNo == $totalRounds) $matchTitle = $roundTitle = "Final";

        if (str_contains($roundTitle, 'Round')) {
            $matchTitle = "Match";
        }

        // --- Display Matches ---
        if (!empty($roundMatches)) {
            echo "<br><br><h3>$roundTitle</h3>";
            $matchCounter = 0;

            foreach ($roundMatches as $m) {
                $t1 = $m['team_1'];
                $t2 = $m['team_2'];
                $t1Data = $teamCache[$t1] ?? ['t_name' => 'TBD', 't_logo' => ''];
                $t2Data = ($t2 && $t2 != "BYE" && $t2 != "TBD") ? ($teamCache[$t2] ?? ['t_name' => 'TBD', 't_logo' => '']) : ['t_name' => $t2, 't_logo' => ''];

                echo '
                <div class="match-container">
                    <h4 class="match-head">'.$matchTitle.' '.(++$matchCounter).'</h4>
                    <div class="team-container">
                        <div class="teams left-side">
                            '.(!empty($t1Data['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$t1Data['t_logo'].'" alt=""></div>' : '<div class="logo"></div>').'
                            <div class="tname">'.$t1Data['t_name'].'</div>
                        </div>
                        <label class="vs">VS</label>
                        <div class="teams right-side">
                            '.(!empty($t2Data['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$t2Data['t_logo'].'" alt=""></div>' : '<div class="logo"></div>').'
                            <div class="tname">'.($t2Data['t_name'] ?? 'TBD').'</div>
                        </div>
                    </div>
                    <div class="team-no">
                        <div class="t-num">('.($t1Data['t_name']!="TBD"?array_search($t1,$teams)+1:'TBD').')</div>
                        <div class="t-num">('.(($t2Data['t_name']!="TBD" && $t2Data['t_name']!="BYE")?array_search($t2,$teams)+1:'TBD').')</div>
                    </div>
                </div>';

                // Determine winner or bye
                $scoreLog = json_decode($m['score_log'], true);
                if (!empty($scoreLog['winner'])) {
                    $roundTeams[] = $scoreLog['winner'];
                } elseif ($t2 == "BYE" || $t2 == "TBD" || empty($t2)) {
                    $roundTeams[] = $t1;
                }
            }
        }

        // --- Handle BYE teams in 1st round only ---
        if ($roundNo == 1) {
            $matchedTeams = [];
            foreach ($roundMatches as $m) {
                $matchedTeams[] = $m['team_1'];
                $matchedTeams[] = $m['team_2'];
            }
            $byeTeams = array_diff($teams, $matchedTeams);
            $matchCounter = count($roundMatches);
            foreach ($byeTeams as $bt) {
                $btData = $teamCache[$bt] ?? ['t_name' => 'TBD', 't_logo' => ''];
                echo '
                <div class="match-container">
                    <h4 class="match-head">'.$matchTitle.' '.(++$matchCounter).'</h4>
                    <div class="team-container">
                        <div class="teams left-side">
                            '.(!empty($btData['t_logo']) ? '<div class="logo"><img src="../assets/images/teams/'.$btData['t_logo'].'" alt=""></div>' : '<div class="logo"></div>').'
                            <div class="tname">'.$btData['t_name'].'</div>
                        </div>
                        <label class="vs">VS</label>
                        <div class="teams right-side"><div class="logo"></div><div class="tname">BYE</div></div>
                    </div>
                    <div class="team-no">
                        <div class="t-num">('.(array_search($bt,$teams)+1).')</div>
                        <div class="t-num">(TBD)</div>
                    </div>
                </div>';
                $roundTeams[] = $bt;
            }
        }

        // --- Schedule Next Round (auto-fill missing matches) ---
        if (!empty($roundTeams) && count($roundTeams) > 1) {
            $nextRoundNo = $roundNo + 1;
            $existingNextRound = mysqli_query($conn, "SELECT COUNT(*) AS c FROM matches WHERE tournament='$tournament_id' AND round='$nextRoundNo'");
            $existCount = mysqli_fetch_assoc($existingNextRound)['c'];

            if ($existCount == 0) {
                // Skip bye scheduling for semifinal if only 3 teams
                if ($nextRoundNo == $totalRounds - 1 && count($roundTeams) == 3) {
                    $roundTeams[] = "BYE";
                }

                $matchCounter = 1;
                for ($i = 0; $i < count($roundTeams); $i += 2) {
                    $t1 = $roundTeams[$i];
                    $t2 = $roundTeams[$i+1] ?? 'BYE';

                    if ($roundTitle == "Semifinal" && $t2 == "BYE") continue;

                    $nextTitle = ($nextRoundNo == $totalRounds - 1) ? "Semifinal" :
                                 (($nextRoundNo == $totalRounds) ? "Final" : "Round $nextRoundNo");
                    $matchName = "$nextTitle ".$matchCounter++." | ".$tournament_name;

                    $id = hash('sha256', uniqid(microtime(true), true));

                    mysqli_query($conn, "INSERT INTO matches (match_id, sport_id, status, tournament, team_1, team_2, round, match_name, created_by)
                        VALUES ('$id','$sport','Upcoming','$tournament_id','$t1','$t2','$nextRoundNo','$matchName','$owner')");

                    $matchesByRound[$nextRoundNo][] = [
                        'team_1'=>$t1,
                        'team_2'=>$t2,
                        'round'=>$nextRoundNo,
                        'match_name'=>$matchName,
                        'score_log'=>json_encode([])
                    ];
                }
            }
        }

        // --- End condition ---
        if (count($roundTeams) == 1 && $roundTitle == "Final") {
            $finalWinner = $roundTeams[0];
            $winnerData = $teamCache[$finalWinner] ?? ['t_name'=>'Unknown'];
            echo "<br><br><h3>🏆 Tournament Winner: ".$winnerData['t_name']."</h3>";
            mysqli_query($conn, "UPDATE tournaments SET winner='$finalWinner' WHERE tournament_id='$tournament_id'");
            break;
        }

        if (empty($roundTeams) || count($roundTeams) <= 1) break;
        $roundNo++;
    }
}


$matchNo = 1;
$matchSlots = []; // 🟢 Store all slots here

if ($format == 'league') {
    echo "<h2>League Matches</h2>";

    // ✅ Step 1: Get all teams for this tournament
    $team_query = mysqli_query($conn, "SELECT team_id FROM tournament_teams WHERE tournament_id = '$tournament_id'");
    $teams = [];
    while ($row = mysqli_fetch_assoc($team_query)) {
        $teams[] = $row['team_id'];
    }

    $numTeams = count($teams);
    $hasBye = ($numTeams % 2 != 0);

    // ✅ Step 2: Add a temporary BYE slot if teams are odd
    if ($hasBye) {
        $teams[] = 'BYE';
        $numTeams++;
    }

    // ✅ Step 3: Get already scheduled matches
    $match_query = mysqli_query($conn, "SELECT * FROM matches WHERE tournament = '$tournament_id' ORDER BY match_id ASC");
    $scheduledMatches = [];
    while ($row = mysqli_fetch_assoc($match_query)) {
        $scheduledMatches[] = $row;
    }

    // ✅ Step 4: Loop through all possible match combinations
    for ($i = 0; $i < $numTeams - 1; $i++) {
        for ($j = $i + 1; $j < $numTeams; $j++) {
            $team1 = $teams[$i];
            $team2 = $teams[$j];
            $matchName = "Match " . $matchNo;

            // 🟠 Case 1: Handle BYE display only
            if ($team1 == 'BYE' || $team2 == 'BYE') {
                $byeTeam = ($team1 == 'BYE') ? $team2 : $team1;
                $bye_query = mysqli_query($conn, "SELECT * FROM teams WHERE t_id = '$byeTeam'");
                $bye_row = mysqli_fetch_assoc($bye_query);

                $matchSlots[] = [
                    'match_name' => $matchName . ' (Bye)',
                    'team1_name' => $bye_row['t_name'],
                    'team2_name' => 'BYE',
                    'match_id'   => null
                ];

                echo '
                <div class="match-container">
                    <h4 class="match-head">' . $matchName . ' (Bye)</h4>
                    <div class="team-container">
                        <div class="teams left-side">
                            ' . (!empty($bye_row['t_logo'])
                                ? '<div class="logo"><img src="../assets/images/teams/' . $bye_row['t_logo'] . '" alt=""></div>'
                                : '<div class="logo"></div>') . '
                            <div class="tname">' . $bye_row['t_name'] . '</div>
                        </div>
                        <label for="" class="vs">VS</label>
                        <div class="teams right-side">
                            <div class="logo"></div>
                            <div class="tname">BYE</div>
                        </div>
                    </div>
                    <div class="team-no">
                        <div class="t-num">(Team ' . (($team1 == 'BYE') ? $j + 1 : $i + 1) . ')</div>
                        <div class="t-num">(Bye)</div>
                    </div>
                </div>';
                $matchNo++;
                continue;
            }

            // 🟢 Case 2: Normal matches
            $team1_query = mysqli_query($conn, "SELECT * FROM teams WHERE t_id = '$team1'");
            $team1_row = mysqli_fetch_assoc($team1_query);

            $team2_query = mysqli_query($conn, "SELECT * FROM teams WHERE t_id = '$team2'");
            $team2_row = mysqli_fetch_assoc($team2_query);

            // Check if this match exists in DB
            $matchId = null;
            $exists = false;
            foreach ($scheduledMatches as $m) {
                if (
                    ($m['team_1'] == $team1 && $m['team_2'] == $team2) ||
                    ($m['team_1'] == $team2 && $m['team_2'] == $team1)
                ) {
                    $exists = true;
                    $matchId = $m['match_id'];
                    break;
                }
            }

            // 🟢 Store this match slot in array
            $matchSlots[] = [
                'match_name' => $matchName,
                'team1_name' => $team1_row['t_name'],
                'team2_name' => $team2_row['t_name'],
                'match_id'   => $matchId
            ];

            // 🟢 Display match
            echo '
            <div class="match-container">
                <h4 class="match-head">' . $matchName . ($exists ? '' : ' (Not Scheduled)') . '</h4>
                <div class="team-container">
                    <div class="teams left-side">
                        ' . (!empty($team1_row['t_logo'])
                            ? '<div class="logo"><img src="../assets/images/teams/' . $team1_row['t_logo'] . '" alt=""></div>'
                            : '<div class="logo"></div>') . '
                        <div class="tname">' . $team1_row['t_name'] . '</div>
                    </div>
                    <label for="" class="vs">VS</label>
                    <div class="teams right-side">
                        ' . (!empty($team2_row['t_logo'])
                            ? '<div class="logo"><img src="../assets/images/teams/' . $team2_row['t_logo'] . '" alt=""></div>'
                            : '<div class="logo"></div>') . '
                        <div class="tname">' . $team2_row['t_name'] . '</div>
                    </div>
                </div>
                <div class="team-no">
                    <div class="t-num">(Team ' . ($i + 1) . ')</div>
                    <div class="t-num">(Team ' . ($j + 1) . ')</div>
                </div>
            </div>';
            $matchNo++;
        }
    }
}

// 🧾 Debug print to verify array (optional)
// echo "<pre>";
// print_r($matchSlots);
// echo "</pre>";
?>

        <!-- Match Details Form -->
        <div class="form-section">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">City/Town</label>
                    <input type="text" class="form-input" value="<?php echo $result['city']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Ground</label>
                    <input type="text" class="form-input" value="<?php echo $result['ground']; ?>" disabled>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-input" value="<?php echo $result['tournament_date']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <input type="time" class="form-input" value="<?php echo $result['tournament_time']; ?>" disabled>
                </div>
            </div>
            
            <div class="fixed-info">
        </div>
        <div class="error" id="error-datetime"></div>
        <div style="text-align: center;margin-top: 30px;display: flex;flex-direction: column; align-items: center;">
        <button class="save-btn">SAVE CHANGES</button>        
        <button class="logout-btn">DELETE TOURNAMENT</button>
            </div>
    </div>
    <form method="post" class="popup-overlay" id="popupOverlay">
        <div class="popup-box" id="popupBox">
            <input type="hidden" name="match_id" value=""> <!-- ✅ This is required -->

            <p class="popup-message" id="popupMessage"></p>
            <div class="popup-actions">
                <button type="button" class="popup-btn cancel" id="cancelBtn">Cancel</button>
                <button type="submit" class="popup-btn confirm" id="confirmBtn" name="confirm">Confirm</button>
            </div>
        </div>
    </form>
    <iframe src="./select-officials.php?p=Scorers" frameborder="0" class="officials-frame"></iframe>
    <script>
        // Edit Toggle Functionality
        const toggle = document.getElementById("editToggle");
        const inputs = document.querySelectorAll("input:not(.file-input)");
        const save_btn = document.querySelector(".save-btn");
        let edit = false;
        

        toggle.addEventListener("click", () => {
            edit = !edit;

            if (edit) {
                // Switch to edit icon
                toggle.innerHTML = '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.3227 15.3097L13.125 17.5002V21.8752H17.5L19.6773 19.6906M22.594 16.7593L29.7252 9.60328C30.2996 9.02892 30.6222 8.24992 30.6222 7.43766C30.6222 6.62539 30.2996 5.84639 29.7252 5.27203C29.1508 4.69767 28.3719 4.375 27.5596 4.375C26.7473 4.375 25.9683 4.69767 25.394 5.27203L18.2292 12.412M23.3333 7.29182L27.7083 11.6668M4.375 4.37516L30.625 30.6252" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
                
                // Enable inputs
                inputs.forEach(el => el.disabled = false);

                save_btn.style.display = "block";
                
                // Add edit mode class
                document.querySelector('.container').classList.add('edit-mode');
            } else {
                // Switch back to edit toggle icon
                toggle.innerHTML = '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23.3333 7.29182L27.7083 11.6668M29.7281 9.60328C30.3025 9.02892 30.6252 8.24992 30.6252 7.43766C30.6252 6.62539 30.3025 5.84639 29.7281 5.27203C29.1538 4.69767 28.3748 4.375 27.5625 4.375C26.7502 4.375 25.9712 4.69767 25.3969 5.27203L13.125 17.5002V21.8752H17.5L29.7281 9.60328Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                
                // Disable inputs
                inputs.forEach(el => el.disabled = true);

                save_btn.style.display = "none";
                
                // Remove edit mode class
                document.querySelector('.container').classList.remove('edit-mode');
            }
        });

        // Back button functionality
        function goBack() {
            // Implement your back navigation logic here
            window.history.back();
        }

        const popupOverlay = document.getElementById("popupOverlay");
        const popupMessage = document.getElementById("popupMessage");
        const cancelBtn = document.getElementById("cancelBtn");

        // Messages for logout
        const messages = {
        "logout": "Are you sure you want to Delete the Tournament?"
        };

        const logoutBtn = document.querySelector(".logout-btn");

        logoutBtn.addEventListener("click", () => {
        const className = "logout";  // We know this is logout

        popupMessage.innerHTML = messages[className] || "Are you sure?";
        popupOverlay.style.display = "flex";


            cancelBtn.onclick = () => {
                popupOverlay.style.display = "none";
            };
        });


        save_btn.addEventListener("click", function() {
            
            fetch('../Backend/update_match.php', {
                method: 'POST',
                body: formdata
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);

                document.querySelectorAll('[id^="error-"]').forEach((el) => {
                    el.innerHTML = '';
                    el.style.display = 'none';
                });

                if (data.status == 409) {
                    let el = document.getElementById(`error-${data.field}`);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                } else if (data.status == 200) {  

                }
            })
            .catch(error => console.log(error));
        });

        // Disable right-click
//   document.addEventListener('contextmenu', event => event.preventDefault());

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