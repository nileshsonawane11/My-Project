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
    <title>Add Tournament</title>
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
                                <img src="" alt="">
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
                    
                    <div class="input-fields"><input type="text" name="" id="tname" class="data" required><label for="tname">Tournament name</label></div>
                    <div class="input-fields"><input type="text" name="" id="city" class="data" required><label for="city">City</label></div>
                    <div class="input-fields"><input type="text" name="" id="ground" class="data" required><label for="ground">Ground</label></div>
                    <div class="input-fields"><input type="text" name="" id="oranizer-name" class="data" required><label for="oranizer-name">Organizer Name</label></div>
                    <div class="input-fields"><input type="number" pattern="[0-9]{10}" maxlength="10" name="" id="oranizer-no" class="data" required><label for="oranizer-no">Organizer Number</label></div>
                    <div class="input-fields"><input type="email" name="" id="email" class="data" required><label for="email">Organizer Email</label></div>
                    <div class="schedule">
                        <div class="input-fields"><input type="date" id="dateInput" placeholder="Select Date" required><label for="dateInput" id="date">Date</label></div>
                        <div class="input-fields event-time"><input type="time" id="timeInput" placeholder="Select Time" required><label for="timeInput" id="time">Time</label></div>
                    </div>
                        <div class="input-fields"><input type="number" name="" id="jno" class="data" required><label for="jno">Jersey No.(optional)</label></div>                
                </div>

                <div class="info">
                    <h4>Sports Type</h4>
                    <select name="" id="s-type">
                        <option value="Default" disabled selected>(Default)</option>
                        <option value="">Test Match</option>
                        <option value="">One Day International (ODI)</option>
                        <option value="">T20 Match</option>
                        <option value="">T10 Match</option>
                        <option value="">Practice Match</option>
                        <option value="">First-Class Cricket</option>
                        <option value="">List A Cricket</option>
                        <option value="">Club Cricket</option>
                        <option value="">Tape Ball Cricket</option>
                        <option value="">Box Cricket / Indoor Cricket</option>
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
                    <input type="hidden" id="selectedformat" name="prize_type">
                </div>
                <div class="info info-input">
                    <h4>Any Additional Details?</h4>
                    <div class="prize-container">
                        <div class="input-fields"><textarea name="" id="notes" placeholder="Add more details about rules, prize, etc." required></textarea></div>
                    </div>
                    <input type="hidden" id="selectedformat" name="prize_type">
                </div>
                <div class="add-btn">
                    <button onclick="add_tournament(event)" type="submit" id="add-event">Add Player</button>
                </div>
            </div>

        </div>
    </div>
    <script>
        let goBack = ()=>{
            window.history.back();
        }

        let uploadfile = () => {
            document.getElementById("fileInput").click();
        }

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
            window.location.href = './match-making.php';
        }
    </script>
</body>
</html>