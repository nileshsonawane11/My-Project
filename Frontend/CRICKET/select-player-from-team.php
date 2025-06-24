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
            --background : linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        }
        body{
            height: -webkit-fill-available;
            background:white;
            
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
            padding: 40px 0 0 40px;
        }
        .return svg{
            cursor: pointer;
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
            justify-content: center;
            align-items: flex-start;
            width: 100%;
        }
        .logo-name{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;

        }
        .logo-img{
            height: 120px;
            width: 120px;
            border-radius: 50%;
            background: #D9D9D9;
            overflow: hidden;
        }
        .logo-img img{
            height: 100%;
            width: 100%;
            object-fit:cover;
        }
        .logo-info{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .data-info{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 40px;
        }
        .other-info{
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            justify-content: center;
        }
        .other-info label{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: row;
            gap: 5px;
        }
        .mem{
            display: flex;
            width: 100%;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            height: 80px;
            background: #EDEDED;
            padding-left: 15px;
        }
        .mem.active{
            border : solid 1px #f85f09;
        }
        .mem-list{
                width: 100%;
                height: max-content;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                gap: 30px;
                overflow: hidden;
        }
        .mem-img{
            width: 60px;
            height: 60px;
            background: #9797974f;
            border-radius: 50%;
            margin-left: 10px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .mem-img img{
            margin-left: 0px;
        }
        .mem-head{
            width: 100%;
            text-align: left;
            margin-left : 15px;
        }
        .list-items{
            cursor: pointer;
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
        }
        .error{
            display: none;
            color:red; 
            width:100%;
            font-size:12px;
            margin: 5px;
            transition : all 0.3 ease-in-out;
        }
        .btn-container{
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .btn-container .btn{
                background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                color: #fff;
                font-size: 12px;
                padding: 10px 45px;
                border: 1px solid transparent;
                border-radius: 20px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                width: 150px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        .player-container{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        @media (min-width:601px) {
            .container{
                display: flex;
                background-color: #fff;
                position: relative;
                width: 90%;
                max-width: 100%;
                min-height: 480px;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                
            }
            .container2{
                gap: 40px;
                width: 70%;
            }
            .mem{
                border-radius: 10px;
            }
        }

        @media(max-width:601px){
            .container{
                display: flex;
                background-color: #fff;
                position: relative;
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
                
            }
            .container2{
                width: 100%;
                gap: 20px;
                height: 60%;
                justify-content: space-between;
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
                
                <!-- <?php
                    $team = $_GET['t'];
                    $sql = "SELECT * FROM teams WHERE t_id = '$team'";
                    $query = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($query);
                    if($row['t_logo']){
                        $src = "../assets/images/teams/".$row['t_logo'];
                    }else{
                        $src = "https://cdn-icons-png.flaticon.com/512/8140/8140303.png";
                    }
                ?> -->
                <div class="content">
                    <div class="logo-name">
                        <div class="logo-img">
                            <img src="" alt="">
                        </div>
                        <div class="logo-info">
                            <h4>Team Name</h4>
                            <div class="other-info">
                                <label for="coordinator" class=""><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg>Coordinator
                                </label>
                                <label for="place" class=""><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg>Place 
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>

            <div class="btn-container">
                <div class="btn">Players</div>
            </div>
        
            <div class="data-info">
                <div class="mem-list">
                    <h4 class="mem-head">Players (12)</h4>
                    <div class="player-container">
                        <div class='mem' data-value="Player_id">1
                            <div class='mem-img'><img src='' alt='' class='mem-img'></div>
                            <div class='mem-name'>name</div>
                        </div>
                        <div class='mem' data-value="Player_id">1
                            <div class='mem-img'><img src='' alt='' class='mem-img'></div>
                            <div class='mem-name'>name</div>
                        </div>
                    </div>
                </div>
            </div>
    </div>


    
    
    <script>
        let selectedplayer = '';
        const selected_player = document.querySelectorAll('.mem');

        let goBack = ()=>{
            window.history.back();
        }
        
        selected_player.forEach(option => {
            option.addEventListener('click', () => {
                if(option.classList.contains('active')) {
                option.classList.remove('active');
                selectedplayer = '';
                console.log("Selection Player", selectedplayer);
                } else {
                selected_player.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                selectedplayer = option.getAttribute('data-value');
                console.log("Selected Player:", selectedplayer);
                }
            });
        });
    </script>
</body>
</html>