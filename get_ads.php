<?php
include "./config.php";

$slot = $_GET['slot'] ?? "";
$page = $_GET['page'] ?? "";
$city = $_GET['city'] ?? "";

$currentDate = date("Y-m-d");

// Base query
$query = "SELECT id, image, url 
          FROM ads 
          WHERE slot = ? 
            AND active = 1 
            AND (
                  (start_date IS NULL OR start_date <= ?) 
                  AND 
                  (end_date IS NULL OR end_date >= ?)
                )";

// Add page filter
$pageFilter = ($page != "")
    ? " AND (page = ? OR page = '' OR page IS NULL)"
    : "";

// Add city filter
$cityFilter = ($city != "")
    ? " AND (city = ? OR city = '' OR city IS NULL)"
    : "";

$query .= $pageFilter . $cityFilter;

$stmt = $conn->prepare($query);

// Bind dynamically
if ($page != "" && $city != "") {
    $stmt->bind_param("sssss", $slot, $currentDate, $currentDate, $page, $city);
} elseif ($page != "") {
    $stmt->bind_param("ssss", $slot, $currentDate, $currentDate, $page);
} elseif ($city != "") {
    $stmt->bind_param("ssss", $slot, $currentDate, $currentDate, $city);
} else {
    $stmt->bind_param("sss", $slot, $currentDate, $currentDate);
}

$stmt->execute();
$res = $stmt->get_result();

$ads = [];
while ($row = $res->fetch_assoc()) {
    $ads[] = [
        "id"    => $row['id'],
        "image" => $row['image'],
        "url"   => $row['url']
    ];
}

echo json_encode($ads);
?>
