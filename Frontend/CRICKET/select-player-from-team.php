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

    $current_inning = null;
    $is_super_over = false;
    $for = $_GET['for'] ?? '';
    $team = $_GET['team'] ?? '';
    $match = $_GET['match'] ?? '';

    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM matches WHERE match_id = '$match'"));
    $score_log = json_decode($row['score_log'],true) ?? '';

    if (!empty($score_log['innings'])) {
        if (!empty($score_log['innings']['2nd']['completed'])) {
            $is_super_over = true;
        }
        if (!empty($score_log['innings']['2nd']['balls'])) {
            $current_inning = '2nd';
        } elseif (!empty($score_log['innings']['1st']['balls'])) {
            $current_inning = '1st';
        } else {
            $current_inning = null; // No valid innings yet
        }
    }

$out_batsmen = [];
    // Check if current innings data exists
if (isset($score_log['innings'][$current_inning]) && $for !== 'Bowler' && !$is_super_over) {

    foreach ($score_log['innings'][$current_inning]['batmans'] as $batsman) {
        if (
            $score_log['innings'][$current_inning]['batting_team'] == $team &&
            isset($batsman['out_status']) &&
            strtolower($batsman['out_status']) !== 'not out'
        ) {
            $out_batsmen[] = $batsman['id'];
        }
    }

    // Output or return the filtered list
    // echo json_encode($out_batsmen);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>Team Info</title>
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
        --primary-color: rgba(209, 34, 31, 1);
        --primary-light: rgba(209, 34, 31, 0.8);
        --primary-dark: rgba(160, 25, 23, 1);
        --primary-light-20: rgba(209, 34, 31, 0.2);
        --primary-dark-10: rgba(160, 25, 23, 0.1);
        --background: #ffffff;
        --card-bg: #ffffff;
        --text-dark: #000000;
        --text-muted: #666666;
        --bg-light: #f5f5f5;
        --border-light: #e0e0e0;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        --radius-sm: 6px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --svg-fill: #000000;
        --hover-bg: rgba(209, 34, 31, 0.08);
    }
    
    /* Dark theme variables */
    [data-theme="dark"] {
        --background: #121212;
        --card-bg: #1e1e1e;
        --text-dark: #ffffff;
        --text-muted: #a0a0a0;
        --bg-light: #2d2d2d;
        --border-light: #333333;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.2);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.3);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.3);
        --svg-fill: #ffffff;
        --hover-bg: rgba(209, 34, 31, 0.15);
    }
    
    svg path {
        fill: var(--text-dark);
    }
    body {
        height: -webkit-fill-available;
        background: var(--background);
        color: var(--text-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .container {
        display: flex;
        background-color: var(--card-bg);
        position: relative;
        width: 100%;
        max-width: 1200px;
        min-height: 480px;
        align-items: center;
        justify-content: flex-start;
        flex-direction: column;
        gap: 30px;
        transition: background-color 0.3s ease;
    }
    
    .return {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-direction: row;
        padding: 40px 0 0 40px;
    }
    
    .return svg {
        cursor: pointer;
        fill: var(--svg-fill);
        transition: var(--transition);
    }
    
    .return svg:hover {
        transform: scale(1.1);
        fill: var(--primary-light);
    }
    
    .content {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 30px;
    }
    
    .container2 {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        width: 100%;
        gap: 30px;
    }
    
    .logo-name {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 20px;
    }
    
    .logo-img {
        height: 140px;
        width: 140px;
        border-radius: 50%;
        background: var(--bg-light);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 3px solid var(--primary-light-20);
        transition: var(--transition);
    }
    
    .logo-img:hover {
        transform: scale(1.05);
    }
    
    .logo-img img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    
    .logo-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .data-info {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .other-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        justify-content: center;
    }
    
    .other-info label {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: row;
        gap: 8px;
        color: var(--text-muted);
    }
    
    .mem {
        display: flex;
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        height: 80px;
        background: var(--bg-light);
        padding: 0 15px;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }
    
    .mem:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        background: var(--hover-bg);
    }
    
    .mem.active {
        border: 2px solid var(--primary-color);
        background-color: var(--primary-light-20);
    }
    
    .mem-list {
        width: 100%;
        height: max-content;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        gap: 20px;
        overflow: hidden;
    }
    
    .mem-img {
        width: 60px;
        height: 60px;
        background: var(--border-light);
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .mem-img img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    
    .mem-head {
        width: 100%;
        text-align: left;
        margin-left: 15px;
        color: var(--text-dark);
        font-weight: 600;
    }
    
    .list-items {
        cursor: pointer;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        gap: 15px;
        width: 100%;
    }
    
    .error {
        display: none;
        color: #DC2626;
        width: 100%;
        font-size: 14px;
        margin: 5px 0;
        padding: 8px 12px;
        background-color: rgba(220, 38, 38, 0.1);
        border-radius: var(--radius-sm);
        border-left: 3px solid #DC2626;
    }
    
    .btn-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-top: 20px;
    }
    
    .btn-container .btn {
        background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
        color: white;
        font-size: 14px;
        padding: 12px 30px;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        width: max-content;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        box-shadow: 0 4px 6px rgba(209, 34, 31, 0.2);
    }
    
    .btn-container .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(209, 34, 31, 0.3);
        opacity: 0.9;
    }
    
    .player-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 15px;
    }
    
    .player-detail {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: row;
        gap: 15px;
    }
    
    .done {
        background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
        color: white;
        font-size: 14px;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        cursor: pointer;
        height: 40px;
        width: 100px;
        display: none;
        transition: var(--transition);
    }
    
    #selectstyle {
        position: fixed;
        transform: translateX(-50%) translateY(-50%);
        top: 50%;
        left: 50%;
        width: 320px;
        border: none;
        height: max-content;
        background: var(--card-bg);
        transition: all 0.3s ease-in-out;
        align-items: flex-start;
        padding: 25px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: var(--text-dark);
    }
    
    #selectstyle::backdrop {
        position: fixed;
        inset: 0px;
        background: rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(2px);
    }
    
    .data {
        text-align: left;
        display: grid;
        align-items: center;
        grid-template-columns: repeat(2, 1fr);
        justify-content: space-evenly;
        gap: 15px;
        margin: 20px 0;
        justify-items: center;
        margin-bottom: 65px;
        height: max-content;
        width: 100%;
    }
    
    .text {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        justify-content: center;
        width: 100%;
    }
    
    .style-container {
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        align-items: center;
        padding: 15px;
        gap: 10px;
        box-shadow: var(--shadow-sm);
        background: var(--bg-light);
        text-align: center;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        width: 100%;
        color: var(--text-dark);
    }
    
    .style-container:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        background: var(--hover-bg);
    }
    
    .style-container.active {
        border: 2px solid var(--primary-color);
        background-color: var(--primary-light-20);
    }
    
    .data:last-child {
        margin: 0;
        width: 100%;
        background: var(--border-light);
        position: absolute;
        grid-template-columns: 1fr 1fr;
        bottom: 0;
        left: 0;
        gap: 0;
        height: 70px;
        justify-content: space-between;
        border-bottom-left-radius: var(--radius-lg);
        border-bottom-right-radius: var(--radius-lg);
    }
    
    .btns {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        color: var(--text-dark);
    }
    
    .btns:last-child {
        background: var(--primary-dark);
        color: white;
        font-weight: 600;
        border-bottom-right-radius: var(--radius-lg);
    }
    
    .btns:first-child {
        border-bottom-left-radius: var(--radius-lg);
    }
    
    .btns:hover {
        opacity: 0.9;
    }
    
    .text h4 {
        color: var(--primary-color);
    }
    
    @keyframes shake {
        0% { transform: translate(-50%, -50%); }
        20% { transform: translate(calc(-50% - 10px), -50%); }
        40% { transform: translate(calc(-50% + 10px), -50%); }
        60% { transform: translate(calc(-50% - 10px), -50%); }
        80% { transform: translate(calc(-50% + 10px), -50%); }
        100% { transform: translate(-50%, -50%); }
    }
    
    .reason {
        color: #DC2626;
        font-weight: 500;
    }
    
    .shake {
        animation: shake 0.4s;
    }

    @media (min-width: 601px) {
        
        .mem {
            border-radius: var(--radius-md);
        }
    }
    
    @media (max-width: 600px) {
        .logo-img {
            height: 120px;
            width: 120px;
        }
        
        .btn-container .btn {
            width: 100%;
            max-width: 280px;
        }
        
        #selectstyle {
            width: 90%;
            max-width: 320px;
        }
        
        .data {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>
    <div class="container">
        <div class="return" >
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div></div>
        </div>

        
            <div class="container2">
                
                <?php
                    $sql = "SELECT * FROM teams t JOIN sports s ON t.t_sport = s.sport_name WHERE t_id = '$team'";
                    $query = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($query);
                    $src = '';
                    if($row['t_logo']){
                        $src = "../../assets/images/teams/".$row['t_logo'];
                    }else{
                        $src = "https://cdn-icons-png.flaticon.com/512/8140/8140303.png";
                    }
                ?>
                <div class="content">
                    <div class="logo-name">
                        <div class="logo-img">
                            <img src="<?php echo $src; ?>" alt="">
                        </div>
                        <div class="logo-info">
                            <h4><?php echo $row['t_name']; ?></h4>
                            <div class="other-info">
                                <label for="coordinator" class=""><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><?php echo $row['t_coordinatorName']; ?>
                                </label>
                                <label for="place" class=""><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><?php echo $row['t_city']; ?>
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>

            <div class="btn-container">
                <div class="btn">Select <?php echo $for; ?></div>
            </div>
        
            <div class="data-info">
                <div class="mem-list">
                    <?php
                        $result = mysqli_query($conn,"SELECT * FROM `players` WHERE `team_id` = '$team'");
                        $count = 0;
                    ?>
                    <h4 class="mem-head">Players (<?php echo mysqli_num_rows($result);?>)</h4>
                    <div class="player-container">

                    <?php
                        while($row2 = mysqli_fetch_assoc($result)) {
                        $user_id = $row2['user_id'];
                        
                        // Try to get from users table first
                        $user_query = mysqli_query($conn, "SELECT * FROM `users` WHERE `user_id` = '$user_id'");
                        $user_data = mysqli_fetch_assoc($user_query);
                        
                        // If not found in users table, try players table
                        if(!$user_data) {
                            $player_query = mysqli_query($conn, "SELECT * FROM `players` WHERE `user_id` = '$user_id'");
                            $player_data = mysqli_fetch_assoc($player_query);
                            
                            if($player_data) {
                                // Map player data to expected format
                                $row = [
                                    'fname' => $player_data['player_name'] ? explode(' ', $player_data['player_name'])[0] : 'Player',
                                    'lname' => $player_data['player_name'] ? (explode(' ', $player_data['player_name'])[1] ?? '') : '',
                                    'user_photo' => $player_data['photo']
                                ];
                            } else {
                                // Player not found in either table - use defaults
                                $row = [
                                    'fname' => 'Unknown',
                                    'lname' => 'Player',
                                    'user_photo' => null
                                ];
                            }
                        } else {
                            // User found in users table
                            $row = [
                                'fname' => $user_data['fname'],
                                'lname' => $user_data['lname'],
                                'user_photo' => $user_data['user_photo']
                            ];
                        }
                        
                        // Handle photo source
                        if(!empty($row['user_photo'])) {
                            $src = "../../assets/images/users/".$row['user_photo'];
                        } else {
                            $src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";
                        }
                        
                        $count++;
                        $player = "
                            <div class='mem' data-value='$user_id' src='$src'>
                                <div class='player-detail'>
                                    $count
                                    <div class='mem-img'><img src='$src' alt='' class='mem-img'></div>
                                    <div class='mem-name'>{$row['fname']} {$row['lname']}</div>
                                </div>
                                <div>
                                    <p class='reason'></p>
                                </div>
                                <div class='done-dtn'>
                                    <button class='done'>Done</button>
                                </div>
                            </div>
                        ";
                        
                        echo $player;
                    }
                    ?>
                        

                        <!-- <div class='mem' data-value="Player_id">
                            <div class="player-detail">
                                2
                                <div class='mem-img'><img src='' alt='' class='mem-img'></div>
                                <div class='mem-name'>name</div>
                            </div>
                            <div class="done-dtn">
                                <button class="done" type='submit'>Done</button>
                            </div>
                        </div> -->
                        <dialog id="selectstyle">
                            <div class="text">
                                <?php 
                                    if($for != 'Bowler'){
                                        echo '<h4>Batting Style</h4>';
                                        echo '<p>What’s the batting style of player</p>';
                                    }else{
                                        echo '<h4>Bowling Style</h4>';
                                        echo '<p>What’s the bowling style of player</p>';
                                    }
                                ?>
                            </div>
                            <div class="data">
                                <?php 
                                    if($for != 'Bowler'){
                                        echo'<div class="style-container">
                                    <svg width="75" height="75" viewBox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <circle cx="37.5" cy="37.5" r="37.5" transform="matrix(-1 0 0 1 75 0)" fill="url(#pattern0_759_3734)"/>
                                    <defs>
                                    <pattern id="pattern0_759_3734" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_759_3734" transform="translate(-0.166667) scale(0.00130208)"/>
                                    </pattern>
                                    <image id="image0_759_3734" width="1024" height="768" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
                                    </defs>
                                    </svg>
                                    Left Hand
                                </div>
                                <div class="style-container">
                                    <svg width="76" height="75" viewBox="0 0 76 75" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <ellipse cx="37.6819" cy="37.5" rx="37.6819" ry="37.5" fill="url(#pattern0_759_3727)"/>
                                    <defs>
                                    <pattern id="pattern0_759_3727" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_759_3727" transform="matrix(0.0012958 0 0 0.00130208 -0.163448 0)"/>
                                    </pattern>
                                    <image id="image0_759_3727" width="1024" height="768" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
                                    </defs>
                                    </svg>
                                    Right Hand
                                </div>';}else{
                                    echo '<div class="style-container">Right-arm fast</div>
                                    <div class="style-container">Right-arm Medium</div>
                                    <div class="style-container">Left-arm fast</div>
                                    <div class="style-container">Left-arm Medium</div>
                                    <div class="style-container">Slow left-arm orthodox</div>
                                    <div class="style-container">Slow left-arm chinaman</div>
                                    <div class="style-container">Right-arm Off Break</div>
                                    <div class="style-container">Right-arm Leg Break</div>'; 
                                } ?>
                                
                                
                            </div>
                            <div class="data">
                                <div class="btns" onclick='close_dialogbox()'>Cancel</div>
                                <div class="btns" onclick='submit()'>OK</div>
                            </div>
                        </dialog>
                    </div>
                </div>
            </div>
    </div>


    
    
    <script>
        let selectedplayer = '';
        let selectedStyle = '';
        const selected_player = document.querySelectorAll('.mem');
        const urlParams = new URLSearchParams(window.location.search);
        const person = urlParams.get('for');
        const strikerParam = urlParams.get('striker');
        const nonStrikerParam = urlParams.get('non-striker');
        let out_players = <?php echo json_encode($out_batsmen ?? []); ?>;                              
        const striker = strikerParam ? strikerParam.split(",")[0] : null;
        const non_striker = nonStrikerParam ? nonStrikerParam.split(",")[0] : null;

        let goBack = ()=>{
            window.parent.postMessage("closeIframe", "*");
        }

        let close_dialogbox = () => {
            document.querySelector('#selectstyle').close();
        }

        let select_player = (option) => {
        if (option.classList.contains('active')) {
            option.querySelector('.done').style.display = 'none';
            option.classList.remove('active');
            selectedplayer = '';
        } else {
            selected_player.forEach(opt => {
            opt.classList.remove('active');
            opt.querySelector('.done').style.display = 'none';
            });
            option.classList.add('active');
            option.querySelector('.done').style.display = 'block';
            selectedplayer = option.getAttribute('data-value');
        }
        };

        function handleSelect(e) {
        select_player(e.currentTarget);
        }

        // Attach event listeners
        selected_player.forEach(option => {
        option.addEventListener('click', handleSelect);
        });

        // Remove event listeners from striker and non-striker
        selected_player.forEach(option => {
        let block = option.getAttribute('data-value');
        if ((striker && block == striker.trim()) || (non_striker && block == non_striker.trim())) {
            option.removeEventListener('click', handleSelect);
            option.style.background = '#fff';
            option.style.border = '#000000 1px solid';
            option.querySelector('.reason').innerHTML = 'Selected';
        }
        if(out_players.includes(block)){
            option.removeEventListener('click', handleSelect);
            option.style.background = '#fff';
            option.style.border = '#ff0000ff 2px solid';
            option.style.border = '#ff0000ff 2px solid';
            option.querySelector('.reason').innerHTML = 'Out';
        }
        });

        // Get all style containers
        let styleContainers = document.querySelectorAll('.style-container');

        styleContainers.forEach(container => {
            container.addEventListener('click', () => {
                // Remove active class from all
                styleContainers.forEach(c => c.classList.remove('active'));

                // Add active class to clicked one
                container.classList.add('active');

                // Get the text (like 'Left Hand' or 'Right Hand')
                selectedStyle = container.textContent.trim();
            });
        });

        let player = '';
        let img = '';
        document.querySelectorAll('.done').forEach(el => {
            el.addEventListener('click', (el) => {
                player = document.querySelector('.mem.active').getAttribute('data-value');
                img = document.querySelector('.mem.active').getAttribute('src');
                console.log(player);
                if(person == 'Fielder'){
                    selectedStyle = 'Cover';
                    submit();
                }else{
                    document.querySelector('#selectstyle').showModal();
                }
            });
        });

        let submit = () => {
            if(!(selectedStyle)){
                navigator.vibrate([100,50,100,50,100]);
                let dialog = document.querySelector('#selectstyle');
                dialog.classList.add('shake');

                // Remove it after animation ends (so it can be retriggered next time)
                dialog.addEventListener('animationend', () => {
                    dialog.classList.remove('shake');
                }, { once: true });
            }else{
                
                let player_data={
                    type : 'player',
                    person : person,
                    data : `${player},${selectedStyle}`,
                    img : img
                }
                console.log(selectedStyle);
                window.parent.postMessage(player_data, "*");
                goBack();
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