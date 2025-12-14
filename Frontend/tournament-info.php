<?php
    session_start();
    // if(!isset($_SESSION['user'])){
    //     header('location: ./front-page.php');
    //     exit();
    // }

    // if($_SESSION['role'] == "User"){
    //     header('location: ../dashboard.php?update="live"&sport="CRICKET"');
    //     exit();
    // }

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

    $sportList = [
        "CRICKET"         => 1,
        "VOLLEYBALL"      => 2,
        "KABADDI"         => 3,
        "KHO-KHO"         => 4,
        "FOOTBALL"        => 5,
        "BADMINTON"       => 6,
        "TABLE-TENNIS"    => 7,
        "CHESS"           => 8,
        "WEIGHT-LIFTING"  => 9,
        "BASKETBALL"      => 10
    ];
    $sportList = array_flip($sportList);

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
    <meta name="google-adsense-account" content="ca-pub-4540243680881407">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4540243680881407" crossorigin="anonymous"></script>
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
    margin: 15px 0;
    padding: 15px;
    border-radius: 15px;
    background: #fff5eb;
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
    cursor:pointer;
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
.m_heading{
    text-align: center;
    margin: 25px;
}
/* ✅ League Points Table Styling */
.league-table-wrapper {
    width: 100%;
    overflow-x: auto;
    margin: 20px 0;
}

/* ✅ Main Table */
table {
    width: 100%;
    border-collapse: collapse;
    background: #0e1624;
    border-radius: 12px;
    overflow: hidden;
    color: #ffffff;
    font-family: 'Segoe UI', sans-serif;
    box-shadow: 0 0 18px rgba(0, 0, 0, 0.5);
}

/* ✅ Header Row */
table th {
    background: linear-gradient(45deg, #2196f3, #00c6ff);
    color: #ffffff;
    padding: 14px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

/* ✅ Body Cells */
table td {
    padding: 12px;
    text-align: center;
    font-size: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* ✅ Alternate Rows */
table tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.04);
}

/* ✅ Hover Effect */
table tr:hover {
    background: rgba(0, 198, 255, 0.2);
    transition: 0.2s ease-in-out;
}

/* ✅ Rank Highlight */
table td:first-child {
    font-weight: bold;
    color: #ffd54f;
}

/* ✅ Team Name Emphasis */
table td:nth-child(2) {
    font-weight: 600;
    color: #90caf9;
}

/* ✅ NRR Color */
table td:last-child {
    color: #80cbc4;
}

/* ✅ Table Heading */
h2 {
    text-align: center;
    margin-bottom: 15px;
    color: #000000ff;
    font-size: 24px;
    letter-spacing: 1px;
}
.league-winner-box {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    text-align: center;
    font-family: 'Arial', sans-serif;
}

.league-winner-title {
    font-size: 28px;
    margin-bottom: 20px;
    color: #ff9800;
}

.winner-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.winner-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.2);
}

.winner-logo img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #ff9800;
}

.winner-info {
    text-align: center;
}

.winner-name {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
}

.winner-points, .winner-nrr {
    font-size: 16px;
    color: #555;
    margin: 3px 0;
}

.winner-points span, .winner-nrr span {
    font-weight: bold;
    color: #000;
}

/* ✅ Mobile Responsive */
@media (max-width: 600px) {
    table th, table td {
        padding: 8px;
        font-size: 12px;
    }

    h2 {
        font-size: 18px;
    }
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
            <?php if(isset($_SESSION['user']) && $_SESSION['role'] != "User"){ ?>
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
                            <a href="./manage-teams.php?sport=<?php echo $sportList[$sport];  ?>&tournament=<?php echo $tournament_id; ?>"><div class="make-slots">Make Slots</div></a>
                        </div>
                    <?php }else{ ?>
                        <button onclick="printDiv('print-area')" style="
                            padding: 10px 25px;
                            font-size: 16px;
                            background: var(--gradient);
                            color: white;
                            border: none;
                            border-radius: 6px;
                            box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.7);
                            cursor: pointer;
                        ">
                        🖨️ Print Slots
                        </button>
                    <?php } ?>
                
                <div class="edit-toggle" id="editToggle">
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23.3333 7.29182L27.7083 11.6668M29.7281 9.60328C30.3025 9.02892 30.6252 8.24992 30.6252 7.43766C30.6252 6.62539 30.3025 5.84639 29.7281 5.27203C29.1538 4.69767 28.3748 4.375 27.5625 4.375C26.7502 4.375 25.9712 4.69767 25.3969 5.27203L13.125 17.5002V21.8752H17.5L29.7281 9.60328Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <?php } ?>
            <h2 style="text-align: center;">Tournament Details</h2>
            <p style="text-align: center; font-size: 1.2rem;">(<?php echo $result['tournament_name']; ?>)</p>
        </div>
        
        <!-- Teams Display --><div id="print-area">
<?php
if ($format == 'knockout') {

    echo "<h2 class='m_heading'>Knockout Matches</h2>";

    // FETCH TEAMS
    $teams = [];
    $teamQuery = mysqli_query($conn, "SELECT team_id FROM tournament_teams WHERE tournament_id='$tournament_id'");
    while ($row = mysqli_fetch_assoc($teamQuery)) $teams[] = $row['team_id'];
    $totalTeams = count($teams);
    if ($totalTeams < 2) {
        echo "<div class='match-container'><div class='tname'>Not enough teams to create bracket.</div></div>";
        return;
    }

    // TEAM CACHE
    $teamCache = [];
    $tq = mysqli_query($conn, "SELECT * FROM teams");
    while ($tr = mysqli_fetch_assoc($tq)) $teamCache[$tr['t_id']] = $tr;
    function getTeamName($team, $teamCache){ return $teamCache[$team]['t_name'] ?? ($team==="BYE"?"BYE":"TBD"); }

    // BRACKET CALC
    $nextPow = 1; while ($nextPow < $totalTeams) $nextPow <<=1;
    $totalRounds = (int)log($nextPow,2);

    echo "<div class='match-container'><div class='tname'>Teams: $totalTeams | Bracket Slots: $nextPow | Rounds: $totalRounds</div></div>";

    // FETCH EXISTING MATCHES
    $matchesByRound = [];
    $res = mysqli_query($conn, "SELECT * FROM matches WHERE tournament='".mysqli_real_escape_string($conn,$tournament_id)."' ORDER BY round ASC, match_id ASC,match_name ASC");
    while ($m = mysqli_fetch_assoc($res)) $matchesByRound[(int)$m['round']][] = $m;

    // -------------------
    // ROUND STRUCTURE
    // -------------------
    $rounds = [];
    $rounds[1]['matches'] = $matchesByRound[1] ?? [];

    // Detect teams that have a match in round 1
    $playedTeams = [];
    foreach ($rounds[1]['matches'] as $m) {
        $playedTeams[] = $m['team_1'];
        $playedTeams[] = $m['team_2'];
    }
    $playedTeams = array_filter(array_unique($playedTeams));

    // Teams without a match are BYE teams
    $byeTeams = array_diff($teams, $playedTeams);

    // Add BYE entries dynamically (not DB)
    foreach($byeTeams as $bt){
        $rounds[1]['matches'][] = [
            'match_id' => "bye_r1_$bt",
            'team_1' => $bt,
            'team_2' => "BYE",
            'round' => 1,
            'match_name' => "BYE",
            'score_log' => json_encode(['match_completed'=>1,'winner'=>$bt])
        ];
    }

    // -------------------
    // ADVANCE WINNERS ROUND BY ROUND AND INSERT NEXT ROUND ONLY IF CURRENT ROUND COMPLETED
    // -------------------
    for($r=1;$r<=$totalRounds;$r++){
        $matches = $rounds[$r]['matches'] ?? [];
        $winners = [];
        $allCompleted = true;

        foreach($matches as $m){
            $score = json_decode($m['score_log'], true);
            if(!empty($score['match_completed']) && $score['match_completed']==1 && !empty($score['winner'])){
                $winners[] = $score['winner'];
            } else {
                $allCompleted = false;
            }
        }

        $rounds[$r]['winners'] = $winners;

        // Only schedule next round if all matches of current round are completed
        if($r<$totalRounds && $allCompleted){
            $nextR = $r+1;

            if(!empty($matchesByRound[$nextR])){
                // DB already has next round matches -> use them
                $rounds[$nextR]['matches'] = $matchesByRound[$nextR];
                continue;
            }

            // Build next round matches from winners
            $nextMatches = [];
            $round_name = null;
            if(count($winners) > 4){
                $round_name = "Match";
            } else if(count($winners) > 2){
                $round_name = "Semifinal";
            } else if(count($winners) > 1){
                $round_name = "Final";
            }
            shuffle($winners);
            for($i=0;$i<count($winners);$i+=2){
                $a = $winners[$i];
                $b = $winners[$i+1] ?? "BYE";

                if($a==="BYE" && $b==="BYE") continue;

                $uid = hash('sha256', microtime(true).rand().$a.$b);
                $extra = "";
                if (str_contains($round_name, "Match")) {
                    $extra = " " . ($r + 1);   // you probably meant next round index
                }

                // Build match name
                $matchName = ($b === "BYE")
                    ? "BYE"
                    : $round_name . " " 
                        . ( $round_name !== "Final" ? ($i/2 + 1) : "" )
                        . " ($extra)"
                        . " | $tournament_name";

                // Insert into DB only if both teams are real
                if($b !== "BYE"){
                    $ins = "INSERT INTO matches 
                        (match_id, sport_id, match_name, status, venue, tournament, team_1, team_2, round, created_by)
                        VALUES (
                            '".mysqli_real_escape_string($conn,$uid)."',
                            '".mysqli_real_escape_string($conn,$sport)."',
                            '".mysqli_real_escape_string($conn,$matchName)."',
                            'Upcoming',
                            '".mysqli_real_escape_string($conn,$venue)."',
                            '".mysqli_real_escape_string($conn,$tournament_id)."',
                            '".mysqli_real_escape_string($conn,$a)."',
                            '".mysqli_real_escape_string($conn,$b)."',
                            '".intval($nextR)."',
                            '".mysqli_real_escape_string($conn,$owner)."'
                        )";
                    mysqli_query($conn, $ins);
                }

                $scoreLog = ($b==="BYE") ? json_encode(['match_completed'=>1,'winner'=>$a]) : json_encode([]);

                $nextMatches[] = [
                    'match_id'=>$uid,
                    'team_1'=>$a,
                    'team_2'=>$b,
                    'round'=>$nextR,
                    'match_name'=>$matchName,
                    'score_log'=>$scoreLog
                ];
            }

            $rounds[$nextR]['matches'] = $nextMatches;
            $matchesByRound[$nextR] = $nextMatches;
        }
    }

    // -------------------
    // DISPLAY ROUNDS INCLUDING BYE
    // -------------------
     function slotLabel($round,$index,$totalRounds){
        if($round==$totalRounds) return "Final";
        if($round==$totalRounds-1) return "Semifinal $index";
        return "Match $index";
    }

    for($r = 1; $r <= $totalRounds; $r++){
    echo "<h3>".($r == $totalRounds ? "🏆 Final" : ($r == $totalRounds-1 ? "⚔️ Semifinals" : "Round $r"))."</h3>";
    $mc = 1;

    // Ensure matches exist for this round (DB or generated)
    $matches = $rounds[$r]['matches'] ?? [];

    // Sort matches by match_name if available
    usort($matches, function($a, $b){
        // BYE matches at the end
        if (($a['team_2'] ?? '') === "BYE" && ($b['team_2'] ?? '') !== "BYE") return 1;
        if (($b['team_2'] ?? '') === "BYE" && ($a['team_2'] ?? '') !== "BYE") return -1;
        return strcmp($a['match_name'] ?? '', $b['match_name'] ?? '');
    });

    // total slots for this round based on bracket
    $slots = (int)pow(2, $totalRounds - $r);
    if ($slots < 1) $slots = 1;

    for($i = 0; $i < $slots; $i++){
        $m = $matches[$i] ?? null;

        if($m){
            $t1 = getTeamName($m['team_1'], $teamCache);
            $t2 = getTeamName($m['team_2'], $teamCache);
            $score = json_decode($m['score_log'], true);
            $link = "./{$sportList[$sport]}/scoreboard.php?match_id={$m['match_id']}";
        } else {
            $t1 = "TBD (waiting...)";
            $t2 = "TBD (waiting...)";
            $link = "";
        }

        // USE window.location properly
        $onclick = $link ? "onclick=\"window.location='$link'\"" : "";

        $label = slotLabel($r, $mc, $totalRounds);

        echo "<div class='match-container'>
            <h4 class='match-head'>".htmlspecialchars($label)."</h4>
            <div class='team-container' $onclick>
                <div class='teams left-side'><div class='logo'></div><div class='tname'>$t1</div></div>
                <label class='vs'>VS</label>
                <div class='teams right-side'><div class='logo'></div><div class='tname'>$t2</div></div>
            </div>
        </div>";

        $mc++;
    }
}

    // -------------------
    // DISPLAY FINAL WINNER IF ANY
    // -------------------
    $finalMatches = $rounds[$totalRounds]['matches'] ?? [];
    foreach($finalMatches as $fm){
        $score = json_decode($fm['score_log'],true);
        if(!empty($score['match_completed']) && $score['match_completed']==1 && !empty($score['winner'])){
            $winnerName = getTeamName($score['winner'],$teamCache);
            echo "<div class='match-container'><div class='tname'>🏆 Tournament Winner: <strong>$winnerName</strong></div></div>";
            break;
        }
    }
}

$matchNo = 1;
$matchSlots = []; // 🟢 Store all slots here

if ($format == 'league') {
    echo "<h2 class='m_heading'>League Matches</h2>";

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

            $link = "./{$sportList[$sport]}/scoreboard.php?match_id={$m['match_id']}";
            $onclick = $link ? "onclick=\"window.location='$link'\"" : "";
            // 🟢 Display match
            echo '
            <div class="match-container">
                <h4 class="match-head">' . $matchName . ($exists ? '' : ' (Not Scheduled)') . '</h4>
                <div class="team-container" '.$onclick.'>
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

//INITIALIZE POINTS TABLE (FROM your tournament teams)
$pointsTable = [];

$team_query = mysqli_query($conn, "SELECT team_id FROM tournament_teams WHERE tournament_id = '$tournament_id'");
while ($row = mysqli_fetch_assoc($team_query)) {
    $teamId = $row['team_id'];

    $pointsTable[$teamId] = [
        'team_id' => $teamId,
        'played' => 0,
        'won' => 0,
        'lost' => 0,
        'points' => 0,
        'runs_scored' => 0,
        'overs_played' => 0,
        'runs_conceded' => 0,
        'overs_bowled' => 0,
        'nrr' => 0
    ];
}

//PROCESS ALL COMPLETED MATCHES
$match_query = mysqli_query($conn, "
    SELECT * FROM matches 
    WHERE tournament = '$tournament_id' 
    AND status = 'completed'
");

while ($m = mysqli_fetch_assoc($match_query)) {

    $t1 = $m['team_1'];
    $t2 = $m['team_2'];
    $score_log = json_decode($m['score_log'],true);

    $pointsTable[$t1]['played']++;
    $pointsTable[$t2]['played']++;

    $team1_inning_no = $t1 == $score_log['innings']['1st']['batting_team'] ? '1st' : '2nd';
    $team2_inning_no = $t2 == $score_log['innings']['1st']['batting_team'] ? '1st' : '2nd';

    // ✅ Runs & Overs
    $pointsTable[$t1]['runs_scored'] += $score_log['team1_score'];
    $pointsTable[$t1]['overs_played'] += $score_log['innings'][$team1_inning_no]['overs_completed'];
    $pointsTable[$t1]['runs_conceded'] += $score_log['team2_score'];
    $pointsTable[$t1]['overs_bowled'] += $score_log['innings'][$team2_inning_no]['overs_completed'];

    $pointsTable[$t2]['runs_scored'] += $score_log['team2_score'];
    $pointsTable[$t2]['overs_played'] += $score_log['innings'][$team2_inning_no]['overs_completed'];
    $pointsTable[$t2]['runs_conceded'] += $score_log['team1_score'];
    $pointsTable[$t2]['overs_bowled'] += $score_log['innings'][$team1_inning_no]['overs_completed'];

    // ✅ Win / Loss / Points
    if ($score_log['winner'] == $t1) {
        $pointsTable[$t1]['won']++;
        $pointsTable[$t1]['points'] += 2;
        $pointsTable[$t2]['lost']++;
    } else if ($score_log['winner'] == $t2) {
        $pointsTable[$t2]['won']++;
        $pointsTable[$t2]['points'] += 2;
        $pointsTable[$t1]['lost']++;
    }
}

// CALCULATE NET RUN RATE (NRR)
foreach ($pointsTable as &$t) {
    if ($t['overs_played'] > 0 && $t['overs_bowled'] > 0) {
        $t['nrr'] = 
            ($t['runs_scored'] / $t['overs_played']) -
            ($t['runs_conceded'] / $t['overs_bowled']);
    }
}

// SORT TEAMS BY OFFICIAL LEAGUE RULE (Points,NRR,Wins)
usort($pointsTable, function ($a, $b) use ($conn, $tournament_id) {

    // 1️⃣ POINTS
    if ($b['points'] != $a['points'])
        return $b['points'] <=> $a['points'];

    // 2️⃣ NRR
    if ($b['nrr'] != $a['nrr'])
        return $b['nrr'] <=> $a['nrr'];

    // 3️⃣ WINS
    if ($b['won'] != $a['won'])
        return $b['won'] <=> $a['won'];

    // 4️⃣ HEAD-TO-HEAD
    $teamA = $a['team_id'];
    $teamB = $b['team_id'];

    $hh = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT score_log FROM matches 
        WHERE tournament = '$tournament_id'
        AND (
            (team_1 = '$teamA' AND team_2 = '$teamB') OR
            (team_1 = '$teamB' AND team_2 = '$teamA')
        )
        AND score_log IS NOT NULL
        LIMIT 1
    "));

    if (!empty($hh)) {
        $score_log = json_decode($hh['score_log'],true); 
        if ($score_log['winner'] == $teamA) return -1;
        if ($score_log['winner'] == $teamB) return 1;
    }

    // 5️⃣ FAIR PLAY (LOWER = BETTER)
    if (isset($a['fair_play'], $b['fair_play']) && $a['fair_play'] != $b['fair_play'])
        return $a['fair_play'] <=> $b['fair_play'];

    // 6️⃣ TOSS (FINAL FALLBACK)
    return rand(-1, 1);
});

//check all comleted ?
$checkMatches = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total_matches,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_matches,
        SUM(CASE WHEN status != 'Completed' THEN 1 ELSE 0 END) AS pending_matches
    FROM matches
    WHERE tournament = '$tournament_id'
"));

$allCompleted = ($checkMatches['total_matches'] == $checkMatches['completed_matches']);
//Display league winner
if ($allCompleted && !empty($pointsTable)) {

    $winnerTeamId = $pointsTable[0]['team_id'];

    $winnerData = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT t_name, t_logo FROM teams WHERE t_id='$winnerTeamId'")
    );

    if(!empty($winnerData['t_logo'])){
        $src = "../assets/images/teams/{$winnerData['t_logo']}";
    }else{
        $src = "https://cdn-icons-png.flaticon.com/512/8140/8140303.png";
    }

    echo "
    <div class='league-winner-box'>
        <h2 class='league-winner-title'>🏆 League Champion</h2>
        <div class='winner-card'>
            <div class='winner-logo'>
                <img src='{$src}' alt='Winner'>
            </div>
            <div class='winner-info'>
                <h3 class='winner-name'>{$winnerData['t_name']}</h3>
                <p class='winner-points'>Points: <span>{$pointsTable[0]['points']}</span></p>
                <p class='winner-nrr'>NRR: <span>".number_format($pointsTable[0]['nrr'], 3)."</span></p>
                <p class='winner-nrr'>Wins: <span>".$pointsTable[0]['won']."</span></p>
            </div>
        </div>
    </div>
    ";
}

// ✅ DISPLAY FINAL LEAGUE POINTS TABLE
echo "<div class='league-table-wrapper'>
        <h2>League Points Table</h2>";

echo "<table class='league-table'>
<tr>
    <th>Rank</th>
    <th>Team</th>
    <th>P</th>
    <th>W</th>
    <th>L</th>
    <th>Pts</th>
    <th>NRR</th>
    <th>Fair Play</th>
    <th>Status</th>
</tr>";

$rank = 1;
$qualified_limit = 4; // ✅ Top 4 qualify (change if needed)

foreach ($pointsTable as $team) {

    // ✅ Fetch team name
    $teamData = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT t_name FROM teams WHERE t_id='{$team['team_id']}'")
    );

    // ✅ Qualification status
    $status = ($rank <= $qualified_limit) 
                ? "<span class='qualified'>🟢</span>" 
                : "<span class='eliminated'>🔴</span>";

    // ✅ Fair Play (safe display)
    $fairPlay = isset($team['fair_play']) ? $team['fair_play'] : '-';

    echo "<tr>
        <td>$rank</td>
        <td>{$teamData['t_name']}</td>
        <td>{$team['played']}</td>
        <td>{$team['won']}</td>
        <td>{$team['lost']}</td>
        <td>{$team['points']}</td>
        <td>".number_format($team['nrr'], 3)."</td>
        <td>$fairPlay</td>
        <td>$status</td>
    </tr>";

    $rank++;
}

echo "</table></div>";

?></div>
    <?php if(isset($_SESSION['user']) && $_SESSION['role'] != "User"){ ?>
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
    <?php } ?>
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

    //print slots
    function printDiv(divId) {
        var content = document.getElementById(divId).outerHTML;

        var printWindow = window.open("", "", "width=1200,height=700");

        // ✅ COPY ALL CSS FILES FROM MAIN PAGE
        var cssLinks = "";
        document.querySelectorAll("link[rel='stylesheet']").forEach(link => {
            cssLinks += `<link rel="stylesheet" href="${link.href}">`;
        });

        // ✅ COPY ALL INLINE STYLES
        var inlineStyles = "";
        document.querySelectorAll("style").forEach(style => {
            inlineStyles += style.outerHTML;
        });

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print</title>
                ${cssLinks}
                ${inlineStyles}
                <style>
                    @media print {
                        button, .no-print { display: none !important; }
                    }
                </style>
            </head>
            <body>
                ${content}
            </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 800);
    }

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