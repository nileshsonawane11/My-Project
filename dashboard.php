<?php
    session_start();
    if(!isset($_SESSION['user'])){
        header('location: ./front-page.php');
        exit();
    }

    
    include 'config.php';
    $sql = "SELECT * FROM `users` WHERE user_id = '$_SESSION[user]'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $_SESSION['name'] = $row['fname'].' '.$row['lname'];
            $_SESSION['role'] = $row['role'];
        }
    }

    function updateMatchStatus($conn){
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i');

        // Set Live
        mysqli_query($conn, "UPDATE matches 
            SET status = 'Live'
            WHERE match_date = '$currentDate'
            AND start_time <= '$currentTime'
            AND status = 'Upcoming'");

    }

    // Call this function whenever a match-related page loads
    updateMatchStatus($conn);


    $username = $_SESSION['name'];
    $update = $_GET['update'];
    $sport = $_GET['sport'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title><?php echo $username;?>'s Dashboard</title>
<style>
    * {
        margin: 0px;
        padding: 0px;
        user-select: none;
        scrollbar-width: none;
        box-sizing: border-box;
    }
    
    body {
        background-color: #f8f8f8;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    :root {
        --primary-light: #FAC01F;
        --primary-dark: #F83900;
        --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
        --card-bg: #ffffff;
        --text-dark: #333333;
        --text-light: #666666;
        --border-color: #e0e0e0;
    }
    
    .nav-bar {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        position: fixed;
        top: 0;
        width: 100%;
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 999;
        height: 61px;
    }
    
    .items {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
        position: relative;
    }
    
    .nav-content {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        width: 100%;
        padding-left: 15px;
        padding-right: 15px;
    }
    
    #loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(255, 255, 255, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    .logo-img {
        overflow: hidden;
        object-fit: contain;
        height: 35px;
    }
    
    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(250, 192, 31, 0.2);
        border-top: 4px solid var(--primary-light);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    .sidebar {
        position: fixed;
        right: 0;
        top: 0;
        width: 300px;
        height: 100%;
        background: #ffffff;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        scroll-behavior: smooth;
        overflow-y: scroll;
        scrollbar-width: none;
        transform: translateX(100%);
        transition: all 0.3s ease;
        z-index: 99999;
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    #close-sidebar {
        text-align: right;
        font-size: 32px;
        font-weight: 300;
        margin: 15px 25px;
        cursor: pointer;
        color: var(--text-light);
    }
    
    .menu-list {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 85%;
        justify-content: space-between;
        padding: 0 20px;
    }
    
    .menu-part {
        width: 100%;
    }
    
    .menu-items {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        font-size: 18px;
        gap: 15px;
        margin: 15px 0;
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .menu-items:hover {
        background: rgba(250, 192, 31, 0.1);
    }
    
    .menu-items p {
        text-align: left;
        font-size: 16px;
        color: var(--text-dark);
    }
    
    .menu-items .username {
        text-align: right;
        font-size: 14px;
        color: var(--text-light);
    }
    
    .menu-items .user-photo {
        height: 50px;
        width: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .menu-items div {
        max-width: fit-content;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        gap: 15px;
        cursor: pointer;
    }
    
    .danger {
        color: #e74c3c;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    #opacity-container {
        position: absolute;
        top: 0;
        left: 0;
        display: none;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        transition: all 0.3s ease-in-out;
    }
    
    .form-control {
        height: 38px;
        padding: 8px 15px;
        width: 100%;
        border: 1px solid var(--border-color);
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-dark);
        background-color: #fff;
        outline: none;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(250, 192, 31, 0.2);
    }
    
    .d-flex {
        position: relative;
        display: flex;
    }
    
    .btn-outline-success:focus {
        background: #f8f8f8;
    }
    
    .btn-outline-success:hover {
        background: #f8f8f8;
    }
    
    .plus-icon {
        display: flex;
        font-size: 32px;
        height: 100%;
        width: 100%;
        justify-content: center;
        align-items: center;
        color: white;
    }
    
    .user-name {
        text-wrap: auto;
        font-size: 13px;
        text-align: left;
        margin: auto;
    }
    
    .game-list {
        height: 100%;
        width: 100%;
    }
    
    .game.selected {
        background: rgba(248, 57, 0, 0.1);
        border-radius: 10px;
    }
    .game-container {
        margin-top: 60px;
    }
    .updates {
        height: 70px;
        background: white;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        position: relative;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        z-index: 1;
    }
    
    .updates .update-container {
        height: 40px;
        width: 100px;
        display: flex;
        border: 1px solid var(--border-color);
        border-radius: 25px;
        margin: 0 10px;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-light);
        transition: all 0.2s ease;
    }
    
    .updates .update-container:hover {
        border-color: var(--primary-light);
    }
    
    .updates .update-container.active {
        background: var(--background);
        color: white;
        border: none;
        box-shadow: 0 4px 10px rgba(248, 57, 0, 0.2);
    }
    
    .game-list {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        height: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
        text-align: center;
        padding: 7px 5px;
    }
    
    .game-info {
        height: 150px;
        padding: 25px;
        display: flex;
        font-size: 14px;
        gap: 15px;
        width: 100%;
        max-width: 450px;
        background: var(--card-bg);
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 10px 0;
    }
    
    .game-info:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .start-btn {
        width: 68px;
        height: 38px;
        border-radius: 18px;
        border: none;
        color: white;
        font-weight: 600;
        background: var(--background);
        box-shadow: 0 4px 10px rgba(248, 57, 0, 0.2);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .start-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(248, 57, 0, 0.3);
    }
    
    .start-btn:active {
        transform: translateY(0);
    }
    
    .match-data {
        display: flex;
        gap: 5px;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    
    .info-container {
        max-height: max-content;
        min-height: 100px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
        margin: 60px 30px;
        align-content: center;
        justify-content: center;
        align-items: center;
        gap: 30px;
        justify-items: center;
    }
    
    .info-container .game-name {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
        width: 100%;
        color: var(--text-dark);
    }
    
    .info {
        display: flex;
        justify-content: space-between;
        flex-direction: row;
        align-items: center;
        width: 100%;
    }
    
    .team-score {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        font-size: clamp(8px, 15px, 18px);
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    
    .team {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: row;
        gap: 10px;
    }
    
    .team img {
        height: 40px;
        width: 40px;
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        overflow: hidden;
        object-fit: cover;
        align-items: center;
        justify-content: center;
    }
    
    .container-list {
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 30px;
        padding: 20px;
    }
    
    .icon {
        margin: 20px;
    }
    
    .txt-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        text-align: left;
        padding-right: 20px;
    }
    
    .txt-container h3 {
        font-size: 20px;
        color: var(--text-dark);
        font-weight: 600;
    }
    
    .txt-container p {
        font-size: 14px;
        color: var(--text-light);
        line-height: 1.4;
    }
    
    .trade-mark {
        position: absolute;
        top: 7px;
        font-size: 7px;
        right: -8px;
    }
    
    #startMatchDialog {
        z-index: 9999;
        position: fixed;
        transform: translateX(-50%) translateY(-50%);
        top: 50%;
        left: 50%;
        width: 90%;
        max-width: 500px;
        border: none;
        height: max-content;
        background: white;
        flex-direction: column;
        transition: all 0.3s ease;
        justify-content: center;
        align-items: flex-start;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    #startMatchDialog::backdrop {
        position: fixed;
        inset: 0px;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(3px);
    }
    
    #content-wrapper {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 25px
    }
    
    #matchPasswordForm {
        display: flex;
        justify-content: center;
        flex-direction: column;
        gap: 25px;
    }
    
    .form-data {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
        width: 100%;
    }
    
    .form-data label {
        font-size: 15px;
        line-height: 35px;
        font-weight: 500;
        color: var(--text-dark);
    }
    
    .btns {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        width: 100%;
    }
    
    #matchPassword {
        padding: 12px 15px;
        height: 45px;
        outline: none;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: #f8f8f8;
        color: var(--text-dark);
        font-size: 15px;
    }
    
    #matchPassword:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(250, 192, 31, 0.2);
    }

    #title{
        font-size: 18px;
        font-weight: 500;
    }
    
    .btns>* {
        width: 110px;
        height: 40px;
        border-radius: 25px;
        border: solid 1px var(--primary-light);
        color: var(--primary-light);
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 600;
    }
    
    .btns>*:last-child {
        background: var(--background);
        color: white;
        border: none;
    }
    
    .btns>*:last-child:hover {
        background: linear-gradient(135deg, #fac01fdb, #f83900cc);
    }
    
    .btns>*:first-child:hover {
        background: rgba(250, 192, 31, 0.1);
    }
    
    .error {
        display: none;
        color: #e74c3c;
        width: 100%;
        font-size: 13px;
        margin: 5px 0;
    }
    
    .update {
        color: #e74c3c;
        width: 100%;
        word-spacing: 2px;
        letter-spacing: 1px;
    }
    
    .logo-name {
        font-size: 24px;
        font-weight: 600;
        color: var(--text-dark);
        position: relative;
    }
    
    .txt-strike {
        font-weight: 600;
        background: var(--background);
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .txt-live {
        color: #000000;
        font-size: 25px;
        white-space: nowrap;
        text-align: left;
        font-weight: 0;
        font-weight: 400;
    }
    
    .game {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        padding: 10px;
        gap: 8px;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .game:hover {
        background: rgba(250, 192, 31, 0.1);
    }
    
    .game p {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-dark);
    }
    
    .game svg {
        height: 24px;
        width: 24px;
    }
    
    .game.selected p {
        font-weight: 600;
        color: var(--primary-dark);
    }
    
    .game.selected svg path {
        fill: var(--primary-dark);
    }
    
    .blocks {
        width: 100%;
        max-width: 400px;
        height: 120px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 15px;
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
        padding: 0 20px;
    }
    
    .blocks:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .plus {
        height: 70px;
        cursor: pointer;
        width: 70px;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(248, 57, 0, 0.3);
        background: var(--background);
        transition: all 0.2s ease;
        z-index: 100;
        border-radius: 50%;
    }
    
    .plus:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 20px rgba(248, 57, 0, 0.4);
    }
    
    .plus:active {
        transform: translateY(0);
    }
    
    .close-icon {
        cursor: pointer;
        color: var(--text-light);
        transition: all 0.2s ease;
    }
    
    .close-icon:hover {
        color: var(--primary-dark);
    }
    
    .add-container {
        z-index: 999999;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        transform: translateY(100%);
        background: white;
        flex-direction: column;
        transition: all 0.3s ease;
        display: flex;
    }
    
    .add-container.active {
        transform: translateY(0);
    }
    
    .close-container {
        width: 100%;
        height: 70px;
        display: flex;
        padding: 20px;
        align-items: center;
        justify-content: flex-end;
    }

    .logo-img {
        height: 35px;
    }

    .logo-img img {
        height: 100%;
    }

    
    .pls {
        position: fixed;
        bottom: 40px;
        border: none;
        border-radius: 50%;
        display: flex;
        font-size: 30px;
        height: max-content;
        width: 100%;
        justify-content: center;
    }
    a{
        text-decoration: none;
        color : black;
    }
    .ad2{
            height: 80px;
            width: 100%;
            background: rgb(20 20 20 / 12%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 14px;
            border-bottom: 1px solid rgba(248, 57, 0, 0.1);
            margin-bottom: 20px;
        }
    @media(max-width: 1000px) {
        .game-list {
            justify-content: flex-start;
            height: 100%;
        }
        
        .info-container {
            grid-template-columns: 1fr;
            margin: 0px 15px;
        }
    }
    
    @media(min-width: 601px) {
        .logo-img img {
            height: 100%;
        }
        .pls {
            justify-content: flex-end;
            right: 40px;
        }
        .btn-outline-success {
            position: absolute;
            border-radius: 0 25px 25px 0;
            border: solid 1px var(--border-color);
            font-size: 20px;
            width: 44px;
            display: flex;
            top: 0;
            right: 0;
            height: 38px;
            align-items: center;
            justify-content: center;
            background: #f8f8f8;
            cursor: pointer;
            color: var(--text-light);
        }
        
        .form-control {
            height: 38px;
            padding: 8px 52px 8px 15px;
            width: 300px;
        }
        
        .add-container {
            width: 70%;
            height: 70%;
            max-width: 600px;
            max-height: 600px;
            border-radius: 20px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, 150%);
        }
        
        .add-container.active {
            transform: translate(-50%, -50%);
        }
    }
    
    @media(max-width: 600px) {
        .nav-content {
            padding-right: 0;
            padding-left: 0;
        }

        .btn-outline-success {
            position: absolute;
            height: 100%;
            width: 44px;
            right: 0;
            border-radius: 0px 25px 25px 0px;
            border: solid 1px #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            background: #f8f8f8;
        }
         .form-control{
                height: 30px;
                padding: 5px;
                width: 100%;
                border: 1px solid #999999;
                border-radius: 25px;
                font-size: 12px;
                font-weight: 500;
                padding-right: 52px;
                color: #333;
                background-color: #fff;
                outline: none;
                transition: all 0.3s ease-in-out;
            }
        
        .updates .update-container {
            margin: 0 5px;
            width: 80px;
            font-size: 13px;
        }
        
        .game-info {
            width: 95%;
            padding: 20px;
        }
        
        .info-container {
            margin-bottom: 100px;
        }
        
        .plus {
            bottom: 30px;
            right: 30px;
        }
    }
</style>
</head>
<body>
    <div class="body">

        <div id="loader">
            <div class="spinner"></div>
        </div>

        <dialog id="startMatchDialog">
            <div id="content-wrapper">
                <div class="top-container">
                    <p id="title">Enter Password to Start Match</p>
                </div>

                <div class="body-container">
                <form id="matchPasswordForm">
                    <div class="form-data">
                        <label for="matchPassword">Password:</label>
                        <input type="text" name="" id="match_id" hidden>
                        <input type="text" id="matchPassword" name="matchPassword" required>
                        <div id="error-matchpassword" class="error"></div>
                    </div>

                    <div class='btns'>
                        <button id='submit-btn' type="button" onclick="closeDialog()">Cancel</button>
                        <button id='cancel-btn' type="submit">Start Match</button>
                    </div>
                </form>
                </div>
            </div>
        </dialog>


        <div class="sidebar">
            <div id="close-sidebar">&times;</div>
            <div class="menu-list">
                <div class="menu-part">
                    <div class="menu-items"><div onclick="window.location.href=`./profile.php`"><p class="username"><?php echo $_SESSION['name']." (".$_SESSION['role'].") "; ?></p>
                        <?php
                            $query = mysqli_query($conn,"SELECT * FROM `users` WHERE user_id = '$_SESSION[user]'");
                            $row = mysqli_fetch_assoc($query);
                            $user_img = $row['user_photo'];

                            if (!empty($user_img)) {
                                echo '<img class="user-photo" src="./assets/images/users/' . $user_img . '" alt="">';
                            } else {
                                echo '<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13 0.5C14.6415 0.499992 16.267 0.823308 17.7835 1.45149C19.3001 2.07967 20.6781 3.00041 21.8388 4.16114C22.9996 5.32187 23.9203 6.69986 24.5485 8.21643C25.1767 9.733 25.5 11.3585 25.5 13C25.5 19.9036 19.9036 25.5 13 25.5C6.09647 25.5 0.5 19.9036 0.5 13C0.5 6.09647 6.09647 0.5 13 0.5ZM14.25 14.25H11.75C8.65537 14.25 5.99855 16.1243 4.85264 18.7997C6.66576 21.3421 9.63928 23 13 23C16.3607 23 19.3342 21.3421 21.1474 18.7995C20.0015 16.1243 17.3447 14.25 14.25 14.25ZM13 4.25C10.9289 4.25 9.24998 5.92895 9.24998 8C9.24998 10.0711 10.9289 11.75 13 11.75C15.071 11.75 16.75 10.0711 16.75 8C16.75 5.92895 15.0711 4.25 13 4.25Z" fill="black"></path>
                                    </svg>';
                            }
                        ?></div>
                    </div><hr>

                    <!-- <div class="menu-items"><div><p>Setting</p><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.76 4.0375C16.3267 4.2125 16.8683 4.4375 17.385 4.7125L19.6763 3.3375C19.9152 3.1942 20.1951 3.13482 20.4716 3.16879C20.7481 3.20276 21.0054 3.32814 21.2025 3.525L22.475 4.7975C22.6719 4.99463 22.7972 5.25187 22.8312 5.52838C22.8652 5.8049 22.8058 6.08484 22.6625 6.32375L21.2875 8.615C21.5625 9.13167 21.7875 9.67333 21.9625 10.24L24.5537 10.8888C24.8241 10.9565 25.064 11.1126 25.2354 11.3322C25.4069 11.5519 25.5 11.8226 25.5 12.1012V13.8988C25.5 14.1774 25.4069 14.4481 25.2354 14.6678C25.064 14.8874 24.8241 15.0435 24.5537 15.1112L21.9625 15.76C21.7875 16.3267 21.5625 16.8683 21.2875 17.385L22.6625 19.6763C22.8058 19.9152 22.8652 20.1951 22.8312 20.4716C22.7972 20.7481 22.6719 21.0054 22.475 21.2025L21.2025 22.475C21.0054 22.6719 20.7481 22.7972 20.4716 22.8312C20.1951 22.8652 19.9152 22.8058 19.6763 22.6625L17.385 21.2875C16.8683 21.5625 16.3267 21.7875 15.76 21.9625L15.1112 24.5537C15.0435 24.8241 14.8874 25.064 14.6678 25.2354C14.4481 25.4069 14.1774 25.5 13.8988 25.5H12.1012C11.8226 25.5 11.5519 25.4069 11.3322 25.2354C11.1126 25.064 10.9565 24.8241 10.8888 24.5537L10.24 21.9625C9.67837 21.7889 9.13431 21.5629 8.615 21.2875L6.32375 22.6625C6.08484 22.8058 5.8049 22.8652 5.52838 22.8312C5.25187 22.7972 4.99463 22.6719 4.7975 22.475L3.525 21.2025C3.32814 21.0054 3.20276 20.7481 3.16879 20.4716C3.13482 20.1951 3.1942 19.9152 3.3375 19.6763L4.7125 17.385C4.43705 16.8657 4.21106 16.3216 4.0375 15.76L1.44625 15.1112C1.17615 15.0436 0.936373 14.8877 0.764953 14.6683C0.593534 14.4488 0.500286 14.1784 0.5 13.9V12.1025C0.500007 11.8238 0.593128 11.5532 0.764569 11.3335C0.936011 11.1138 1.17594 10.9577 1.44625 10.89L4.0375 10.2413C4.2125 9.67458 4.4375 9.13292 4.7125 8.61625L3.3375 6.325C3.1942 6.08609 3.13482 5.80615 3.16879 5.52963C3.20276 5.25312 3.32814 4.99588 3.525 4.79875L4.7975 3.525C4.99463 3.32814 5.25187 3.20276 5.52838 3.16879C5.8049 3.13482 6.08484 3.1942 6.32375 3.3375L8.615 4.7125C9.13167 4.4375 9.67333 4.2125 10.24 4.0375L10.8888 1.44625C10.9564 1.17615 11.1123 0.936373 11.3317 0.764953C11.5512 0.593534 11.8216 0.500286 12.1 0.5H13.8975C14.1762 0.500007 14.4468 0.593128 14.6665 0.764569C14.8862 0.936011 15.0423 1.17594 15.11 1.44625L15.76 4.0375ZM13 18C14.3261 18 15.5979 17.4732 16.5355 16.5355C17.4732 15.5979 18 14.3261 18 13C18 11.6739 17.4732 10.4021 16.5355 9.46447C15.5979 8.52678 14.3261 8 13 8C11.6739 8 10.4021 8.52678 9.46447 9.46447C8.52678 10.4021 8 11.6739 8 13C8 14.3261 8.52678 15.5979 9.46447 16.5355C10.4021 17.4732 11.6739 18 13 18Z" fill="black"/>
                        </svg></div>
                    </div> -->

                    <?php
                    if($_SESSION['role'] == 'Admin'){

                    echo '<div class="menu-items"><div onclick="window.location.href=`./Frontend/manage-matches.php`"><p>My Matches</p><img src="./assets/images/logo.png" alt="" style="width:26px; filter: grayscale(1) invert(1) brightness(0.0) contrast(29);"></div></div>';

                    echo '<div class="menu-items"><div onclick="window.location.href=`./Frontend/manage-tournaments.php`"><p>My Tournaments</p><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">';
                      echo  '<path d="M20.5 0.5V4.25H25.5C25.4658 7.26719 24.9918 9.39498 24.0779 10.6334L23.9375 10.8125C23.2027 11.6942 21.9485 12.3209 20.1749 12.6927C19.3552 15.3769 17.0705 17.4221 14.2527 17.8958C14.2506 17.9305 14.2497 17.9652 14.25 18C14.25 20.7614 16.4885 23 19.25 23V25.5H6.74996V23C9.51143 23 11.75 20.7614 11.75 18L11.7473 17.8958C8.92947 17.4221 6.64484 15.3769 5.82553 12.6924C4.05143 12.3209 2.79723 11.6942 2.06246 10.8125C1.05668 9.60551 0.535859 7.41801 0.5 4.25H5.49998V0.5H20.5ZM18 2.99996H8V10.5C8 13.1778 10.1049 15.3638 12.7504 15.4938L13 15.5C15.7614 15.5 18 13.2614 18 10.5V2.99996ZM13 4.25L14.2644 7.25996L17.375 7.59688L15.0431 9.78436L15.7037 13L13 11.3462L10.2962 13L10.9569 9.77996L8.62496 7.59248L11.7356 7.25562L13 4.25ZM5.49998 6.74996H3.17246L3.19965 6.92996C3.3599 7.95605 3.60588 8.68408 3.90131 9.1049L3.98305 9.21201C4.21367 9.48881 4.64721 9.74949 5.28951 9.96846L5.49998 10.035V6.74996ZM22.8275 6.74996H20.5V10.035L20.7105 9.96846C21.3528 9.74949 21.7863 9.48881 22.0169 9.21201L22.0987 9.1049C22.3941 8.68408 22.6401 7.95605 22.8004 6.92996L22.8275 6.74996Z" fill="black"/>';
                       echo '</svg></div>';
                    echo '</div>';

                    echo '<div class="menu-items"><div onclick="window.location.href=`./Frontend/manage-teams.php`"><p>My Teams</p><svg width="30" height="22" viewBox="0 0 30 22" fill="none" xmlns="http://www.w3.org/2000/svg">';
                     echo   '<path d="M15 12.5C16.7262 12.5 18.2887 11.8 19.42 10.6687C20.55 9.53875 21.25 7.97625 21.25 6.25C21.25 4.52375 20.55 2.96125 19.42 1.83125C18.2887 0.7 16.7262 0 15 0C13.2738 0 11.7112 0.7 10.58 1.83125C9.45 2.96125 8.75 4.52375 8.75 6.25C8.75 7.97625 9.45 9.53875 10.58 10.6687C11.1604 11.2493 11.8494 11.7098 12.6078 12.024C13.3662 12.3383 14.1791 12.5 15 12.5ZM25 13.75C25.4106 13.7508 25.8173 13.6706 26.1969 13.5138C26.5764 13.357 26.9212 13.1269 27.2115 12.8365C27.5019 12.5462 27.732 12.2014 27.8888 11.8219C28.0456 11.4423 28.1258 11.0356 28.125 10.625C28.125 9.7625 27.7762 8.98125 27.21 8.415C26.9198 8.12484 26.5752 7.89468 26.196 7.73769C25.8168 7.58069 25.4104 7.49992 25 7.5C24.5894 7.49918 24.1827 7.57944 23.8031 7.7362C23.4236 7.89295 23.0788 8.12311 22.7885 8.41345C22.4981 8.7038 22.268 9.04863 22.1112 9.42814C21.9544 9.80765 21.8742 10.2144 21.875 10.625C21.8743 11.0356 21.9547 11.4422 22.1115 11.8217C22.2683 12.2011 22.4985 12.5459 22.7888 12.8362C23.0791 13.1265 23.4239 13.3567 23.8033 13.5135C24.1828 13.6703 24.5894 13.7507 25 13.75ZM25 14.4875C23.3363 14.4875 22.085 14.995 21.3538 15.6975C19.96 14.5513 17.7563 13.75 15 13.75C12.1675 13.75 10.0063 14.56 8.635 15.705C7.89 14.9987 6.625 14.4875 5 14.4875C2.265 14.4875 0.625 15.85 0.625 17.215C0.625 17.8962 2.265 18.58 5 18.58C5.755 18.58 6.4325 18.5163 7.02875 18.4138L6.97875 18.7512C6.97875 20.0012 9.98625 21.2512 15 21.2512C19.7025 21.2512 23.0212 20.0012 23.0212 18.7512L22.9963 18.4325C23.575 18.5238 24.24 18.58 25 18.58C27.5637 18.58 29.375 17.8962 29.375 17.215C29.375 15.85 27.6588 14.4875 25 14.4875ZM5 13.75C5.8625 13.75 6.64375 13.4012 7.21 12.835C7.50017 12.5448 7.73032 12.2002 7.88731 11.821C8.04431 11.4418 8.12508 11.0354 8.125 10.625C8.12582 10.2144 8.04556 9.80765 7.8888 9.42814C7.73205 9.04863 7.50189 8.7038 7.21155 8.41345C6.9212 8.12311 6.57637 7.89295 6.19686 7.7362C5.81735 7.57944 5.41061 7.49918 5 7.5C4.58943 7.49934 4.18278 7.57972 3.80334 7.73654C3.4239 7.89335 3.07914 8.12351 2.78882 8.41382C2.49851 8.70414 2.26835 9.0489 2.11154 9.42834C1.95472 9.80778 1.87434 10.2144 1.875 10.625C1.87418 11.0356 1.95444 11.4423 2.1112 11.8219C2.26795 12.2014 2.49811 12.5462 2.78845 12.8365C3.0788 13.1269 3.42363 13.357 3.80314 13.5138C4.18265 13.6706 4.58939 13.7508 5 13.75Z" fill="black"/>';
                    echo    '</svg></div>';
                    echo '</div>';
                    }
                    ?>

                    <div class="menu-items"><div onclick="shareContent()"><p>Share</p><svg width="24" height="19" viewBox="0 0 24 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23.25 9L14.5 0.25V5.25C5.75 6.5 2 12.75 0.75 19C3.875 14.625 8.25 12.625 14.5 12.625V17.75L23.25 9Z" fill="black"/>
                        </svg></div>
                    </div>

                    <div class="menu-items"><div onclick="window.location.href=`./privacy-policy.php`"><p>Privacy Policy</p><svg width="22" height="28" viewBox="0 0 22 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 14V6.18625C21.0001 6.08758 20.9807 5.98987 20.9429 5.8987C20.9052 5.80754 20.8498 5.72473 20.78 5.655L16.845 1.72C16.7045 1.57931 16.5138 1.50018 16.315 1.5H1.75C1.55109 1.5 1.36032 1.57902 1.21967 1.71967C1.07902 1.86032 1 2.05109 1 2.25V25.75C1 25.9489 1.07902 26.1397 1.21967 26.2803C1.36032 26.421 1.55109 26.5 1.75 26.5H12.25M6 11.5H16M6 6.5H11M6 16.5H9.75" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg></div>
                    </div>

                    <div class="menu-items"><div><p>Follow Us</p><svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.375 1.75C20.5462 1.75 19.7513 2.07924 19.1653 2.66529C18.5792 3.25134 18.25 4.0462 18.25 4.875C18.25 5.32125 18.3475 5.745 18.5175 6.13125L16.0738 8.88C15.2428 8.30862 14.2585 8.00186 13.25 8C12.325 8 11.47 8.27 10.7263 8.7075L7.88375 5.86625L7.855 5.895C8.1 5.3975 8.25 4.84375 8.25 4.25C8.25 3.50832 8.03007 2.7833 7.61801 2.16661C7.20596 1.54993 6.62029 1.06928 5.93506 0.785453C5.24984 0.501625 4.49584 0.427362 3.76841 0.572057C3.04098 0.716751 2.3728 1.0739 1.84835 1.59835C1.3239 2.1228 0.966751 2.79098 0.822057 3.51841C0.677362 4.24584 0.751625 4.99984 1.03545 5.68506C1.31928 6.37029 1.79993 6.95596 2.41661 7.36801C3.0333 7.78007 3.75832 8 4.5 8C5.09375 8 5.64625 7.85 6.145 7.605L6.11625 7.63375L8.95875 10.475C8.49978 11.2376 8.25494 12.1099 8.25 13C8.25 14.2463 8.725 15.3738 9.48125 16.2513L6.26 19.4713C5.89873 19.3275 5.5138 19.2525 5.125 19.25C3.4025 19.25 2 20.6513 2 22.375C2 24.0988 3.4025 25.5 5.125 25.5C6.8475 25.5 8.25 24.0988 8.25 22.375C8.25 21.9738 8.1675 21.5925 8.02875 21.2388L11.5763 17.6913C12.1025 17.8788 12.66 18 13.25 18C16.0075 18 18.25 15.7575 18.25 13C18.25 12.205 18.0463 11.4638 17.715 10.795L20.3612 7.81625C20.6812 7.92625 21.0188 8 21.375 8C23.0988 8 24.5 6.59875 24.5 4.875C24.5 3.15125 23.0988 1.75 21.375 1.75ZM13.25 15.5C11.8713 15.5 10.75 14.3788 10.75 13C10.75 11.6213 11.8713 10.5 13.25 10.5C14.6288 10.5 15.75 11.6213 15.75 13C15.75 14.3788 14.6288 15.5 13.25 15.5Z" fill="black"/>
                        </svg></div>
                    </div>
                </div>
                <div class="menu-part">
                    <div class="menu-items"><div onclick="window.location.href=`./feedback.php`"><p>Feedback</p><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 16.75C13.3542 16.75 13.6513 16.63 13.8913 16.39C14.1313 16.15 14.2508 15.8533 14.25 15.5C14.2492 15.1467 14.1292 14.85 13.89 14.61C13.6508 14.37 13.3542 14.25 13 14.25C12.6458 14.25 12.3492 14.37 12.11 14.61C11.8708 14.85 11.7508 15.1467 11.75 15.5C11.7492 15.8533 11.8692 16.1504 12.11 16.3913C12.3508 16.6321 12.6475 16.7517 13 16.75ZM11.75 11.75H14.25V4.25H11.75V11.75ZM0.5 25.5V3C0.5 2.3125 0.745 1.72417 1.235 1.235C1.725 0.745833 2.31333 0.500833 3 0.5H23C23.6875 0.5 24.2763 0.745 24.7663 1.235C25.2563 1.725 25.5008 2.31333 25.5 3V18C25.5 18.6875 25.2554 19.2763 24.7663 19.7663C24.2771 20.2563 23.6883 20.5008 23 20.5H5.5L0.5 25.5ZM4.4375 18H23V3H3V19.4062L4.4375 18Z" fill="black"/>
                        </svg></div>
                    </div>

                    <div class="menu-items"><div onclick="window.location.href=`./about-us.php`"><p>About Us</p><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13 0.5C6.09647 0.5 0.5 6.09641 0.5 13C0.5 19.9035 6.09647 25.5 13 25.5C19.9036 25.5 25.5 19.9036 25.5 13C25.5 6.09641 19.9036 0.5 13 0.5ZM13 23C7.48602 23 3.00002 18.514 3.00002 13C3.00002 7.48596 7.48596 3.00002 13 3.00002C18.5141 3.00002 23 7.48596 23 13C23 18.514 18.5141 23 13 23ZM14.5653 8C14.5653 8.90633 13.9054 9.56252 13.0127 9.56252C12.0839 9.56252 11.4402 8.90627 11.4402 7.98266C11.4402 7.09496 12.1013 6.43754 13.0127 6.43754C13.9054 6.43754 14.5653 7.09496 14.5653 8ZM11.7528 11.75H14.2527V19.25H11.7528V11.75Z" fill="black"/>
                        </svg></div>
                    </div>

                    <div class="menu-items"><div onclick="window.location.href=`./support.php`"><p>Support</p><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.375C20.4375 16.375 18.9375 16.125 17.5375 15.6625C17.4125 15.625 17.275 15.6 17.15 15.6C16.825 15.6 16.5125 15.725 16.2625 15.9625L13.5125 18.7125C9.96717 16.9093 7.08361 14.0301 5.275 10.4875L8.025 7.725C8.375 7.3875 8.475 6.9 8.3375 6.4625C7.86415 5.02237 7.62363 3.51593 7.625 2C7.625 1.3125 7.0625 0.75 6.375 0.75H2C1.3125 0.75 0.75 1.3125 0.75 2C0.75 13.7375 10.2625 23.25 22 23.25C22.6875 23.25 23.25 22.6875 23.25 22V17.625C23.25 16.9375 22.6875 16.375 22 16.375ZM3.2875 3.25H5.1625C5.25 4.35 5.4375 5.4375 5.7375 6.4875L4.2375 8C3.725 6.4875 3.4 4.9 3.2875 3.25ZM20.75 20.7125C19.1 20.6 17.5 20.275 16 19.7625L17.5 18.2625C18.5625 18.5625 19.65 18.75 20.75 18.825V20.7125ZM12 0.75V13.25L15.75 9.5H23.25V0.75H12ZM20.75 7H14.5V3.25H20.75V7Z" fill="black"/>
                        </svg></div>
                    </div>

                    <div class="menu-items danger"><div onclick='deleteAccount()';><p>Delete Account</p><svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.375 21.625V20.5C1.375 18.0137 3.38875 16 5.875 16H10.375C12.8612 16 14.875 18.0137 14.875 20.5V21.625" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.125 12.625C6.2575 12.625 4.75 11.1175 4.75 9.25C4.75 7.3825 6.2575 5.875 8.125 5.875C9.9925 5.875 11.5 7.3825 11.5 9.25C11.5 11.1175 9.9925 12.625 8.125 12.625Z" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14.875 1.375L21.625 8.125" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21.625 1.375L14.875 8.125" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg></div>
                    </div>

                    <div class="menu-items danger"><div onclick='logout()';><p>Log Out</p><svg width="21" height="19" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.7085 4.29167L14.2397 5.76042L16.9272 8.45833H6.3335V10.5417H16.9272L14.2397 13.2292L15.7085 14.7083L20.9168 9.5M2.16683 2.20833H10.5002V0.125H2.16683C1.021 0.125 0.0834961 1.0625 0.0834961 2.20833V16.7917C0.0834961 17.9375 1.021 18.875 2.16683 18.875H10.5002V16.7917H2.16683V2.20833Z" fill="#FF0000"/>
                        </svg></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="add-container">
            <div class="close-container"><span class="close-icon"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.82397 0.632195L8.99997 6.8082L15.144 0.664195C15.2797 0.519743 15.4432 0.404187 15.6246 0.324455C15.8061 0.244723 16.0018 0.202458 16.2 0.200195C16.6243 0.200195 17.0313 0.368766 17.3313 0.668824C17.6314 0.968883 17.8 1.37585 17.8 1.8002C17.8037 1.99636 17.7673 2.19121 17.693 2.37281C17.6187 2.5544 17.5081 2.71889 17.368 2.8562L11.144 9.0002L17.368 15.2242C17.6317 15.4822 17.7863 15.8315 17.8 16.2002C17.8 16.6245 17.6314 17.0315 17.3313 17.3316C17.0313 17.6316 16.6243 17.8002 16.2 17.8002C15.9961 17.8087 15.7926 17.7746 15.6026 17.7003C15.4125 17.6259 15.24 17.5128 15.096 17.3682L8.99997 11.1922L2.83997 17.3522C2.70478 17.4918 2.54327 17.6033 2.36477 17.6802C2.18627 17.7571 1.99431 17.7979 1.79997 17.8002C1.37562 17.8002 0.968655 17.6316 0.668596 17.3316C0.368538 17.0315 0.199967 16.6245 0.199967 16.2002C0.196237 16.004 0.232632 15.8092 0.306919 15.6276C0.381207 15.446 0.491819 15.2815 0.631967 15.1442L6.85597 9.0002L0.631967 2.7762C0.368263 2.51821 0.213631 2.16885 0.199967 1.8002C0.199967 1.37585 0.368538 0.968883 0.668596 0.668824C0.968655 0.368766 1.37562 0.200195 1.79997 0.200195C2.18397 0.204995 2.55197 0.360195 2.82397 0.632195Z" fill="black"/>
                </svg>
                </span></div>
            <div class="container-list">
                <div class="blocks" onclick="window.location = './Frontend/create-team.php?for=team'">
                    <div class="icon"><svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_432_789)"/>
                        <defs>
                        <linearGradient id="paint0_linear_432_789" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#FFEB0B"/>
                        <stop offset="1" stop-color="#C11218"/>
                        </linearGradient>
                        </defs>
                        </svg>
                        </div>
                    <div class="txt-container"><h3>Create New Team</h3>
                    <p>Roll over previous roster or add a roster from scratch</p></div>
                </div>
                <div class="blocks" onclick="window.location = './Frontend/create-team.php?for=schedule_match'">
                    <div class="icon"><svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_432_789)"/>
                        <defs>
                        <linearGradient id="paint0_linear_432_789" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#FFEB0B"/>
                        <stop offset="1" stop-color="#C11218"/>
                        </linearGradient>
                        </defs>
                        </svg>
                        </div>
                    <div class="txt-container"><h3>Schedule Match</h3>
                    <p>Carry forward your existing roster or build a new one from the ground up</p></div>
                </div>
                <div class="blocks" onclick="window.location = './Frontend/create-team.php?for=tournament'">
                    <div class="icon"><svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_432_789)"/>
                        <defs>
                        <linearGradient id="paint0_linear_432_789" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#FFEB0B"/>
                        <stop offset="1" stop-color="#C11218"/>
                        </linearGradient>
                        </defs>
                        </svg>
                        </div>
                    <div class="txt-container"><h3>Add a Tournament</h3>
                    <p>Continue with a previous tournament or create a new one from scratch</p></div>
                </div>
            </div>
        </div>

        <div id="opacity-container" onclick="hide()"></div>

        <div class="main-body">
            <nav class="nav-bar">
                <div class="nav-content">
                    <a href="javascript:location.reload()">
                        <div class="items">
                            <div class="logo-img"><img src="./assets/images/logo.png" alt=""></div>
                            <div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
                            <sup class="trade-mark">TM</sup>
                        </div>
                    </a>

                    <div class="items list">

                    <form class="d-flex" role="search" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="Get">
                        <!-- retain existing GET parameters as hidden fields -->
                        <input type="hidden" name="sport" value="<?php echo isset($_GET['sport']) ? $_GET['sport'] : ''; ?>">
                        <input type="hidden" name="update" value="<?php echo isset($_GET['update']) ? $_GET['update'] : ''; ?>">

                        <!-- your search input with a name attribute -->
                        <input class="form-control me-2" type="search" name="search" placeholder="Search" aria-label="Search">

                        <button class="btn btn-outline-success" type="submit">
                            <i class='bx bx-search'></i>
                        </button>
                    </form>


                        <a href="" class="menu-bar"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" focusable="false" aria-hidden="true" style="pointer-events: none; display: inherit; width: 100%; height: 100%;"><path d="M21 6H3V5h18v1zm0 5H3v1h18v-1zm0 6H3v1h18v-1z"></path></svg></a>
                    
                    </div>

                </div>
            </nav>
            <div class="game-container">
                <div class="game-list">

                    <div class="game cricket selected"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.5401 23.655L20.6495 21.54L27.0079 27.87L24.8535 30L18.5401 23.655ZM24.7637 0C26.1525 0 27.4843 0.553123 28.4663 1.53769C29.4483 2.52226 30 3.85761 30 5.25C30 6.64239 29.4483 7.97774 28.4663 8.96231C27.4843 9.94688 26.1525 10.5 24.7637 10.5C23.375 10.5 22.0431 9.94688 21.0612 8.96231C20.0792 7.97774 19.5275 6.64239 19.5275 5.25C19.5275 3.85761 20.0792 2.52226 21.0612 1.53769C22.0431 0.553123 23.375 0 24.7637 0ZM0.437601 7.665L4.67149 3.42C4.95687 3.13876 5.34102 2.98117 5.74118 2.98117C6.14134 2.98117 6.52549 3.13876 6.81087 3.42L19.4826 16.155C20.0661 16.74 20.0661 17.685 19.4826 18.27L15.2487 22.5C15.1107 22.6424 14.9456 22.7556 14.7631 22.833C14.5807 22.9103 14.3846 22.9501 14.1865 22.9501C13.9884 22.9501 13.7924 22.9103 13.6099 22.833C13.4275 22.7556 13.2623 22.6424 13.1243 22.5L0.437601 9.795C-0.145867 9.195 -0.145867 8.25 0.437601 7.665Z" fill="black"/>
                    </svg><p>CRICKET</p>
                    </div>

                    <div class="game vollyball"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.5117 14.2404C13.4652 12.0418 12.9992 9.87218 12.1387 7.8483C6.64663 10.4124 2.61226 15.2503 1.23319 20.9591C2.04025 22.8079 3.21062 24.4757 4.67481 25.8636C6.06721 21.0227 9.21909 16.8771 13.5117 14.2404ZM11.2798 6.11269C10.3599 4.51019 9.18757 3.06648 7.80794 1.83716C2.30378 4.84878 -0.950327 11.0837 0.247282 17.6935C2.26749 12.6681 6.19904 8.50142 11.2798 6.11269ZM22.6268 16.097C23.147 10.0617 20.9756 4.15333 16.7295 0.107597C15.8404 0.00479119 13.6085 -0.225011 10.7475 0.621629C14.2479 4.24818 16.2652 9.05249 16.4029 14.0905C18.3305 15.1502 20.4431 15.8313 22.6268 16.097ZM15.0843 16.6715C13.203 17.8135 11.5566 19.3035 10.2334 21.0619C15.1932 24.5271 21.3929 25.6035 27.0181 23.9526C28.2281 22.3317 29.0964 20.4822 29.5706 18.516C27.9711 18.9235 26.3279 19.1347 24.6773 19.1449C21.3748 19.1388 18.0904 18.2922 15.0843 16.6715ZM9.13862 22.7008C8.21924 24.2731 7.58415 25.9906 7.19704 27.7927C9.88013 29.4392 13.0148 30.1965 16.1538 29.9565C19.2928 29.7165 22.2758 28.4915 24.6773 26.4562C17.8485 27.4299 12.3806 24.9565 9.13862 22.7008ZM20.0441 0.893763C23.389 5.15115 25.0342 10.6422 24.5563 16.23C26.399 16.2339 28.2305 15.944 29.9819 15.3713C29.9819 15.2503 30 15.1294 30 15.0085C30 8.48933 25.8447 2.96803 20.0441 0.893763Z" fill="black"/>
                    </svg>
                    <p>VOLLEYBALL</p></div>

                    <div class="game kabaddi"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.66312 21V23.4767C6.66312 23.6628 6.63124 23.8433 6.56748 24.0181C6.50372 24.193 6.40808 24.3498 6.28055 24.4884L2.13603 29.0233C1.90223 29.2791 1.60468 29.407 1.24336 29.407C0.88204 29.407 0.584485 29.2791 0.350691 29.0233C0.116897 28.7674 0 28.4419 0 28.0465C0 27.6512 0.116897 27.3256 0.350691 27.0698L4.11265 22.9535L2.70988 18.8372C2.5611 18.4186 2.52922 17.9363 2.61424 17.3902C2.69926 16.8442 2.94368 16.3498 3.3475 15.907L7.55579 11.3023C7.81084 11.0233 8.08714 10.814 8.3847 10.6744C8.68225 10.5349 9.01169 10.4651 9.37301 10.4651C9.73432 10.4651 10.0638 10.5349 10.3613 10.6744C10.6589 10.814 10.9352 11.0233 11.1902 11.3023L13.7407 14.0233C14.3146 14.6512 15.0159 15.1456 15.8448 15.5065C16.6737 15.8674 17.5664 16.0474 18.5228 16.0465V18.8372C17.2476 18.8372 16.0574 18.6163 14.9522 18.1744C13.847 17.7326 12.8905 17.0698 12.0829 16.186L11.1902 15.2093L8.19341 18.4884L10.5207 21.0698C10.627 21.186 10.712 21.3316 10.7758 21.5065C10.8395 21.6814 10.8714 21.8614 10.8714 22.0465V28.6046C10.8714 29 10.7494 29.3316 10.5054 29.5995C10.2614 29.8674 9.95834 30.0009 9.59617 30C9.234 29.9991 8.93135 29.8651 8.6882 29.5981C8.44506 29.3312 8.32263 29 8.32093 28.6046V22.6744L6.66312 21ZM21.0733 22.3256V28.6046C21.0733 29 20.9509 29.3316 20.7061 29.5995C20.4612 29.8674 20.1586 30.0009 19.7981 30C19.4376 29.9991 19.135 29.8651 18.8901 29.5981C18.6453 29.3312 18.5228 29 18.5228 28.6046V20.8256C18.5228 20.6395 18.5547 20.4595 18.6185 20.2856C18.6823 20.1116 18.7779 19.9544 18.9054 19.814L21.2008 17.4419L20.4357 12.5581C20.1169 12.9767 19.7662 13.3488 19.3836 13.6744C19.0011 14 18.5866 14.3023 18.1403 14.5814C17.4389 14.5349 16.7745 14.3721 16.1471 14.093C15.5197 13.814 14.9938 13.4419 14.5696 12.9767C15.526 12.7907 16.3711 12.4358 17.1048 11.9121C17.8385 11.3884 18.4387 10.72 18.9054 9.90698L20.1807 7.67442C20.542 7.04652 21.0414 6.62187 21.6791 6.40047C22.3167 6.17908 22.9437 6.20838 23.56 6.48838L29.2349 9.13954C29.4686 9.25582 29.6548 9.42466 29.7934 9.64605C29.932 9.86745 30.0008 10.1172 30 10.3954V14.6512C30 15.0465 29.8776 15.3781 29.6327 15.646C29.3879 15.914 29.0852 16.0474 28.7248 16.0465C28.3643 16.0456 28.0616 15.9116 27.8168 15.6447C27.5719 15.3777 27.4495 15.0465 27.4495 14.6512V11.3023L25.1541 10.3256L28.4059 28.2907C28.491 28.7326 28.4004 29.1279 28.1343 29.4767C27.8682 29.8256 27.5124 30 27.0669 30C26.7694 30 26.4982 29.9009 26.2533 29.7028C26.0085 29.5046 25.8546 29.2433 25.7917 28.9186L23.7513 19.5349L21.0733 22.3256ZM14.0595 11.1628C13.3581 11.1628 12.7579 10.8898 12.2589 10.3437C11.7598 9.79768 11.5099 9.14047 11.509 8.3721C11.5082 7.60373 11.7581 6.94698 12.2589 6.40187C12.7596 5.85675 13.3598 5.58326 14.0595 5.5814C14.7592 5.57954 15.3598 5.85303 15.8614 6.40187C16.363 6.9507 16.6125 7.60745 16.61 8.3721C16.6074 9.13675 16.3579 9.79396 15.8614 10.3437C15.3649 10.8935 14.7643 11.1665 14.0595 11.1628ZM20.4357 5.5814C19.7343 5.5814 19.1341 5.30838 18.6351 4.76233C18.136 4.21629 17.8861 3.55908 17.8852 2.79071C17.8844 2.02233 18.1343 1.36559 18.6351 0.820474C19.1358 0.275358 19.736 0.00186989 20.4357 9.42803e-06C21.1354 -0.00185104 21.736 0.271637 22.2376 0.820474C22.7392 1.36931 22.9887 2.02605 22.9862 2.79071C22.9836 3.55536 22.7341 4.21257 22.2376 4.76233C21.7411 5.3121 21.1405 5.58512 20.4357 5.5814Z" fill="black"/>
                    </svg>
                    <p>KABADDI</p></div>

                    <div class="game kho-kho"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.6837 0V1.36364L12.5051 0.490909L7.19388 6.13636L12.1531 8.86364V5.67273L11.8163 5.45455L13.6837 3.49091V30H16.7449V16.6909L19.699 14.0591C23.6327 14.6045 27.6582 12.7636 30 10.6773L27.8265 8.74091C26.0816 10.3091 22.4082 11.7409 19.8061 11.2636L16.7449 8.50909V0M21.3367 4.09091C20.5248 4.09091 19.7462 4.37825 19.1721 4.88971C18.598 5.40117 18.2755 6.09486 18.2755 6.81818C18.2755 7.5415 18.598 8.23519 19.1721 8.74666C19.7462 9.25812 20.5248 9.54545 21.3367 9.54545C22.1486 9.54545 22.9273 9.25812 23.5013 8.74666C24.0754 8.23519 24.398 7.5415 24.398 6.81818C24.398 6.09486 24.0754 5.40117 23.5013 4.88971C22.9273 4.37825 22.1486 4.09091 21.3367 4.09091ZM12.1531 9.21818L6.18367 14.5364C3.35204 17.0455 1.91327 18.8864 0 23.4L2.87755 24.3545C4.40816 20.7273 5.5102 19.2818 7.36224 17.5091L9.42857 19.35L1.85204 26.1L4.02551 28.0364L12.1531 20.7818V9.21818Z" fill="black"/>
                    </svg>
                    <p>KHO-KHO</p></div>

                    <div class="game football"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C6.72909 0 0 6.72909 0 15C0 23.2709 6.72909 30 15 30C23.2709 30 30 23.2709 30 15C30 6.72909 23.2709 0 15 0ZM25.3125 21.9231H22.0514C21.9504 21.9228 21.8512 21.8961 21.7637 21.8455C21.6763 21.7948 21.6036 21.7221 21.5531 21.6346L20.3892 19.6385C20.3126 19.5079 20.2903 19.3526 20.3272 19.2058L21.3988 14.8846C21.4203 14.7975 21.4618 14.7167 21.52 14.6485C21.5782 14.5802 21.6515 14.5265 21.7341 14.4916L23.7606 13.6334C23.8596 13.5914 23.9684 13.5779 24.0747 13.5945C24.1809 13.6111 24.2805 13.657 24.362 13.7272L27.3642 16.3103C27.4382 16.3741 27.4948 16.4555 27.5287 16.5471C27.5627 16.6387 27.5728 16.7373 27.5582 16.8339C27.3095 18.5397 26.7136 20.1763 25.8072 21.6425C25.756 21.7281 25.6835 21.7988 25.5968 21.848C25.5101 21.8972 25.4122 21.923 25.3125 21.9231ZM6.23942 13.6392L8.26587 14.4974C8.34846 14.5323 8.42178 14.586 8.47999 14.6542C8.5382 14.7225 8.5797 14.8033 8.6012 14.8904L9.67284 19.2115C9.70972 19.3583 9.68744 19.5137 9.61082 19.6442L8.44688 21.6346C8.39635 21.7221 8.32373 21.7948 8.23628 21.8455C8.14882 21.8961 8.04961 21.9228 7.94856 21.9231H4.6875C4.58887 21.9231 4.49187 21.8979 4.40577 21.8498C4.31966 21.8017 4.24733 21.7323 4.19567 21.6483C3.28928 20.1821 2.69335 18.5454 2.44471 16.8397C2.43009 16.7431 2.44022 16.6444 2.47415 16.5529C2.50808 16.4613 2.5647 16.3798 2.6387 16.3161L5.64087 13.7329C5.72213 13.6633 5.82115 13.6177 5.92685 13.6012C6.03255 13.5846 6.14077 13.5978 6.23942 13.6392ZM24.7687 7.34784L23.4656 11.0531C23.4394 11.1288 23.3978 11.1983 23.3433 11.257C23.2887 11.3157 23.2226 11.3625 23.149 11.3942L21.0159 12.2978C20.918 12.3393 20.8105 12.3528 20.7054 12.337C20.6003 12.3213 20.5016 12.2767 20.4202 12.2084L16.363 8.80529C16.2982 8.75165 16.2458 8.68448 16.2097 8.60849C16.1736 8.5325 16.1545 8.44952 16.1538 8.36538V5.94375C16.1539 5.84887 16.1774 5.75546 16.2221 5.67181C16.2669 5.58816 16.3316 5.51684 16.4106 5.46418L19.4993 3.40601C19.5799 3.35221 19.6727 3.31958 19.7693 3.31113C19.8658 3.30269 19.963 3.3187 20.0517 3.35769C21.829 4.13279 23.4062 5.30281 24.6635 6.77885C24.7296 6.85602 24.7741 6.94927 24.7926 7.04919C24.8111 7.1491 24.8029 7.25212 24.7687 7.34784ZM10.5 3.40601L13.5894 5.46418C13.6684 5.51684 13.7331 5.58816 13.7779 5.67181C13.8226 5.75546 13.8461 5.84887 13.8462 5.94375V8.36538C13.8462 8.44966 13.8277 8.53292 13.7921 8.60929C13.7564 8.68566 13.7045 8.7533 13.6399 8.80745L9.58269 12.2106C9.50128 12.2789 9.40257 12.3234 9.29745 12.3392C9.19234 12.355 9.08491 12.3414 8.98702 12.3L6.85096 11.3942C6.77693 11.3629 6.71028 11.3164 6.65526 11.2578C6.60024 11.1992 6.55809 11.1297 6.53149 11.0538L5.22837 7.34856C5.1945 7.2525 5.18667 7.14919 5.20567 7.04912C5.22467 6.94906 5.26982 6.85581 5.33654 6.77885C6.59456 5.30133 8.17307 4.13027 9.95192 3.35481C10.0403 3.317 10.1367 3.30188 10.2325 3.31083C10.3282 3.31977 10.4201 3.35249 10.5 3.40601ZM11.7483 27.0209L10.3255 23.4375C10.2929 23.356 10.2794 23.2682 10.2859 23.1808C10.2924 23.0933 10.3188 23.0084 10.363 22.9327L11.457 21.0577C11.5075 20.9702 11.5801 20.8975 11.6676 20.8469C11.755 20.7962 11.8542 20.7695 11.9553 20.7692H18.0447C18.1458 20.7695 18.245 20.7962 18.3324 20.8469C18.4199 20.8975 18.4925 20.9702 18.543 21.0577L19.6377 22.9327C19.6821 23.0084 19.7086 23.0932 19.7152 23.1807C19.7218 23.2682 19.7084 23.356 19.676 23.4375L18.2632 27.0195C18.2287 27.107 18.1732 27.1848 18.1017 27.2461C18.0302 27.3073 17.9447 27.3501 17.8529 27.3707C15.9782 27.8009 14.0304 27.8009 12.1558 27.3707C12.0646 27.3498 11.9799 27.307 11.9089 27.2461C11.8379 27.1852 11.7828 27.1079 11.7483 27.0209Z" fill="black"/>
                    </svg>
                    <p>FOOTBALL</p></div>

                    <div class="game tennis"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.4507 0.00226117C13.9956 0.0472598 12.8706 1.26222 12.9156 2.70218C12.9306 3.06217 13.0056 3.40716 13.1556 3.75215L13.5906 4.71212C13.6506 4.90712 13.5456 5.11711 13.3356 5.17711C13.2006 5.25211 13.0506 5.17711 12.9456 5.05711L12.3156 4.23214C11.8205 3.60216 11.0705 3.21217 10.2755 3.19717C8.82041 3.16717 7.62035 4.32213 7.59035 5.76209C7.57535 6.39207 7.78536 6.99205 8.17537 7.50204L8.8054 8.25202H8.82041C8.94041 8.44701 8.89541 8.68701 8.7304 8.807C8.59539 8.912 8.41539 8.912 8.29538 8.807L7.50034 8.17702C6.99032 7.78703 6.37529 7.57704 5.76026 7.59204C4.3202 7.62204 3.16515 8.822 3.19515 10.277C3.21015 11.0719 3.60017 11.8219 4.23019 12.3169L5.08523 12.9769C5.25024 13.1269 5.25024 13.3669 5.07023 13.5019C5.02087 13.553 4.95691 13.5877 4.8871 13.6011C4.81729 13.6145 4.74503 13.6061 4.68021 13.5769H4.66521L3.75017 13.1569C3.40516 13.0219 3.06014 12.9319 2.70012 12.9169C1.26006 12.8719 0.0450021 14.0118 0 15.4668C0 16.5468 0.600027 17.5367 1.59007 17.9567L18.6759 25.5615L25.5612 18.6767L17.9558 1.59221C17.5358 0.587244 16.5008 -0.0427375 15.4507 0.00226117ZM16.6958 6.15208C17.3258 6.13708 17.8958 6.49707 18.1358 7.06705L22.711 17.3268L16.7858 11.4169L15.3907 8.25202C14.9407 7.30705 15.6307 6.18208 16.6958 6.15208ZM11.7755 10.277C12.1806 10.277 12.5556 10.427 12.8406 10.7269L20.0559 17.9417C20.6559 18.5117 20.6709 19.4567 20.1009 20.0567C19.5009 20.6567 18.5709 20.6717 17.9408 20.0567L10.7255 12.8419C10.5828 12.706 10.4687 12.5431 10.3898 12.3626C10.3109 12.1821 10.2688 11.9877 10.266 11.7907C10.2632 11.5938 10.2998 11.3982 10.3735 11.2155C10.4473 11.0329 10.5567 10.8668 10.6955 10.7269C10.9805 10.427 11.3705 10.277 11.7755 10.277ZM7.69535 15.2568C7.89036 15.2568 8.10037 15.3168 8.25038 15.3918L11.4455 16.8018L17.3558 22.7116L7.06532 18.1367C5.53525 17.4767 6.04528 15.2118 7.69535 15.2568ZM27.4213 21.0616L21.061 27.4215L22.336 28.6964C23.4761 29.8214 25.1262 30.2564 26.6712 29.8514C27.4323 29.6441 28.126 29.2416 28.6838 28.6839C29.2416 28.1262 29.644 27.4325 29.8514 26.6715C30.2564 25.1265 29.8214 23.4766 28.6963 22.3366L27.4213 21.0616Z" fill="black"/>
                    </svg>
                    <p>BADMINTON</p></div>

                    <div class="game table-tennis"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M29.0744 17.3699C30.9198 12.8117 30 7.39234 26.3091 3.69544C21.3938 -1.23181 13.4204 -1.23181 8.50511 3.69544L5.22434 6.98223L17.6151 19.3736C20.3159 15.7353 25.319 14.8389 29.0744 17.3699ZM16.3086 22.0393L3.39648 9.13241C1.79711 11.7864 2.12518 15.2841 4.41 17.5749L6.43119 19.6021L0.396922 24.8399C-0.106911 25.2793 -0.136203 26.0526 0.338337 26.5272L3.46678 29.6616C3.93546 30.1362 4.70879 30.1069 5.14818 29.6031L10.3798 23.5509L12.4713 25.6425C13.6254 26.7967 15.0842 27.4294 16.5899 27.5935C16.1739 26.5975 15.9454 25.5078 15.9454 24.3653C15.9396 23.5568 16.0919 22.7834 16.3086 22.0393ZM24.3758 18.7467C21.2708 18.7467 18.7516 21.266 18.7516 24.3712C18.7516 27.4763 21.2708 29.9956 24.3758 29.9956C27.4808 29.9956 30 27.4763 30 24.3712C30 21.266 27.4808 18.7467 24.3758 18.7467Z" fill="black"/>
                    </svg>
                    <p>TABLE-TENNIS</p></div>

                    <div class="game chess"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.4375 0.9375C8.4375 0.421875 8.01562 0 7.5 0C6.98438 0 6.5625 0.421875 6.5625 0.9375V1.875H5.625C5.10938 1.875 4.6875 2.29688 4.6875 2.8125C4.6875 3.32812 5.10938 3.75 5.625 3.75H6.5625V5.625H3.52734C2.87695 5.625 2.34375 6.1582 2.34375 6.80859C2.34375 6.95508 2.37305 7.0957 2.41992 7.23633L4.32422 12.1875H4.21875C3.43945 12.1875 2.8125 12.8145 2.8125 13.5938C2.8125 14.373 3.43945 15 4.21875 15H4.45312L3.51562 22.5H11.4844L10.5469 15H10.7812C11.5605 15 12.1875 14.373 12.1875 13.5938C12.1875 12.8145 11.5605 12.1875 10.7812 12.1875H10.6758L12.5801 7.23633C12.6328 7.10156 12.6562 6.95508 12.6562 6.80859C12.6562 6.15234 12.123 5.625 11.4727 5.625H8.4375V3.75H9.375C9.89062 3.75 10.3125 3.32812 10.3125 2.8125C10.3125 2.29688 9.89062 1.875 9.375 1.875H8.4375V0.9375ZM2.8125 24.375L0.28125 27.75C0.0996094 27.9961 0 28.2891 0 28.5938C0 29.373 0.626953 30 1.40625 30H13.5938C14.373 30 15 29.373 15 28.5938C15 28.2891 14.9004 27.9961 14.7188 27.75L12.1875 24.375H2.8125ZM19.6875 24.375L17.1562 27.75C16.9746 27.9961 16.875 28.2891 16.875 28.5938C16.875 29.373 17.502 30 18.2812 30H28.5938C29.373 30 30 29.373 30 28.5938C30 28.2891 29.9004 27.9961 29.7188 27.75L27.1875 24.375H19.6875ZM17.8125 12.1875V15.2285C17.8125 15.6855 17.9766 16.125 18.2812 16.4648L19.875 18.2812L19.7461 22.5H27.0996L26.9062 18.2812L28.5645 16.4766C28.8809 16.1309 29.0625 15.6797 29.0625 15.2051V12.1875C29.0625 11.6719 28.6406 11.25 28.125 11.25H27.1875C26.6719 11.25 26.25 11.6719 26.25 12.1875V13.125H24.8438V12.1875C24.8438 11.6719 24.4219 11.25 23.9062 11.25H22.9688C22.4531 11.25 22.0312 11.6719 22.0312 12.1875V13.125H20.625V12.1875C20.625 11.6719 20.2031 11.25 19.6875 11.25H18.75C18.2344 11.25 17.8125 11.6719 17.8125 12.1875ZM22.5 17.8125C22.5 17.2969 22.9219 16.875 23.4375 16.875C23.9531 16.875 24.375 17.2969 24.375 17.8125V19.6875H22.5V17.8125Z" fill="black"/>
                    </svg>
                    <p>CHESS</p></div>

                    <!-- <div class="game weight-lifting"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C13.6757 0 12.5212 2.024 12.5212 4.7C12.5212 7.376 13.6757 9.4 15 9.4C16.3243 9.4 17.4788 7.376 17.4788 4.7C17.4788 2.024 16.3243 0 15 0ZM11.4309 5.8045C9.44422 6.6605 8.4286 9.2063 7.4918 11.9495C6.39134 15.2087 5.7042 19.0547 5.19795 22.2H4.38559V16.7H3.24153V22.2H2.22458V18.3H1.08051V22.2H0V24H1.08051V27.9H2.22458V24H3.24153V29.5H4.38559V24H9.7622C10.1071 25.3 10.5191 26.6 10.9322 27.9L7.88136 28.5V30H12.5078L11.7229 24H18.2771L17.4922 30H22.1186V28.5L19.0678 27.9C19.4809 26.6 19.8928 25.3 20.2378 24H25.6144V29.5H26.7585V24H27.7754V27.9H28.9195V24H30V22.2H28.9195V18.3H27.7754V22.2H26.7585V16.7H25.6144V22.2H24.8021C24.2957 19.0537 23.6061 15.1983 22.4595 11.9273C21.505 9.2043 20.5758 6.7747 18.5623 5.873C18.257 8.8506 16.813 11.2 15 11.2C13.1731 11.2 11.7208 8.8144 11.4309 5.8045ZM11.2016 10.643L11.8397 15.667C9.91525 16.7 8.8983 16.7 8.8983 18.3C8.8983 19.6 9.0661 20.9 9.33369 22.2H7.29775C7.78055 19.3055 8.41875 16.0246 9.2878 13.4506C9.86034 11.998 10.402 11.5722 11.2016 10.643ZM18.7842 10.778C19.5037 11.6033 20.117 11.871 20.6785 13.4727C21.577 16.0359 22.2188 19.3077 22.702 22.2H20.6663C20.9339 20.9 21.1017 19.6 21.1017 18.3C21.1017 16.7 20.0847 16.7 18.1602 15.667L18.7842 10.778ZM15 18.3C16.4587 18.3 18.8136 19.9 18.8136 19.9L18.5127 22.2H11.4873L11.1864 19.9C11.1864 19.9 13.5413 18.3 15 18.3Z" fill="black"/>
                    </svg>
                    <p>WEIGHT-LIFTING</p></div> -->

                    <div class="game basketball"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.1818 12H6.81818C6.45652 12 6.10968 12.158 5.85395 12.4393C5.59821 12.7206 5.45455 13.1022 5.45455 13.5C5.45455 13.8978 5.59821 14.2794 5.85395 14.5607C6.10968 14.842 6.45652 15 6.81818 15H8.18182V28.5C8.18182 28.8978 8.32549 29.2794 8.58122 29.5607C8.83695 29.842 9.1838 30 9.54545 30C9.90711 30 10.254 29.842 10.5097 29.5607C10.7654 29.2794 10.9091 28.8978 10.9091 28.5V27H13.6364V28.5C13.6364 28.8978 13.78 29.2794 14.0358 29.5607C14.2915 29.842 14.6383 30 15 30C15.3617 30 15.7085 29.842 15.9642 29.5607C16.22 29.2794 16.3636 28.8978 16.3636 28.5V27H19.0909V28.5C19.0909 28.8978 19.2346 29.2794 19.4903 29.5607C19.746 29.842 20.0929 30 20.4545 30C20.8162 30 21.1631 29.842 21.4188 29.5607C21.6745 29.2794 21.8182 28.8978 21.8182 28.5V15H23.1818C23.5435 15 23.8903 14.842 24.1461 14.5607C24.4018 14.2794 24.5455 13.8978 24.5455 13.5C24.5455 13.1022 24.4018 12.7206 24.1461 12.4393C23.8903 12.158 23.5435 12 23.1818 12ZM13.6364 24H10.9091V21H13.6364V24ZM13.6364 18H10.9091V15H13.6364V18ZM19.0909 24H16.3636V21H19.0909V24ZM19.0909 18H16.3636V15H19.0909V18ZM25.9091 0H4.09091C3.00593 0 1.96539 0.474106 1.1982 1.31802C0.431005 2.16193 0 3.30653 0 4.5V16.5C0 17.6935 0.431005 18.8381 1.1982 19.682C1.96539 20.5259 3.00593 21 4.09091 21C4.45257 21 4.79941 20.842 5.05515 20.5607C5.31088 20.2794 5.45455 19.8978 5.45455 19.5C5.45455 19.1022 5.31088 18.7206 5.05515 18.4393C4.79941 18.158 4.45257 18 4.09091 18C3.72925 18 3.3824 17.842 3.12667 17.5607C2.87094 17.2794 2.72727 16.8978 2.72727 16.5V4.5C2.72727 4.10218 2.87094 3.72064 3.12667 3.43934C3.3824 3.15804 3.72925 3 4.09091 3H25.9091C26.2708 3 26.6176 3.15804 26.8733 3.43934C27.1291 3.72064 27.2727 4.10218 27.2727 4.5V16.5C27.2727 16.8978 27.1291 17.2794 26.8733 17.5607C26.6176 17.842 26.2708 18 25.9091 18C25.5474 18 25.2006 18.158 24.9449 18.4393C24.6891 18.7206 24.5455 19.1022 24.5455 19.5C24.5455 19.8978 24.6891 20.2794 24.9449 20.5607C25.2006 20.842 25.5474 21 25.9091 21C26.9941 21 28.0346 20.5259 28.8018 19.682C29.569 18.8381 30 17.6935 30 16.5V4.5C30 3.30653 29.569 2.16193 28.8018 1.31802C28.0346 0.474106 26.9941 0 25.9091 0ZM17.7273 9C18.0889 9 18.4358 8.84196 18.6915 8.56066C18.9472 8.27936 19.0909 7.89782 19.0909 7.5C19.0909 7.10218 18.9472 6.72064 18.6915 6.43934C18.4358 6.15804 18.0889 6 17.7273 6H12.2727C11.9111 6 11.5642 6.15804 11.3085 6.43934C11.0528 6.72064 10.9091 7.10218 10.9091 7.5C10.9091 7.89782 11.0528 8.27936 11.3085 8.56066C11.5642 8.84196 11.9111 9 12.2727 9H17.7273Z" fill="black"/>
                    </svg>
                    <p>BASKETBALL</p></div>

                </div>
            </div>
            <div class="updates">
                <div class="update-container live active">Live</div>
                <div class="update-container upcoming">Upcoming</div>
                <div class="update-container all">All</div>
                <div class="update-container completed">Completed</div>
            </div>
            
            <div class="ad2">Advertisement (412px x 80px)</div>
            <div class="info-container">

            </div>

            <?php
            if($_SESSION['role']=="Admin"){
                echo '<div class="pls">';
                    echo '<div class="plus">';
                        echo '<div class="plus-icon"><i class="bx bx-plus bx-rotate-180"></i></div>';
                    echo '</div>';
                echo '</div>';
            }?>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script>
       function resizeText(className) {
    const elements = document.querySelectorAll(className);

    elements.forEach(element => {
        let fontSize = 20;
        element.style.fontSize = fontSize + "px";

        // Temporarily allow wrapping to check overflow
        element.style.whiteSpace = 'nowrap';

        // Stop shrinking too small
        while (
            element.scrollWidth > element.clientWidth &&
            fontSize > 12 // Set your preferred minimum size here
        ) {
            fontSize--;
            element.style.fontSize = fontSize + "px";
        }

        // Optional: revert wrapping
        element.style.whiteSpace = ''; // or 'normal' if your design allows wrapping
    });
}

resizeText(".team-score");

        //display content as per user's selection(Status & Sport)
        function loadgames(update, sport) {
            let params = new URLSearchParams(window.location.search);
            params.set('update', update);
            params.set('sport', sport);
            let newUrl = window.location.pathname + '?' + params.toString();
            window.history.replaceState({}, '', newUrl);

            let data = {
                update: update,
                sport: sport,
                for : 'dashboard',
                search : params.get('search') || null
            }

            displayContent(data);

            function displayContent(data) {
                fetch('update_data.php',{
                    method: 'post',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    let info_container = document.querySelector('.info-container');

                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;

                    requestAnimationFrame(() => {
                        if (info_container && info_container.innerHTML !== tempDiv.innerHTML) {
                            info_container.innerHTML = data;
                            console.log('updated');
                        }
                    });
                    
                })
                .catch(error => console.error(error))
            }
            
        }

        // Open dialog for password
        function openDialog(button, event) {
            if (event) event.stopPropagation();
            const dialog = document.getElementById("startMatchDialog");
            dialog.showModal();

            const match_to_start = button.closest('.game-info').getAttribute('data-match_id');
            console.log("Match : " + match_to_start);

            document.getElementById("match_id").value = match_to_start;
        }

        // Close dialog of password
        function closeDialog() {
            const dialog = document.getElementById("startMatchDialog");
            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });
            document.getElementById("matchPasswordForm").reset();
            dialog.close();
        }

        
        // Variefy match password
        document.getElementById("matchPasswordForm").addEventListener("submit", function(e) {
            e.preventDefault();
            password = document.getElementById("matchPassword").value;
            match_id = document.getElementById("match_id").value;

            let formdata = new FormData();
            formdata.append('password', password.trim());
            formdata.append('match_id', match_id);

            fetch('./Backend/ckeck-match-password.php',{
                method : 'POST',
                body : formdata
            })
            .then(response => response.json())
            .then((data)=>{
                console.log(data);
                document.querySelectorAll('[id^="error-"]').forEach((el) => {
                    el.innerHTML = '';
                    el.style.display = 'none';
                });
                if(data.status != 200){
                    let err = document.getElementById(`error-${data.field}`);
                    err.innerHTML = data.message;
                    err.style.display = 'block';
                }else{
                    window.location.href = `./Frontend/${SportName}/match_toss.php?match_id=${match_id}`;
                    document.getElementById("matchPasswordForm").reset();
                    closeDialog();
                }
            })
            .catch();
        });
        
        //Default Status & Sport
        let update = document.querySelector('.live').textContent.trim();
        console.log(update);
        const defaultCategory = document.querySelector('.cricket');
        let SportName = defaultCategory.querySelector('p').textContent.trim();
        console.log(SportName);
        loadgames(update,SportName);
        setInterval(() => {
            loadgames(update,SportName);
        }, 1000);

        //update Status
        const updates = document.querySelectorAll('.update-container');

        updates.forEach(updatename => {
                updatename.addEventListener('click', () => {
                update = updatename.textContent.trim();
                console.log(update,SportName);

                loadgames(update,SportName)

                document.querySelector('.update-container.active').classList.remove('active');
                updatename.classList.add('active');
            });
        });

        //update sport
        const categories = document.querySelectorAll('.game');

        categories.forEach(category => {
            category.addEventListener('click', () => {
                SportName = category.querySelector('p').textContent.trim();
                console.log(update,SportName);

                loadgames(update,SportName)

                document.querySelector('.game.selected').classList.remove('selected');
                category.classList.add('selected');
            });
        });
        
        //Menu Bar
        const menu_bar = document.querySelector('.menu-bar');
        const side = document.querySelector('.sidebar');
        const closeside = document.querySelector('#close-sidebar');
        const opacity = document.querySelector('#opacity-container');

        menu_bar.addEventListener('click', function (e) {
            e.preventDefault();
            side.classList.add('active');
            opacity.style.display = 'block';
        });

        closeside.addEventListener('click',function(){
            side.classList.remove('active');
            opacity.style.display = 'none';
        });

        //open option container
        let add_container = document.querySelector('.add-container');
        let plus_sign = document.querySelector('.plus');
        if(plus_sign){
            plus_sign.addEventListener('click', function () {
                console.log("clicked")
                //add_container.style.display = "flex";
                add_container.classList.add('active');
                opacity.style.display = 'block';
                plus_sign.style.display = 'none';
            });
        }
        

        //close option container
        let close_container = document.querySelector('.close-icon');
        close_container.addEventListener('click', function () {
            add_container.classList.remove('active');
            //add_container.style.display = "none";
            opacity.style.display = 'none';
            plus_sign.style.display = 'block';
        });

        //log out
        function logout(){
            fetch('logout.php')
            .then(response => response.json())
            .then(data => {
                if(data.status === 200){
                    window.location.href = 'front-page.php';
                    alert ('You have been logged out!')
                }
                console.log(data)})
            .catch(error => console.error(error));
        }

        function deleteAccount(){
            fetch('./Backend/delete_account.php')
            .then(response => response.json())
            .then(data => {
                if(data.status === 200){
                    // window.location.href = 'front-page.php';
                    alert ('Account will deactivate in 24 hrs.!')
                }
                console.log(data)})
            .catch(error => console.error(error));
        }

        //load next-page
        let nextpage = ()=>{
            document.getElementById('loader').style.display = 'flex';

  
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = './front-page.php';

            iframe.onload = function () {

            window.location.href = './front-page.php';
            };

            document.body.appendChild(iframe);
        }

        //hide sidebar 
        let hide = ()=>{
            side.classList.remove('active');
            add_container.classList.remove('active');
            opacity.style.display = 'none';
            plus_sign.style.display = 'block';
        }
        

        let open_scoreboard = (el) => {
            let match = el.getAttribute('data-match_id');
            console.log(match);
            console.log(SportName);
            window.location.href = `./Frontend/${SportName}/scoreboard.php?match_id=${match}`;
        }

        function shareContent() {
            if (navigator.share) {
                navigator.share({
                    title: 'LiveStrike',
                    text: 'Check out this awesome real-time score tracking!',
                    url: window.location.href
                })
                .then(() => console.log('Successfully shared'))
                .catch((error) => console.error('Error sharing:', error));
            } else {
                alert('Sharing not supported on this browser.');
            }
        }
    </script>
</body>
</html>