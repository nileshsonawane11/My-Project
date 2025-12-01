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
    <title>Playing 11</title>
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
        display: none;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        box-shadow: 0 4px 6px rgba(209, 34, 31, 0.2);
        cursor: pointer;
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
                <div class="btn">Select Playing 11</div>
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
                            </div>
                        ";
                        
                        echo $player;
                        echo $player;
                        echo $player;
                        echo $player;

                        echo $player;echo $player;
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
                                        echo '<h4>Alert</h4>';
                                        echo '<p></p>';
                                ?>
                            </div>
                            <div class="data">
                                <?php 
                                        echo'<div class="style-container">
                                            11 Players Required...
                                        </div>';
                                ?>
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
        let selectedplayer = [];
        let selectedStyle = '';
        const selected_player = document.querySelectorAll('.mem');
        const btn = document.querySelector('.btn');
        const urlParams = new URLSearchParams(window.location.search);
        const person = urlParams.get('for');
        const team = urlParams.get('team');
        const match = urlParams.get('match');

        let goBack = ()=>{
            window.parent.postMessage("closeIframe", "*");
        }

        let close_dialogbox = () => {
            document.querySelector('#selectstyle').close();
        }

        let select_player = (option) => {
        if (option.classList.contains('active')) {
            option.classList.remove('active');
            let valueToRemove = option.getAttribute('data-value');
            selectedplayer = selectedplayer.filter(item => item !== valueToRemove);
            if(selectedplayer.length >= 2){
                btn.style.display = 'flex';
            }else{
                btn.style.display = 'none';
            }
            console.log(selectedplayer)
            console.log(selectedplayer.length)

        } else if(selectedplayer.length <= 10){
            option.classList.add('active');
            selectedplayer.push(option.getAttribute('data-value'));
            if(selectedplayer.length >= 2){
                btn.style.display = 'flex';
            }else{
                btn.style.display = 'none';
            }
            console.log(selectedplayer)
            console.log(selectedplayer.length)
        }
        };

        function handleSelect(e) {
        select_player(e.currentTarget);
        }

        // Attach event listeners
        selected_player.forEach(option => {
        option.addEventListener('click', handleSelect);
        });

        // Get all style containers
       

        document.querySelector('.btn').addEventListener('click', (el) => {
            if(selectedplayer.length >= 2){
                submit();
            }else{
                navigator.vibrate([100,50,100,50,100]);
                 let dialog = document.querySelector('#selectstyle');
                dialog.classList.add('shake');

                // Remove it after animation ends (so it can be retriggered next time)
                dialog.addEventListener('animationend', () => {
                    dialog.classList.remove('shake');
                }, { once: true });
                dialog.showModal();
            }
        });

        let submit = () => { 
            let player_data={
                team : team,
                match : match,
                data : selectedplayer
            }
            window.parent.postMessage(player_data, "*");
            goBack();
        }

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