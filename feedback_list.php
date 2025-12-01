<?php
session_start();
include './config.php';
$email = $_SESSION['email'] ?? '';
$type = $_GET['rating'] ?? 'all';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - LiveStrike</title>
    <meta name="google-adsense-account" content="ca-pub-4540243680881407">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4540243680881407" crossorigin="anonymous"></script>
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
    }

    [data-theme="dark"] {
        --background: #121212;
        --text-color: #ffffff;
        --light-bg: #1e1e1e;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
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
        padding: 10px 20px;
        width: 100%;
        max-width: 800px;
    }

    .return {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .return svg {
        cursor: pointer;
        transition: var(--transition);
        fill: var(--text-color);
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
        padding: 10px;
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
    
    .team-member {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 15px 0;
        padding: 15px;
        background-color: var(--primary-transparent);
        border-radius: var(--border-radius);
        transition: background-color 0.3s ease;
    }
    
    .team-member img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color);
        transition: border-color 0.3s ease;
    }
    
    .team-info h4 {
        color: var(--primary-color);
        margin-bottom: 5px;
        transition: color 0.3s ease;
    }
    
    .team-info p {
        font-size: 16px;
        color: var(--text-color);
        transition: color 0.3s ease;
    }

    a {
        text-decoration: none;
    }
    .logo-img{
        height: 80px;
        object-fit: cover;
        overflow: hidden;
    }
    .logo-img img{
        height: 100%;
    }
    .l-name{
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 15px;
    }
    .logo-name{
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 5px;
    }
    .l-desc{
        font-size: 10px;
    }
    .rating-field{
        display: flex;
        gap: 5px;
        justify-content: flex-start;
        background: var(--light-bg);
        padding: 8px;
        border-radius: 10px;
        align-items: center;
        height: 30px;
    }
    .str{
        color: var(--primary-color);
        display: flex;
        align-items: center;
        font-size: 18px;
    }
    .filter-list{
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .list{
        width: 50px;
        background: var(--light-bg);
        border-radius: 10px;
        text-align: center;
        padding: 5px;
    }
    .list.active{
        background: var(--primary-transparent)
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
    .feed-list{
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    .empty{
        display: flex;
        align-items: center;
        justify-content: center;
        height: 60vh;
        width: 100%;
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
                <div class="logo-img"><img src="./assets/images/logo.png" alt=""></div>
                <div class="logo-name">
                    <div class="l-name">
                        <?php
                            $result = $conn->query("SELECT * FROM feedback");
                            $count = $result->num_rows;
                            $sum_rating = 0;
                            $avg_rating = 0.0;
                            while($row = $result->fetch_assoc()){
                                $sum_rating += $row['rating'];
                            }
                                if($count != 0){
                                    $avg_rating = $sum_rating/$count; 
                                    $avg_rating = round($avg_rating, 1);
                                }
                        ?>
                        <div class="namefield">LiveStrike</div>
                        <div class="rating-field"><?php echo $avg_rating;?> <div class="str">★</div></div>
                    </div>
                    <div class="l-desc">All LiveStrike ratings and feedback are authentic user reviews.</div>
                </div>
            </div>
        </div>
        <div class="container2">
            <div class="filter-list">
                <div class="list active" data-rate="all">All</div>
                <div class="list" data-rate="5">5★</div>
                <div class="list" data-rate="4">4★</div>
                <div class="list" data-rate="3">3★</div>
                <div class="list" data-rate="2">2★</div>
                <div class="list" data-rate="1">1★</div>
            </div>
        </div>
        <div class="feed-list">
            <?php
                $html = "";
                $sql = "SELECT * FROM feedback " . ($type != 'all' ? "WHERE rating = $type " : "") . "ORDER BY `datetime` DESC";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
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
                if($result->num_rows < 1){
                    echo '<div class="empty">No Feedbacks</div>';
                }else{
                    echo $html;
                }
                
            ?>
        </div>
    </div>
    
    <script>
        // ✅ Go back button
function goBack() {
    window.history.back();
    // setActiveFromURL();
}

// ✅ Get all list items
const lists = document.querySelectorAll('.filter-list .list');

// ✅ Handle click on rating filters
lists.forEach(list => {
    list.addEventListener('click', () => {

        // Remove previous active
        lists.forEach(item => item.classList.remove('active'));

        // Set new active
        list.classList.add('active');

        // Read selected rating
        const value = list.dataset.rate;

        // Update URL without reload
        const url = new URL(window.location.href);
        url.searchParams.set("rating", value);
        history.replaceState({}, "", url.toString());

        // Update active state again (optional but safe)
        setActiveFromURL();
    });
});


// ✅ Set active rating based on URL parameter
function setActiveFromURL() {
    const url = new URL(window.location.href);
    const rating = url.searchParams.get("rating") || "all";

    const lists = document.querySelectorAll('.filter-list .list');

    lists.forEach(item => item.classList.remove('active'));

    const activeItem = document.querySelector(`.list[data-rate="${String(rating)}"]`);
    if (activeItem) {
        activeItem.classList.add('active');
        console.log("Active:", activeItem);

        fetch('./Backend/feedback_list.php',{
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                'rating' : rating
            })
        })
        .then(res => res.text())
        .then((data)=>{
            console.log(data);
            let container = document.querySelector('.feed-list');
            container.innerHTML = data;
        })
        .catch(err=>console.log(err))
    }
}


// ✅ Run when page fully loads
setActiveFromURL();


// ✅ Keep URL state when back button is used
window.addEventListener("popstate", () => {
    setActiveFromURL();
});

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