<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Add Event</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
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
            --gradient: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
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
            --gradient: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
            --invert: invert(1);
        }
        svg path {
            fill : var(--text-color);
        }


        body{
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            flex-direction: column;
            background-color: var(--background);
            color: var(--text-dark);
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
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
            width: 100%;
        }
        .txt{
            line-height: 20px;
            color: var(--text-dark);
        }
        .save-btn{
            width: 96px;
            height: 40px;
            display: flex;
            border-radius: 50px;
            align-items: center;
            justify-content: center;
            background: var(--gradient);
            color: #fff;
        }
        .get-info{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            flex-wrap: wrap;
        }
        .info{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 50px;
            overflow: hidden;
        }
        .schedule{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            width: 100%;
            gap: 15px;
        }
        .check{
            width: 100%;
            position: relative;
        }
        .time{
            width: 100%;
            display: flex;
            gap: 15px;
            flex-direction: row;
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
        #flexSwitchCheckChecked.form-check-input{
            height: 25px;
            width: 50px;
        }
        #flexSwitchCheckChecked.form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-light);
        }
        .form-check-input:focus {
            border-color: var(--primary-color);
            outline: 0;
            box-shadow: 0 0 0 .25rem rgba(209, 34, 31, 0.22);
        }
        .form-check-input{
            border: 1px solid var(--primary-color);
            transition: background-position .5s ease-in-out;
        }
        .form-switch{
            position: absolute;
            right: 3px;
            top: 15px;
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
        .input-fields input:valid + label,
        .input-fields input:focus + label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: var(--primary-color);
        }
        .input-fields textarea:valid + label,
        .input-fields textarea:focus + label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: var(--primary-color);
        }
        #dateInput input:valid + #date {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: var(--primary-color);
        }
        .event-time{
            display: block;
            transition : width 0.5s ease-in-out;
        }
        .event-time.active{
            width: 0;
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

        @media (min-width:601px) {
             .container{
                display: flex;
                background-color: var(--card-bg);
                position: relative;
                overflow-x: hidden;
                width: 90%;
                max-width: 100%;
                min-height: 100vh;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                padding: 40px;
                scrollbar-width: none;
            }
            .container2{
                gap: 60px;
                width: 70%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="datetime"],[type="time"],[type="date"],select{
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
        }

        @media(max-width: 601px) {
            .container{
                display: flex;
                background-color: var(--card-bg);
                position: relative;
                overflow-x: hidden;
                width: 768px;
                z-index: 0;
                max-width: 100%;
                min-height: 480px;
                padding: 40px 40px;
                height: 100vh;
                align-items: flex-start;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
            }
            .container2{
                gap: 40px;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="datetime"],[type="time"],[type="date"],select{
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="return">
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg></div>
            <div class="save-btn">Save</div>
        </div>
        
        <div class="container2">
            <div class="txt">
                <label for="">Team Name</label>
                <h4>Add Event</h4>
            </div>
            <div class="get-info">
                <div class="info">
                    <div class="schedule">
                        <div class="input-fields"><input type="date" id="dateInput" placeholder="Select Date" required><label for="dateInput" id="date">Date</label></div>
                        <div class="input-fields event-time"><input type="time" id="timeInput" placeholder="Select Time" required><label for="timeInput" id="time">Time</label></div>
                    </div>
                    <div class="input-fields"><input type="text" name="" id="opponent" required><label for="opponent">Opponent</label></div>
                    <div class="input-fields"><input type="text" name="" id="location" required><label for="location">Location</label></div>
                    <div class="check">
                        <input type="text" name="" id="" value="" placeholder="Full Day Event" readonly disabled>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" onclick="checkStatus()">
                        </div>
                    </div>
                    <div class="time">
                        <div class="input-fields"><input type="number" name="" id="duration" required><label for="duration">Duration</label></div>
                        <div class="input-fields"><input type="datetime" name="" id="arrival" required><label for="arrival">Arrival</label></div>
                    </div>
                    <div class="input-fields"><textarea name="" id="notes" placeholder="Description(Optional)" required></textarea></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function goBack() {
            window.history.back();
        }

        //manage textarea
        const textarea = document.getElementById('notes');

        textarea.addEventListener('input', function(){
            this.style.height = 'auto';  // reset height
            this.style.height = (this.scrollHeight) + 'px';  // set new height
        });

        function checkStatus(){
            const checkbox = document.getElementById('flexSwitchCheckChecked');
            const timeInput = document.querySelector('.event-time');
            if(checkbox.checked){
                timeInput.classList.add('active');
                console.log('Checkbox is checked');
            } else {
                console.log('Checkbox is unchecked');
                timeInput.classList.remove('active');
            }
        }

        document.querySelector('.save-btn').addEventListener('click',()=>{
            window.location.href = './team-info.php';
        })

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