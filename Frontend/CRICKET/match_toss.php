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

    include '../../config.php';
    $match = $_GET['match_id'] ?? '';

    $query1 = mysqli_query($conn, "SELECT * FROM `matches` WHERE `match_id` = '$match'");
    $row = mysqli_fetch_assoc($query1);

    if(!empty($row['toss_winner'])){
        header("Location: ./select-players.php?match_id=$match");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Scoring</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
        user-select: none;
        scrollbar-width: none;
    }
    
    /* Theme Variables */
    :root {
        --primary-color: rgba(209, 34, 31, 1);
        --primary-light: rgba(209, 34, 31, 0.8);
        --primary-dark: rgba(160, 25, 23, 1);
        --primary-light-transparent: rgba(209, 34, 31, 0.1);
        --primary-dark-transparent: rgba(160, 25, 23, 0.1);
        --background: #ffffff;
        --card-bg: #ffffff;
        --text-dark: #333333;
        --text-light: #666666;
        --border-color: #e0e0e0;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --svg-fill: #333333;
        --hover-bg: rgba(209, 34, 31, 0.08);
        --input-bg: #ffffff;
        --light-bg: #f8f9fa;
    }

    /* Dark theme variables */
    [data-theme="dark"] {
        --background: #121212;
        --card-bg: #1e1e1e;
        --text-dark: #ffffff;
        --text-light: #b0b0b0;
        --border-color: #333333;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        --svg-fill: #ffffff;
        --hover-bg: rgba(209, 34, 31, 0.15);
        --input-bg: #2a2a2a;
        --light-bg: #2d2d2d;
        --primary-light-transparent: rgba(209, 34, 31, 0.2);
        --primary-dark-transparent: rgba(160, 25, 23, 0.2);
    }

    svg path {
        fill: var(--text-dark);
    }
    
    body {
        height: max-content;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        flex-direction: column;
        background-color: var(--light-bg);
        color: var(--text-dark);
    }
    
    .part {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 70px;
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
        fill: var(--svg-fill);
        transition: transform 0.2s;
    }
    
    .return svg:hover {
        transform: scale(1.1);
    }
    
    .container {
        display: flex;
        background-color: var(--card-bg);
        box-shadow: var(--shadow);
        position: relative;
        border-radius: 16px;
        width: 100%;
        max-width: 100%;
        min-height: 480px;
        padding: 40px;
        align-items: center;
        justify-content: flex-start;
        flex-direction: column;
        gap: 30px;
        border: 1px solid var(--border-color);
    }
    
    .container2 {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        flex-wrap: wrap;
        width: 100%;
        gap: 60px;
    }
    
    .txt {
        line-height: 20px;
        color: var(--text-dark);
    }
    
    .input-fields {
        width: 100%;
        position: relative;
    }
    
    .input-fields label {
        position: absolute;
        bottom: 22px;
        left: 14px;
        text-align: center;
        font-size: 16px;
        transition: 0.3s ease-in-out;
        color: var(--text-light);
    }
    
    .input-fields input:valid~label,
    .input-fields input:focus~label {
        transform: translateX(-5px) translateY(-24px);
        font-size: 14px;
        color: var(--primary-color);
    }
    
    .container input[type="text"],
    .container input[type="email"],
    .container input[type="password"],
    .container input[type="number"],
    .container input[type="tel"],
    .container input[type="datetime"],
    .container input[type="time"],
    .container input[type="date"],
    .container select {
        border: none;
        border-bottom: solid 2px var(--border-color);
        margin: 8px 0;
        padding: 10px 15px;
        font-size: 16px;
        width: 100%;
        outline: none;
        height: 45px;
        background: var(--input-bg);
        transition: all 0.3s;
        color: var(--text-dark);
    }
    
    .container input[type="text"]:focus,
    .container input[type="email"]:focus,
    .container input[type="password"]:focus,
    .container input[type="number"]:focus,
    .container input[type="tel"]:focus,
    .container input[type="datetime"]:focus,
    .container input[type="time"]:focus,
    .container input[type="date"]:focus,
    .container select:focus {
        border-bottom-color: var(--primary-color);
        box-shadow: 0 2px 0 0 var(--primary-light-transparent);
    }
    
    .container3 {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 60px;
    }
    
    .info {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 30px;
    }
    
    .error {
        display: none;
        color: var(--primary-color);
        width: 100%;
        font-size: 14px;
        margin: 5px;
        padding: 8px 12px;
        background-color: var(--primary-light-transparent);
        border-radius: 4px;
    }
    
    .teams,
    .options {
        height: 147px;
        width: 110px;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-around;
        background: var(--card-bg);
        box-shadow: var(--shadow);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .teams:hover,
    .options:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }
    
    .teams.active,
    .options.active {
        border: 2px solid var(--primary-color);
        box-shadow: 0 0 0 4px var(--primary-light-transparent);
    }
    
    .logo {
        height: 75px;
        width: 75px;
        background: var(--hover-bg);
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .types .logo {
        background: none;
    }
    
    .logo img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    
    .tname {
        font-size: 15px;
        text-wrap: auto;
        text-align: center;
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .sector {
        display: flex;
        width: 100%;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .add-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    
    .add-btn button {
        background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
        color: #fff;
        font-size: 14px;
        padding: 12px 45px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        cursor: pointer;
        height: 48px;
        width: 300px;
        max-width: 100%;
        transition: all 0.3s;
        box-shadow: 0 4px 8px rgba(209, 34, 31, 0.2);
    }
    
    .add-btn button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(209, 34, 31, 0.3);
    }
    
    .add-btn button:active {
        transform: translateY(0);
    }
    
    #flexSwitchCheckChecked.form-check-input {
        height: 25px;
        width: 50px;
    }
    
    #flexSwitchCheckChecked.form-check-input:checked {
        background-color: var(--primary-dark);
        border-color: var(--primary-light);
    }
    
    .form-check-input:focus {
        border-color: var(--primary-dark);
        outline: 0;
        box-shadow: 0 0 0 .25rem rgba(209, 34, 31, 0.22);
    }
    
    .form-check-input {
        border: 1px solid var(--primary-dark);
        transition: background-position .5s ease-in-out;
    }
    
    .rules {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 20px;
    }
    
    .rule {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        position: relative;
        padding: 15px;
        background-color: var(--card-bg);
        border-radius: 12px;
        box-shadow: var(--shadow);
    }
    .rule:first-child{
        box-shadow: none;
    }
    .rule:first-child{
        box-shadow: none;
    }
    .rule:first-child{
        box-shadow: none;
    }
    
    @media (min-width: 601px) {
        
        .container2 {
            gap: 80px;
            width: 70%;
        }
        
        .add-btn button {
            width: 300px;
        }
    }
    
    @media (max-width: 601px) {
        
        .container2 {
            gap: 50px;
        }
        
        .add-btn button {
            width: 100%;
        }
        
        
        .logo {
            height: 60px;
            width: 60px;
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
        </div>
        <div class="container2">
            <div class="part">
                <div class="txt">
                    <label for="">Scoring</label>
                    <h4>Toss</h4>
                </div>
                <div class="container3">
                    <?php
                        $query = mysqli_query($conn,"SELECT m.*, t1.t_id AS team1, t2.t_id AS team2, t1.t_logo AS team1_logo, t1.t_name AS team1_name, t2.t_logo AS team2_logo , t2.t_name AS team2_name FROM `matches` m JOIN `teams` t1 ON m.team_1 = t1.t_id JOIN `teams` t2 ON m.team_2 = t2.t_id WHERE m.match_id = '$match' ");
                        $row = mysqli_fetch_assoc($query);
                    ?>
                    <div class="info">
                        <label for="">Who won the toss?</label>
                        <div class="sector team">
                            <div class="teams" data-value="<?php echo $row['team1']; ?>">
                                <?php
                                    if (empty($row['team1_logo'])) {
                                        echo '<div class="logo"></div>';
                                    } else {
                                        echo "<div class=\"logo\"><img src=\"../../assets/images/teams/{$row['team1_logo']}\" alt=\"\"></div>";
                                    }

                                ?>
                                <div class="tname"><?php echo $row['team1_name']; ?></div>
                            </div>
                            <div class="teams" data-value="<?php echo $row['team2']; ?>">
                                <?php
                                    if (empty($row['team2_logo'])) {
                                        echo '<div class="logo"></div>';
                                    } else {
                                        echo "<div class=\"logo\"><img src=\"../../assets/images/teams/{$row['team2_logo']}\" alt=\"\"></div>";
                                    }

                                ?>
                                <div class="tname"><?php echo $row['team2_name'] ?></div>
                            </div>
                        </div>
                        <div class="error" id="error-team"></div>
                    </div>

                    <div class="info">
                        <label for="">Toss winner chose to</label>
                        <div class="sector types">
                            <div class="options" data-value="BAT">
                               <div class="logo">
                                    <img src="https://i.ibb.co/23rGnG2Y/Pngtree-cricketer-hitting-the-ball-20047688.png">
                                </div>
                                <div class="tname">BAT</div>
                            </div>

                            <div class="options" data-value="BOWL">
                                <div class="logo">
                                    <img src="https://i.ibb.co/rKcjrSFM/Pngtree-cricket-player-bowling-action-illustration-21241027.png">
                                </div>
                                <div class="tname">BOWL</div>
                            </div>
                            <div class="error" id="error-decision"></div>
                            <div class="error" id="error-empty"></div> 
                        </div>
                    </div>

                    <div class="info">
                        <label for="">Non-Mandatory Cricket Rules</label>
                        <div class="sector rules">
                            <div class="rule">
                                <h6>Overs</h6>
                                <div class="form-check form-switch">
                                    <select name="" id="over">
                                        <option value='Null' disabled selected></option>
                                        <?php
                                            for ($i = 1; $i <= 20; $i++) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                        ?>
                                        <option value='50'>50</option>
                                        <option value='-'>Unlimited</option>
                                    </select>
                                </div>

                            </div>
                            <div class="error" id="error-over"></div>
                            <div class="rule">
                                <h6>Free Hit on No Ball</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input isfreehit" type="checkbox" id="flexSwitchCheckChecked" checked>
                                </div>
                            </div>
                            <div class="rule">
                                <h6>Wide Ball</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input iswide" type="checkbox" id="flexSwitchCheckChecked" checked>
                                </div>
                            </div>
                            <div class="rule">
                                <h6>Super Over</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input issuperover" type="checkbox" id="flexSwitchCheckChecked" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="add-btn">
                        <button onclick="start_match(event)" type="submit" id="start-match">Let’s Start</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const teams = document.querySelectorAll('.teams');
        const options = document.querySelectorAll('.options');
        let selecteddecision = '';
        let selectedteam = '';

        let goBack = ()=>{
            window.history.back();
        }

        options.forEach(option => {
            option.addEventListener('click', () => {
                if(option.classList.contains('active')) {
                option.classList.remove('active');
                selecteddecision = '';
                console.log("Selection Option", selecteddecision);
                } else {
                options.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                selecteddecision = option.getAttribute('data-value');
                console.log("Selected Option:", selecteddecision);
                }
            });
        });

        teams.forEach(option => {
            option.addEventListener('click', () => {
                if(option.classList.contains('active')) {
                option.classList.remove('active');
                selectedteam = '';
                console.log("Selection Team", selectedteam);
                } else {
                teams.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                selectedteam = option.getAttribute('data-value');
                console.log("Selected Team:", selectedteam);
                }
            });
        });

        let start_match = (e) => {
            e.preventDefault();
            const iswide = document.querySelector('.iswide').checked;
            const isfreehit = document.querySelector('.isfreehit').checked;
            const issuperover = document.querySelector('.issuperover').checked;
            const overs = document.querySelector('#over').value;

            let formdata = new FormData();
            formdata.append('match_id', '<?php echo $match; ?>');
            formdata.append('selectedteam', selectedteam);
            formdata.append('selecteddecision', selecteddecision);
            formdata.append('iswide', iswide);
            formdata.append('isfreehit', isfreehit);
            formdata.append('issuperover', issuperover);
            formdata.append('overs', overs);

            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });

            fetch('../../Backend/make_toss.php',{
                method : 'POST',
                body : formdata
            })
            .then(rsponse => rsponse.json())
            .then((data) => {
                console.log(data);
                if(data.status != 200){
                    let el = document.getElementById(`error-${data.field}`);
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                }else{
                    window.location.href = './select-players.php?match_id=<?php echo $match; ?>';
                }
            })
            .catch(error => console.log(error));

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