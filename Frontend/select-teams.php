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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <title>Schedule Match</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
        }
        :root {
            --primary-color: rgb(255 5 0);
            --primary-light: rgba(209, 34, 31, 0.8);
            --primary-dark: rgba(160, 25, 23, 1);
            --primary-transparent: rgba(212, 53, 50, 0.15);
            --background: #ffffff;
            --text-color: #000000;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
            --border-color: #dddddd;
            --special-color: #e3e3e3;
            --hover-bg: rgba(209, 34, 31, 0.08);

        }

        [data-theme="dark"] {
            --background: #121212;
            --text-color: #ffffff;
            --light-bg: #1e1e1e;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            --border-color: #333333;
            --primary-transparent: rgba(210, 31, 28, 0.29);
            --special-color: #3f3f3f;
            --hover-bg: rgba(209, 34, 31, 0.8);
        }
        body{
            background-color: var(--background);
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

        svg path {
            fill : var(--text-color);
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

        .team-name {
            color: var(--text-color);
        }
        .txt{
            color: var(--text-color);
            line-height: 20px;
        }
        
        .container3{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 50px;
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
            width: 150px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background:transparent;
            color:var(--text-color);
        }
        .img-container{
            height: 100px;
            width: 100px;
            background: var(--hover-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            border : 2px solid #d9d9d9;
        }
        .img-container img{
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .vs{
            font-size: 25px;
            font-weight: bold;
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
        }
        .match-frame.active{
            bottom: 0;
        }

        @media (min-width:601px) {
             .container{
                display: flex;
                background-color: var(--background);
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
            
        }

        @media(max-width: 601px) {
            .container{
                display: flex;
                background-color: var(--background);
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
                    <?php
                        if(isset($team1)){
                            $sql = "SELECT * FROM teams WHERE t_id = '$team1'";
                            $query = mysqli_query($conn,$sql);
                            $row = mysqli_fetch_assoc($query);
                    ?>
                    <div class="container4" id="team1">
                        <div class="img-container"><?php if(!($row)){ ?><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.35799 13.2757L13.2757 13.2757L13.2757 2.41452C13.268 2.16688 13.3104 1.92024 13.4003 1.68938C13.4902 1.45852 13.6258 1.24819 13.799 1.07101C14.1741 0.69594 14.6828 0.485226 15.2132 0.485227C15.7436 0.485227 16.2523 0.69594 16.6274 1.07101C16.8041 1.2411 16.9442 1.4455 17.039 1.67167C17.1338 1.89783 17.1815 2.141 17.179 2.38623L17.1082 13.3181L28.1108 13.3181C28.5719 13.313 29.0174 13.4852 29.3553 13.7989C29.7304 14.174 29.9411 14.6827 29.9411 15.2131C29.9411 15.7436 29.7304 16.2523 29.3553 16.6274C29.1826 16.8151 28.9727 16.9648 28.7389 17.0671C28.5052 17.1693 28.2528 17.2219 27.9977 17.2213L17.1507 17.1506L17.1507 28.0401C17.1546 28.283 17.1104 28.5243 17.0206 28.75C16.9308 28.9757 16.7971 29.1815 16.6274 29.3553C16.2523 29.7304 15.7436 29.9411 15.2132 29.9411C14.6828 29.9411 14.1741 29.7304 13.799 29.3553C13.6223 29.1852 13.4822 28.9808 13.3874 28.7546C13.2926 28.5285 13.2449 28.2853 13.2474 28.0401L13.3181 17.1082L2.31557 17.1082C1.85445 17.1132 1.40899 16.9411 1.07106 16.6274C0.695987 16.2523 0.485273 15.7436 0.485273 15.2131C0.485273 14.6827 0.695987 14.174 1.07106 13.7989C1.41471 13.4638 1.87716 13.2757 2.35799 13.2757Z" fill="#838383"/>
                        </svg><?php }else{
                            if(!($row['t_logo'])){ echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' alt=''>"; }else{
                                echo "<img src='../assets/images/teams/{$row['t_logo']}' alt=''>";
                            }
                        } ?>
                        </div>
                        <div class="team-name"><?php if(($row)){ echo $row['t_name'];} ?></div>
                        <div class="text"><label for="">Select Team A</label></div>
                    </div>
                    <?php
                        }
                    ?>

                    <div class="container4">
                        <label for="" class="vs txt">VS</label>
                    </div>

                    <?php
                        if(isset($team2)){
                            $sql = "SELECT * FROM teams WHERE t_id = '$team2'";
                            $query = mysqli_query($conn,$sql);
                            $row = mysqli_fetch_assoc($query);
                    ?>
                    <div class="container4" id="team2">
                        <div class="img-container"><?php if(!($row)){ ?><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.35799 13.2757L13.2757 13.2757L13.2757 2.41452C13.268 2.16688 13.3104 1.92024 13.4003 1.68938C13.4902 1.45852 13.6258 1.24819 13.799 1.07101C14.1741 0.69594 14.6828 0.485226 15.2132 0.485227C15.7436 0.485227 16.2523 0.69594 16.6274 1.07101C16.8041 1.2411 16.9442 1.4455 17.039 1.67167C17.1338 1.89783 17.1815 2.141 17.179 2.38623L17.1082 13.3181L28.1108 13.3181C28.5719 13.313 29.0174 13.4852 29.3553 13.7989C29.7304 14.174 29.9411 14.6827 29.9411 15.2131C29.9411 15.7436 29.7304 16.2523 29.3553 16.6274C29.1826 16.8151 28.9727 16.9648 28.7389 17.0671C28.5052 17.1693 28.2528 17.2219 27.9977 17.2213L17.1507 17.1506L17.1507 28.0401C17.1546 28.283 17.1104 28.5243 17.0206 28.75C16.9308 28.9757 16.7971 29.1815 16.6274 29.3553C16.2523 29.7304 15.7436 29.9411 15.2132 29.9411C14.6828 29.9411 14.1741 29.7304 13.799 29.3553C13.6223 29.1852 13.4822 28.9808 13.3874 28.7546C13.2926 28.5285 13.2449 28.2853 13.2474 28.0401L13.3181 17.1082L2.31557 17.1082C1.85445 17.1132 1.40899 16.9411 1.07106 16.6274C0.695987 16.2523 0.485273 15.7436 0.485273 15.2131C0.485273 14.6827 0.695987 14.174 1.07106 13.7989C1.41471 13.4638 1.87716 13.2757 2.35799 13.2757Z" fill="#838383"/>
                        </svg><?php }else{
                            if(!($row['t_logo'])){ echo "<img src='https://cdn-icons-png.flaticon.com/512/8140/8140303.png' alt=''>"; }else{
                                echo "<img src='../assets/images/teams/{$row['t_logo']}' alt=''>";
                            }
                        } ?>
                        </div>
                        <div class="team-name"><?php if(($row)){ echo $row['t_name'];} ?></div>
                        <div class="text"><label for="">Select Team B</label></div>
                    </div>
                    <?php
                        }
                    ?>

                </div>
            </div>

        </div>
    </div>
    <iframe src="./schedule-match.php" frameborder="0" class="match-frame"></iframe>
    <script>
        let team1 = '<?php echo $team1;?>';
        let team2 = '<?php echo $team2;?>';
        let next_page = document.querySelector('.match-frame');
        let vs_txt = document.querySelector('.vs');
        const urlParams = new URLSearchParams(window.location.search);
        const game = urlParams.get('game');


        let goBack = ()=>{
            window.location.href = './create-team.php?for=schedule_match';
        }

        document.addEventListener('click', (e) => {
            if (e.target.closest('.img-container')) {
                let parentContainer = e.target.closest('.container4');
                if (parentContainer) {
                    let parentId = parentContainer.id;
                    console.log('for-team:', parentId);

                    window.location.href = './manage-teams.php?for-team=' + parentId + '&sport=' + game;
                }
            }
        });

        function startCountdown() {
            let i = 3;

            const countdown = setInterval(() => {
                vs_txt.innerText = i;
                vs_txt.style.fontSize = '40px';
                vs_txt.style.color = 'red';
                if (i === 0) {
                    clearInterval(countdown);
                }
                i--;
            }, 1000); // show each number every 1 second
        }

        if(team1 && team2){
            console.log("both are set")
            if(team1 == team2){
                alert("Team 1 and Team 2 can't be same");
                window.location.href = './manage-teams.php?for-team=team2'+ '&sport=' + game;
            }else{
                startCountdown()
                setTimeout(() => {
                    next_page.src = './schedule-match.php?team1='+team1+'&team2='+team2+'&sport='+game;
                    next_page.classList.add('active');
                    
                }, 4000);
            }
        }

        window.addEventListener("message", (event) => {
            if (event.data === "closeIframe") {
                next_page.classList.remove('active');  

                setTimeout(()=>{
                    window.location.replace(`./select-teams.php?game=${game}`);
                },550)
            }

            if(event.data === "gotohome"){
                window.location.href = '../dashboard.php?update=live&sport=CRICKET';
            }

        });

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