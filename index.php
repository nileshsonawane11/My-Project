<?php
    session_start();
    include './config.php';
    if(isset($_COOKIE['user'])){
      $_SESSION['user']=$_COOKIE['user'];
    }
    $isloggedin = isset($_COOKIE['user']);

    $current_time = date('Y-m-d H:i:s');

    // Delete accounts older than 24 hours after deletion request
    $query = "DELETE FROM users WHERE delete_on IS NOT NULL AND TIMESTAMPDIFF(HOUR, delete_on, ?) >= 24";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $current_time);
    $stmt->execute();
    $stmt->close();
    $conn->close();
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
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
  height: 100vh;
  background-color: #fff;
  font-family: Arial, sans-serif;
  overflow: hidden;
}

.container {
  width: 100%;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.logo-container {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.logo1, .logo2 {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  will-change: transform, opacity;
  height: auto;
}

.logo1 {
  z-index: 1;
  animation: logo-right 0.3s ease-out 0.6s forwards;
  height: 75px;
  transform: translate(200%, -91%);
  opacity: 0;
}

.logo2 {
  z-index: 0;
  height: 170px;
  transform: translate(-200%, -40%);
  opacity: 0;
  animation: logo-left 0.3s ease-out 0.6s forwards;
}

@keyframes logo-left {
  from {
    transform: translate(-200%, -40%);
    opacity: 0;
  }
  to {
    transform: translate(-50%, -40%);
    opacity: 1;
  }
}

@keyframes logo-right {
  from {
    transform: translate(200%, -91%);
    opacity: 0;
  }
  to {
    transform: translate(-62%, -91%);
    opacity: 1;
  }
}

/* Loader */
#loader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: var(--card-bg);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--gray-bg);
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media(max-width: 600px) {
  .logo1 { height: 65px; }
  .logo2 { height: 150px; }
}
</style>
</head>
<body>
  <div id="loader">
    <div class="spinner"></div>
  </div>

  <div class="container">
    <div class="logo-container">
      <img class="logo1" src="./assets/images/logo_part1.png" alt="logo" border="0">         
      <img class="logo2" src="./assets/images/logo_part2.png" alt="logo" border="0">   
    </div>
  </div>

<script>
/* ===============================
   ✅ PWA Detection & Redirect Fix
   =============================== */
window.addEventListener('load', async () => {
  // If already in PWA mode, do nothing
  if (window.matchMedia('(display-mode: standalone)').matches) {
    console.log("Running inside PWA — no redirect needed.");
    return;
  }

  // If previously installed PWA, redirect immediately from index
  if (localStorage.getItem('livestrike_pwa_installed') === 'true') {
    console.log("Detected LiveStrike PWA installation — redirecting to app...");
    window.location.href = '/'; // Redirect to your PWA start URL
    return;
  }

  // Listen for installation event
  window.addEventListener('appinstalled', () => {
    localStorage.setItem('livestrike_pwa_installed', 'true');
  });
});

/* ===============================
   Existing Logo Animation + Redirect Logic
   =============================== */
setTimeout(() => {
  let isloggedin = <?php echo json_encode($isloggedin); ?>;
  console.log("User logged in:", isloggedin);

  document.getElementById('loader').style.display = 'flex';

  if (!isloggedin) {
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = './landing-page.php';
    iframe.onload = () => window.location.href = './landing-page.php';
    document.body.appendChild(iframe);
  } else {
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = './dashboard.php?update="live"&sport="CRICKET"';
    iframe.onload = () => window.location.href = './dashboard.php?update="live"&sport="CRICKET"';
    document.body.appendChild(iframe);
  }
}, 4000);

// Disable right-click and dev tools shortcuts
document.addEventListener('contextmenu', e => e.preventDefault());
document.onkeydown = function(e) {
  if(e.keyCode == 123) return false;
  if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
  if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
  if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
}
</script>
</body>
</html>
