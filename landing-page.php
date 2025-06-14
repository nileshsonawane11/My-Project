<?php
    session_start();
    if(isset($_SESSION['user'])){
        header('location: ./dashboard.php?update="live"&sport="CRICKET"');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <title>LiveStrike</title>
    <style>
        *{
            margin: 0px;
            padding: 0px;
            user-select: none;
        }
        body{
            background-color: #f0f0f0;
        }
        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(0deg, var(--primary-light), var(--primary-dark));
        }
        .nav-bar{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px -5px 20px black;
            padding: 10px;
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            z-index: 999;
        }
        .items,.list{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }
        .nav-content{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding-left: 20px;
            padding-right: 50px;
        }
        .img-container{
            position: relative;
            width: 100%;
            margin-top: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .img-container img{
            object-fit: cover;
            width: 100%;
        }
        @keyframes slider{
            0%{
                transform: translateX(300px);
            }
            5%{
                transform: translateX(0px);
            }
            95%{
                transform: translateX(0px);
            }
            100%{
                transform: translateX(-300px);
            }
        }
        .img-indicator{
            max-width: 100%;
            height: 20px;
            padding: 10px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .img-bar{
            height: 10px;
            width: 10px;
            background-color: #FAC01F;
            border-radius: 25px;
            transition: all 0.2s ease-in-out;
        }
        .block-container{
            max-width: 100%;
            overflow: hidden;
            text-align: center;
            display: grid;
            gap: 50px;
            line-height: 30px;
            margin-top: 60px;
            justify-items: center;
            align-items: center;
            justify-content: center;
            align-content: center;
        }
        .blocks{
            padding: 20px;
            width: 80%;
            margin:auto;
            border-radius: 15px;
            background-color: #D9D9D9;
        }
        .stage-container{
            display: grid;
            max-width: 100%;
            overflow: hidden;
            justify-content: center;
            align-items: center;
            align-self: flex-start;
            margin: 100px auto;
        }
        .stage{
            position: relative;
            height: 140px;
            width: 260px;
            display: flex;
            flex-direction: column;
            gap: 7px;
            border-left: solid 2px black;
            padding-left: 30px;
        }
        .stage-container > *:last-child{
            border: none;
            height: fit-content;
        }
        .circle{
            position: absolute;
            height: 10px;
            width: 10px;
            background-color: black;
            border-radius: 50%;
            border: solid 2px ;
            left: -5px;
            top: 0;
        }
        .footer{
            margin-top: 100px;
        }
        .footer .footer-content{
            margin-top: 60px;
            text-decoration: none;
            font-style: none;
            background-color: #D9D9D9;
            border-radius: 30px 30px 0px 0px;
        }
        a{
            text-decoration: none;
            color: black;
        }
        .footer .footer-legal {
            background: rgb(255, 255, 255);
            padding: 30px 0px;
        }
        .footer .footer-legal .copyright {
            text-align: center;
            color: var(--color-secondary);
        }
        .footer .footer-legal .credits {
            padding-top: 4px;
            text-align: center;
            font-size: 13px;
            color: var(--color-secondary);
        }a {
            color: rgb(0, 164, 239);
            transition: 0.5s;
            text-decoration: none;
        }
        .footer li{
            list-style: none;
            
        }
        .footer li a{
            color: rgb(60, 60, 60);
            font-size: 13px;
        }
        .footer ol, ul {
            padding-left: 1rem;
        }
        .team{
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: start;
            align-self: start;
            margin-top: 20px;
        }
        .mem-img {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            background-color: #cecece;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
        
        .mem-img::after {
            content: "";
            position: absolute;
            top: -5px;
            left: -5px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-light), var(--primary-dark), var(--primary-light));
            z-index: -1;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .mem-img.loaded::after {
            animation: spin 3s linear infinite;
            opacity: 1;
        }
        
        .mem-img img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            position: relative;
            z-index: 2;
        }
        
        
        .teammates{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 20px;
        }
        .mem-name{
            max-width: 120px;
            text-wrap: wrap;
            font-weight: 600;
            text-align: center;
        }
        
        .list-items:hover{
            background-image: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
            background-clip: text;
            -webkit-text-fill-color: transparent;
            padding-bottom: 5px;
            border-bottom: 1px solid transparent;
            border-image: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
            border-image-slice: 1;
        }
        @keyframes spin{
            to{
                transform: rotate(360deg);
            }
        }
        @media(min-width: 601px) {
            .menu-bar{
                display: none;
            }
            .logo-img img{
                height: 45px;
            }
            .txt-strike{
                font-weight: 200;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .logo-name {
                font-size: 25px;
                color: black;
                text-align: center;
                width: 130px;
                font-weight: 400;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0;
            }
            .list{
                gap: 30px;
            }
            .list-items{
                font-size: 20px;
                gap: 15px;
                cursor: pointer;
                transition: all 0.2s ease-in-out;
            }
            .img-opacity{
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 0;
                background-color: rgba(0, 0, 0, 0.6);
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 50px;
            }
            .img-text2,.img-text1{
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                color: white;
                gap: 60px;
                text-align: center;
                padding: 60px;
            }
            .img-text2 .img-btn{
                height: 49px;
                width: 161px;
                font-size: 15px;
                border-radius: 100px;
                font-weight: 700;
                background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                border: none;
            }
            .img-para{
                font-size: 25px;
            }
            .img-head{
                font-size: 40px;
            }
            .headers{
                margin: 30px auto;
                text-align: center;
                font-size: 30px;
            }
            .slides-container{
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                overflow: hidden;
            }
            .slides{
                display: flex;
                margin: 30px;
                justify-content: center;
                flex-direction: column;
                align-items: center;
                border-radius: 15px;
                box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.3);
                padding: 20px;
                animation: slider 4.5s ease-in-out forwards;
                position: relative;
                gap: 20px;
                text-align: center;
            }
            
            .block-container > *:last-child {
                grid-column: span 2;
                justify-self: center;
                width: 500px;
            }
            .block-container{
                 grid-template-columns: 1fr 1fr;
            }
            .sidebar{
                display: none;
            }
        }
        @media(max-width: 600px) {
            .logo-img img{
                height: 40px;
            }
            .nav-content{
                padding-right: 20px;
                display: flex;
                justify-content:space-between ;
                align-items: center;
                width: 100%;
            }
            .sidebar {
                position: fixed;
                right: 0;
                top: 0;
                width: 250px;
                height: 100%;
                background: #f4f4f4;
                transform: translateX(100%);
                transition: transform 0.5s ease;
                z-index: 99999;
              }
              
              .sidebar.active {
                transform: translateX(0);
              }
            .list-items{
                display: none;
            }
            .menu-bar{
                position: absolute;
                right: 30px;
            }
            #close-sidebar{
                text-align: right;
                font-size: 42px;
                font-weight: 500;
                margin: 0px 30px 70px;
                cursor: pointer;
            }
            .txt-strike{
                font-weight: 200;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .logo-name {
                font-size: 25px;
                color: black;
                text-align: center;
                width: 130px;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0rem;
                text-align: left;
            }
            .img-container img{
                object-fit: cover;
                width: 250%;
            }
            .img-opacity{
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 0;
                background-color: rgba(0, 0, 0, 0.6);
                display: flex;
                color: white;
                flex-direction: column;
                gap: 40px;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            .img-text2{
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                gap: 80px;
            }
            .img-text2 .img-btn{
                height: 49px;
                width: 161px;
                font-size: 15px;
                border-radius: 100px;
                font-weight: 700;
                background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
                border: none;
            }
            .img-text2 .img-para{
                font-size: 17px;
                padding: 30px;
            }
            .headers{
                margin: 20px auto;
                text-align: center;
                font-weight: 700;
                color: #787878;
            }
            h5{
                font-weight: 600;
            }
            .slides-container{
                
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                justify-content: center;
                align-items: center;
                overflow: hidden;
                height: max-content;
            }
            .slides{
                display: flex;
                margin: 30px;
                justify-content: center;
                flex-direction: column;
                align-items: center;
                border-radius: 15px;
                box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.3);
                padding: 20px;
                animation: slider 4.5s ease-in-out forwards;
                position: relative;
                gap: 20px;
                text-align: center;
            }
            .img-indicator{
                height: 20px;
            }
            .blocks p{
                font-size: 14px;
            }
            .menu-list{
                display: flex;
                gap: 41px;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .list1{
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 20px;
                width: 80%;
                background-color: rgb(217, 217, 217, 0.3);
                height: 50px;
                text-align: center;
            }
            
        }
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: white;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
          }
      
          .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #ddd;
            border-top: 3px solid #EECF4F;
            border-radius: 50%;
            animation: spin 1s linear infinite;
          }
    </style>
</head>
<body>
    <div class="body">
        <div id="loader">
            <div class="spinner"></div>
        </div>
        <div class="sidebar">
            <div id="close-sidebar">&times;</div>
            <div class="menu-list">
                <div class="list1">About</div>
                <div class="list1">Contact</div>
                <div class="list1">Feedback</div>
            </div>
        </div>
        <div>
            <nav class="nav-bar">
                <div class="nav-content">
                    <div class="items">
                        <div class="logo-img"><img src="https://i.ibb.co/gLY2MgSd/logo.png" alt=""></div>
                        <div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
                    </div>
                    <div class="items">
                        <a href="" class="menu-bar"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAGZJREFUSEvtlrENACAMw8pnnMZpfAYTC1W3CDOEA2JhUpUW0GkQNwx+Zt6qj+ohdp7yKtVLDE6c78DiC+c4t/o46WLX8877rlzYOGGqxU/scYryB4KVCwNja9GtlhvwWpQrrQIx1Rt3TwofeC3yFwAAAABJRU5ErkJggg=="/></a>
                    </div>
                    <div class="items list">
                        <div class="list-items">About</div>
                        <div class="list-items">Contact</div>
                        <div class="list-items">Feedback</div>
                    </div>
                </div>
            </nav>
            
            <div class="img-container">
                <img src="https://i.ibb.co/G4tHhjBc/landing-img.jpg" alt="">
                <div class="img-opacity">
                    <div class="img-text1">
                        <h1 class="img-head">Live Scores, Instant<br>Updates & More</h1>
                    </div>
                    <div class="img-text2">
                        <div class="img-content"><p class="img-para">Stay updated with real-time match scores, player stats, and game highlights—all in one place.</p></div>
                        <div class="img-content"><button class="img-btn" onclick="nextpage()">Get Started</button></div>
                    </div>
                </div>
            </div>

            <h2 class="headers">Our Trends</h2>
            <div class="slides-container">
                <div class="slides">
                    <div class="slide-img"></div>
                    <div><h2 class="slide-head">The Power Of Sports</h2></div>
                    <div><p class="slide-para">Sports improve health, teach teamwork, and build confidence. They help people stay active, develop skills, and connect with others, all while having fun and pushing personal limits.</p></div>
                </div>
                
                <div class="slides">
                    <div class="slide-img"></div>
                    <div><h2 class="slide-head">The Joy Of Sports</h2></div>
                    <div><p class="slide-para">Sports bring excitement, challenge, and fun. They help improve fitness, encourage teamwork, and provide a sense of achievement. Whether for competition or enjoyment, sports play an important role in maintaining a healthy lifestyle.</p></div>
                </div>
                <div class="slides">
                    <div class="slide-img"></div>
                    <div><h2 class="slide-head">The Spirit Of Sports</h2></div>
                    <div><p class="slide-para">Sports inspire teamwork, dedication, and personal growth. They offer a way to stay fit, have fun, and challenge oneself. Whether played casually or competitively, sports bring people together and teach valuable life lessons.</p></div>
                </div>
            </div>
            
            <div class="img-indicator">
                <span class="img-bar"></span>
                <span class="img-bar"></span>
                <span class="img-bar"></span>
            </div>

            <h2 class="headers">Features</h2>
            <div class="block-container">
                <div class="blocks"><h5>Live & Fast Updates</h5><p>(Get match scores instantly)</p></div>
                <div class="blocks"><h5>Open-Source & Free</h5><p>(No hidden costs, fully community-driven)</p></div>
                <div class="blocks"><h5>User-Friendly Interface</h5><p>(Simple, clean, and easy to navigate)</p></div>
                <div class="blocks"><h5>Customizable & Scalable</h5><p>(Modify it to fit your needs)</p></div>
                <div class="blocks"><h5>Multi-Sport Coverage</h5><p>(Supports various sports like football, cricket, and more)</p></div>
            </div>

            <h2 class="headers">How It Works?</h2>
            <div class="stage-container">
                <div class="stage">
                    <h5>Access Scores -</h5>
                    <p>View real-time match updates</p>
                    <span class="circle"></span>
                </div>
                <div class="stage">
                    <h5>Track Teams & Players –</h5>
                    <p>Follow your favorite teams and players</p>
                    <span class="circle"></span>
                </div>
                <div class="stage">
                    <h5>Contribute & Improve -</h5>
                    <p>Join the open-source community and enhance the system</p>
                    <span class="circle"></span>
                </div>
            </div>

            <h2 class="headers">Experience seamless live score tracking—fast, free, and open to all</h2>
            <footer class="footer">
                <div class="footer-content">
                    <div class="container">
                        <div class="row gy-4">
                            <div class="col-lg-4 col-md-12 footer-info">
                                <a href="landing-page.html" class="logo d-flex align-items-center">
                                    <h2><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></h2>
                                </a>
                                <p>
                                    <br><br>
                                    <strong>Your Ultimate Real-Time Sports Tracking Platform</strong><br>
                                    <strong>Phone:</strong><a href="tel:+91 9607828130">+91 XXXXXXXXXX</a><br>
                                    <strong>Email:</strong><a href="mailto:sonawanenileshk6@gmail.com">
                                        contact@yourdomain.com</a><br> 
                                </p>
            
                            </div>
            
                            <div class="col-lg-2 col-6 footer-links">
                                <h4>About US</h4>
                                <ul>
                                    <li> <a href="#">What is LiveStrike?</a></li>
                                    <li> <a href="#">Meet the Team</a></li>
                                    <li> <a href="#">Our Vision & Mission</a></li>
                                    <li> <a href="#">Live Coverage Features</a></li>
                                    <li> <a href="#">Media & Press</a></li>
                                </ul>
                            </div>
            
                            <div class="col-lg-2 col-6 footer-links">
                                <h4>Features</h4>
                                <ul>
                                    <li> <a href="#">Real-Time Score Updates</a></li>
                                    <li> <a href="#">Match Schedules</a></li>
                                    <li> <a href="#">Player & Team Stats</a></li>
                                    <li> <a href="#">Leaderboards</a></li>
                                    <li> <a href="#">Notifications & Alerts</a></li>
                                </ul>
                            </div>
            
                            <div class="col-lg-2 col-6 footer-links">
                                <h4>Resources</h4>
                                <ul>
                                    <li> <a href="#">User Guide</a></li>
                                    <li> <a href="#">FAQs</a></li>
                                    <li> <a href="#">Terms & Conditions</a></li>
                                    <li> <a href="#">Privacy Policy</a></li>
                                    <li> <a href="#">Contact Support</a></li>
                                </ul>
                            </div>
            
                            <div class="col-lg-2 col-6 footer-links">
                                <h4>Community</h4>
                                <ul>
                                    <li> <a href="#">Feedback & Suggestions</a></li>
                                    <li> <a href="#">Report an Issue</a></li>
                                    <li> <a href="#">Fan Gallery</a></li>
                                    <li> <a href="#">Join Our Beta</a></li>
                                    <li> <a href="#">Alumni Shoutouts</a></li>
                                    <li> <a href="./front-page.php">Login</a></li>
                                </ul>
                            </div>
            
                        </div>
                    </div><hr>
                    <h4 style="text-align: center;">Team :</h4>
                    <div class="team">
                        <div class="teammates">
                            <div class="img-border">
                                <div class="mem-img"><img src="https://i.ibb.co/5WYMftRQ/myimg.jpg" alt="Member 1"  onerror="showAlt(this)" onload="this.parentElement.classList.add('loaded')"></div>
                            </div>
                            <p class="mem-name">Sonawane Nilesh</p>
                        </div>
                        <div class="teammates">
                            <div class="img-border">
                                <div class="mem-img"><img src="https://i.ibb.co/RpR7xkpz/kp.jpg" alt="Member 2"  onerror="showAlt(this)" onload="this.parentElement.classList.add('loaded')"></div>
                            </div>
                            <p class="mem-name">Patil Kaustubh</p>
                        </div>
                        <div class="teammates">
                            <div class="img-border">
                                <div class="mem-img"><img src="" alt="Member 3"  onerror="showAlt(this)" onload="this.parentElement.classList.add('loaded')"></div>
                            </div>
                            <p class="mem-name"></p>          
                        </div>
                        <div class="teammates">
                            <div class="img-border">
                                <div class="mem-img"><img src="" alt="Member 4"  onerror="showAlt(this)" onload="this.parentElement.classList.add('loaded')"></div>
                            </div>
                            <p class="mem-name"></p>   
                        </div>
                        <div class="teammates">
                            <div class="img-border">
                                <div class="mem-img"><img src="" alt="Member 5"  onerror="showAlt(this)" onload="this.parentElement.classList.add('loaded')"></div>
                            </div>
                            <p class="mem-name"></p>  
                        </div>
                    
                </div>
                <div class="footer-legal">
                    <div class="container">
                        <div class="copyright">
                            2025 © Copyright <strong><span>LiveStrike</span></strong> All Rights Reserved.
                                <div class="credits text-center">
                                    Designed &amp; Developed by <a href="#" target="_blank">Sonawane Nilesh</a>, <a href="#" target="_blank">Patil Kaustubh</a>, <a href="#" target="_blank">Jadhav Prabhavati</a>, <a href="#" target="_blank">Chavan Pranav</a>, <a href="#" target="_blank">Sonawane Ketki </a>Department of Information Technology, Government Polytechnic Nashik (Batch 2023–2026)
                                    <br>Guided by <a target="_blank" href="#" rel="follow"><u>Prof: Mr.P.B.Mali</u></a>
                                </div>
                        </div>
                    </div>
                </div>
            </footer>
            
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script>
        const slides = document.querySelectorAll('.slides');
        const img_bar = document.querySelectorAll('.img-bar');

        let count = 0;

        let slideshow = ()=>{
            for(let i = 0 ; i < slides.length; i++){
                slides[i].style.display ='none';
                img_bar[i].style.width = '10px';
            }
            count++;
            if(count > slides.length){
                count = 1;
            }
            slides[count-1].style.display = 'flex';
            img_bar[count-1].style.width = '20px';
        }
        slideshow();
        let interval = setInterval(slideshow, 4500);

        function showAlt(img) {
            const altText = img.alt;
            const container = img.parentElement;
        
            img.style.display = "none";
        
            const span = document.createElement("span");
            span.textContent = altText;
            container.appendChild(span);
        }

        const menu_bar = document.querySelector('.menu-bar');
        const side = document.querySelector('.sidebar');
        const closeside = document.querySelector('#close-sidebar');

        menu_bar.addEventListener('click', function (e) {
            e.preventDefault();
            side.classList.add('active');
        });

        closeside.addEventListener('click',function(){
            side.classList.remove('active');
        });

        let nextpage = ()=>{
            document.getElementById('loader').style.display = 'flex';

  
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = './front-page.php';

            iframe.onload = function () {

            window.location.href = './front-page.php';
            };

            document.body.appendChild(iframe);
        }

    </script>
</body>
</html>
