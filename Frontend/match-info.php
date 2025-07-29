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

    $match_id = $_GET['match_id'];

    if (empty($match_id)) {
        header("Location: ../dashboard.php?update=Live&sport=CRICKET"); // Change 'index.php' to your actual file
        exit();
    }

    if (isset($_POST['confirm']) && isset($_POST['match_id'])) {

        $delete = mysqli_query($conn, "DELETE FROM matches WHERE match_id = '$match_id'");

        if ($delete) {
            echo "<script>window.history.back();</script>";
        } else {
            echo "<script>alert('Failed to delete match');</script>";
        }
    }


    // Example: assume $conn is your database connection
    $query = mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'");
    $result = mysqli_fetch_assoc($query);

    if(mysqli_num_rows($query) == 0) {
        echo "<script>window.history.back();</script>";
        exit;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Details</title>
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
            --primary-light-transparent: rgba(250, 192, 31, 0.1);
            --primary-dark-transparent: rgba(248, 57, 0, 0.1);
            --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            --text-color: #333333;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
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
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .header {
            background: var(--background);
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
            background: white;
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
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background: white;
        }

        .team-logo-edit {
            position: absolute;
            bottom: -5px;
            right: -5px;
            background: white;
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
            background: #f0f0f0;
        }

        .team-logo-edit svg {
            width: 20px;
            height: 20px;
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
        }

        .vs {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-dark);
            margin: 0 20px;
        }

        /* Match Details Section */
        .form-section {
            padding: 30px;
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
            color: var(--primary-dark);
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: #f9f9f9;
        }

        .form-input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-light-transparent);
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            background-color: #f9f9f9;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
        }

        /* Edit mode styles */
        .edit-mode .form-input,
        .edit-mode .form-select,
        .edit-mode .team-name {
            background-color: white;
            border-color: #ccc;
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
            box-shadow: 0 4px 12px rgba(248, 57, 0, 0.2);
        }

        .logout-btn:hover,
        .save-btn:hover {
            background-color: #e03400;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 57, 0, 0.3);
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
            background: #fff;
            padding: 30px;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
        }

        .popup-input {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
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
            background-color: #f0f0f0;
            color: #333;
            border: none;
        }

        .popup-btn.cancel:hover {
            background-color: #e0e0e0;
        }

        .popup-btn.confirm {
            background-color: var(--primary-dark);
            color: white;
            border: none;
        }

        .popup-btn.confirm:hover {
            background-color: #e03400;
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
            color:red; 
            width:100%;
            font-size:14px;
            margin: 5px;
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
                <div class="edit-toggle" id="editToggle">
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23.3333 7.29182L27.7083 11.6668M29.7281 9.60328C30.3025 9.02892 30.6252 8.24992 30.6252 7.43766C30.6252 6.62539 30.3025 5.84639 29.7281 5.27203C29.1538 4.69767 28.3748 4.375 27.5625 4.375C26.7502 4.375 25.9712 4.69767 25.3969 5.27203L13.125 17.5002V21.8752H17.5L29.7281 9.60328Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <h1 style="text-align: center; font-size: 1.8rem;">Match Details</h1>
        </div>
        <div class="password">
            <div class="pass-el">
                <input type="password" class="pass" value="<?php echo $result['password'] ?>" disabled>
                <div id= "editToggle1">
                    <svg class="eye" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 17.1429C10.932 17.1429 9.88795 16.8412 8.99992 16.2761C8.1119 15.711 7.41976 14.9078 7.01105 13.9681C6.60234 13.0284 6.4954 11.9943 6.70376 10.9967C6.91212 9.99906 7.42642 9.08269 8.18162 8.36345C8.93683 7.64421 9.89901 7.1544 10.9465 6.95596C11.994 6.75752 13.0798 6.85937 14.0665 7.24862C15.0532 7.63787 15.8966 8.29704 16.4899 9.14278C17.0833 9.98852 17.4 10.9828 17.4 12C17.4 13.364 16.8311 14.6721 15.8184 15.6365C14.8057 16.601 13.4322 17.1429 12 17.1429ZM12 4C3.6 4 0 12 0 12C0 12 3.6 20 12 20C20.4 20 24 12 24 12C24 12 20.4 4 12 4Z" fill="black"/>
                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" fill="black"/>
                    </svg>
                </div>
            </div>
        </div>
        <!-- Teams Display -->
        <?php
            $sql2 = "SELECT * FROM `teams` WHERE t_id = '{$result['team_1']}'";
            $query = mysqli_query($conn, $sql2) or die("Error: ");
            $team1 = mysqli_fetch_assoc($query);

            $sql3 = "SELECT * FROM `teams` WHERE t_id = '{$result['team_2']}'";
            $query3 = mysqli_query($conn, $sql3) or die("Error: ");
            $team2 = mysqli_fetch_assoc($query3);
        ?>
        <div class="teams-section">
            <div class="team">
                <div class="team-logo-container">
                    <?php if(!($team1['t_logo'])){ echo "<img id='team1Logo' class='team-logo'  src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' alt=''>"; }else{
                        echo "<img id='team1Logo' class='team-logo'  src='../assets/images/teams/{$team1['t_logo']}' alt=''>";
                    } ?>
                    <div class="team-logo-edit" onclick="document.getElementById('team1LogoInput').click()">
                        <svg width="28" height="22" viewBox="0 0 31 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26.2918 3.24984H23.8467L22.3051 1.70817C21.4125 0.815546 19.8461 0.166504 18.5835 0.166504H12.4168C11.1542 0.166504 9.58787 0.815546 8.69525 1.70817L7.15358 3.24984H4.7085C2.15858 3.24984 0.0834961 5.32492 0.0834961 7.87484V20.2082C0.0834961 22.7581 2.15858 24.8332 4.7085 24.8332H26.2918C28.8417 24.8332 30.9168 22.7581 30.9168 20.2082V7.87484C30.9168 5.32492 28.8417 3.24984 26.2918 3.24984ZM15.5002 18.6665C14.0689 18.6663 12.6963 18.0975 11.6844 17.0853C10.6725 16.0731 10.1041 14.7004 10.1043 13.2691C10.1045 11.8379 10.6733 10.4653 11.6855 9.45338C12.6977 8.44146 14.0704 7.87309 15.5017 7.87329C16.933 7.8735 18.3055 8.44227 19.3175 9.45447C20.3294 10.4667 20.8977 11.8394 20.8975 13.2707C20.8973 14.7019 20.3286 16.0745 19.3164 17.0864C18.3042 18.0983 16.9314 18.6667 15.5002 18.6665ZM24.7502 11.4191C24.487 11.4191 24.2264 11.3673 23.9832 11.2666C23.74 11.1659 23.5191 11.0182 23.333 10.8321C23.1469 10.646 22.9993 10.4251 22.8986 10.1819C22.7978 9.93877 22.746 9.67815 22.746 9.41496C22.746 9.15177 22.7978 8.89116 22.8986 8.648C22.9993 8.40484 23.1469 8.18391 23.333 7.9978C23.5191 7.8117 23.74 7.66407 23.9832 7.56335C24.2264 7.46263 24.487 7.4108 24.7502 7.4108C25.2817 7.4108 25.7915 7.62195 26.1673 7.9978C26.5432 8.37366 26.7543 8.88342 26.7543 9.41496C26.7543 9.9465 26.5432 10.4563 26.1673 10.8321C25.7915 11.208 25.2817 11.4191 24.7502 11.4191Z" fill="var(--primary-dark)"/>
                        </svg>
                        <input type="file" id="team1LogoInput" class="file-input" accept="image/*">
                    </div>
                </div>
                <input type="text" class="team-name" value="<?php echo $team1['t_name']; ?>" disabled>
            </div>
            
            <div class="vs">VS</div>
            
            <div class="team">
                <div class="team-logo-container">
                    <?php if(!($team2['t_logo'])){ echo "<img id='team2Logo' class='team-logo'  src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' alt=''>"; }else{
                        echo "<img id='team2Logo' class='team-logo'  src='../assets/images/teams/{$team2['t_logo']}' alt=''>";
                    } ?>
                    <div class="team-logo-edit" onclick="document.getElementById('team2LogoInput').click()">
                        <svg width="28" height="22" viewBox="0 0 31 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26.2918 3.24984H23.8467L22.3051 1.70817C21.4125 0.815546 19.8461 0.166504 18.5835 0.166504H12.4168C11.1542 0.166504 9.58787 0.815546 8.69525 1.70817L7.15358 3.24984H4.7085C2.15858 3.24984 0.0834961 5.32492 0.0834961 7.87484V20.2082C0.0834961 22.7581 2.15858 24.8332 4.7085 24.8332H26.2918C28.8417 24.8332 30.9168 22.7581 30.9168 20.2082V7.87484C30.9168 5.32492 28.8417 3.24984 26.2918 3.24984ZM15.5002 18.6665C14.0689 18.6663 12.6963 18.0975 11.6844 17.0853C10.6725 16.0731 10.1041 14.7004 10.1043 13.2691C10.1045 11.8379 10.6733 10.4653 11.6855 9.45338C12.6977 8.44146 14.0704 7.87309 15.5017 7.87329C16.933 7.8735 18.3055 8.44227 19.3175 9.45447C20.3294 10.4667 20.8977 11.8394 20.8975 13.2707C20.8973 14.7019 20.3286 16.0745 19.3164 17.0864C18.3042 18.0983 16.9314 18.6667 15.5002 18.6665ZM24.7502 11.4191C24.487 11.4191 24.2264 11.3673 23.9832 11.2666C23.74 11.1659 23.5191 11.0182 23.333 10.8321C23.1469 10.646 22.9993 10.4251 22.8986 10.1819C22.7978 9.93877 22.746 9.67815 22.746 9.41496C22.746 9.15177 22.7978 8.89116 22.8986 8.648C22.9993 8.40484 23.1469 8.18391 23.333 7.9978C23.5191 7.8117 23.74 7.66407 23.9832 7.56335C24.2264 7.46263 24.487 7.4108 24.7502 7.4108C25.2817 7.4108 25.7915 7.62195 26.1673 7.9978C26.5432 8.37366 26.7543 8.88342 26.7543 9.41496C26.7543 9.9465 26.5432 10.4563 26.1673 10.8321C25.7915 11.208 25.2817 11.4191 24.7502 11.4191Z" fill="var(--primary-dark)"/>
                        </svg>
                        <input type="file" id="team2LogoInput" class="file-input" accept="image/*">
                    </div>
                </div>
                <input type="text" class="team-name" value="<?php echo $team2['t_name']; ?>" disabled>
            </div>
        </div>
        
        <!-- Match Details Form -->
        <div class="form-section">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">City/Town</label>
                    <input type="text" class="form-input" value="<?php echo $result['venue']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Ground</label>
                    <input type="text" class="form-input" value="<?php echo $result['venue']; ?>" disabled>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-input" value="<?php echo $result['match_date']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <input type="time" class="form-input" value="<?php echo $result['start_time']; ?>" disabled>
                </div>
            </div>
            
            <div class="fixed-info">
                <div class="form-group">
                    <?php
                        $umpires = json_decode($result['umpires']);

                        // Ensure $umpires is an array before filtering
                        if (is_array($umpires)) {
                            $valid_emails = array_filter($umpires);
                        } else {
                            $valid_emails = [];
                        }

                        if (!empty($valid_emails)) {
                            $emails_list = "'" . implode("','", $valid_emails) . "'";
                            $query = "SELECT fname, lname, email FROM users WHERE email IN ($emails_list)";
                            $result0 = mysqli_query($conn, $query);

                            $names = [];

                            while ($row = mysqli_fetch_assoc($result0)) {
                                $names[] = $row['fname'] . ' ' . $row['lname'];
                            }

                            // Output or use the names
                        }
                    ?>
                    <label class="form-label">Umpires</label>
                    <p class="name-info">
                    <?php 
                        if (!empty($valid_emails)) {
                            $count = count($names);
                            foreach ($names as $i => $name) {
                                echo $name;
                                if ($i < $count - 1) {
                                    echo ', ';
                                }
                            };
                        }
                    ?>
                    </p>
                    <svg onclick="select_person(this)" class="pencil" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 19H6.425L16.2 9.225L14.775 7.8L5 17.575V19ZM3 21V16.75L16.2 3.575C16.4 3.39167 16.621 3.25 16.863 3.15C17.105 3.05 17.359 3 17.625 3C17.891 3 18.1493 3.05 18.4 3.15C18.6507 3.25 18.8673 3.4 19.05 3.6L20.425 5C20.625 5.18333 20.771 5.4 20.863 5.65C20.955 5.9 21.0007 6.15 21 6.4C21 6.66667 20.9543 6.921 20.863 7.163C20.7717 7.405 20.6257 7.62567 20.425 7.825L7.25 21H3ZM15.475 8.525L14.775 7.8L16.2 9.225L15.475 8.525Z" fill="black"/>
                    </svg>
                </div>
                <div class="form-group">
                     <?php
                        $commentators = json_decode($result['commentators']);
                        
                        // Ensure $umpires is an array before filtering
                        if (is_array($commentators)) {
                            $valid_emails = array_filter($commentators);
                        } else {
                            $valid_emails = [];
                        }

                        if (!empty($valid_emails)) {
                            $emails_list = "'" . implode("','", $valid_emails) . "'";
                            $query = "SELECT fname, lname, email FROM users WHERE email IN ($emails_list)";
                            $result1 = mysqli_query($conn, $query);

                            $names = [];

                            while ($row = mysqli_fetch_assoc($result1)) {
                                $names[] = $row['fname'] . ' ' . $row['lname'];
                            }

                            // Output or use the names
                            
                        }
                    ?>
                    <label class="form-label">Commentator</label>
                    <p class="name-info">
                    <?php 
                        if (!empty($valid_emails)) {
                            $count = count($names);
                            foreach ($names as $i => $name) {
                                echo $name;
                                if ($i < $count - 1) {
                                    echo ', ';
                                }
                            };
                        }
                    ?>
                    </p>
                    <svg onclick="select_person(this)" class="pencil" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 19H6.425L16.2 9.225L14.775 7.8L5 17.575V19ZM3 21V16.75L16.2 3.575C16.4 3.39167 16.621 3.25 16.863 3.15C17.105 3.05 17.359 3 17.625 3C17.891 3 18.1493 3.05 18.4 3.15C18.6507 3.25 18.8673 3.4 19.05 3.6L20.425 5C20.625 5.18333 20.771 5.4 20.863 5.65C20.955 5.9 21.0007 6.15 21 6.4C21 6.66667 20.9543 6.921 20.863 7.163C20.7717 7.405 20.6257 7.62567 20.425 7.825L7.25 21H3ZM15.475 8.525L14.775 7.8L16.2 9.225L15.475 8.525Z" fill="black"/>
                    </svg>
                </div>
                <div class="form-group">
                    <?php
                        $scorers = json_decode($result['scorers']);
                        
                        // Ensure $umpires is an array before filtering
                        if (is_array($scorers)) {
                            $valid_emails = array_filter($scorers);
                        } else {
                            $valid_emails = [];
                        }

                        if (!empty($valid_emails)) {
                            $emails_list = "'" . implode("','", $valid_emails) . "'";
                            $query = "SELECT fname, lname, email FROM users WHERE email IN ($emails_list)";
                            $result2 = mysqli_query($conn, $query);

                            $names = [];

                            while ($row = mysqli_fetch_assoc($result2)) {
                                $names[] = $row['fname'] . ' ' . $row['lname'];
                            }

                            // Output or use the names
                            
                        }
                    ?>
                    <label class="form-label">Scorers</label>
                    <p class="name-info">
                    <?php 
                       if (!empty($valid_emails)) {
                            $count = count($names);
                            foreach ($names as $i => $name) {
                                echo $name;
                                if ($i < $count - 1) {
                                    echo ', ';
                                }
                            };
                        }
                    ?>
                    </p>
                    <svg onclick="select_person(this)" class="pencil" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 19H6.425L16.2 9.225L14.775 7.8L5 17.575V19ZM3 21V16.75L16.2 3.575C16.4 3.39167 16.621 3.25 16.863 3.15C17.105 3.05 17.359 3 17.625 3C17.891 3 18.1493 3.05 18.4 3.15C18.6507 3.25 18.8673 3.4 19.05 3.6L20.425 5C20.625 5.18333 20.771 5.4 20.863 5.65C20.955 5.9 21.0007 6.15 21 6.4C21 6.66667 20.9543 6.921 20.863 7.163C20.7717 7.405 20.6257 7.62567 20.425 7.825L7.25 21H3ZM15.475 8.525L14.775 7.8L16.2 9.225L15.475 8.525Z" fill="black"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="error" id="error-datetime"></div>
        <div style="text-align: center;margin-top: 30px;display: flex;flex-direction: column; align-items: center;">
        <button class="save-btn">SAVE CHANGES</button>        
        <button class="logout-btn">DELETE MATCH</button>
            </div>
    </div>
    <form method="post" class="popup-overlay" id="popupOverlay">
        <div class="popup-box" id="popupBox">
            <input type="hidden" name="match_id" value="<?php echo $match_id; ?>"> <!-- ✅ This is required -->

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
        const matchID = <?php echo json_encode($match_id); ?>;
        let edit = false;
        const inputs = document.querySelectorAll("input:not(.file-input)");
        const logoEdits = document.querySelectorAll(".team-logo-edit");
        const pencil = document.querySelectorAll(".pencil");
        const pass = document.querySelector(".pass");
        const save_btn = document.querySelector(".save-btn");
        save_btn.style.display = "none";
        let Umpires = [];
        let Scorers = [];
        let Commentators = [];
        let next_page = document.querySelector('.officials-frame');

        let select_person = (el) => {
            let parent = el.closest('.form-group');
            let text = parent.querySelector('.form-label').textContent.trim();
            next_page.src = `./select-officials.php?p=${text}`;
            next_page.classList.add('active');
        };
        
         window.addEventListener("message", (event) => {
            if (event.data === "closeIframe") {
                next_page.classList.remove('active');  

            }

            if (event.data.type === "emailList" && (event.data.Umpires)) {
                let arr = event.data.Umpires;
                Umpires = arr;
            }

            if (event.data.type === "emailList" && (event.data.Scorers)) {
                let arr = event.data.Scorers;
                Scorers = arr;

            }

            if (event.data.type === "emailList" && (event.data.Commentator)) {
                let arr = event.data.Commentator;
                Commentators = arr;
            }
        });

        let passfield = true;
        
        // Initially hide edit buttons
        logoEdits.forEach(el => el.style.display = "none");

        toggle.addEventListener("click", () => {
            edit = !edit;

            if (edit) {
                // Switch to edit icon
                toggle.innerHTML = '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.3227 15.3097L13.125 17.5002V21.8752H17.5L19.6773 19.6906M22.594 16.7593L29.7252 9.60328C30.2996 9.02892 30.6222 8.24992 30.6222 7.43766C30.6222 6.62539 30.2996 5.84639 29.7252 5.27203C29.1508 4.69767 28.3719 4.375 27.5596 4.375C26.7473 4.375 25.9683 4.69767 25.394 5.27203L18.2292 12.412M23.3333 7.29182L27.7083 11.6668M4.375 4.37516L30.625 30.6252" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
                
                // Enable inputs
                inputs.forEach(el => el.disabled = false);
                
                // Show logo edit buttons
                logoEdits.forEach(el => el.style.display = "flex");

                pencil.forEach(el => el.style.display = "flex");

                save_btn.style.display = "block";
                
                // Add edit mode class
                document.querySelector('.container').classList.add('edit-mode');
            } else {
                // Switch back to edit toggle icon
                toggle.innerHTML = '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23.3333 7.29182L27.7083 11.6668M29.7281 9.60328C30.3025 9.02892 30.6252 8.24992 30.6252 7.43766C30.6252 6.62539 30.3025 5.84639 29.7281 5.27203C29.1538 4.69767 28.3748 4.375 27.5625 4.375C26.7502 4.375 25.9712 4.69767 25.3969 5.27203L13.125 17.5002V21.8752H17.5L29.7281 9.60328Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                
                // Disable inputs
                inputs.forEach(el => el.disabled = true);
                
                // Hide logo edit buttons
                logoEdits.forEach(el => el.style.display = "none");

                pencil.forEach(el => el.style.display = "none");

                save_btn.style.display = "none";
                
                // Remove edit mode class
                document.querySelector('.container').classList.remove('edit-mode');
            }
        });

        // Handle team logo uploads
        document.getElementById('team1LogoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('team1Logo').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('team2LogoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('team2Logo').src = event.target.result;
                };
                reader.readAsDataURL(file);
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
        "logout": "Are you sure you want to Delete the Match?"
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

        const toggleBtn = document.getElementById("editToggle1");

        toggleBtn.addEventListener("click", () => {
            if (pass.type === "password") {
                pass.type = "text";
                toggleBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.99988 4.03116L4.03103 3L20.9999 19.9689L19.9688 21L2.99988 4.03116ZM12.1565 9.00389L14.9958 11.8431C14.9563 11.1029 14.6445 10.4034 14.1204 9.87928C13.5962 9.35513 12.8967 9.04333 12.1565 9.00389ZM11.8434 14.9955L9.00419 12.1562C9.04363 12.8964 9.35543 13.5959 9.87958 14.1201C10.4037 14.6442 11.1032 14.956 11.8434 14.9955Z" fill="black"/><path d="M12 16.5C11.3077 16.5 10.6248 16.3403 10.0043 16.0333C9.38376 15.7264 8.84246 15.2803 8.42248 14.73C8.00249 14.1797 7.71514 13.5398 7.58278 12.8603C7.45043 12.1808 7.47664 11.4799 7.65937 10.8122L4.42172 7.57406C3.09938 8.78531 1.83937 10.3659 0.75 12C1.98844 14.0625 3.6825 16.1831 5.44687 17.3991C7.47094 18.7931 9.67172 19.5 11.9887 19.5C13.255 19.5008 14.5117 19.282 15.7031 18.8531L13.1902 16.3406C12.8024 16.4468 12.4021 16.5004 12 16.5ZM12 7.5C12.6923 7.49997 13.3752 7.65966 13.9957 7.96665C14.6162 8.27365 15.1575 8.71966 15.5775 9.27C15.9975 9.82034 16.2849 10.4602 16.4172 11.1397C16.5496 11.8192 16.5234 12.5201 16.3406 13.1878L19.6528 16.5C21.0192 15.2695 22.2811 13.6144 23.25 12C22.0134 9.96422 20.3016 7.84875 18.5072 6.61781C16.4578 5.2125 14.2645 4.5 11.9887 4.5C10.7365 4.50178 9.49467 4.72867 8.32266 5.16984L10.8122 7.65937C11.1992 7.55342 11.5987 7.49982 12 7.5Z" fill="black"/></svg>'; // Or your SVG for "hide"
            } else {
                pass.type = "password";
                toggleBtn.innerHTML = '<svg class="eye" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.1429C10.932 17.1429 9.88795 16.8412 8.99992 16.2761C8.1119 15.711 7.41976 14.9078 7.01105 13.9681C6.60234 13.0284 6.4954 11.9943 6.70376 10.9967C6.91212 9.99906 7.42642 9.08269 8.18162 8.36345C8.93683 7.64421 9.89901 7.1544 10.9465 6.95596C11.994 6.75752 13.0798 6.85937 14.0665 7.24862C15.0532 7.63787 15.8966 8.29704 16.4899 9.14278C17.0833 9.98852 17.4 10.9828 17.4 12C17.4 13.364 16.8311 14.6721 15.8184 15.6365C14.8057 16.601 13.4322 17.1429 12 17.1429ZM12 4C3.6 4 0 12 0 12C0 12 3.6 20 12 20C20.4 20 24 12 24 12C24 12 20.4 4 12 4Z" fill="black"/><path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" fill="black"/></svg>'; // Or your SVG for "show"
            }
        });

        save_btn.addEventListener("click", function() {
            
            const team1Name = document.querySelectorAll(".team-name")[0].value;
            const team2Name = document.querySelectorAll(".team-name")[1].value;
            const team1Logo = document.getElementById("team1LogoInput").files[0];
            const team2Logo = document.getElementById("team2LogoInput").files[0];
            const matchVenue = document.querySelectorAll(".form-input")[0].value;
            const matchCity = document.querySelectorAll(".form-input")[1].value;
            const matchDate = document.querySelectorAll(".form-input")[2].value;
            const matchTime = document.querySelectorAll(".form-input")[3].value;
            const matchPass = document.querySelector(".pass").value;

            const formdata = new FormData();
            formdata.append("match_id", matchID);
            formdata.append("team1Name", team1Name);
            formdata.append("team2Name", team2Name);
            formdata.append("team1Logo", team1Logo);
            formdata.append("team2Logo", team2Logo);
            formdata.append("matchVenue", matchVenue);
            formdata.append("matchCity", matchCity);
            formdata.append("matchDate", matchDate);
            formdata.append("matchTime", matchTime);
            formdata.append("matchPass", matchPass);
            formdata.append('Umpires[]', Umpires);
            formdata.append('Scorers[]', Scorers);
            formdata.append('Commentators[]', Commentators);

            // for (let [key, value] of formdata.entries()) {
            //     console.log(`${key}:`, value);
            // }

             // First schedule the match via fetch()
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

                    // Send mails via sendBeacon (fire-and-forget)
                    Scorers.forEach((scorer) => {
                        let scorerData = {
                            for_value: 'Scorer',
                            game: data.game,
                            venue: matchCity,
                            time: matchTime,
                            password: matchPass,
                            date: matchDate,
                            recipient_email: scorer
                        };
                        const scorerBlob = new Blob([JSON.stringify(scorerData)], { type: 'application/json' });
                        navigator.sendBeacon('../mail.php', scorerBlob);
                    });

                    Umpires.forEach((umpire) => {
                        let umpireData = {
                            for_value: 'Umpire',
                            game: data.game,
                            venue: matchCity,
                            time: matchTime,
                            date: matchDate,
                            recipient_email: umpire
                        };
                        const umpireBlob = new Blob([JSON.stringify(umpireData)], { type: 'application/json' });
                        navigator.sendBeacon('../mail.php', umpireBlob);
                    });

                    const totalBeacons = Scorers.length + Umpires.length;

                        if (totalBeacons > 0) {
                            console.log("Waiting 500ms before reload");
                            setTimeout(() => {
                                console.log("Reloading now...");
                                window.location.reload();
                            }, 500);
                        } else {
                            console.log("Reloading immediately...");
                            window.location.reload();
                        }

                }
            })
            .catch(error => console.log(error));
        });

    </script>
</body>
</html>