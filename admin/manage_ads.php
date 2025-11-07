<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: ./login.php");
    exit;
}

include "../config.php";

function getSlotSize($slot) {
    $map = [
        "ad"   => "412 × 150",
        "ad2"  => "412 × 60",
        "ad3_A" => "600 × 300",
        "ad3_B" => "600 × 300",
        "ad3_C" => "600 × 300",
        "ad3_D" => "600 × 300"
    ];

    return $map[$slot] ?? "Unknown Size";
}

// handle add
if (isset($_POST['add'])) {
    $slot = $_POST['slot'] ?? '';
    $page = $conn->real_escape_string($_POST['page'] ?? '');
    $city = $conn->real_escape_string($_POST['city'] ?? '');
    $url  = $conn->real_escape_string($_POST['url'] ?? '');
    $partner  = $conn->real_escape_string($_POST['partner'] ?? '');
    $start = $_POST['start_date'] ?: null;
    $end   = $_POST['end_date'] ?: null;

    if (!empty($_FILES['image']['name'])) {
        $f = $_FILES['image'];
        $fname = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $f['name']);
        $dest = "../assets/ads/" . $fname;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
        move_uploaded_file($f['tmp_name'], $dest);

        $stmt = $conn->prepare("INSERT INTO ads (slot, partner, page,city,image,url,start_date,end_date,active) VALUES (?,?,?,?,?,?,?,?,1)");
        $stmt->bind_param("ssssssss", $slot, $partner, $page, $city, $fname, $url, $start, $end);
        $stmt->execute();
        $added = true;
    } else {
        $error = "Please choose an image.";
    }
}

// handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // optionally remove file
    $row = $conn->query("SELECT image FROM ads WHERE id=$id")->fetch_assoc();
    if ($row && !empty($row['image'])) {
        @unlink("../assets/ads/" . $row['image']);
    }
    $conn->query("DELETE FROM ads WHERE id=$id");
    $conn->query("DELETE FROM ad_analytics WHERE ad_id=$id");
    header("Location: manage_ads.php");
    exit;
}

// filters
$filter_slot = $conn->real_escape_string($_GET['slot'] ?? '');
$filter_page = $conn->real_escape_string($_GET['page'] ?? '');
$filter_city = $conn->real_escape_string($_GET['city'] ?? '');
$filter_partner = $conn->real_escape_string($_GET['partner'] ?? '');

$where = "1=1";
if ($filter_slot) $where .= " AND slot='{$filter_slot}'";
if ($filter_page) $where .= " AND page='{$filter_page}'";
if ($filter_city) $where .= " AND city='{$filter_city}'";
if ($filter_partner) $where .= " AND partner='{$filter_partner}'";

$q = "SELECT * FROM ads WHERE $where ORDER BY id DESC";
$res = $conn->query($q);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Manage Ads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* ----------------------------------------------------
   ✅  Tablet & Below  (max-width: 768px)
---------------------------------------------------- */
@media (max-width: 768px) {

    .navbar-brand {
        font-size: 1.5rem !important;
    }

    .navbar .btn {
        margin-top: 5px;
        padding: 10px 16px;
        font-size: 1.1rem;
    }

    .row.g-2 > .col-md-3,
    .row.g-2 > .col-md-3,
    .row.g-2 > .col-md-3 {
        width: 100%;
    }

    .form-control {
        font-size: 1.2rem;
        height: 52px;
    }

    .table img {
        max-width: 120px;
    }

    table.table th,
    table.table td {
        font-size: 1rem;
    }
}

/* ----------------------------------------------------
   ✅  Mobile Boost (max-width: 600px)
---------------------------------------------------- */
@media (max-width: 600px) {

    html {
        font-size: 19px;
    }

    .navbar {
        flex-direction: column;
        padding: 1rem;
        gap: 10px;
    }

    .navbar .btn {
        width: 100%;
        font-size: 1.25rem;
        padding: 12px;
    }

    .card {
        padding: 1.4rem !important;
    }

    h5 {
        font-size: 1.6rem !important;
        margin-bottom: 1rem;
    }

    .row.g-2 {
        flex-direction: column;
        margin-bottom: 1.8rem !important;
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
    }

    .navbar .btn {
        font-size: 0.7rem;
        width: max-content;
        margin: 4px 0;
        padding: 6px 10px;
    }


    .form-control {
        height: 55px;
        font-size: 1rem;
    }

    /* Table readable in mobile */
    .table-responsive {
        overflow-x: auto;
    }

    .table th,
    .table td {
        font-size: 1.15rem;
        padding: 12px 8px;
    }

    .table img {
        max-width: 160px;
    }

    .btn-sm {
        padding: 7px;
        font-size: 0.8rem;
        margin-top: 10px;
        width: 100%;
    }
}

/* ----------------------------------------------------
   ✅  Very Small Screens (max-width: 380px)
---------------------------------------------------- */

  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand">Manage Ads</span>
    <div>
      <a class="btn btn-outline-light me-2" href="#" onclick="safeBack('./dashboard.php'); return false;">Dashboard</a>
      <a class="btn btn-outline-light me-2" href="export_csv.php">Export CSV</a>
      <a class="btn btn-outline-light" href="#" onclick='logout()';>Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="card p-3 mb-4">
    <h5>Add New Ad</h5>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
    <?php if (!empty($added)): ?><div class="alert alert-success">Ad added.</div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="row g-2 mb-2">
        <div class="col-md-3">
          <select name="slot" class="form-control" required>
            <option value="ad">(412 x 150) Scoreboard</option>
            <option value="ad2">(412 x 80) Dashboard + Scoreboard</option>
            <option value="ad3_A">Slot A (600 x 300) Scoreboard</option>
            <option value="ad3_B">Slot B (600 x 300) Scoreboard</option>
            <option value="ad3_C">Slot C (600 x 300) Scoreboard</option>
            <option value="ad3_D">Slot D (600 x 300) Scoreboard</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="page" class="form-control">
            <option value="">Page (Optional)</option>
            <option value="Dashboard">Dashboard</option>
            <option value="CRICKET_scoreboard">CRICKET_scoreboard</option>
            <option value="VOLLEYBALL_scoreboard">VOLLEYBALL_scoreboard</option>
            <option value="KABADDI_scoreboard">KABADDI_scoreboard</option>
            <option value="KHO-KHO_scoreboard">KHO-KHO_scoreboard</option>
            <option value="FOOTBALL_scoreboard">FOOTBALL_scoreboard</option>
            <option value="BADMINTON_scoreboard">BADMINTON_scoreboard</option>
            <option value="TABLE-TENNIS_scoreboard">TABLE-TENNIS_scoreboard</option>
            <option value="CHESS_scoreboard">CHESS_scoreboard</option>
            <option value="BASKETBALL_scoreboard">BASKETBALL_scoreboard</option>
          </select>
        </div>
        <div class="col-md-3">
          <input name="city" class="form-control" placeholder="City (optional)">
        </div>
         <div class="col-md-3">
          <input name="partner" class="form-control" placeholder="Partner Name (mandatory)" required>
        </div>
        <div class="col-md-3">
          <input name="url" class="form-control" placeholder="Click URL (optional)">
        </div>
      </div>

      <div class="row g-2 mb-2">
        <div class="col">
          <label>Start date</label>
          <input type="date" name="start_date" class="form-control">
        </div>
        <div class="col">
          <label>End date</label>
          <input type="date" name="end_date" class="form-control">
        </div>
        <div class="col">
          <label>Image (required)</label>
          <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>
      </div>

      <button name="add" class="btn btn-primary">Add Ad</button>
    </form>
  </div>

  <div class="card p-3">
    <h5>All Ads</h5>

    <form class="row g-2 mb-3" method="GET">

    <!-- SLOT -->
    <div class="col-md-3">
        <input list="slotList" name="slot" value="<?= htmlspecialchars($filter_slot) ?>"
               class="form-control" placeholder="Filter by Slot">
        <datalist id="slotList">
            <?php
            $rs = $conn->query("SELECT DISTINCT slot FROM ads ORDER BY slot ASC");
            while ($s = $rs->fetch_assoc()) {
                echo "<option value='".$s['slot']."'>";
            }
            ?>
        </datalist>
    </div>

    <!-- PAGE -->
    <div class="col-md-3">
        <input list="pageList" name="page" value="<?= htmlspecialchars($filter_page) ?>"
               class="form-control" placeholder="Filter by Page">
        <datalist id="pageList">
            <?php
            $rs = $conn->query("SELECT DISTINCT page FROM ads WHERE page!='' ORDER BY page ASC");
            while ($p = $rs->fetch_assoc()) {
                echo "<option value='".$p['page']."'>";
            }
            ?>
        </datalist>
    </div>

    <!-- CITY -->
    <div class="col-md-3">
        <input list="cityList" name="city" value="<?= htmlspecialchars($filter_city) ?>"
               class="form-control" placeholder="Filter by City">
        <datalist id="cityList">
            <?php
            $rs = $conn->query("SELECT DISTINCT city FROM ads WHERE city!='' ORDER BY city ASC");
            while ($c = $rs->fetch_assoc()) {
                echo "<option value='".$c['city']."'>";
            }
            ?>
        </datalist>
    </div>

    <!-- PARTNER -->
    <div class="col-md-3">
        <input list="partnerList" name="partner" value="<?= htmlspecialchars($filter_partner) ?>"
               class="form-control" placeholder="Filter by Partner">
        <datalist id="partnerList">
            <?php
            $rs = $conn->query("SELECT DISTINCT partner FROM ads WHERE partner!='' ORDER BY partner ASC");
            while ($p = $rs->fetch_assoc()) {
                echo "<option value='".$p['partner']."'>";
            }
            ?>
        </datalist>
    </div>

    <div class="col-md-12">
        <button class="btn btn-secondary">Apply</button>
        <a class="btn btn-outline-secondary" href="manage_ads.php">Clear</a>
    </div>
</form>


    <div class="table-responsive">
    <table class="table table-bordered table-hover mt-3">
      <thead class="table-dark"><tr><th>ID</th><th></th><th>Partner</th><th>Slot</th><th>Preview</th><th>Page</th><th>City</th><th>Start</th><th>End</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while($row = $res->fetch_assoc()): ?>
        <tr onclick="goTo('<?= $row['id'] ?>','<?= $row['partner'] ?>')" style="cursor:pointer;">
          <td><?= $row['id'] ?></td>
          <td><a href="./partner_analytics.php?partner=<?= htmlspecialchars($row['partner']) ?>">Partners' Ads</a></td>
          <td><?= htmlspecialchars($row['partner']) ?></td>
          <td><?= getSlotSize($row['slot']) ?></td>
          <td><img src="../assets/ads/<?= htmlspecialchars($row['image']) ?>" style="max-width:140px"></td>
          <td><?= $row['page'] ? htmlspecialchars($row['page']) : 'All Pages' ?></td>
          <td><?= $row['city'] ? htmlspecialchars($row['city']) : 'All Cities'  ?></td>
          <td><?= $row['start_date'] ?? '-' ?></td>
          <td><?= $row['end_date'] ?? '-' ?></td>
          <td>
            <a class="btn btn-sm btn-warning" href="edit_ad.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
<script>
  function logout(){
            fetch('./logout.php')
            .then(response => response.json())
            .then(data => {
                if(data.status === 200){
                    window.location.href = './login.php';
                    alert ('You have been logged out!')
                }
                console.log(data)})
            .catch(error => console.error(error));
        }

  function goTo(adId,partner){
      window.location.href = `./partner_analytics.php?partner=${partner}&ad_id=${adId}`;
  }

    function safeBack(fallback) {
      if (window.history.length > 1) {
          history.back();
      } else {
          window.location.href = fallback;
      }
  }
</script>
</body>
</html>
