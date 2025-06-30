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

    $match_id = '';
    $bat_team = '';
    $bowl_team = '';
    $back_decision = false;
    $for = $_GET['for'] ?? '';
    $data = json_decode($_GET['data'] ?? '',true);
    if(empty($data)){
        $match_id = $_GET['match_id'];
    }else{
        $match_id = $data['match_id'];
        $back_decision = true;
    }

    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'"));
    $score_log = json_decode($row['score_log'], true);
    
    //detect current inning
    $current_innings = null;
    foreach ($score_log['innings'] as $innings_name => $innings_data) {
        if ($innings_data['completed'] == false) {
            $current_innings = $innings_name;
            $bat_team = $score_log['innings'][$current_innings]['batting_team'];
            $bowl_team = $score_log['innings'][$current_innings]['bowling_team'];
            break;
        }
    }

    $isfreehit_allow = $score_log['freehit'];
    $iswide_allow = $score_log['wide'];
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>Team Info</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
        }
        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(90deg, var(--primary-light), var(--primary-dark));
        }
        body{
            height: -webkit-fill-available;
            background:white;
            
            display: flex;
            align-items: center;
            justify-content: center;
            
            flex-direction: column;
        }
        .bg{
            position: absolute;
            top: -110px;
            background: url(https://i.pinimg.com/736x/54/16/f3/5416f309b2df3b79e7a7b84444ca7a02.jpg);
            width: 100%;
            background-attachment: local;
            height: 100%;
            background-origin: border-box;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            opacity: 0.4;
            z-index: -1;
        }
        .return{
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
            padding: 40px 40px 0 40px;
        }
        .return svg{
            cursor: pointer;
        }
        .txt{
            text-align: left;
            width: 100%;
            padding-left: 50px;
            color : white;
        }
        .content{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 50%;
        }
        .error{
            display: none;
            color:red; 
            width:100%;
            font-size:12px;
            margin: 5px;
            transition : all 0.3 ease-in-out;
        }
        .score-container{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 10px;
            color : white;
            padding: 10px;
        }
        .score{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 50px;
        }
        .batsman{
             width: 100%;
            display: flex;
            height: 70px;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }
        .overs{
            font-size: 20px;
        }
        .batmans{
            width: 50%;
            height: 100%;
            display: flex;
            gap: 5px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 1.5px solid white;
        }
        .batmans:first-child{
            color : white;
        }
        .batsman-type{
            width: 100%;
            display: flex;
            gap: 10px;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }
        .data-info{
            width: 100%;
            position: relative;
            z-index: 0;
        }
        .bowler-container{
            width: 100%;
            height: 70px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            background: #D9D9D9;
            padding: 12px;
            
        }
        .bowler-name{
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
            gap: 10px;
            font-size: 18px;
        }
        .bowls{
            font-size: 15px;
        }
        .info{
            display: flex;
            justify-content: center;
            gap: 10px;
            align-items: center;
            background: #AFAFAF;
            height: 25px;
        }
        .score-numpad{
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            align-items: center;
            justify-items: center;
            height: 32vh;
        }
        .num-columns{
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            height: -webkit-fill-available;

        }
        .double{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: -webkit-fill-available;
        }
        .num{
            height: -webkit-fill-available;
            width: 100%;
            font-size: 20px;
            border: 0.1px solid #0000002e;
            cursor: pointer;
            outline: none;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .numbers{
            background : white ;
        }
        .direct{
            position: absolute;
            bottom: 0;
            color: #A8A8A8;
        }
        .other-options{
            cursor: pointer;
            display: flex;
            gap: 10px;
            color: white;
            align-items: center;
            justify-content: center;
        }
        .undo{
            color: #009E4A;
        }
        .out{
            color: #A80000;
        }
        .line{
            height: 3px;
            width: 50px;
            background: white;
        }
        .dropdown{
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: absolute;
            bottom: 0;
            z-index: 2;
            transform: translateY(600px);
            transition: all 0.6s ease-in-out;
        }
        .dropdown.active{
            transform: translateY(0px);
        }
        .dropdown-menu{
            width: 100%;
            display: flex;
            border-radius: 30px 30px 0 0;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: white;
        }
        .note{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 20px;
        }
        .menu-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .menu-columns {
            display: contents; /* lets child .menu items participate in parent grid */
        }

        .menu {
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .opacity-container{
            height: 100%;
            width: 100%;
            position: absolute;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
            display: none;
        }
        #shotdialog{
            position: fixed;
            transform: translateX(-50%) translateY(-50%);
            top: 50%;
            left: 50%;
            width: 100%;
            border: none;
            height: max-content;
            background: rgb(255 255 255);
            transition: all 0.5s ease-in-out;
            padding: 20px;
            display: none;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
            flex-direction: column;
            justify-content: center;
            z-index: 2;
            align-items: center;
            scrollbar-width: none;
        }
        .parent-circle{
            width: 380px;
            height: 380px;
            border-radius: 50%;
            display: flex;
            overflow: hidden;
            background: #fff;
            border: 2px solid rgb(255, 81, 0);
            align-items: center;
            justify-content: center;
            background: #149428;
        }
        .circle {
            position: relative;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            overflow: hidden;
            background: #fff;
            border: 3px solid #ffffff;
        }

        .slice {
            position: absolute;
            width: 50%;
            height: 50%;
            top: 50%;
            left: 50%;
            transform-origin: 0% 0%;
            background: #149428;
            border: 1px solid #ffffff;
            box-sizing: border-box;
            cursor: pointer;
            clip-path: polygon(0% 0%, 100% 100%, 0% 150%);
        }

        /* Position each slice with a z-index stack */
        .slice:nth-child(1) { transform: rotate(0deg) }
        .slice:nth-child(2) { transform: rotate(45deg) }
        .slice:nth-child(3) { transform: rotate(90deg) }
        .slice:nth-child(4) { transform: rotate(135deg) }
        .slice:nth-child(5) { transform: rotate(180deg) }
        .slice:nth-child(6) { transform: rotate(225deg) }
        .slice:nth-child(7) { transform: rotate(270deg) }
        .slice:nth-child(8) { transform: rotate(315deg) }

        .slice:hover {
            background:rgba(0, 163, 25, 0.87);
        }
        .batsman-name{
            color: #FF6200;
            font-size: 20px;
            text-transform: capitalize;
        }
        .side{
            position: absolute;
            top: 50%;
            width: 70px;
            left: 0;
            font-size: 11px;
            color: white;
            opacity: 50%;
            padding-left: 10px;
        }
        .slice:nth-child(1) .side { transform: rotate(0deg) }
        .slice:nth-child(2) .side { transform: rotate(315deg) }
        .slice:nth-child(3) .side { transform: rotate(270deg) }
        .slice:nth-child(4) .side { transform: rotate(225deg) }
        .slice:nth-child(5) .side { transform: rotate(180deg) }
        .slice:nth-child(6) .side { transform: rotate(135deg) }
        .slice:nth-child(7) .side { transform: rotate(90deg) }
        .slice:nth-child(8) .side { transform: rotate(45deg) }

        @keyframes shake {
            0%   { transform: translate(-50%, -50%); }
            20%  { transform: translate(calc(-50% - 10px), -50%); }
            40%  { transform: translate(calc(-50% + 10px), -50%); }
            60%  { transform: translate(calc(-50% - 10px), -50%); }
            80%  { transform: translate(calc(-50% + 10px), -50%); }
            100% { transform: translate(-50%, -50%); }
        }
        .pitch-container{
            position: absolute;
            z-index: 10;
            top: 50%;
            right: 50%;
            transform: translate(50%, -50%);
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            align-items: center;
        }
        .pitch{
            height: 50px;
            width: 16px;
            background: #DAC07D;
        }
        .side-name{
            color: #fff;
            background: #149428;
        }
        #selectshot,
        #undo{
            position: fixed;
            transform: translateX(-50%) translateY(-50%);
            top: 50%;
            left: 50%;
            width: 300px;
            border: none;
            height: max-content;
            background: rgb(255 255 255);
            transition: all 0.5s ease-in-out;
            align-items: flex-start;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
            flex-direction: column;
            justify-content: center;
            align-items: center;
            scrollbar-width: none;
        }
        #selectshot::backdrop,
        #undo::backdrop{
            position: fixed;
            inset: 0px;
            background: rgba(0, 0, 0, 0.15);
        }
        .data{
            text-align: left;
            display: grid;
            align-items: center;
            grid-template-columns: repeat(2, 124px);
            justify-content: space-evenly;
            gap: 20px;
            margin: 20px 0;
            justify-items: center;
            height: max-content;
        }
        .text{
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }
        .style-container{
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            align-items: center;
            padding: 20px;
            gap: 10px;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.5);
            background: #80808029;
            text-align: center;
            height: 100%;
            width: 100%;
            font-size: 15px;
            letter-spacing: 1px;
        }
        .style-container.active{
            border : 2px solid #FF6200;
        }
        .style-container:last-child:nth-child(odd){
            grid-column: 1 / -1;
        }
        .undo-container{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }
        .undo-txt{
            font-size: 25px;
            font-weight: bold;
        }
        .undo-warn{
            font-size: 18px;
            color: #A5A5A5;
            letter-spacing: 1px;
        }
        .undo-btn{
            height: 30px;
            width: 130px;
            color: white;
            outline: none;
            border: none;
            background: var(--background);
        }
        .undo-cancel{
            color: #AFAFAF;
            font-size: 15px;
        }
        .shake {
            animation: shake 0.4s;
        }
        .out-text{
            color: #FF6200;
            font-size: 20px;
            letter-spacing: 2px;
        }
        .decision{
            text-transform: capitalize;
        }
        .player-frame{
            position: fixed;
            bottom: -100%;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            transition: bottom 0.8s ease;
            z-index: 999;
        }
        .player-frame.active{
            bottom: 0;
        }
        .type-container{
            width: 100%;
            display: flex;
            flex-direction: row;
            gap: 3px;
            font-size: 10px;
            align-items: center;
            justify-content: space-around;
        }
        .style-container2{
            height: 30px;
            width: max-content;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.5);
            background: #80808029;
            text-align: center;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .style-container2.active{
            border : 2px solid #FF6200;
        }

        @media (min-width:601px) {
            .container{
                display: flex;
                background-color: #bcbcbc;
                position: relative;
                width: 90%;
                max-width: 100%;
                min-height: 480px;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                z-index: 0;
                overflow: hidden;
            }
            .container2{
                gap: 53px;
                width: 100%;
            }
        }

        @media(max-width:601px){
            .container{
                display: flex;
                background-color: #bcbcbc;
                width: 768px;
                z-index: 0;
                max-width: 100%;
                text-align: center;
                align-items: flex-start;
                justify-content: flex-start;
                flex-direction: column;
                flex-wrap: wrap;
                gap: 30px;
                scrollbar-width: none;
                position: relative;
                overflow: hidden;
            }
            .container2{
                width: 100%;
                gap: 99px;
                height: 50%;
                justify-content: space-between;
            }
             .score-numpad{
                height: 34vh;
            }
        }
    </style>
</head>
<body>
    <div class="container"><div class="bg"></div>
        <div class="opacity-container"></div>

        <dialog id="selectshot">
            <div class="text">    
                <h4>Select Shot Type</h4>
                <p>Shot name</p>
            </div>
            <div class="data">
                <div class="style-container">FLICK</div>
                <div class="style-container">PULL</div>
                <div class="style-container">PUNCH</div>
                <div class="style-container">DEFENCE</div>
                <div class="style-container">INSIDE EDGE</div>
                <div class="style-container">SWEEP</div>
                <div class="style-container">NONE OF THE ABOVE</div> 
            </div>
        </dialog>

        <dialog id="undo">
            <div class="undo-container">
                <div class="undo-seyup">
                    <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M72.4125 77.036C72.9522 77.5504 73.674 77.8295 74.4194 77.812C75.1648 77.7944 75.8727 77.4815 76.3875 76.9422C84.2325 68.7185 89.0625 57.431 89.0625 44.9997C89.0625 32.5685 84.2325 21.281 76.3875 13.0572C76.1324 12.7898 75.8271 12.5753 75.4891 12.4259C75.1511 12.2764 74.787 12.195 74.4175 12.1863C74.0481 12.1776 73.6805 12.2418 73.3359 12.3751C72.9912 12.5085 72.6762 12.7084 72.4088 12.9635C72.1413 13.2186 71.9268 13.5238 71.7774 13.8618C71.6279 14.1998 71.5466 14.564 71.5378 14.9334C71.5291 15.3029 71.5933 15.6705 71.7266 16.0151C71.86 16.3598 72.0599 16.6748 72.315 16.9422C79.185 24.1422 83.4375 34.0422 83.4375 44.9997C83.4375 55.9535 79.185 65.861 72.315 73.0572C71.8006 73.5969 71.5214 74.3188 71.539 75.0641C71.5566 75.8095 71.8694 76.5174 72.4088 77.0322M17.5875 77.036C18.1273 76.5217 18.4408 75.814 18.4591 75.0687C18.4774 74.3233 18.199 73.6011 17.685 73.061C10.815 65.8572 6.5625 55.9497 6.5625 44.9997C6.5625 34.0497 10.815 24.1385 17.685 16.9422C18.2002 16.4022 18.4797 15.6796 18.4622 14.9334C18.4446 14.1873 18.1313 13.4787 17.5912 12.9635C17.0512 12.4483 16.3286 12.1687 15.5825 12.1863C14.8363 12.2039 14.1277 12.5172 13.6125 13.0572C5.7675 21.281 0.9375 32.5685 0.9375 44.9997C0.9375 57.431 5.7675 68.7185 13.6125 76.9422C14.1273 77.4815 14.8352 77.7944 15.5806 77.812C16.326 77.8295 17.0478 77.5504 17.5875 77.036ZM40.335 31.4997C40.0725 28.6872 42.24 26.2497 45 26.2497C47.76 26.2497 49.9238 28.6835 49.665 31.4997L47.8125 47.0622C47.7563 47.772 47.4365 48.4351 46.916 48.9209C46.3955 49.4067 45.712 49.6801 45 49.6872C44.288 49.6801 43.6045 49.4067 43.084 48.9209C42.5635 48.4351 42.2437 47.772 42.1875 47.0622L40.335 31.4997ZM49.6875 59.0622C49.6875 60.3054 49.1936 61.4977 48.3146 62.3768C47.4355 63.2559 46.2432 63.7497 45 63.7497C43.7568 63.7497 42.5645 63.2559 41.6854 62.3768C40.8064 61.4977 40.3125 60.3054 40.3125 59.0622C40.3125 57.819 40.8064 56.6267 41.6854 55.7477C42.5645 54.8686 43.7568 54.3747 45 54.3747C46.2432 54.3747 47.4355 54.8686 48.3146 55.7477C49.1936 56.6267 49.6875 57.819 49.6875 59.0622Z" fill="url(#paint0_linear_789_687)"/>
                    <path d="M12.1875 45C12.1875 36.2976 15.6445 27.9516 21.7981 21.7981C27.9516 15.6445 36.2976 12.1875 45 12.1875C53.7024 12.1875 62.0484 15.6445 68.2019 21.7981C74.3555 27.9516 77.8125 36.2976 77.8125 45C77.8125 53.7024 74.3555 62.0484 68.2019 68.2019C62.0484 74.3555 53.7024 77.8125 45 77.8125C36.2976 77.8125 27.9516 74.3555 21.7981 68.2019C15.6445 62.0484 12.1875 53.7024 12.1875 45ZM45 17.8125C41.4297 17.8125 37.8943 18.5157 34.5958 19.882C31.2973 21.2483 28.3001 23.2509 25.7755 25.7755C23.2509 28.3001 21.2483 31.2973 19.882 34.5958C18.5157 37.8943 17.8125 41.4297 17.8125 45C17.8125 48.5703 18.5157 52.1057 19.882 55.4042C21.2483 58.7027 23.2509 61.6999 25.7755 64.2245C28.3001 66.7491 31.2973 68.7517 34.5958 70.118C37.8943 71.4843 41.4297 72.1875 45 72.1875C52.2106 72.1875 59.1258 69.3231 64.2245 64.2245C69.3231 59.1258 72.1875 52.2106 72.1875 45C72.1875 37.7894 69.3231 30.8742 64.2245 25.7755C59.1258 20.6769 52.2106 17.8125 45 17.8125Z" fill="url(#paint1_linear_789_687)"/>
                    <defs>
                    <linearGradient id="paint0_linear_789_687" x1="45" y1="12.1855" x2="45" y2="77.8127" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FAC01F"/>
                    <stop offset="1" stop-color="#F83900"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear_789_687" x1="45" y1="12.1875" x2="45" y2="77.8125" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FAC01F"/>
                    <stop offset="1" stop-color="#F83900"/>
                    </linearGradient>
                    </defs>
                    </svg>
                </div>
                <div class="undo-seyup"><p class="undo-txt">UNDO ?</p></div>
                <div class="undo-seyup"><p class="undo-warn">Cancel the last ball ?</p></div>
                <div class="undo-seyup"><button class="undo-btn">Yes I’m certain</button></div>
                <div class="undo-seyup"><p class="undo-cancel">Cancel</p></div>
            </div>
        </dialog>


        <div class="dropdown">
            <span class="line"></span>
            <div class="dropdown-menu">
                <div class="note">
                    <p>----</p>
                    <p>Click a Shortcut</p>
                    <p>----</p>
                </div>
                <div class="menu-list">

                    <div class="menu-columns">
                        <div class="menu">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 0C15.523 0 20 4.477 20 10C20 15.523 15.523 20 10 20C4.477 20 0 15.523 0 10C0 4.477 4.477 0 10 0ZM10 2C7.87827 2 5.84344 2.84285 4.34315 4.34315C2.84285 5.84344 2 7.87827 2 10C2 12.1217 2.84285 14.1566 4.34315 15.6569C5.84344 17.1571 7.87827 18 10 18C12.1217 18 14.1566 17.1571 15.6569 15.6569C17.1571 14.1566 18 12.1217 18 10C18 7.87827 17.1571 5.84344 15.6569 4.34315C14.1566 2.84285 12.1217 2 10 2ZM10 14C10.2652 14 10.5196 14.1054 10.7071 14.2929C10.8946 14.4804 11 14.7348 11 15C11 15.2652 10.8946 15.5196 10.7071 15.7071C10.5196 15.8946 10.2652 16 10 16C9.73478 16 9.48043 15.8946 9.29289 15.7071C9.10536 15.5196 9 15.2652 9 15C9 14.7348 9.10536 14.4804 9.29289 14.2929C9.48043 14.1054 9.73478 14 10 14ZM10 4.5C10.8423 4.50003 11.6583 4.79335 12.3078 5.3296C12.9573 5.86585 13.3998 6.61154 13.5593 7.43858C13.7188 8.26562 13.5853 9.12239 13.1818 9.86171C12.7783 10.601 12.1299 11.1768 11.348 11.49C11.2322 11.5326 11.1278 11.6014 11.043 11.691C10.999 11.741 10.992 11.805 10.993 11.871L11 12C10.9997 12.2549 10.9021 12.5 10.7272 12.6854C10.5522 12.8707 10.313 12.9822 10.0586 12.9972C9.80416 13.0121 9.55362 12.9293 9.35817 12.7657C9.16271 12.6021 9.0371 12.3701 9.007 12.117L9 12V11.75C9 10.597 9.93 9.905 10.604 9.634C10.8783 9.52446 11.1176 9.34227 11.2962 9.10699C11.4748 8.87171 11.5859 8.59222 11.6176 8.29856C11.6493 8.00489 11.6004 7.70813 11.4762 7.44014C11.352 7.17215 11.1571 6.94307 10.9125 6.77748C10.6679 6.61189 10.3829 6.51606 10.0879 6.50027C9.79295 6.48448 9.49927 6.54934 9.23839 6.68787C8.97752 6.8264 8.75931 7.03338 8.60719 7.28658C8.45508 7.53978 8.37481 7.82962 8.375 8.125C8.375 8.39022 8.26964 8.64457 8.08211 8.83211C7.89457 9.01964 7.64022 9.125 7.375 9.125C7.10978 9.125 6.85543 9.01964 6.66789 8.83211C6.48036 8.64457 6.375 8.39022 6.375 8.125C6.375 7.16359 6.75692 6.24156 7.43674 5.56174C8.11656 4.88192 9.03859 4.5 10 4.5Z" fill="black"/>
                            </svg>
                            <p>Need Help</p>
                        </div>
                        <div class="menu">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.638 13.136L19.539 12.141L19.506 12.144L19.474 12.15L19.638 13.136ZM19.638 13.136L19.738 14.131L19.771 14.128L19.803 14.123L19.638 13.136ZM15.598 18.618L16.231 17.844L16.223 17.838L15.598 18.618ZM15.598 18.618L14.965 19.392L14.973 19.399L15.598 18.618ZM6.40202 18.618L5.76902 17.845L5.75302 17.858L5.73802 17.871L6.40202 18.618ZM6.40202 18.618L7.03502 19.393L7.05102 19.38L7.06602 19.366L6.40202 18.618ZM2.36102 8.86402V7.86402H2.31102L2.26202 7.86902L2.36102 8.86402ZM2.36102 8.86402V9.86402H2.41102L2.46102 9.85902L2.36102 8.86402ZM14.131 2.26102C14.1145 2.06867 14.0908 1.87691 14.062 1.68602C14.0312 1.47588 13.967 1.27199 13.872 1.08202L12.095 2.00002C12.072 1.95502 12.077 1.93902 12.09 2.01802C12.105 2.10702 12.118 2.23102 12.141 2.46102L14.131 2.26102ZM14.226 3.21102C14.289 3.29302 14.268 3.33002 14.233 3.13502C14.2 2.94902 14.174 2.68802 14.131 2.26102L12.14 2.46002C12.18 2.85402 12.214 3.20802 12.265 3.49002C12.314 3.76002 12.4 4.11502 12.635 4.42302L14.226 3.21102ZM14.226 3.21102L12.635 4.42302C12.849 4.70387 13.1336 4.92305 13.4597 5.05829C13.7859 5.19354 14.1421 5.24002 14.492 5.19302L14.226 3.21102ZM14.964 2.60602C14.7399 2.79543 14.5094 2.9772 14.273 3.15102C14.11 3.26402 14.123 3.22402 14.226 3.21102L14.492 5.19302C14.876 5.14102 15.188 4.95102 15.414 4.79302C15.649 4.63102 15.924 4.40502 16.23 4.15402L14.964 2.60602ZM15.98 1.95602C15.7787 2.02298 15.5894 2.12155 15.419 2.24802C15.279 2.34802 15.122 2.47702 14.964 2.60602L16.23 4.15402C16.409 4.00802 16.506 3.92902 16.58 3.87702C16.645 3.83002 16.636 3.84602 16.589 3.86102L15.98 1.95602ZM17.384 2.02602C16.9399 1.83357 16.4411 1.80869 15.98 1.95602L16.589 3.86102L17.384 2.02602ZM18.33 2.77402C18.1954 2.63558 18.0563 2.50151 17.913 2.37202C17.7558 2.22929 17.5769 2.11249 17.383 2.02602L16.589 3.86102C16.543 3.84102 16.536 3.82502 16.596 3.87802C16.664 3.93802 16.753 4.02502 16.916 4.18802L18.33 2.77402ZM19.224 3.66802L18.33 2.77402L16.916 4.18802L17.81 5.08202L19.224 3.66802ZM19.972 4.61402C19.8856 4.42011 19.7688 4.2412 19.626 4.08402C19.4965 3.94108 19.3624 3.80234 19.224 3.66802L17.81 5.08302C17.973 5.24602 18.06 5.33502 18.12 5.40302C18.173 5.46302 18.157 5.45602 18.137 5.41002L19.972 4.61402ZM20.042 6.01802C20.1893 5.55697 20.1645 5.05813 19.972 4.61402L18.137 5.41002L20.042 6.01802ZM19.392 7.03402C19.5162 6.88623 19.6356 6.73449 19.75 6.57902C19.856 6.43102 19.97 6.24702 20.042 6.01802L18.137 5.41002C18.152 5.36302 18.167 5.35302 18.121 5.41902C18.069 5.49202 17.991 5.58902 17.844 5.76802L19.392 7.03402ZM18.787 7.77302C18.774 7.87602 18.734 7.88802 18.847 7.72502C18.954 7.57002 19.12 7.36602 19.392 7.03402L17.844 5.76802C17.594 6.07402 17.367 6.34902 17.204 6.58402C17.047 6.81002 16.857 7.12202 16.805 7.50602L18.787 7.77302ZM18.787 7.77302L16.805 7.50602C16.758 7.85597 16.8045 8.21213 16.9397 8.5383C17.075 8.86446 17.2942 9.14901 17.575 9.36302L18.787 7.77302ZM19.737 7.86802C19.4446 7.84356 19.1531 7.80921 18.863 7.76502C18.668 7.73002 18.705 7.71002 18.787 7.77302L17.575 9.36302C17.883 9.59802 18.237 9.68402 18.509 9.73302C18.79 9.78302 19.144 9.81802 19.538 9.85802L19.737 7.86802ZM20.916 8.12602C20.7261 8.03102 20.5222 7.96688 20.312 7.93602C20.1211 7.90719 19.9294 7.88451 19.737 7.86802L19.538 9.85802C19.6861 9.87103 19.8338 9.8877 19.981 9.90802C20.061 9.92202 20.044 9.92702 19.999 9.90402L20.916 8.12602ZM21.859 9.16902C21.6813 8.71868 21.3462 8.34809 20.916 8.12602L20 9.90602L21.859 9.16902ZM21.999 10.367C21.999 10.163 21.999 9.96002 21.988 9.78802C21.9779 9.57576 21.934 9.36647 21.858 9.16802L20 9.90602C19.982 9.85902 19.988 9.84302 19.994 9.92302C20 10.013 20 10.138 20 10.369L21.999 10.367ZM21.999 11.631V10.367H19.999V11.631H21.999ZM21.859 12.829C21.947 12.606 21.976 12.392 21.988 12.209C21.999 12.038 21.999 11.835 21.999 11.631H19.999C19.999 11.862 19.999 11.987 19.993 12.077C19.987 12.157 19.981 12.141 19.999 12.094L21.859 12.829ZM20.916 13.872C21.3462 13.65 21.6813 13.2794 21.859 12.829L20 12.095L20.916 13.872ZM19.737 14.13C19.941 14.11 20.142 14.09 20.312 14.062C20.492 14.032 20.702 13.982 20.916 13.872L20 12.095C20.044 12.072 20.061 12.077 19.982 12.09C19.8349 12.1111 19.6871 12.1281 19.539 12.141L19.737 14.13ZM18.788 14.225C18.705 14.288 18.668 14.268 18.863 14.233C19.049 14.2 19.31 14.173 19.737 14.13L19.538 12.14C19.145 12.18 18.79 12.214 18.509 12.265C18.238 12.314 17.883 12.4 17.575 12.635L18.788 14.225ZM18.788 14.225L17.575 12.635C17.2942 12.849 17.075 13.1336 16.9397 13.4597C16.8045 13.7859 16.758 14.1421 16.805 14.492L18.788 14.225ZM19.392 14.963C19.2026 14.7392 19.0209 14.5091 18.847 14.273C18.734 14.11 18.775 14.122 18.788 14.225L16.805 14.492C16.857 14.876 17.047 15.188 17.205 15.414C17.368 15.649 17.594 15.924 17.844 16.23L19.392 14.963ZM20.042 15.979C19.975 15.7781 19.8764 15.589 19.75 15.419C19.65 15.278 19.521 15.122 19.392 14.963L17.844 16.23C17.99 16.409 18.069 16.505 18.121 16.579C18.168 16.644 18.153 16.636 18.137 16.589L20.042 15.979ZM19.972 17.384C20.1647 16.9396 20.1896 16.4404 20.042 15.979L18.137 16.589L19.972 17.384ZM19.224 18.33C19.369 18.185 19.512 18.043 19.626 17.913C19.746 17.775 19.876 17.604 19.972 17.383L18.137 16.588C18.157 16.542 18.173 16.536 18.12 16.596C18.06 16.664 17.973 16.752 17.81 16.916L19.224 18.33ZM18.33 19.223L19.224 18.33L17.81 16.916L16.916 17.809L18.33 19.223ZM17.384 19.972C17.578 19.8852 17.7569 19.7681 17.914 19.625C18.043 19.512 18.186 19.368 18.33 19.223L16.916 17.809C16.8123 17.9153 16.7055 18.0187 16.596 18.119C16.536 18.173 16.543 18.157 16.589 18.137L17.384 19.972ZM15.98 20.042C16.4411 20.1893 16.9399 20.1645 17.384 19.972L16.589 18.137L15.98 20.042ZM14.964 19.392C15.122 19.521 15.278 19.65 15.419 19.75C15.567 19.855 15.751 19.969 15.98 20.042L16.589 18.137C16.636 18.152 16.645 18.167 16.579 18.121C16.4601 18.0319 16.3438 17.9396 16.23 17.844L14.964 19.392ZM14.226 18.788C14.123 18.773 14.11 18.733 14.273 18.846C14.428 18.954 14.631 19.119 14.963 19.391L16.23 17.844C15.924 17.593 15.649 17.367 15.414 17.204C15.188 17.046 14.876 16.857 14.492 16.805L14.226 18.788ZM14.226 18.788L14.492 16.805C14.1421 16.758 13.7859 16.8045 13.4597 16.9397C13.1336 17.075 12.849 17.2942 12.635 17.575L14.226 18.788ZM14.13 19.737C14.173 19.31 14.2 19.048 14.233 18.863C14.268 18.668 14.288 18.704 14.225 18.787L12.635 17.574C12.4 17.882 12.314 18.237 12.265 18.508C12.215 18.79 12.181 19.144 12.141 19.538L14.13 19.737ZM13.871 20.916C13.981 20.702 14.031 20.492 14.061 20.312C14.091 20.142 14.109 19.941 14.13 19.737L12.141 19.538C12.1278 19.6865 12.1105 19.8335 12.089 19.981C12.076 20.061 12.071 20.044 12.094 19.999L13.871 20.916ZM12.83 21.859C13.2796 21.6809 13.6494 21.3459 13.871 20.916L12.095 20L12.83 21.859ZM11.63 21.999C11.835 21.999 12.038 21.999 12.21 21.988C12.392 21.976 12.606 21.949 12.83 21.859L12.095 20C12.142 19.982 12.158 19.988 12.078 19.994C11.9294 20.0002 11.7807 20.0022 11.632 20L11.63 21.999ZM10.367 21.999H11.63L11.631 19.999H10.367V21.999ZM9.16902 21.859C9.39202 21.947 9.60602 21.976 9.78902 21.988C9.96002 21.999 10.163 21.999 10.367 21.999V19.999C10.136 19.999 10.011 19.999 9.92102 19.993C9.84102 19.987 9.85702 19.981 9.90402 19.999L9.16902 21.859ZM8.12602 20.916C8.34809 21.3462 8.71868 21.6813 9.16902 21.859L9.90402 19.999L8.12602 20.916ZM7.86802 19.737C7.88802 19.941 7.90802 20.142 7.93602 20.312C7.96602 20.492 8.01602 20.702 8.12602 20.916L9.90602 20C9.92902 20.044 9.92402 20.061 9.91102 19.982C9.88991 19.8349 9.8729 19.6871 9.86002 19.539L7.86802 19.737ZM7.77302 18.787C7.71002 18.705 7.73002 18.667 7.76502 18.863C7.79802 19.048 7.82502 19.31 7.86802 19.737L9.85802 19.538C9.81802 19.144 9.78402 18.79 9.73302 18.508C9.68402 18.238 9.59802 17.883 9.36302 17.575L7.77302 18.787ZM7.77302 18.787L9.36302 17.575C9.14901 17.2942 8.86446 17.075 8.5383 16.9397C8.21213 16.8045 7.85597 16.758 7.50602 16.805L7.77302 18.787ZM7.03402 19.392C7.36702 19.12 7.57002 18.954 7.72502 18.847C7.88802 18.734 7.87602 18.774 7.77302 18.787L7.50602 16.805C7.12202 16.857 6.81002 17.047 6.58402 17.205C6.34902 17.367 6.07402 17.593 5.76802 17.844L7.03402 19.392ZM6.01802 20.042C6.21932 19.9751 6.40869 19.8765 6.57902 19.75C6.71902 19.65 6.87602 19.521 7.03402 19.392L5.76902 17.845C5.65495 17.9406 5.53924 18.033 5.42002 18.122C5.35502 18.169 5.36402 18.154 5.41102 18.138L6.01802 20.042ZM4.61402 19.972C5.05813 20.1645 5.55697 20.1893 6.01802 20.042L5.40902 18.137L4.61402 19.972ZM3.66702 19.224C3.81202 19.369 3.95502 19.512 4.08502 19.626C4.22202 19.746 4.39302 19.877 4.61502 19.973L5.40902 18.137C5.45502 18.157 5.46202 18.173 5.40202 18.12C5.2925 18.0197 5.18579 17.9163 5.08202 17.81L3.66702 19.224ZM2.77402 18.33L3.66802 19.224L5.08202 17.81L4.18802 16.916L2.77402 18.33ZM2.02602 17.384C2.12102 17.604 2.25202 17.776 2.37202 17.914C2.48602 18.043 2.62902 18.186 2.77402 18.33L4.18802 16.916C4.08174 16.8123 3.97836 16.7055 3.87802 16.596C3.82502 16.536 3.84102 16.543 3.86102 16.589L2.02602 17.384ZM1.95602 15.98C1.80869 16.4411 1.83357 16.9399 2.02602 17.384L3.86102 16.589L1.95602 15.98ZM2.60602 14.964C2.48184 15.1118 2.36244 15.2636 2.24802 15.419C2.12155 15.5894 2.02298 15.7787 1.95602 15.98L3.86102 16.589C3.84502 16.636 3.83102 16.645 3.87702 16.579C3.92902 16.506 4.00702 16.409 4.15402 16.23L2.60602 14.964ZM3.21002 14.225C3.22402 14.122 3.26402 14.11 3.15102 14.273C3.04402 14.428 2.87802 14.632 2.60602 14.964L4.15402 16.23C4.40402 15.924 4.63102 15.649 4.79402 15.414C4.95102 15.188 5.14102 14.876 5.19302 14.492L3.21002 14.225ZM3.21002 14.225L5.19302 14.492C5.24002 14.1421 5.19354 13.7859 5.05829 13.4597C4.92305 13.1336 4.70387 12.849 4.42302 12.635L3.21002 14.225ZM2.26002 14.13C2.68802 14.173 2.95002 14.2 3.13502 14.233C3.33002 14.268 3.29302 14.288 3.21002 14.225L4.42302 12.635C4.11502 12.4 3.76102 12.314 3.48902 12.265C3.20802 12.215 2.85402 12.18 2.45902 12.14L2.26002 14.13ZM1.08202 13.872C1.29602 13.982 1.50602 14.032 1.68602 14.062C1.85602 14.09 2.05702 14.111 2.26102 14.131L2.45902 12.14C2.31092 12.1275 2.16419 12.1108 2.01702 12.09C1.93702 12.076 1.95402 12.071 1.99902 12.094L1.08202 13.872ZM0.14002 12.83C0.317751 13.2804 0.652807 13.651 1.08302 13.873L2.00002 12.095L0.14002 12.83ZM2.04061e-05 11.632C2.04061e-05 11.836 2.04435e-05 12.039 0.0110204 12.211C0.0230204 12.393 0.0510204 12.607 0.14102 12.831L2.00002 12.094C2.01802 12.141 2.01202 12.157 2.00702 12.077C2.00056 11.9288 1.99823 11.7804 2.00002 11.632H2.04061e-05ZM2.04061e-05 10.368V11.632H2.00002V10.368H2.04061e-05ZM0.14002 9.16902C0.0643663 9.36752 0.0208206 9.57681 0.0110204 9.78902C-0.000979557 9.96102 2.04061e-05 10.164 2.04061e-05 10.369H2.00002C2.00002 10.137 2.00002 10.012 2.00702 9.92202C2.01202 9.84202 2.01802 9.85802 2.00002 9.90502L0.14002 9.16902ZM1.08302 8.12802C0.653167 8.3496 0.318159 8.71942 0.14002 9.16902L2.00002 9.90502L1.08302 8.12802ZM2.26202 7.86802C2.05802 7.88902 1.85702 7.90802 1.68702 7.93802C1.50702 7.96802 1.29702 8.01802 1.08302 8.12802L2.00002 9.90502C1.95502 9.92802 1.93902 9.92302 2.01802 9.91002C2.16518 9.88891 2.31291 9.8719 2.46102 9.85902L2.26202 7.86802ZM3.21202 7.77402C3.29502 7.71102 3.33202 7.73102 3.13602 7.76602C2.95102 7.79902 2.68902 7.82602 2.26202 7.86902L2.46102 9.85902C2.85402 9.81902 3.20902 9.78502 3.49102 9.73402C3.76202 9.68502 4.11702 9.59902 4.42502 9.36402L3.21202 7.77402ZM3.21202 7.77402L4.42502 9.36402C4.70569 9.14989 4.92467 8.8653 5.05974 8.53914C5.1948 8.21298 5.24113 7.85688 5.19402 7.50702L3.21202 7.77402ZM2.60702 7.03502C2.87902 7.36702 3.04502 7.57102 3.15302 7.72602C3.26602 7.88902 3.22602 7.87702 3.21202 7.77402L5.19402 7.50702C5.14202 7.12302 4.95302 6.81202 4.79502 6.58502C4.63202 6.35002 4.40502 6.07502 4.15502 5.76902L2.60702 7.03502ZM1.95702 6.01802C2.03002 6.24802 2.14302 6.43102 2.24902 6.58002C2.34902 6.72002 2.47802 6.87702 2.60702 7.03502L4.15502 5.76902C4.05945 5.65495 3.96708 5.53924 3.87802 5.42002C3.83102 5.35502 3.84702 5.36302 3.86202 5.41002L1.95702 6.01802ZM2.02702 4.61502C1.83481 5.05884 1.80995 5.55727 1.95702 6.01802L3.86202 5.41002L2.02702 4.61502ZM2.77502 3.66802C2.63002 3.81302 2.48702 3.95502 2.37302 4.08502C2.23029 4.2422 2.11349 4.42111 2.02702 4.61502L3.86202 5.41002C3.84202 5.45602 3.82602 5.46302 3.87902 5.40202C3.93902 5.33402 4.02602 5.24602 4.18902 5.08202L2.77502 3.66802ZM3.66902 2.77502L2.77502 3.66802L4.18902 5.08202L5.08402 4.18902L3.66902 2.77502ZM4.61502 2.02602C4.42104 2.1128 4.24213 2.22993 4.08502 2.37302C3.94208 2.50253 3.80334 2.6366 3.66902 2.77502L5.08402 4.18902C5.24702 4.02502 5.33502 3.93802 5.40302 3.87902C5.46302 3.82502 5.45602 3.84102 5.41002 3.86102L4.61502 2.02602ZM6.01902 1.95602C5.55797 1.80869 5.05913 1.83357 4.61502 2.02602L5.41002 3.86102L6.01902 1.95602ZM7.03402 2.60602C6.87602 2.47702 6.72102 2.34902 6.58002 2.24902C6.40976 2.1222 6.22039 2.02329 6.01902 1.95602L5.41002 3.86102C5.36302 3.84602 5.35402 3.83102 5.42002 3.87702C5.49302 3.93002 5.59002 4.00802 5.76902 4.15502L7.03402 2.60602ZM7.77402 3.21102C7.87702 3.22502 7.88902 3.26502 7.72602 3.15202C7.48994 2.97819 7.25981 2.79642 7.03602 2.60702L5.76902 4.15502C6.07502 4.40502 6.34902 4.63102 6.58402 4.79502C6.81002 4.95202 7.12202 5.14102 7.50602 5.19302L7.77402 3.21102ZM7.77402 3.21102L7.50702 5.19302C7.85697 5.24002 8.21313 5.19354 8.5393 5.05829C8.86546 4.92305 9.15001 4.70387 9.36402 4.42302L7.77402 3.21102ZM7.86902 2.26102C7.82602 2.68902 7.79902 2.95102 7.76602 3.13602C7.73102 3.33102 7.71102 3.29402 7.77402 3.21102L9.36402 4.42302C9.59902 4.11502 9.68502 3.76102 9.73402 3.49002C9.78402 3.20902 9.82002 2.85402 9.85902 2.46002L7.86902 2.26102ZM8.12702 1.08302C8.03202 1.27299 7.96788 1.47688 7.93702 1.68702C7.90902 1.85702 7.88902 2.05802 7.86902 2.26202L9.85902 2.46002C9.87203 2.31196 9.8887 2.16525 9.90902 2.01802C9.92302 1.93802 9.92802 1.95602 9.90502 2.00002L8.12702 1.08302ZM9.17002 0.14002C8.71968 0.317751 8.34909 0.652807 8.12702 1.08302L9.90502 2.00002L9.17002 0.14002ZM10.368 2.04061e-05C10.164 2.04061e-05 9.96102 2.04435e-05 9.78902 0.0110204C9.57681 0.0208206 9.36752 0.0643663 9.16902 0.14002L9.90602 2.00002C9.85902 2.01802 9.84302 2.01202 9.92302 2.00702C10.013 2.00102 10.138 2.00002 10.369 2.00002L10.368 2.04061e-05ZM11.632 2.04061e-05H10.368V2.00002H11.632V2.04061e-05ZM12.83 0.14002C12.6315 0.0643663 12.4222 0.0208206 12.21 0.0110204C12.04 -0.000979557 11.837 2.04061e-05 11.633 2.04061e-05V2.00002C11.864 2.00002 11.989 2.00002 12.079 2.00702C12.159 2.01202 12.142 2.01802 12.095 2.00002L12.83 0.14002ZM13.873 1.08302C13.651 0.652807 13.2804 0.317751 12.83 0.14002L12.095 2.00002L13.873 1.08302ZM14 11C14 11.7957 13.6839 12.5587 13.1213 13.1213C12.5587 13.6839 11.7957 14 11 14V16C12.3261 16 13.5979 15.4732 14.5356 14.5356C15.4732 13.5979 16 12.3261 16 11H14ZM11 8.00002C11.7957 8.00002 12.5587 8.31609 13.1213 8.8787C13.6839 9.44131 14 10.2044 14 11H16C16 9.67394 15.4732 8.40217 14.5356 7.46449C13.5979 6.5268 12.3261 6.00002 11 6.00002V8.00002ZM8.00002 11C8.00002 10.2044 8.31609 9.44131 8.8787 8.8787C9.44131 8.31609 10.2044 8.00002 11 8.00002V6.00002C9.67394 6.00002 8.40217 6.5268 7.46449 7.46449C6.5268 8.40217 6.00002 9.67394 6.00002 11H8.00002ZM11 14C10.2044 14 9.44131 13.6839 8.8787 13.1213C8.31609 12.5587 8.00002 11.7957 8.00002 11H6.00002C6.00002 12.3261 6.5268 13.5979 7.46449 14.5356C8.40217 15.4732 9.67394 16 11 16V14Z" fill="black"/>
                            </svg>
                            <p>Match Rules</p>
                        </div>
                        <div class="menu">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 14.5H18V1.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 9.5H4V20.5M21 4.5L18 1.5L15 4.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 17.5L4 20.5L1 17.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p>Change Scorer</p>
                        </div>
                    </div>
                    <div class="menu-columns">
                        <div class="menu">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 19.5C3.80653 19.5 2.66193 19.0259 1.81802 18.182C0.974106 17.3381 0.5 16.1935 0.5 15C0.5 13.8065 0.974106 12.6619 1.81802 11.818C2.66193 10.9741 3.80653 10.5 5 10.5C6.19347 10.5 7.33807 10.9741 8.18198 11.818C9.02589 12.6619 9.5 13.8065 9.5 15C9.5 16.1935 9.02589 17.3381 8.18198 18.182C7.33807 19.0259 6.19347 19.5 5 19.5ZM15 9.5C14.4091 9.5 13.8239 9.3836 13.2779 9.15746C12.732 8.93131 12.2359 8.59984 11.818 8.18198C11.4002 7.76412 11.0687 7.26804 10.8425 6.72208C10.6164 6.17611 10.5 5.59095 10.5 5C10.5 4.40905 10.6164 3.82389 10.8425 3.27792C11.0687 2.73196 11.4002 2.23588 11.818 1.81802C12.2359 1.40016 12.732 1.06869 13.2779 0.842542C13.8239 0.616396 14.4091 0.5 15 0.5C16.1935 0.5 17.3381 0.974106 18.182 1.81802C19.0259 2.66193 19.5 3.80653 19.5 5C19.5 6.19347 19.0259 7.33807 18.182 8.18198C17.3381 9.02589 16.1935 9.5 15 9.5ZM5 17.5C5.66304 17.5 6.29893 17.2366 6.76777 16.7678C7.23661 16.2989 7.5 15.663 7.5 15C7.5 14.337 7.23661 13.7011 6.76777 13.2322C6.29893 12.7634 5.66304 12.5 5 12.5C4.33696 12.5 3.70107 12.7634 3.23223 13.2322C2.76339 13.7011 2.5 14.337 2.5 15C2.5 15.663 2.76339 16.2989 3.23223 16.7678C3.70107 17.2366 4.33696 17.5 5 17.5ZM15 7.5C15.663 7.5 16.2989 7.23661 16.7678 6.76777C17.2366 6.29893 17.5 5.66304 17.5 5C17.5 4.33696 17.2366 3.70107 16.7678 3.23223C16.2989 2.76339 15.663 2.5 15 2.5C14.337 2.5 13.7011 2.76339 13.2322 3.23223C12.7634 3.70107 12.5 4.33696 12.5 5C12.5 5.66304 12.7634 6.29893 13.2322 6.76777C13.7011 7.23661 14.337 7.5 15 7.5ZM1 6C1 4.67392 1.52678 3.40215 2.46447 2.46447C3.40215 1.52678 4.67392 1 6 1H9V3H6C5.20435 3 4.44129 3.31607 3.87868 3.87868C3.31607 4.44129 3 5.20435 3 6V9H1V6ZM19 11H17V14C17 14.7956 16.6839 15.5587 16.1213 16.1213C15.5587 16.6839 14.7956 17 14 17H11V19H14C15.3261 19 16.5979 18.4732 17.5355 17.5355C18.4732 16.5979 19 15.3261 19 14V11Z" fill="black"/>
                            </svg>
                            <p>Change Squad</p>
                        </div>
                        <div class="menu">
                            <svg width="20" height="24" viewBox="0 0 20 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 18V12M10 18V13M13 18V10M15 3H19V23H1V3H5M5 1H15V5H5V1Z" stroke="black" stroke-width="2"/>
                            </svg>
                            <p>Full Scoreboard</p>
                        </div>
                        <div class="menu">
                            <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 9.99998V6.99998C19 6.46954 18.7893 5.96084 18.4142 5.58576C18.0391 5.21069 17.5304 4.99998 17 4.99998H11M11 4.99998L14 7.99998M11 4.99998L14 1.99998M1 12.013V15.013C1 15.5434 1.21071 16.0521 1.58579 16.4272C1.96086 16.8023 2.46957 17.013 3 17.013H9M9 17.013L6 14.013M9 17.013L6 20.013M19 20.499C19 19.9685 18.7893 19.4598 18.4142 19.0848C18.0391 18.7097 17.5304 18.499 17 18.499H15C14.4696 18.499 13.9609 18.7097 13.5858 19.0848C13.2107 19.4598 13 19.9685 13 20.499M7 8.49898C7 7.96854 6.78929 7.45984 6.41421 7.08476C6.03914 6.70969 5.53043 6.49898 5 6.49898H3C2.46957 6.49898 1.96086 6.70969 1.58579 7.08476C1.21071 7.45984 1 7.96854 1 8.49898M14 15.502C14.0001 15.7647 14.0519 16.0248 14.1525 16.2675C14.2531 16.5102 14.4005 16.7307 14.5863 16.9164C14.7721 17.1021 14.9927 17.2494 15.2354 17.3499C15.4781 17.4504 15.7383 17.502 16.001 17.502C16.2637 17.5019 16.5238 17.4501 16.7665 17.3495C17.0092 17.2489 17.2297 17.1015 17.4154 16.9157C17.6011 16.7299 17.7484 16.5093 17.8489 16.2666C17.9494 16.0238 18.0011 15.7637 18.001 15.501C18.0009 14.9704 17.79 14.4616 17.4147 14.0866C17.0395 13.7115 16.5306 13.5008 16 13.501C15.4694 13.5011 14.9607 13.712 14.5856 14.0873C14.2105 14.4625 13.9999 14.9714 14 15.502ZM2 3.50198C2.00007 3.76469 2.05187 4.02481 2.15247 4.2675C2.25307 4.51018 2.40048 4.73068 2.58629 4.9164C2.7721 5.10211 2.99267 5.24941 3.2354 5.34989C3.47814 5.45036 3.73829 5.50204 4.001 5.50198C4.26371 5.50191 4.52383 5.4501 4.76652 5.34951C5.00921 5.24891 5.2297 5.1015 5.41542 4.91569C5.60114 4.72988 5.74844 4.50931 5.84891 4.26657C5.94939 4.02384 6.00107 3.76369 6.001 3.50098C6.00087 2.97041 5.78997 2.46163 5.41471 2.08656C5.03945 1.71148 4.53057 1.50084 4 1.50098C3.46943 1.50111 2.96065 1.712 2.58558 2.08726C2.21051 2.46252 1.99987 2.97141 2 3.50198Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p>Replace Batter</p>
                        </div>
                    </div>
                    <div class="menu-columns">
                        <div class="menu">
                            <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 9.99998V6.99998C19 6.46954 18.7893 5.96084 18.4142 5.58576C18.0391 5.21069 17.5304 4.99998 17 4.99998H11M11 4.99998L14 7.99998M11 4.99998L14 1.99998M1 12.013V15.013C1 15.5434 1.21071 16.0521 1.58579 16.4272C1.96086 16.8023 2.46957 17.013 3 17.013H9M9 17.013L6 14.013M9 17.013L6 20.013M19 20.499C19 19.9685 18.7893 19.4598 18.4142 19.0848C18.0391 18.7097 17.5304 18.499 17 18.499H15C14.4696 18.499 13.9609 18.7097 13.5858 19.0848C13.2107 19.4598 13 19.9685 13 20.499M7 8.49898C7 7.96854 6.78929 7.45984 6.41421 7.08476C6.03914 6.70969 5.53043 6.49898 5 6.49898H3C2.46957 6.49898 1.96086 6.70969 1.58579 7.08476C1.21071 7.45984 1 7.96854 1 8.49898M14 15.502C14.0001 15.7647 14.0519 16.0248 14.1525 16.2675C14.2531 16.5102 14.4005 16.7307 14.5863 16.9164C14.7721 17.1021 14.9927 17.2494 15.2354 17.3499C15.4781 17.4504 15.7383 17.502 16.001 17.502C16.2637 17.5019 16.5238 17.4501 16.7665 17.3495C17.0092 17.2489 17.2297 17.1015 17.4154 16.9157C17.6011 16.7299 17.7484 16.5093 17.8489 16.2666C17.9494 16.0238 18.0011 15.7637 18.001 15.501C18.0009 14.9704 17.79 14.4616 17.4147 14.0866C17.0395 13.7115 16.5306 13.5008 16 13.501C15.4694 13.5011 14.9607 13.712 14.5856 14.0873C14.2105 14.4625 13.9999 14.9714 14 15.502ZM2 3.50198C2.00007 3.76469 2.05187 4.02481 2.15247 4.2675C2.25307 4.51018 2.40048 4.73068 2.58629 4.9164C2.7721 5.10211 2.99267 5.24941 3.2354 5.34989C3.47814 5.45036 3.73829 5.50204 4.001 5.50198C4.26371 5.50191 4.52383 5.4501 4.76652 5.34951C5.00921 5.24891 5.2297 5.1015 5.41542 4.91569C5.60114 4.72988 5.74844 4.50931 5.84891 4.26657C5.94939 4.02384 6.00107 3.76369 6.001 3.50098C6.00087 2.97041 5.78997 2.46163 5.41471 2.08656C5.03945 1.71148 4.53057 1.50084 4 1.50098C3.46943 1.50111 2.96065 1.712 2.58558 2.08726C2.21051 2.46252 1.99987 2.97141 2 3.50198Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p>Replace Bowler</p>
                        </div>
                        <div class="menu">
                            <svg width="21" height="23" viewBox="0 0 21 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.33508 8.13039L6.60508 8.39539L6.29508 8.70039C6.42054 8.89595 6.56096 9.08151 6.71508 9.25539L13.7552 2.21039C13.5807 2.06069 13.397 1.92209 13.2052 1.79539L12.8952 2.10039L12.6302 1.83539L12.8702 1.59539C11.9232 1.08617 10.8374 0.895748 9.7737 1.05236C8.71 1.20897 7.72512 1.70427 6.96514 2.4648C6.20517 3.22533 5.71058 4.21056 5.55475 5.27436C5.39893 6.33816 5.59015 7.42385 6.10007 8.37039L6.33508 8.13039ZM11.9302 2.53539L12.1952 2.80039L11.5001 3.50039L11.2351 3.23539L11.9302 2.53539ZM10.5351 3.93539L10.7951 4.20039L10.1001 4.90039L9.83513 4.63539L10.5351 3.93539ZM9.13512 5.33539L9.40012 5.60039L8.70011 6.29539L8.43511 6.03039L9.13512 5.33539ZM7.7351 6.73039L8.0001 7.00039L7.30009 7.70039L7.03509 7.43539L7.7351 6.73039ZM7.7951 10.2054L8.1051 9.90039L8.37011 10.1654L8.1301 10.4054C9.07703 10.9146 10.1629 11.105 11.2266 10.9484C12.2903 10.7918 13.2752 10.2965 14.0351 9.53597C14.7951 8.77545 15.2897 7.79021 15.4455 6.72641C15.6013 5.66262 15.4101 4.57693 14.9002 3.63039L14.6652 3.87039L14.3952 3.60539L14.7052 3.30039C14.5797 3.10482 14.4393 2.91927 14.2852 2.74539L7.24509 9.79039C7.41957 9.94008 7.60327 10.0787 7.7951 10.2054ZM13.7002 4.30539L13.9652 4.57039L13.2652 5.27039L13.0002 5.00039L13.7002 4.30539ZM12.3002 5.70539L12.5652 5.97039L11.8652 6.66539L11.6002 6.40039L12.3002 5.70539ZM10.9001 7.10039L11.1651 7.36539L10.4701 8.06539L10.2001 7.80039L10.9001 7.10039ZM9.50012 8.50039L9.76513 8.76539L9.06512 9.46539L8.80011 9.20039L9.50012 8.50039Z" fill="#272727"/>
                            <g clip-path="url(#clip0_789_487)">
                            <path d="M17.2233 21.522L19.5633 18.5925C19.9877 18.0609 20.2189 17.4008 20.2188 16.7205V10.2705C20.2188 9.87268 20.0608 9.49115 19.7794 9.20985C19.4981 8.92854 19.1166 8.77051 18.7188 8.77051C18.3209 8.77051 17.9394 8.92854 17.6581 9.20985C17.3768 9.49115 17.2188 9.87268 17.2188 10.2705V14.7705" stroke="black" stroke-linecap="round"/>
                            <path d="M15.8463 17.5133L16.9068 16.4528C17.188 16.1715 17.346 15.79 17.346 15.3923C17.346 14.9945 17.188 14.613 16.9068 14.3318C16.6255 14.0506 16.2441 13.8926 15.8463 13.8926C15.4486 13.8926 15.0671 14.0506 14.7858 14.3318L14.1648 14.9528C12.7587 16.3592 11.9688 18.2665 11.9688 20.2553V21.5213" stroke="black" stroke-linecap="round"/>
                            <path d="M3.77878 21.522L1.43875 18.5925C1.01378 18.0611 0.7821 17.401 0.781738 16.7205V10.2705C0.781738 9.87268 0.939776 9.49115 1.22108 9.20985C1.50239 8.92854 1.88393 8.77051 2.28176 8.77051C2.67959 8.77051 3.06113 8.92854 3.34243 9.20985C3.62374 9.49115 3.78178 9.87268 3.78178 10.2705V14.7705" stroke="black" stroke-linecap="round"/>
                            <path d="M5.15449 17.5133L4.09397 16.4528C3.81276 16.1715 3.65479 15.79 3.65479 15.3923C3.65479 14.9945 3.81276 14.613 4.09397 14.3318C4.37527 14.0506 4.75673 13.8926 5.15449 13.8926C5.55224 13.8926 5.93371 14.0506 6.215 14.3318L6.83601 14.9528C8.2421 16.3592 9.03201 18.2665 9.03204 20.2553V21.5213" stroke="black" stroke-linecap="round"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_789_487">
                            <rect width="21.0003" height="21" fill="white" transform="translate(0 1.5)"/>
                            </clipPath>
                            </defs>
                            </svg>
                            <p>Dropped Catch</p>
                        </div>
                        <div class="menu">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.25 2.75H4.75C4.21957 2.75 3.71086 2.96071 3.33579 3.33579C2.96071 3.71086 2.75 4.21957 2.75 4.75V19.25C2.75 19.7804 2.96071 20.2891 3.33579 20.6642C3.71086 21.0393 4.21957 21.25 4.75 21.25H19.25C19.7804 21.25 20.2891 21.0393 20.6642 20.6642C21.0393 20.2891 21.25 19.7804 21.25 19.25V4.75C21.25 4.21957 21.0393 3.71086 20.6642 3.33579C20.2891 2.96071 19.7804 2.75 19.25 2.75Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2.75 16.734L12.304 15.011C13.426 16.1055 13.4675 17.678 13.0745 19.416C15.57 18.1265 16.1065 16.657 16.0785 14.33L21.25 13.3975M6.208 10.115L8.7975 12.857M8.2605 9.75898L6.745 13.2125M12.259 12.2575L11.722 9.15948L12.7365 8.98348C13.3095 8.88448 13.855 9.26948 13.9545 9.84398C14.054 10.4185 13.67 10.965 13.097 11.064M13.097 11.064L12.0825 11.24M13.097 11.064L14.287 11.905M8.9095 9.64698L10.962 9.29098M10.4725 12.567L9.9355 9.46898M18.09 10.897C18.323 11.112 18.5775 11.162 18.9085 11.105L19.3675 11.0255C19.5723 10.9897 19.7545 10.8742 19.8742 10.7042C19.9939 10.5343 20.0413 10.3238 20.006 10.119V10.1155C19.9703 9.91053 19.8548 9.72811 19.6847 9.6083C19.5147 9.48848 19.304 9.44107 19.099 9.47648L18.593 9.56398C18.4914 9.58158 18.3874 9.579 18.2868 9.55639C18.1862 9.53378 18.0911 9.49157 18.0068 9.43219C17.9225 9.37281 17.8508 9.29741 17.7957 9.21029C17.7405 9.12318 17.7031 9.02606 17.6855 8.92448C17.6499 8.71896 17.6974 8.50772 17.8175 8.33718C17.9376 8.16665 18.1205 8.05076 18.326 8.01498L18.7825 7.93598C19.1135 7.87848 19.3675 7.92948 19.6005 8.14398M4.1425 12.0685L5.152 11.8935M5.9595 13.349L4.411 13.617L3.874 10.5195L5.423 10.251M16.74 9.06098C16.9175 8.93098 16.898 8.72098 16.8815 8.62498L16.8375 8.37148" stroke="black" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.7202 8.74609C16.9273 8.74609 17.0952 8.5782 17.0952 8.37109C17.0952 8.16399 16.9273 7.99609 16.7202 7.99609C16.5131 7.99609 16.3452 8.16399 16.3452 8.37109C16.3452 8.5782 16.5131 8.74609 16.7202 8.74609Z" fill="black"/>
                            <path d="M16.5325 10.4593L15.16 10.6973M14.998 11.7823L15.4875 8.50684L17.0505 11.4268" stroke="black" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p>Bonus Runs</p>
                        </div>
                    </div>
                    <div class="menu-columns">
                        <div class="menu">
                            <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.62344 7.18438C9.62344 8.17344 8.95781 8.97031 8.1375 8.97031C7.80469 8.97031 7.5375 8.91406 7.33125 8.79688V5.60938C7.5375 5.48281 7.80469 5.4125 8.1375 5.4125C8.95781 5.4125 9.62344 6.20469 9.62344 7.18438ZM12.0891 10.325C11.7562 10.325 11.4891 10.3953 11.2828 10.5219V13.7094C11.4891 13.8266 11.7562 13.8828 12.0891 13.8828C12.9047 13.8828 13.575 13.0906 13.575 12.1109C13.575 11.1219 12.9047 10.325 12.0891 10.325ZM21 2.75V19.25C21 20.4922 19.9922 21.5 18.75 21.5H2.25C1.00781 21.5 0 20.4922 0 19.25V2.75C0 1.50781 1.00781 0.5 2.25 0.5H18.75C19.9922 0.5 21 1.50781 21 2.75ZM8.67188 10.9578C10.5938 10.9578 12.15 9.28906 12.15 7.22656C12.15 5.16406 10.5938 3.49531 8.67188 3.49531C8.10938 3.49531 7.54219 3.64531 7.05 3.90781H4.90781V13.5781L7.33594 13.1047V10.7328C7.73906 10.8781 8.18437 10.9578 8.67188 10.9578ZM16.0969 12.1437C16.0969 10.0812 14.5406 8.4125 12.6328 8.4125C12.4828 8.4125 12.3328 8.42188 12.1828 8.44531C12.0094 9.03125 11.7094 9.56094 11.2828 10.0109C10.6359 10.7141 9.77344 11.1266 8.85469 11.1734V18.5L11.2828 18.0266V15.6547C11.6859 15.8047 12.1359 15.875 12.6281 15.875C14.5406 15.875 16.0969 14.2062 16.0969 12.1437Z" fill="black"/>
                            </svg>
                            <p>Power Play</p>
                        </div>
                        <div class="menu">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 18C12.1217 18 14.1566 17.1571 15.6569 15.6569C17.1571 14.1566 18 12.1217 18 10C18 7.87827 17.1571 5.84344 15.6569 4.34315C14.1566 2.84285 12.1217 2 10 2C7.87827 2 5.84344 2.84285 4.34315 4.34315C2.84285 5.84344 2 7.87827 2 10C2 12.1217 2.84285 14.1566 4.34315 15.6569C5.84344 17.1571 7.87827 18 10 18ZM10 0C11.3132 0 12.6136 0.258658 13.8268 0.761205C15.0401 1.26375 16.1425 2.00035 17.0711 2.92893C17.9997 3.85752 18.7362 4.95991 19.2388 6.17317C19.7413 7.38642 20 8.68678 20 10C20 12.6522 18.9464 15.1957 17.0711 17.0711C15.1957 18.9464 12.6522 20 10 20C4.47 20 0 15.5 0 10C0 7.34784 1.05357 4.8043 2.92893 2.92893C4.8043 1.05357 7.34784 0 10 0ZM10.5 5V10.25L15 12.92L14.25 14.15L9 11V5H10.5Z" fill="black"/>
                            </svg>
                            <p>Match Breaks</p>
                        </div>
                        <div class="menu">
                            <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.00006 4C4.00006 1.79 5.79006 0 8.00006 0C10.2101 0 12.0001 1.79 12.0001 4C12.0001 6.21 10.2101 8 8.00006 8C5.79006 8 4.00006 6.21 4.00006 4ZM13.0001 20H14.0001C15.1001 20 16.0001 19.1 16.0001 18V13.22C16.0001 12.1 15.3901 11.07 14.3901 10.56C13.9601 10.34 13.5001 10.13 13.0001 9.94V20ZM8.34007 15L11.0001 9.33C10.0701 9.12 9.07006 9 8.00006 9C5.47007 9 3.29007 9.7 1.61007 10.56C1.12212 10.8113 0.713366 11.1929 0.429165 11.6625C0.144963 12.132 -0.00356536 12.6712 6.49903e-05 13.22V20H2.34007C2.12007 19.55 2.00007 19.04 2.00007 18.5C2.00007 16.57 3.57007 15 5.50006 15H8.34007ZM6.00006 20L7.41006 17H5.50006C4.67006 17 4.00006 17.67 4.00006 18.5C4.00006 19.33 4.67006 20 5.50006 20H6.00006Z" fill="black"/>
                            </svg>
                            <p>Retired Hurt</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="return" >
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.2502 12.75H3.81271L13.0002 21.9375L11.8452 23.25L0.470215 11.875L11.8452 0.5L13.0002 1.8125L3.81271 11H25.2502V12.75Z" fill="white"/>
                </svg>
            </div>
            <div id='commentaryIcon'>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mic-on">
                <path d="M12 1a4 4 0 0 1 4 4v6a4 4 0 0 1-8 0V5a4 4 0 0 1 4-4z"/>
                <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                <line x1="12" y1="19" x2="12" y2="23"/>
                <line x1="8" y1="23" x2="16" y2="23"/>
                </svg>
            </div>
        </div>

        
            <div class="container2">
                <div class="txt">
                    <h4>
                        <?php
                            $t_id = $score_log['innings'][$current_innings]['batting_team'];
                            $t_name = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$t_id'"));
                            echo $t_name['t_name'];
                        ?>
                        (toss winning team)
                    </h4>
                </div>
                
                <div class="score-container">
                    <div class="score">
                        <?php 
                            echo $score_log['innings'][$current_innings]['total_runs'].'/'.$score_log['innings'][$current_innings]['wickets']; 
                        ?> 
                        <p class="overs">(<?php echo $score_log['innings'][$current_innings]['overs_completed']; ?>/
                        <?php echo $score_log['overs']; ?>)</p>
                    </div>
                    <div class="decision">
                        <?php 
                            $toss_winner_id = $row['toss_winner'];
                            $toss_winner_name = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM teams WHERE t_id = '$toss_winner_id'"));
                            echo $toss_winner_name['t_name'];      
                        ?> won the toss and elected to 
                        <?php 
                            echo $row['toss_decision']; 
                        ?>
                    </div>
                </div>
                
                <div class="data-info">
                    <div class="batsman">
                        <div class="batmans">
                                <?php
                                    
                                    $striker = $score_log['innings'][$current_innings]['openers']['striker_id']['id'];
                                    $name = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$striker'"));
                                ?>
                            <div class="batsman-type" data-striker='<?php echo $striker; ?>'>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.9641 6.85855L17.7261 10.5918C17.9948 10.8584 18.1299 11.1702 18.1312 11.5271C18.1326 11.8841 18 12.1969 17.7333 12.4656L6.40028 23.8859C6.13362 24.1546 5.83299 24.2896 5.49838 24.2909C5.16377 24.2922 4.86211 24.1595 4.5934 23.8928L0.83141 20.1596C0.562696 19.8929 0.427613 19.57 0.426158 19.1908C0.424703 18.8116 0.557304 18.4876 0.823964 18.2189L12.0903 6.86574C12.357 6.59702 12.6688 6.46198 13.0257 6.46061C13.3826 6.45924 13.6954 6.59189 13.9641 6.85855ZM23.4521 2.94063L18.7189 7.7103L16.8379 5.84368L21.5711 1.07402C21.8155 0.827697 22.1274 0.703808 22.5066 0.702353C22.8858 0.700898 23.1986 0.822391 23.4449 1.06683C23.6912 1.31127 23.8151 1.6231 23.8166 2.00232C23.818 2.38154 23.6965 2.69431 23.4521 2.94063ZM4.74339 2.14268C4.74339 2.14268 5.12042 1.47201 4.51156 2.57213C4.39223 2.78774 4.74339 2.14268 4.74339 2.14268C4.74339 2.14268 4.07831 3.21752 4.07418 2.14525C4.08373 4.62457 4.74339 2.14268 4.74339 2.14268Z" fill="white"/>
                                </svg>
                                <?php
                                    echo $name['fname'] ?? '';
                                ?>
                                (striker)
                            </div>
                            <p class="batsman-score">
                                <?php
                                    echo $score_log['innings'][$current_innings]['openers']['striker_id']['runs'].' ('.
                                        $score_log['innings'][$current_innings]['openers']['striker_id']['balls_faced'].')';
                                ?>
                            </p>

                        </div>

                        <div class="batmans">
                                <?php
                                    
                                    $non_striker = $score_log['innings'][$current_innings]['openers']['non_striker_id']['id'];
                                    $name = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$non_striker'"));
                                ?>
                            <div class="batsman-type" data-non-striker='<?php echo $non_striker; ?>'>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.9641 6.85855L17.7261 10.5918C17.9948 10.8584 18.1299 11.1702 18.1312 11.5271C18.1326 11.8841 18 12.1969 17.7333 12.4656L6.40028 23.8859C6.13362 24.1546 5.83299 24.2896 5.49838 24.2909C5.16377 24.2922 4.86211 24.1595 4.5934 23.8928L0.83141 20.1596C0.562696 19.8929 0.427613 19.57 0.426158 19.1908C0.424703 18.8116 0.557304 18.4876 0.823964 18.2189L12.0903 6.86574C12.357 6.59702 12.6688 6.46198 13.0257 6.46061C13.3826 6.45924 13.6954 6.59189 13.9641 6.85855ZM23.4521 2.94063L18.7189 7.7103L16.8379 5.84368L21.5711 1.07402C21.8155 0.827697 22.1274 0.703808 22.5066 0.702353C22.8858 0.700898 23.1986 0.822391 23.4449 1.06683C23.6912 1.31127 23.8151 1.6231 23.8166 2.00232C23.818 2.38154 23.6965 2.69431 23.4521 2.94063ZM4.74339 2.14268C4.74339 2.14268 5.12042 1.47201 4.51156 2.57213C4.39223 2.78774 4.74339 2.14268 4.74339 2.14268C4.74339 2.14268 4.07831 3.21752 4.07418 2.14525C4.08373 4.62457 4.74339 2.14268 4.74339 2.14268Z" fill="black"/>
                                </svg>
                                <?php
                                    echo $name['fname'] ?? '';
                                ?>
                                (non-striker)
                            </div>
                            <p class="batsman-score">
                                <?php
                                    echo $score_log['innings'][$current_innings]['openers']['non_striker_id']['runs'].' ('.
                                        $score_log['innings'][$current_innings]['openers']['non_striker_id']['balls_faced'].')';
                                ?>
                            </p>

                        </div>

                    </div>
                    <div class="numpad">
                        <div class="bowler-container">
                                    <?php
                                    
                                        $bowler = $score_log['innings'][$current_innings]['current_bowler']['id'];
                                        $name = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$bowler'"));
                                    ?>
                                <div class="bowler-name" data-bowler="<?php echo $bowler; ?>"> 
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.67013 14.2598L2.21013 14.7898L1.59013 15.3998C1.84106 15.7909 2.12189 16.162 2.43013 16.5098L16.5101 2.4198C16.1612 2.12041 15.7938 1.8432 15.4101 1.5898L14.7901 2.1998L14.2601 1.6698L14.7401 1.1898C12.8463 0.171369 10.6746 -0.20948 8.54727 0.103742C6.41991 0.416965 4.45017 1.40757 2.93024 2.92863C1.41031 4.44968 0.421157 6.42015 0.109505 8.54774C-0.202146 10.6753 0.180306 12.8467 1.20013 14.7398L1.67013 14.2598ZM12.8601 3.0698L13.3901 3.5998L12.0001 4.9998L11.4701 4.4698L12.8601 3.0698ZM10.0701 5.8698L10.5901 6.3998L9.20013 7.7998L8.67013 7.2698L10.0701 5.8698ZM7.27013 8.6698L7.80013 9.1998L6.40013 10.5898L5.87013 10.0598L7.27013 8.6698ZM4.47013 11.4598L5.00013 11.9998L3.60013 13.3998L3.07013 12.8698L4.47013 11.4598ZM4.59013 18.4098L5.21013 17.7998L5.74013 18.3298L5.26013 18.8098C7.15396 19.8282 9.32563 20.2091 11.453 19.8959C13.5804 19.5826 15.5501 18.592 17.07 17.071C18.59 15.5499 19.5791 13.5794 19.8908 11.4519C20.2024 9.32426 19.82 7.15287 18.8001 5.2598L18.3301 5.7398L17.7901 5.2098L18.4101 4.5998C18.1592 4.20867 17.8784 3.83756 17.5701 3.4898L3.49013 17.5798C3.83909 17.8792 4.20648 18.1564 4.59013 18.4098ZM16.4001 6.6098L16.9301 7.1398L15.5301 8.5398L15.0001 7.9998L16.4001 6.6098ZM13.6001 9.4098L14.1301 9.9398L12.7301 11.3298L12.2001 10.7998L13.6001 9.4098ZM10.8001 12.1998L11.3301 12.7298L9.94013 14.1298L9.40013 13.5998L10.8001 12.1998ZM8.00013 14.9998L8.53013 15.5298L7.13013 16.9298L6.60013 16.3998L8.00013 14.9998Z" fill="black"/>
                                    </svg>
                                    <?php
                                        echo $name['fname'] ?? '';
                                    ?>
                                    (bowler)
                                </div>
                                <div class="bowls">0.0-0-0-0</div>
                        </div>
                        <div class="score-numpad">

                            <div class="num-columns">
                                <button class="num numbers">0</button>
                                <button class="num numbers">3</button>
                                <button class="num wide">WD</button>
                            </div>
               
                            <div class="num-columns">
                                <button class="num numbers">1</button>
                                <button class="num numbers">4<p class="direct">FOUR</p></button>
                                <button class="num nb">NB</button>
                            </div>
               
                            <div class="num-columns">
                                <button class="num numbers">2</button>
                                <button class="num numbers">6<p class="direct">SIX</p></button>
                                <button class="num bye">BYE</button>
                            </div>

                            <div class="num-columns">
                                <button class="num undo">UNDO</button>
                                <div class="double">
                                    <button class="num">5</button>
                                    <button class="num">7</button>
                                </div>
                                <button class="num out">OUT</button>
                                <button class="num lb">LB</button>
                            </div>

                        </div>
                        <div class="option-slider">
                            <div class="info">
                                <div class='other-options' onclick='open_dropdown()'>
                                    Scoring Efficiencies
                                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.63804 0.37971L0.328037 5.95271C0.239392 6.0457 0.189941 6.16924 0.189941 6.29771C0.189941 6.42618 0.239392 6.54972 0.328037 6.64271L0.334037 6.64871C0.37701 6.69394 0.428736 6.72996 0.486069 6.75458C0.543402 6.77919 0.605144 6.79188 0.667537 6.79188C0.72993 6.79188 0.791671 6.77919 0.849004 6.75458C0.906337 6.72996 0.958063 6.69394 1.00104 6.64871L6.00104 1.40071L10.999 6.64871C11.042 6.69394 11.0937 6.72996 11.1511 6.75458C11.2084 6.77919 11.2701 6.79188 11.3325 6.79188C11.3949 6.79188 11.4567 6.77919 11.514 6.75458C11.5713 6.72996 11.6231 6.69394 11.666 6.64871L11.672 6.64271C11.7607 6.54972 11.8101 6.42618 11.8101 6.29771C11.8101 6.16924 11.7607 6.0457 11.672 5.95271L6.36204 0.37971C6.31534 0.330697 6.25918 0.291678 6.19695 0.265018C6.13472 0.238357 6.06773 0.224609 6.00004 0.224609C5.93234 0.224609 5.86535 0.238357 5.80312 0.265018C5.7409 0.291678 5.68473 0.330697 5.63804 0.37971Z" fill="white"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="shotdialog">
                <div class="batsman-name batsman">batsman name</div>
                <div class="parent-circle">
                    <div class="circle">
                        <div class="slice" data-part="1"><p class="side">Long On</p></div>
                        <div class="slice" data-part="2"><p class="side">Long Off</p></div>
                        <div class="slice" data-part="3"><p class="side">Deep Cover</p></div>
                        <div class="slice" data-part="4"><p class="side">Deep Point</p></div>
                        <div class="slice" data-part="5"><p class="side">Third Man</p></div>
                        <div class="slice" data-part="6"><p class="side">Deep Fine Leg</p></div>
                        <div class="slice" data-part="7"><p class="side">Deep Square Leg</p></div>
                        <div class="slice" data-part="8"><p class="side">Deep Mid Wicket</p></div>

                        <div class="pitch-container">
                            <p class="side-name">OFF</p>
                            <div class="pitch">
                                <svg width="22" height="28" viewBox="0 0 22 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.0667 3C10.0667 3.79565 10.3617 4.55871 10.8868 5.12132C11.4119 5.68393 12.1241 6 12.8667 6C13.6093 6 14.3215 5.68393 14.8466 5.12132C15.3717 4.55871 15.6667 3.79565 15.6667 3C15.6667 2.20435 15.3717 1.44129 14.8466 0.87868C14.3215 0.31607 13.6093 0 12.8667 0C12.1241 0 11.4119 0.31607 10.8868 0.87868C10.3617 1.44129 10.0667 2.20435 10.0667 3ZM4.69069 14.36L8.42402 10.36C8.51475 10.2431 8.62916 10.15 8.75854 10.0876C8.88792 10.0252 9.02888 9.99525 9.17069 10H18C18.3394 9.99176 18.6697 10.1177 18.928 10.3538C19.1862 10.5899 19.3542 10.9196 19.4 11.28L21.2667 26.68C21.2789 26.7729 21.2789 26.8671 21.2667 26.96C21.2667 27.2252 21.1684 27.4796 20.9933 27.6671C20.8183 27.8546 20.5809 27.96 20.3334 27.96C20.1292 27.9748 19.9263 27.9159 19.7571 27.7926C19.5879 27.6694 19.4622 27.4889 19.4 27.28L17.16 19.5L16.6747 17.84L15.928 18.72L13.8 21.2V27C13.8 27.2652 13.7017 27.5196 13.5267 27.7071C13.3516 27.8946 13.1142 28 12.8667 28C12.6192 28 12.3818 27.8946 12.2067 27.7071C12.0317 27.5196 11.9334 27.2652 11.9334 27V21C11.9499 20.8427 12.0011 20.6919 12.0827 20.56L14.7707 17L12.904 12H9.71202L5.97869 15.68C5.89227 15.7854 5.78611 15.8702 5.66712 15.9287C5.54813 15.9873 5.41896 16.0184 5.28802 16.02C5.05712 15.9902 4.84492 15.8691 4.693 15.6804C4.54107 15.4917 4.46037 15.2491 4.46669 15C4.46887 14.7651 4.54818 14.5385 4.69069 14.36ZM0.160116 24.614C0.329971 24.6894 0.0301132 24.4747 0.160116 24.614C0.290119 24.7533 0.0897598 24.432 0.160116 24.614C0.160116 24.417 0.230473 24.796 0.160116 24.614C0.160116 24.811 0.230473 24.432 0.160116 24.614C0.0897598 24.796 0.290119 24.4747 0.160116 24.614C0.0301132 24.7533 0.329971 24.5386 0.160116 24.614C-0.00973892 24.6894 0.343967 24.614 0.160116 24.614C-0.211187 24.614 0.422667 24.8953 0.160116 24.614C-0.102434 24.3327 0.160116 25.0118 0.160116 24.614C-0.102434 24.8953 0.160116 24.2162 0.160116 24.614C-0.211187 24.614 0.422667 24.3327 0.160116 24.614ZM4.91469 19.48V18C4.91469 17.8674 4.96385 17.7402 5.05137 17.6464C5.13889 17.5527 5.25759 17.5 5.38135 17.5C5.50512 17.5 5.62382 17.5527 5.71134 17.6464C5.79885 17.7402 5.84802 17.8674 5.84802 18V19.48C5.98762 19.564 6.10419 19.6858 6.18609 19.8332C6.26798 19.9806 6.31232 20.1485 6.31469 20.32V27C6.31469 27.2652 6.21635 27.5196 6.04132 27.7071C5.86629 27.8946 5.62889 28 5.38135 28C5.13382 28 4.89642 27.8946 4.72139 27.7071C4.54635 27.5196 4.44802 27.2652 4.44802 27V20.32C4.45038 20.1485 4.49473 19.9806 4.57662 19.8332C4.65851 19.6858 4.77509 19.564 4.91469 19.48Z" fill="black"/>
                                </svg>
                            </div>
                            <p class="side-name">ON</p>
                        </div>
                    </div>
                </div>
            </div>
    </div>


    <iframe src="./select-player-from-team.php" frameborder="0" class="player-frame"></iframe>
    
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const back_decision = '<?php echo $back_decision; ?>';
        let data = urlParams.get('data') || '';
        console.log(data);
        const match = urlParams.get('match_id') || '';
        let opacity = document.querySelector('.opacity-container');
        let dropdown = document.querySelector('.dropdown');
        let shotdialog = document.querySelector('#shotdialog');
        let shot = document.querySelector('#selectshot');
        let data_container = document.querySelector('.data');
        let undo = document.querySelector('.undo');
        let undo_container = document.querySelector('#undo');
        let out = document.querySelector('.out');
        let bye = document.querySelector('.bye');
        let lb = document.querySelector('.lb');
        let NB = document.querySelector('.nb');
        let wide_ball = document.querySelector('.wide');
        let comm = document.getElementById('comm');
        let players_page = document.querySelector('.player-frame');
        let bat_team = '<?php echo $bat_team; ?>';
        let bowl_team = '<?php echo $bowl_team; ?>';
        let DeepFineLeg = `
        <div class="style-container" onclick="get_shot(this)">FLICK</div>
        <div class="style-container" onclick="get_shot(this)">PULL</div>
        <div class="style-container" onclick="get_shot(this)">PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">DEFENCE</div>
        <div class="style-container" onclick="get_shot(this)">INSIDE EDGE</div>
        <div class="style-container" onclick="get_shot(this)">SWEEP</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>
        `;
        let DeepSquareLeg = `
        <div class="style-container" onclick="get_shot(this)">FLICK</div>
        <div class="style-container" onclick="get_shot(this)">PULL</div>
        <div class="style-container" onclick="get_shot(this)">PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">DEFENCE</div>
        <div class="style-container" onclick="get_shot(this)">INSIDE EDGE</div>
        <div class="style-container" onclick="get_shot(this)">SWEEP</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>`;

        let DeepMidWicket = `
        <div class="style-container" onclick="get_shot(this)">FLICK</div>
        <div class="style-container" onclick="get_shot(this)">PULL</div>
        <div class="style-container" onclick="get_shot(this)">LOFTED SHOT</div>
        <div class="style-container" onclick="get_shot(this)">SLOG SWEEP</div>
        <div class="style-container" onclick="get_shot(this)">DRIVE</div>
        <div class="style-container" onclick="get_shot(this)">PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">HELLICOPTER</div>
        <div class="style-container" onclick="get_shot(this)">DEFENCE</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>`;
        let LongOn = `
        <div class="style-container" onclick="get_shot(this)">DEFENCE</div>
        <div class="style-container" onclick="get_shot(this)">PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">STRAIGHT DRIVE</div>
        <div class="style-container" onclick="get_shot(this)">ON DRIVE</div>
        <div class="style-container" onclick="get_shot(this)">LOFTED SHOT</div>
        <div class="style-container" onclick="get_shot(this)">HELLICOPTER</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>`;
        let LongOff = `
        <div class="style-container" onclick="get_shot(this)">DEFENCE</div>
        <div class="style-container" onclick="get_shot(this)">PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">STRAIGHT DRIVE</div>
        <div class="style-container" onclick="get_shot(this)">OFF DRIVE</div>
        <div class="style-container" onclick="get_shot(this)">LOFTED SHOT</div>
        <div class="style-container" onclick="get_shot(this)">HELLICOPTER</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>`;
        let DeepCover = `
        <div class="style-container" onclick="get_shot(this)">DEFENCE</div>
        <div class="style-container" onclick="get_shot(this)">PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">DRIVE</div>
        <div class="style-container" onclick="get_shot(this)">BACK FOOT PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">INSIDE OUT</div>
        <div class="style-container" onclick="get_shot(this)">SWITCH HIT</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>`;
        let DeepPoint = `
        <div class="style-container" onclick="get_shot(this)">DEFENCE</div>
        <div class="style-container" onclick="get_shot(this)">LATE CUT</div>
        <div class="style-container" onclick="get_shot(this)">CUT SHOT</div>
        <div class="style-container" onclick="get_shot(this)">BACK FOOT PUNCH</div>
        <div class="style-container" onclick="get_shot(this)">SQUARE DRIVE</div>
        <div class="style-container" onclick="get_shot(this)">SLASH</div>
        <div class="style-container" onclick="get_shot(this)">REVERSE SWEEP</div>
        <div class="style-container" onclick="get_shot(this)">SWITCH HIT</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>`;
        let ThirdMan = `
        <div class="style-container" onclick="get_shot(this)">OUT SIDE EDGE</div>
        <div class="style-container" onclick="get_shot(this)">TOP EDGE</div>
        <div class="style-container" onclick="get_shot(this)">LATE CUT</div>
        <div class="style-container" onclick="get_shot(this)">REVERSE SWEEP</div>
        <div class="style-container" onclick="get_shot(this)">NONE OF THE ABOVE</div>`;
        let out_data = `
        <div class="style-container" onclick="get_shot(this)">Bowled</div>
        <div class="style-container" onclick="get_shot(this)">Caught</div>
        <div class="style-container" onclick="get_shot(this)">Caught Behind</div>
        <div class="style-container" onclick="get_shot(this)">Caught & Bowled</div>
        <div class="style-container" onclick="get_shot(this)">Run Out (Striker)</div>
        <div class="style-container" onclick="get_shot(this)">Run Out (Non-Striker)</div>
        <div class="style-container" onclick="get_shot(this)">LBW</div>
        <div class="style-container" onclick="get_shot(this)">Stumped</div>
        <div class="style-container" onclick="get_shot(this)">Retired Out</div>
        <div class="style-container" onclick="get_shot(this)">Run out (Mankaded)</div>
        <div class="style-container" onclick="get_shot(this)">Hit Wicket</div>
        <div class="style-container" onclick="get_shot(this)">Retired Out</div>
        <div class="style-container" onclick="get_shot(this)">Hit the Ball Twice</div>
        <div class="style-container" onclick="get_shot(this)">Obstructing the Field (Striker)</div>
        <div class="style-container" onclick="get_shot(this)">Obstructing the Field (Non-Striker)</div>
        <div class="style-container" onclick="get_shot(this)">Retired</div>`;
        let byes = `
        <div class="style-container" onclick="get_shot(this)">1</div>
        <div class="style-container" onclick="get_shot(this)">2</div>
        <div class="style-container" onclick="get_shot(this)">3</div>
        <div class="style-container" onclick="get_shot(this)">4</div>
        <div class="style-container" onclick="get_shot(this)">5</div>
        <div class="style-container" onclick="get_shot(this)">+</div>`;

        let dismissedPlayerid = null;
        let run_per_ball = null;
        let Shot_side = null;
        let Shot_type = null;
        let extras = null;
        let ball_type = null;
        let out_type = null;
        let freehit = false;
        let allowfreehit = '<?php echo $isfreehit_allow ?>';
        let allowwide = '<?php echo $iswide_allow ?>';

        let new_player = '';
        let wicket_by = '';

        //go to prevoius page
        let goBack = () => {
            if(back_decision){
                window.location.href = '../../dashboard.php?update=Live&sport=CRICKET';
            }else{
                window.history.back();
            }
        }

        function verifyPlayers() {
            let strikerEl = document.querySelectorAll('.batsman-type')[0];
            let nonStrikerEl = document.querySelectorAll('.batsman-type')[1];
            const bowlerEl = document.querySelector('.bowler-name');

            let striker = strikerEl.getAttribute('data-striker') || '';
            let non_striker = nonStrikerEl.getAttribute('data-non-striker') || '';
            const bowler = bowlerEl.getAttribute('data-bowler') || '';

             // If striker missing
            if (!striker || strikerEl.innerText.trim() === '') {
                navigator.vibrate([100,50,100,50,100]);
                strikerEl.closest('.batmans').style.borderColor = 'red';
                players_page.classList.add('active');
                players_page.src = `./select-player-from-team.php?for=Striker&team=${bat_team}&striker=&non-striker=${non_striker}`;
                return false;
            }

            // If non-striker missing
            if (!non_striker || nonStrikerEl.innerText.trim() === '') {
                navigator.vibrate([100,50,100,50,100]);
                nonStrikerEl.closest('.batmans').style.borderColor = 'red';
                players_page.classList.add('active');
                players_page.src = `./select-player-from-team.php?for=Non-Striker&team=${bat_team}&striker=${striker}&non-striker=`;
                return false;
            }

            // If bowler missing
            if (!bowler || bowlerEl.innerText.trim() === '') {
                navigator.vibrate([100,50,100,50,100]);
                bowlerEl.closest('.batmans').style.borderColor = 'red';
                players_page.classList.add('active');
                players_page.src = `./select-player-from-team.php?for=Bowler&team=${bowl_team}`;
                return false;
            }

            // if both exist
            return true;
        }

            window.addEventListener('load', () => {
                verifyPlayers();
            });



        window.addEventListener("message", (event) => {
            if (event.data === "closeIframe") {
                players_page.classList.remove('active');  

               goBack();

            }

             if(event.data.type == 'player'){
                if(event.data.person == 'Striker'){
                    new_player = event.data.data;
                    // console.log(striker);
                }else if(event.data.person == 'Non-Striker'){
                    new_player = event.data.data;
                    // console.log(non_striker);
                }else if(event.data.person == 'Bowler'){
                    new_player = event.data.data;
                    // console.log(bowler);
                }else if(event.data.person == 'Fielder'){
                    wicket_by = event.data.data;
                }

                let info = {
                    person : event.data.person,
                    ...(event.data.person == 'Fielder'?{wicket_by : wicket_by} : {data : new_player})
                }

                console.log(info);
                event.data.person == 'Fielder'? get_score_on_wicket() : null;
            }
        });

        //open menubar
        let open_dropdown = () => {
            if (!verifyPlayers()) return;
            dropdown.classList.toggle('active');
            setTimeout(() => {
                opacity.style.display = 'block';
            }, 100);
            
        }

        //prevent from refesh page
        function preventReload(e) {
            e.preventDefault();
            e.returnValue = '';
        }

        window.addEventListener("beforeunload", preventReload);

        // Disable F5 and Ctrl+R keyboard shortcuts
        window.addEventListener("keydown", function (e) {
            if (e.key === "F5" || (e.ctrlKey && e.key.toLowerCase() === "r")) {
                e.preventDefault();
                alert("Reload is disabled for the scorer!");
            }
        });

        //block wide if not allowed
        wide_ball.disabled = !allowwide;

        let commentaryEnabled = true;

        //allow and deny voice commentry
        document.getElementById('commentaryIcon').addEventListener('click', function() {
            commentaryEnabled = commentaryEnabled ? false : true;

            if (commentaryEnabled) {
                console.log("Commentary enabled",commentaryEnabled);
                //  change icon color to active
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mic-on"><path d="M12 1a4 4 0 0 1 4 4v6a4 4 0 0 1-8 0V5a4 4 0 0 1 4-4z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>';
            } else {
                console.log("Commentary disabled",commentaryEnabled);
                stopCommentary();
                //  change icon color to muted
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mic-on"><path d="M12 1a4 4 0 0 1 4 4v6a4 4 0 0 1-8 0V5a4 4 0 0 1 4-4z"/><line x1="4" y1="4" x2="20" y2="20"stroke="#9f9d8b"stroke-width="6"stroke-linecap="round" /><line x1="4" y1="4" x2="20" y2="20"stroke="white"stroke-width="2"stroke-linecap="round" /><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>';
            }
        });



        //close opacity container
        opacity.addEventListener('click', () => {
            if(dropdown.classList.contains('active')){
                opacity.style.display = 'none';
            }
            dropdown.classList.remove('active');
        });

        //open side containre for shots
        let num = document.querySelectorAll('.num');

        num.forEach((el) => {
            el.addEventListener('click', () => {
                if (!verifyPlayers()) return;
                let value = el.innerText.trim();
                let match = value.match(/\b\d+\b/);  // finds first standalone number
                if (match) {
                    console.log(match[0]);  // logs the number part
                    run_per_ball = match[0];
                    setTimeout(() => {
                        shotdialog.style.display = 'flex';
                        opacity.style.display = 'block';
                    }, 300);
                    
                }
            });
        });

        let slice = document.querySelectorAll('.slice');
        let value = '';

        //select shot based on side
        slice.forEach((el) => {
            el.addEventListener('click', () => {
                value = el.querySelector('.side').innerText;
                Shot_side = value;
                value = value.replace(/\s+/g, "");console.log(Shot_side);
                setTimeout(() => {
                    shotdialog.style.display = 'none';
                    opacity.style.display = 'none';
                    document.querySelector('.text').innerHTML = '<p class="out-text">Shot Type</p>'
                    if (value === "DeepFineLeg") {
                        data_container.innerHTML = DeepFineLeg;
                    }else if(value === "DeepSquareLeg"){
                        data_container.innerHTML = DeepSquareLeg;
                    }else if(value === "DeepMidWicket"){
                        data_container.innerHTML = DeepMidWicket;
                    }else if(value === "LongOn"){
                        data_container.innerHTML = LongOn;
                    }else if(value === "LongOff"){
                        data_container.innerHTML = LongOff;
                    }else if(value === "DeepCover"){
                        data_container.innerHTML = DeepCover;
                    }else if(value === "DeepPoint"){
                        data_container.innerHTML = DeepPoint;
                    }else if(value === "ThirdMan"){
                        data_container.innerHTML = ThirdMan;
                    }else{
                        data_container.innerHTML = '';
                    }
                    shot.showModal();
                }, 300);
            });
        });

        //open dialog for out
        out.addEventListener('click', () => {
            if (!verifyPlayers()) return;
            document.querySelector('.text').innerHTML = '<p class="out-text">Select out type</p>'
            data_container.innerHTML = out_data;
            shot.showModal();
        });

        //open dialog box for leg bye & bye
        bye.addEventListener('click', () => {
            if (!verifyPlayers()) return;
            document.querySelector('.text').innerHTML = '<p class="out-text">Bye Runs</p>'
            data_container.innerHTML = byes;
            shot.showModal();
        });

        lb.addEventListener('click', () => {
            if (!verifyPlayers()) return;
            document.querySelector('.text').innerHTML = '<p class="out-text">Leg Bye Runs</p>'
            data_container.innerHTML = byes;
            shot.showModal();
        });

        //open dialog for No_ball
        NB.addEventListener('click', () => {
            if (!verifyPlayers()) return;
            let data = '';
            document.querySelector('.text').innerHTML = '<p class="out-text">No ball</p><p>(NB=1)</p>'
            for(let i = 0;i < 8; i++){
                if(i == 7){
                    data = data + `<div class="style-container" onclick="get_noball(this)">+</div>`;
                }else{
                    data = data + `<div class="style-container" onclick="get_noball(this)">NB + ${i}</div>`;
                }
            }
            data = data + `<div class='style-container noballtypes' style="display:none">
                                <div class="type-container">
                                    <div class="style-container2" onclick="get_noball_type(this)">Hit</div>
                                    <div class="style-container2" onclick="get_noball_type(this)">Bye</div>
                                    <div class="style-container2" onclick="get_noball_type(this)">Leg Bye</div>
                                    <div class="style-container2" onclick="get_noball_type(this)">Wicket</div>
                                </div>
                            </div>`;
            data_container.innerHTML = data;
            shot.showModal();
        });

        //open dialog for Wide_ball
        wide_ball.addEventListener('click', () => {
            if (!verifyPlayers()) return;
            let data = '';
            document.querySelector('.text').innerHTML = '<p class="out-text">wide ball</p><p>(WD=1)</p>'
            for(let i = 0;i < 8; i++){
                if(i == 7){
                    data = data + `<div class="style-container" onclick="get_shot(this)">+</div>`;
                }else{
                    data = data + `<div class="style-container" onclick="get_shot(this)">WD + ${i}</div>`;
                }
            }
            data_container.innerHTML = data;
            shot.showModal();
        });

        //
        let get_score_on_wicket = (el) => {
            let data = '';
            document.querySelector('.text').innerHTML = '<p class="out-text">Runs</p>'
            for(let i = 0;i < 8; i++){
                if(i == 7){
                    data = data + `<div class="style-container" onclick="get_score(this)">+</div>`;
                }else{
                    data = data + `<div class="style-container" onclick="get_score(this)">${i}</div>`;
                }
            }
            data_container.innerHTML = data;
            shot.showModal();
        }

        //shot selection
        let selectedShot = '';

        let shotContainers = document.querySelectorAll('.style-container');
        let noballtype = document.querySelectorAll('.style-container2');
        let balltype = '';
        let no_balltype = '';
        let no_ball = '';

        //get score
        let get_score = (el) => {
            balltype = document.querySelector('#selectshot .text').innerText;

            // Remove 'active' class from all shot containers
            document.querySelectorAll('.style-container').forEach(c => c.classList.remove('active'));

            // Add 'active' class to the clicked container
            el.classList.add('active');

            // Set selected shot text
            run_per_ball = el.textContent.trim();

            if (run_per_ball === '+') {
                let customRun = prompt("Enter number of runs:");
                run_per_ball = customRun || 0;
            }

            console.log('Runs:', run_per_ball);
            console.log('out type :',selectedShot)
            if(freehit && ["Bowled","Caught","Caught Behind","Caught & Bowled","LBW","Stumped","Hit Wicket"].includes(selectedShot)){
                display_content();
            }else{
                if(["Caught","Caught Behind","Run Out (Striker)","Run Out (Non-Striker)","Obstructing the Field (Striker)","Obstructing the Field (Non-Striker)","Run out (Mankaded)"].includes(selectedShot)){
                    setTimeout(() => {
                            shotdialog.style.display = 'flex';
                            opacity.style.display = 'block';
                        }, 300);
                }else{
                    
                            
                    display_content();
                }
            }
            
            setTimeout(() => {
                shot.close();
            }, 300);
        }

        //get no ball
        let get_noball = (el) => {
            balltype = document.querySelector('#selectshot .text').innerText;

            // Remove 'active' class from all shot containers
            document.querySelectorAll('.style-container').forEach(c => c.classList.remove('active'));

            // Add 'active' class to the clicked container
            el.classList.add('active');

            // Set selected shot text
            selectedShot = el.textContent.trim();

            console.log('Selected shot:', selectedShot);

            if(selectedShot != null){
                document.querySelector('.noballtypes').style.display = 'block';
            }
            

            if(selectedShot != '' && no_balltype != ''){
                    setTimeout(() => {
                        shot.close();
                    }, 300);
                    
                    display_content();
                }

        }

         let get_noball_type = (el) => {
                balltype = document.querySelector('#selectshot .text').innerText;

                // Remove 'active' class from all shot containers
                document.querySelectorAll('.style-container2').forEach(c => c.classList.remove('active'));

                // Add 'active' class to the clicked container
                el.classList.add('active');

                // Set selected shot text
                no_balltype = el.textContent.trim();

                console.log('Selected shot:', no_balltype);
                ball_type = 'No Ball';
                type(selectedShot);

                if(selectedShot != '' && no_balltype != ''){
                    

                    if(no_balltype == 'Wicket'){
                        let data = '';
                        document.querySelector('.text').innerHTML = '<p class="out-text">Select out type</p>'
                            
                        data = data + `<div class="style-container" onclick="get_shot_noball(this)">Run Out (Striker)</div>
                                        <div class="style-container" onclick="get_shot_noball(this)">Run Out (Non-Striker)</div>
                                        <div class="style-container" onclick="get_shot_noball(this)">Run out (Mankaded)</div>
                                        <div class="style-container" onclick="get_shot_noball(this)">Obstructing the Field (Striker)</div>
                                        <div class="style-container" onclick="get_shot_noball(this)">Obstructing the Field (Non-Striker)</div>
                                        <div class="style-container" onclick="get_shot_noball(this)">Hit the Ball Twice</div>
                                        <div class="style-container" onclick="get_shot_noball(this)">Retired</div>
                                        <div class="style-container" onclick="get_shot_noball(this)">Retired Out</div>`;
                                                      
                        data_container.innerHTML = data;
                        shot.showModal();
                    }else{
                        setTimeout(() => {
                            shot.close();
                        }, 300);
                        display_content();
                    }
                    
                }

                
        }

        let get_shot_noball = (el) => {
            balltype = document.querySelector('#selectshot .text').innerText;

                // Remove active class from all
                shotContainers.forEach(c => c.classList.remove('active'));

                // Add active class to clicked container
                el.classList.add('active');

                // Get the text (like shot name)
                selectedShot = el.textContent.trim();
                out_type = selectedShot;
                get_score_on_wicket();
                console.log('Ball Type : ',ball_type)
        }

        let type = (selectedShot) => {
            let run = selectedShot.match(/\d+/);  
            if (run) {
                run = parseInt(run[0]);
                console.log('Run:', run);
                if(ball_type == 'No Ball'){
                    if (no_balltype === 'Hit') {
                        extras = 1;
                        run_per_ball = run;
                    } else if (no_balltype === 'Bye' || no_balltype === 'Leg Bye') {
                        extras = run + 1;
                    }else if(no_balltype === 'Wicket'){
                        out_type = "Run out";
                        extras = 1;
                        run_per_ball = run;
                    }
                }else if(ball_type == 'Wide Ball'){
                    extras = run+1;
                }else{
                    extras = run;
                }      
            }
        }

        // Get all style-containers
        let get_shot = (el) => {
                balltype = document.querySelector('#selectshot .text').innerText;

                // Remove active class from all
                shotContainers.forEach(c => c.classList.remove('active'));

                // Add active class to clicked container
                el.classList.add('active');

                // Get the text (like shot name)
                selectedShot = el.textContent.trim();
                
                if(balltype.includes('WD') && allowwide){

                    console.log('Wide BAll')
                    ball_type = 'Wide Ball';

                    if (selectedShot === '+') {
                        let customRun = prompt("Enter number of extra runs:");
                        selectedShot = customRun || '0';
                    }

                    type(selectedShot);

                    setTimeout(() => {
                        shot.close();
                    }, 300);

                    display_content();
                    

                }else if (balltype.includes('NB')) {
                    console.log('No Ball');
                    ball_type = 'No Ball';

                    if (selectedShot === '+') {
                        let customRun = prompt("Enter number of extra runs:");
                        selectedShot = customRun || '0';
                    }

                    type(selectedShot);

                    setTimeout(() => {
                        shot.close();
                    }, 300);

                    display_content();
                }
                else if (balltype.includes('Leg Bye')) {
                    console.log('Leg Bye');
                    ball_type = 'Leg Bye';

                    if (selectedShot === '+') {
                        let customRun = prompt("Enter number of runs:");
                        selectedShot = customRun || '0';
                    }

                    type(selectedShot);

                    setTimeout(() => {
                        shot.close();
                    }, 300);

                    display_content();
                }
                else if (balltype.includes('Bye')) {
                    console.log('Bye');
                    ball_type = 'Bye';

                    if (selectedShot === '+') {
                        let customRun = prompt("Enter number of runs:");
                        selectedShot = customRun || '0';
                    }
                    type(selectedShot);

                    setTimeout(() => {
                        shot.close();
                    }, 300);

                    display_content();

                    
                }else if(balltype.includes('Shot')){

                    console.log(selectedShot);
                    Shot_type = selectedShot;

                    if(!ball_type?.startsWith('No Ball')){
                        ball_type = 'Legal Delivery';
                    }
                    

                    setTimeout(() => {
                        shot.close();
                    }, 300);

                    display_content();

                }else if(balltype.includes('out')){

                    console.log('Out : ',selectedShot);
                    out_type = selectedShot;
                    ball_type = 'Wicket';

                    

                    if([ "Run Out (Striker)","Run Out (Non-Striker)","Run out (Mankaded)","Obstructing the Field (Striker)","Obstructing the Field (Non-Striker)","Retired","Retired Out"].includes(selectedShot)){
                        
                        get_score_on_wicket();
                       
                    }else if(["Caught","Caught Behind"].includes(selectedShot)){
                        
                        if(freehit){
                            setTimeout(() => {
                                shot.close();
                            }, 300);
                            display_content();
                        }else{
                            players_page.classList.add('active');
                            players_page.src = `./select-player-from-team.php?for=Fielder&team=${bowl_team}`;
                            setTimeout(() => {
                                shot.close();
                            }, 100);
                        }
                        
                    }else{

                        setTimeout(() => {
                            shot.close();
                        }, 300);
                        display_content();
                    }
                }

            };

            //open dialog for undo
            undo.addEventListener('click',()=>{
                if (!verifyPlayers()) return;
                undo_container.showModal();
                undo_container.classList.add('shake');
                navigator.vibrate(100);
            });

            //close dialog of undo
            document.querySelector('.undo-cancel').addEventListener('click',()=>{
                undo_container.close();
                undo_container.classList.remove('shake');
            });

            const commentaries = {
                noRun: [
                    "{striker} plays it safe, no run.",
                    "Dot ball — {striker} stays put.",
                    "{striker} taps it straight to the fielder.",
                    "No run taken by {striker}."
                ],
                four: [
                    "That's a cracking boundary from {striker}!",
                    "Lovely shot by {striker}, four runs!",
                    "{striker} times it beautifully to the fence.",
                    "Another delightful FOUR by {striker}!"
                ],
                six: [
                    "It's a massive hit for SIX by {striker}!",
                    "{striker} sends that one out of here!",
                    "Over the ropes for a huge six from {striker}!",
                    "Powerful shot — gone for six! {striker} is on fire!"
                ],
                miscRuns: [
                    "{striker} takes {runs} run{plural}.",
                    "Good running between the wickets — {striker} picks up {runs}.",
                    "{runs} run{plural} added by {striker}."
                ],
                out: [
                    "OUT! {outType} sends {striker} back to the pavilion.",
                    "Wicket! {striker} departs via {outType}.",
                    "The umpire's finger is up — {striker} is gone, {outType}.",
                    "That's the end for {striker}, {outType}."
                ],
                bye: [
                    "{runs} bye{plural} taken. {striker} stays at the striker's end.",
                    "The ball beats everyone, {runs} bye{plural}.",
                    "Keeper lets it through — {runs} bye{plural} added."
                ],
                legBye: [
                    "{runs} leg bye{plural} sneaked. {striker} looks for another.",
                    "Off the pads of {striker}, {runs} leg bye{plural}.",
                    "Leg byes taken — {runs} run{plural} added."
                ],
                noBall: [
                    "No ball! Overstepped there. Free Hit coming up for {striker}.",
                    "Oh dear, that's a no ball to {striker}.",
                    "Bowler overstepped — no ball to {striker}."
                ],
                noBallFreeHit: [
                    "Another no ball on the Free Hit! {striker} gets another chance.",
                    "No ball again! Free Hit continues for {striker}.",
                    "That’s poor — no ball on a Free Hit. {striker} will be loving this."
                ],
                wide: [
                    "Wide ball, extra run given. {striker} leaves it alone.",
                    "Too wide for {striker}.",
                    "That’s a wide down the leg side, {striker} watches it go."
                ],
                illegalWicketOnFreeHit: [
                    "That's a wicket on a Free Hit — only dismissable this way! {striker} is gone.",
                    "{striker} is out on a Free Hit via {outType}, rare moment.",
                    "He’s out on a Free Hit! Only possible through {outType}. {striker} walks off."
                ],
                wicketOnNoBall: [
                    "{striker} has been dismissed on a no ball — {outType} is still allowed!",
                    "Unbelievable scenes! A no ball, but {striker} is out via {outType}.",
                    "{outType} dismissal for {striker} on a no ball. That’s within the laws!",
                    "Even with the no ball call, {striker} walks back to the pavilion — {outType} it is.",
                    "A rare moment! No ball signalled, but {striker} is out through {outType}.",
                    "Drama in the middle! {striker} falls to a {outType}, despite the no ball."
                ]
            };

            function getRandomCommentary(category, data = {}) {
                let phrases = commentaries[category];
                let randomPhrase = phrases[Math.floor(Math.random() * phrases.length)];

                // Replace placeholders
                return randomPhrase
                    .replace('{striker}', data.striker)
                    .replace('{runs}', data.runs)
                    .replace('{plural}', data.runs > 1 ? 's' : '')
                    .replace('{outType}', data.outType ? data.outType.replace(/\s*\(.*?\)/, '') : null);
            }

            function handleWicket() {
                
                // Run Out or Obstruction — Non-Striker out or Mankaded
                if (
                    out_type.includes("Non-Striker") ||
                    out_type == "Run out (Mankaded)"
                ) {
                    console.log(`nonStriker is out — ${out_type}`); 
                }else // Run Out or Obstruction — Striker out
                if (out_type.includes("Striker")) {
                    console.log(`striker is out — ${out_type}`);
                }
                // All other types — always Striker out
                else if(ball_type === 'No Ball' && out_type.startsWith("Run out")){
                    console.log('runs :',run_per_ball)
                    
                        
                }else {
                    console.log(`striker is out — ${out_type}`);
                }
            }

            function generateCommentary(run, outType, ballType, freeHit ,extra, striker,non_striker) {
                let commentary = '';

                if (outType !== null) {

                    let dismissedPlayer = (["(Non-Striker)", "(Mankaded)"].some(sub => outType.includes(sub))) ? non_striker : striker;
                    
                    let batsmen = document.querySelectorAll('.batsman-type');
                    dismissedPlayerid = (
                    ["(Non-Striker)", "(Mankaded)"].some(sub => outType.includes(sub))
                    )
                    ? batsmen[1]?.getAttribute('data-non-striker')
                    : batsmen[0]?.getAttribute('data-striker');
                    console.log(dismissedPlayerid)
                    console.log('No ball test : ',ball_type)
                    
                    if (ball_type === 'No Ball') {

                        handleWicket();
                        commentary = getRandomCommentary('wicketOnNoBall', { outType, striker: dismissedPlayer });

                    } else if (freeHit && (
                        outType.startsWith("Run") || 
                        outType.startsWith("Obstructing") || 
                        outType === "Hit the Ball Twice" || 
                        outType.startsWith("Retired"))
                    ) {
                        handleWicket();
                        commentary = getRandomCommentary('illegalWicketOnFreeHit', { outType, striker: dismissedPlayer });

                    } else if (freeHit) {
                        commentary = "Not out — it's a free hit!";

                    } else {
                        handleWicket();
                        commentary = getRandomCommentary('out', { outType, striker: dismissedPlayer });
                    }
                }
                else if (balltype.includes('Leg Bye')) {
                    commentary = getRandomCommentary('legBye', { runs: extra,striker });
                }
                else if (balltype.includes('Bye')) {
                    commentary = getRandomCommentary('bye', { runs: extra,striker });
                }
                else if (balltype.includes('NB')) {
                    commentary = freeHit ? getRandomCommentary('noBallFreeHit',{striker}) : getRandomCommentary('noBall',{striker});
                }
                else if (balltype.includes('WD')) {
                    commentary = getRandomCommentary('wide',{striker});
                }
                else if (run == 0) {
                    commentary = getRandomCommentary('noRun',{striker});
                }
                else if (run == 4) {
                    commentary = getRandomCommentary('four',{striker});
                }
                else if (run == 6) {
                    commentary = getRandomCommentary('six',{striker});
                }
                else {
                    commentary = getRandomCommentary('miscRuns', { runs: run,striker });
                }

                speakCommentary(commentary)
                console.log(commentary);
                return commentary;
            }

            let voices = [];

            // Load voices when available
            function loadVoices() {
                voices = window.speechSynthesis.getVoices();
            }

            // Fire voice loading event
            window.speechSynthesis.onvoiceschanged = loadVoices;

            function speakCommentary(text) {
                // Cancel any currently queued speech
                if(commentaryEnabled){
                    // window.speechSynthesis.cancel(); // optional if overlapping voices happen

                    let utter = new SpeechSynthesisUtterance(text);

                    if (!voices.length) {
                        voices = window.speechSynthesis.getVoices();
                    }

                    // Ensure voices are loaded
                    if (voices.length > 0) {
                        utter.voice = voices.find(v => v.lang.includes("en")) || voices[1];
                    }

                    // Tune commentary feel
                    utter.pitch = 0.95;
                    utter.rate = 1.2;
                    utter.volume = 1;

                    // Speak it
                    setTimeout(() => {
                        window.speechSynthesis.speak(utter);
                    }, 000);
                    
                }
            }

            function stopCommentary() {
                window.speechSynthesis.cancel();
            }
            let ball_data = '';
            //update score
            let display_content = () => {
                let commentary = generateCommentary(run_per_ball, out_type, ball_type, freehit, extras, document.querySelectorAll('.batsman-type')[0].innerText,document.querySelectorAll('.batsman-type')[1].innerText);
                let scoreText = document.querySelector('.score').childNodes[0].nodeValue.trim().split('/');

                if (ball_type.startsWith('No Ball') && allowfreehit) {
                    freehit = true;
                } else if (freehit && ball_type.startsWith('No Ball')) {
                    console.log('Wicket free hit');
                    freehit = true;
                } else if (ball_type == 'Wide Ball' && freehit) {
                    freehit = true;
                } else if (freehit && (ball_type == 'Leg Bye' || ball_type == 'Bye')) {
                    freehit = false;
                } else if (freehit) {
                    ball_type = 'Free Hit';
                    freehit = false;
                }
                free = ball_type;

                
                let strikerName = document.querySelector('.batsman-type').getAttribute('data-striker');
                ball_data = {
                    'Run' : run_per_ball,
                    'Shot Type' : Shot_type,
                    'Shot Side' : Shot_side,
                    'Wicket Type': out_type,
                    'Extra' : extras,
                    ...(ball_type == 'No Ball' ? {'Ball Type': `${ball_type}-${no_balltype}`}:{'Ball Type': ball_type}),
                    ...(freehit? { 'Freehit': freehit } : {}),
                    'Bowler': document.querySelector('.bowler-name').innerText,
                    'Wicket By ': wicket_by,
                    ...(ball_type == 'Wicket' || no_balltype == 'Wicket' ? { 'Out Player': dismissedPlayerid ,'New Player':new_player} : { 'Striker': strikerName }),
                    'TotalScore': scoreText[0],
                    'Wickets': scoreText[1],
                    'Commentary': commentary
                }

                update_score();
                
            }

            let update_score = () => {
                console.log(ball_data);

                //Bypass reload
                // window.removeEventListener("beforeunload", preventReload);
                // location.reload();

                run_per_ball =null;
                Shot_type = null;
                Shot_side = null;
                out_type = null;
                extras = null;
                ball_type = null;
                dismissedPlayer = '';
                no_balltype = '';
                wicket_by = '';
            }
    </script>
</body>
</html>