<?php
session_start();
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
        $role = 'User';
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
            session_start();
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
            $stmt = $conn->prepare("INSERT INTO users (user_id, fname, lname, email, role, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $google_id, $fname, $lname, $email, $role, $phone, $password);
            $stmt->execute();
        }

        // Start session
        $_SESSION['user'] = $google_id;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;

        header("Location: ./dashboard.php?update=Live&sport=CRICKET");
        exit;
    } else {
        echo "⚠️ Token Error: " . htmlspecialchars($token['error']);
    }
} else {
    echo "❌ Code not set in URL<br>";
    echo "🔗 Current URL: " . htmlspecialchars($_SERVER['REQUEST_URI']);
}
?>
