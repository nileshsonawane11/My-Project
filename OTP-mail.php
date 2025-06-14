<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
error_reporting(0);
ini_set('display_errors', 0);

require 'vendor/autoload.php';
include 'config.php';
$data = file_get_contents('php://input');
$data = json_decode($data,true);
session_start();
$recipient_email = $data['email'];
$name = ($data['fname'].' ' . $data['lname']);
$for = $data['for'];
$otp_type = 'otp'.$for;


if ($for == 'registration') {
    $message_text = "You are required to enter the following code to verify your account on <b>LiveStrike</b> – the real-time score tracking system. The code is valid for <b>10 minutes</b>. Please enter the code in the appropriate field to continue.";
} elseif ($for == 'forgot') {
    $message_text = "You requested a password reset on <b>LiveStrike</b>. Please use the code below to reset your password. The code is valid for <b>10 minutes</b>. If you didn't request this, you can safely ignore this email.";
} else {
    $message_text = "Your OTP code is provided below.";
}

function generateOTP($otp_type, $recipient_email) {
    $otp = rand(1000, 9999);
    $_SESSION['recipient_email'] = $recipient_email;
    $_SESSION[$otp_type][$recipient_email] = $otp;
    $_SESSION['otp_created'][$recipient_email] = time();
}

$sql1 = "SELECT * FROM users WHERE email='$recipient_email' LIMIT 1";
$result1 = mysqli_query($conn, $sql1);

if($for == "registration"){
    if (mysqli_num_rows($result1) > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
        
            if ($row['email'] === $recipient_email){
                echo json_encode(["status" => "error", "message" => "Email already Used","for" => "$for"]);
                exit();
            }
        
        }
    }else{
   
        $otp = $_SESSION[$otp_type][$recipient_email];
        $validDuration = 600;

        if (isset($_SESSION[$otp_type][$recipient_email]) && isset($_SESSION['otp_created'][$recipient_email])) {
            if (time() - $_SESSION['otp_created'][$recipient_email] <= $validDuration) {
                // OTP is still valid
            } else {
                unset($_SESSION[$otp_type][$recipient_email]);
                unset($_SESSION['otp_created'][$recipient_email]);
                generateOTP($otp_type, $recipient_email);
            }
        } else {
            generateOTP($otp_type, $recipient_email);
        }

        flush();
        ob_flush();
        $otp = $_SESSION[$otp_type][$recipient_email];
    

        $email_content = "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>LiveStrike OTP Email</title>
            <style>
                :root {
                    --primary-light: #FAC01F;
                    --primary-dark: #F83900;
                }

                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: Arial, sans-serif;
                    background-color: #f0f0f0;
                    padding: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .container {
                    width: 100%;
                    max-width: 600px;
                    background-color: #fff;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 0 15px rgba(0,0,0,0.2);
                }

                .head {
                    background:rgb(0, 132, 255);
                    text-align: center;
                    padding: 20px;
                    font-size: 22px;
                    letter-spacing: 2px;
                }

                .image-header {
                    text-align: center;
                    padding: 20px;
                }

                .image-header img {
                    max-width: 100px;
                    height: auto;
                }

                .logo-name {
                    font-size: 30px;
                    font-weight: 400;
                    white-space: nowrap;
                }

                .txt-strike {
                    font-weight: 600;
                    background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                    background-clip: text;
                    -webkit-text-fill-color: transparent;
                }

                .para {
                    font-size: 22px;
                    font-weight: 600;
                    color: green;
                    text-align: center;
                    margin: 10px 0;
                }

                .content {
                    padding: 20px;
                    font-size: 16px;
                    background: #f9f9f9;
                    line-height: 1.6;
                    color: #333;
                }

                .otp-txt {
                    text-align: center;
                    font-weight: bold;
                    margin-top: 20px;
                }

                .otp-container {
                    font-size: 28px;
                    letter-spacing: 10px;
                    text-align: center;
                    margin: 10px 0;
                }

                .footer {
                    text-align: center;
                    font-size: 13px;
                    padding: 20px;
                    color: #555;
                }

                .footer a {
                    color: #007bff;
                    text-decoration: none;
                }

                hr {
                    border: none;
                    height: 1px;
                    background: #ccc;
                    margin: 20px 0;
                }

                @media (max-width: 480px) {
                    .otp-container {
                        font-size: 24px;
                        letter-spacing: 6px;
                    }

                    .content, .footer, .head {
                        padding: 15px;
                    }

                    .logo-name {
                        font-size: 24px;
                    }

                    .para {
                        font-size: 18px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='head'>Welcome to LiveStrike</div>
                <div class='image-header'>
                    <img src='https://i.ibb.co/gLY2MgSd/logo.png' alt='Livestrike'>
                    <div class='logo-name'>
                        <span class='txt-live'><b>Live</b></span><span class='txt-strike'>Strike</span>
                    </div>
                </div>
                <div class='para'>Prove your Signer Identity</div>
                <div class='content'>
                    Hello $name,<br><br>
                    $message_text
                    <div class='otp-txt'>Your verification code:</div>
                    <div class='otp-container'>$otp</div>
                </div>
                <hr>
                <div class='footer'>
                    <p>If this wasn't you, please ignore this email or contact our customer service:</p>
                    <p><a href='#'>support@livestrikesupport.com</a></p>
                    <br>
                    <p>© 2025 LiveStrike. All rights reserved.</p>
                    <p>Maharashtra, Nashik City, India</p>
                    <br>
                    <p><a href='#'>About LiveStrike</a> | <a href='#'>Contact Us</a></p>
                </div>
            </div>
        </body>
        </html>
        ";                                                                               
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
            $mail->addAddress($recipient_email); // Recipient
            $mail->Subject = 'LiveStrike OTP Verification';
            $mail->isHTML(true);                                                                    
            $mail->Body = $email_content;

            if ($mail->send()) {
                echo json_encode(["status" => "success", "message" => "Email sent successfully","for" => "$for"]);
            }
        } catch (Exception $e) {
            $otp_error = 'Email failed: ' . $mail->ErrorInfo;
            echo json_encode(["status" => "error", "message" => "$otp_error"]);
        }
    }
}elseif($for == 'forgot'){

    if (mysqli_num_rows($result1) > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
        
            if ($row['email'] === $recipient_email){
                $otp = $_SESSION[$otp_type][$recipient_email];
                $validDuration = 600;

                if (isset($_SESSION[$otp_type][$recipient_email]) && isset($_SESSION['otp_created'][$recipient_email])) {
                    if (time() - $_SESSION['otp_created'][$recipient_email] <= $validDuration) {
                        // OTP is still valid
                    } else {
                        unset($_SESSION[$otp_type][$recipient_email]);
                        unset($_SESSION['otp_created'][$recipient_email]);
                        generateOTP($otp_type, $recipient_email);
                    }
                } else {
                    generateOTP($otp_type, $recipient_email);
                }

                flush();
                ob_flush();
                $otp = $_SESSION[$otp_type][$recipient_email];
            

                $email_content = "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>LiveStrike OTP Email</title>
                    <style>
                        :root {
                            --primary-light: #FAC01F;
                            --primary-dark: #F83900;
                        }

                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }

                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f0f0f0;
                            padding: 20px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                        }

                        .container {
                            width: 100%;
                            max-width: 600px;
                            background-color: #fff;
                            border-radius: 10px;
                            overflow: hidden;
                            box-shadow: 0 0 15px rgba(0,0,0,0.2);
                        }

                        .head {
                            background:rgb(0, 132, 255);
                            text-align: center;
                            padding: 20px;
                            font-size: 22px;
                            letter-spacing: 2px;
                        }

                        .image-header {
                            text-align: center;
                            padding: 20px;
                        }

                        .image-header img {
                            max-width: 100px;
                            height: auto;
                        }

                        .logo-name {
                            font-size: 30px;
                            font-weight: 400;
                            white-space: nowrap;
                        }

                        .txt-strike {
                            font-weight: 600;
                            background: linear-gradient(0deg, var(--primary-light), var(--primary-dark));
                            background-clip: text;
                            -webkit-text-fill-color: transparent;
                        }

                        .para {
                            font-size: 22px;
                            font-weight: 600;
                            color: green;
                            text-align: center;
                            margin: 10px 0;
                        }

                        .content {
                            padding: 20px;
                            font-size: 16px;
                            background: #f9f9f9;
                            line-height: 1.6;
                            color: #333;
                        }

                        .otp-txt {
                            text-align: center;
                            font-weight: bold;
                            margin-top: 20px;
                        }

                        .otp-container {
                            font-size: 28px;
                            letter-spacing: 10px;
                            text-align: center;
                            margin: 10px 0;
                        }

                        .footer {
                            text-align: center;
                            font-size: 13px;
                            padding: 20px;
                            color: #555;
                        }

                        .footer a {
                            color: #007bff;
                            text-decoration: none;
                        }

                        hr {
                            border: none;
                            height: 1px;
                            background: #ccc;
                            margin: 20px 0;
                        }

                        @media (max-width: 480px) {
                            .otp-container {
                                font-size: 24px;
                                letter-spacing: 6px;
                            }

                            .content, .footer, .head {
                                padding: 15px;
                            }

                            .logo-name {
                                font-size: 24px;
                            }

                            .para {
                                font-size: 18px;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='head'>Welcome to LiveStrike</div>
                        <div class='image-header'>
                            <img src='https://i.ibb.co/gLY2MgSd/logo.png' alt='Livestrike'>
                            <div class='logo-name'>
                                <span class='txt-live'><b>Live</b></span><span class='txt-strike'>Strike</span>
                            </div>
                        </div>
                        <div class='para'>Prove your Signer Identity</div>
                        <div class='content'>
                            Hello $name,<br><br>
                            $message_text
                            <div class='otp-txt'>Your verification code:</div>
                            <div class='otp-container'>$otp</div>
                        </div>
                        <hr>
                        <div class='footer'>
                            <p>If this wasn't you, please ignore this email or contact our customer service:</p>
                            <p><a href='#'>support@livestrikesupport.com</a></p>
                            <br>
                            <p>© 2025 LiveStrike. All rights reserved.</p>
                            <p>Maharashtra, Nashik City, India</p>
                            <br>
                            <p><a href='#'>About LiveStrike</a> | <a href='#'>Contact Us</a></p>
                        </div>
                    </div>
                </body>
                </html>
                ";                                                                               
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
                    $mail->addAddress($recipient_email); // Recipient
                    $mail->Subject = 'LiveStrike OTP Verification';
                    $mail->isHTML(true);                                                                    
                    $mail->Body = $email_content;

                    if ($mail->send()) {
                        echo json_encode(["status" => "success", "message" => "Email sent successfully","for" => "$for"]);
                    }
                } catch (Exception $e) {
                    $otp_error = 'Email failed: ' . $mail->ErrorInfo;
                    echo json_encode(["status" => "error", "message" => "$otp_error"]);
                }
            }else{
                echo json_encode(["status" => "error", "message" => "Email not registered","for" => "$for"]);
                exit();
            }
        
        }
    }else{
        echo json_encode(["status" => "error", "message" => "Email not registered","for" => "$for"]);
        exit();
    }

}else{
    echo json_encode(["status" => "error", "message" => "invalid Credentials"]);
    exit();
}

?>