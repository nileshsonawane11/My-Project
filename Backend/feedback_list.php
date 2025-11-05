<?php
ob_start();
session_start();
error_reporting(1);
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

$rating = $data['rating'];

function generateStars($avg) {
    $full = floor($avg);               // full stars
    $fraction = $avg - $full;          // decimal part
    $empty = 5 - ceil($avg);           // remaining stars

    $html = "";

    // full stars
    for ($i = 0; $i < $full; $i++) {
        $html .= '<span>★</span>';
    }

    // fractional star (only if there's a decimal part)
    if ($fraction > 0) {
        $width = $fraction * 100; // e.g. 0.3 → 30%
        $html .= '<span class="partial" style="--fill:' . $width . '%;">★</span>';
    }

     // empty stars
     for ($i = 0; $i < $empty; $i++) {
         $html .= '★';
    }

    return $html;
}

$html = "";
$sql = "SELECT * FROM feedback " . ($rating != 'all' ? "WHERE rating = $rating " : "") . "ORDER BY `datetime` DESC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $email = $row['user_email'];
    $name = $row['user_name'];
    $message = $row['message'];
    $rating = $row['rating'];
    $date = $row['date'];

    $result2 = $conn->query("SELECT user_photo FROM users WHERE email = '$email'");
    $row2 = $result2->fetch_assoc();

    $img = (!empty($row2['user_photo'])) ? './assets/images/users/'.$row2['user_photo'] : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSORFOJqVPeomYYBCyhvMENTHiHex_yB9dEHA&s';

    $rate_stars = generateStars($rating);
    $html .= <<<HTML
                <div class="mem-feed">
                    <div class="mem-head">
                        <div class="mem-info">
                            <img src="$img" alt="">
                            <label class="mems-name">{$name}</label>
                        </div>
                        <div class="mem-rate-date">
                            <div class="mem-rate avg-stars">{$rate_stars}</div>
                            <div class="mem-date">{$date}</div>
                        </div>
                    </div>
                    <div class="mem-message">
                        {$message}
                    </div>
                </div>
            HTML;
}
if($result->num_rows < 1){
    echo '<div class="empty">No Feedbacks</div>';
}else{
    echo $html;
}

?>