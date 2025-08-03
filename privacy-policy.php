<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <h1 style="text-align: center; font-size: 1.8rem;">Privacy Policy</h1>
                <div style="text-align: center; font-size: 1.1rem;">LiveStrike - Where Your Trust is Honored and Your Privacy Respected</div>
            </div>
        </div>
        <div class="container2">
            At <b>LiveStrike</b> (the Real-Time Sports Score Tracking System), we value your trust and are committed to <b>protecting your privacy</b>. This <b>Privacy Policy</b> outlines how we collect, use, disclose, and safeguard your information when you use our platform, including our website, web application, and any future mobile applications.

            <div class="info">
                <h3>Introduction</h3>
                <b>LiveStrike</b> is committed to respecting and protecting the <b>privacy</b> of its users. This <b>Privacy Policy</b> describes how we handle the personal and non-personal information you provide while using our platform. By accessing or using any part of LiveStrike, including our website and mobile applications, you <b>agree</b> to the terms set forth in this Privacy Policy. We are dedicated to maintaining <b>transparency</b> about what data we collect, how we use it, and how we protect it.
            </div>

            <div class="info">
                <h3>Data Collection</h3>
                We collect information that helps us <b>provide and improve</b> our services. This includes personal information you submit during registration or while updating your profile, such as your name, email address, phone number, and any <b>role-specific identifiers</b> like team or scorer affiliation. We also collect <b>usage data</b> as you interact with the platform, including the matches you participate in or follow, the updates you submit or view, and the sessions you initiate. Automatically, we gather <b>technical information</b> such as your device type, operating system, browser, IP address, time zone, and interaction timestamps.
            </div>

            <div class="info">
                <h3>Use of Information</h3>
                The information we collect is primarily used to <b>deliver the core functionalities</b> of the LiveStrike platform. This includes registering your account, authenticating your login, assigning you to a role such as scorer or player, and allowing you to access features relevant to your role. It enables us to <b>manage and display real-time scores</b>, schedule matches, track player statistics, and offer personalized experiences to each user.
            </div>

            <div class="info">
                <h3>Data Sharing and Disclosure</h3>
                We do <b>not sell or rent</b> your personal information to any third party. However, we may share your data with carefully selected <b>service providers</b> who help us operate the platform efficiently. These may include notification services, analytics partners, authentication systems, or other technical infrastructure providers. Each of these entities is bound by <b>strict confidentiality agreements</b>.
            </div>

            <div class="info">
                <h3>Legal basis for Processing</h3>
                Depending on where you reside, we process your data under different <b>lawful bases</b>. This may include your <b>consent</b>, which you provide explicitly when creating an account or choosing to participate in features like notifications. In many cases, the processing of your data is necessary to <b>fulfill our contractual obligations</b>.
            </div>

            <div class="info">
                <h3>Data Retention</h3>
                We retain your <b>personal data</b> for as long as it is necessary to provide our services to you. This means your data remains active while your account is active and for a reasonable period thereafter to handle <b>backups, logs, dispute resolution</b>, or regulatory matters.
            </div>

            <div class="info">
                <h3>Data Security</h3>
                We take the <b>protection</b> of your personal information seriously and employ industry-standard security practices to safeguard your data. This includes access control mechanisms, encrypted communications, secure data storage, and real-time monitoring of our systems.
            </div>

            <div class="info">
                <h3>International users</h3>
                If you are accessing LiveStrike from a country outside India, please be aware that your information may be <b>transferred to and stored</b> in India or other jurisdictions where our servers or service providers operate. By using our platform, you <b>consent</b> to the transfer of your data to these locations.
            </div>

            <div class="info">
                <h3>Children's Privacy</h3>
                LiveStrike is <b>not intended</b> for use by children under the age of 13, and we do not <b>knowingly collect</b> personal information from anyone in this age group.
            </div>

            <div class="info">
                <h3>Your Rights</h3>
                As a user, you have the right to <b>access</b> the personal information we hold about you, <b>correct</b> any inaccuracies, and <b>request the deletion</b> of your data. You may also <b>object</b> to certain types of data processing or request the <b>restriction</b> of processing in certain circumstances.
            </div>

            <div class="info">
                <h3>Changes to this Policies</h3>
                LiveStrike may <b>update</b> this Privacy Policy from time to time to reflect <b>legal, technical, or operational changes</b>. When we do, we will revise the "Last Updated" date at the top of the policy.
            </div>

            <div class="info">
                <h3>Contact us</h3>
                If you have any <b>questions, concerns, or feedback</b> about this Privacy Policy or our data practices, please contact us at [Insert Official Email Address or Contact Form URL].
            </div>
        </div>
    </div>
</body>
<script>
    let goBack = ()=>{
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
</html>