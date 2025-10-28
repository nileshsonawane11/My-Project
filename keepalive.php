<?php
// keepalive.php
$url = "https://my-project-pk19.onrender.com"; // Replace with your Render app URL

// Perform the ping
$response = @file_get_contents($url);

// Optional logging (to check if it’s running)
$log = "[" . date("Y-m-d H:i:s") . "] Ping sent to $url\n";
file_put_contents(__DIR__ . "/keepalive_log.txt", $log, FILE_APPEND);

echo "Ping sent to $url";
?>
