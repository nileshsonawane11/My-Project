<?php
    session_start();
    if(isset($_COOKIE['user'])){
      $_SESSION['user']=$_COOKIE['user'];
    }
    $isloggedin = isset($_COOKIE['user']);

    $current_time = date('Y-m-d H:i:s');

    // Prepare and execute delete query
    $query = "DELETE FROM users WHERE delete_on IS NOT NULL AND TIMESTAMPDIFF(HOUR, delete_on, ?) >= 24";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $current_time);

    if ($stmt->execute()) {
        echo (['status' => 'success', 'message' => 'Accounts older than 24 hours deleted']);
    } else {
        echo (['status' => 'error', 'message' => 'Deletion failed: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <title>LiveStrike</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            user-select: none;
          }

          :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(0deg, var(--primary-light), var(--primary-dark));
          }
      
          body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 97vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            user-select: none;
          }
      
          .container {
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
          }
      
          .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: -5px;
          }
      
          .logo {
            width: 150px;
            position: relative;
            z-index: -1;
            filter: drop-shadow(5px 8px 4px rgba(0, 0, 0, 0.2));
            animation: logo-animation 2s forwards;
            transition: transform 0.3s ease;
            cursor: pointer;
          }
      
          .logo:hover {
            transform: scale(1.1) rotate(5deg);
          }
      
          .logo-name {
            position: relative;
            top: -30px;
            left: 0;
            font-size: 40px;
            color: black;
            text-align: center;
            width: 0;
            font-weight: 400;
            overflow: hidden;
            white-space: nowrap;
            animation: typing 1s ease-in-out forwards 2.3s;
          }
      
          .mask {
            width: 100%;
            height: 150px;
            position: absolute;
            bottom: 0;
            z-index: 1;
            background-color: #f0f0f0;
          }
      
        
            @keyframes logo-animation {
            0% {
              transform: translateY(500px) scale(0);
            }
            25% {
              transform: translateY(-140px) scale(1.2);
            }
            50% {
              transform: translateY(-20px) scale(1);
            }
            75% {
              transform: translateY(-45px) scale(1.1);
            }
            100% {
              transform: translateY(-20px) scale(0.8);
            }
          }
      
          @keyframes typing {
            from { width: 0 }
            to { width: 200px } /* Adjust to fit the full text */
          }
          
          .txt-strike{
            font-weight: 200;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            background-clip: text;
            -webkit-text-fill-color: transparent;
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
            border-top: 4px solid #ddd;
            border-top: 3px solid #EECF4F;
            border-radius: 50%;
            animation: spin 1s linear infinite;
          }
      
          @keyframes spin {
            to {
              transform: rotate(360deg);
            }
          }
    </style>
</head>
<body>
  
    <div id="loader">
      <div class="spinner"></div>
    </div>

    <div class="container">
        <div class="logo-container">
            <a href="https://ibb.co/Zp9j6TBS"><img class="logo" src="https://i.ibb.co/gLY2MgSd/logo.png" alt="logo" border="0"></a>         
            <p class="logo-name"><span class="txt-live"><b>Live</b></span><span class="txt-strike">Strike</span></p>   
        </div>
        <div class="mask">

        </div>
    </div>
    
</body>
<script>
  
 setTimeout(() => {
    let isloggedin = <?php echo json_encode($isloggedin);?>;
    console.log(isloggedin)
    document.getElementById('loader').style.display = 'flex';
    if(!isloggedin){
      let iframe = document.createElement('iframe');
      iframe.style.display = 'none';
      iframe.src = './landing-page.php';

      iframe.onload = function () {

        window.location.href = './landing-page.php';
      };

       document.body.appendChild(iframe);
    }else{
      let iframe = document.createElement('iframe');
      iframe.style.display = 'none';
      iframe.src = './dashboard.php?update="live"&sport="CRICKET"';

      iframe.onload = function () {

        window.location.href = './dashboard.php?update="live"&sport="CRICKET"';
      };

       document.body.appendChild(iframe);
    }

}, 6000);
</script>
</html>