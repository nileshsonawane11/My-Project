<?php
session_start();
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

/* ------------------------------------------------------------------
   ALLOW ACCESS:
   1) Admin (via session)
   2) Partners via shared link (?partner=ABC)
------------------------------------------------------------------- */

$logged_in_admin = isset($_SESSION['admin_user']);
$partner_param   = $_GET['partner'] ?? "";

if (!$logged_in_admin && $partner_param == "") {
    die("<h3>Access Denied</h3>");
}

/* ------------------------------------------------------------------
   SELECTED PARTNER
------------------------------------------------------------------- */

$selected_partner = $logged_in_admin ? ($_GET['partner'] ?? "") : $partner_param;

/* ------------------------------------------------------------------
   FETCH FILTER OPTIONS DYNAMICALLY
------------------------------------------------------------------- */
$slots  = [];
$pages  = [];
$cities = [];
$ads_ids = [];

$res = $conn->query("SELECT DISTINCT slot FROM ads");
while ($r = $res->fetch_assoc()) $slots[] = $r['slot'];

$res = $conn->query("SELECT DISTINCT page FROM ads WHERE page!='' AND page IS NOT NULL");
while ($r = $res->fetch_assoc()) $pages[] = $r['page'];

$res = $conn->query("SELECT DISTINCT city FROM ads WHERE city!='' AND city IS NOT NULL");
while ($r = $res->fetch_assoc()) $cities[] = $r['city'];

$res = $conn->query("SELECT id FROM ads ORDER BY id DESC");
while ($r = $res->fetch_assoc()) $ads_ids[] = $r['id'];

/* ------------------------------------------------------------------
   GET FILTER INPUT
------------------------------------------------------------------- */
$filter_slot = $_GET['slot'] ?? "";
$filter_page = $_GET['page'] ?? "";
$filter_city = $_GET['city'] ?? "";
$filter_adid = $_GET['ad_id'] ?? "";

/* ------------------------------------------------------------------
   BUILD CONDITIONS
------------------------------------------------------------------- */
$where = "1=1";
$params = [];
$types  = "";

// Force partner filter
if ($selected_partner != "") {
    $where .= " AND ads.partner = ?";
    $params[] = $selected_partner;
    $types .= "s";
}

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
if ($filter_adid != "") {
    $where .= " AND ads.id = ?";
    $params[] = $filter_adid;
    $types .= "i";
}

/* ------------------------------------------------------------------
   FETCH PARTNER ANALYTICS TABLE
------------------------------------------------------------------- */
$sql = "
SELECT 
    ads.id,
    ads.partner,
    ads.slot,
    ads.page,
    ads.city,
    ads.image,
    ads.start_date,
    ads.end_date,
    SUM(CASE WHEN ad_analytics.event_type='impression' THEN 1 ELSE 0 END) AS views,
    SUM(CASE WHEN ad_analytics.event_type='click' THEN 1 ELSE 0 END) AS clicks
FROM ads
LEFT JOIN ad_analytics ON ads.id = ad_analytics.ad_id
WHERE $where
GROUP BY ads.id
ORDER BY views DESC
";

$stmt = $conn->prepare($sql);
if ($types != "") $stmt->bind_param($types, ...$params);
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ------------------------------------------------------------------
   PARTNER SHARING URL
------------------------------------------------------------------- */
function get_full_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? "https://"
        : "http://";

    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

$share_url = get_full_url();


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Partner Ad Analytics</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
@media(max-width:600px){
  table{ font-size:14px; }
  .navbar{flex-direction:column;text-align:center;gap:10px;}
}
</style>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-3">
  <span class="navbar-brand">Partner Ad Analytics</span>

  <?php if($logged_in_admin): ?>
      <a href="#" class="btn btn-outline-light btn-sm"onclick="safeBack('dashboard.php'); return false;">← Back to Admin Dashboard</a>
  <?php endif; ?>
</nav>

<div class="container mt-4">

<!-- PARTNER TITLE -->
<h4 class="mb-3">
    Partner: <span class="text-primary"><?= $selected_partner ?: "All" ?></span>
</h4>

<?php if($logged_in_admin && $selected_partner != ""): ?>
<div class="alert alert-info">
    Share this link with the partner:<br>
    <strong><?= $share_url ?></strong>
</div>
<?php endif; ?>

<!-- FILTER FORM -->
<div class="card p-3 mb-4">
<form method="GET" class="row g-2">

    <input type="hidden" name="partner" value="<?= $selected_partner ?>">

    <div class="col-md-3">
      <select name="slot" class="form-control">
        <option value="">All Slots</option>
        <?php foreach($slots as $s): ?>
            <option value="<?=$s?>" <?=$filter_slot==$s?"selected":""?>><?=$s?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <select name="page" class="form-control">
        <option value="">All Pages</option>
        <?php foreach($pages as $p): ?>
            <option value="<?=$p?>" <?=$filter_page==$p?"selected":""?>><?=$p?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <select name="city" class="form-control">
        <option value="">All Cities</option>
        <?php foreach($cities as $c): ?>
            <option value="<?=$c?>" <?=$filter_city==$c?"selected":""?>><?=$c?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <select name="ad_id" class="form-control">
        <option value="">All Ad IDs</option>
        <?php foreach($ads_ids as $id): ?>
            <option value="<?=$id?>" <?=$filter_adid==$id?"selected":""?>>ID <?=$id?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-12">
      <button class="btn btn-primary w-100">Apply Filters</button>
    </div>

    <div class="col-md-12">
      <a class="btn btn-outline-secondary" href="./partner_analytics.php?partner=<?php echo $selected_partner;?>">Clear</a>
    </div>

</form>
</div>

<!-- OVERALL PERFORMANCE -->
<?php
// Calculate totals from fetched data
$totalViews = 0;
$totalClicks = 0;

foreach ($data as $d) {
    $totalViews  += (int)$d['views'];
    $totalClicks += (int)$d['clicks'];
}

$ctr = ($totalViews > 0) ? round(($totalClicks / $totalViews) * 100, 2) : 0;
?>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card p-3">
      <h6>Total Views</h6>
      <div class="fs-3 text-primary"><?= $totalViews ?></div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-3">
      <h6>Total Clicks</h6>
      <div class="fs-3 text-success"><?= $totalClicks ?></div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-3">
      <h6>Total CTR</h6>
      <div class="fs-3 text-dark"><?= $ctr ?>%</div>
    </div>
  </div>
</div>

<!-- ANALYTICS TABLE -->
<div class="card p-3">
<h5>Ads Performance</h5>
<div class="table-responsive">
<table class="table table-bordered table-hover mt-3">
<thead class="table-dark">
<tr>
  <th>ID</th>
  <th>Slot</th>
  <th>Preview</th>
  <th>Page</th>
  <th>City</th>
  <th>Start</th>
  <th>End</th>
  <th>Views</th>
  <th>Clicks</th>
  <th>CTR (%)</th>
</tr>
</thead>
<tbody>
<?php foreach($data as $row): ?>
<tr onclick="goTo('<?= $row['id'] ?>')" style="cursor:pointer;">
  <td><?= $row['id'] ?></td>
  <td><?= getSlotSize($row['slot']) ?></td>
  <td><img src="../assets/ads/<?= htmlspecialchars($row['image']) ?>" style="max-width:140px"></td>
  <td><?= $row['page'] !== "" ? htmlspecialchars($row['page']) : 'All Pages' ?></td>
  <td><?= $row['city'] !== "" ? htmlspecialchars($row['city']) : 'All Cities' ?></td>
  <td><?= $row['start_date'] ?? '-' ?></td>
  <td><?= $row['end_date'] ?? '-' ?></td>
  <td class="text-primary"><?= $row['views'] ?? 0 ?></td>
  <td class="text-success"><?= $row['clicks'] ?? 0 ?></td>
  <td><strong><?= $row['views']>0 ? round(($row['clicks']/$row['views'])*100,2):0 ?></strong></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

</div>
<script>
  function goTo(adId){
      let partner = "<?= $selected_partner ?>";
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