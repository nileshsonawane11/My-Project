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
<<<<<<< HEAD

=======
>>>>>>> 1707ca4e091edd07b456ac8f89e8295804f894c2
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
                        <input type="hidden" id="sessionEmail" value="<?php echo $_SESSION['email']; ?>">
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
        let userEmail = document.getElementById('sessionEmail').value;
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

            if (Commentators.length === 0) {
                Commentators.push(userEmail);
            }

            if (Scorers.length === 0) {
                Scorers.push(userEmail);
            }

            if (Umpires.length === 0) {
                Umpires.push(userEmail);
            }

            let formdata = new FormData();
            formdata.append('team1', team1);
            formdata.append('team2', team2);
            formdata.append('game', game);
            formdata.append('city', city);
            formdata.append('ground', ground);
            formdata.append('timeInput', timeInput);
            formdata.append('dateInput', dateInput);
            formdata.append('s_type', s_type);
            formdata.append('over', over);
            formdata.append('Umpires[]', Umpires);
            formdata.append('Scorers[]', Scorers);
            formdata.append('Commentators[]', Commentators);

            formdata.forEach((value, key) => {
                console.log(key + ': ' + value);
            });

            // First schedule the match via fetch()
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

                if (data.status == 409) {
                    let el = document.getElementById(`error-${data.field}`);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                } else if (data.status == 200) {
                    password = data.pass;

                    // Send mails via sendBeacon (fire-and-forget)
                    Scorers.forEach((scorer) => {
                        let scorerData = {
                            for_value: 'Scorer',
                            game: game,
                            venue: city,
                            time: timeInput,
                            password: password,
                            date: dateInput,
                            recipient_email: scorer
                        };
                        const scorerBlob = new Blob([JSON.stringify(scorerData)], { type: 'application/json' });
                        navigator.sendBeacon('../mail.php', scorerBlob);
                    });

                    Umpires.forEach((umpire) => {
                        let umpireData = {
                            for_value: 'Umpire',
                            game: game,
                            venue: city,
                            time: timeInput,
                            date: dateInput,
                            recipient_email: umpire
                        };
                        const umpireBlob = new Blob([JSON.stringify(umpireData)], { type: 'application/json' });
                        navigator.sendBeacon('../mail.php', umpireBlob);
                    });

                    // Immediately navigate to dashboard
                    window.parent.postMessage('gotohome', '*');
                }
            })
            .catch(error => console.log(error));
        };


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