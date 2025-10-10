<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
error_reporting(0);
ini_set('display_errors', 0);

require 'vendor/autoload.php';
include 'config.php';
$data = file_get_contents('php://input');
$data = json_decode($data,true);
header('Content-Type: application/json');
session_start();

// Fetch input values from POST request
$for_value = $data['for_value'] ?? 'Unknown person';
$game = $data['game'] ?? 'Unknown Game';
$date = $data['date'] ?? 'Not Specified';
$time = $data['time'] ?? 'Not Specified';
$venue = $data['venue'] ?? 'Not Specified';
$recipient_email = $data['recipient_email'] ?? 'sonawanenileshk6@gmail.com';

$sql = mysqli_query($conn,"SELECT * FROM users WHERE email = '$recipient_email'");
$row = mysqli_fetch_assoc($sql);
$name = $row['fname'].' '.$row['lname'];

$sql2 = mysqli_query($conn,"SELECT * FROM users");
$count = mysqli_num_rows($sql2);

echo "$game ,$date ,$time ,$venue ,$recipient_email";

// Create email content (HTML format)
if($for_value == 'Scorer'){
$password = $data['password'];
$email_content = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Congratulations - Scorer Selection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
            text-align: left;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 20px;
        }
        .details {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #555;
        }
        .button {
            display: block;
            width: 100%;
            text-align: center;
            padding: 10px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>CONGRATULATIONS!</h2>
        </div>
        <div class='content'>
            <p>Dear <strong>$name</strong>,</p>
            <p>Congratulations! You have been selected as the official scorer for an upcoming game. Your access code is: <strong>$password</strong></p>
            <div class='details'>
                <div class='details'>
                <p><strong>Game:</strong> $game</p>
                <p><strong>Date:</strong> $date</p>
                <p><strong>Time:</strong> $time</p>
                <p><strong>Venue:</strong> $venue</p>
            </div>

        </div>
        <div class='footer'>
            <p>If you have any questions, please contact us.</p>
            <a href='/support.php' class='button'>Contact</a>
        </div>
    </div>
</body>
</html>"; 

$subject = 'Scorer Selection Confirmation';
}else if($for_value == 'Umpire'){
$email_content = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Congratulations - Umpire Selection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
            text-align: left;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 20px;
        }
        .details {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #555;
        }
        .button {
            display: block;
            width: 96%;
            text-align: center;
            padding: 10px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>CONGRATULATIONS!</h2>
        </div>
        <div class='content'>
            <p>Dear <strong>$name</strong>,</p>
            <p>Congratulations! You have been selected as the official umpire for an upcoming game. Below are the details:</p>
            <div class='details'>
                <div class='details'>
                <p><strong>Game:</strong> $game</p>
                <p><strong>Date:</strong> $date</p>
                <p><strong>Time:</strong> $time</p>
                <p><strong>Venue:</strong> $venue</p>
            </div>

        </div>
        <div class='footer'>
            <p>If you have any questions, please contact us.</p>
            <a href='/support.php' class='button'>Contact</a>
        </div>
    </div>
</body>
</html>"; 

$subject = 'Umpire Selection Confirmation';
}else if($for_value == 'new_user'){
$email_content = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Welcome - LiveStrike</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
            text-align: left;
        }
        .header {
            background: #28a745;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 20px;
        }
        .details {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #555;
        }
        .button {
            display: block;
            width: 96%;
            text-align: center;
            padding: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>WELCOME TO LIVESTRIKE!</h2>
        </div>
        <div class='content'>
            <p>Dear <strong>$name</strong>,</p>
            <p>We are excited to welcome you as a $count<sup>th</sup> new member of <strong>LiveStrike</strong>! 🎉</p>
            <div class='details'>
                <p>✅ You can now log in and explore your account.</p>
                <p>✅ Stay updated with real-time scores and match updates.</p>
                <p>✅ Manage your profile and settings easily.</p>
            </div>
        </div>
        <div class='footer'>
            <p>If you need any assistance, feel free to contact us anytime.</p>
            <a href='/support.php' class='button'>Get Support</a>
        </div>
    </div>
</body>
</html>"; 

$subject = 'Welcome to LiveStrike';

    // Send email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'livestrike.in@gmail.com'; // Change to your email
        $mail->Password = 'sdie phiv vbgk qymy'; // Use App Password if required
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('livestrike.in@gmail.com', 'LiveStrike'); // Sender

        // $mail->isSMTP();
        // $mail->Host = 'smtp.hostinger.com';
        // $mail->SMTPAuth = true;
        // $mail->Username = 'admin@livestrike.in'; // Change to your email
        // $mail->Password = 'Livestrike@123'; // Use App Password if required
        // $mail->SMTPSecure = 'ssl';
        // $mail->Port = 465;

        // $mail->setFrom('admin@livestrike.in', 'LiveStrike'); // Sender
        
        $mail->addAddress($recipient_email); // Recipient
        $mail->Subject = $subject;
        $mail->isHTML(true);                                                                    
        $mail->Body = $email_content;
        $mail->send();

        $mail->clearAddresses();
        $mail->addAddress('livestrike.in@gmail.com'); // Recipient
        $mail->Subject = $subject;
        $mail->isHTML(true);                                                                    
        $mail->Body = $email_content;

        if ($mail->send()) {
            echo 'Email sent successfully';
        }
    } catch (Exception $e) {
        echo 'Email failed: ' . $mail->ErrorInfo;
    }

    exit();
}


// Send email using PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'livestrike.in@gmail.com'; // Change to your email
    $mail->Password = 'sdie phiv vbgk qymy'; // Use App Password if required
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('livestrike.in@gmail.com', 'LiveStrike'); // Sender

    // $mail->isSMTP();
    // $mail->Host = 'smtp.hostinger.com';
    // $mail->SMTPAuth = true;
    // $mail->Username = 'admin@livestrike.in'; // Change to your email
    // $mail->Password = 'Livestrike@123'; // Use App Password if required
    // $mail->SMTPSecure = 'ssl';
    // $mail->Port = 465;

    // $mail->setFrom('admin@livestrike.in', 'LiveStrike'); // Sender
    
    $mail->addAddress($recipient_email); // Recipient
    $mail->Subject = $subject;
    $mail->isHTML(true);                                                                    
    $mail->Body = $email_content;

    if ($mail->send()) {
        echo 'Email sent successfully';
    }
} catch (Exception $e) {
    echo 'Email failed: ' . $mail->ErrorInfo;
}
?>