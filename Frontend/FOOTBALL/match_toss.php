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
        header("Location: ./score_panel.php?match_id=$match");
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
        --primary-light-10: rgba(209, 34, 31, 0.1);
        --primary-dark-10: rgba(160, 25, 23, 0.1);
        --background: #ffffff;
        --card-bg: #ffffff;
        --text-dark: #2d3748;
        --text-light: #4a5568;
        --bg-light: #f8fafc;
        --border-light: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --transition: all 0.2s ease-in-out;
        --svg-fill: #2d3748;
    }

    /* Dark theme variables */
    [data-theme="dark"] {
        --background: #121212;
        --card-bg: #1e1e1e;
        --text-dark: #ffffff;
        --text-light: #a0aec0;
        --bg-light: #2d3748;
        --border-light: #4a5568;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.2);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.3);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.3);
        --svg-fill: #ffffff;
        --primary-light-10: rgba(209, 34, 31, 0.2);
        --primary-dark-10: rgba(160, 25, 23, 0.2);
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
        background-color: var(--bg-light);
        color: var(--text-dark);
        line-height: 1.5;
    }
    
    .container {
        display: flex;
        background-color: var(--card-bg);
        box-shadow: var(--shadow-lg);
        position: relative;
        width: 100%;
        max-width: 1200px;
        min-height: 480px;
        align-items: center;
        justify-content: flex-start;
        flex-direction: column;
        gap: 30px;
        padding: 40px;
    }
    
    .part {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 50px;
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
        transition: var(--transition);
    }
    
    .return svg:hover {
        transform: scale(1.1);
        fill: var(--primary-color);
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
        line-height: 1.6;
        color: var(--text-light);
    }
    
    .input-fields {
        width: 100%;
        position: relative;
        margin-bottom: 24px;
    }
    
    .input-fields label {
        position: absolute;
        bottom: 22px;
        left: 14px;
        font-size: 16px;
        transition: var(--transition);
        color: var(--text-light);
        pointer-events: none;
    }
    
    .input-fields input:valid ~ label,
    .input-fields input:focus ~ label {
        transform: translateX(-5px) translateY(-28px);
        font-size: 14px;
        color: var(--primary-color);
        font-weight: 600;
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
        border-bottom: 2px solid var(--border-light);
        margin: 8px 0;
        padding: 10px 15px;
        font-size: 16px;
        width: 100%;
        outline: none;
        height: 48px;
        background: transparent;
        transition: var(--transition);
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
        box-shadow: 0 2px 0 0 var(--primary-light-10);
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
        margin: 5px 0;
        padding: 8px 12px;
        background-color: var(--primary-light-10);
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--primary-color);
    }
    
    .teams,
    .options {
        height: 160px;
        width: 130px;
        border-radius: var(--radius-md);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-around;
        background: var(--card-bg);
        box-shadow: var(--shadow-md);
        cursor: pointer;
        transition: var(--transition);
        border: 2px solid transparent;
        padding: 12px;
    }
    
    .teams:hover,
    .options:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .teams.active,
    .options.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px var(--primary-light-10);
    }
    
    .logo {
        height: 80px;
        width: 80px;
        background: var(--bg-light);
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        transition: var(--transition);
    }
    
    .types .logo {
        background: transparent;
    }
    
    .logo img {
        height: 100%;
        width: 100%;
        object-fit: contain;
        filter: contrast(104%);
    }
    
    .tname {
        font-size: 15px;
        text-align: center;
        font-weight: 600;
        color: var(--text-dark);
        margin-top: 8px;
    }
    
    .sector {
        display: flex;
        width: 100%;
        flex-direction: row;
        align-items: center;
        justify-content: space-evenly;
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
        color: white;
        font-size: 14px;
        padding: 12px 45px;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        cursor: pointer;
        height: 48px;
        width: 300px;
        transition: var(--transition);
        box-shadow: 0 4px 6px rgba(209, 34, 31, 0.2);
    }
    
    .add-btn button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(209, 34, 31, 0.3);
    }
    
    .add-btn button:active {
        transform: translateY(0);
    }
    
    #flexSwitchCheckChecked.form-check-input {
        height: 24px;
        width: 48px;
    }
    
    #flexSwitchCheckChecked.form-check-input:checked {
        background-color: var(--primary-dark);
        border-color: var(--primary-light);
    }
    
    .form-check-input:focus {
        border-color: var(--primary-dark);
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(209, 34, 31, 0.25);
    }
    
    .form-check-input {
        border: 1px solid var(--primary-dark);
        transition: var(--transition);
    }
    
    .form-switch {
        position: absolute;
        right: 3px;
        top: 15px;
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
        padding: 16px;
        background-color: var(--card-bg);
        color: var(--text-dark);
    }
    
    @media (min-width: 601px) {
        .container2 {
            gap: 70px;
            width: 80%;
        }
        
        .add-btn button {
            width: 320px;
            font-size: 15px;
        }
    }
    
    @media (max-width: 600px) {
           
        
        .container2 {
            gap: 40px;
        }

        
        .logo {
            height: 70px;
            width: 70px;
        }
        
        .add-btn button {
            width: 100%;
            max-width: 280px;
        }
        
        .part {
            gap: 40px;
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
                            <div class="options" data-value="KICKOFF">
                                <div class="logo">
                                    <img src="https://i.ibb.co/m5qrQzV5/Pngtree-cartoon-football-player-picture-4360062.png">

                                </div>
                                <div class="tname">KICKOFF</div>
                            </div>

                            <div class="options" data-value="SIDE">
                                <div class="logo">
                                    <img src="https://i.ibb.co/FkT3kcyR/vecteezy-ai-generated-soccer-field-with-gate-isolated-on-transparent-41858242.png">

                                    </div>
                                    <div class="tname">SIDE</div>
                                </div>
                            </div>
                            <div class="error" id="error-decision"></div>
                            <div class="error" id="error-empty"></div> 
                        </div>
                    </div>
                    <div class="info">
                        <label for="">Enter match format</label>
                        <div class="sector rules">

                            <div class="rule">
                                 <div class="input-fields event-time">
                                    <input type="number" id="points"  required><label for="points" id="time">Enter Total halves</label></div>
                            </div>
                        </div>
                        <div class="error" id="error-data_empty"></div>
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
        const match_id = "<?php echo $match; ?>";
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
            let halves = document.querySelector('#points');

            let formdata = new FormData();
            formdata.append('match_id', match_id);
            formdata.append('selectedteam', selectedteam);
            formdata.append('selecteddecision', selecteddecision);
            formdata.append('halves', halves.value);


            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });

            fetch('./Backend/make_toss.php',{
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
                    window.location.href = './score_panel.php?match_id='+match_id;
                }
            })
            .catch(error => console.log(error));

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
