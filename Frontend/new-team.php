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

    if(empty($_GET['game'])){
        // Redirect back if no teamName provided
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    $gameName = $_GET['game'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>New Team</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            scrollbar-width: none;
            user-select : none;
        }
        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        }
        body{
            height: -webkit-fill-available;
            background:white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            flex-direction: column;
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
        }
        .txt{
            line-height: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
        }
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
        }
        .content{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            gap: 30px;
            flex-wrap: wrap;
        }
        .info{
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: center;
            gap: 25px;
        }
        .input-fields{
            width: 100%;
            position: relative;
        }
        .contact{
            width: 100%;
            display: grid;
            align-items: center;
            justify-items: center;
            justify-content: space-between;
            align-content: center;
            gap: 10px;
            grid-template-columns: 1fr 270px;
        }
        .input-fields label{
            position: absolute;
            bottom: 22px;
            left: 14px;
            text-align: center;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }
        .input-fields input:valid ~ label,
        .input-fields input:focus ~ label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: black;
        }
        .set{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }
        .logo-info{
            height: 140px;
            width: 140px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            flex-direction: column;
        }
        .logo{
            height: 110px;
            width: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-container{
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
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .select-img{
            position: absolute;
            bottom: 0;
            right: 0;
            background: white;
            height: 40px;
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
        }
        .error{
            display: none;
            color:red; 
            width:100%;
            font-size:12px;
            margin: 5px;
        }
        .coordinater-no{
            opacity : 0.4;
        }
        #preview{
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        #add-btn {
            background: var(--background);
            color: white;
            font-size: 14px;
            padding: 15px 45px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            cursor: pointer;
            width: 100%;
            max-width: 300px;
            height: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(248, 57, 0, 0.2);
        }
        
        #add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(248, 57, 0, 0.3);
        }
        
        #add-btn:active {
            transform: translateY(0);
        }
        @media (min-width:601px) {
            .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                width: 90%;
                max-width: 100%;
                min-height: 480px;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                margin: 40px;
            }
            .container2{
                gap: 70px;
                width: 90%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="tel"],select{
                border: none;
                border-bottom: solid 1px black;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 16px;
                width: 100%;
                outline: none;
                height: 45px;
                background: white;
            }
            .content{
                flex-direction: row;
                gap: 80px;
                justify-content: space-evenly;
            }
        }

        @media(max-width:601px){
            .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                width: 768px;
                z-index: 0;
                max-width: 100%;
                min-height: 480px;
                padding: 40px 40px;
                height: 100vh;
                align-items: flex-start;
                justify-content: flex-start;
                flex-direction: column;
                flex-wrap: wrap;
                gap: 30px;
                scrollbar-width: none;
            }
            .container2{
                gap: 50px;
                height: 60%;
                justify-content: space-between;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="tel"],select{
                border: none;
                border-bottom: solid 1px black;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 15px;
                width: 100%;
                outline: none;
                height: 45px;
                background: white;
            }
            .info{
                gap : 20px;
            }
        }
    </style>
</head>
<body>
    <!-- <?php
        if(isset($gameName)){
            echo "Selected game is: " . $gameName;
        } else {
            header("Location: ./create-team.php");
            exit;
        }
    ?> -->
    <div class="container">
        <div class="return">
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div></div>
        </div>
        <div class="container2">
            <div class="txt">
                <div>
                    <label for="">Create New Team</label>
                    <h4><?php echo $gameName; ?></h4>
                </div>
                <div>
                    <div id="error-empty" class="error"></div>
                </div>
            </div>
            <div class="content">
                <div class="logo-info">
                    <div class="logo">
                        <div class="logo-container">
                            <div class="image">
                                <img id="preview" src="https://i.pinimg.com/1200x/58/79/29/5879293da8bd698f308f19b15d3aba9a.jpg" alt="">
                            </div>
                            <div class="select-img" onclick="uploadfile()">
                                <svg width="28" height="22" viewBox="0 0 31 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.2918 3.24984H23.8467L22.3051 1.70817C21.4125 0.815546 19.8461 0.166504 18.5835 0.166504H12.4168C11.1542 0.166504 9.58787 0.815546 8.69525 1.70817L7.15358 3.24984H4.7085C2.15858 3.24984 0.0834961 5.32492 0.0834961 7.87484V20.2082C0.0834961 22.7581 2.15858 24.8332 4.7085 24.8332H26.2918C28.8417 24.8332 30.9168 22.7581 30.9168 20.2082V7.87484C30.9168 5.32492 28.8417 3.24984 26.2918 3.24984ZM15.5002 18.6665C14.0689 18.6663 12.6963 18.0975 11.6844 17.0853C10.6725 16.0731 10.1041 14.7004 10.1043 13.2691C10.1045 11.8379 10.6733 10.4653 11.6855 9.45338C12.6977 8.44146 14.0704 7.87309 15.5017 7.87329C16.933 7.8735 18.3055 8.44227 19.3175 9.45447C20.3294 10.4667 20.8977 11.8394 20.8975 13.2707C20.8973 14.7019 20.3286 16.0745 19.3164 17.0864C18.3042 18.0983 16.9314 18.6667 15.5002 18.6665ZM24.7502 11.4191C24.487 11.4191 24.2264 11.3673 23.9832 11.2666C23.74 11.1659 23.5191 11.0182 23.333 10.8321C23.1469 10.646 22.9993 10.4251 22.8986 10.1819C22.7978 9.93877 22.746 9.67815 22.746 9.41496C22.746 9.15177 22.7978 8.89116 22.8986 8.648C22.9993 8.40484 23.1469 8.18391 23.333 7.9978C23.5191 7.8117 23.74 7.66407 23.9832 7.56335C24.2264 7.46263 24.487 7.4108 24.7502 7.4108C25.2817 7.4108 25.7915 7.62195 26.1673 7.9978C26.5432 8.37366 26.7543 8.88342 26.7543 9.41496C26.7543 9.9465 26.5432 10.4563 26.1673 10.8321C25.7915 11.208 25.2817 11.4191 24.7502 11.4191Z" fill="black"/>
                                </svg>
                                <input type="file" name="logo" id="fileInput" style="display:none">
                            </div>
                        </div>
                    </div>
                    <div class="logo-name">
                        <label for="logo-name">LOGO</label>
                    </div>
                </div>
                <div class="info">
                    <div class="input-fields"><input type="text" name="" id="team_name" class="data" required><label for="team_name">Team Name</label></div>
                    <div id="error-teamName" class="error"></div>
                    <div class="input-fields"><input type="text" name="" id="city" class="data" required><label for="city">City/Town</label></div>
                    <div class="contact">
                        <?php
                            $countryCodes = [
                                ["name" => "India", "code" => "+91"],
                                ["name" => "USA", "code" => "+1"],
                                ["name" => "United Kingdom", "code" => "+44"],
                                ["name" => "Japan", "code" => "+81"],
                                ["name" => "Australia", "code" => "+61"],
                                ["name" => "Canada", "code" => "+1"],
                                ["name" => "Germany", "code" => "+49"],
                                ["name" => "France", "code" => "+33"],
                                ["name" => "Italy", "code" => "+39"],
                                ["name" => "China", "code" => "+86"],
                                ["name" => "Russia", "code" => "+7"],
                                ["name" => "Brazil", "code" => "+55"],
                                ["name" => "South Africa", "code" => "+27"],
                                ["name" => "UAE", "code" => "+971"],
                                ["name" => "Singapore", "code" => "+65"],
                                ["name" => "Nepal", "code" => "+977"],
                                ["name" => "Bangladesh", "code" => "+880"],
                                ["name" => "Sri Lanka", "code" => "+94"],
                                ["name" => "Pakistan", "code" => "+92"],
                                ["name" => "New Zealand", "code" => "+64"],
                                ["name" => "Indonesia", "code" => "+62"],
                                ["name" => "Malaysia", "code" => "+60"],
                                ["name" => "Thailand", "code" => "+66"],
                                ["name" => "Saudi Arabia", "code" => "+966"],
                                ["name" => "South Korea", "code" => "+82"],
                                ["name" => "Philippines", "code" => "+63"],
                                ["name" => "Turkey", "code" => "+90"],
                                ["name" => "Spain", "code" => "+34"],
                                ["name" => "Netherlands", "code" => "+31"],
                                ["name" => "Sweden", "code" => "+46"],
                                ["name" => "Switzerland", "code" => "+41"]
                            ];
                            ?>
                        <select name="" id="country" onchange="updateCode()">
                            <option value="" disabled selected>Select Country</option>
                            <?php foreach ($countryCodes as $country) { ?>
                                <option value="<?php echo $country['code']; ?>">
                                    <?php echo $country['name'] . " (" . $country['code'] . ")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="input-fields coordinater-no"><input disabled type="tel" name="" id="coordinater-no" class="data coordinater-no" required><label for="coordinater-no">Coordinator Number</label></div>
                        <div id="error-coordinaterNo" class="error"></div>
                    </div>
                    <div class="input-fields"><input type="text" name="" id="coordinater-name" class="data" required><label for="coordinater-name">Team Coordinator Name</label></div>
                </div>
            </div>
            <div class="set">
                <button type="submit" id="add-btn" onclick="addTeam()" class="btn" name = "add-team">Add Team</button>
            </div>
        </div>
    </div>
</body>
<script>
    let code = null;
    let game = '<?php echo $gameName; ?>';
    let goBack = () => {
        parent.postMessage("closeIframe", "*");
    }

    let uploadfile = () => {
        document.getElementById("fileInput").click();
    }

    let updateCode = () => {
        code = document.querySelector('#country').value;
        let coordinater_no = document.querySelector('.coordinater-no');
        let coordinater_no2 = document.querySelector('#coordinater-no');
        coordinater_no.style.opacity = 1;
        coordinater_no2.style.opacity = 1;
        coordinater_no2.disabled = false;
        console.log(code);
    }

    document.getElementById('fileInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const imgPreview = document.getElementById('preview');
            imgPreview.src = URL.createObjectURL(file);
            console.log(imgPreview.src)
            imgPreview.style.display = 'block'; // Show the image
        }
    });


    let addTeam =()=>{
        let teamName = document.getElementById("team_name").value;
        let city = document.getElementById("city").value;
        let coordinaterNo = document.getElementById("coordinater-no").value;
        let coordinaterName = document.getElementById("coordinater-name").value;
        let logo = document.getElementById("fileInput").files[0];

        let formData = new FormData();
        formData.append("teamName", teamName);
        formData.append("city", city);
        formData.append("coordinaterNo", coordinaterNo);
        formData.append("coordinaterName", coordinaterName);
        formData.append("logo", logo);
        formData.append("game", game);
        formData.append("code", code);
        
        console.log(logo);
        fetch("../Backend/addnew-team.php",{
            method: "POST",
            body: formData
        })
        .then(respone => respone.json())
        .then((data)=>{

            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });

            if(data.status === 200){
                parent.postMessage("goto_manage_team", "*");

            }else{
                let el = document.getElementById('error-'+data.field);
                if (el) {
                    el.innerHTML = data.message;
                    el.style.display = 'block';
                } else {
                    console.warn("No error element found for id: error-" + data.field);
                }
            }

            console.log(data);
        })
        .catch(error =>{
            console.log(error);
        });
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
</html>