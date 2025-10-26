<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies
if(isset($_SESSION['user'])){
    header('location: ./dashboard.php?update=Live&sport=CRICKET');
    exit();
}
include './config.php'; // your DB connection file

// Fix for broken $_GET on localhost
parse_str($_SERVER['QUERY_STRING'] ?? '', $_GET);


if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($client);
        $data = $google_service->userinfo->get();

        $fname = $data->givenName ?? '';
        $lname = $data->familyName ?? '';
        $email = $data->email ?? '';
        $role = 'Admin';
        $phone = NULL;
        $password = md5($email);


        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists
            $row = $result->fetch_assoc();
            
            // Start session
            $_SESSION['user'] = $row['user_id']; // or $row['id'] depending on your DB
            $_SESSION['role'] = $row['role'];
            $_SESSION['email'] = $row['email'];

            // Redirect to dashboard
            header('Location: ./dashboard.php?update=Live&sport=CRICKET');
            exit();
        }

        if ($result->num_rows === 0) {
            // Generate secure unique user ID
            $input = uniqid(microtime(true) . bin2hex(random_bytes(5)) . $email, true);
            $google_id = hash('sha256', $input);
            // Register new user
            $stmt = $conn->prepare("INSERT INTO users (user_id, fname, lname, email, role, phone, password) VALUES ( ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $google_id, $fname, $lname, $email, $role, $phone, $password);
            $stmt->execute();
        }

        $sql2 = mysqli_query($conn,"SELECT * FROM users");
        $count = mysqli_num_rows($sql2);

        // Start session
        $_SESSION['user'] = $google_id;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;

        setcookie('user', $row['user_id'], time() + (60 * 60 * 24 * 30), "/");   
        setcookie('email', $row['email'], time() + (60 * 60 * 24 * 30), "/");

        $name = $fname.' '.$lname;

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
            
            $mail->addAddress($email); // Recipient
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

        header("Location: ./dashboard.php?update=Live&sport=CRICKET");
        exit();
    } else {
        echo "⚠️ Token Error: " . htmlspecialchars($token['error']);
    }
} else {
    echo "❌ Code not set in URL<br>";
    echo "🔗 Current URL: " . htmlspecialchars($_SERVER['REQUEST_URI']);
}
?>
