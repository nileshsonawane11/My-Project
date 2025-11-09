<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}

include "../config.php";

/* -----------------------------------------------------------
   FETCH DYNAMIC FILTER DATA
----------------------------------------------------------- */

// Cities
$cities = [];
$resCity = $conn->query("SELECT DISTINCT city FROM ads WHERE city IS NOT NULL AND city!=''");
while ($r = $resCity->fetch_assoc()) $cities[] = $r['city'];

// Partners
$partners = [];
$resPartner = $conn->query("SELECT DISTINCT partner FROM ads WHERE partner IS NOT NULL AND partner!=''");
while ($r = $resPartner->fetch_assoc()) $partners[] = $r['partner'];

// Ad IDs
$adIds = [];
$resAd = $conn->query("SELECT id FROM ads ORDER BY id DESC");
while ($r = $resAd->fetch_assoc()) $adIds[] = $r['id'];

/* -----------------------------------------------------------
   GET FILTERS
----------------------------------------------------------- */
$filter_slot    = $_GET['slot'] ?? "";
$filter_page    = $_GET['page'] ?? "";
$filter_city    = $_GET['city'] ?? "";
$filter_partner = $_GET['partner'] ?? "";
$filter_ad_id   = $_GET['ad_id'] ?? "";

/* -----------------------------------------------------------
   BUILD WHERE CLAUSE
----------------------------------------------------------- */
$where = "1=1";
$params = [];
$types  = "";

if ($filter_slot != "") {
    $where .= " AND ads.slot = ?";
    $params[] = $filter_slot;
    $types .= "s";
}

if ($filter_page != "") {
    $where .= " AND ads.page = ?";
    $params[] = $filter_page;
    $types .= "s";
}

if ($filter_city != "") {
    $where .= " AND ads.city = ?";
    $params[] = $filter_city;
    $types .= "s";
}

if ($filter_partner != "") {
    $where .= " AND ads.partner = ?";
    $params[] = $filter_partner;
    $types .= "s";
}

if ($filter_ad_id != "") {
    $where .= " AND ads.id = ?";
    $params[] = $filter_ad_id;
    $types .= "i";
}

/* -----------------------------------------------------------
   TOTAL VIEWS
----------------------------------------------------------- */
$sqlV = "
    SELECT COUNT(*) AS v
    FROM ad_analytics 
    INNER JOIN ads ON ad_analytics.ad_id = ads.id
    WHERE ad_analytics.event_type='impression' AND $where
";

$stmtV = $conn->prepare($sqlV);
if ($types != "") $stmtV->bind_param($types, ...$params);
$stmtV->execute();
$totalViews = $stmtV->get_result()->fetch_assoc()['v'] ?? 0;

/* -----------------------------------------------------------
   TOTAL CLICKS
----------------------------------------------------------- */
$sqlC = "
    SELECT COUNT(*) AS c
    FROM ad_analytics 
    INNER JOIN ads ON ad_analytics.ad_id = ads.id
    WHERE ad_analytics.event_type='click' AND $where
";

$stmtC = $conn->prepare($sqlC);
if ($types != "") $stmtC->bind_param($types, ...$params);
$stmtC->execute();
$totalClicks = $stmtC->get_result()->fetch_assoc()['c'] ?? 0;

$ctr = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;

/* -----------------------------------------------------------
   CHART DATA (Clicks + Impressions per ad)
----------------------------------------------------------- */
$sqlChart = "
    SELECT 
        ads.id AS ad_id, 
        ads.slot,
        ads.partner,
        COALESCE(SUM(CASE WHEN ad_analytics.event_type='click' THEN 1 ELSE 0 END),0) AS clicks,
        COALESCE(SUM(CASE WHEN ad_analytics.event_type='impression' THEN 1 ELSE 0 END),0) AS views
    FROM ads
    LEFT JOIN ad_analytics ON ads.id = ad_analytics.ad_id
    WHERE ads.active = 1 AND $where
    GROUP BY ads.id
    ORDER BY clicks DESC
    LIMIT 20
";

$stmtChart = $conn->prepare($sqlChart);
if ($types != "") $stmtChart->bind_param($types, ...$params);
$stmtChart->execute();
$resChart = $stmtChart->get_result();

$labels = [];
$clickValues = [];
$viewValues  = [];

while ($r = $resChart->fetch_assoc()) {
    $labels[]      = 'ID '.$r['ad_id'] .' - ('.$r['partner'].')';
    $clickValues[] = (int)$r['clicks'];
    $viewValues[]  = (int)$r['views'];
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Ad Analytics - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
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
    #clickChart{ height:230px!important; }
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
  </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">LiveStrike Ads</span>
    <div class="btn-row">
      <a class="btn btn-outline-light me-2" href="manage_ads.php">Manage Ads</a>
      <a class="btn btn-success me-2" href="export_csv.php">Download CSV</a>
      <a class="btn btn-outline-light" href="#" onclick='logout()';>Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

<!-- FILTER PANEL -->
<div class="card p-3 mb-4">
  <form method="GET" class="row g-2">

    <div class="col-md-2">
      <select name="slot" class="form-control">
        <option value="">All Slots</option>
        <option value="ad"     <?= $filter_slot=="ad" ? "selected":"" ?>>412x150</option>
        <option value="ad2"    <?= $filter_slot=="ad2" ? "selected":"" ?>>412x60</option>
        <option value="ad3_A"  <?= $filter_slot=="ad3_A" ? "selected":"" ?>>Slot A</option>
        <option value="ad3_B"  <?= $filter_slot=="ad3_B" ? "selected":"" ?>>Slot B</option>
        <option value="ad3_C"  <?= $filter_slot=="ad3_C" ? "selected":"" ?>>Slot C</option>
        <option value="ad3_D"  <?= $filter_slot=="ad3_D" ? "selected":"" ?>>Slot D</option>
      </select>
    </div>

    <div class="col-md-2">
      <select name="page" class="form-control">
        <option value="">All Pages</option>
        <?php
            $pages = [
                "CRICKET_scoreboard","VOLLEYBALL_scoreboard","KABADDI_scoreboard",
                "KHO-KHO_scoreboard","FOOTBALL_scoreboard","BADMINTON_scoreboard",
                "TABLE-TENNIS_scoreboard","CHESS_scoreboard","BASKETBALL_scoreboard"
            ];
            foreach ($pages as $p)
                echo "<option value='$p' ".($filter_page==$p?'selected':'').">$p</option>";
        ?>
      </select>
    </div>

    <div class="col-md-2">
      <select name="city" class="form-control">
        <option value="">All Cities</option>
        <?php foreach ($cities as $c): ?>
            <option value="<?=$c?>" <?= $filter_city==$c?"selected":"" ?>><?=$c?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <select name="partner" class="form-control">
        <option value="">All Partners</option>
        <?php foreach ($partners as $p): ?>
            <option value="<?=$p?>" <?= $filter_partner==$p?"selected":"" ?>><?=$p?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <select name="ad_id" class="form-control">
        <option value="">All Ads</option>
        <?php foreach ($adIds as $id): ?>
            <option value="<?=$id?>" <?= $filter_ad_id==$id?"selected":"" ?>>Ad #<?=$id?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <button class="btn btn-primary w-100">Apply</button>
    </div>

    <div class="col-md-2">
      <a class="btn btn-secondary" href="dashboard.php">Clear</a>
    </div>

  </form>
</div>

<!-- TOTAL CARDS -->
<div class="row g-3">
  <div class="col-md-4"><div class="card p-3"><h6>Total Views</h6><div class="fs-3 text-primary"><?=$totalViews?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><h6>Total Clicks</h6><div class="fs-3 text-success"><?=$totalClicks?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><h6>CTR</h6><div class="fs-3 text-dark"><?=$ctr?>%</div></div></div>
</div>

<!-- CHART -->
<div class="card mt-4 p-3">
  <h5>Top Ads by Clicks</h5>
  <canvas id="clickChart" style="height:300px"></canvas>
</div>

</div>

<script>
const labels      = <?= json_encode($labels) ?>;
const clicks      = <?= json_encode($clickValues) ?>;
const impressions = <?= json_encode($viewValues) ?>;

// ✅ Create Chart
const ctx = document.getElementById('clickChart');
const clickChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,          // Example: ["Slot A (ID 37)", "Slot B (ID 40)", ...]
        datasets: [
            { label: 'Clicks',      data: clicks,      backgroundColor:'rgba(54,162,235,0.7)' },
            { label: 'Impressions', data: impressions, backgroundColor:'rgba(255,206,86,0.7)' }
        ]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } },
        onClick: (evt, activeEls) => {

            if (activeEls.length === 0) return;

            let chartIndex = activeEls[0].index;     // Which bar clicked  
            let label = labels[chartIndex];          // Example: "Slot A (ID 37)"

            // ✅ Extract ID from text (everything after last space)
            let match = label.match(/ID\s+(\d+)/);
            if (!match) return;

            let adId = match[1];

            // ✅ Build redirect URL
            let partner = "<?= $_GET['partner'] ?? '' ?>";  // Keep partner filter
            let url = `./partner_analytics.php?partner=${partner}&slot=&page=&city=&ad_id=${adId}`;

            window.location.href = url;
        }
    }
});

function logout(){
    fetch('./logout.php')
        .then(r=>r.json())
        .then(d=>{
            if(d.status==200){
                window.location.href='./login.php';
                alert('You have been logged out!');
            }
        });
}
</script>

</body>
</html>
