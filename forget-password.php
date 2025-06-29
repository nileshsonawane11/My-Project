<?php
    session_start();
    include 'config.php';
    if(isset($_SESSION['user'])){
        header('location: ./dashboard.php?update="live"&sport="CRICKET"');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forgot password</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        }
        .container form{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 5px;
        }
        .otptxt {
            font-size: 13px;
            margin: 10px;
        }
        .toogle-pass {
            width: 100%;
            gap: 9px;
            font-size: 12px;
            display: flex;
            align-items: center;
            flex-direction: row;
        }
        .otp-container {
            display: flex;
            justify-content: space-between;
            gap: 5px;
            width: 100%;
        }
        .error{
            display: none;
            color:red; 
            width:100%;
            font-size:12px;
            margin: 5px;
        }
        .otp-btn{
            width: 100%;
            margin: 9px;
            color: blue;
            font-size: 13px;
            cursor: pointer;
            display: none;
        }
        .otp{
            width: 100%;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .otp-container input{
            text-align: center;
        }
        .submit-btn{
            width: 100%;
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
        #showPass{
            cursor: pointer;
        }
        
        h1{
            margin-bottom:60px;
        }

        @media (min-width:601px) {
            body{
                backdrop-filter: blur(10px);
                display: flex;
                background-repeat: no-repeat;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                background-attachment: fixed;
                background-position: center;
                background-size: cover;
                flex-direction: column;
                height: 100vh;
                user-select: none;
            }
            .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                overflow: hidden;
                width: 768px;
                max-width: 100%;
                min-height: 480px;
                align-items: center;
                justify-content : space-between;
                flex-direction: column;
            }
            .container button{
                background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                color: #fff;
                font-size: 12px;
                padding: 10px 45px;
                border: 1px solid transparent;
                border-radius: 8px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                margin-top: 10px;
                cursor: pointer;
            }
            .container input[type="text"],[type="email"],[type="password"],select{
                background-color: #eee;
                border:none;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 13px;
                border-radius: 8px;
                width: 100%;
                outline: none;
                height: 45px;
                overflow: hidden;
            }
            .container form{
                max-width: 450px;
            }
        }

        @media(max-width: 601px) {
            body{
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                height: 100vh;
            }
            .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                overflow: hidden;
                width: 768px;
                z-index: 0;
                max-width: 100%;
                min-height: 480px;
                padding: 40px 40px;
                height: 100vh;
                align-items: center;
                justify-content : space-between;
                flex-direction: column;
            }
            .container button{
                background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                color: #fff;
                font-size: 12px;
                padding: 10px 45px;
                border: 1px solid transparent;
                border-radius: 8px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                margin-top: 10px;
                cursor: pointer;
                width: 100%;
                height: 45px;
            }
            .container input[type="text"],[type="email"],[type="password"],select{
                background-color: #eee;
                border:none;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 15px;
                border-radius: 8px;
                width: 100%;
                outline: none;
                height: 45px;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="return" onclick="goBack()">
            <div><svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div></div>
        </div>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
            <h1>Reset Password</h1>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <div id="error-email" class="error"></div>
            <button type="button" name="forgot" onclick="send_otp(event)" id="sendOTP" disabled style="opacity: 0.5;">Request OTP</button>
            <div class="otp">
                <label class="otptxt" for="otp">Enter OTP : </label>
                <div class="otp-container">
                    <input type="text" name="otp1" maxlength="1" id="otp1" oninput="moveFocus(this, 'otp2', 'next')" onkeydown="handleBackspace(event, this, 'otp1')" />
                    <input type="text" name="otp2" maxlength="1" id="otp2" oninput="moveFocus(this, 'otp3', 'next')" onkeydown="handleBackspace(event, this, 'otp2')" />
                    <input type="text" name="otp3" maxlength="1" id="otp3" oninput="moveFocus(this, 'otp4', 'next')" onkeydown="handleBackspace(event, this, 'otp3')" />
                    <input type="text" name="otp4" maxlength="1" id="otp4" onkeydown="handleBackspace(event, this, 'otp4')" />
                </div>
                <div id="error-otp" class="error"></div>
                <div class="otp-btn" id="otp-btn"></div>
            </div>
                <input type="password" name="password" placeholder="Password"  id="password" class='password'>
                <input type="password" name="password2" placeholder="Re-Enter Password"  id="password2" class='password'>
                    <div id="error-password" class="error"></div>
                <div class='toogle-pass'><input type="checkbox" id="showPass" onclick="showPassword()"> Show Password</div>
                <div id="error-empty" class="error"></div>
        </form>
        <div class="submit-btn">
            <button onclick="reset(event)" type="submit" id="signup-btn" name="reset_pass">Reset Password</button>
        </div>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }

        function showPassword() {
            var passwordInputs = document.querySelectorAll(".password");
            var checkbox = document.getElementById("showPass");

            passwordInputs.forEach(function(input) {
                input.type = checkbox.checked ? "text" : "password";
            });
        }

        let reset = (e)=>{
            e.preventDefault();
        
            let email = document.getElementById('email').value;
            let otp1 = document.getElementById('otp1').value;
            let otp2 = document.getElementById('otp2').value;
            let otp3 = document.getElementById('otp3').value;
            let otp4 = document.getElementById('otp4').value;
            let otp = otp1 + otp2 + otp3 + otp4;
            let password = document.getElementById('password').value;
            let password2 = document.getElementById('password2').value;
            let data = {
                'email': email,
                'otp': otp,
                'password': password,
                'password2': password2,
            }

            fetch("./Backend/reset_password.php", {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then((data)=>{
                document.querySelectorAll('[id^="error-"]').forEach((el) => {
                    el.innerHTML = '';
                    el.style.display = 'none';
                });

                if(data.status === 200){
                    alert(`Password Reset Successfully!`)
                    window.location.href = './front-page.php';
                    
                }else{
                    let el = document.getElementById('error-'+data.field);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }

                console.log(data.message);
            })
            .catch(error => console.log(error));

        };
        
        let send_otp =(e)=>{
            e.preventDefault();
            let email = document.getElementById('email').value;
            let data = {
                'role': '',
                'email': email,
                'fname': '',
                'lname': '',
                'for' : 'forgot'
            }
            data = JSON.stringify(data);
            e.preventDefault();
            fetch('./OTP-mail.php', {
                        method: 'POST',
                        body: data,
                        headers: {
                            'Content-type': 'application/json; charset=UTF-8'
                        }
                    })
            .then((response) => response.json())
            .then((data) => {
                        document.querySelectorAll('[id^="error-"]').forEach((el) => {
                            el.innerHTML = '';
                            el.style.display = 'none';
                        });

                        if(data.status == "error"){
                            let send_btn = document.getElementById('sendOTP'); 

                            if(!send_btn){
                                console.error("Send OTP button not found");
                                return;
                            }

                            send_btn.setAttribute('disabled', 'true');
                            send_btn.style.opacity = '0.5';

                            let email_error = document.getElementById('error-email');
                            email_error.style.display = 'block';
                            email_error.innerHTML = data.message;
                        }else{
                            sent();
                            alert(`OTP sent successfully! on ${email}`); 
                        }
                        console.log(data);
                    })
            .catch();
        }

        function sent(){
                
                let otp_container = document.querySelector('.otp');
                let send_again_btn = document.getElementById('otp-btn');
                let send_btn = document.getElementById('sendOTP'); 

                if(!send_btn){
                    console.error("Send OTP button not found");
                    return;
                }

                otp_container.style.display = 'flex';
                send_again_btn.style.display = 'block';
                send_btn.setAttribute('disabled', 'true');
                send_btn.style.opacity = '0.5';

                let waitTime = 59;
                send_again_btn.innerHTML = '00:' + waitTime;

                let countdown = setInterval(() => {
                    waitTime--;
                    send_again_btn.innerHTML = '00:' + (waitTime < 10 ? '0' + waitTime : waitTime);

                    if (waitTime <= 0) {
                        clearInterval(countdown);
                        send_again_btn.disabled = false;
                        send_again_btn.innerHTML = "<span class='sendagain' onclick='send_otp(event)'>Resend OTP</span>";
                    }
                }, 1000);
            };

             function moveFocus(current, nextId, direction) {
                if (direction === 'next' && current.value.length === 1) {
                    
                    const nextInput = document.getElementById(nextId);
                    if (nextInput) nextInput.focus();
                }
            };
            function handleBackspace(event, current, currentId) {
                if (event.key === "Backspace" && current.value === "") {
                    const prevInput = current.previousElementSibling;
                    if (prevInput) prevInput.focus();
                }
            };

            setInterval(() => {
                let send_btn = document.querySelector('#sendOTP');
                let email2 = document.getElementById('email');
                email2.addEventListener('input', function () {
                    var email = email2.value;
                    var isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                    send_btn.removeAttribute('disabled');
                    send_btn.style.opacity = '1';
                });
           },10); 
    </script>
</body>
</html>