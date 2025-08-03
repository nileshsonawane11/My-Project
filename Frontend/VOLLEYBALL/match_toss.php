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
    
    :root {
        --primary-light: #FAC01F;
        --primary-dark: #F83900;
        --primary-light-20: rgba(250, 192, 31, 0.2);
        --primary-dark-10: rgba(248, 57, 0, 0.1);
        --background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        --text-dark: #2D3748;
        --text-muted: #718096;
        --bg-light: #F8FAFC;
        --card-bg: #FFFFFF;
        --border-light: #E2E8F0;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        --radius-sm: 6px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    body {
        height: max-content;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        flex-direction: column;
        background-color: var(--bg-light);
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
        fill: var(--primary-dark);
        transition: var(--transition);
    }
    
    .return svg:hover {
        transform: scale(1.1);
        fill: var(--primary-light);
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
        color: var(--text-muted);
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
        color: var(--text-muted);
        pointer-events: none;
    }
    
    .input-fields input:valid ~ label,
    .input-fields input:focus ~ label {
        transform: translateX(-5px) translateY(-28px);
        font-size: 14px;
        color: var(--primary-dark);
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
        border-bottom-color: var(--primary-dark);
        box-shadow: 0 2px 0 0 var(--primary-light-20);
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
        color: #DC2626;
        width: 100%;
        font-size: 14px;
        margin: 5px 0;
        padding: 8px 12px;
        background-color: rgba(220, 38, 38, 0.1);
        border-radius: var(--radius-sm);
        border-left: 3px solid #DC2626;
    }
    
    .teams,
    .options {
        height: 160px;
        width: 130px;
        border-radius: var(--radius-md);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
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
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px var(--primary-light-20);
        background-color: var(--primary-light-20);
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
        margin-bottom: 12px;
    }
    
    .types .logo {
        background: transparent;
    }
    
    .logo img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        filter: contrast(1.1);
        border-radius: 50%;
    }
    
    .tname {
        font-size: 15px;
        text-align: center;
        font-weight: 600;
        color: var(--text-dark);
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
        margin-top: 20px;
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
        box-shadow: 0 4px 6px rgba(248, 57, 0, 0.2);
    }
    
    .add-btn button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(248, 57, 0, 0.3);
        opacity: 0.9;
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
        box-shadow: 0 0 0 0.25rem rgba(248, 57, 0, 0.25);
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
        width: 100%;
        padding: 16px;
        background-color: var(--card-bg);
        transition: var(--transition);
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
        
        
        .add-btn button {
            width: 100%;
            max-width: 280px;
        }
        
        .part {
            gap: 40px;
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
            font-size: 15px;
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
                            <div class="options" data-value="SERVE">
                                <div class="logo">
                                    <img src="https://i.ibb.co/YFftD8VJ/Pngtree-volleyball-player-blue-costume-8875190.png">

                                </div>
                                <div class="tname">SERVE</div>
                            </div>

                            <div class="options" data-value="COURT">
                                <div class="logo">
                                    <img src="https://i.ibb.co/xS1nd7nb/Pngtree-volleyball-player-red-custom-8530229.png">

                                    </div>
                                    <div class="tname">COURT</div>
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
                                    <input type="number" id="sets"  required><label for="sets" id="time">Number of Sets</label></div>
                            </div>

                            <div class="rule">
                                 <div class="input-fields event-time">
                                    <input type="number" id="points"  required><label for="points" id="time">Points to complete set</label></div>
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
            const sets = document.getElementById('sets').value;
            const points = document.getElementById('points').value;

            let formdata = new FormData();
            formdata.append('match_id', '<?php echo $match; ?>');
            formdata.append('selectedteam', selectedteam);
            formdata.append('selecteddecision', selecteddecision);
            formdata.append('sets',sets);
            formdata.append('points',points);

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
                    window.location.href = './score_panel.php?match_id=<?php echo $match; ?>';
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
    </script>
</body>
</html>
