<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
include "../config.php";

$filename = "ad_analytics_" . date("Y-m-d_H-i-s") . ".csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename={$filename}");
$out = fopen("php://output", "w");
fputcsv($out, ['id','ad_id','event_type','user_ip','city','device','created_at']);

$res = $conn->query("SELECT id,ad_id,event_type,user_ip,city,device,created_at FROM ad_analytics ORDER BY id DESC");
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['id'],$r['ad_id'],$r['event_type'],$r['user_ip'],$r['city'],$r['device'],$r['created_at']]);
}
fclose($out);
exit;
