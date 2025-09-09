<?php
    session_start();
    include './config.php';

    if(!isset($_SESSION['user'])){
        header('location: ../../front-page.php');
        exit();
    }

    $user = $_SESSION['user'];
    $result = mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$user'");
    $row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiveStrike - Settings</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        :root {
            --primary-color: rgba(209, 34, 31, 1);
            --primary-red: rgba(209, 34, 31, 1);
            --primary-red-light: rgba(209, 34, 31, 0.8);
            --primary-red-dark: rgba(160, 25, 23, 1);
            --background: linear-gradient(135deg, #1e1e1e, #121212);
            --card-bg: #1e1e1e;
            --text-dark: #e0e0e0;
            --text-light: #a0a0a0;
            --border-color: #333333;
            --text-color: #ffffff;
            --light-bg: #1e1e1e;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            --border-color: #333333;
            --accent-color: #1c0003ff;
            --svg-fill: white;
            --nav-fill: #2d2d2d;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --transition: all 0.3s ease;
        }

        [data-theme="light"] {
            --background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
            --card-bg: #ffffff;
            --text-color: #000000;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --text-dark: #212529;
            --text-light: #495057;
            --border-color: #dee2e6;
            --svg-fill: black;
            --nav-fill: #ffffffff;
        }

        body {
            background: var(--background);
            color: var(--text-dark);
            min-height: 100vh;
            padding: 20px;
            transition: var(--transition);
        }

        .settings-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .settings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .settings-header h1 {
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-header h1 svg {
            fill: var(--primary-red);
        }

        .back-button {
            background: var(--primary-red);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: var(--transition);
        }

        .back-button:hover {
            background: var(--primary-red-dark);
            transform: translateY(-2px);
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .settings-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
        }

        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .card-header svg {
            fill: var(--primary-red);
            width: 24px;
            height: 24px;
        }

        .setting-item {
            margin-bottom: 20px;
        }

        .setting-item:last-child {
            margin-bottom: 0;
        }

        .setting-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border-color);
            transition: var(--transition);
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: var(--transition);
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-red);
        }

        input:checked + .slider:before {
            transform: translateX(30px);
        }

        .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        backdrop-filter: blur(3px);
    }

    .popup-box {
        background: var(--background);
        padding: 30px;
        border-radius: var(--border-radius);
        width: 100%;
        max-width: 400px;
        box-shadow: var(--card-shadow);
        animation: popIn 0.3s ease-out;
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    @keyframes popIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .popup-message {
        margin-bottom: 20px;
        font-size: 1.1rem;
        line-height: 1.5;
        color: var(--text-color);
        transition: color 0.3s ease;
    }

    .popup-input {
        width: 100%;
        padding: 12px 15px;
        margin: 8px 0 15px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        transition: var(--transition);
        background-color: var(--light-bg);
        color: var(--text-color);
    }

    .popup-input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 3px var(--primary-transparent);
    }

    .popup-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .popup-btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
    }

    .popup-btn.cancel {
        background-color: var(--light-bg);
        color: var(--text-color);
        border: none;
    }

    .popup-btn.cancel:hover {
        background-color: var(--border-color);
    }

    .popup-btn.confirm {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }

    .popup-btn.confirm:hover {
        background-color: var(--primary-dark);
    }

        .select-style {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--card-bg);
            color: var(--text-dark);
            font-size: 16px;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
        }

        .button-primary {
            background: var(--primary-red);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            margin-top: 10px;
        }

        .button-primary:hover {
            background: var(--primary-red-dark);
        }

        .button-secondary {
            background: transparent;
            color: var(--primary-red);
            border: 1px solid var(--primary-red);
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            margin-top: 10px;
        }

        .button-secondary:hover {
            background: rgba(209, 34, 31, 0.1);
        }

        .sports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .sport-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .sport-item:hover {
            border-color: var(--primary-red);
        }

        .sport-item.active {
            border-color: var(--primary-red);
            background: rgba(209, 34, 31, 0.1);
        }

        .sport-icon {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
            fill: var(--text-light);
        }

        .sport-item.active .sport-icon {
            fill: var(--primary-red);
        }

        .sport-name {
            font-size: 14px;
            text-align: center;
            color: var(--text-light);
        }

        .sport-item.active .sport-name {
            color: var(--primary-red);
            font-weight: 600;
        }

        .account-detail {
            display: flex;
            white-space: normal;
            overflow-wrap: break-word;
            gap: 50px;
            justify-content: space-between;
            text-wrap: auto;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .account-detail:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--text-light);
        }

        .detail-value {
            font-weight: 500;
            flex: 1 1 0;
            min-width: 0;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            text-align: right;
        }

        .danger-zone {
            border: 1px solid var(--primary-red);
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }

        .danger-zone h3 {
            color: var(--primary-red);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .sports-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }

        .theme-switch-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .theme-text {
            font-size: 16px;
            color: var(--text-dark);
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <div class="settings-header">
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                    <path d="M12 1l9.5 5.5v11L12 23l-9.5-5.5v-11L12 1zm0 2.311L4.5 7.653v8.694l7.5 4.342 7.5-4.342V7.653L12 3.311zM12 16a4 4 0 110-8 4 4 0 010 8zm0-2a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
                Settings
            </h1>
            <button class="back-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </button>
        </div>

        <div class="settings-grid">
            <!-- Appearance Settings -->
            <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 9a3 3 0 100 6 3 3 0 000-6zm0 8a5 5 0 110-10 5 5 0 010 10zm9-5a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2>Appearance</h2>
                </div>
                
                <div class="setting-item">
                    <label>Theme</label>
                    <div class="theme-switch-wrapper">
                        <span class="theme-text">Dark Mode</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="theme-switcher">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                
                <!-- <div class="setting-item">
                    <label for="font-size">Font Size</label>
                    <select class="select-style" id="font-size">
                        <option value="small">Small</option>
                        <option value="medium" selected>Medium</option>
                        <option value="large">Large</option>
                    </select>
                </div>
                
                <div class="setting-item">
                    <label for="sport-density">Match Density</label>
                    <select class="select-style" id="sport-density">
                        <option value="compact">Compact</option>
                        <option value="normal" selected>Normal</option>
                        <option value="comfortable">Comfortable</option>
                    </select>
                </div> -->
            </div>

            <!-- Notification Settings -->
            <!-- <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M20 17h2v2H2v-2h2v-7a8 8 0 1116 0v7zm-2 0v-7a6 6 0 10-12 0v7h12zm-9 4h6v2H9v-2z"/>
                    </svg>
                    <h2>Notifications</h2>
                </div>
                
                <div class="setting-item">
                    <label>Match Updates</label>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label>Score Alerts</label>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label>Live Commentary</label>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label>News & Updates</label>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div> -->

            <!-- Sports Preferences -->
            <!-- <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 11.586l4.243 4.242-1.415 1.415L10.586 13l-4.243 4.243-1.414-1.415L9.172 12 4.929 7.757l1.414-1.414L10.586 11l4.242-4.243 1.415 1.414L12 11.586z"/>
                    </svg>
                    <h2>Sports Preferences</h2>
                </div>
                
                <p>Select your favorite sports to prioritize in your feed:</p>
                
                <div class="sports-grid">
                    <div class="sport-item active">
                        <svg class="sport-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8zm6-10a6 6 0 01-6 6 6 6 0 01-6-6 6 6 0 016-6 6 6 0 016 6z"/>
                        </svg>
                        <span class="sport-name">Cricket</span>
                    </div>
                    
                    <div class="sport-item active">
                        <svg class="sport-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8z"/>
                        </svg>
                        <span class="sport-name">Football</span>
                    </div>
                    
                    <div class="sport-item">
                        <svg class="sport-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M17 6a1 1 0 000 2 1 1 0 010 2H7a1 1 0 010-2 1 1 0 000-2h10zm-1 4a1 1 0 010 2H8a1 1 0 010-2h8zm-1 4a1 1 0 010 2H9a1 1 0 010-2h6z"/>
                        </svg>
                        <span class="sport-name">Basketball</span>
                    </div>
                    
                    <div class="sport-item">
                        <svg class="sport-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M17 6a1 1 0 000 2 1 1 0 010 2H7a1 1 0 010-2 1 1 0 000-2h10zm-1 4a1 1 0 010 2H8a1 1 0 010-2h8zm-1 4a1 1 0 010 2H9a1 1 0 010-2h6z"/>
                        </svg>
                        <span class="sport-name">Volleyball</span>
                    </div>
                    
                    <div class="sport-item active">
                        <svg class="sport-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8z"/>
                        </svg>
                        <span class="sport-name">Kabaddi</span>
                    </div>
                    
                    <div class="sport-item">
                        <svg class="sport-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M17 6a1 1 0 000 2 1 1 0 010 2H7a1 1 0 010-2 1 1 0 000-2h10zm-1 4a1 1 0 010 2H8a1 1 0 010-2h8zm-1 4a1 1 0 010 2H9a1 1 0 010-2h6z"/>
                        </svg>
                        <span class="sport-name">Kho-Kho</span>
                    </div>
                </div>
                
                <button class="button-primary">Save Preferences</button>
            </div> -->

            <!-- Account Settings -->
            <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 2a5 5 0 105 5 5 5 0 00-5-5zm0 8a3 3 0 113-3 3 3 0 01-3 3zm9 11v-1a7 7 0 00-7-7h-4a7 7 0 00-7 7v1h2v-1a5 5 0 015-5h4a5 5 0 015 5v1h2z"/>
                    </svg>
                    <h2>Account</h2>
                </div>
                
                <div class="account-detail">
                    <span class="detail-label">Name</span>
                    <span class="detail-value"><?php echo $row['fname'].' '.$row['lname']; ?></span>
                </div>
                
                <div class="account-detail">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?php echo $row['email']; ?></span>
                </div>
                
                <div class="account-detail">
                    <span class="detail-label">Member since</span>
                    <span class="detail-value"><?php echo date("F Y", strtotime($row['date'])); ?></span>
                </div>
                
                <div class="account-detail">
                    <span class="detail-label">Subscription</span>
                    <span class="detail-value">Free</span>
                </div>
                
            </div>

            <!-- Data & Privacy -->
            <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 1l9.5 5.5v11L12 23l-9.5-5.5v-11L12 1zm0 2.311L4.5 7.653v8.694l7.5 4.342 7.5-4.342V7.653L12 3.311zM12 16a4 4 0 110-8 4 4 0 010 8zm0-2a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    <h2>Data & Privacy</h2>
                </div>
                
                <!-- <div class="setting-item">
                    <label>Data Saving Mode</label>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label>Auto-play Videos</label>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label>Personalized Ads</label>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div> -->
                
                <!-- <button class="button-secondary">Download My Data</button> -->
                <button class="button-secondary" onclick = "window.location.href = './privacy-policy.php'">Privacy Policy</button>
            </div>

            <!-- Language & Region -->
            <!-- <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8zm5-9a5 5 0 01-5 5 5 5 0 01-5-5 5 5 0 015-5 5 5 0 015 5z"/>
                    </svg>
                    <h2>Language & Region</h2>
                </div>
                
                <div class="setting-item">
                    <label for="language">Language</label>
                    <select class="select-style" id="language">
                        <option value="en" selected>English</option>
                        <option value="hi">Hindi</option>
                        <option value="bn">Bengali</option>
                        <option value="ta">Tamil</option>
                        <option value="te">Telugu</option>
                    </select>
                </div>
                
                <div class="setting-item">
                    <label for="region">Region</label>
                    <select class="select-style" id="region">
                        <option value="global" selected>Global</option>
                        <option value="in">India</option>
                        <option value="us">United States</option>
                        <option value="uk">United Kingdom</option>
                        <option value="au">Australia</option>
                    </select>
                </div>
                
                <div class="setting-item">
                    <label for="timezone">Timezone</label>
                    <select class="select-style" id="timezone">
                        <option value="ist" selected>IST (India Standard Time)</option>
                        <option value="est">EST (Eastern Standard Time)</option>
                        <option value="pst">PST (Pacific Standard Time)</option>
                        <option value="gmt">GMT (Greenwich Mean Time)</option>
                    </select>
                </div>
                
                <button class="button-primary">Apply Changes</button>
            </div> -->
            <!-- <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm0 18a8 8 0 118-8 8 8 0 01-8 8zm5-9a5 5 0 01-5 5 5 5 0 01-5-5 5 5 0 015-5 5 5 0 015 5z"/>
                    </svg>
                    <h2>Language Preferences</h2>
                </div>
                
                <div class="setting-item">
                    <label for="languageSelector">Default Language</label>
                    <select class="select-style" id="languageSelector">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                        <option value="hi">Hindi</option>
                        <option value="bn">Bengali</option>
                        <option value="ta">Tamil</option>
                        <option value="te">Telugu</option>
                        <option value="mr">Marathi</option>
                        <option value="ur">Urdu</option>
                        <option value="zh">Chinese</option>
                        <option value="ja">Japanese</option>
                        <option value="ru">Russian</option>
                        <option value="ar">Arabic</option>
                    </select>
                </div>
                
                <div class="setting-item">
                    <label>Auto-Translate Content</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="autoTranslateToggle">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="translation-status" id="translationStatus">
                    Translation applied successfully!
                </div>
                
                <button class="button-primary" id="applyLanguageBtn">Apply Language Settings</button>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M21.5 9.5L20 8l-1.5 1.5L17 8l-1.5 1.5L14 8l-1.5 1.5L11 8 9.5 9.5 8 8 6.5 9.5 5 8 3.5 9.5 2 8v12l1.5-1.5L5 20l1.5-1.5L8 20l1.5-1.5L11 20l1.5-1.5L14 20l1.5-1.5L17 20l1.5-1.5L20 20l1.5-1.5L23 20V8l-1.5 1.5zM3 10h18v8H3v-8z"/>
                    </svg>
                    <h2>Translate Page Content</h2>
                </div>
                
                <div class="setting-item">
                    <label for="translateTo">Translate to:</label>
                    <select class="select-style" id="translateTo">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                        <option value="hi">Hindi</option>
                        <option value="bn">Bengali</option>
                        <option value="ta">Tamil</option>
                        <option value="te">Telugu</option>
                    </select>
                </div>
                
                <button class="button-primary" id="translatePageBtn">Translate Now</button>
                
                <div class="translation-status" id="pageTranslationStatus">
                    Page translation completed!
                </div>
            </div>
        </div> -->

        <!-- Danger Zone -->
        <div class="danger-zone">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Danger Zone
            </h3>
            <p>Once you delete your account, there is no going back. Please be certain.</p>
            <button class="delete-account button-secondary" style="color: var(--primary-red); border-color: var(--primary-red);">
                Delete Account
            </button>
            <button class="logout-btn button-secondary" style="color: var(--primary-red); border-color: var(--primary-red);">
                Log Out
            </button>
        </div>
    </div>
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-box" id="popupBox">
            <p class="popup-message" id="popupMessage"></p>
            <div class="popup-actions">
                <button class="popup-btn cancel" id="cancelBtn">Cancel</button>
                <button class="popup-btn confirm" id="confirmBtn">Confirm</button>
            </div>
        </div>
    </div>

    <script>

        let back =document.querySelector(".back-button");
        let theme_label = document.querySelector(".theme-text");
        const popupOverlay = document.getElementById("popupOverlay");
        const popupMessage = document.getElementById("popupMessage");
        const confirmBtn = document.getElementById("confirmBtn");
        const cancelBtn = document.getElementById("cancelBtn");

        const messages = {
        "logout": "Are you sure you want to log out?",
        "del": "Are you sure you want to delete your account?"
        };

        // Cancel button hides popup
        cancelBtn.addEventListener("click", () => {
        popupOverlay.style.display = "none";
        });


        //log out
        function logout(){
            fetch('logout.php')
            .then(response => response.json())
            .then(data => {
                if(data.status === 200){
                    window.location.href = 'front-page.php';
                    alert ('You have been logged out!')
                    popupOverlay.style.display = "none";
                }
                console.log(data)})
            .catch(error => console.error(error));
        }

        function deleteAccount(){
            fetch('./Backend/delete_account.php')
            .then(response => response.json())
            .then(data => {
                if(data.status === 200){
                    // window.location.href = 'front-page.php';
                    alert ('Account will deactivate in 24 hrs.!')
                    popupOverlay.style.display = "none";
                }
                console.log(data)})
            .catch(error => console.error(error));
        }

        back.addEventListener("click" ,() => {
            window.history.back();
        });
        // Theme switching functionality
        const themeSwitcher = document.getElementById('theme-switcher');
        
        // Check for saved theme preference or use system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Set initial theme
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
            themeSwitcher.checked = (savedTheme === 'dark');

            if(savedTheme === 'dark')
            {
                theme_label.innerText = "Dark Mode";
            }
            else {
                theme_label.innerText = "Light Mode";
            }
        } else if (systemPrefersDark) {
            document.documentElement.setAttribute('data-theme', 'Dark');
            themeSwitcher.checked = true;
            theme_label.innerText = "Dark Mode";
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            themeSwitcher.checked = false;
            theme_label.innerText = "Light Mode";
        }
        
        // Theme switcher event listener
        themeSwitcher.addEventListener('change', function() {
            if (this.checked) {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                theme_label.innerText = "Dark Mode";
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                theme_label.innerText = "Light Mode";
            }
        });
        
        // Sport selection functionality
        const sportItems = document.querySelectorAll('.sport-item');
        sportItems.forEach(item => {
            item.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        });

        // Language Translator Functionality
            class LiveStrikeTranslator {
                constructor() {
                    this.currentLang = 'en';
                    this.autoTranslate = false;
                    this.apiKey = 'YOUR_GOOGLE_TRANSLATE_API_KEY'; // Replace with your actual API key
                    this.translateEndpoint = 'https://translation.googleapis.com/language/translate/v2';
                    
                    // this.init();
                }

                // init() {
                //     // Load saved preferences
                //     this.loadPreferences();
                    
                //     // Set up event listeners
                //     this.setupEventListeners();
                // }

                loadPreferences() {
                    // Load language preference
                    const savedLang = localStorage.getItem('liveStrikeLanguage');
                    if (savedLang) {
                        this.currentLang = savedLang;
                        document.getElementById('languageSelector').value = savedLang;
                    }
                    
                    // Load auto-translate preference
                    const autoTranslate = localStorage.getItem('liveStrikeAutoTranslate');
                    if (autoTranslate) {
                        this.autoTranslate = autoTranslate === 'true';
                        document.getElementById('autoTranslateToggle').checked = this.autoTranslate;
                    }
                }

                setupEventListeners() {
                    // Language selector
                    document.getElementById('languageSelector').addEventListener('change', (e) => {
                        this.currentLang = e.target.value;
                    });
                    
                    // Auto-translate toggle
                    document.getElementById('autoTranslateToggle').addEventListener('change', (e) => {
                        this.autoTranslate = e.target.checked;
                        localStorage.setItem('liveStrikeAutoTranslate', this.autoTranslate);
                        
                        if (this.autoTranslate) {
                            this.showStatus('Auto-translation enabled', true);
                        } else {
                            this.showStatus('Auto-translation disabled', true);
                        }
                    });
                    
                    // Apply language settings
                    document.getElementById('applyLanguageBtn').addEventListener('click', () => {
                        this.saveLanguagePreferences();
                    });
                    
                    // Translate page button
                    document.getElementById('translatePageBtn').addEventListener('click', () => {
                        const targetLang = document.getElementById('translateTo').value;
                        this.translatePage(targetLang);
                    });
                }

                saveLanguagePreferences() {
                    localStorage.setItem('liveStrikeLanguage', this.currentLang);
                    this.showStatus('Language preferences saved successfully!', true);
                    
                    // If auto-translate is enabled, translate the page
                    if (this.autoTranslate) {
                        this.translatePage(this.currentLang);
                    }
                }

                async translatePage(targetLang) {
                    const statusElement = document.getElementById('pageTranslationStatus');
                    
                    try {
                        this.showStatus('Translating page content...', false, statusElement);
                        
                        // In a real implementation, you would send text to Google Translate API
                        // For demonstration, we'll simulate the translation process
                        
                        // Simulate API call delay
                        await new Promise(resolve => setTimeout(resolve, 1500));
                        
                        // This is where you would make the actual API call:
                        /*
                        const response = await fetch(`${this.translateEndpoint}?key=${this.apiKey}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                q: textToTranslate,
                                target: targetLang,
                                format: 'text'
                            })
                        });
                        
                        const data = await response.json();
                        const translatedText = data.data.translations[0].translatedText;
                        */
                        
                        // For demo purposes, we'll just change the language attribute
                        document.documentElement.lang = targetLang;
                        
                        this.showStatus(`Page translated to ${this.getLanguageName(targetLang)}!`, true, statusElement);
                        
                    } catch (error) {
                        console.error('Translation error:', error);
                        this.showStatus('Translation failed. Please try again.', false, statusElement, true);
                    }
                }

                getLanguageName(code) {
                    const languages = {
                        'en': 'English',
                        'es': 'Spanish',
                        'fr': 'French',
                        'de': 'German',
                        'hi': 'Hindi',
                        'bn': 'Bengali',
                        'ta': 'Tamil',
                        'te': 'Telugu',
                        'mr': 'Marathi',
                        'ur': 'Urdu',
                        'zh': 'Chinese',
                        'ja': 'Japanese',
                        'ru': 'Russian',
                        'ar': 'Arabic'
                    };
                    
                    return languages[code] || code;
                }

                showStatus(message, isSuccess, element = null, isError = false) {
                    const statusElement = element || document.getElementById('translationStatus');
                    
                    statusElement.textContent = message;
                    statusElement.style.display = 'block';
                    
                    if (isError) {
                        statusElement.classList.add('error');
                        statusElement.classList.remove('success');
                    } else {
                        statusElement.classList.remove('error');
                        statusElement.classList.add('success');
                    }
                    
                    // Hide status after 3 seconds
                    setTimeout(() => {
                        statusElement.style.display = 'none';
                    }, 3000);
                }

                // Method to translate specific text (for use elsewhere in the app)
                async translateText(text, targetLang) {
                    try {
                        // This is where you would make the actual API call to Google Translate
                        /*
                        const response = await fetch(`${this.translateEndpoint}?key=${this.apiKey}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                q: text,
                                target: targetLang,
                                format: 'text'
                            })
                        });
                        
                        const data = await response.json();
                        return data.data.translations[0].translatedText;
                        */
                        
                        // For demo purposes, return the original text with a prefix
                        return `[${targetLang}] ${text}`;
                    } catch (error) {
                        console.error('Translation error:', error);
                        return text; // Return original text on error
                    }
                }
            }

            // Initialize the translator when the page loads
            document.addEventListener('DOMContentLoaded', () => {
                window.liveStrikeTranslator = new LiveStrikeTranslator();
            });

            // Add event listeners to each action button
        document.querySelectorAll(".action-btn, .logout-btn, .delete-account").forEach(element => {
            element.addEventListener("click", () => {
                popupMessage.innerHTML = ""; // Reset dialog content
                let className = element.classList.contains('logout-btn') ? 'logout' : 
                              element.classList.contains('delete-account') ? 'del' : 
                              element.textContent.trim().toLowerCase().replace(' ', '-');

                if (className === "change-password") {
                    // Show form for change password
                    popupMessage.innerHTML = `
                    <label for="currentPass">Current Password</label><br>
                    <input type="password" id="currentPass" class="popup-input"><br><br>

                    <label for="newPass">New Password</label><br>
                    <input type="password" id="newPass" class="popup-input"><br><br>

                    <label for="confirmPass">Confirm Password</label><br>
                    <input type="password" id="confirmPass" class="popup-input"><br>

                    <div id="error-empty" class="error"></div>
                    `;
                } else {
                    // Show message for logout or delete
                    popupMessage.textContent = messages[className] || "Are you sure you want to proceed?";
                }

                // Show popup
                popupOverlay.style.display = "flex";

                // Handle Confirm
                confirmBtn.onclick = (e) => {
                    e.preventDefault();
                    if (className === "change-password") {
                        const current = document.getElementById("currentPass").value;
                        const newPass = document.getElementById("newPass").value;
                        const confirm = document.getElementById("confirmPass").value;
                        console.log(current, newPass, confirm);

                        let formData = new FormData();
                        formData.append('user_id', user_id);
                        formData.append('current', current);
                        formData.append('newPass', newPass);
                        formData.append('confirm', confirm);
                        
                        fetch('./Backend/change-password.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            console.log(data);
                            document.querySelectorAll('[id^="error-"]').forEach((el) => {
                                el.innerHTML = '';
                                el.style.display = 'none';
                            });

                            if(data.status !== 200){
                                let el = document.getElementById(`error-${data.field}`);
                                el.innerHTML = data.message;
                                el.style.display = 'block';
                            }else if(data.status == 200){
                                popupOverlay.style.display = "none";
                            }
                        })
                        .catch(err => console.error('Error:', err));

                    } else {
                    // alert(`${className} confirmed.`);
                        if(className === "logout"){
                            logout();
                        }

                        if(className === "del"){
                            deleteAccount();
                        }
                    }

                    // popupOverlay.style.display = "none"; // Hide popup
                };
            });
        });
    </script>
</body>
</html>