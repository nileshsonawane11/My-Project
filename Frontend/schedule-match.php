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

    $team1 = $_GET['team1'] ?? '';
    $team2 = $_GET['team2'] ?? '';
    $game = $_GET['sport'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>Schedule Match</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
            scrollbar-width: none;
        }
        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(90deg, var(--primary-light), var(--primary-dark));
        }
        body{
            height: max-content;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            flex-direction: column;
        }
        .part{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 70px;
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
            
        }
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
            width: 100%;
            gap: 80px
        }
        .txt{
            line-height: 20px;
        }
        
        .input-fields{
            width: 100%;
            position: relative;
        }
        .input-fields label{
            position: absolute;
            bottom: 22px;
            left: 14px;
            text-align: center;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }
        .input-fields input:valid ~ label,
        .input-fields input:focus ~ label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: black;
        }
        .container3{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 40px;
        }
        .logo-img{
            height: 110px;
            width: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img-container{
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            position: relative;
        }
        .image{
            height: 100%;
            width: 100%;
            border: solid 1px black;
            border-radius: 50%;
        }
        .select-img{
            position: absolute;
            bottom: 0;
            right: 0;
            background: white;
            height: 40px;
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor : pointer;
        }
        .info{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 25px
        }
        .name{
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            gap: 20px;
        }
        .history-staff{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
        }
        .team{
            width: 100%;
            display: flex;
            flex-direction: row;
            align-content: center;
            justify-content: space-evenly;
            align-items: center;
            gap: 10px;
            color: black;
            padding: 10px;
            border-radius: 20px;
            text-wrap: auto;
            background: #eeeeeeab;
            cursor: pointer;
            box-shadow: 1px 2px 3px rgba(0, 0, 0, 0.5);
            flex-wrap: wrap;
        }
        .other-info{
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 20px;
            margin-left: 15px;
            cursor: pointer;
        }
        .team-info{
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            height: 100%;
            cursor: pointer;
            justify-content: center;
        }
        .team-info label{
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
            gap: 5px;
        }
        .logo{
            min-height: 75px;
            min-width: 75px;
            background: #e8e8e8;
            margin: 10px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .part.or{
            text-align: center;
        }
        .add-btn{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .schedule{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            width: 100%;
            gap: 15px;
        }
        .input-fields #date,
        .input-fields #time {
            position: absolute;
            bottom: 22px;
            left: 14px;
            background: white;
            width: 100px;
            height: 20px;
            text-align: left;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }
        .info-input{
            gap: 40px;
        }
        .prize-container{
            display: flex;
            gap: 10px;
            width: 100%;
            justify-content: space-around;
            align-items: center;
            flex-direction: row;
            
        }
        .option,.option1 {
            padding: 7px 16px;
            border-radius: 13px;
            cursor: pointer;
            user-select: none;
            background:#D9D9D9;
            transition:all 0.2s ease-in-out;
        }
        .option.active,
        .option1.active {
            background: #ff47000a;
            border: 1px solid #ff5e00;
        }
        .input-fields textarea:valid + label,
        .input-fields textarea:focus + label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: black;
        }
        textarea{
            overflow: hidden;
            resize: none;
            min-height: 30px;
            padding: 8px;
            font-size: 14px;
            width: 100%;
            border: none;
            border-bottom: solid 1px black;
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 15px;
            width: 100%;
            outline: none;
            height: 45px;
            background: white;
        }
        .container4{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 20px;
        }
        .text{
            height: 50px;
            width: 130px;
            border-radius: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            background:var(--background);
            color:white;
        }
        .img-container{
            height: 100px;
            width: 100px;
            background: #73737330;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .img-container img{
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .vs{
            font-size: 25px;
            font-weight: bold;
            transform: translate(0,20rem);
            transition: all 0.5s ease-in-out;
            opacity: 0;
        }
        .teams{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }
        .officials{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
        .officials-container{
            width: 100%;
            gap: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .person{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .right-side{
            transform: translateX(-200%) translateY(0);
            transition: all 0.5s ease-in-out;
        }
        .left-side{
            transform: translateX(200%) translateY(0);
            transition: all 0.5s ease-in-out;
        }
        .show{
            opacity: 1;
            transform: translate(0,0);
        }
        .knife.show1{
            opacity: 1;
            transform: scale(1);
        }
        .knife{
            transform: scale(5);
            z-index: 1;
            opacity: 0;
            transition: all 0.5s ease-in-out;
            transition-delay: 0.6s;
        }
        #s-type{
            cursor: pointer;
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

        @media (min-width:601px) {
             .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                width: 90%;
                max-width: 100%;
                min-height: 480px;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                padding: 40px;
            }
            .container2{
                gap: 100px;
                width: 70%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="tel"],[type="datetime"],[type="time"],[type="date"],select{
                border: none;
                border-bottom: solid 1px black;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 16px;
                width: 100%;
                outline: none;
                height: 45px;
                background: white;
            }
            .staff-container{
                width: 100%;
                display: grid;
                justify-items: center;
                align-items: center;
                gap: 30px;
                justify-content: space-around;
                grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            }
            .add-btn button{
                background:linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                color: #fff;
                font-size: 12px;
                padding: 10px 45px;
                border: 1px solid transparent;
                border-radius: 8px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                cursor: pointer;
                height: 40px;
                width: 300px;
            }
            .teams{
                justify-content: space-evenly;
            }
            .officials{
                justify-content: space-evenly;
            }
        }

        @media(max-width: 601px) {
            .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                width: 768px;
                z-index: 0;
                max-width: 100%;
                min-height: 480px;
                padding: 40px 40px;
                align-items: flex-start;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px
            }
            
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="tel"],[type="datetime"],[type="time"],[type="date"],select{
                border: none;
                border-bottom: solid 1px black;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 15px;
                width: 100%;
                outline: none;
                height: 45px;
                background: white;
            }
            .staff-container{
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 20px;
            }
            .team{
                height: 90px;
                width: 100%;
            }
            .add-btn button{
                background:linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                color: #fff;
                font-size: 12px;
                padding: 10px 45px;
                border: 1px solid transparent;
                border-radius: 8px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                cursor: pointer;
                height: 40px;
                width: 300px;
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
            <div></div>
        </div>
        <div class="container2">
            <div class="part">
                <div class="txt">
                    <label for="">Schedule Match</label>
                    <h4>SELECT PLAYING TEAMS</h4>
                </div>
                <div class="container3">
                    <div class="teams">
                        <?php
                        if(isset($team1)){
                            $sql = "SELECT * FROM teams WHERE t_id = '$team1'";
                            $query = mysqli_query($conn,$sql);
                            $row = mysqli_fetch_assoc($query);
                        ?>
                        <div class="container4 right-side" id="team1">
                            <div class="img-container"><?php if(!($row['t_logo'])){ echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' alt=''>"; }else{
                                echo "<img src='../assets/images/teams/{$row['t_logo']}' alt=''>";
                            } ?>
                            </div>
                            <div class="team-name"><?php if(($row)){ echo $row['t_name'];} ?></div>
                            <div class="text"><label for="">Select Team A</label></div>
                        </div>
                        <?php
                            }
                        ?>

                        <div class="container4">
                            <svg class="knife" width="46" height="42" viewBox="0 0 46 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M44.762 37.3485C43.709 36.4703 43.3363 36.2543 43.1188 36.4155C43.0642 36.1251 42.9159 35.8605 42.6965 35.6625C42.3733 35.37 41.933 35.241 41.4733 35.2553C41.469 35.0686 41.427 34.8848 41.3496 34.7149C41.2723 34.545 41.1612 34.3925 41.0233 34.2668C40.6993 33.9735 40.2583 33.8445 39.7985 33.8588C39.7948 33.6721 39.753 33.4882 39.6758 33.3182C39.5986 33.1482 39.4874 32.9958 39.3493 32.8703C39.026 32.577 38.585 32.448 38.126 32.4615C38.1222 32.2748 38.0803 32.0908 38.003 31.9208C37.9256 31.7509 37.8143 31.5985 37.676 31.473C37.3003 31.1318 36.767 31.0118 36.227 31.086C37.4743 29.0625 35.8805 26.772 34.634 25.7303C33.8518 25.0778 34.2253 25.9425 32.8423 28.0575C30.9908 26.5063 29.1797 24.9075 27.4108 23.2628C30.5953 21.0608 34.103 18.4673 37.6303 15.522C46.427 8.17725 45.4693 0 45.4693 0C45.4693 0 44.4538 1.038 42.7078 2.2515C40.2373 3.969 36.3013 6.0465 31.7 6.0465L31.4953 6.045C31.4953 6.045 31.259 10.149 22.9985 18.8918C14.7395 10.1483 14.5048 6.045 14.5048 6.045L14.3 6.0465C9.69877 6.0465 5.76277 3.969 3.29227 2.2515C1.54627 1.038 0.530769 0 0.530769 0C0.530769 0 -0.426981 8.17725 8.36827 15.5213C11.8888 18.4613 15.4003 21.0638 18.5803 23.2628C16.814 24.9077 15.0052 26.5062 13.1555 28.0568C11.7733 25.9425 12.1468 25.077 11.3645 25.7295C10.118 26.7713 8.52427 29.061 9.77152 31.0853C9.23227 31.0103 8.69827 31.131 8.32252 31.4723C8.18421 31.5977 8.07296 31.7501 7.99558 31.9201C7.9182 32.0901 7.87633 32.274 7.87252 32.4608C7.41277 32.4465 6.97252 32.5763 6.64927 32.8695C6.35602 33.135 6.21427 33.486 6.20002 33.858C5.74027 33.843 5.29927 33.9728 4.97602 34.266C4.68277 34.5315 4.54102 34.8825 4.52677 35.2545C4.06702 35.2403 3.62602 35.3693 3.30352 35.6618C3.08529 35.8606 2.9374 36.1248 2.88202 36.4148C2.66377 36.2535 2.29102 36.4695 1.23802 37.3478C-0.59048 38.8763 1.41502 38.2118 2.55052 39.558C4.22377 41.5425 8.59402 43.4333 12.1783 40.4393C15.1273 37.9785 16.4293 34.0065 15.1055 30.9383C16.1165 30.3878 19.0625 28.74 23 26.2095C26.942 28.7438 29.8835 30.3863 30.8938 30.939C29.57 34.0073 30.872 37.9793 33.821 40.44C37.4053 43.434 41.7748 41.544 43.4488 39.5588C44.5843 38.2125 46.5905 38.877 44.762 37.3485ZM4.37977 38.6153C5.04727 38.838 5.76652 38.7533 6.23977 38.3235C6.53152 38.0588 6.67327 37.7078 6.68752 37.3358C7.14727 37.3507 7.58752 37.221 7.91152 36.9285C8.20402 36.663 8.34577 36.312 8.36002 35.9393C8.81977 35.9543 9.26077 35.8238 9.58552 35.5313C9.87727 35.2665 10.019 34.9148 10.0333 34.5428C10.493 34.5578 10.934 34.4288 11.258 34.1355C11.5475 33.8715 11.69 33.5235 11.7065 33.1538C14.4275 36.951 8.36527 42.024 4.37977 38.6153ZM32.3968 7.1565C35.7193 7.0155 38.6735 5.89575 40.9985 4.62975C40.1008 7.43625 38.2003 10.851 34.2823 14.1203C31.1525 16.734 28.0378 19.0673 25.1548 21.0945C24.6883 20.6344 24.2268 20.1694 23.7703 19.6995C30.0095 13.0875 31.8598 8.958 32.3968 7.1565ZM13.6033 7.1565C14.3165 9.54675 17.3113 16.0223 29.8588 26.985C25.55 24.384 18.6448 19.905 11.7178 14.121C7.80052 10.8518 5.90002 7.437 5.00152 4.6305C7.32652 5.89575 10.2815 7.0155 13.6033 7.1565ZM16.1413 26.9843C17.2895 25.9814 18.4197 24.9579 19.5313 23.9145C19.7748 24.0805 20.0163 24.2438 20.2558 24.4043C18.898 25.286 17.5263 26.1461 16.1413 26.9843ZM34.2935 33.1538C34.31 33.5228 34.4525 33.8715 34.742 34.1355C35.0668 34.4288 35.5078 34.5578 35.9675 34.5428C35.9818 34.9148 36.1235 35.2673 36.4153 35.5313C36.74 35.8238 37.181 35.9535 37.6408 35.9393C37.6558 36.312 37.7968 36.6638 38.0893 36.9285C38.4133 37.221 38.8543 37.3507 39.314 37.3358C39.3283 37.7078 39.4693 38.0595 39.7618 38.3235C40.2358 38.7525 40.955 38.838 41.6218 38.6153C37.6348 42.024 31.5725 36.951 34.2935 33.1538Z" fill="black"/>
                            </svg>
                            <label for="" class="vs">VS</label>
                        </div>

                        <?php
                            if(isset($team2)){
                                $sql = "SELECT * FROM teams WHERE t_id = '$team2'";
                                $query = mysqli_query($conn,$sql);
                                $row = mysqli_fetch_assoc($query);
                        ?>
                        <div class="container4 left-side" id="team2">
                            <div class="img-container"><?php if(!($row['t_logo'])){ echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' alt=''>"; }else{
                                echo "<img src='../assets/images/teams/{$row['t_logo']}' alt=''>";
                            } ?>
                            </div>
                            <div class="team-name"><?php if(($row)){ echo $row['t_name'];} ?></div>
                            <div class="text"><label for="">Select Team B</label></div>
                        </div>
                        <?php
                            }
                        ?>
                    </div>

                    <div class="info">
                        <h4>Match Details</h4>
                        <div class="input-fields"><input type="text" name="" id="city" class="data" required><label for="city">City</label></div>
                        <div class="input-fields"><input type="text" name="" id="ground" class="data" required><label for="ground">Ground</label></div>
                        <div class="schedule">
                            <div class="input-fields"><input type="date" id="dateInput" placeholder="Select Date" required><label for="dateInput" id="date">Date</label></div>
                            <div class="input-fields event-time"><input type="time" id="timeInput" placeholder="Select Time" required><label for="timeInput" id="time">Time</label></div>
                        </div>
                        
                         <div class="info">
                            <h4>Overs</h4>
                            <select name="" id="over">
                                <option value='Null' disabled selected>Select Overs</option>
                                <?php
                                    for ($i = 1; $i <= 20; $i++) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                ?>
                                <option value='50'>50</option>
                                <option value='-'>Unlimited</option>
                            </select>
                        </div>

                        <div id="error-empty" class="error"></div>
                        <div id="error-datetime" class="error"></div>
                    </div>
                    
                    <div class="info">
                        <?php
                            $sportsTypes = [
                                "CRICKET" => "Team Sport",
                                "VOLLEYBALL" => "Team Sport",
                                "KABADDI" => "Team Sport",
                                "KHO-KHO" => "Team Sport",
                                "FOOTBALL" => "Team Sport",
                                "TENNIS" => "Individual Sport",
                                "TABLE-TENNIS" => "Individual Sport",
                                "CHESS" => "Mind Sport",
                                "WEIGHT-LIFTING" => "Individual Sport",
                                "BASKETBALL" => "Team Sport"
                            ];
                        ?>
                    <div class="info">
                        <h4>Sports Type</h4>
                        <select name="" id="s-type" disabled>
                            <?php
                                echo "<option value='{$sportsTypes[$game]}' disabled selected>{$sportsTypes[$game]}</option>";
                            ?>
                        </select>
                    </div>

                    <div class="officials-container">
                        <h4>Match Officials</h4>
                        <div class="officials">
                            <div class="person">
                                <div class="logo umpire" onclick="select_person(this)">
                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5 12.5003C15.9071 12.5003 18.6667 9.74074 18.6667 6.33366C18.6667 2.92658 15.9071 0.166992 12.5 0.166992C9.09294 0.166992 6.33335 2.92658 6.33335 6.33366C6.33335 9.74074 9.09294 12.5003 12.5 12.5003ZM12.5 15.5837C8.38377 15.5837 0.166687 17.6495 0.166687 21.7503V24.8337H24.8334V21.7503C24.8334 17.6495 16.6163 15.5837 12.5 15.5837Z" fill="black"/>
                                    </svg>
                                </div>
                                <label for="">Umpires</label>
                            </div>
                            <div class="person">
                                <div class="logo scorer" onclick="select_person(this)">
                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5 12.5003C15.9071 12.5003 18.6667 9.74074 18.6667 6.33366C18.6667 2.92658 15.9071 0.166992 12.5 0.166992C9.09294 0.166992 6.33335 2.92658 6.33335 6.33366C6.33335 9.74074 9.09294 12.5003 12.5 12.5003ZM12.5 15.5837C8.38377 15.5837 0.166687 17.6495 0.166687 21.7503V24.8337H24.8334V21.7503C24.8334 17.6495 16.6163 15.5837 12.5 15.5837Z" fill="black"/>
                                    </svg>
                                </div>
                                <label for="">Scorers</label>
                            </div>
                            <div class="person">
                                <div class="logo commentator" onclick="select_person(this)">
                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5 12.5003C15.9071 12.5003 18.6667 9.74074 18.6667 6.33366C18.6667 2.92658 15.9071 0.166992 12.5 0.166992C9.09294 0.166992 6.33335 2.92658 6.33335 6.33366C6.33335 9.74074 9.09294 12.5003 12.5 12.5003ZM12.5 15.5837C8.38377 15.5837 0.166687 17.6495 0.166687 21.7503V24.8337H24.8334V21.7503C24.8334 17.6495 16.6163 15.5837 12.5 15.5837Z" fill="black"/>
                                    </svg>
                                </div>
                                <label for="">Commentator</label>
                            </div>
                        </div>
                    </div>
                    <div class="add-btn">
                        <button onclick="schedule_match(event)" type="submit" id="schedule_match">Make Match</button>
                    </div>
                </div>
                    
            </div>

        </div>
        <iframe src="./select-officials.php" frameborder="0" class="officials-frame"></iframe>
    </div>
    <script>
        const team1 = '<?php echo $team1; ?>';
        const team2 = '<?php echo $team2; ?>';
        const game = '<?php echo $game; ?>';
        const admin = '<?php echo $_SESSION["email"]; ?>';
        let Umpires = [];
        let Scorers = [];
        let Commentators = [];
        let next_page = document.querySelector('.officials-frame');
        let goBack = ()=>{
            parent.postMessage("closeIframe", "*");
        }

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

            document.querySelectorAll('.knife').forEach(el => {
                el.classList.add('show1');
            });
        };

        let schedule_match = (e) => {
            e.preventDefault();

            let city = document.getElementById('city').value;
            let ground = document.getElementById('ground').value;
            let timeInput = document.getElementById('timeInput').value;
            let dateInput = document.getElementById('dateInput').value;
            let s_type = document.getElementById('s-type').value;
            let over = document.getElementById('over').value;
            let password = '';

            let formdata = new FormData();
            formdata.append('team1',team1);
            formdata.append('team2',team2);
            formdata.append('game',game);
            formdata.append('city',city);
            formdata.append('ground',ground);
            formdata.append('timeInput',timeInput);
            formdata.append('dateInput',dateInput);
            formdata.append('s_type',s_type);
            formdata.append('over',over);
            formdata.append('Umpires[]', Umpires);
            formdata.append('Scorers[]', Scorers);
            formdata.append('Commentators[]', Commentators);

            formdata.forEach((value, key) => {
            console.log(key + ': ' + value);
            });

            fetch('../Backend/schedule_match.php', {
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
                if(data.status == 409){
                    let el = document.getElementById(`error-${data.field}`);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }else if(data.status == 200){
                    //alert('Match Scheduled Successfully');
                    password = data.pass;

                    Scorers.forEach((scorer) => {
                        fetch('../mail.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                for_value: 'Scorer',
                                game: game,
                                venue: city,
                                time: timeInput,
                                password : password,
                                date: dateInput,
                                recipient_email: scorer
                            })
                        });
                    });

                    Umpires.forEach((umpire) => {
                        fetch('../mail.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                for_value: 'Umpire',
                                game: game,
                                venue: city,
                                time: timeInput,
                                date: dateInput,
                                recipient_email: umpire
                            })
                        });
                    });

                    // Immediately navigate to next page — no waiting
                    location.replace('../dashboard.php?update="live"&sport="CRICKET"');
                }
            })
            .catch(error => console.log(error));
            
        }

        window.addEventListener("message", (event) => {
            if (event.data === "closeIframe") {
                next_page.classList.remove('active');  

            }

            if (event.data.type === "emailList" && (event.data.Umpires)) {
                let arr = event.data.Umpires;
                Umpires = arr;
                document.querySelector('.logo.umpire').style.border = '2px solid orange'
            }

            if (event.data.type === "emailList" && (event.data.Scorers)) {
                let arr = event.data.Scorers;
                Scorers = arr;
                document.querySelector('.logo.scorer').style.border = '2px solid orange'

            }

            if (event.data.type === "emailList" && (event.data.Commentator)) {
                let arr = event.data.Commentator;
                Commentators = arr;
                document.querySelector('.logo.commentator').style.border = '2px solid orange'
            }
        });

        let select_person = (el) => {
            let parent = el.closest('.person');
            let text = parent.querySelector('label').textContent.trim();
            next_page.src = `./select-officials.php?p=${text}`;
            next_page.classList.add('active');
        }
    </script>
</body>
</html>