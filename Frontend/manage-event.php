<?php
    session_start();
    if(!isset($_SESSION['user'])){
        header('location: ./front-page.php');
        exit();
    }else{
        $user_id = $_SESSION['user'];
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
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        // Apply stored theme instantly before the page renders
        (function() {
            const theme = localStorage.getItem('theme') ||
                            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            
            // Apply theme attributes early to avoid white flash
            document.documentElement.setAttribute('data-theme', theme);
            document.body?.setAttribute('data-theme', theme);

            // Wait for the logo to exist, then update it
            const checkLogo = setInterval(() => {
                const logo = document.querySelector('.logo-img img');
                if (logo) {
                    logo.src = theme === 'dark'
                        ? "../../assets/images/toggle-logo.png"
                        : "../../assets/images/logo.png";
                    clearInterval(checkLogo);
                }
            }, 50);
        })();
    </script>
    <title>Manage Tournaments</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
        user-select: none;
        scrollbar-width: none;
    }
    
    :root {
        --primary-color: rgba(209, 34, 31, 1);
        --primary-light: rgba(209, 34, 31, 0.8);
        --primary-dark: rgba(160, 25, 23, 1);
        --background: #ffffff;
        --text-dark: #000000;
        --text-light: #666666;
        --border-color: #e0e0e0;
        --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --card-bg: #ffffff;
        --hover-bg: rgba(209, 34, 31, 0.1);
        --selected-bg: rgba(209, 34, 31, 0.15);
        --nav-fill: #ffffffff;
    }

    [data-theme="dark"] {
        --background: #121212;
        --text-dark: #ffffff;
        --text-light: #a0a0a0;
        --border-color: #333333;
        --shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        --card-bg: #1e1e1e;
        --hover-bg: rgba(209, 34, 31, 0.2);
        --selected-bg: rgba(209, 34, 31, 0.25);
        --nav-fill: #2d2d2d;
    }
    
    svg path {
        fill : var(--text-dark);
    }
    body {
        height: 100vh;
        height: -webkit-fill-available;
        background: var(--background);
        color: var(--text-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease, color 0.3s ease;
    }
    
    .container {
        display: flex;
        background-color: var(--card-bg);
        position: relative;
        width: 90%;
        max-width: 1200px;
        height: 90vh;
        max-height: 900px;
        align-items: flex-start;
        justify-content: flex-start;
        flex-direction: column;
        gap: 20px;
        border-radius: 20px;
        padding: 20px;
        overflow: hidden;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    .container2 {
        height: 100%;
        width: 100%;
    }
    
    .return {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 10px 20px 10px;
    }
    
    .return svg {
        cursor: pointer;
        transition: transform 0.2s ease;
        color: var(--text-dark);
        fill: var(--text-dark);
    }
    
    .return svg:hover {
        transform: translateX(-3px);
        color: var(--primary-color);
        fill: var(--primary-color);
    }
    
    .txt {
        line-height: 20px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 10px;
        color: var(--text-dark);
        font-size: 18px;
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }
    
    .game-container {
        width: 100%;
        background: var(--nav-fill);
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        overflow: hidden;
        transition: background 0.3s ease;
    }
    
    .game-list {
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        height: 70px;
        overflow-x: auto;
        padding: 0 5px;
        gap: 10px;
    }
    
    .game {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 86px;
        padding: 10px;
        gap: 8px;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 10px;
    }
    
    .game:hover {
        background: var(--hover-bg);
    }
    
    .game.selected {
        background: var(--selected-bg);
        border-radius: 10px;
    }
    
    .game p {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-dark);
        text-align: center;
        text-wrap: auto;
        transition: color 0.3s ease;
    }
    
    .game svg {
        height: 24px;
        width: 24px;
        fill: var(--text-dark);
        transition: fill 0.3s ease;
    }
    
    .game.selected p {
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .game.selected svg {
        fill: var(--primary-color);
    }
    
    .team-container {
        width: 100%;
        height: calc(100% - 150px);
        padding-bottom: 20px;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        overflow-y: auto;
    }
    
    .team-list {
        width: 100%;
        height: max-content;
        gap: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        justify-content: center;
        justify-items: center;
        align-items: start;
        padding: 10px;
        margin-bottom: 20px;
    }
    
    .team {
        width: 100%;
        max-width: 300px;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 15px;
        color: var(--text-dark);
        border: 1px solid var(--border-color);
        padding: 15px;
        border-radius: 15px;
        background: var(--nav-fill);
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .team:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }
    
    .selected-team {
        border: 2px solid var(--primary-color);
        background: var(--selected-bg);
    }
    
    .logo {
        height: 70px;
        width: 70px;
        background: var(--card-bg);
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    
    .logo img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    
    .team-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        width: calc(100% - 85px);
    }
    
    .team-info h4 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color 0.3s ease;
    }
    
    .team-info label.data {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 5px;
        font-size: 13px;
        color: var(--text-light);
        width: 100%;
        transition: color 0.3s ease;
    }
    
    label.data .dt {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 180px;
    }
    
    .no-data {
        color: var(--text-light);
        font-style: italic;
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px 0;
        transition: color 0.3s ease;
    }
    
    .plus {
        position: fixed;
        bottom: 40px;
        right: 40px;
        border: none;
        border-radius: 50%;
        display: flex;
        font-size: 24px;
        height: 60px;
        cursor: pointer;
        width: 60px;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(209, 34, 31, 0.3);
        background: var(--primary-color);
        color: white;
        transition: all 0.2s ease;
        z-index: 10;
    }
    
    .plus:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 20px rgba(209, 34, 31, 0.4);
        background: var(--primary-dark);
    }
    
    .plus:active {
        transform: translateY(0);
    }
    
    .add-btn {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }
    
    .add-btn button {
        background: var(--primary-color);
        color: white;
        font-size: 14px;
        padding: 12px 30px;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        cursor: pointer;
        height: 45px;
        width: 150px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(209, 34, 31, 0.2);
    }
    
    .add-btn button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(209, 34, 31, 0.3);
        background: var(--primary-dark);
    }
    
    .add-btn button:active {
        transform: translateY(0);
    }

    @media (max-width: 600px) {
        .container {
            width: 100%;
            height: 100vh;
            border-radius: 0;
            padding: 20px 15px;
            box-shadow: none;
        }
        
        .team-list {
            grid-template-columns: 1fr;
        }
        
        .team {
            max-width: 100%;
        }
        
        .plus {
            bottom: 30px;
            right: 30px;
            height: 55px;
            width: 55px;
        }
    }

    /* Custom scrollbar */
    .team-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .team-container::-webkit-scrollbar-track {
        background: var(--hover-bg);
        border-radius: 10px;
    }
    
    .team-container::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }
    
    .team-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }
    
    .game-list::-webkit-scrollbar {
        height: 4px;
    }
    
    .game-list::-webkit-scrollbar-track {
        background: var(--hover-bg);
        border-radius: 10px;
    }
    
    .game-list::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }
    
    .game-list::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }
</style>
</head>
<body>
    <div class="container">
        <div class="return" >
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div>
                
            </div>
        </div>
        <div class="container2">
            <div class="txt">
                <div>
                    <label for="">My Events</label>
                    <h4>Manage Events</h4>
                </div>
                <div>
                
                </div>
            </div>
           
            <div class="team-container">
               
            </div>
        </div>
        <?php
            if($_SESSION['role']=="Admin"){
                echo '<div class="pls">';
                    echo '<div class="plus">';
                        echo '<div class="plus-icon"><i class="bx bx-plus bx-rotate-180"></i></div>';
                    echo '</div>';
                echo '</div>';
            }?>
    </div>
    <script>
        let urlParams = new URLSearchParams(window.location.search);
        const tournament = urlParams.get('tournament');
       

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

        //display content as per user's selection(Status & Sport)
         function loadgames() {

            let data = {
                update : '',
                sport : '',
                for : 'manage_events'
            }

            displayContent(data);

            function displayContent(data) {
                fetch('../update_data.php',{
                    method: 'post',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    let info_container = document.querySelector('.team-container');
                    info_container.innerHTML = data;
                })
                .catch(error => console.error(error))
            }
        }

        loadgames();

        let get_event_info = (el)=>{
            console.log("Hello...");
            const eventID = el.getAttribute('data-event_id');
            window.location.href = `event-info.php?t=${eventID}`;
        }

        //go to previous page
        let goBack = ()=>{
            window.history.back();
        }

        document.querySelector('.plus').addEventListener('click',()=>{
            window.location.href = "./add-event.php";
        })

        let save = () =>{
            window.location.href = `./match-making.php?t=${tournament}&teams=${selectedTeams}`;

        }

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