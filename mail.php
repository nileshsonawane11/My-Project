<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Fetch input values from POST request
$name = $_POST['name'] ?? 'Scorer';
$game = $_POST['game'] ?? 'Unknown Game';
$date = $_POST['date'] ?? 'Not Specified';
$time = $_POST['time'] ?? 'Not Specified';
$venue = $_POST['venue'] ?? 'Not Specified';
$recipient_email = $_POST['recipient_email'] ?? 'sonawanenileshk6@gmail.com';

// Create email content (HTML format)
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
            <p>Congratulations! You have been selected as the official scorer for an upcoming game. Below are the details:</p>
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
            <a href='/contact.html' class='button'>Contact</a>
        </div>
    </div>
</body>
</html>";                                                                               
// Send email using PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sonawanenileshk6@gmail.com'; // Change to your email
    $mail->Password = 'rwtv ftnw nsft luud'; // Use App Password if required
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('sonawanenileshk6@gmail.com', 'Nilesh Sonawane'); // Sender
    $mail->addAddress($recipient_email); // Recipient
    $mail->Subject = 'Scorer Selection Confirmation';
    $mail->isHTML(true);                                                                    
    $mail->Body = $email_content;

    if ($mail->send()) {
        echo 'Email sent successfully';
    }
} catch (Exception $e) {
    echo 'Email failed: ' . $mail->ErrorInfo;
}
?>