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
    <title>Manage Matches</title>
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
        --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
        --card-bg: #ffffff;
        --text-dark: #333333;
        --text-light: #666666;
        --border-color: #e0e0e0;
        --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    body {
        height: 100vh;
        height: -webkit-fill-available;
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .container {
        display: flex;
        background-color: var(--card-bg);
        box-shadow: var(--shadow);
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
        padding: 30px;
        overflow: hidden;
    }
    
    .return {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 20px 10px;
    }
    
    .return svg {
        cursor: pointer;
        font-size: 28px;
        color: var(--text-dark);
        transition: all 0.2s ease;
    }
    
    .return svg:hover {
        color: var(--primary-dark);
        transform: translateX(-3px);
    }
    
    .txt {
        line-height: 20px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 20px;
        color: var(--text-dark);
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .game-container {
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
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
        min-width: 90px;
        padding: 12px;
        gap: 8px;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 12px;
    }
    
    .game:hover {
        background: rgba(250, 192, 31, 0.1);
    }
    
    .game.selected {
        background: rgba(248, 99, 0, 0.15);
        border-radius: 12px;
    }
    
    .game p {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-dark);
        text-align: center;
        text-wrap: auto;
    }
    
    .game svg {
        height: 28px;
        width: 28px;
    }
    
    .game.selected p {
        font-weight: 600;
        color: var(--primary-dark);
    }
    
    .team-container {
        width: 100%;
        height: calc(100% - 180px);
        padding: 10px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 25px;
        overflow-y: auto;
        align-content: flex-start;
    }
    
    .game-info {
        height: 100%;
        padding: 15px;
        display: flex;
        font-size: 14px;
        gap: 15px;
        width: 100%;
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid var(--border-color);
    }
    
    .game-info:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-light);
    }
    
    .selected-team {
        border: 2px solid var(--primary-dark);
        background: rgba(248, 99, 0, 0.05);
    }
    
    .match-data {
        display: flex;
        gap: 8px;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    
    .game-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
        width: 100%;
    }
    
    .info {
        display: flex;
        justify-content: space-between;
        flex-direction: row;
        align-items: center;
        width: 100%;
    }
    
    .team-score {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
    }
    
    .team {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: row;
        gap: 10px;
    }
    
    .team img {
        height: 40px;
        width: 40px;
        background: #f5f5f5;
        border-radius: 50%;
        display: flex;
        overflow: hidden;
        object-fit: cover;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
    }
    
    .start-btn {
        width: 90px;
        height: 36px;
        border-radius: 18px;
        border: none;
        color: white;
        font-weight: 600;
        background: var(--background);
        box-shadow: 0 4px 10px rgba(248, 57, 0, 0.2);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .start-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(248, 57, 0, 0.3);
    }
    
    .start-btn:active {
        transform: translateY(0);
    }
    
    .no-data {
        color: var(--text-light);
        font-style: italic;
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px 0;
        font-size: 16px;
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
        box-shadow: 0 4px 15px rgba(248, 57, 0, 0.3);
        background: var(--background);
        color: white;
        transition: all 0.2s ease;
        z-index: 10;
    }
    
    .plus:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 20px rgba(248, 57, 0, 0.4);
    }
    
    .plus:active {
        transform: translateY(0);
    }

    .container2 {
        height: 100%;
        width: 100%;
    }

    @media (max-width: 1000px) {
        .team-container {
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        
    }

    @media (max-width: 600px) {
        .container {
            width: 100%;
            height: 100vh;
            border-radius: 0;
            padding: 20px 15px;
            box-shadow: none;
        }
        
        .team-container {
            grid-template-columns: 1fr;
            padding: 10px 5px;
        }

     
        
        .plus {
            bottom: 30px;
            right: 30px;
            height: 55px;
            width: 55px;
        }
        
        .game-list {
            height: 70px;
            padding: 0 5px;
            gap: 10px;
        }
        
        .game {
            min-width: 86px;
            padding: 10px;
        }
    }

    /* Custom scrollbar */
    .team-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .team-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .team-container::-webkit-scrollbar-thumb {
        background: var(--primary-light);
        border-radius: 10px;
    }
    
    .team-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }
    
    .game-list::-webkit-scrollbar {
        height: 4px;
    }
    
    .game-list::-webkit-scrollbar-thumb {
        background: var(--primary-light);
        border-radius: 10px;
    }
    #startMatchDialog {
        z-index: 9999;
        position: fixed;
        transform: translateX(-50%) translateY(-50%);
        top: 50%;
        left: 50%;
        width: 90%;
        max-width: 500px;
        border: none;
        height: max-content;
        background: white;
        flex-direction: column;
        transition: all 0.3s ease;
        justify-content: center;
        align-items: flex-start;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    #startMatchDialog::backdrop {
        position: fixed;
        inset: 0px;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(3px);
    }
    
    #content-wrapper {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 25px
    }
    
    #matchPasswordForm {
        display: flex;
        justify-content: center;
        flex-direction: column;
        gap: 25px;
    }
    
    .form-data {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
        width: 100%;
    }
    
    .form-data label {
        font-size: 15px;
        line-height: 35px;
        font-weight: 500;
        color: var(--text-dark);
    }
    
    .btns {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        width: 100%;
    }
    
    #matchPassword {
        padding: 12px 15px;
        height: 45px;
        outline: none;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: #f8f8f8;
        color: var(--text-dark);
        font-size: 15px;
    }
    
    #matchPassword:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(250, 192, 31, 0.2);
    }

    #title{
        font-size: 18px;
        font-weight: 500;
    }
    
    .btns>* {
        width: 110px;
        height: 40px;
        border-radius: 25px;
        border: solid 1px var(--primary-light);
        color: var(--primary-light);
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 600;
    }
    
    .btns>*:last-child {
        background: var(--background);
        color: white;
        border: none;
    }
    
    .btns>*:last-child:hover {
        background: linear-gradient(135deg, #fac01fdb, #f83900cc);
    }
    
    .btns>*:first-child:hover {
        background: rgba(250, 192, 31, 0.1);
    }
</style>
</head>
<body>
    <div class="container">

        <dialog id="startMatchDialog">
            <div id="content-wrapper">
                <div class="top-container">
                    <p id="title">Enter Password to Start Match</p>
                </div>

                <div class="body-container">
                <form id="matchPasswordForm">
                    <div class="form-data">
                        <label for="matchPassword">Password:</label>
                        <input type="text" name="" id="match_id" hidden>
                        <input type="text" id="matchPassword" name="matchPassword" required>
                        <div id="error-matchpassword" class="error"></div>
                    </div>

                    <div class='btns'>
                        <button id='submit-btn' type="button" onclick="closeDialog()">Cancel</button>
                        <button id='cancel-btn' type="submit">Start Match</button>
                    </div>
                </form>
                </div>
            </div>
        </dialog>

        <div class="return" >
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg>
            </div>
            <div>
                <?php
                if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'add-tournament.php') !== false){
                    echo "<div class='add-btn'>
                        <button onclick='save(event)' type='submit' id='save'>save</button>
                    </div>";
                }
                ?>
            </div>
        </div>
        <div class="container2">
            <div class="txt">
                <div>
                    <label for="">My Teams</label>
                    <h4>Manage Teams</h4>
                </div>
                <div>
                
                </div>
            </div>
            <div class="game-container">
                <div class="game-list">

                    <div class="game cricket selected"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.5401 23.655L20.6495 21.54L27.0079 27.87L24.8535 30L18.5401 23.655ZM24.7637 0C26.1525 0 27.4843 0.553123 28.4663 1.53769C29.4483 2.52226 30 3.85761 30 5.25C30 6.64239 29.4483 7.97774 28.4663 8.96231C27.4843 9.94688 26.1525 10.5 24.7637 10.5C23.375 10.5 22.0431 9.94688 21.0612 8.96231C20.0792 7.97774 19.5275 6.64239 19.5275 5.25C19.5275 3.85761 20.0792 2.52226 21.0612 1.53769C22.0431 0.553123 23.375 0 24.7637 0ZM0.437601 7.665L4.67149 3.42C4.95687 3.13876 5.34102 2.98117 5.74118 2.98117C6.14134 2.98117 6.52549 3.13876 6.81087 3.42L19.4826 16.155C20.0661 16.74 20.0661 17.685 19.4826 18.27L15.2487 22.5C15.1107 22.6424 14.9456 22.7556 14.7631 22.833C14.5807 22.9103 14.3846 22.9501 14.1865 22.9501C13.9884 22.9501 13.7924 22.9103 13.6099 22.833C13.4275 22.7556 13.2623 22.6424 13.1243 22.5L0.437601 9.795C-0.145867 9.195 -0.145867 8.25 0.437601 7.665Z" fill="black"/>
                    </svg><p>CRICKET</p>
                    </div>

                    <div class="game vollyball"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.5117 14.2404C13.4652 12.0418 12.9992 9.87218 12.1387 7.8483C6.64663 10.4124 2.61226 15.2503 1.23319 20.9591C2.04025 22.8079 3.21062 24.4757 4.67481 25.8636C6.06721 21.0227 9.21909 16.8771 13.5117 14.2404ZM11.2798 6.11269C10.3599 4.51019 9.18757 3.06648 7.80794 1.83716C2.30378 4.84878 -0.950327 11.0837 0.247282 17.6935C2.26749 12.6681 6.19904 8.50142 11.2798 6.11269ZM22.6268 16.097C23.147 10.0617 20.9756 4.15333 16.7295 0.107597C15.8404 0.00479119 13.6085 -0.225011 10.7475 0.621629C14.2479 4.24818 16.2652 9.05249 16.4029 14.0905C18.3305 15.1502 20.4431 15.8313 22.6268 16.097ZM15.0843 16.6715C13.203 17.8135 11.5566 19.3035 10.2334 21.0619C15.1932 24.5271 21.3929 25.6035 27.0181 23.9526C28.2281 22.3317 29.0964 20.4822 29.5706 18.516C27.9711 18.9235 26.3279 19.1347 24.6773 19.1449C21.3748 19.1388 18.0904 18.2922 15.0843 16.6715ZM9.13862 22.7008C8.21924 24.2731 7.58415 25.9906 7.19704 27.7927C9.88013 29.4392 13.0148 30.1965 16.1538 29.9565C19.2928 29.7165 22.2758 28.4915 24.6773 26.4562C17.8485 27.4299 12.3806 24.9565 9.13862 22.7008ZM20.0441 0.893763C23.389 5.15115 25.0342 10.6422 24.5563 16.23C26.399 16.2339 28.2305 15.944 29.9819 15.3713C29.9819 15.2503 30 15.1294 30 15.0085C30 8.48933 25.8447 2.96803 20.0441 0.893763Z" fill="black"/>
                    </svg>
                    <p>VOLLEYBALL</p></div>

                    <div class="game kabaddi"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.66312 21V23.4767C6.66312 23.6628 6.63124 23.8433 6.56748 24.0181C6.50372 24.193 6.40808 24.3498 6.28055 24.4884L2.13603 29.0233C1.90223 29.2791 1.60468 29.407 1.24336 29.407C0.88204 29.407 0.584485 29.2791 0.350691 29.0233C0.116897 28.7674 0 28.4419 0 28.0465C0 27.6512 0.116897 27.3256 0.350691 27.0698L4.11265 22.9535L2.70988 18.8372C2.5611 18.4186 2.52922 17.9363 2.61424 17.3902C2.69926 16.8442 2.94368 16.3498 3.3475 15.907L7.55579 11.3023C7.81084 11.0233 8.08714 10.814 8.3847 10.6744C8.68225 10.5349 9.01169 10.4651 9.37301 10.4651C9.73432 10.4651 10.0638 10.5349 10.3613 10.6744C10.6589 10.814 10.9352 11.0233 11.1902 11.3023L13.7407 14.0233C14.3146 14.6512 15.0159 15.1456 15.8448 15.5065C16.6737 15.8674 17.5664 16.0474 18.5228 16.0465V18.8372C17.2476 18.8372 16.0574 18.6163 14.9522 18.1744C13.847 17.7326 12.8905 17.0698 12.0829 16.186L11.1902 15.2093L8.19341 18.4884L10.5207 21.0698C10.627 21.186 10.712 21.3316 10.7758 21.5065C10.8395 21.6814 10.8714 21.8614 10.8714 22.0465V28.6046C10.8714 29 10.7494 29.3316 10.5054 29.5995C10.2614 29.8674 9.95834 30.0009 9.59617 30C9.234 29.9991 8.93135 29.8651 8.6882 29.5981C8.44506 29.3312 8.32263 29 8.32093 28.6046V22.6744L6.66312 21ZM21.0733 22.3256V28.6046C21.0733 29 20.9509 29.3316 20.7061 29.5995C20.4612 29.8674 20.1586 30.0009 19.7981 30C19.4376 29.9991 19.135 29.8651 18.8901 29.5981C18.6453 29.3312 18.5228 29 18.5228 28.6046V20.8256C18.5228 20.6395 18.5547 20.4595 18.6185 20.2856C18.6823 20.1116 18.7779 19.9544 18.9054 19.814L21.2008 17.4419L20.4357 12.5581C20.1169 12.9767 19.7662 13.3488 19.3836 13.6744C19.0011 14 18.5866 14.3023 18.1403 14.5814C17.4389 14.5349 16.7745 14.3721 16.1471 14.093C15.5197 13.814 14.9938 13.4419 14.5696 12.9767C15.526 12.7907 16.3711 12.4358 17.1048 11.9121C17.8385 11.3884 18.4387 10.72 18.9054 9.90698L20.1807 7.67442C20.542 7.04652 21.0414 6.62187 21.6791 6.40047C22.3167 6.17908 22.9437 6.20838 23.56 6.48838L29.2349 9.13954C29.4686 9.25582 29.6548 9.42466 29.7934 9.64605C29.932 9.86745 30.0008 10.1172 30 10.3954V14.6512C30 15.0465 29.8776 15.3781 29.6327 15.646C29.3879 15.914 29.0852 16.0474 28.7248 16.0465C28.3643 16.0456 28.0616 15.9116 27.8168 15.6447C27.5719 15.3777 27.4495 15.0465 27.4495 14.6512V11.3023L25.1541 10.3256L28.4059 28.2907C28.491 28.7326 28.4004 29.1279 28.1343 29.4767C27.8682 29.8256 27.5124 30 27.0669 30C26.7694 30 26.4982 29.9009 26.2533 29.7028C26.0085 29.5046 25.8546 29.2433 25.7917 28.9186L23.7513 19.5349L21.0733 22.3256ZM14.0595 11.1628C13.3581 11.1628 12.7579 10.8898 12.2589 10.3437C11.7598 9.79768 11.5099 9.14047 11.509 8.3721C11.5082 7.60373 11.7581 6.94698 12.2589 6.40187C12.7596 5.85675 13.3598 5.58326 14.0595 5.5814C14.7592 5.57954 15.3598 5.85303 15.8614 6.40187C16.363 6.9507 16.6125 7.60745 16.61 8.3721C16.6074 9.13675 16.3579 9.79396 15.8614 10.3437C15.3649 10.8935 14.7643 11.1665 14.0595 11.1628ZM20.4357 5.5814C19.7343 5.5814 19.1341 5.30838 18.6351 4.76233C18.136 4.21629 17.8861 3.55908 17.8852 2.79071C17.8844 2.02233 18.1343 1.36559 18.6351 0.820474C19.1358 0.275358 19.736 0.00186989 20.4357 9.42803e-06C21.1354 -0.00185104 21.736 0.271637 22.2376 0.820474C22.7392 1.36931 22.9887 2.02605 22.9862 2.79071C22.9836 3.55536 22.7341 4.21257 22.2376 4.76233C21.7411 5.3121 21.1405 5.58512 20.4357 5.5814Z" fill="black"/>
                    </svg>
                    <p>KABADDI</p></div>

                    <div class="game kho-kho"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.6837 0V1.36364L12.5051 0.490909L7.19388 6.13636L12.1531 8.86364V5.67273L11.8163 5.45455L13.6837 3.49091V30H16.7449V16.6909L19.699 14.0591C23.6327 14.6045 27.6582 12.7636 30 10.6773L27.8265 8.74091C26.0816 10.3091 22.4082 11.7409 19.8061 11.2636L16.7449 8.50909V0M21.3367 4.09091C20.5248 4.09091 19.7462 4.37825 19.1721 4.88971C18.598 5.40117 18.2755 6.09486 18.2755 6.81818C18.2755 7.5415 18.598 8.23519 19.1721 8.74666C19.7462 9.25812 20.5248 9.54545 21.3367 9.54545C22.1486 9.54545 22.9273 9.25812 23.5013 8.74666C24.0754 8.23519 24.398 7.5415 24.398 6.81818C24.398 6.09486 24.0754 5.40117 23.5013 4.88971C22.9273 4.37825 22.1486 4.09091 21.3367 4.09091ZM12.1531 9.21818L6.18367 14.5364C3.35204 17.0455 1.91327 18.8864 0 23.4L2.87755 24.3545C4.40816 20.7273 5.5102 19.2818 7.36224 17.5091L9.42857 19.35L1.85204 26.1L4.02551 28.0364L12.1531 20.7818V9.21818Z" fill="black"/>
                    </svg>
                    <p>KHO-KHO</p></div>

                    <div class="game football"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C6.72909 0 0 6.72909 0 15C0 23.2709 6.72909 30 15 30C23.2709 30 30 23.2709 30 15C30 6.72909 23.2709 0 15 0ZM25.3125 21.9231H22.0514C21.9504 21.9228 21.8512 21.8961 21.7637 21.8455C21.6763 21.7948 21.6036 21.7221 21.5531 21.6346L20.3892 19.6385C20.3126 19.5079 20.2903 19.3526 20.3272 19.2058L21.3988 14.8846C21.4203 14.7975 21.4618 14.7167 21.52 14.6485C21.5782 14.5802 21.6515 14.5265 21.7341 14.4916L23.7606 13.6334C23.8596 13.5914 23.9684 13.5779 24.0747 13.5945C24.1809 13.6111 24.2805 13.657 24.362 13.7272L27.3642 16.3103C27.4382 16.3741 27.4948 16.4555 27.5287 16.5471C27.5627 16.6387 27.5728 16.7373 27.5582 16.8339C27.3095 18.5397 26.7136 20.1763 25.8072 21.6425C25.756 21.7281 25.6835 21.7988 25.5968 21.848C25.5101 21.8972 25.4122 21.923 25.3125 21.9231ZM6.23942 13.6392L8.26587 14.4974C8.34846 14.5323 8.42178 14.586 8.47999 14.6542C8.5382 14.7225 8.5797 14.8033 8.6012 14.8904L9.67284 19.2115C9.70972 19.3583 9.68744 19.5137 9.61082 19.6442L8.44688 21.6346C8.39635 21.7221 8.32373 21.7948 8.23628 21.8455C8.14882 21.8961 8.04961 21.9228 7.94856 21.9231H4.6875C4.58887 21.9231 4.49187 21.8979 4.40577 21.8498C4.31966 21.8017 4.24733 21.7323 4.19567 21.6483C3.28928 20.1821 2.69335 18.5454 2.44471 16.8397C2.43009 16.7431 2.44022 16.6444 2.47415 16.5529C2.50808 16.4613 2.5647 16.3798 2.6387 16.3161L5.64087 13.7329C5.72213 13.6633 5.82115 13.6177 5.92685 13.6012C6.03255 13.5846 6.14077 13.5978 6.23942 13.6392ZM24.7687 7.34784L23.4656 11.0531C23.4394 11.1288 23.3978 11.1983 23.3433 11.257C23.2887 11.3157 23.2226 11.3625 23.149 11.3942L21.0159 12.2978C20.918 12.3393 20.8105 12.3528 20.7054 12.337C20.6003 12.3213 20.5016 12.2767 20.4202 12.2084L16.363 8.80529C16.2982 8.75165 16.2458 8.68448 16.2097 8.60849C16.1736 8.5325 16.1545 8.44952 16.1538 8.36538V5.94375C16.1539 5.84887 16.1774 5.75546 16.2221 5.67181C16.2669 5.58816 16.3316 5.51684 16.4106 5.46418L19.4993 3.40601C19.5799 3.35221 19.6727 3.31958 19.7693 3.31113C19.8658 3.30269 19.963 3.3187 20.0517 3.35769C21.829 4.13279 23.4062 5.30281 24.6635 6.77885C24.7296 6.85602 24.7741 6.94927 24.7926 7.04919C24.8111 7.1491 24.8029 7.25212 24.7687 7.34784ZM10.5 3.40601L13.5894 5.46418C13.6684 5.51684 13.7331 5.58816 13.7779 5.67181C13.8226 5.75546 13.8461 5.84887 13.8462 5.94375V8.36538C13.8462 8.44966 13.8277 8.53292 13.7921 8.60929C13.7564 8.68566 13.7045 8.7533 13.6399 8.80745L9.58269 12.2106C9.50128 12.2789 9.40257 12.3234 9.29745 12.3392C9.19234 12.355 9.08491 12.3414 8.98702 12.3L6.85096 11.3942C6.77693 11.3629 6.71028 11.3164 6.65526 11.2578C6.60024 11.1992 6.55809 11.1297 6.53149 11.0538L5.22837 7.34856C5.1945 7.2525 5.18667 7.14919 5.20567 7.04912C5.22467 6.94906 5.26982 6.85581 5.33654 6.77885C6.59456 5.30133 8.17307 4.13027 9.95192 3.35481C10.0403 3.317 10.1367 3.30188 10.2325 3.31083C10.3282 3.31977 10.4201 3.35249 10.5 3.40601ZM11.7483 27.0209L10.3255 23.4375C10.2929 23.356 10.2794 23.2682 10.2859 23.1808C10.2924 23.0933 10.3188 23.0084 10.363 22.9327L11.457 21.0577C11.5075 20.9702 11.5801 20.8975 11.6676 20.8469C11.755 20.7962 11.8542 20.7695 11.9553 20.7692H18.0447C18.1458 20.7695 18.245 20.7962 18.3324 20.8469C18.4199 20.8975 18.4925 20.9702 18.543 21.0577L19.6377 22.9327C19.6821 23.0084 19.7086 23.0932 19.7152 23.1807C19.7218 23.2682 19.7084 23.356 19.676 23.4375L18.2632 27.0195C18.2287 27.107 18.1732 27.1848 18.1017 27.2461C18.0302 27.3073 17.9447 27.3501 17.8529 27.3707C15.9782 27.8009 14.0304 27.8009 12.1558 27.3707C12.0646 27.3498 11.9799 27.307 11.9089 27.2461C11.8379 27.1852 11.7828 27.1079 11.7483 27.0209Z" fill="black"/>
                    </svg>
                    <p>FOOTBALL</p></div>

                    <div class="game tennis"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.4507 0.00226117C13.9956 0.0472598 12.8706 1.26222 12.9156 2.70218C12.9306 3.06217 13.0056 3.40716 13.1556 3.75215L13.5906 4.71212C13.6506 4.90712 13.5456 5.11711 13.3356 5.17711C13.2006 5.25211 13.0506 5.17711 12.9456 5.05711L12.3156 4.23214C11.8205 3.60216 11.0705 3.21217 10.2755 3.19717C8.82041 3.16717 7.62035 4.32213 7.59035 5.76209C7.57535 6.39207 7.78536 6.99205 8.17537 7.50204L8.8054 8.25202H8.82041C8.94041 8.44701 8.89541 8.68701 8.7304 8.807C8.59539 8.912 8.41539 8.912 8.29538 8.807L7.50034 8.17702C6.99032 7.78703 6.37529 7.57704 5.76026 7.59204C4.3202 7.62204 3.16515 8.822 3.19515 10.277C3.21015 11.0719 3.60017 11.8219 4.23019 12.3169L5.08523 12.9769C5.25024 13.1269 5.25024 13.3669 5.07023 13.5019C5.02087 13.553 4.95691 13.5877 4.8871 13.6011C4.81729 13.6145 4.74503 13.6061 4.68021 13.5769H4.66521L3.75017 13.1569C3.40516 13.0219 3.06014 12.9319 2.70012 12.9169C1.26006 12.8719 0.0450021 14.0118 0 15.4668C0 16.5468 0.600027 17.5367 1.59007 17.9567L18.6759 25.5615L25.5612 18.6767L17.9558 1.59221C17.5358 0.587244 16.5008 -0.0427375 15.4507 0.00226117ZM16.6958 6.15208C17.3258 6.13708 17.8958 6.49707 18.1358 7.06705L22.711 17.3268L16.7858 11.4169L15.3907 8.25202C14.9407 7.30705 15.6307 6.18208 16.6958 6.15208ZM11.7755 10.277C12.1806 10.277 12.5556 10.427 12.8406 10.7269L20.0559 17.9417C20.6559 18.5117 20.6709 19.4567 20.1009 20.0567C19.5009 20.6567 18.5709 20.6717 17.9408 20.0567L10.7255 12.8419C10.5828 12.706 10.4687 12.5431 10.3898 12.3626C10.3109 12.1821 10.2688 11.9877 10.266 11.7907C10.2632 11.5938 10.2998 11.3982 10.3735 11.2155C10.4473 11.0329 10.5567 10.8668 10.6955 10.7269C10.9805 10.427 11.3705 10.277 11.7755 10.277ZM7.69535 15.2568C7.89036 15.2568 8.10037 15.3168 8.25038 15.3918L11.4455 16.8018L17.3558 22.7116L7.06532 18.1367C5.53525 17.4767 6.04528 15.2118 7.69535 15.2568ZM27.4213 21.0616L21.061 27.4215L22.336 28.6964C23.4761 29.8214 25.1262 30.2564 26.6712 29.8514C27.4323 29.6441 28.126 29.2416 28.6838 28.6839C29.2416 28.1262 29.644 27.4325 29.8514 26.6715C30.2564 25.1265 29.8214 23.4766 28.6963 22.3366L27.4213 21.0616Z" fill="black"/>
                    </svg>
                    <p>TENNIS</p></div>

                    <div class="game table-tennis"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M29.0744 17.3699C30.9198 12.8117 30 7.39234 26.3091 3.69544C21.3938 -1.23181 13.4204 -1.23181 8.50511 3.69544L5.22434 6.98223L17.6151 19.3736C20.3159 15.7353 25.319 14.8389 29.0744 17.3699ZM16.3086 22.0393L3.39648 9.13241C1.79711 11.7864 2.12518 15.2841 4.41 17.5749L6.43119 19.6021L0.396922 24.8399C-0.106911 25.2793 -0.136203 26.0526 0.338337 26.5272L3.46678 29.6616C3.93546 30.1362 4.70879 30.1069 5.14818 29.6031L10.3798 23.5509L12.4713 25.6425C13.6254 26.7967 15.0842 27.4294 16.5899 27.5935C16.1739 26.5975 15.9454 25.5078 15.9454 24.3653C15.9396 23.5568 16.0919 22.7834 16.3086 22.0393ZM24.3758 18.7467C21.2708 18.7467 18.7516 21.266 18.7516 24.3712C18.7516 27.4763 21.2708 29.9956 24.3758 29.9956C27.4808 29.9956 30 27.4763 30 24.3712C30 21.266 27.4808 18.7467 24.3758 18.7467Z" fill="black"/>
                    </svg>
                    <p>TABLE-TENNIS</p></div>

                    <div class="game chess"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.4375 0.9375C8.4375 0.421875 8.01562 0 7.5 0C6.98438 0 6.5625 0.421875 6.5625 0.9375V1.875H5.625C5.10938 1.875 4.6875 2.29688 4.6875 2.8125C4.6875 3.32812 5.10938 3.75 5.625 3.75H6.5625V5.625H3.52734C2.87695 5.625 2.34375 6.1582 2.34375 6.80859C2.34375 6.95508 2.37305 7.0957 2.41992 7.23633L4.32422 12.1875H4.21875C3.43945 12.1875 2.8125 12.8145 2.8125 13.5938C2.8125 14.373 3.43945 15 4.21875 15H4.45312L3.51562 22.5H11.4844L10.5469 15H10.7812C11.5605 15 12.1875 14.373 12.1875 13.5938C12.1875 12.8145 11.5605 12.1875 10.7812 12.1875H10.6758L12.5801 7.23633C12.6328 7.10156 12.6562 6.95508 12.6562 6.80859C12.6562 6.15234 12.123 5.625 11.4727 5.625H8.4375V3.75H9.375C9.89062 3.75 10.3125 3.32812 10.3125 2.8125C10.3125 2.29688 9.89062 1.875 9.375 1.875H8.4375V0.9375ZM2.8125 24.375L0.28125 27.75C0.0996094 27.9961 0 28.2891 0 28.5938C0 29.373 0.626953 30 1.40625 30H13.5938C14.373 30 15 29.373 15 28.5938C15 28.2891 14.9004 27.9961 14.7188 27.75L12.1875 24.375H2.8125ZM19.6875 24.375L17.1562 27.75C16.9746 27.9961 16.875 28.2891 16.875 28.5938C16.875 29.373 17.502 30 18.2812 30H28.5938C29.373 30 30 29.373 30 28.5938C30 28.2891 29.9004 27.9961 29.7188 27.75L27.1875 24.375H19.6875ZM17.8125 12.1875V15.2285C17.8125 15.6855 17.9766 16.125 18.2812 16.4648L19.875 18.2812L19.7461 22.5H27.0996L26.9062 18.2812L28.5645 16.4766C28.8809 16.1309 29.0625 15.6797 29.0625 15.2051V12.1875C29.0625 11.6719 28.6406 11.25 28.125 11.25H27.1875C26.6719 11.25 26.25 11.6719 26.25 12.1875V13.125H24.8438V12.1875C24.8438 11.6719 24.4219 11.25 23.9062 11.25H22.9688C22.4531 11.25 22.0312 11.6719 22.0312 12.1875V13.125H20.625V12.1875C20.625 11.6719 20.2031 11.25 19.6875 11.25H18.75C18.2344 11.25 17.8125 11.6719 17.8125 12.1875ZM22.5 17.8125C22.5 17.2969 22.9219 16.875 23.4375 16.875C23.9531 16.875 24.375 17.2969 24.375 17.8125V19.6875H22.5V17.8125Z" fill="black"/>
                    </svg>
                    <p>CHESS</p></div>

                    <!-- <div class="game weight-lifting"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 0C13.6757 0 12.5212 2.024 12.5212 4.7C12.5212 7.376 13.6757 9.4 15 9.4C16.3243 9.4 17.4788 7.376 17.4788 4.7C17.4788 2.024 16.3243 0 15 0ZM11.4309 5.8045C9.44422 6.6605 8.4286 9.2063 7.4918 11.9495C6.39134 15.2087 5.7042 19.0547 5.19795 22.2H4.38559V16.7H3.24153V22.2H2.22458V18.3H1.08051V22.2H0V24H1.08051V27.9H2.22458V24H3.24153V29.5H4.38559V24H9.7622C10.1071 25.3 10.5191 26.6 10.9322 27.9L7.88136 28.5V30H12.5078L11.7229 24H18.2771L17.4922 30H22.1186V28.5L19.0678 27.9C19.4809 26.6 19.8928 25.3 20.2378 24H25.6144V29.5H26.7585V24H27.7754V27.9H28.9195V24H30V22.2H28.9195V18.3H27.7754V22.2H26.7585V16.7H25.6144V22.2H24.8021C24.2957 19.0537 23.6061 15.1983 22.4595 11.9273C21.505 9.2043 20.5758 6.7747 18.5623 5.873C18.257 8.8506 16.813 11.2 15 11.2C13.1731 11.2 11.7208 8.8144 11.4309 5.8045ZM11.2016 10.643L11.8397 15.667C9.91525 16.7 8.8983 16.7 8.8983 18.3C8.8983 19.6 9.0661 20.9 9.33369 22.2H7.29775C7.78055 19.3055 8.41875 16.0246 9.2878 13.4506C9.86034 11.998 10.402 11.5722 11.2016 10.643ZM18.7842 10.778C19.5037 11.6033 20.117 11.871 20.6785 13.4727C21.577 16.0359 22.2188 19.3077 22.702 22.2H20.6663C20.9339 20.9 21.1017 19.6 21.1017 18.3C21.1017 16.7 20.0847 16.7 18.1602 15.667L18.7842 10.778ZM15 18.3C16.4587 18.3 18.8136 19.9 18.8136 19.9L18.5127 22.2H11.4873L11.1864 19.9C11.1864 19.9 13.5413 18.3 15 18.3Z" fill="black"/>
                    </svg>
                    <p>WEIGHT-LIFTING</p></div> -->

                    <div class="game basketball"><svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.1818 12H6.81818C6.45652 12 6.10968 12.158 5.85395 12.4393C5.59821 12.7206 5.45455 13.1022 5.45455 13.5C5.45455 13.8978 5.59821 14.2794 5.85395 14.5607C6.10968 14.842 6.45652 15 6.81818 15H8.18182V28.5C8.18182 28.8978 8.32549 29.2794 8.58122 29.5607C8.83695 29.842 9.1838 30 9.54545 30C9.90711 30 10.254 29.842 10.5097 29.5607C10.7654 29.2794 10.9091 28.8978 10.9091 28.5V27H13.6364V28.5C13.6364 28.8978 13.78 29.2794 14.0358 29.5607C14.2915 29.842 14.6383 30 15 30C15.3617 30 15.7085 29.842 15.9642 29.5607C16.22 29.2794 16.3636 28.8978 16.3636 28.5V27H19.0909V28.5C19.0909 28.8978 19.2346 29.2794 19.4903 29.5607C19.746 29.842 20.0929 30 20.4545 30C20.8162 30 21.1631 29.842 21.4188 29.5607C21.6745 29.2794 21.8182 28.8978 21.8182 28.5V15H23.1818C23.5435 15 23.8903 14.842 24.1461 14.5607C24.4018 14.2794 24.5455 13.8978 24.5455 13.5C24.5455 13.1022 24.4018 12.7206 24.1461 12.4393C23.8903 12.158 23.5435 12 23.1818 12ZM13.6364 24H10.9091V21H13.6364V24ZM13.6364 18H10.9091V15H13.6364V18ZM19.0909 24H16.3636V21H19.0909V24ZM19.0909 18H16.3636V15H19.0909V18ZM25.9091 0H4.09091C3.00593 0 1.96539 0.474106 1.1982 1.31802C0.431005 2.16193 0 3.30653 0 4.5V16.5C0 17.6935 0.431005 18.8381 1.1982 19.682C1.96539 20.5259 3.00593 21 4.09091 21C4.45257 21 4.79941 20.842 5.05515 20.5607C5.31088 20.2794 5.45455 19.8978 5.45455 19.5C5.45455 19.1022 5.31088 18.7206 5.05515 18.4393C4.79941 18.158 4.45257 18 4.09091 18C3.72925 18 3.3824 17.842 3.12667 17.5607C2.87094 17.2794 2.72727 16.8978 2.72727 16.5V4.5C2.72727 4.10218 2.87094 3.72064 3.12667 3.43934C3.3824 3.15804 3.72925 3 4.09091 3H25.9091C26.2708 3 26.6176 3.15804 26.8733 3.43934C27.1291 3.72064 27.2727 4.10218 27.2727 4.5V16.5C27.2727 16.8978 27.1291 17.2794 26.8733 17.5607C26.6176 17.842 26.2708 18 25.9091 18C25.5474 18 25.2006 18.158 24.9449 18.4393C24.6891 18.7206 24.5455 19.1022 24.5455 19.5C24.5455 19.8978 24.6891 20.2794 24.9449 20.5607C25.2006 20.842 25.5474 21 25.9091 21C26.9941 21 28.0346 20.5259 28.8018 19.682C29.569 18.8381 30 17.6935 30 16.5V4.5C30 3.30653 29.569 2.16193 28.8018 1.31802C28.0346 0.474106 26.9941 0 25.9091 0ZM17.7273 9C18.0889 9 18.4358 8.84196 18.6915 8.56066C18.9472 8.27936 19.0909 7.89782 19.0909 7.5C19.0909 7.10218 18.9472 6.72064 18.6915 6.43934C18.4358 6.15804 18.0889 6 17.7273 6H12.2727C11.9111 6 11.5642 6.15804 11.3085 6.43934C11.0528 6.72064 10.9091 7.10218 10.9091 7.5C10.9091 7.89782 11.0528 8.27936 11.3085 8.56066C11.5642 8.84196 11.9111 9 12.2727 9H17.7273Z" fill="black"/>
                    </svg>
                    <p>BASKETBALL</p></div>

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
        let SportName = '';
        let urlParams = new URLSearchParams(window.location.search);
        const tournament = urlParams.get('tournament');
       if (document.referrer.includes('select-teams.php')){
            let urlParams = new URLSearchParams(window.location.search);
            let sport = urlParams.get('sport');
            document.querySelector('.game-container').style.display = 'none';
            loadgames(sport)
       }else if(document.referrer.includes('add-tournament.php')){
            let urlParams = new URLSearchParams(window.location.search);
            let sport = urlParams.get('sport');
            document.querySelector('.game-container').style.display = 'none';
            loadgames(sport)
       }else{
            //Default sport
            const defaultCategory = document.querySelector('.cricket');
            SportName = defaultCategory.querySelector('p').textContent.trim();
            console.log(SportName);

            loadgames(SportName)
            // setInterval(() => {
            //     loadgames(SportName)
            // }, 100);
       }

        //update sport
        const categories = document.querySelectorAll('.game');

        categories.forEach(category => {
            category.addEventListener('click', () => {
                SportName = category.querySelector('p').textContent.trim();
                console.log(SportName);

                setInterval(() => {
                    loadgames(SportName)
                }, 100);
                

                document.querySelector('.game.selected').classList.remove('selected');
                category.classList.add('selected');
            });
        });

        //display content as per user's selection(Status & Sport)
         function loadgames(sport) {
            let params = new URLSearchParams(window.location.search);
            params.set('sport', sport);
            let newUrl = window.location.pathname + '?' + params.toString();
            window.history.replaceState({}, '', newUrl);

            let data = {
                update : '',
                sport: sport,
                for : 'manage_matches'
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

                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;

                    if (info_container.innerHTML !== tempDiv.innerHTML) {
                        info_container.innerHTML = data;
                        console.log('updated');
                    }
                })
                .catch(error => console.error(error))
            }
            
        }

        //go to previous page
        let goBack = ()=>{
            window.history.back();
        }
        
        let selectedTeams = [];

        let get_team_info = (el)=>{
            const tournamentID = el.getAttribute('data-team_id');
            if (document.referrer.includes('select-teams.php')) {
                let urlParams = new URLSearchParams(window.location.search);
                let teamParam = urlParams.get('for-team');
                let previousUrl = document.referrer;

                if(previousUrl){
                    // Add parameters to the previous URL
                    let url = new URL(previousUrl);
                    url.searchParams.set(teamParam,tournamentID);

                    // Redirect to modified URL
                    window.location.href = url.toString();
                }
            }else if (document.referrer.includes('add-tournament.php')) {
                // Multi-selection logic for add-tournament
                if (!selectedTeams.includes(tournamentID)) {
                    selectedTeams.push(tournamentID);
                    el.classList.add('selected-team');
                } else {
                    selectedTeams = selectedTeams.filter(id => id !== tournamentID);
                    el.classList.remove('selected-team');
                }

                console.log(selectedTeams);

            }else{
                // window.location.href = `team-info.php?t=${tournamentID}`;
            }
        }

        document.querySelector('.plus').addEventListener('click',()=>{
            window.location.href = "./create-team.php?for=schedule_match";
        })

        let save = () =>{
            window.location.href = `./match-making.php?t=${tournament}&teams=${selectedTeams}`;

        }

        // Open dialog for password
        function openDialog(button, event) {
            if (event) event.stopPropagation();
            const dialog = document.getElementById("startMatchDialog");
            dialog.showModal();

            const match_to_start = button.closest('.game-info').getAttribute('data-match_id');
            console.log("Match : " + match_to_start);

            document.getElementById("match_id").value = match_to_start;
        }

        // Close dialog of password
        function closeDialog() {
            const dialog = document.getElementById("startMatchDialog");
            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                el.innerHTML = '';
                el.style.display = 'none';
            });
            document.getElementById("matchPasswordForm").reset();
            dialog.close();
        }

        
        // Variefy match password
        document.getElementById("matchPasswordForm").addEventListener("submit", function(e) {
            e.preventDefault();
            password = document.getElementById("matchPassword").value;
            match_id = document.getElementById("match_id").value;

            let formdata = new FormData();
            formdata.append('password', password.trim());
            formdata.append('match_id', match_id);

            fetch('../Backend/ckeck-match-password.php',{
                method : 'POST',
                body : formdata
            })
            .then(response => response.json())
            .then((data)=>{
                console.log(data);
                document.querySelectorAll('[id^="error-"]').forEach((el) => {
                    el.innerHTML = '';
                    el.style.display = 'none';
                });
                if(data.status != 200){
                    let err = document.getElementById(`error-${data.field}`);
                    err.innerHTML = data.message;
                    err.style.display = 'block';
                }else{
                    window.location.href = `./${SportName}/match_toss.php?match_id=${match_id}`;
                    document.getElementById("matchPasswordForm").reset();
                    closeDialog();
                }
            })
            .catch();
        });

        let edit_match = (el) => {
            let match = el.getAttribute('data-match_id');
            console.log(match);
            console.log(SportName);
            window.location.href = `./match-info.php?match_id=${match}`;
        }
    </script>
</body>
</html>