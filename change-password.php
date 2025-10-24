<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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

    .toogle-pass {
        width: 100%;
        gap: 9px;
        font-size: 12px;
        display: flex;
        align-items: center;
        flex-direction: row;
        color: var(--text-dark);
    }

    .error {
        display: none;
        color: var(--primary-red);
        width: 100%;
        font-size: 12px;
        margin: 5px;
    }

    .success {
        display: none;
        color: #28a745;
        width: 100%;
        font-size: 12px;
        margin: 5px;
        text-align: center;
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
        margin-bottom: 40px;
        color: var(--text-dark);
        text-align: center;
    }

    .password-strength {
        width: 100%;
        margin: 5px 0;
        font-size: 12px;
    }

    .strength-bar {
        width: 100%;
        height: 4px;
        background: var(--border-color);
        border-radius: 2px;
        margin-top: 5px;
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease, background 0.3s ease;
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

        <form id="changePasswordForm">
            <h1>Change Password</h1>
            
            <input type="password" name="current_password" placeholder="Current Password" id="current_password" class='password' required>
            <div id="error-current_password" class="error"></div>
            
            <input type="password" name="new_password" placeholder="New Password" id="new_password" class='password' required>
            <div class="password-strength">
                <div id="password-strength-text"></div>
                <div class="strength-bar">
                    <div class="strength-fill" id="password-strength-bar"></div>
                </div>
            </div>
            <div id="error-new_password" class="error"></div>
            
            <input type="password" name="confirm_password" placeholder="Confirm New Password" id="confirm_password" class='password' required>
            <div id="error-confirm_password" class="error"></div>
            
            <div class='toogle-pass'>
                <input type="checkbox" id="showPass" onclick="showPassword()"> Show Password
            </div>
            
            <div id="success-message" class="success"></div>
            <div id="error-general" class="error"></div>
        </form>
        
        <div class="submit-btn">
            <button onclick="changePassword(event)" type="submit" id="change-btn" name="change_password">Change Password</button>
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

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = "";

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength++;

            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');

            switch(strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '20%';
                    strengthBar.style.background = '#dc3545';
                    strengthText.textContent = 'Weak';
                    strengthText.style.color = '#dc3545';
                    break;
                case 2:
                case 3:
                    strengthBar.style.width = '50%';
                    strengthBar.style.background = '#ffc107';
                    strengthText.textContent = 'Medium';
                    strengthText.style.color = '#ffc107';
                    break;
                case 4:
                    strengthBar.style.width = '80%';
                    strengthBar.style.background = '#28a745';
                    strengthText.textContent = 'Strong';
                    strengthText.style.color = '#28a745';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.style.background = '#28a745';
                    strengthText.textContent = 'Very Strong';
                    strengthText.style.color = '#28a745';
                    break;
            }
        }

        // Real-time password strength checking
        document.getElementById('new_password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        let changePassword = (e) => {
            e.preventDefault();
            
            let currentPassword = document.getElementById('current_password').value;
            let newPassword = document.getElementById('new_password').value;
            let confirmPassword = document.getElementById('confirm_password').value;
            
            let data = {
                'current_password': currentPassword,
                'new_password': newPassword,
                'confirm_password': confirmPassword
            }

            // Clear previous messages
            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });
            document.getElementById('success-message').style.display = 'none';

            // Basic client-side validation
            if (newPassword !== confirmPassword) {
                let el = document.getElementById('error-confirm_password');
                el.innerHTML = 'Passwords do not match';
                el.style.display = 'block';
                return;
            }

            if (newPassword.length < 8) {
                let el = document.getElementById('error-new_password');
                el.innerHTML = 'Password must be at least 8 characters long';
                el.style.display = 'block';
                return;
            }

            // Simulate API call - replace with your actual endpoint
            fetch("./Backend/change_password.php", {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then((data) => {
                if(data.status === 200){
                    document.getElementById('success-message').innerHTML = data.message || 'Password changed successfully!';
                    document.getElementById('success-message').style.display = 'block';
                    
                    // Clear form
                    document.getElementById('changePasswordForm').reset();
                    document.getElementById('password-strength-bar').style.width = '0%';
                    document.getElementById('password-strength-text').textContent = '';
                    
                    // Optionally redirect after success
                    // setTimeout(() => {
                    //     window.location.href = './dashboard.php';
                    // }, 2000);
                    
                } else {
                    let el = document.getElementById('error-' + (data.field || 'general'));
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }
            })
            .catch(error => {
                console.log(error);
                let el = document.getElementById('error-general');
                el.innerHTML = 'An error occurred. Please try again.';
                el.style.display = 'block';
            });
        };

        // Disable right-click
        // document.addEventListener('contextmenu', event => event.preventDefault());

        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        document.onkeydown = function(e) {
            if(e.keyCode == 123) return false; // F12
            if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
            if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
            if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
        }

        // Theme management functions
        function initializeTheme() {
            // Check for saved theme preference or use system preference
            const currentTheme = localStorage.getItem('theme') || 
                                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            
            // Set the initial theme
            setTheme(currentTheme, false);

            // Add event listener to theme toggle if it exists
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('change', function() {
                    if (this.checked) {
                        setTheme('dark');
                    } else {
                        setTheme('light');
                    }
                });
            }
        }

        // Listen for theme changes from other tabs/windows
        function setupThemeSync() {
            window.addEventListener('storage', function(e) {
                if (e.key === 'theme') {
                    setTheme(e.newValue, false);
                }
            });
        }

        function setTheme(theme, save = true) {
            const logo = document.querySelector('.logo-img img');
            
            if (theme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                if (save) localStorage.setItem('theme', 'dark');
                if (document.getElementById('theme-toggle')) {
                    document.getElementById('theme-toggle').checked = true;
                }
                if (logo) logo.src = "./assets/images/toggle-logo.png";
            } else {
                document.body.setAttribute('data-theme', 'light');
                if (save) localStorage.setItem('theme', 'light');
                if (document.getElementById('theme-toggle')) {
                    document.getElementById('theme-toggle').checked = false;
                }
                if (logo) logo.src = "./assets/images/logo.png";
            }
            
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: theme }));
        }

        function getCurrentTheme() {
            return document.body.getAttribute('data-theme') || 'dark';
        }

        // Initialize theme when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            setupThemeSync();
        });
    </script>
</body>
</html>