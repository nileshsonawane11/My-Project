<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include "./config.php";

$data = json_decode(file_get_contents("php://input"), true);

// Validate
if (!$data || !isset($data['ad_id']) || !isset($data['event'])) {
    echo json_encode(["status" => "error", "msg" => "Invalid payload"]);
    exit;
}

$ad_id = intval($data["ad_id"]);
$slot  = $data["slot"] ?? "";
$page  = $data["page"] ?? "";
$city  = $data["city"] ?? "";
$event = $data["event"];

// Detect IP & device
$ip = $_SERVER["REMOTE_ADDR"];
$ua = strtolower($_SERVER["HTTP_USER_AGENT"]);
$device = (preg_match("/mobile|android|iphone|ipad|tablet/", $ua)) ? "mobile" : "desktop";

// Insert analytics
$stmt = $conn->prepare("
    INSERT INTO ad_analytics (ad_id, slot, page, city, device, ip, event_type)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("issssss",
    $ad_id,
    $slot,
    $page,
    $city,
    $device,
    $ip,
    $event
);

$stmt->execute();

echo json_encode(["status" => "ok"]);
?>
