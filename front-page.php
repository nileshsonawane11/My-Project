<?php
    session_start();
    if(isset($_SESSION['user'])){
        header('location: ./dashboard.php?update="live"&sport="CRICKET"');
        exit();
    }
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies
    include './config.php';

    $login_url = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <link rel="icon" type="image/png" href="./assets/images/logo.png">

    <title>Login & Register Page</title>
    <!-- Web App Manifest -->
    <link rel="manifest" href="./manifest.json">

    <!-- Theme Color for Mobile Browsers -->
    <meta name="theme-color" content="#d1221f"/>

    <!-- iOS Safari Specific Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="LiveStrike">
    <link rel="apple-touch-icon" href="./assets/images/logo-192.png">
    <meta name="mobile-web-app-capable" content="yes">
    
<style>
       @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
    }

    /* CSS Variables for Light Theme (default) */
    :root {
        --primary-light: rgba(209, 34, 31, 1);
        --primary-dark: rgba(160, 25, 23, 1);
        --background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        --bg-color: #ffffff;
        --text-color: #333333;
        --input-bg: #eeeeee;
        --card-bg: #ffffff;
        --border-color: #dddddd;
        --shadow-color: rgba(0, 0, 0, 0.1);
        --social-border: #cccccc;
        --link-color: #333333;
        --error-color: #ff3333;
    }

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
        background-color: var(--bg-color);
        color: var(--text-color);
        transition: background-color 0.3s, color 0.3s;
    }

    .remember label{
        font-size : 10px;
        color: var(--text-color);
    }

    .toogle-pass{
        width: 100%;
        gap: 9px;
        font-size: 12px;
        display: flex;
        align-items: center;
        flex-direction: row;
        color: var(--text-color);
    }
        
    .password-container{
        width: 100%;
        position: relative;
    }

    #eyeBtn{
        position: absolute;
        top: 50%;
        border-radius: 8px;
        right: 0;
        display: flex;
        transform: translateY(-50%);
        height: 44px;
        width: 50px;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        background-color: var(--input-bg);
        transition: background-color 0.3s;
    }

    #showPass{
        cursor: pointer;
    }

    @keyframes animate-shape {
        0%{
            transform: translate3d(500px, 500px, 10px) scale(0);
        }
        50%{
            transform: translate3d(0px, 0px, 10px) scale(5);
        }
        100%{
            transform: translate3d(-600px, -600px, 10px) scale(0);
        }
    }

    .animate-shape{
        position: absolute;
        display: block;
        z-index: 9999;
        animation: animate-shape forwards 2.5s ease-in;
    }

    .container{
        background-color: var(--card-bg);
        border-radius: 30px;
        box-shadow: 0 5px 15px var(--shadow-color);
        position: relative;
        overflow: hidden;
        width: 768px;
        max-width: 100%;
        min-height: 480px;
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .container p{
        font-size: 14px;
        line-height: 20px;
        letter-spacing: 0.3px;
        margin: 20px 0;
        color: var(--text-color);
    }

    .container span{
        font-size: 12px;
        color: var(--text-color);
    }

    .container a{
        color: var(--link-color);
        font-size: 13px;
        text-decoration: none;
        margin: 15px 0 10px;
        transition: color 0.3s;
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

    .container button.hidden{
        background-color: transparent;
        border-color: #fff;
    }

    .container .register{
        background-color: var(--card-bg);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex-direction: column;
        padding: 40px 40px;
        overflow-y: auto;
        overflow-x: hidden;
        height: 100%;
        transition: background-color 0.3s;
        scrollbar-width: none;
    }

    .container .login{
        background-color: var(--card-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 40px 40px;
        overflow-y: auto;
        height: 100%;
        transition: background-color 0.3s;
        scrollbar-width: none;
    }

    .container input[type="text"],[type="email"],[type="password"],select{
        background-color: var(--input-bg);
        color: var(--text-color);
        border: 1px solid var(--border-color);
        margin: 8px 0;
        padding: 10px 15px;
        font-size: 13px;
        border-radius: 8px;
        width: 100%;
        outline: none;
        height: 45px;
        overflow: hidden;
        transition: background-color 0.3s, color 0.3s, border-color 0.3s;
    }

    .form-container{
        position: absolute;
        top: 0;
        height: 100%;
        transition: all 0.6s ease-in-out;
    }

    .sign-in{
        left: 0;
        width: 50%;
        z-index: 2;
    }

    .container.active .sign-in{
        transform: translateX(100%);
    }

    .sign-up{
        left: 0;
        width: 50%;
        opacity: 0;
        z-index: 1;
    }

    .container.active .sign-up{
        transform: translateX(100%);
        opacity: 1;
        z-index: 5;
        animation: move 0.6s;
    }

    @keyframes move{
        0%, 49.99%{
            opacity: 0;
            z-index: 1;
        }
        50%, 100%{
            opacity: 1;
            z-index: 5;
        }
    }

    .social-icons{
        margin: 20px 0;
    }

    .social-icons a {
        border: 1px solid var(--social-border);
        border-radius: 10px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin: 0 3px;
        width: max-content;
        height: 40px;
        gap: 5px;
        transition: border-color 0.3s;
        padding: 0px 20px;
        font-size: 15px;
        color: white;
        background: linear-gradient(90deg, var(--primary-light), var(--primary-dark), rgba(160, 25, 23, 1));
    }

    .toggle-container{
        position: absolute;
        top: 0;
        left: 50%;
        width: 50%;
        height: 100%;
        overflow: hidden;
        transition: all 0.6s ease-in-out;
        border-radius: 20px;
        z-index: 1000;
    }

    .container.active .toggle-container{
        transform: translateX(-100%);
        border-radius: 20px;
    }

    .toggle{
        background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        height: 100%;
        color: #fff;
        position: relative;
        left: -100%;
        height: 100%;
        width: 200%;
        transform: translateX(0);
        transition: all 0.6s ease-in-out;
    }

    .container.active .toggle{
        transform: translateX(50%);
    }

    .toggle-panel{
        position: absolute;
        width: 50%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 0 30px;
        text-align: center;
        top: 0;
        transform: translateX(0);
        transition: all 0.6s ease-in-out;
    }

    .toggle-left{
        transform: translateX(-200%);
    }

    .container.active .toggle-left{
        transform: translateX(0);
    }

    .toggle-right{
        right: 0;
        transform: translateX(0);
    }

    .sml-screen #sml-login, #sml-register{ 
        font-weight: 700;    
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    /* Mobile Styles */
    @media(max-width: 601px) {
        .toggle-container{
            display: none;
        }

        body {
            backdrop-filter: none;
        }

        .container{
            display: block;
            background-color: var(--card-bg);
            box-shadow: 0 5px 15px var(--shadow-color);
            position: relative;
            overflow: hidden;
            width: 768px;
            z-index: 0;
            max-width: 100%;
            min-height: 480px;
            height: 100vh;
            border-radius: 0;
        }

        .container button{
            width: 100%;
            height: 45px;
        }

        .container .register{
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: column;
            padding: 40px 40px;
            height: 125%;
        }
        
        .container .login{
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 40px 40px;
            height: 100%;
        }

        .form-container{
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }
        
        .sign-in{
            transform: translateY(0%);
            top: 0;
            width: 100%;
            z-index: 2;
        }

        .container.active .sign-in{
            transform: translateY(100%);
        }

        .sign-up{
            transform: translateY(-100%);
            top: 0;
            width: 100%;
            opacity: 0;
            z-index: 1;
        }

        .container.active .sign-up{
            transform: translateY(0%);
            opacity: 1;
            z-index: 5;
            animation: move 0.6s;
        }

        .container.active .sign-up {
            opacity: 1;
            z-index: 2;
            overflow-y: scroll;
        }
    }

    #remember{
        height: 11px;
        cursor: pointer;
    }

    #forget-password{
        font-size: 11px;
        color: var(--link-color);
    }

    .remember{
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
        width: 100%;
    }

    .remember .remember-checkbox{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: row;
        gap: 5px;
        flex-wrap: nowrap;
        font-size: 11px;
        color: var(--text-color);
    }

    option {
        font-size: 15px;    
        color: var(--text-color);
        padding: 5px;      
        background: var(--input-bg);
    }

    .otp-container {
        display: none;
        justify-content: space-between;
        gap: 5px;
        width: 100%;
    }

    .otp-container input {
        width: 60px;
        padding: 10px;
        text-align: center;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 18px;
        background-color: var(--input-bg);
        color: var(--text-color);
        transition: background-color 0.3s, color 0.3s, border-color 0.3s;
    }

    .otp-container input:focus {
        outline: none;
        border-color: var(--primary-dark);
    }

    .otptxt{
        font-size : 13px;
        margin: 10px;
        color: var(--text-color);
        display: none;
    }

    .error{
        display: none;
        color: var(--error-color);
        width:100%;
        font-size:12px;
        margin: 5px;
    }

    .otp-btn{
        width: 100%;
        margin: 9px;
        color: var(--primary-dark);
        font-size: 13px;
        cursor: pointer;
        display: none;
    }

    .social-icons svg{
        height:25px;
    }

    .bx{
        font-size: 20px;
    }
</style>
</head>

<body>

    <!-- <div class="animate-shape">
        <img src="https://i.ibb.co/SZsLy9f/animate-shape.png" alt=" ">
    </div> -->
    
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="" method="post" class="register" autocomplete="on">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <a href="<?php echo $login_url; ?>" class="icons"><i class='bx bxl-google'></i> Continue with Google</a>
                    <!-- <a href="#" class="icons"><i class='bx bxl-facebook'></i></a>
                    <a href="#" class="icons"><i class='bx bxl-github'></i></a>
                    <a href="#" class="icons"><i class='bx bxl-linkedin'></i></a> -->
                </div>
                <span>Register with E-mail</span>
                
                <select name="role" id="role" required>
                    <option value="" selected disabled>Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
                <input type="text" placeholder="First Name" required name="fname" id="fname">
                <input type="text" placeholder="Last Name" required name="lname" id="lname">
                <input type="text" placeholder="phone Number" required name="phone" maxlength="10" minlength="10" id="phone">
                
                    <div id="error-phone" class="error"></div>
                
                <input type="email" placeholder="E-mail" required name="email" id="email" autocomplete="username">
                
                    <div id="error-email" class="error"></div>
                <button name="send_otp" onclick="sendotp(event)" type="button" id="sendOTP" disabled style="opacity: 0.5;">Send OTP Via Email</button>
                <label class="otptxt" for="otp">Enter OTP : </label>
                <div class="otp-container">
                    <input type="number" name="otp1" maxlength="1" id="otp1" oninput="moveFocus(this, 'otp2', 'next')" onkeydown="handleBackspace(event, this, 'otp1')" />
                    <input type="number" name="otp2" maxlength="1" id="otp2" oninput="moveFocus(this, 'otp3', 'next')" onkeydown="handleBackspace(event, this, 'otp2')" />
                    <input type="number" name="otp3" maxlength="1" id="otp3" oninput="moveFocus(this, 'otp4', 'next')" onkeydown="handleBackspace(event, this, 'otp3')" />
                    <input type="number" name="otp4" maxlength="1" id="otp4" oninput="limitLength(this)" onkeydown="handleBackspace(event, this, 'otp4')" />
                </div>
                <div id="error-otp" class="error"></div>
                <div class="otp-btn" id="otp-btn"></div>
                
                <input type="password" name="password" placeholder="Password"  id="password" class='password' autocomplete="password">
                
                    <div id="error-password" class="error"></div>
                
                <input type="password" name="password2" placeholder="Re-Enter Password"  id="password2" class='password' autocomplete="new-password">

                <div class='toogle-pass'><input type="checkbox" id="showPass" onclick="showPassword()"> Show Password</div>
                <div id="error-empty" class="error"></div>

                <button onclick="signup(event)" type="submit" id="signup-btn">Sign Up</button>
                <p class="sml-screen">Already have account? <a href="#" id="sml-register">Sign In</a></p>
            </form>
        </div>


        <div class="form-container sign-in">
            <form action="" method="post" class="login" autocomplete="on">
                <h1>Sign In</h1>
                <div class="social-icons">
                    <a href="<?php echo $login_url; ?>" class="icons"><i class='bx bxl-google'></i>  Continue with Google</a>
                    <!-- <a href="#" class="icons"><i class='bx bxl-facebook'></i></a>
                    <a href="#" class="icons"><i class='bx bxl-github'></i></a>
                    <a href="#" class="icons"><i class='bx bxl-linkedin'></i></a> -->
                </div>
                <span>Login With Email & Password</span>
                <input id="login-email" type="email" placeholder="Enter Email" name="email" autocomplete="email" required>
                <div class="password-container">
                    <input id="login-password" type="password" placeholder="Enter Password" name="password" autocomplete="current-password" required>
                    <span onclick="togglePassword()" id="eyeBtn"><i class="fa-solid fa-eye" id="eyeBtn" 
                    style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); cursor: pointer;font-size: 14px;"></i></span>
                </div>
                <div class="remember">
                        <label class="remember-checkbox" for="remember"><input type="checkbox" name="remember" id="remember">Remember Me</label> 
                    
                        <a href="./forget-password.php" id="forget-password">Forget Password?</a>
                </div>
                <div id="error-invalid" class="error"></div>
                <button onclick="processLogin(event)" name="login" type="submit">Sign In</button>
                <p class="sml-screen">Don't have an account? <a href="#" id="sml-login">Sign Up</a></p>
            </form>
        </div>


        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome To <br>LiveStrike</h1>
                    <p>Already have account?</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Welcome To <br>LiveStrike</h1>
                    <p>Don't have an account?</p>
                    <button class="hidden" id="register">Sign Up</button>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("login-password");
            var eyeIcon = document.getElementById("eyeBtn");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><!--Boxicons v3.0 https://boxicons.com | License  https://docs.boxicons.com/free--><path d="M12 17c-5.35 0-7.42-3.84-7.93-5 .2-.46.65-1.34 1.45-2.23l-1.4-1.4c-1.49 1.65-2.06 3.28-2.08 3.31-.07.21-.07.43 0 .63.02.07 2.32 6.68 9.95 6.68.91 0 1.73-.1 2.49-.26l-1.77-1.77c-.24.02-.47.03-.72.03ZM21.95 12.32c.07-.21.07-.43 0-.63-.02-.07-2.32-6.68-9.95-6.68-1.84 0-3.36.39-4.61.97L2.71 1.29 1.3 2.7l4.32 4.32 1.42 1.42 2.27 2.27 3.98 3.98 1.8 1.8 1.53 1.53 4.68 4.68 1.41-1.41-4.32-4.32c2.61-1.95 3.55-4.61 3.56-4.65m-7.25.97c.19-.39.3-.83.3-1.29 0-1.64-1.36-3-3-3-.46 0-.89.11-1.29.3l-1.8-1.8c.88-.31 1.9-.5 3.08-.5 5.35 0 7.42 3.85 7.93 5-.3.69-1.18 2.33-2.96 3.55z"></path></svg>'
                
            } else {
                passwordInput.type = "password";
                eyeIcon.innerHTML = '<i class="fa-solid fa-eye" id="eyeBtn" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); cursor: pointer;font-size: 14px;"></i>';
                
            }
        }

        function showPassword() {
            var passwordInputs = document.querySelectorAll(".password");
            var checkbox = document.getElementById("showPass");

            passwordInputs.forEach(function(input) {
                input.type = checkbox.checked ? "text" : "password";
            });
        }

        const container = document.getElementById('container');
        const body = document.querySelector("body");
        const form_container = document.querySelector('.form-container');
        if(window.innerWidth > 600){
            container.style.display = 'none';
            
        }

        setTimeout(()=>{
            if(window.innerWidth > 600){
                container.style.display = 'block';
                body.style.background = 'url(https://i.ibb.co/q34NF7rR/loginBG.jpg)';
            }
        },10)
    
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');
    
        const smlRegisterBtn = document.getElementById('sml-register');
        const smlLoginBtn = document.getElementById('sml-login');
    
        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });
    
        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });
    
        
        smlRegisterBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });
    
        smlLoginBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        function moveFocus(current, nextId, direction) {
            limitLength(current)
            if (direction === 'next' && current.value.length === 1) {
                
                const nextInput = document.getElementById(nextId);
                if (nextInput) nextInput.focus();
            }
        }

        function limitLength(input) {
            if (input.value.length > 1) {
                input.value = input.value.slice(0, 1);
            }
        }

    function handleBackspace(event, current, currentId) {
      if (event.key === "Backspace" && current.value === "") {
        const prevInput = current.previousElementSibling;
        if (prevInput) prevInput.focus();
      }
    };

    function processLogin(e){
        e.preventDefault();
        console.log("Login");
        const email = (document.getElementById('login-email').value).trim();
        const password = document.getElementById('login-password').value;
        const checkbox = document.getElementById('remember').checked

        let login_data = {
            'email': email,
            'password': password,
            'checkbox': checkbox
        }

        document.querySelectorAll('[id^="error-"]').forEach((el) => {
        el.innerHTML = '';
        el.style.display = 'none';
        });

        if(email === '' || password === ''){
            let el = document.getElementById('error-invalid');
            el.innerHTML = "Please fill all fields";
            el.style.display = 'block';
        }else{
            fetch("./login.php",{
                method: 'POST',
                body: JSON.stringify(login_data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).
            then((response)=>{
                return response.json();
            })
            .then((data)=>{
                if(data.status !== 200){
                    let el = document.getElementById('error-invalid');
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }
                
                console.log(data)

                if(data.status === 200){
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = './dashboard.php';

                    iframe.onload = function () {

                    window.location.href = './dashboard.php?update="live"&sport="CRICKET"';
                    };

                    document.body.appendChild(iframe);
                }
            })
            .catch((error)=>{
                let el = document.getElementById('error-invalid');
                el.innerHTML = error;
                el.style.display = 'block';
                console.log(error)
            })

        }
            
    }

        function signup(e) {
            e.preventDefault(); 
            const role = (document.getElementById('role').value).trim();
            const fname = (document.getElementById('fname').value).trim();
            const lname = (document.getElementById('lname').value).trim();
            const email = (document.getElementById('email').value).trim();

            const otp1 = document.getElementById('otp1').value;
            const otp2 = document.getElementById('otp2').value;
            const otp3 = document.getElementById('otp3').value;
            const otp4 = document.getElementById('otp4').value;

            const otp = (otp1 + otp2 + otp3 + otp4).trim();

            const password = document.getElementById('password').value;
            const password2 = document.getElementById('password2').value;
            const phone = (document.getElementById('phone').value).trim();

            let formdata = {
                'role' : role,
                'fname' : fname,
                'lname' : lname,
                'email' : email,
                'otp' : otp,
                'password' : password,
                'password2' : password2,
                'phone' : phone
                
            };
            
            fetch('./register.php',{
                method: 'POST',
                body: JSON.stringify(formdata),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then((response)=>{
                return response.json();
            })
            .then((data)=>{
                document.querySelectorAll('[id^="error-"]').forEach((el) => {
                    el.innerHTML = '';
                    el.style.display = 'none';
                });

                if(data.status === 200){
                    alert(`${email} User Registered Successfully!`)
                    window.location.href = './front-page.php';

                        let scorerData = {
                            for_value: 'new_user',
                            game: '',
                            venue: '',
                            time: '',
                            password: '',
                            date: '',
                            recipient_email: email
                        };
                        const scorerBlob = new Blob([JSON.stringify(scorerData)], { type: 'application/json' });
                        navigator.sendBeacon('./mail.php', scorerBlob);
                    console.log('mail sent');
                    
                }else{
                    let el = document.getElementById('error-'+data.field);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }

                console.log(data.message);
            })
            .catch((error)=>{console.log(error)});
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

            async function sendotp(e) {
                let send_btn = document.getElementById('sendOTP');
                e.preventDefault();

                var role = document.getElementById('role').value;
                var email = document.getElementById('email').value;
                var fname = document.getElementById('fname').value;
                var lname = document.getElementById('lname').value;

                var data_for_OTP = {
                    'for' : 'registration',
                    'role': role,
                    'email': email,
                    'fname': fname,
                    'lname': lname
                };

                if (role == '' || email == '' || fname == '' || lname == '') {
                    var role_error = document.getElementById('error-email');
                    role_error.style.display = 'block';
                    role_error.innerText = 'Please fill all the required fields';
                } else {
                    send_btn.innerText = 'Proccessing...';
                    fetch('./OTP-mail.php', {
                        method: 'POST',
                        body: JSON.stringify(data_for_OTP),
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
                             

                            if(!send_btn){
                                console.error("Send OTP button not found");
                                return;
                            }

                            send_btn.innerText = 'SEND OTP VIA EMAIL';
                            send_btn.setAttribute('disabled', 'true');
                            send_btn.style.opacity = '0.5';

                            let email_error = document.getElementById('error-email');
                            email_error.style.display = 'block';
                            email_error.innerHTML = data.message;
                        }else{
                            sent();
                            alert(`OTP sent successfully! on ${email}`); 
                            send_btn.innerText = 'SEND OTP VIA EMAIL';
                        }
                        console.log(data);
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                }
            }


            function sent(){
                

                let send_again_btn = document.getElementById('otp-btn');
                let send_btn = document.getElementById('sendOTP'); 
                let otp_container = document.querySelector('.otp-container');
                let otp_txt = document.querySelector('.otptxt');
                otp_container.style.display = 'flex';
                otp_txt.style.display = 'block';

                if(!send_btn){
                    console.error("Send OTP button not found");
                    return;
                }

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
                        send_again_btn.innerHTML = "<span class='sendagain' onclick='sendotp(event)'>Resend OTP</span>";
                    }
                }, 1000);
            };

// Disable right-click
  //document.addEventListener('contextmenu', event => event.preventDefault());

  // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
  document.onkeydown = function(e) {
    if(e.keyCode == 123) return false; // F12
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
    if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
  }
    </script>
</body>

</html>