<?php
    require_once 'vendor/autoload.php'; // Make sure Google Client is installed via Composer

    // GOOGLE CLIENT SETUP
    $client = new Google_Client();
    $client->setClientId('997608002354-bj5utfhc7758qae953pci431j0gudahe.apps.googleusercontent.com');
    $client->setClientSecret('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');
    $client->setRedirectUri('http://localhost/project_files/google-login.php'); // adjust if your folder is named differently
    $client->addScope("email");
    $client->addScope("profile");
    // important for refresh token
    $client->setAccessType('offline');
    $client->setPrompt('consent');

    $host = "127.0.0.1";
    $user = "root";
    $pass = "";
    $db = "livestrike";
    $port = "3307";

    $conn = mysqli_connect($host, $user, $pass, $db, $port);

    date_default_timezone_set("Asia/Kolkata");
?>