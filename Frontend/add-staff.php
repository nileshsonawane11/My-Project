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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>Add Staff</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
        }
        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        }
        body{
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
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
            width: 100%;
            gap: 60px
        }
        .txt{
            line-height: 20px;
        }
        .team{
            width: 100%;
            display: flex;
            flex-direction: row;
            align-content: center;
            justify-content: space-evenly;
            align-items: center;
            gap: 10px;
            color: black;
            padding: 10px;
            border-radius: 20px;
            text-wrap: auto;
            background: #eeeeeeab;
            box-shadow: 1px 2px 3px rgba(0, 0, 0, 0.5);
            flex-wrap: wrap;
        }
        .logo{
            min-height: 75px;
            min-width: 75px;
            background: #e8e8e8;
            margin: 10px;
            border-radius: 50%;
        }
        .other-info{
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 20px;
            margin-left: 15px;
            cursor: pointer;
            line-height: 30px;
        }
        .team-info{
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            height: 100%;
            justify-content: center;
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
        }
        .input-fields input:valid ~ label,
        .input-fields input:focus ~ label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: black;
        }
        .history-staff{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
        }
        .team-info h4{
            overflow-wrap: break-word;
            width: 100%;
        }
        .team-info label.data{
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
            gap: 5px;
        }
        .add-history-staff svg{
            flex-shrink: 0;
            flex-grow: 0;
            cursor: pointer;
        }
        label.data .dt{
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            max-width: 200px; /* or whatever fits your layout */
            display: block;
        }
        .no-data{
            color:rgb(0, 0, 0);
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
            }
            .container2{
                gap: 70px;
                width: 70%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],select{
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
            .staff-container{
                width: 100%;
                display: grid;
                justify-items: center;
                align-items: center;
                gap: 30px;
                justify-content: space-around;
                grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            }
        }

        @media(max-width: 601px) {
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
                gap: 30px
            }
            .team{
                height: 90px;
                width: 100%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],select{
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
            .staff-container{
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 20px;
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
            <div class="txt">
                <label for="">Team Name</label>
                <h4>Add Staff</h4>
            </div>
            <div class="search-staff" style="width: 100%;">
                <div class="input-fields"><input type="text" name="" id="email" required><label for="email">Email</label></div>
            </div>
            <h3 class="saved_staff" style="display: none;">Select From Saved Staff</h3>
            <div class="history-staff">
                <h3>Select From Saved Staff</h3>
                <div class="staff-container">
                    
                    <div class="team">
                        <div class="logo"></div>
                        <div class="team-info">
                            <h4 class="staff-name">Staff Name</h4>
                            <div class="other-info">
                                <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><span class="dt">
                                place</span></label>
                                <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><span class="dt">
                                Contact No.</span></label>
                            </div>
                        </div>
                        <div class="add-history-staff">
                            <svg onclick="add_staff(this)" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_500_232)"/>
                            <defs>
                            <linearGradient id="paint0_linear_500_232" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFEB0B"/>
                            <stop offset="1" stop-color="#C11218"/>
                            </linearGradient>
                            </defs>
                            </svg>
                        </div>
                    </div>

                    <div class="team">
                        <div class="logo"></div>
                        <div class="team-info">
                            <h4 class="staff-name">Staff Name</h4>
                            <div class="other-info">
                                <label for="place" class="data"><svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.49984 0.416504C2.75859 0.416504 0.541504 2.63359 0.541504 5.37484C0.541504 9.09359 5.49984 14.5832 5.49984 14.5832C5.49984 14.5832 10.4582 9.09359 10.4582 5.37484C10.4582 2.63359 8.24109 0.416504 5.49984 0.416504ZM5.49984 7.14567C5.03018 7.14567 4.57976 6.9591 4.24767 6.62701C3.91557 6.29491 3.729 5.84449 3.729 5.37484C3.729 4.90518 3.91557 4.45476 4.24767 4.12267C4.57976 3.79057 5.03018 3.604 5.49984 3.604C5.96949 3.604 6.41991 3.79057 6.75201 4.12267C7.0841 4.45476 7.27067 4.90518 7.27067 5.37484C7.27067 5.84449 7.0841 6.29491 6.75201 6.62701C6.41991 6.9591 5.96949 7.14567 5.49984 7.14567Z" fill="black"/>
                                </svg><span class="dt">
                                plac</span></label>
                                <label for="coordinator" class="data"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.72933 4.52067V2.93734C3.72933 2.57347 3.801 2.21316 3.94025 1.87699C4.07949 1.54081 4.28359 1.23536 4.54089 0.978062C4.79818 0.720766 5.10364 0.516669 5.43981 0.377421C5.77598 0.238174 6.13629 0.166504 6.50016 0.166504C6.86403 0.166504 7.22434 0.238174 7.56051 0.377421C7.89669 0.516669 8.20214 0.720766 8.45944 0.978062C8.71673 1.23536 8.92083 1.54081 9.06008 1.87699C9.19933 2.21316 9.271 2.57347 9.271 2.93734V4.52067C9.271 5.46275 8.80154 6.294 8.0835 6.79513V7.54721C8.0836 7.70541 8.13109 7.85994 8.21984 7.99089C8.3086 8.12184 8.43455 8.22319 8.58145 8.28188L9.83704 8.78459C10.488 9.04491 11.0459 9.49435 11.439 10.0749C11.832 10.6554 12.0419 11.3404 12.0418 12.0415H0.958496C0.958378 11.3404 1.16837 10.6554 1.56138 10.0749C1.95438 9.49435 2.51235 9.04491 3.16329 8.78459L4.41887 8.28188C4.56577 8.22319 4.69173 8.12184 4.78048 7.99089C4.86924 7.85994 4.91673 7.70541 4.91683 7.54721V6.79513C4.55019 6.53983 4.25072 6.19967 4.04395 5.80363C3.83718 5.4076 3.72924 4.96744 3.72933 4.52067Z" fill="black"/>
                                </svg><span class="dt">
                                Contact</span></label>
                            </div>
                        </div>
                        <div class="add-history-staff">
                            <svg onclick="add_staff(this)" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.25 0C12.2891 0 13.2852 0.132812 14.2383 0.398438C15.1914 0.664062 16.0898 1.03906 16.9336 1.52344C17.7773 2.00781 18.5352 2.59375 19.207 3.28125C19.8789 3.96875 20.4648 4.73047 20.9648 5.56641C21.4648 6.40234 21.8438 7.29688 22.1016 8.25C22.3594 9.20312 22.4922 10.2031 22.5 11.25C22.5 12.2891 22.3672 13.2852 22.1016 14.2383C21.8359 15.1914 21.4609 16.0898 20.9766 16.9336C20.4922 17.7773 19.9062 18.5352 19.2188 19.207C18.5312 19.8789 17.7695 20.4648 16.9336 20.9648C16.0977 21.4648 15.2031 21.8438 14.25 22.1016C13.2969 22.3594 12.2969 22.4922 11.25 22.5C10.2109 22.5 9.21484 22.3672 8.26172 22.1016C7.30859 21.8359 6.41016 21.4609 5.56641 20.9766C4.72266 20.4922 3.96484 19.9062 3.29297 19.2188C2.62109 18.5312 2.03516 17.7695 1.53516 16.9336C1.03516 16.0977 0.65625 15.2031 0.398438 14.25C0.140625 13.2969 0.0078125 12.2969 0 11.25C0 10.2109 0.132812 9.21484 0.398438 8.26172C0.664062 7.30859 1.03906 6.41016 1.52344 5.56641C2.00781 4.72266 2.59375 3.96484 3.28125 3.29297C3.96875 2.62109 4.73047 2.03516 5.56641 1.53516C6.40234 1.03516 7.29688 0.65625 8.25 0.398438C9.20312 0.140625 10.2031 0.0078125 11.25 0ZM11.25 21C12.1484 21 13.0117 20.8828 13.8398 20.6484C14.668 20.4141 15.4414 20.0859 16.1602 19.6641C16.8789 19.2422 17.5391 18.7344 18.1406 18.1406C18.7422 17.5469 19.25 16.8906 19.6641 16.1719C20.0781 15.4531 20.4062 14.6758 20.6484 13.8398C20.8906 13.0039 21.0078 12.1406 21 11.25C21 10.3516 20.8828 9.48828 20.6484 8.66016C20.4141 7.83203 20.0859 7.05859 19.6641 6.33984C19.2422 5.62109 18.7344 4.96094 18.1406 4.35938C17.5469 3.75781 16.8906 3.25 16.1719 2.83594C15.4531 2.42188 14.6758 2.09375 13.8398 1.85156C13.0039 1.60938 12.1406 1.49219 11.25 1.5C10.3516 1.5 9.48828 1.61719 8.66016 1.85156C7.83203 2.08594 7.05859 2.41406 6.33984 2.83594C5.62109 3.25781 4.96094 3.76562 4.35938 4.35938C3.75781 4.95312 3.25 5.60938 2.83594 6.32812C2.42188 7.04688 2.09375 7.82422 1.85156 8.66016C1.60938 9.49609 1.49219 10.3594 1.5 11.25C1.5 12.1484 1.61719 13.0117 1.85156 13.8398C2.08594 14.668 2.41406 15.4414 2.83594 16.1602C3.25781 16.8789 3.76562 17.5391 4.35938 18.1406C4.95312 18.7422 5.60938 19.25 6.32812 19.6641C7.04688 20.0781 7.82422 20.4062 8.66016 20.6484C9.49609 20.8906 10.3594 21.0078 11.25 21ZM12 10.5H18V12H12V18H10.5V12H4.5V10.5H10.5V4.5H12V10.5Z" fill="url(#paint0_linear_500_232)"/>
                            <defs>
                            <linearGradient id="paint0_linear_500_232" x1="-4.5" y1="23" x2="29" y2="6" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FFEB0B"/>
                            <stop offset="1" stop-color="#C11218"/>
                            </linearGradient>
                            </defs>
                            </svg>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const team_id = urlParams.get('t');
        const email_input = document.querySelector('#email');
        let goBack = ()=>{
            window.history.back();
        }

        email_input.addEventListener('input',(el)=>{
            if(el.target.value.length >= 3){
                console.log('email is valid');
                let data = {
                    update: el.target.value,
                    sport: '',
                    for : 'add_staff'
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
                        let info_container = document.querySelector('.history-staff');
                        let saved_staff = document.querySelector('.saved_staff');
                            saved_staff.style.display = 'none';
                        info_container.innerHTML = data;
                    })
                    .catch(error => console.error(error))
                }
            }else{
                displaySavedStaff();
            }
        });

        //save staff
        function saveStaffToLocal(user) {
            let existingStaff = JSON.parse(localStorage.getItem('savedStaff')) || [];

            const exists = existingStaff.some(staff => staff.email === user.email);

            if (!exists) {
                existingStaff.push(user);
                localStorage.setItem('savedStaff', JSON.stringify(existingStaff));
                console.log('Staff saved:', user);
            } else {
                console.log('Staff already exists');
            }

            let user_data = {
                email : user.email,
                team_id : team_id
            }
            fetch('../Backend/add_staff-to-team.php',{
                method: 'POST',
                body: JSON.stringify(user_data),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then((data)=>{
                console.log(data);
            })
            .catch(error => console.error(error));

            window.history.back();
        }

        //add staff
       let add_staff = (el) => {
            let parent = el.closest('.team');
            let email = parent.querySelector('.staff-name').getAttribute('data-staff_email');

            let data = {
                email : email 
            };
            saveStaffToLocal(data);
        }

        // Display previously saved staff
        displaySavedStaff();
        function displaySavedStaff() {
            let staffList = JSON.parse(localStorage.getItem('savedStaff')) || [];
            let container = document.querySelector('.history-staff');
            container.innerHTML = '';

            if (staffList.length > 0) {
                staffList.forEach(staff => {
                    let data = {
                    update: staff.email,
                    sport: 'saved',
                    for : 'add_staff'
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
                            let info_container = document.querySelector('.history-staff');
                            let saved_staff = document.querySelector('.saved_staff');
                            saved_staff.style.display = 'block';
                            info_container.innerHTML = info_container.innerHTML+data;
                        })
                        .catch(error => console.error(error))
                    }
                });
            } else {
                container.innerHTML = '<p>No previously used staff found.</p>';
            }
        }
    </script>
</body>
</html>