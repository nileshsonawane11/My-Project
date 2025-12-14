<?php
session_start();
if (isset($_SESSION['admin_user'])) {
    header("Location: ./dashboard.php");
    exit;
}
include "../config.php";

if (isset($_POST['back'])) {
    echo "<script>
            window.location.replace('./../landing-page.php');
          </script>";
    exit;
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $passwordRaw = $_POST['password'] ?? '';
    $password = md5($passwordRaw);

    if ($username === '' || $passwordRaw === '') {
        $error = "Enter username & password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            if ($password == $row['password']) {
                $_SESSION['admin_user'] = $row['username'];
                $_SESSION['admin_id'] = $row['id'];

                echo "<script>
                        localStorage.setItem('admin_user', '".htmlspecialchars($username)."');
                      </script>";

                header("Location: ./dashboard.php");
                exit;
            } else $error = "Invalid credentials.";
        } else $error = "Invalid credentials.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    #toggleText{
      position: absolute;
      right: 10px;
      top: 10px;
      cursor: pointer;
      font-size: 0.7rem;
      color: #007bff;
    }
    form{
      display: flex;
      flex-direction: column;
      gap: 15px;
      justify-content: center;
    }
    .bg-light{
      height: 100vh;
      width: 409px;
      margin: auto;
    }
    .card{
      width: 100%;
    }
    @media (max-width : 600px) {
      .bg-light{
        width: 90%;
      }
      .form-control{
        height: 50px;
      }
    }
  </style>
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh">
  <div class="card shadow-sm p-4">

    <h3 class="mb-3 text-center">Admin Login</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <form method="post">

      <input name="username"
             id="usernameInput"
             class="form-control"
             placeholder="Username"
             autocomplete="username">

      <div class="position-relative">
        <input id="passwordField"
               name="password"
               type="password"
               class="form-control"
               placeholder="Password"
               autocomplete="current-password">

        <small id="toggleText"
               onclick="togglePassword()">
            Show
        </small>
      </div>

      <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
      <button type="submit" name="back" class="btn btn-secondary w-100">Back To Dashboard</button>

    </form>
  </div>

<script>
window.onload = () => {
    let saved = localStorage.getItem('admin_user');
    if (saved) document.getElementById("usernameInput").value = saved;
};

function togglePassword() {
    const f = document.getElementById("passwordField");
    const t = document.getElementById("toggleText");
    if (f.type === "password") { f.type = "text"; t.textContent = "Hide"; }
    else { f.type = "password"; t.textContent = "Show"; }
}
</script>

</body>
</html>
