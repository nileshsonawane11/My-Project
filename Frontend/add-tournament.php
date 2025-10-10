<?php
    session_start();

    if(!isset($_SESSION['user'])){
        header('location: ../front-page.php');
        exit();
    }
    if($_SESSION['role'] == "User"){
        header('location: ../dashboard.php?update="live"&sport="CRICKET"');
        exit();
    }

    $game = $_GET['game'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <title>Add Tournament</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
            scrollbar-width: none;
        }
        
        /* Theme Variables */
        :root {
            --primary-color: rgba(209, 34, 31, 1);
            --primary-light: rgba(209, 34, 31, 0.8);
            --primary-dark: rgba(160, 25, 23, 1);
            --background: #ffffff;
            --card-bg: #ffffff;
            --text-dark: #000000;
            --text-light: #333333;
            --border-color: #e0e0e0;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --svg-fill: #000000;
            --hover-bg: rgba(209, 34, 31, 0.08);
            --input-bg: #ffffff;
            --gradient: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
            --team-bg: #eeeeeeab;
            --logo-bg: #e8e8e8;
            --invert: invert(0);

        }

        /* Dark theme variables */
        [data-theme="dark"] {
            --background: #121212;
            --card-bg: #1e1e1e;
            --text-dark: #ffffff;
            --text-light: #e0e0e0;
            --border-color: #333333;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            --svg-fill: #ffffff;
            --hover-bg: rgba(209, 34, 31, 0.15);
            --input-bg: #2a2a2a;
            --gradient: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
            --team-bg: #3d3d3d;
            --logo-bg: #4d4d4d;
            --invert: invert(1);
        }
        
        svg path {
            fill: var(--text-dark);
        }
        body{
            height: max-content;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            flex-direction: column;
            background-color: var(--background);
            color: var(--text-dark);
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
            fill: var(--svg-fill);
        }

        /* For <input type="date"> */
        input[type="date"]::-webkit-calendar-picker-indicator {
        filter: var(--invert);    /* makes it white */
        cursor: pointer;
        }

        /* For <input type="time"> */
        input[type="time"]::-webkit-calendar-picker-indicator {
        filter: var(--invert);    /* makes it white */
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
            color: var(--text-dark);
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
            color: var(--text-light);
        }
        .input-fields input:valid ~ label,
        .input-fields input:focus ~ label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: var(--primary-color);
        }
        .container3{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
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
            border: solid 1px var(--border-color);
            border-radius: 50%;
            overflow: hidden;
        }
        .image img{
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .select-img{
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--card-bg);
            height: 40px;
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor : pointer;
            box-shadow: var(--shadow);
        }
        .select-img svg {
            fill: var(--svg-fill);
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
            color: var(--text-dark);
            padding: 10px;
            border-radius: 20px;
            text-wrap: auto;
            background: var(--team-bg);
            cursor: pointer;
            box-shadow: var(--shadow);
            flex-wrap: wrap;
        }
        .other-info{
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 20px;
            margin-left: 15px;
            cursor: pointer;
            color: var(--text-dark);
        }
        .team-info{
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            height: 100%;
            cursor: pointer;
            justify-content: center;
            color: var(--text-dark);
        }
        .team-info label{
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
            gap: 5px;
            color: var(--text-dark);
        }
        .logo{
            min-height: 75px;
            min-width: 75px;
            background: var(--logo-bg);
            margin: 10px;
            border-radius: 50%;
        }
        .part.or{
            text-align: center;
            color: var(--text-dark);
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
            background: var(--input-bg);
            width: 100px;
            height: 20px;
            text-align: left;
            font-size: 16px;
            transition: 0.3s ease-in-out;
            color: var(--text-dark);
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
            background: var(--hover-bg);
            transition:all 0.2s ease-in-out;
            color: var(--text-dark);
        }
        .option.active,
        .option1.active {
            background: rgba(209, 34, 31, 0.1);
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }
        .input-fields textarea:valid + label,
        .input-fields textarea:focus + label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: var(--primary-color);
        }
        textarea{
            overflow: hidden;
            resize: none;
            min-height: 30px;
            padding: 8px;
            font-size: 14px;
            width: 100%;
            border: none;
            border-bottom: solid 1px var(--border-color);
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 15px;
            width: 100%;
            outline: none;
            height: 45px;
            background: var(--input-bg);
            color: var(--text-dark);
        }
        .ast{
            color: var(--primary-color);
            font-size: 14px;
        }
        .error{
            display: none;
            color: var(--primary-color); 
            width:100%;
            font-size:14px;
            margin: 5px;
        }
        .match-frame{
            position: fixed;
            bottom: -100%;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            transition: bottom 0.8s ease;
            z-index: 999;
            background-color: var(--background);
        }
        .match-frame.active{
            bottom: 0;
        }
        .team-selector{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            height: 50px;
            background: var(--hover-bg);
            color: var(--text-dark);
        }
        .team-selector svg,
        .team-selector label{
            cursor: pointer;
            fill: var(--svg-fill);
            color: var(--text-dark);
        }

        @media (min-width:601px) {
             .container{
                display: flex;
                background-color: var(--card-bg);
                box-shadow: var(--shadow);
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
                border-bottom: solid 1px var(--border-color);
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 16px;
                width: 100%;
                outline: none;
                height: 45px;
                background: var(--input-bg);
                color: var(--text-dark);
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
                height: 40px;
                width: 300px;
            }
        }

        @media(max-width: 601px) {
            .container{
                display: flex;
                background-color: var(--card-bg);
                box-shadow: var(--shadow);
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
                border-bottom: solid 1px var(--border-color);
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 15px;
                width: 100%;
                outline: none;
                height: 45px;
                background: var(--input-bg);
                color: var(--text-dark);
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
                    <label for="">Add a Tournament</label>
                    <h4>Add Details</h4>
                </div>
                <div class="container3">
                    <div class="logo-img">
                        <div class="img-container">
                            <div class="image">
                                <img  id="previewImage" src="https://img.freepik.com/free-vector/championship-trophy-vector-logo-design_47987-24739.jpg?semt=ais_hybrid&w=740" alt="">
                            </div>
                            <div class="select-img" onclick="uploadfile()">
                                <svg width="28" height="22" viewBox="0 0 31 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.2918 3.24984H23.8467L22.3051 1.70817C21.4125 0.815546 19.8461 0.166504 18.5835 0.166504H12.4168C11.1542 0.166504 9.58787 0.815546 8.69525 1.70817L7.15358 3.24984H4.7085C2.15858 3.24984 0.0834961 5.32492 0.0834961 7.87484V20.2082C0.0834961 22.7581 2.15858 24.8332 4.7085 24.8332H26.2918C28.8417 24.8332 30.9168 22.7581 30.9168 20.2082V7.87484C30.9168 5.32492 28.8417 3.24984 26.2918 3.24984ZM15.5002 18.6665C14.0689 18.6663 12.6963 18.0975 11.6844 17.0853C10.6725 16.0731 10.1041 14.7004 10.1043 13.2691C10.1045 11.8379 10.6733 10.4653 11.6855 9.45338C12.6977 8.44146 14.0704 7.87309 15.5017 7.87329C16.933 7.8735 18.3055 8.44227 19.3175 9.45447C20.3294 10.4667 20.8977 11.8394 20.8975 13.2707C20.8973 14.7019 20.3286 16.0745 19.3164 17.0864C18.3042 18.0983 16.9314 18.6667 15.5002 18.6665ZM24.7502 11.4191C24.487 11.4191 24.2264 11.3673 23.9832 11.2666C23.74 11.1659 23.5191 11.0182 23.333 10.8321C23.1469 10.646 22.9993 10.4251 22.8986 10.1819C22.7978 9.93877 22.746 9.67815 22.746 9.41496C22.746 9.15177 22.7978 8.89116 22.8986 8.648C22.9993 8.40484 23.1469 8.18391 23.333 7.9978C23.5191 7.8117 23.74 7.66407 23.9832 7.56335C24.2264 7.46263 24.487 7.4108 24.7502 7.4108C25.2817 7.4108 25.7915 7.62195 26.1673 7.9978C26.5432 8.37366 26.7543 8.88342 26.7543 9.41496C26.7543 9.9465 26.5432 10.4563 26.1673 10.8321C25.7915 11.208 25.2817 11.4191 24.7502 11.4191Z" fill="black"/>
                                </svg>
                                <input type="file" name="" id="fileInput" style="display:none">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info">
                    
                    <div class="input-fields"><input type="text" name="" id="tname" class="data" required><label for="tname">Tournament name <sup class="ast">*</sup></label></div>
                    <div class="input-fields"><input type="text" name="" id="city" class="data" required><label for="city">City <sup class="ast">*</sup></label></div>
                    <div class="input-fields"><input type="text" name="" id="ground" class="data" required><label for="ground">Ground <sup class="ast">*</sup></label></div>
                    <div class="input-fields"><input type="text" name="" id="organizer-name" class="data" required><label for="oranizer-name">Organizer Name <sup class="ast">*</sup></label></div>
                    <div class="input-fields"><input type="number" pattern="[0-9]{10}" maxlength="10" name="" id="organizer-no" class="data" required><label for="oranizer-no">Organizer Number <sup class="ast">*</sup></label></div>
                    <div class="input-fields"><input type="email" name="" id="email" class="data" required><label for="email">Organizer Email <sup class="ast">*</sup></label></div>
                    <div class="schedule">
                        <div class="input-fields"><input type="date" id="dateInput" placeholder="Select Date" required><label for="dateInput" id="date">Date <sup class="ast">*</sup></label></div>
                        <div class="input-fields event-time"><input type="time" id="timeInput" placeholder="Select Time" required><label for="timeInput" id="time">Time <sup class="ast">*</sup></label></div>
                    </div>
                    <div id="error-datetime" class="error"></div>
                    <div id="error-empty" class="error"></div>
                </div>
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

                <div class="info info-input">
                    <h4>Winning prize (optional)</h4>
                    <div class="prize-container">
                        <div class="option" data-value="Cash">CASH</div>
                        <div class="option" data-value="Trophies">TROPHIES</div>
                        <div class="option" data-value="Both">BOTH</div>
                    </div>
                    <input type="hidden" id="selectedprize" name="prize_type">
                </div>
                <div class="info info-input">
                    <h4>Tournament Format</h4>
                    <div class="prize-container">
                        <div class="option1" data-value="league">LEAGUE</div>
                        <div class="option1" data-value="knockout">KNOCKOUT</div>
                    </div>
                    <input type="hidden" id="selectedformat" name="format_type">
                </div>
                <div class="info info-input">
                    <h4>Any Additional Details?</h4>
                    <div class="prize-container">
                        <div class="input-fields"><textarea name="" id="notes" placeholder="Add more details about rules, prize, etc." required></textarea></div>
                    </div>
                </div>
                <div id="error-exist" class="error"></div>
                <div class="add-btn">
                    <button onclick="add_tournament(event)" type="submit" id="add-event">Next (Add TEAMS)</button>
                </div>
            </div>

        </div>
    </div>
    <script>
        const gameName = '<?php echo $game; ?>';
        let t_id = '';
        let goBack = ()=>{
            window.history.back();
        }

        let uploadfile = () => {
            document.getElementById("fileInput").click();
        }

        let select_teams = () => {

        }

        document.addEventListener('message',(event)=>{
            if (event.data === "closeIframe") {
                next_page.classList.remove('active');  

                setTimeout(()=>{
                    window.location.replace(`./add-tournament.php?game=${gameName}`);
                },550)
            }
        })

        document.getElementById("fileInput").addEventListener("change", function(event) {
            const file = URL.createObjectURL(event.target.files[0]);
            document.getElementById("previewImage").src = file;
        });


        //radio for Prize
        const options = document.querySelectorAll('.option');
        const options1 = document.querySelectorAll('.option1');
        const hiddenInput = document.getElementById('selectedprize');
        const formatInput = document.getElementById('selectedformat');

        options.forEach(option => {
            option.addEventListener('click', () => {
                if(option.classList.contains('active')) {
                option.classList.remove('active');
                hiddenInput.value = '';
                console.log("Selection cleared");
                } else {
                options.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                hiddenInput.value = option.getAttribute('data-value');
                console.log("Selected Match Type:", hiddenInput.value);
                }
            });
        });

        options1.forEach(option => {
            option.addEventListener('click', () => {
                if(option.classList.contains('active')) {
                option.classList.remove('active');
                formatInput.value = '';
                console.log("Selection cleared");
                } else {
                options1.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                formatInput.value = option.getAttribute('data-value');
                console.log("Selected Format:", formatInput.value);
                }
            });
        });

        //manage textarea
        const textarea = document.getElementById('notes');

        textarea.addEventListener('input', function(){
            this.style.height = 'auto';  // reset height
            this.style.height = (this.scrollHeight) + 'px';  // set new height
        });

        //Add Tournament Button
        let add_tournament = (event)=>{
            let tname = document.getElementById('tname').value;
            let city = document.getElementById('city').value;
            let ground = document.getElementById('ground').value;
            let organizer_no = document.getElementById('organizer-no').value;
            let organizer_name = document.getElementById('organizer-name').value;
            let email = document.getElementById('email').value;
            let dateInput = document.getElementById('dateInput').value;
            let timeInput = document.getElementById('timeInput').value;
            let s_type = document.getElementById('s-type').value;            
            let details = document.getElementById('notes').value;
            let logo = document.getElementById("fileInput").files[0];

            let formData = new FormData();
            formData.append('tournament_name',tname)
            formData.append('city',city)
            formData.append('ground',ground)
            formData.append('organizer_name',organizer_name)
            formData.append('organizer_number',organizer_no)
            formData.append('organizer_email',email)
            formData.append('tournament_date',dateInput)
            formData.append('tournament_time',timeInput)
            formData.append('sports_type',s_type)
            formData.append('winning_prize',hiddenInput.value)
            formData.append('tournament_format',formatInput.value)
            formData.append('additional_details',details)
            formData.append('logo',logo)
            formData.append('game',gameName)

            formData.forEach((value, key) => console.log(key+ ':'+ value));

            fetch('../Backend/add_tournament.php',{
                body : formData,
                method : 'POST'
            })
            .then(response => response.json())
            .then((data) => {
                console.log(data)
                document.querySelectorAll('[id^="error-"]').forEach((el) => {
                    el.innerHTML = '';
                    el.style.display = 'none';
                });

                if(data.status != 200){
                    let el = document.getElementById(`error-${data.field}`);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }else{
                    t_id = data.id;
                    window.location.href = `./manage-teams.php?sport=${gameName}&tournament=${t_id}`;
                }
            })
            .catch(error => console.log(error));
            // window.location.href = './match-making.php';
        }

        // Disable right-click
  document.addEventListener('contextmenu', event => event.preventDefault());

  // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
  document.onkeydown = function(e) {
    if(e.keyCode == 123) return false; // F12
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
    if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
  }

  // Theme management for this page
    function initializeTheme() {
        // Check for saved theme preference or use system preference
        const currentTheme = localStorage.getItem('theme') || 
                            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        // Set the initial theme
        if (currentTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
        }
        
        // Listen for theme changes from other tabs/pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') {
                if (e.newValue === 'dark') {
                    document.body.setAttribute('data-theme', 'dark');
                } else {
                    document.body.removeAttribute('data-theme');
                }
            }
        });
        
        // Listen for custom events if your dashboard dispatches them
        window.addEventListener('themeChanged', function(e) {
            if (e.detail === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
            } else {
                document.body.removeAttribute('data-theme');
            }
        });
    }

    // Initialize theme when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
    });

    // Function to programmatically change theme if needed
    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        }
    }

    // Function to get current theme
    function getCurrentTheme() {
        return document.body.getAttribute('data-theme') || 'light';
    }
    </script>
</body>
</html>