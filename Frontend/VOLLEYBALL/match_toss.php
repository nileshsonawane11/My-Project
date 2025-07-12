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

    include '../../config.php';
    $match = $_GET['match_id'] ?? '';

    $query1 = mysqli_query($conn, "SELECT * FROM `matches` WHERE `match_id` = '$match'");
    $row = mysqli_fetch_assoc($query1);

    if(!empty($row['toss_winner'])){
        header("Location: ./score_panel.php?match_id=$match");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Scoring</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
            scrollbar-width: none;
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
            gap: 100px;
        }
        .info{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 40px
        }
        .error{
            display: none;
            color:red; 
            width:100%;
            font-size:14px;
            margin: 5px;
        }
        .teams,
        .options{
            height: 147px;
            width: 111px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-around;
            background: #EEEEEE;
            box-shadow: 1px 2px 5px rgba(0, 0, 0, 0.5);
            cursor: pointer;
        }
        .teams.active,
        .options.active{
            border:#ffae00 2px solid;
            box-shadow: none;
        }
        .logo{
            height: 75px;
            width: 75px;
            background: #D9D9D9;
            border-radius: 50%;
            overflow: hidden;
        }
        .types .logo{
            background: none;
        }
        .logo img{
            height: 100%;
            width: 100%;
            object-fit: contain;
        }
        .tname{
            font-size: 17px;
            text-wrap: auto;
            text-align: center;
            font-weight: 600;
        }
        .sector{
            display: flex;
            width: 100%;
            flex-direction: row;
            align-items: center;
            justify-content: space-around;
        }
        .add-btn{
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #flexSwitchCheckChecked.form-check-input{
            height: 25px;
            width: 50px;
        }
        #flexSwitchCheckChecked.form-check-input:checked {
            background-color: #ff7100;
            border-color: #fd9f0d;
        }
        .form-check-input:focus {
            border-color:rgb(255, 111, 0);
            outline: 0;
            box-shadow: 0 0 0 .25rem rgba(253, 65, 13, 0.22);
        }
        .form-check-input{
            border: 1px solid rgb(255 89 0);
            transition: background-position .5s ease-in-out;
        }
        .form-switch{
            position: absolute;
            right: 3px;
            top: 15px;
        }
        .rules{
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 30px;
        }
        .rule{
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            position: relative;
        }
        .logo {
            height: 75px;
            width:75px;
            background-color: transparent;
            border-radius: 50%;
            background-size: cover;
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
            .add-btn button{
                background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                color: #fff;
                font-size: 12px;
                padding: 10px 45px;
                border: 1px solid transparent;
                border-radius: 8px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                cursor: pointer;
                width: 90%;
                height: 45px;
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
                    <label for="">Scoring</label>
                    <h4>Toss</h4>
                </div>
                <div class="container3">
                     <?php
                        $query = mysqli_query($conn,"SELECT m.*, t1.t_id AS team1, t2.t_id AS team2, t1.t_logo AS team1_logo, t1.t_name AS team1_name, t2.t_logo AS team2_logo , t2.t_name AS team2_name FROM `matches` m JOIN `teams` t1 ON m.team_1 = t1.t_id JOIN `teams` t2 ON m.team_2 = t2.t_id WHERE m.match_id = '$match' ");
                        $row = mysqli_fetch_assoc($query);
                    ?>
                    <div class="info">
                        <label for="">Who won the toss?</label>
                        <div class="sector team">
                            <div class="teams" data-value="<?php echo $row['team1']; ?>">
                                <?php
                                    if (empty($row['team1_logo'])) {
                                        echo '<div class="logo"></div>';
                                    } else {
                                        echo "<div class=\"logo\"><img src=\"../../assets/images/teams/{$row['team1_logo']}\" alt=\"\"></div>";
                                    }

                                ?>
                                <div class="tname"><?php echo $row['team1_name']; ?></div>
                            </div>
                            <div class="teams" data-value="<?php echo $row['team2']; ?>">
                                <?php
                                    if (empty($row['team2_logo'])) {
                                        echo '<div class="logo"></div>';
                                    } else {
                                        echo "<div class=\"logo\"><img src=\"../../assets/images/teams/{$row['team2_logo']}\" alt=\"\"></div>";
                                    }

                                ?>
                                <div class="tname"><?php echo $row['team2_name'] ?></div>
                            </div>
                        </div>
                        <div class="error" id="error-team"></div>
                    </div>

                    <div class="info">
                        <label for="">Toss winner chose to</label>
                        <div class="sector types">
                            <div class="options" data-value="SERVE">
                                <div class="logo">
                                    <img src="https://i.ibb.co/YFftD8VJ/Pngtree-volleyball-player-blue-costume-8875190.png">

                                </div>
                                <div class="tname">SERVE</div>
                            </div>

                            <div class="options" data-value="COURT">
                                <div class="logo">
                                    <img src="https://i.ibb.co/xS1nd7nb/Pngtree-volleyball-player-red-custom-8530229.png">

                                    </div>
                                    <div class="tname">COURT</div>
                                </div>
                            </div>
                            <div class="error" id="error-decision"></div>
                            <div class="error" id="error-empty"></div> 
                        </div>
                    </div>

                    <div class="info">
                        <label for="">Enter match format</label>
                        <div class="sector rules">
                            
                            <div class="rule">
                                 <div class="input-fields event-time">
                                    <input type="number" id="sets"  required><label for="sets" id="time">Number of Sets</label></div>
                            </div>

                            <div class="rule">
                                 <div class="input-fields event-time">
                                    <input type="number" id="points"  required><label for="points" id="time">Points to complete set</label></div>
                            </div>
                        </div>
                        <div class="error" id="error-data_empty"></div>
                    </div>
                    <div class="add-btn">
                        <button onclick="start_match(event)" type="submit" id="start-match">Let’s Start</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        const teams = document.querySelectorAll('.teams');
        const options = document.querySelectorAll('.options');
        let selecteddecision = '';
        let selectedteam = '';

        let goBack = ()=>{
            window.history.back();
        }

        options.forEach(option => {
            option.addEventListener('click', () => {
                if(option.classList.contains('active')) {
                option.classList.remove('active');
                selecteddecision = '';
                console.log("Selection Option", selecteddecision);
                } else {
                options.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                selecteddecision = option.getAttribute('data-value');
                console.log("Selected Option:", selecteddecision);
                }
            });
        });

        teams.forEach(option => {
            option.addEventListener('click', () => {
                if(option.classList.contains('active')) {
                option.classList.remove('active');
                selectedteam = '';
                console.log("Selection Team", selectedteam);
                } else {
                teams.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                selectedteam = option.getAttribute('data-value');
                console.log("Selected Team:", selectedteam);
                }
            });
        });

        let start_match = (e) => {
            e.preventDefault();
            const sets = document.getElementById('sets').value;
            const points = document.getElementById('points').value;

            let formdata = new FormData();
            formdata.append('match_id', '<?php echo $match; ?>');
            formdata.append('selectedteam', selectedteam);
            formdata.append('selecteddecision', selecteddecision);
            formdata.append('sets',sets);
            formdata.append('points',points);

            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });

            fetch('./Backend/make_toss.php',{
                method : 'POST',
                body : formdata
            })
            .then(rsponse => rsponse.json())
            .then((data) => {
                console.log(data);
                if(data.status != 200){
                    let el = document.getElementById(`error-${data.field}`);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }else{
                    window.location.href = './score_panel.php?match_id=<?php echo $match; ?>';
                }
            })
            .catch(error => console.log(error));

        }
    </script>
</body>
</html>
