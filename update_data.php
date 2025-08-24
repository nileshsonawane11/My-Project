<?php
header('Content-Type: application/json');
session_start();
include 'config.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);

$status = mysqli_real_escape_string($conn, $data['update']);
$sport = mysqli_real_escape_string($conn, $data['sport']);
$for = $data['for'];

function formatMatchTime($matchDate, $startTime) {
        if (empty($matchDate) || empty($startTime)) {
            return "Not Scheduled";
        }

        $currentDate = date('Y-m-d');
        $yesterday   = date('Y-m-d', strtotime('-1 day'));
        $tomorrow    = date('Y-m-d', strtotime('+1 day'));

        if ($matchDate === $currentDate) {
            return "Today, " . date('h:i A', strtotime($startTime));
        } elseif ($matchDate === $yesterday) {
            return "Yesterday, " . date('h:i A', strtotime($startTime));
        } elseif ($matchDate === $tomorrow) {
            return "Tomorrow, " . date('h:i A', strtotime($startTime));
        } else {
            return date('d-m-Y', strtotime($matchDate)) . ", " . date('h:i A', strtotime($startTime));
        }
    }

if($for == "dashboard"){

    $search = isset($data['search']) ? trim(mysqli_real_escape_string($conn, $data['search'])) : '';

    if ($data['update'] != "All") {
        $sql = "SELECT matches.*, 
                   sports.sport_name,
                   t1.t_name AS team1_name,
                   t2.t_name AS team2_name 
            FROM matches 
            LEFT JOIN sports ON sports.sport_id = matches.sport_id 
            LEFT JOIN teams AS t1 ON matches.team_1 = t1.t_id 
            LEFT JOIN teams AS t2 ON matches.team_2 = t2.t_id 
            WHERE sports.sport_name = '$sport'";

    if (empty($search)) {
        $sql .= " AND matches.status = '$status'";
    }

    if (!empty($search)) {
        $sql .= " AND (
            matches.match_name LIKE '%$search%' OR 
            matches.status LIKE '%$search%' OR 
            matches.match_id LIKE '%$search%' OR 
            matches.team_1 LIKE '%$search%' OR 
            matches.team_2 LIKE '%$search%' OR 
            t1.t_name LIKE '%$search%' OR 
            t2.t_name LIKE '%$search%' OR 
            matches.match_date LIKE '%$search%' OR 
            matches.start_time LIKE '%$search%' OR 
            matches.venue LIKE '%$search%'
        )";
    }
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) { // Check if there are rows in the result
            while ($row = mysqli_fetch_assoc($result)) {
                $sql2 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_1']}'";
                $query = mysqli_query($conn, $sql2) or die("Error: ");
                $team1 = mysqli_fetch_assoc($query);

                $sql3 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_2']}'";
                $query3 = mysqli_query($conn, $sql3) or die("Error: ");
                $team2 = mysqli_fetch_assoc($query3);
                
                $score_log = json_decode($row['score_log'], true);

                echo "<div class='game-info' data-match_id='{$row['match_id']}' onclick='open_scoreboard(this)'>";
                echo "<div class='match-data'>";

                    echo "<div class='info'><p>" . (!empty($row['match_name']) ? $row['match_name'] : "Match 1 | No Tournament") . "</p></div>";

                    echo "<div class='info team-score'>";
                        echo "<div class='team'>";
                            if (!empty($team1['t_logo'])) {
                                echo "<img src='./assets/images/teams/{$team1['t_logo']}' alt='{$team1['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }
                            echo $team1['t_name'];
                        echo "</div>";                    
                        echo "<div class='score'>{$row['score_team_1']}</div>";
                    echo "</div>";

                    echo "<div class='info team-score'>";
                        echo "<div class='team'>";
                            if (!empty($team2['t_logo'])) {
                                echo "<img src='./assets/images/teams/{$team2['t_logo']}' alt='{$team2['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }
                            echo $team2['t_name'];
                        echo "</div>";                     
                        echo "<div class='score'>{$row['score_team_2']}</div>";
                    echo "</div>";

                    if(empty($row['toss_winner'])){
                        echo "<div class='info'><p>" . formatMatchTime($row['match_date'], $row['start_time']) . "</p></div>";
                    }else if($row['status'] == 'Live'){
                        

                        $team = '';
                        if($row['toss_winner'] == $team1['t_id']){
                            $team = $team1['t_name'];
                        }else{
                            $team = $team2['t_name'];
                        }

                        echo "<div class='info update'><p>" . $team . " Elected To ". $row['toss_decision'] ."</p></div>";
                    }else if($row['status'] == 'Completed'){
                        $winner = $score_log['winner'];
                        $winner_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `teams` WHERE t_id = '$winner'"))['t_name'];
                        // If match is not completed and no winner is declared
                        if (!empty($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])){
                            echo "<div class='info update'><p>Match Tied (".$winner_name." Won The Match)</p></div>";
                        }else{
                            echo "<div class='info update'><p>".$winner_name." Won The Match</p></div>";
                        }
                        
                    }

                echo "</div>";

                    echo "<div class='strt-btn'>";
                    
                    $scorers = [];

                    if (!empty($row['scorers'])) {
                        $decoded = json_decode($row['scorers'], true); // decode as array
                        if (is_array($decoded)) {
                            $scorers = $decoded;
                        }
                    }

                    $scorer_emails = isset($scorers[0]) ? explode(",", $scorers[0]) : [];
                    $session_email = $_SESSION['email'];

                    if ($scorer_emails && in_array($session_email, $scorer_emails) && $row['status'] == 'Live') {
                        echo "<div class='info'><button class='start-btn' onclick='openDialog(this, event)'>Start</button></div>";
                    }
                    echo "</div>";
                echo "</div>";
            }
        }
    } else {
        $search = isset($data['search']) ? trim(mysqli_real_escape_string($conn, $data['search'])) : '';

        $sql = "SELECT matches.*, 
               sports.sport_name, 
               t1.t_name AS team1_name, 
               t2.t_name AS team2_name 
        FROM matches 
        LEFT JOIN sports ON sports.sport_id = matches.sport_id 
        LEFT JOIN teams AS t1 ON matches.team_1 = t1.t_id 
        LEFT JOIN teams AS t2 ON matches.team_2 = t2.t_id 
        WHERE sports.sport_name = '$sport'";

        if (!empty($search)) {
            $sql .= " AND (
                matches.match_name LIKE '%$search%' OR 
                matches.status LIKE '%$search%' OR 
                matches.match_id LIKE '%$search%' OR 
                matches.team_1 LIKE '%$search%' OR 
                matches.team_2 LIKE '%$search%' OR 
                t1.t_name LIKE '%$search%' OR 
                t2.t_name LIKE '%$search%' OR 
                matches.match_date LIKE '%$search%' OR 
                matches.start_time LIKE '%$search%' OR 
                matches.venue LIKE '%$search%'
            )";
        }
        $result = mysqli_query($conn, $sql) or die("Error: ");

        if (mysqli_num_rows($result) > 0) { // Check if there are rows in the result
            while ($row = mysqli_fetch_assoc($result)) {
                $sql2 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_1']}'";
                $query = mysqli_query($conn, $sql2) or die("Error: ");
                $team1 = mysqli_fetch_assoc($query);

                $sql3 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_2']}'";
                $query3 = mysqli_query($conn, $sql3) or die("Error: ");
                $team2 = mysqli_fetch_assoc($query3);

                $score_log = json_decode($row['score_log'], true);

                echo "<div class='game-info' data-match_id='{$row['match_id']}' onclick='open_scoreboard(this)'>";
                echo "<div class='match-data'>";

                    echo "<div class='info'><p>" . (!empty($row['match_name']) ? $row['match_name'] : "Match 1 | No Tournament") . "</p></div>";

                    echo "<div class='info team-score'>";
                        echo "<div class='team'>";
                            if (!empty($team1['t_logo'])) {
                                echo "<img src='./assets/images/teams/{$team1['t_logo']}' alt='{$team1['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }
                            echo $team1['t_name'];
                        echo "</div>";                    
                        echo "<div class='score'>{$row['score_team_1']}</div>";
                    echo "</div>";

                    echo "<div class='info team-score'>";
                        echo "<div class='team'>";
                            if (!empty($team2['t_logo'])) {
                                echo "<img src='./assets/images/teams/{$team2['t_logo']}' alt='{$team2['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }
                            echo $team2['t_name'];
                        echo "</div>";                    
                        echo "<div class='score'>{$row['score_team_2']}</div>";
                    echo "</div>";

                    if(empty($row['toss_winner'])){
                        echo "<div class='info'><p>" . formatMatchTime($row['match_date'], $row['start_time']) . "</p></div>";
                    }else if($row['status'] == 'Live'){
                        

                        $team = '';
                        if($row['toss_winner'] == $team1['t_id']){
                            $team = $team1['t_name'];
                        }else{
                            $team = $team2['t_name'];
                        }

                        echo "<div class='info update'><p>" . $team . " Elected To ". $row['toss_decision'] ."</p></div>";
                    }else if($row['status'] == 'Completed'){
                        $winner = $score_log['winner'];
                        $winner_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `teams` WHERE t_id = '$winner'"))['t_name'];
                        // If match is not completed and no winner is declared
                        if (!empty($score_log['super_over_innings']) && is_array($score_log['super_over_innings'])){
                            echo "<div class='info update'><p>Match Tied (".$winner_name." Won The Match)</p></div>";
                        }else{
                            echo "<div class='info update'><p>".$winner_name." Won The Match</p></div>";
                        }
                        
                    }
                    
                echo "</div>";

                    echo "<div class='strt-btn'>";
                    
                    $scorers = json_decode($row['scorers']) ?? '[]'; // decode JSON array
                    $scorer_emails = explode(",", $scorers[0]);
                    $session_email = $_SESSION['email'];

                    if ($scorer_emails && in_array($session_email, $scorer_emails) && $row['status'] == 'Live') {
                        echo "<div class='info'><button class='start-btn' onclick='openDialog(this, event)'>Start</button></div>";
                    }
                    echo "</div>";

                echo "</div>";

                
            }
        }
    }
}

if($for == "manage_teams"){
    $user_id = $_SESSION['user'];
    $sql = "SELECT * FROM teams WHERE t_admin_id = '$user_id' AND t_sport = '$sport'";
                    $query = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($query) > 0){
                        echo '<div class="team-list">';

                        while($row = mysqli_fetch_assoc($query)){
                            if($row['t_logo']){
                                $src = "../assets/images/teams/".$row['t_logo'];
                            }else{
                                $src = "https://cdn-icons-png.flaticon.com/512/8140/8140303.png";
                            }
                            $data = <<<TEXT
                                        <div class="team" data-team_id="{$row['t_id']}" onclick="get_team_info(this)">
                                            <div class="logo"><img src='$src' alt=''></div>
                                            <div class="team-info">
                                                <h4>{$row['t_name']}</h4>
                                                <div class="other-info">
                                                    <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                                    </svg><span class="dt">
                                                    {$row['t_city']}</span></label>
                                                    <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                                    </svg><span class="dt">
                                                    {$row['t_coordinatorName']}</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    TEXT;
                            echo $data;
                        }
                        echo '</div>';
                    }else{
                        echo '<h2 class="no-data">No Team Found</h2>';
                    }
}

if($for == "manage_tournaments"){
    $user_id = $_SESSION['user'];
    $sql = "SELECT * FROM tournaments t join sports s ON s.sport_id = t.sport_id WHERE t.created_by = '$user_id' AND s.sport_name = '$sport'";
                    $query = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($query) > 0){
                        echo '<div class="team-list">';

                        while($row = mysqli_fetch_assoc($query)){
                            if($row['logo']){
                                $src = "../assets/images/tournaments/".$row['logo'];
                            }else{
                                $src = "https://cdn-icons-png.flaticon.com/512/8140/8140303.png";
                            }
                            $data = <<<TEXT
                                        <div class="team" data-team_id="{$row['tournament_id']}" onclick="get_team_info(this)">
                                            <div class="logo"><img src='$src' alt=''></div>
                                            <div class="team-info">
                                                <h4>{$row['tournament_name']}</h4>
                                                <div class="other-info">
                                                    <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                                    </svg><span class="dt">
                                                    {$row['city']}</span></label>
                                                    <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                                    </svg><span class="dt">
                                                    {$row['organizer_name']}</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    TEXT;
                            echo $data;
                        }
                        echo '</div>';
                    }else{
                        echo '<h2 class="no-data">No Tournament Found</h2>';
                    }
}

if($for == "manage_matches"){
    $user_id = $_SESSION['user'];
    $sql = "SELECT * FROM `matches` LEFT join `sports` ON sports.sport_id = matches.sport_id WHERE sports.sport_name = '$sport' AND matches.created_by = '$user_id'";
    $result = mysqli_query($conn, $sql) or die("Error: ");

        if (mysqli_num_rows($result) > 0) { // Check if there are rows in the result
            while ($row = mysqli_fetch_assoc($result)) {
                $sql2 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_1']}'";
                $query = mysqli_query($conn, $sql2) or die("Error: ");
                $team1 = mysqli_fetch_assoc($query);

                $sql3 = "SELECT * FROM `teams` WHERE t_id = '{$row['team_2']}'";
                $query3 = mysqli_query($conn, $sql3) or die("Error: ");
                $team2 = mysqli_fetch_assoc($query3);

                echo "<div class='game-info' data-match_id='{$row['match_id']}' onclick='edit_match(this)'>";
                echo "<div class='match-data'>";

                    echo "<div class='info'><p>" . (!empty($row['match_name']) ? $row['match_name'] : "Match 1 | No Tournament") . "</p></div>";

                    echo "<div class='info team-score'>";
                        echo "<div class='team'>";
                            if (!empty($team1['t_logo'])) {
                                echo "<img src='../assets/images/teams/{$team1['t_logo']}' alt='{$team1['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }
                            echo $team1['t_name'];
                        echo "</div>";                     
                        echo "<div class='score'>{$row['score_team_1']}</div>";
                    echo "</div>";

                    echo "<div class='info team-score'>";
                        echo "<div class='team'>";
                            if (!empty($team2['t_logo'])) {
                                echo "<img src='../assets/images/teams/{$team2['t_logo']}' alt='{$team2['t_name']}' onerror=\"this.style.opacity='0'\">";
                            }else{
                                echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' onerror=\"this.style.opacity='0'\">";
                            }
                            echo $team2['t_name'];
                        echo "</div>";                     
                        echo "<div class='score'>{$row['score_team_2']}</div>";
                    echo "</div>";

                    echo "<div class='info'><p>" . formatMatchTime($row['match_date'], $row['start_time']) . "</p></div>";
                echo "</div>";

                    echo "<div class='strt-btn'>";
                    
                    $scorers = json_decode($row['scorers']) ?? '[]'; // decode JSON array
                    $scorer_emails = [];

                    if (!empty($scorers) && isset($scorers[0])) {
                        $scorer_emails = explode(",", $scorers[0]);
                    }
                    
                    $session_email = $_SESSION['email'];

                    if ($scorer_emails && in_array($session_email, $scorer_emails) && $row['status'] == 'Live') {
                        echo "<div class='info'><button class='start-btn' onclick='openDialog(this, event)'>Start</button></div>";
                    }
                    echo "</div>";
                echo "</div>";
            }
        }else{
             echo '<h2 class="no-data">No Matches Found</h2>';
        }
}

if($for == "add_staff"){
    $sql = "SELECT * FROM users WHERE email LIKE '%$status%' AND place IS NOT NULL";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        $data = ''; // Initialize $data before the loop
        
        if($sport != 'saved'){
            $data .= "<h3>Select From Search</h3>";
        }
        while($row = mysqli_fetch_assoc($query)){

            if($row['user_photo']){
                $src = "../assets/images/users/".$row['user_photo'];
            }else{
                $src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";
            }
            
            $data.= <<<HTML
                <div class="staff-container">
                    
                    <div class="team" onclick="">
                        <div class="logo"><img src="{$src}" alt=""></div>
                        <div class="team-info">
                            <h4 class="staff-name" data-staff_email="{$row['email']}">{$row['lname']} {$row['fname']}</h4>
                            <div class="other-info">
                                <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['place']}</span></label>
                                <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['phone']}</span></label>
                            </div>
                        </div>
                        <div class="add-history-staff">
                            <svg onclick="add_staff(this)" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_500_232)"/>
                            <defs>
                            <linearGradient id="paint0_linear_500_232" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFEB0B"/>
                            <stop offset="1" stop-color="#C11218"/>
                            </linearGradient>
                            </defs>
                            </svg>
                        </div>
                    </div>
                    <div id="error-block {$row['user_id']}" class="error"></div>

                </div>
            HTML;
        }
        echo $data;
    }else{
        echo '<h2 class="no-data">No Staff Found</h2>';
    }
}

if($for == "add_player"){
    $sql = "SELECT * FROM users u WHERE u.user_id AND u.email LIKE '%$status%' AND u.place IS NOT NULL";
    $query = mysqli_query($conn, $sql);

    if(mysqli_num_rows($query) > 0){
        $data = ''; // Initialize $data before the loop
        
        if($sport != 'saved'){
            $data .= "<h3>Select From Search</h3>";
        }
        while($row = mysqli_fetch_assoc($query)){

            if($row['user_photo']){
                $src = "../assets/images/users/".$row['user_photo'];
            }else{
                $src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";
            }

            $data.= <<<HTML
                <div class="staff-container">
                    
                    <div class="team" onclick="" data-user_id="{$row['user_id']}">
                        <div class="logo"><img src="{$src}" alt=""></div>
                        <div class="team-info">
                            <h4 class="player-name" data-staff_email="{$row['email']}">{$row['lname']} {$row['fname']}</h4>
                            <div class="other-info">
                                <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['place']}</span></label>
                                <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['phone']}</span></label>
                            </div>
                        </div>
                        <div class="add-history-staff">
                            <svg onclick="select_player(this)" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_500_232)"/>
                            <defs>
                            <linearGradient id="paint0_linear_500_232" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFEB0B"/>
                            <stop offset="1" stop-color="#C11218"/>
                            </linearGradient>
                            </defs>
                            </svg>
                        </div>
                    </div>
                    <div id="error-block {$row['user_id']}" class="error"></div>

                </div>
                <div id="error-empty" class="error"></div>
            HTML;
        }
        echo $data;
    }else{
        echo '<h2 class="no-data">No Player Found</h2>';
    }
}

if($for == "add_umpire"){
    $sql = "SELECT * FROM users WHERE email LIKE '%$status%' AND place IS NOT NULL";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        $data = ''; // Initialize $data before the loop
        
        if($sport != 'saved'){
            $data .= "<h3>Select From Search</h3>";
        }
        while($row = mysqli_fetch_assoc($query)){
            $logoHtml = !empty($row['user_photo']) 
            ? '<img src="../assets/images/users/'.$row['user_photo'].'" alt="">' 
            : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";

            $data.= <<<HTML
                <div class="staff-container">
                    
                    <div class="team" onclick="">
                        <div class="logo">{$logoHtml}</div>
                        <div class="team-info">
                            <h4 class="staff-name" data-staff_email="{$row['email']}">{$row['lname']} {$row['fname']}</h4>
                            <div class="other-info">
                                <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['place']}</span></label>
                                <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['phone']}</span></label>
                            </div>
                        </div>
                        <div class="add-history-staff">
                            <svg onclick="add_person(this)" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_500_232)"/>
                            <defs>
                            <linearGradient id="paint0_linear_500_232" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFEB0B"/>
                            <stop offset="1" stop-color="#C11218"/>
                            </linearGradient>
                            </defs>
                            </svg>
                        </div>
                    </div>
                    <div id="error-block {$row['user_id']}" class="error"></div>

                </div>
            HTML;
        }
        echo $data;
    }else{
        if($sport != 'saved'){
            echo '<h2 class="no-data">No Umpire Found</h2>';
        }
    }
}

if($for == "add_scorer"){
    $sql = "SELECT * FROM users WHERE email LIKE '%$status%' AND place IS NOT NULL";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        $data = ''; // Initialize $data before the loop
        
        if($sport != 'saved'){
            $data .= "<h3>Select From Search</h3>";
        }
        while($row = mysqli_fetch_assoc($query)){
            $logoHtml = !empty($row['user_photo']) 
            ? '<img src="../assets/images/users/'.$row['user_photo'].'" alt="">' 
            : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";

            $data.= <<<HTML
                <div class="staff-container">
                    
                    <div class="team" onclick="">
                        <div class="logo">{$logoHtml}</div>
                        <div class="team-info">
                            <h4 class="staff-name" data-staff_email="{$row['email']}">{$row['lname']} {$row['fname']}</h4>
                            <div class="other-info">
                                <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['place']}</span></label>
                                <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['phone']}</span></label>
                            </div>
                        </div>
                        <div class="add-history-staff">
                            <svg onclick="add_person(this)" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_500_232)"/>
                            <defs>
                            <linearGradient id="paint0_linear_500_232" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFEB0B"/>
                            <stop offset="1" stop-color="#C11218"/>
                            </linearGradient>
                            </defs>
                            </svg>
                        </div>
                    </div>
                    <div id="error-block {$row['user_id']}" class="error"></div>

                </div>
            HTML;
        }
        echo $data;
    }else{
        if($sport != 'saved'){
            echo '<h2 class="no-data">No Scorer Found</h2>';
        }
    }
}

if($for == "add_commentator"){
    $sql = "SELECT * FROM users WHERE email LIKE '%$status%' AND place IS NOT NULL";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        $data = ''; // Initialize $data before the loop
        
        if($sport != 'saved'){
            $data .= "<h3>Select From Search</h3>";
        }
        while($row = mysqli_fetch_assoc($query)){
            $logoHtml = !empty($row['user_photo']) 
            ? '<img src="../assets/images/users/'.$row['user_photo'].'" alt="">' 
            : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";

            $data.= <<<HTML
                <div class="staff-container">
                    
                    <div class="team" onclick="">
                        <div class="logo">{$logoHtml}</div>
                        <div class="team-info">
                            <h4 class="staff-name" data-staff_email="{$row['email']}">{$row['lname']} {$row['fname']}</h4>
                            <div class="other-info">
                                <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['place']}</span></label>
                                <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><span class="dt">
                                {$row['phone']}</span></label>
                            </div>
                        </div>
                        <div class="add-history-staff">
                            <svg onclick="add_person(this)" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_500_232)"/>
                            <defs>
                            <linearGradient id="paint0_linear_500_232" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFEB0B"/>
                            <stop offset="1" stop-color="#C11218"/>
                            </linearGradient>
                            </defs>
                            </svg>
                        </div>
                    </div>
                    <div id="error-block {$row['user_id']}" class="error"></div>

                </div>
            HTML;
        }
        echo $data;
    }else{
        if($sport != 'saved'){
            echo '<h2 class="no-data">No Commentator Found</h2>';
        }
    }
}

?>
