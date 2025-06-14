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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>Add Staff</title>
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
            --background : linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        }
        body{
            height: max-content;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
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
            gap: 60px
        }
        .txt{
            line-height: 20px;
        }
        .matches{
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: max-content;
            gap: 80px;
        }
        .match-container{
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            flex-direction: column;
            gap: 20px
        }
        .team-container{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
        }
        .teams{
            height: 147px;
            width: 111px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-around;
            opacity: 0;
            background: #EEEEEE;
            box-shadow: 1px 2px 5px rgba(0, 0, 0, 0.5);
            transition: all 0.5s ease-in-out;
        }
        .logo{
            height: 75px;
            width: 75px;
            background: #D9D9D9;
            border-radius: 50%;
        }
        .tname{
            font-size: 17px;
            text-wrap: auto;
            text-align: center;
        }
        .vs,.plus{
            font-size: 30px;
            transform: translate(0,20rem);
            transition: all 0.5s ease-in-out;
            opacity: 0;
        }
        .right-side{
            transform: translateX(150%) translateY(50%);
        }
        .left-side{
            transform: translateX(-150%) translateY(50%);
        }
        .show{
            opacity: 1;
            transform: translate(0,0);
        }
        .team-no{
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        .match-head,.t-num{
            transition: all 0.5s ease-in-out;
            transition-delay: 0.3s;
            opacity: 0;
        }
        .match-list{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 80px;
        }
        .pls{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .plus{
                bottom: 30px;
                height: 60px;
                border: none;
                cursor: pointer;
                display: flex;
                font-size: 30px;
                width: 60px;
                border-radius: 50%;
                justify-content: center;
                align-items: center;
                overflow:hidden;
                box-shadow:0px 4px 0px rgb(131 131 131 / 50%);
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                transition: transform 0.5s ease;
            }
        .plus-icon{
            display: flex;
            align-items: center;
            justify-content: center;
             transition: transform 0.3s ease-out;
        }

        .plus:hover .plus-icon {
            transform: rotate(180deg);
        }


        @media (min-width:1001px){
            .matches{
                width: 75%;
            }
        }

        @media (min-width:601px) {
             .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                width: 90%;
                max-width: 100%;
                min-height: 100vh;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                padding: 40px;
            }
            .container2{
                gap: 70px;
                width: 70%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],select{
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
                min-height: 100vh;
                padding: 40px 40px;
                align-items: flex-start;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px
            }
            
            .container input[type="text"],[type="email"],[type="password"],[type="number"],select{
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
            <div class="txt">
                <label for="">Add a Tournament</label>
                <h4>Match Matching</h4>
            </div>
            <div class="match-list">
                <div class="matches">

                    <div class="match-container">
                        <h4 class="match-head">Match 1</h4>
                        <div class="team-container">
                            <div class="teams left-side">
                                <div class="logo"></div>
                                <div class="tname">Team name</div>
                            </div>
                            <label for="" class="vs">VS</label>
                            <div class="teams right-side">
                                <div class="logo"></div>
                                <div class="tname">Team name</div>
                            </div>
                        </div>
                        <div class="team-no">
                            <div class="t-num">(Team 1)</div>
                            <div class="t-num">(Team 2)</div>
                        </div>
                    </div>

                    <div class="match-container">
                        <h4 class="match-head">Match 1</h4>
                        <div class="team-container">
                            <div class="teams left-side">
                                <div class="logo"></div>
                                <div class="tname">Team name</div>
                            </div>
                            <label for="" class="vs">VS</label>
                            <div class="teams right-side">
                                <div class="logo"></div>
                                <div class="tname">Team name</div>
                            </div>
                        </div>
                        <div class="team-no">
                            <div class="t-num">(Team 1)</div>
                            <div class="t-num">(Team 2)</div>
                        </div>
                    </div>

                </div>
                <div class="pls">
                    <div class="plus" onclick="shuffle()">
                        <div class="plus-icon"><svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M27.9997 12.6418C27.5921 9.70879 26.2315 6.9912 24.1274 4.90759C22.0234 2.82398 19.2926 1.48996 16.3558 1.11102C13.419 0.732074 10.439 1.32923 7.87501 2.81051C5.31098 4.29178 3.30509 6.57499 2.16634 9.30843M1.33301 2.64176V9.30843H7.99967M1.33301 15.9751C1.74061 18.9081 3.10123 21.6257 5.20528 23.7093C7.30933 25.7929 10.0401 27.1269 12.9769 27.5058C15.9137 27.8848 18.8936 27.2876 21.4577 25.8064C24.0217 24.3251 26.0276 22.0419 27.1663 19.3084M27.9997 25.9751V19.3084H21.333" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        let goBack = ()=>{
            window.history.back();
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

            document.querySelectorAll('.plus').forEach(el => {
                el.classList.add('show');
            });

            document.querySelectorAll('.match-head').forEach(el => {
                el.style.opacity = '1';
            });

            document.querySelectorAll('.t-num').forEach(el => {
                el.style.opacity = '1';
            });
        };

        //Shuffle match
        let shuffle = ()=>{
            window.location.reload();
        }
    </script>
</body>
</html>