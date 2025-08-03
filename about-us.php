<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - LiveStrike</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            -webkit-user-drag: none;
            user-select: none;
            scrollbar-width: none;
        }

        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --primary-light-transparent: rgba(250, 192, 31, 0.1);
            --primary-dark-transparent: rgba(248, 57, 0, 0.1);
            --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            --text-color: #333333;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            min-height: 100vh;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .header {
            background: var(--background);
            padding: 20px;
            color: white;
            height: 220px;
            width: 100%;
            max-width: 800px;
        }

        .return {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .return svg {
            cursor: pointer;
            transition: var(--transition);
            fill: white;
        }

        .return svg:hover {
            transform: translateX(-3px);
        }

        .header-txt {
            display: flex;
            gap: 25px;
            flex-direction: column;
        }
        .container2 {
            margin-top: 20px;
            padding: 20px;
            line-height: 27px;
            font-size: 18px;
        }
        .para {
            position: relative;
        }

        .info {
            margin-top: 20px;
        }

        h3 {
            margin-bottom: 10px;
        }
        
        .team-member {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 15px 0;
            padding: 15px;
            background-color: var(--primary-light-transparent);
            border-radius: var(--border-radius);
        }
        
        .team-member img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-dark);
        }
        
        .team-info h4 {
            color: var(--primary-dark);
            margin-bottom: 5px;
        }
        
        .team-info p {
            font-size: 16px;
            color: var(--text-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="return">
                <svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z"/>
                </svg>
            </div>
            <div class="header-txt">
                <h1 style="text-align: center; font-size: 1.8rem;">About LiveStrike</h1>
                <div style="text-align: center; font-size: 1.1rem;">Revolutionizing Real-Time Sports Scoring</div>
            </div>
        </div>
        <div class="container2">
            <p><b>LiveStrike</b> is an innovative real-time sports scoring platform designed to transform how athletic competitions are tracked, managed, and experienced. Founded in 2025, our mission is to bring cutting-edge technology to sports at all levels, from local community games to professional tournaments.</p>

            <div class="info">
                <h3>Our Vision</h3>
                <p>We envision a world where every sports event, regardless of size or location, can benefit from professional-grade scoring technology. Our platform eliminates the barriers between players, officials, and fans, creating a <b>seamless, interactive experience</b> that enhances the joy of competition.</p>
            </div>

            <div class="info">
                <h3>What We Do</h3>
                <p>LiveStrike provides a <b>comprehensive scoring solution</b> that includes:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Real-time score tracking and statistics</li>
                    <li>Automated match reporting and analytics</li>
                    <li>Player performance metrics</li>
                    <li>Live updates for fans and spectators</li>
                    <li>Customizable scoring systems for various sports</li>
                </ul>
            </div>

            <div class="info">
                <h3>Our Technology</h3>
                <p>Built with the latest web and mobile technologies, LiveStrike offers:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li><b>Cloud-based infrastructure</b> for reliable access anywhere</li>
                    <li><b>Intuitive interfaces</b> designed for scorers of all experience levels</li>
                    <li><b>Cross-platform compatibility</b> across devices</li>
                    <li><b>Secure data handling</b> with enterprise-grade encryption</li>
                </ul>
            </div>

            <div class="info">
                <h3>Meet the Team</h3>
                
                <div class="team-member">
                    <img src="https://i.ibb.co/rGZDkQsV/IMG-20250701-192708.jpg" alt="Founder">
                    <div class="team-info">
                        <h4>Sonawane Nilesh</h4>
                    </div>
                </div>
                
                <div class="team-member">
                    <img src="https://i.ibb.co/RpR7xkpz/kp.jpg" alt="CTO">
                    <div class="team-info">
                        <h4>Patil Kaustubh</h4>
                    </div>
                </div>
                
                <div class="team-member">
                    <img src="https://i.ibb.co/zWGGS3kS/prabhavati.jpg" alt="Product">
                    <div class="team-info">
                        <h4>Jadhav Prabhavati</h4>                       
                    </div>
                </div>

                <div class="team-member">
                    <img src="https://i.ibb.co/p6MqTrx4/Ketki.jpg" alt="Product">
                    <div class="team-info">
                        <h4>Sonawane Ketki</h4>                       
                    </div>
                </div>
            </div>

            <div class="info">
                <h3>Our Values</h3>
                <p>At LiveStrike, we're guided by core principles that shape everything we do:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li><b>Accuracy First:</b> Precision scoring you can trust</li>
                    <li><b>Accessibility:</b> Technology that serves all levels of sport</li>
                    <li><b>Innovation:</b> Continuous improvement of our platform</li>
                    <li><b>Community:</b> Supporting local sports ecosystems</li>
                    <li><b>Passion:</b> We love sports as much as you do</li>
                </ul>
            </div>

            <div class="info">
                <h3>Join Our Mission</h3>
                <p>Whether you're an athlete, official, league organizer, or fan, we invite you to be part of the LiveStrike movement. Together, we're changing the game - one match at a time.</p>
                <p style="margin-top: 10px;">Have questions or want to learn more? <b>Contact our team</b> at [email protected]</p>
            </div>
        </div>
    </div>
    
    <script>
        function goBack() {
            window.history.back();
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