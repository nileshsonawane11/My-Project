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
        .nav-content{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding-left: 20px;
            padding-right: 50px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #fff;
            background: #333;
        }

        @media(max-width: 600px) {
            .nav-content{
                padding-right: 20px;
                display: flex;
                justify-content:space-between ;
                align-items: center;
                width: 100%;
            }
            .logo-img img{
                height: 40px;
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
            .txt-strike{
                font-weight: 200;
                background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        }

        @media(min-width: 601px) {
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

    <div class="swiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide">Page 1</div>
        <div class="swiper-slide">Page 2</div>
        <div class="swiper-slide">Page 3</div>
    </div>
    </div>

    <script>
    const swiper = new Swiper('.swiper', {
        direction: 'horizontal',
        loop: false,
        pagination: {
        el: '.swiper-pagination',
        },
    });
    </script>
</body>
</html>