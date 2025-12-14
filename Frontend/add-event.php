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
            --input-bg: #1e1e1e;
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

        .add-btn{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
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
                </svg></div>
            <!-- <div class="save-btn">Save</div> -->
        </div>
        
        <div class="container2">
            <div class="txt">
                <!-- <label for="">Team Name</label> -->
                <h4>Add Event</h4>
            </div>
            <div class="get-info">
                <div class="info">
                    <div class="container3">
                    <div class="logo-img">
                        <div class="img-container">
                            <div class="image">
                                <img  id="previewImage" src="../assets/images/new_event_img.png" alt="">
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
                    <div class="input-fields"><input type="text" name="" id="name" required><label for="opponent">Event Name</label></div>
                    <div class="schedule">
                        <div class="input-fields"><input type="date" id="dateInput" placeholder="Select Date" required><label for="dateInput" id="date">Start Date</label></div>
                        <div class="input-fields"><input type="date" id="dateInput" placeholder="Select Date" required><label for="dateInput" id="date">End Date</label></div>
                    </div>
                    <div class="input-fields"><input type="text" name="" id="location" required><label for="location">Location</label></div>
                    <!-- Organizer / Department -->
                    <div class="input-fields">
                        <input type="text" id="organizer" name="organizer" required>
                        <label for="organizer">Organizer / Department</label>
                    </div>

                    <!-- Coordinator Name -->
                    <div class="input-fields">
                        <input type="text" id="coordinator" name="coordinator" required>
                        <label for="coordinator">Coordinator Name</label>
                    </div>

                    <!-- Contact Number -->
                    <div class="input-fields">
                        <input type="text" id="coordinator" name="coordinator" required>
                        <label for="coordinator">Contact Number</label>
                    </div>

                    <!-- Email -->
                    <div class="input-fields">
                        <input type="email" id="email" name="email" required>
                        <label for="email">Email</label>
                    </div>

                    <!-- Event Type -->
                    <div class="input-fields">
                        <select id="event_type" name="event_type" required>
                            <option value="" disabled selected hidden>Select Event Type</option>
                            <option value="intra">Intra-College</option>
                            <option value="inter">Inter-College</option>
                            <option value="zonal">Zonal</option>
                            <option value="national">National</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="input-fields">
                        <input type="text" id="sponsor" name="sponsor" required>
                        <label for="sponsor">Sponsorship Partner (Optional)</label>
                    </div>
                    <!-- <div class="check">
                        <input type="text" name="" id="" value="" placeholder="Full Day Event" readonly disabled>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" onclick="checkStatus()">
                        </div>
                    </div> -->
                    <!-- <div class="time">
                        <div class="input-fields"><input type="number" name="" id="duration" required><label for="duration">Duration</label></div>
                        <div class="input-fields"><input type="datetime" name="" id="arrival" required><label for="arrival">Arrival</label></div>
                    </div> -->
                    <div class="input-fields"><textarea name="" id="notes" placeholder="Description(Optional)" required></textarea></div>

                    <!-- <div class="input-fields file-field">
                        <input type="file" id="poster" name="poster" accept="image/*" required>
                        <label for="poster">Upload Event Poster</label>
                    </div> -->

                    

                    <div class="add-btn">
                        <button onclick="add_tournament(event)" type="submit" id="add-event">Next (Add TOURNAMENTS)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function goBack() {
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

        // document.querySelector('.save-btn').addEventListener('click',()=>{
        //     window.location.href = './team-info.php';
        // })

        // Disable right-click
  //document.addEventListener('contextmenu', event => event.preventDefault());

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