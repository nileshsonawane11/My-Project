<?php
    session_start();
    include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forgot password</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
    }

    :root {
        --primary-red: rgba(209, 34, 31, 1);
        --primary-red-light: rgba(209, 34, 31, 0.8);
        --primary-red-dark: rgba(160, 25, 23, 1);
        --background: linear-gradient(135deg, #1e1e1e, #121212);
        --card-bg: #1e1e1e;
        --text-dark: #e0e0e0;
        --text-light: #a0a0a0;
        --border-color: #333333;
        --accent-color: #1c0003ff;
        --svg-fill: white;
        --nav-fill: #2d2d2d;
        --invert: invert(1);
    }

    [data-theme="light"] {
        --background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
        --card-bg: #ffffff;
        --text-dark: #212529;
        --text-light: #495057;
        --border-color: #dee2e6;
        --svg-fill: black;
        --nav-fill: #ffffffff;
        --invert: invert(0);
    }

    body {
        background: var(--background);
        color: var(--text-dark);
    }

    .container form {
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
        color: var(--text-dark);
    }

    .toogle-pass {
        width: 100%;
        gap: 9px;
        font-size: 12px;
        display: flex;
        align-items: center;
        flex-direction: row;
        color: var(--text-dark);
    }

    .otp-container {
        display: flex;
        justify-content: space-between;
        gap: 5px;
        width: 100%;
    }

    .error {
        display: none;
        color: var(--primary-red);
        width: 100%;
        font-size: 12px;
        margin: 5px;
    }

    .otp-btn {
        width: 100%;
        margin: 9px;
        color: var(--primary-red-light);
        font-size: 13px;
        cursor: pointer;
        display: none;
    }

    .otp {
        width: 100%;
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .otp-container input {
        text-align: center;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-dark);
    }

    .submit-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .return {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-direction: row;
    }

    .return svg {
        cursor: pointer;
        filter: var(--invert);
    }

    #showPass {
        cursor: pointer;
        filter: var(--invert);
    }

    h1 {
        margin-bottom: 60px;
        color: var(--text-dark);
    }

    @media (min-width:601px) {
        body {
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

        .container {
            display: flex;
            background-color: var(--card-bg);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
            align-items: center;
            justify-content: space-between;
            flex-direction: column;
            border: 1px solid var(--border-color);
            padding: 40px;
        }

        .container button {
            background: linear-gradient(90deg, var(--primary-red-light), var(--primary-red-dark));
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

        .container input[type="text"],
        [type="email"],
        [type="password"],
        select {
            background-color: var(--nav-fill);
            border: 1px solid var(--border-color);
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
            height: 45px;
            overflow: hidden;
            color: var(--text-dark);
        }

        .container form {
            max-width: 450px;
        }
    }

    @media(max-width: 601px) {
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
        }

        .container {
            display: flex;
            background-color: var(--card-bg);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            width: 768px;
            z-index: 0;
            max-width: 100%;
            min-height: 480px;
            padding: 40px 40px;
            height: 100vh;
            align-items: center;
            justify-content: space-between;
            flex-direction: column;
            border: 1px solid var(--border-color);
        }

        .container button {
            background: linear-gradient(90deg, var(--primary-red-light), var(--primary-red-dark));
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

        .container input[type="text"],
        [type="email"],
        [type="password"],
        select {
            background-color: var(--nav-fill);
            border: 1px solid var(--border-color);
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 15px;
            border-radius: 8px;
            width: 100%;
            outline: none;
            height: 45px;
            overflow: hidden;
            color: var(--text-dark);
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

           // Disable right-click
 // document.addEventListener('contextmenu', event => event.preventDefault());

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