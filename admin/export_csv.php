<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include "../config.php";

// Only admin can access
if (!isset($_SESSION['admin_user'])) {
    die("Access denied.");
}

// Ensure DB connection
if (!$conn) die("DB connection failed: " . mysqli_connect_error());

// Start output buffering
ob_start();

// CSV filename
$filename = "ad_analytics_" . date("Y-m-d_H-i-s") . ".csv";

// Set headers
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$filename}");
header("Pragma: no-cache");
header("Expires: 0");

// Open output stream
$out = fopen("php://output", "w");
if (!$out) die("Failed to open output stream.");

// CSV header row (exact columns)
fputcsv($out, ['ID','Ad ID','Slot','Page','City','Device','IP','Event Type','Created At']);

// Fetch data from table
$query = "SELECT id, ad_id, slot, page, city, device, ip, event_type, created_at 
          FROM ad_analytics 
          ORDER BY id DESC";

$res = $conn->query($query);
if (!$res) die("Query failed: " . $conn->error);

// Write rows
while ($row = $res->fetch_assoc()) {
    fputcsv($out, [
        $row['id'],
        $row['ad_id'],
        $row['slot'],
        $row['page'],
        $row['city'],
        $row['device'],
        $row['ip'],
        $row['event_type'],
        $row['created_at']
    ]);
}

// Close output
fclose($out);
ob_end_flush();
exit;
