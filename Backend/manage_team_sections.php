<?php
    header('Content-Type: application/json');
    session_start();
    include '../config.php';

    $data = file_get_contents('php://input');
    $data = json_decode($data, true);

    $sec = mysqli_real_escape_string($conn, $data['sec']);
    $mem = mysqli_real_escape_string($conn, $data['mem']);
    $team = mysqli_real_escape_string($conn, $data['team']) ?? ''; 

    if($sec == 'Schedule'){
        $no_event = <<<TEXT
                        <div class="no-event">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/man-with-no-schedule-illustration-download-in-svg-png-gif-file-formats--calendar-appointment-empty-state-pack-people-illustrations-10920936.png" alt="" >
                            <h3>No Events Scheduled</h3>                                    
                        </div>
                    TEXT;

        $sql = "Select * FROM matches WHERE team_1 = '$team' OR team_2 = '$team' AND status IN ('Upcoming','Live')";
        $result = mysqli_query($conn,$sql);

        if(mysqli_num_rows($result) > 0){
            $events = null;
            while($row = mysqli_fetch_assoc($result)){
                $opponent = null;
                if($row['team_1'] == $team){
                    $opponent = $row['team_2'];
                }else if($row['team_2'] == $team){
                    $opponent = $row['team_1'];
                }

                $result2 = mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$opponent' LIMIT 1");
                $row2 = mysqli_fetch_assoc($result2);

                $timestamp = strtotime($row['match_date']);
                // Get month and year in words
                $month_year = date("F, Y", $timestamp);

                $t1_score = $row['score_team_1'] ?? 0;
                $t2_score = $row['score_team_2'] ?? 0;

                $events .= <<<TEXT
                        <div class="Events">
                            <div class="event">
                                <label for="" class="schedule-time ">$month_year</label>
                                <div class="opponent">
                                    <label for="opponent-team">Vs. {$row2['t_name']}</label>
                                    <label for="date">{$row['match_date']}</label>
                                    <label for="score">$t1_score - $t2_score</label>
                                </div>
                            </div>
                        </div>
                    TEXT;
            }
            
            echo $events;
        }else{
            echo $no_event;
        }

        $new_btn = <<<TEXT
                        <div class="new-btn">
                            <button onclick="add_event(event)" type="submit" id="add-event">Add Event</button>
                        </div>
                    TEXT;
        
        //echo $new_btn;
    }

    if($sec == 'Team' && $mem == 'Staff'){
        $sql_count = "SELECT COUNT(*) as count FROM staff WHERE team = '$team'";
        $query_count = mysqli_query($conn, $sql_count);
        $count_data = mysqli_fetch_assoc($query_count);
        $current_count = $count_data['count'];

        $staff_mem = '';
        $count =0;
        $sql = "SELECT * FROM staff s join users u ON s.staff_id = u.user_id WHERE team = '$team' ORDER BY 'name'";
        $query = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($query)){

            if($row['user_photo']){
                $src = "../assets/images/users/".$row['user_photo'];
            }else{
                $src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";
            }

            $count++;
            $staff_mem.= "
                        <div class='mem' data-id='{$row['staff_id']}' >$count
                            <div class='mem-img'><img src='$src' alt='' class='mem-img'></div>
                            <div class='mem-name'>{$row['name']}</div>
                        </div>
            ";
        }

         $for_staff = <<<TEXT
                        <div class="members">
                            <div class="mem-container">
                                <div class="mem-type staff active">Staff</div>
                                <div class="mem-type player">Players</div>
                            </div>
                        </div>

                        <div class="mem-info">
                            <div class="event">
                                <label for="" class="schedule-time ">Team Staff ($current_count/1)</label>
                                <div class="add-member" onclick="add_member()">
                                    <label for="add-icon" class="add-icon"><svg width="19" height="20" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 0.0634766C8.19271 0.0634766 8.85677 0.152018 9.49219 0.329102C10.1276 0.506185 10.7266 0.756185 11.2891 1.0791C11.8516 1.40202 12.3568 1.79264 12.8047 2.25098C13.2526 2.70931 13.6432 3.21712 13.9766 3.77441C14.3099 4.33171 14.5625 4.92806 14.7344 5.56348C14.9062 6.19889 14.9948 6.86556 15 7.56348C15 8.25618 14.9115 8.92025 14.7344 9.55566C14.5573 10.1911 14.3073 10.79 13.9844 11.3525C13.6615 11.915 13.2708 12.4202 12.8125 12.8682C12.3542 13.3161 11.8464 13.7067 11.2891 14.04C10.7318 14.3734 10.1354 14.626 9.5 14.7979C8.86458 14.9697 8.19792 15.0583 7.5 15.0635C6.80729 15.0635 6.14323 14.9749 5.50781 14.7979C4.8724 14.6208 4.27344 14.3708 3.71094 14.0479C3.14844 13.7249 2.64323 13.3343 2.19531 12.876C1.7474 12.4176 1.35677 11.9098 1.02344 11.3525C0.690104 10.7952 0.4375 10.1989 0.265625 9.56348C0.09375 8.92806 0.00520833 8.26139 0 7.56348C0 6.87077 0.0885417 6.20671 0.265625 5.57129C0.442708 4.93587 0.692708 4.33691 1.01562 3.77441C1.33854 3.21191 1.72917 2.70671 2.1875 2.25879C2.64583 1.81087 3.15365 1.42025 3.71094 1.08691C4.26823 0.753581 4.86458 0.500977 5.5 0.329102C6.13542 0.157227 6.80208 0.0686849 7.5 0.0634766ZM7.5 14.0635C8.09896 14.0635 8.67448 13.9854 9.22656 13.8291C9.77865 13.6729 10.2943 13.4541 10.7734 13.1729C11.2526 12.8916 11.6927 12.5531 12.0938 12.1572C12.4948 11.7614 12.8333 11.3239 13.1094 10.8447C13.3854 10.3656 13.6042 9.84733 13.7656 9.29004C13.9271 8.73275 14.0052 8.15723 14 7.56348C14 6.96452 13.9219 6.389 13.7656 5.83691C13.6094 5.28483 13.3906 4.76921 13.1094 4.29004C12.8281 3.81087 12.4896 3.37077 12.0938 2.96973C11.6979 2.56868 11.2604 2.23014 10.7812 1.9541C10.3021 1.67806 9.78385 1.45931 9.22656 1.29785C8.66927 1.13639 8.09375 1.05827 7.5 1.06348C6.90104 1.06348 6.32552 1.1416 5.77344 1.29785C5.22135 1.4541 4.70573 1.67285 4.22656 1.9541C3.7474 2.23535 3.30729 2.57389 2.90625 2.96973C2.50521 3.36556 2.16667 3.80306 1.89062 4.28223C1.61458 4.76139 1.39583 5.27962 1.23438 5.83691C1.07292 6.39421 0.994792 6.96973 1 7.56348C1 8.16243 1.07812 8.73796 1.23438 9.29004C1.39062 9.84212 1.60938 10.3577 1.89062 10.8369C2.17188 11.3161 2.51042 11.7562 2.90625 12.1572C3.30208 12.5583 3.73958 12.8968 4.21875 13.1729C4.69792 13.4489 5.21615 13.6676 5.77344 13.8291C6.33073 13.9906 6.90625 14.0687 7.5 14.0635ZM8 7.06348H12V8.06348H8V12.0635H7V8.06348H3V7.06348H7V3.06348H8V7.06348Z" fill="black"/>
                                    </svg>
                                    </label>
                                    <label for="date">Add a Staff </label>
                                </div>
                                <div id="error-staff_full" class="error"></div>
                            </div>

                            <div class="mem-list">
                                <h3 class="mem-head">Staff Member :</h3>
                                $staff_mem
                            </div>
                        </div>
                    TEXT;
                    echo ($for_staff);
    }else if($sec == 'Team' && $mem == 'Players'){
        $sql_count = "SELECT COUNT(*) as count FROM players WHERE team_id = '$team'";
        $query_count = mysqli_query($conn, $sql_count);
        $count_data = mysqli_fetch_assoc($query_count);
        $current_count = $count_data['count'];

        $players = '';
        $count =0;
        $sql = "SELECT * FROM players WHERE team_id = '$team' ORDER BY 'name'";
        $query = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($query)){

            if($row['photo']){
                $src = "../assets/images/users/".$row['photo'];
            }else{
                $src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s";
            }

            $count++;
            $players.= "
                        <div class='mem' data-id='{$row['user_id']}' >$count
                            <div class='mem-img'><img src='$src' alt='' class='mem-img'></div>
                            <div class='mem-name'>{$row['player_name']}</div>
                        </div>
            ";
        }

        $for_player = <<<TEXT
                        <div class="members">
                            <div class="mem-container">
                                <div class="mem-type staff ">Staff</div>
                                <div class="mem-type player active">Players</div>
                            </div>
                        </div>

                        <div class="mem-info">
                            <div class="event">
                                <label for="" class="schedule-time ">Players ($current_count)</label>
                                <div class="add-member"  onclick="add_player()">
                                    <label for="add-icon" class="add-icon"><svg width="19" height="20" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 0.0634766C8.19271 0.0634766 8.85677 0.152018 9.49219 0.329102C10.1276 0.506185 10.7266 0.756185 11.2891 1.0791C11.8516 1.40202 12.3568 1.79264 12.8047 2.25098C13.2526 2.70931 13.6432 3.21712 13.9766 3.77441C14.3099 4.33171 14.5625 4.92806 14.7344 5.56348C14.9062 6.19889 14.9948 6.86556 15 7.56348C15 8.25618 14.9115 8.92025 14.7344 9.55566C14.5573 10.1911 14.3073 10.79 13.9844 11.3525C13.6615 11.915 13.2708 12.4202 12.8125 12.8682C12.3542 13.3161 11.8464 13.7067 11.2891 14.04C10.7318 14.3734 10.1354 14.626 9.5 14.7979C8.86458 14.9697 8.19792 15.0583 7.5 15.0635C6.80729 15.0635 6.14323 14.9749 5.50781 14.7979C4.8724 14.6208 4.27344 14.3708 3.71094 14.0479C3.14844 13.7249 2.64323 13.3343 2.19531 12.876C1.7474 12.4176 1.35677 11.9098 1.02344 11.3525C0.690104 10.7952 0.4375 10.1989 0.265625 9.56348C0.09375 8.92806 0.00520833 8.26139 0 7.56348C0 6.87077 0.0885417 6.20671 0.265625 5.57129C0.442708 4.93587 0.692708 4.33691 1.01562 3.77441C1.33854 3.21191 1.72917 2.70671 2.1875 2.25879C2.64583 1.81087 3.15365 1.42025 3.71094 1.08691C4.26823 0.753581 4.86458 0.500977 5.5 0.329102C6.13542 0.157227 6.80208 0.0686849 7.5 0.0634766ZM7.5 14.0635C8.09896 14.0635 8.67448 13.9854 9.22656 13.8291C9.77865 13.6729 10.2943 13.4541 10.7734 13.1729C11.2526 12.8916 11.6927 12.5531 12.0938 12.1572C12.4948 11.7614 12.8333 11.3239 13.1094 10.8447C13.3854 10.3656 13.6042 9.84733 13.7656 9.29004C13.9271 8.73275 14.0052 8.15723 14 7.56348C14 6.96452 13.9219 6.389 13.7656 5.83691C13.6094 5.28483 13.3906 4.76921 13.1094 4.29004C12.8281 3.81087 12.4896 3.37077 12.0938 2.96973C11.6979 2.56868 11.2604 2.23014 10.7812 1.9541C10.3021 1.67806 9.78385 1.45931 9.22656 1.29785C8.66927 1.13639 8.09375 1.05827 7.5 1.06348C6.90104 1.06348 6.32552 1.1416 5.77344 1.29785C5.22135 1.4541 4.70573 1.67285 4.22656 1.9541C3.7474 2.23535 3.30729 2.57389 2.90625 2.96973C2.50521 3.36556 2.16667 3.80306 1.89062 4.28223C1.61458 4.76139 1.39583 5.27962 1.23438 5.83691C1.07292 6.39421 0.994792 6.96973 1 7.56348C1 8.16243 1.07812 8.73796 1.23438 9.29004C1.39062 9.84212 1.60938 10.3577 1.89062 10.8369C2.17188 11.3161 2.51042 11.7562 2.90625 12.1572C3.30208 12.5583 3.73958 12.8968 4.21875 13.1729C4.69792 13.4489 5.21615 13.6676 5.77344 13.8291C6.33073 13.9906 6.90625 14.0687 7.5 14.0635ZM8 7.06348H12V8.06348H8V12.0635H7V8.06348H3V7.06348H7V3.06348H8V7.06348Z" fill="black"/>
                                    </svg>
                                    </label>
                                    <label for="date">Add Player </label>
                                </div>
                                <div id="error-player_full" class="error"></div>
                            </div>

                            <div class="mem-list">
                                <h3 class="mem-head">Players :</h3>
                                $players
                            </div>
                        </div>
                    TEXT;
                    echo ($for_player);
    }else{}
?>