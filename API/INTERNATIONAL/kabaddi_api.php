<?php
// sports.php
// Replace with your real EntitySport API token
$apiToken = "068cdc7a4981de1b3f78309fd6b306d0";
$base = "https://rest.entitysport.com/v2/matches/?token={$apiToken}&status=";

// Helper to get matches by status
function fetchMatches($status) {
    global $base;
    $ch = curl_init($base . $status);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true)['response']['items'] ?? [];
}

// Fetch each category
$upcoming = fetchMatches(1);
$live     = fetchMatches(3);
$finished = fetchMatches(2);

echo "<h2>EntitiesSport Match Status Overview</h2>";

// Display function
function displayMatches($matches, $title) {
    echo "<h3>$title</h3>";
    if ($matches) {
        foreach ($matches as $m) {
            echo "<b>" . htmlentities($m['title']) . "</b><br>";
            echo "Teams: " . htmlentities($m['teama']['name'] ?? '') . " vs " . htmlentities($m['teamb']['name'] ?? '') . "<br>";
            echo "Status: " . htmlentities($m['status_str'] ?? '') . "<br>";
            echo "Format: " . htmlentities($m['format_str'] ?? '') . "<hr>";
        }
    } else {
        echo "No $title available right now.<br>";
    }
}

displayMatches($live, "Live Matches");
displayMatches($upcoming, "Upcoming Matches");
displayMatches($finished, "Completed Matches");
?>