<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
include "../config.php";

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: manage_ads.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM ads WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$ad = $res->fetch_assoc();
if (!$ad) { header("Location: manage_ads.php"); exit; }

if (isset($_POST['update'])) {
    $slot = $conn->real_escape_string($_POST['slot'] ?? '');
    $page = $conn->real_escape_string($_POST['page'] ?? '');
    $city = $conn->real_escape_string($_POST['city'] ?? '');
    $url  = $conn->real_escape_string($_POST['url'] ?? '');
    $start = $_POST['start_date'] ?: null;
    $end   = $_POST['end_date'] ?: null;

    $imageSql = "";
    if (!empty($_FILES['image']['name'])) {
        $f = $_FILES['image'];
        $fname = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $f['name']);
        $dest = "../assets/ads/" . $fname;
        move_uploaded_file($f['tmp_name'], $dest);
        // delete old file
        @unlink("../assets/ads/" . $ad['image']);
        $imageSql = ", image = '".$conn->real_escape_string($fname)."'";
    }

    $sql = "UPDATE ads SET slot=?, page=?, city=?, url=?, start_date=?, end_date=? $imageSql WHERE id=?";
    // prepare and bind for consistent handling (image part handled above)
    $stmt2 = $conn->prepare("UPDATE ads SET slot=?, page=?, city=?, url=?, start_date=?, end_date=? WHERE id=?");
    $stmt2->bind_param("ssssssi", $slot, $page, $city, $url, $start, $end, $id);
    $stmt2->execute();

    header("Location: ./manage_ads.php?updated=1");
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Ad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media (max-width: 768px) {

    .navbar-brand {
        font-size: 1.5rem !important;
    }

    .navbar .btn {
        font-size: 1.15rem;
        padding: 10px 16px;
    }

    .row.g-2 .col-md-3,
    .row.g-2 .col {
        width: 100%;
    }

    .form-control {
        font-size: 1.2rem;
        height: 52px;
    }

    label {
        font-size: 1.1rem;
        font-weight: 500;
    }

    img {
        max-width: 160px !important;
        margin-bottom: 10px;
    }
}

/* ----------------------------------------------------
   ✅  Mobile 600px & Below
---------------------------------------------------- */
@media (max-width: 600px) {

    html {
        font-size: 19px;
    }

   .navbar {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .btn-row{
          display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    }

    .navbar-brand {
        font-size: 1rem !important;
        margin-right: 2px;
    }

    .navbar .btn {
        font-size: 0.7rem;
        width: max-content;
        margin: 4px 0;
        padding: 6px 10px;
    }

    /* Card form spacing */
    .card {
        padding: 1.6rem !important;
    }

    h5 {
        font-size: 1.3rem !important;
        margin-bottom: 1rem;
    }

    /* Make fields full width */
    .row.g-2 {
        flex-direction: column;
        margin-bottom: 1.8rem !important;
    }
    .mb-3 {
      margin-bottom: 2rem !important;
    }
    label {
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .form-control {
        height: 55px;
        font-size: 1rem;
    }

    img {
        max-width: 200px !important;
    }

    .btn-primary {
        width: 100%;
        font-size: 1rem;
        padding: 8px;
    }
}

  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand">Edit Ad</span>
    <div>
      <a class="btn btn-outline-light" href="./manage_ads.php">Back</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="card p-3">
    <h5>Edit Ad #<?= $ad['id'] ?></h5>

    <form method="post" enctype="multipart/form-data">
      <div class="row g-2 mb-2">
        <div class="col-md-3">
          <select name="slot" class="form-control">
            <option value="ad" <?= $ad['slot']=='ad'?'selected':'' ?>>(412 x 150) Scoreboard</option>
            <option value="ad2" <?= $ad['slot']=='ad2'?'selected':'' ?>>(412 x 80) Dashboard + Scoreboard</option>
            <option value="ad3_A" <?= $ad['slot']=='ad3_A'?'selected':'' ?>>Slot A (600 x 300) Scoreboard</option>
            <option value="ad3_B" <?= $ad['slot']=='ad3_B'?'selected':'' ?>>Slot B (600 x 300) Scoreboard</option>
            <option value="ad3_C" <?= $ad['slot']=='ad3_C'?'selected':'' ?>>Slot C (600 x 300) Scoreboard</option>
            <option value="ad3_D" <?= $ad['slot']=='ad3_D'?'selected':'' ?>>Slot D (600 x 300) Scoreboard</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="page" class="form-control">
            <option value="" <?= empty($ad['page']) ?'selected':'' ?>>All Pages</option>
            <option value="Dashboard" <?= $ad['page']=='Dashboard'?'selected':'' ?>>Dashboard</option>
            <option value="CRICKET_scoreboard" <?= $ad['page']=='CRICKET_scoreboard'?'selected':'' ?>>CRICKET_scoreboard</option>
            <option value="VOLLEYBALL_scoreboard" <?= $ad['page']=='VOLLEYBALL_scoreboard'?'selected':'' ?>>VOLLEYBALL_scoreboard</option>
            <option value="KABADDI_scoreboard" <?= $ad['page']=='KABADDI_scoreboard'?'selected':'' ?>>KABADDI_scoreboard</option>
            <option value="KHO-KHO_scoreboard" <?= $ad['page']=='KHO-KHO_scoreboard'?'selected':'' ?>>KHO-KHO_scoreboard</option>
            <option value="FOOTBALL_scoreboard" <?= $ad['page']=='FOOTBALL_scoreboard'?'selected':'' ?>>FOOTBALL_scoreboard</option>
            <option value="BADMINTON_scoreboard" <?= $ad['page']=='BADMINTON_scoreboard'?'selected':'' ?>>BADMINTON_scoreboard</option>
            <option value="TABLE-TENNIS_scoreboard" <?= $ad['page']=='TABLE-TENNIS_scoreboard'?'selected':'' ?>>TABLE-TENNIS_scoreboard</option>
            <option value="CHESS_scoreboard" <?= $ad['page']=='CHESS_scoreboard'?'selected':'' ?>>CHESS_scoreboard</option>
            <option value="BASKETBALL_scoreboard" <?= $ad['page']=='BASKETBALL_scoreboard'?'selected':'' ?>>BASKETBALL_scoreboard</option>
          </select>
        </div>
        <div class="col-md-3">
          <input name="city" value="<?= htmlspecialchars($ad['city']) ?>" class="form-control" placeholder="City (optional)">
        </div>
        <div class="col-md-3">
          <input name="url" value="<?= htmlspecialchars($ad['url']) ?>" class="form-control" placeholder="Click URL">
        </div>
      </div>

      <div class="row g-2 mb-2">
        <div class="col"><label>Start date</label><input type="date" name="start_date" value="<?= $ad['start_date'] ?>" class="form-control"></div>
        <div class="col"><label>End date</label><input type="date" name="end_date" value="<?= $ad['end_date'] ?>" class="form-control"></div>
        <div class="col"><label>Current Image</label><br><img src="../assets/ads/<?= htmlspecialchars($ad['image']) ?>" style="max-width:200px"></div>
      </div>

      <div class="mb-3">
        <label>Change Image (optional)</label>
        <input type="file" name="image" class="form-control">
      </div>

      <button name="update" class="btn btn-primary">Save</button>
    </form>
  </div>
</div>
</body>
</html>
