<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <title>Document</title>
</head>
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
            --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            --card-bg: #ffffff;
            --text-dark: #333333;
            --text-light: #666666;
            --border-color: #e0e0e0;
        }
        .nav-bar{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            /* box-shadow: 0px -5px 20px black; */
            top: 0;
            width: 100%;
            background-color: white;
            z-index: 999;
        }
        .nav-content{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding-left: 20px;
            padding-right: 20px;
        }
        .items,.list{
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 5px;
            }
        html, body {
            height: 100%;
            margin: 0;
        }
        .swiper {
            width: 100%;
            height: 100%;
        }
        .swiper-slide {
            box-sizing: border-box;
            width: 100%;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: column;
            background: #ffffff;
            gap: 20px;
        }
        .ad{
            height: 150px;
            width: 100%;
            position: relative;
        }
        .ad2{
            height: 60px;
            width: 100%;
        }
        .hide-ad{
            position: absolute;
            top: 15px;
            right: 15px;
            width: 25px;
            background: #d9d9d9;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
        }
        .match_score{
            height: max-content;
            width: 100%;
            background: white;
            /* padding: 15px; */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-line{
            width: 100%;
            height: 40px;
            background: #ffffff;
            box-shadow: 0 0px 2px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu {
            display: flex;
            position: relative;
            border-bottom: 1px solid #ddd;
        }

        .menu-items {
            cursor: pointer;
            height: 40px;
            width: 75px;
            align-items: center;
            justify-content: center;
            display: flex;
            transition: color 0.3s ease;
        }

        .menu-items.active {
        color: #ff6800;
        }

        .menu-line-indicator {
            position: absolute;
            bottom: 0;
            height: 2px;
            width: 75px;
            background: #ff6800;
            transition: left 0.3s ease;
            left: 0;
        }

        .menu{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-around;
            width: 100%;
        }
        .game-info {
            height: 150px;
            padding: 10px;
            display: flex;
            font-size: 14px;
            gap: 15px;
            width: 100%;
            max-width: 500px;
            background: var(--card-bg);
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            margin: 10px 0;
        }
        .match-data {
            display: flex;
            gap: 5px;
            flex-direction: column;
            align-items: flex-start;
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
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            overflow: hidden;
            object-fit: cover;
            align-items: center;
            justify-content: center;
        }
        .start-btn {
            width: 68px;
            height: 38px;
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
        table {
            width: 100%;
            border: solid 1px #f8f8f8;
            border-radius: 20px;
            font-size: 1.2rem;
            margin: 10px 0;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table .title {
            font-weight: 550;
            background: #f8f8f8;
            width: 110px;
            color: #626262;
        }

        table .data {
            text-align: left;
            color: #333;
        }
        .innings{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
            overflow: hidden;
            border-radius: 10px;
            border: 1px solid #bab8b8;
        }
        .inning-data{
            width: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 25px;
            padding: 15px;
            box-sizing: border-box;
        }
        .inning{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            border: 1px solid #627385;
            border-radius: 5px;
            overflow: hidden;
        }
        .inn-info{
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
            padding: 5px;
            height: -webkit-fill-available;
            justify-content: center;
            position: relative;
            font-size: 18px;
            font-weight: 600;
        }
        .inn-info label,
        .inn-no{
            color: #595959a6;
            font-size: 15px;
        }
        .stat{
            background: #d5d5d5;
            width: 100%;
            height: 30px;
            display: flex;
            align-items: center;
            padding: 0px 20px;
            box-sizing: border-box;
        }
        .skew{
            position: absolute;
            height: 100%;
            width: 127%;
            transform: skew(-15deg, 0deg);
            transform-origin: top left;
            background: #e9f3ff73;
            z-index: -1;
        }
        .weather{
            width: 100%;
            border: 1px solid black;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 15px;
            overflow: hidden;
        }
        .weather-info{
            height: 100%;
            padding: 10px;
            display: flex;
            justify-items: center;
            flex-direction: column;
            justify-content: center;
        }
        .weather-info label{
            font-size: 15px;
            text-align: center;
            color: #dddddd
        }
        .weather-data{
            display: flex;
            flex-direction: row;
            align-items: center;
            height: 80px;
            padding: 5px;
            background: linear-gradient(180deg, #11569d, #447ab0);
            color: white;
        }
        .weather-title {
            background: #eef7ff;
            height: 25px;
        }

        @media(max-width: 600px) {
            .nav-content{
                display: flex;
                justify-content:space-between ;
                align-items: center;
                width: 100%;
            }
            .logo-img img{
                height: 35px;
                padding: 9px 0;
            }
            .logo-name {
                font-size: 25px;
                color: black;
                text-align: left;
                width: 130px;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0rem;
                text-align: left;
            }
            .txt-strike{
                font-weight: 200;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .swiper-slide{
                width: 382px;
            }
        }

        @media(min-width: 601px) {
            .logo-img img{
                height: 35px;
                padding: 9px 0;
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
                text-align: left;
                width: 130px;
                font-weight: 400;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 0;
            }
            .swiper-slide{
                width: 382px;
            }
        }
</style>
<body>

    <nav class="nav-bar">
        <div class="nav-content">
            <div class="items">
                <div class="logo-img"><img src="https://i.ibb.co/gLY2MgSd/logo.png" alt=""></div>
                <div class="logo-name"><p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p></div>
            </div>
            <div class="items">
                <a href="" class="menu-bar"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAGZJREFUSEvtlrENACAMw8pnnMZpfAYTC1W3CDOEA2JhUpUW0GkQNwx+Zt6qj+ohdp7yKtVLDE6c78DiC+c4t/o46WLX8877rlzYOGGqxU/scYryB4KVCwNja9GtlhvwWpQrrQIx1Rt3TwofeC3yFwAAAABJRU5ErkJggg=="/></a>
            </div>
        </div>
    </nav>    

    <div class="ad">
        <div class="hide-ad">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"><path d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z" fill="black" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        </div>
    </div>

    <div class="match_score">
        <div class="game-info" data-match_id="MATCH_ID_HERE">
            <div class="match-data">
                <div class="info">
                    <p>Match 1 | No Tournament</p>
                </div>

                <div class="info team-score">
                    <div class="team">
                        <img src="./assets/images/teams/team1_logo.png" alt="Team 1 Name" onerror="this.style.opacity='0'">
                        Team 1 Name
                    </div>
                    <div class="score">100/2 (10.0)</div>
                </div>

                <div class="info team-score">
                    <div class="team">
                        <img src="./assets/images/teams/team2_logo.png" alt="Team 2 Name" onerror="this.style.opacity='0'">
                        Team 2 Name
                    </div>
                    <div class="score">98/3 (9.2)</div>
                </div>

                <div class="info">
                    <p>14 Jul 2025 | 3:00 PM</p>
                </div>
                <div class="info">
                    <p>CRR : 123</p>
                </div>
                <!-- OR if toss declared -->
                <!--
                <div class="info update">
                    <p>Team 1 Name Elected To Bat</p>
                </div>
                -->
            </div>

            <div class="strt-btn">
                <div class="info">
                    <button class="start-btn" onclick="openDialog(this)">Start</button>
                </div>
            </div>
        </div>
    </div>

    <div class="menu-line">
        <div class="menu">
            <div class="menu-items active">Info</div>
            <div class="menu-items">Fantasy</div>
            <div class="menu-items">Live</div>
            <div class="menu-items">Scoreboard</div>
            <div class="menu-items">Squad</div>
            <div class="menu-line-indicator"></div>
        </div>
    </div>

    <div class="ad2">
    </div>

    <div class="swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <table>
                    <tbody>
                        <tr>
                            <td class="title">Date & Time</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Venue</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Match</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Umpires</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Scorers</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Referee</td>
                            <td class="data"></td>
                        </tr>
                        <tr>
                            <td class="title">Toss</td>
                            <td class="data"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="innings">
                    <div class="stat">VENUE STATS</div>
                    <div class="inning-data">
                        <div class="inning">
                            <div class="inn-no inn-info"><div class="skew"></div> 1st Inning</div>
                            <div class="runs inn-info">453 <label for="">Avg Runs</label></div>
                            <div class="wicket inn-info">9 <label for="">Avg Wickets</label></div>
                            <div class="SR inn-info">55.00 <label for="">Avg SR</label></div>
                        </div>
                        <div class="inning">
                            <div class="inn-no inn-info"><div class="skew"></div> 2nd Inning</div>
                            <div class="runs inn-info">453 <label for="">Avg Runs</label></div>
                            <div class="wicket inn-info">9 <label for="">Avg Wickets</label></div>
                            <div class="SR inn-info">55.00 <label for="">Avg SR</label></div>
                        </div>
                    </div>
                </div>
                <?php
                    $apiKey = "76604801ccb3576d81ddd1bca09b978a";
$location = "Nashik";
$geo_url = "http://api.openweathermap.org/geo/1.0/direct?q=".urlencode($location)."&limit=1&appid={$apiKey}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $geo_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$geo_response = curl_exec($ch);
curl_close($ch);

$geo_data = json_decode($geo_response, true);

if (!empty($geo_data)) {
    $lat = $geo_data[0]['lat'];
    $lon = $geo_data[0]['lon'];

    // Now fetch weather using lat & lon
    $weather_url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weather_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $weather_response = curl_exec($ch);
    curl_close($ch);

    // echo $weather_response;

    $weather_data = json_decode($weather_response, true);

    if ($weather_data['cod'] == 200) {
        echo "Temperature at {$location}: " . $weather_data['main']['temp'] . "°C";
    } else {
        echo "Weather data unavailable. " . $weather_data['message'];
    }
} else {
    echo "Could not find location.";
}

                ?>

                <div class="weather">
                    <div class="weather-info weather-title">Weather Update</div>
                    <div class="weather-data">
                        <div class="weather-info weather-dt">Harare,Zimbabwe <label for="">Updated: 16 Jul 00:00</label></div>
                        <div class="weather-info weather-dt">15.8°C .<label for="">Clouds</label></div>
                        <div class="weather-info weather-dt">70% <label for="">Clouds</label></div>
                        <div class="weather-info weather-dt">3.8 kmph <label for="">Wind</label></div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">Page 2</div>

            <div class="swiper-slide">Page 3</div>

            <div class="swiper-slide">Page 4</div>

            <div class="swiper-slide">Page 5</div>

        </div>
    </div>

    <script>
    const menuItems = document.querySelectorAll('.menu-items');
    const indicator = document.querySelector('.menu-line-indicator');

    const swiper = new Swiper(".swiper", {
        speed: 300,
        slidesPerView: 1,
        on: {
        slideChange: () => {
            // Update active tab based on swiper index
            menuItems.forEach(i => i.classList.remove('active'));
            menuItems[swiper.activeIndex].classList.add('active');
            moveIndicator(swiper.activeIndex);
        }
        }
    });

    menuItems.forEach((item, index) => {
        item.addEventListener('click', () => {
        swiper.slideTo(index);
        });
    });

    function moveIndicator(index) {
        const targetItem = menuItems[index];
        indicator.style.left = targetItem.offsetLeft + 'px';
    }

    // Set initial position
    window.addEventListener('load', () => {
        moveIndicator(0);
    });


    </script>
</body>
</html>