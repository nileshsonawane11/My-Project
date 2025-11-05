<?php
session_start();
include './config.php';
$email = $_SESSION['email'] ?? '';
$submitted = false;
$result = $conn->query("SELECT user_email FROM feedback where user_email = '$email' LIMIT 1");
if($result->num_rows > 0){
    $submitted = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - LiveStrike</title>
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
        --primary-color: rgba(209, 34, 31, 1);
        --primary-light: rgba(209, 34, 31, 0.8);
        --primary-dark: rgba(160, 25, 23, 1);
        --primary-transparent: rgba(209, 34, 31, 0.1);
        --background: #ffffff;
        --text-color: #000000;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        --border-radius: 12px;
        --transition: all 0.3s ease;
        --border-color: #dddddd;
        --bar-color : #8080802e;
        --arrow-bg : #e8e8e8;
    }

    [data-theme="dark"] {
        --background: #121212;
        --text-color: #ffffff;
        --light-bg: #1e1e1e;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        --border-color: #333333;
        --bar-color : #ffffff;
        --arrow-bg : #333333ff;
    }

    body {
        min-height: 100vh;
        background-color: var(--light-bg);
        color: var(--text-color);
        display: flex;
        justify-content: center;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .container {
        width: 100%;
        max-width: 800px;
        background: var(--background);
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    .header {
        background: var(--primary-color);
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
        opacity: 0.8;
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
        color: var(--text-color);
        transition: color 0.3s ease;
    }
    
    .para {
        position: relative;
    }

    .info {
        margin-top: 20px;
    }

    h3 {
        margin-bottom: 10px;
        color: var(--text-color);
        transition: color 0.3s ease;
    }
    
    .feedback-form {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    label,.ranks-no {
        font-size: 17px;
        font-weight: 600;
        color: var(--primary-color);
        transition: color 0.3s ease;
    }
    
    input, textarea, select {
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 16px;
        transition: var(--transition);
        background-color: var(--background);
        color: var(--text-color);
    }
    
    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px var(--primary-transparent);
    }
    
    textarea {
        min-height: 150px;
        resize: vertical;
    }
    
    .rating {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .star {
        font-size: 28px;
        color: var(--border-color);
        cursor: pointer;
        transition: var(--transition);
    }
    
    .star:hover, .star.active {
        color: var(--primary-color);
    }
    
    .submit-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 15px;
        border-radius: var(--border-radius);
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 10px;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(209, 34, 31, 0.3);
        background: var(--primary-dark);
    }
    
    .thank-you {
        text-align: center;
        padding: 40px 20px;
        display: none;
        color: var(--text-color);
        transition: color 0.3s ease;
    }
    
    .thank-you h2 {
        color: var(--primary-color);
        margin-bottom: 15px;
        transition: color 0.3s ease;
    }
    
    .thank-you p {
        font-size: 16px;
    }

    .astr{
        color:red;
    }
    input:disabled {
        background-color: #eee;
        cursor: not-allowed;
    }
    .ratings{
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-top: 30px;
        gap: 20px;
    }
    .notice{
        font-size: 15px;
    }
    .rating-txt{
        font-weight: 500;
    }
    .rate-container{
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-direction: row;
        gap: 30px;
    }
    .rate-avg{
        display: flex;
        justify-content: flex-end;
        flex-direction: column;
        align-items: center;
        font-size: x-large;
        gap: 5px;
        padding: 20px;
    }
    .rate-list{
        text-align: left;
        display: flex;
        align-items: flex-start;
        flex: 1;
    }
    .rate-container li{
        list-style: none;
        width: 100%;
        gap: 5px;
        display: flex;
        align-items: center;
    }
    ul{
        width: 100%;
    }
    .stat{
        width: 100%;
        background: var(--bar-color);
        border-radius: 25px;
        height: 15px;
        display: flex;
        overflow: hidden;
    }
    .ranks-no{
        width: 15px;
        text-align: left;
    }
    .no-rate{
        font-size: 15px;
    }
    .avg-stars{
        font-size: 20px;
    }
    .avg{
        font-size: 30px;
        font-weight: 600;
        color: var(--primary-color);
        transition: color 0.3s ease;
    }
    .mem-feed{
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .mem-head{
        display: flex;
        flex-direction: column;
        gap:5px;
    }
    .mem-info{
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: flex-start;
    }
    .mem-rate-date{
        display: flex;
        gap: 10px;
    }
    .mem-date{
        font-size: 15px;
    }
    .mem-info img{
        border-radius: 50%;
        object-fit: cover;
        height: 45px;
        width : 45px;
    }
    .feedback-list{
        display: flex;
        flex-direction: column;
        gap: 30px;
    }
    .inner-bar{
        background: var(--primary-color);
    }
    .avg-stars {
        font-size: 25px;
        color: #ccc; /* empty color */
        position: relative;
        display: inline-block;
    }

    .avg-stars span {
        color: var(--primary-color);
        position: relative;
        display: inline-block;
    }

    .avg-stars span.partial {
        color: #ccc; /* make background gray */
        position: relative;
    }

    .avg-stars span.partial::before {
        content: "★";
        color: var(--primary-color);
        position: absolute;
        left: 0;
        top: 0;
        width: var(--fill);
        overflow: hidden;
    }
    .mem-rate{
        font-size: 20px;
    }
    .see-all{
        color: var(--primary-color);
        transition: color 0.3s ease;
        cursor: pointer;
    }
    .rate-feedbacks{
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .arr{
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--arrow-bg);
        padding: 5px;
        border-radius: 7px;
        cursor: pointer;
        transition: 0.2s ease;
    }
    .arr:hover{
        transform: translate(1px, -1px) scale(1.1);
    }
    a {
        text-decoration: none;
        color: black;
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
                <h1 style="text-align: center; font-size: 1.8rem;">Your Feedback Matters</h1>
                <div style="text-align: center; font-size: 1.1rem;">Help us improve LiveStrike</div>
            </div>
        </div>
        <div class="container2">
            <p>We're committed to making <b>LiveStrike</b> the best sports scoring platform available. Your feedback helps us understand what's working well and where we can improve. Please take a moment to share your thoughts with us.</p>

            <form class="feedback-form" id="feedbackForm" style="<?= $submitted ? 'display:none;' : 'display:flex;' ?>">
                <div class="form-group">
                    <span><label for="name">Your Name</label><sup class="astr"> *</sup></span>
                    <input type="text" id="name" value="<?php echo $_SESSION['name'] ?? ''; ?>" placeholder="Enter your name" <?php echo $edit = (isset($_SESSION['user'])) ? 'disabled' : ''; ?>>
                </div>
                
                <div class="form-group">
                    <span><label for="email">Email</label><sup class="astr"> *</sup></span>
                    <input type="email" id="email" value="<?php echo $_SESSION['email'] ?? ''; ?>" placeholder="Enter your email" <?php echo $edit = (isset($_SESSION['user'])) ? 'disabled' : ''; ?>>
                </div>
                
                <div class="form-group">
                    <span><label for="feedback-type">Type of Feedback</label><sup class="astr"> *</sup></span>
                    <select id="feedback-type">
                        <option value="">Select feedback type</option>
                        <option value="suggestion">Feature Suggestion</option>
                        <option value="bug">Bug Report</option>
                        <option value="praise">Compliment</option>
                        <option value="general">General Feedback</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <span><label>How would you rate your experience?</label></span>
                    <div class="rating">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <span><label for="message">Your Feedback</label><sup class="astr"> *</sup></span>
                    <textarea id="message" placeholder="Tell us what you think about LiveStrike..." required></textarea>
                </div>
                
                <button type="button" class="submit-btn" onclick="submitFeedback()">Submit Feedback</button>
            </form>
            
            <div class="thank-you" id="thankYou">
                <h2>Thank You for Your Feedback!</h2>
                <p>We appreciate you taking the time to help us improve LiveStrike. Our team will review your comments carefully.</p>
                <p style="margin-top: 15px;">Want to help even more? <a href="#" style="color: var(--primary-dark); font-weight: 600;">Join our beta testing program</a></p>
            </div>

            <div class="ratings">
                <?php
                    $result = $conn->query("SELECT * FROM feedback");
                    $count = $result->num_rows;
                    $sum_rating = 0;
                    $percentages = [];
                    $avg_rating = 0.0;

                    // Initialize count for each rating
                    $rating_counts = [1=>0,2=>0,3=>0,4=>0,5=>0];

                    // Count ratings and sum them
                    while($row = $result->fetch_assoc()){
                        $rating = $row['rating'];
                        $sum_rating += $rating;

                        if(isset($rating_counts[$rating])){
                            $rating_counts[$rating]++;
                        }
                    }

                    // Calculate percentage for each rating
                    foreach($rating_counts as $rate => $rate_count){
                        $percentages[$rate] = $count != 0 ? round(($rate_count / $count) * 100, 1) : 0;
                    }

                    // Calculate average rating
                    if($count != 0){
                        $avg_rating = round($sum_rating / $count, 1);
                    }

                   function generateStars($avg) {
                        $full = floor($avg);               // full stars
                        $fraction = $avg - $full;          // decimal part
                        $empty = 5 - ceil($avg);           // remaining stars

                        $html = "";

                        // full stars
                        for ($i = 0; $i < $full; $i++) {
                            $html .= '<span>★</span>';
                        }

                        // fractional star (only if there's a decimal part)
                        if ($fraction > 0) {
                            $width = $fraction * 100; // e.g. 0.3 → 30%
                            $html .= '<span class="partial" style="--fill:' . $width . '%;">★</span>';
                        }

                        // empty stars
                        for ($i = 0; $i < $empty; $i++) {
                            $html .= '★';
                        }

                        return $html;
                    }

                    $stars = generateStars($avg_rating);

                ?>
                <div class="rate-feedbacks">
                    <div class="rating-txt">Rating and feedbacks</div>
                    <div class="rating-txt"><a href="./feedback_list.php?rating=all"><div class="arr"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M12.043 7.5L9.396 4.854l.708-.708L13.957 8l-3.853 3.854l-.708-.707L12.043 8.5H3v-1z" clip-rule="evenodd"/></svg></div></a></div>
                </div>
                <span class="notice">Every rating and feedback entry on LiveStrike reflects real user experience with our platform.</span>
                <div class="rate-container">
                    <div class="rate-avg">
                        <div class="avg"><?php echo $avg_rating ?></div>
                        <div class="avg-stars"><?= $stars ?></div>
                        <div class="no-rate"><?php echo $count ?></div>
                    </div>
                    <div class="rate-list">
                        <ul>
                            <li class="rank-no1"><label for="" class="ranks-no">1</label><div class="stat"><div class="inner-bar" style="<?= 'width:' . $percentages[1] . '%;' ?>"></div></div></li>
                            <li class="rank-no2"><label for="" class="ranks-no">2</label><div class="stat"><div class="inner-bar" style="<?= 'width:' . $percentages[2] . '%;' ?>"></div></div></li>
                            <li class="rank-no3"><label for="" class="ranks-no">3</label><div class="stat"><div class="inner-bar" style="<?= 'width:' . $percentages[3] . '%;' ?>"></div></div></li>
                            <li class="rank-no4"><label for="" class="ranks-no">4</label><div class="stat"><div class="inner-bar" style="<?= 'width:' . $percentages[4] . '%;' ?>"></div></div></li>
                            <li class="rank-no5"><label for="" class="ranks-no">5</label><div class="stat"><div class="inner-bar" style="<?= 'width:' . $percentages[5] . '%;' ?>"></div></div></li>
                        </ul>
                    </div>
                </div>
                <br>
                <div class="feedback-list">
                   <?php
                        $html = "";
                        $inc = 0;
                        $result = $conn->query("SELECT * FROM feedback ORDER BY `datetime` DESC Limit 3");
                        while ($row = $result->fetch_assoc()) {
                            $inc++;
                            $email = $row['user_email'];
                            $name = $row['user_name'];
                            $message = $row['message'];
                            $rating = $row['rating'];
                            $date = $row['date'];

                            $result2 = $conn->query("SELECT user_photo FROM users WHERE email = '$email'");
                            $row2 = $result2->fetch_assoc();

                            $img = (!empty($row2['user_photo'])) ? './assets/images/users/'.$row2['user_photo'] : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s';

                            $rate_stars = generateStars($rating);
                            $html .= <<<HTML
                                        <div class="mem-feed">
                                            <div class="mem-head">
                                                <div class="mem-info">
                                                    <img src="$img" alt="">
                                                    <label class="mems-name">{$name}</label>
                                                </div>
                                                <div class="mem-rate-date">
                                                    <div class="mem-rate avg-stars">{$rate_stars}</div>
                                                    <div class="mem-date">{$date}</div>
                                                </div>
                                            </div>
                                            <div class="mem-message">
                                                {$message}
                                            </div>
                                        </div>
                                    HTML;
                        }

                        echo $html;
                        if($inc == 3){
                            echo "<a href='./feedback_list.php?rating=all'><div class='see-all'>See all feedbacks</div></a>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function goBack() {
            window.history.back();
        }
        
        // Star rating functionality
        const stars = document.querySelectorAll('.star');
        let currentRating = 0;
        
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = parseInt(star.getAttribute('data-rating'));
                currentRating = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            star.addEventListener('mouseover', () => {
                const rating = parseInt(star.getAttribute('data-rating'));
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.color = 'var(--primary-light)';
                    }
                });
            });
            
            star.addEventListener('mouseout', () => {
                stars.forEach((s, index) => {
                    if (index >= currentRating) {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
        
        function submitFeedback() {
        const message = document.getElementById('message').value;
        const feed_name = document.getElementById('name').value;
        const feed_email = document.getElementById('email').value;
        const feed_type = document.getElementById('feedback-type').value

        if(!feed_email || !feed_name || !feed_type){
            alert('All Fields Required.');
            return;
        }
        
        if (!message) {
            alert('Please enter your feedback before submitting.');
            return;
        }
        
        // Get form data
        const feedbackData = {
            name: feed_name || 'Anonymous',
            email: feed_email || 'No email provided',
            type: feed_type || 'General Feedback',
            rating: currentRating || 1,
            message: message
        };

        // Show loading state
        const submitBtn = document.querySelector('.submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        // Send to FormSubmit.co
        fetch('https://formsubmit.co/ajax/livestrike.in@gmail.com', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                "Feedback Type": feedbackData.type,
                "Rating": feedbackData.rating + ' ★',
                "From": feedbackData.name + ' (' + feedbackData.email + ')',
                "Message": feedbackData.message,
                "_subject": "New Feedback Submission - " + feedbackData.type
            })
        })
        .then(response => response.json())
        .then(data => {
            fetch('./Backend/feedback.php',{
                method : 'post',
                headers: { 
                    'Content-Type': 'application/json'
                },
                body : JSON.stringify(feedbackData)
            })
            .then(res=>res.json())
            .then(data => console.log(data))
            .catch(err => console.log(err))

            console.log('Feedback email sent:', data);
            // Show thank you message
            document.getElementById('feedbackForm').style.display = 'none';
            document.getElementById('thankYou').style.display = 'block';
            document.getElementById('thankYou').scrollIntoView({ behavior: 'smooth' });

        })
        .catch(error => {
            console.error('Error sending feedback:', error);
            alert('Failed to submit feedback. Please try again later.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Feedback';
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

    // Rating functionality
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        let currentRating = 0;
        
        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                currentRating = index + 1;
                stars.forEach((s, i) => {
                    if (i <= index) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            star.addEventListener('mouseover', () => {
                stars.forEach((s, i) => {
                    if (i <= index) {
                        s.style.color = 'var(--primary-color)';
                    }
                });
            });
            
            star.addEventListener('mouseout', () => {
                stars.forEach((s, i) => {
                    if (i >= currentRating) {
                        s.style.color = 'var(--border-color)';
                    }
                });
            });
        });
        
        // Form submission
        const feedbackForm = document.querySelector('.feedback-form');
        if (feedbackForm) {
            feedbackForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const thankYou = document.querySelector('.thank-you');
                if (thankYou) {
                    feedbackForm.style.display = 'none';
                    thankYou.style.display = 'block';
                }
            });
        }
    });
    </script>
</body>
</html>