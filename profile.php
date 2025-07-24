<?php
    session_start();
    $user_id = $_SESSION['user'];
    include './config.php';

    if(!isset($user_id)){
        header('location: ./front-page.php');
        exit();
    }

    $query = mysqli_query($conn,"SELECT * FROM `users` WHERE `user_id` = '$user_id'");
    $count = mysqli_num_rows($query);
    if($count == 0){
        header('location: ./front-page.php');
        exit();
    }

    $row = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select: none;
            scrollbar-width: none;
        }

        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --primary-light-transparent: rgba(250, 192, 31, 0.1);
            --primary-dark-transparent: rgba(248, 57, 0, 0.1);
            --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            --text-color: #333333;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            min-height: 100vh;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 800px;
            background: white;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .header {
            background: var(--background);
            padding: 20px;
            color: white;
            position: relative;
        }

        .return {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .return svg {
            cursor: pointer;
            transition: var(--transition);
            fill: white;
        }

        .return svg:hover {
            transform: translateX(-3px);
        }

        .edit-toggle svg {
            fill: none;
            stroke: white;
            transition: var(--transition);
        }

        .edit-toggle:hover svg {
            stroke: rgba(255,255,255,0.8);
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 0;
        }

        .img-container {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .profile-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .select-img {
            position: absolute;
            bottom: 0;
            right: 0;
            background: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .select-img:hover {
            background: #f0f0f0;
        }

        .select-img svg {
            width: 20px;
            height: 20px;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: white;
        }

        .profile-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .meta-item svg {
            width: 16px;
            height: 16px;
            fill: white;
        }

        .form-section {
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-dark);
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: #f9f9f9;
        }

        .form-input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-light-transparent);
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            background-color: #f9f9f9;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
        }

        .actions-section {
            padding: 0 30px 30px;
        }

        .action-btn,
        .action-btn1 {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border: none;
            width: 100%;
            text-align: left;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .action-btn:hover,
        .action-btn1:hover {
            background-color: #f5f5f5;
            transform: translateY(-2px);
        }

        .logout-btn,
        .save-btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 12px 30px;
            background-color: var(--primary-dark);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(248, 57, 0, 0.2);
        }

        .logout-btn:hover {
            background-color: #e03400;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 57, 0, 0.3);
        }

        .delete-account {
            display: inline-block;
            margin-top: 15px;
            color: var(--primary-dark);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .delete-account:hover {
            color: #e03400;
            text-decoration: underline;
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
            background: #fff;
            padding: 30px;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: popIn 0.3s ease-out;
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
        }

        .popup-input {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .popup-input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-light-transparent);
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
            background-color: #f0f0f0;
            color: #333;
            border: none;
        }

        .popup-btn.cancel:hover {
            background-color: #e0e0e0;
        }

        .popup-btn.confirm {
            background-color: var(--primary-dark);
            color: white;
            border: none;
        }

        .popup-btn.confirm:hover {
            background-color: #e03400;
        }

        /* Edit mode styles */
        .edit-mode .form-input,
        .edit-mode .form-select {
            background-color: white;
            border-color: #ccc;
        }

        .edit-mode .form-input:focus,
        .edit-mode .form-select:focus {
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 3px var(--primary-dark-transparent);
        }

        /* Custom Select Box Styles */
        .select-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .error{
            display: none;
            color:red; 
            width:100%;
            font-size:15px;
            margin: 5px;
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            background-color: #f9f9f9;
            appearance: none;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #333;
        }

        .form-select:focus {
            border-color: #FAC01F;
            outline: none;
            box-shadow: 0 0 0 3px rgba(250, 192, 31, 0.2);
        }

        .select-wrapper::after {
            content: "";
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23F83900' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        /* Custom Dropdown Options */
        .custom-options {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            z-index: 100;
            margin-top: 5px;
            border: 1px solid #eee;
            animation: fadeIn 0.2s ease-out;
        }

        .custom-options.show {
            display: block;
        }

        .custom-option {
            padding: 12px 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .custom-option:hover {
            background-color: rgba(250, 192, 31, 0.1);
            color: #F83900;
        }

        .custom-option.selected {
            background-color: rgba(248, 57, 0, 0.1);
            color: #F83900;
            font-weight: 500;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar styling */
        .custom-options::-webkit-scrollbar {
            width: 6px;
        }

        .custom-options::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 0 8px 8px 0;
        }

        .custom-options::-webkit-scrollbar-thumb {
            background: #FAC01F;
            border-radius: 3px;
        }

        .custom-options::-webkit-scrollbar-thumb:hover {
            background: #F83900;
        }
        .notice{
            color: red;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="return">
                <svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z"/>
                </svg>
                <div class="edit-toggle">
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23.3333 7.29182L27.7083 11.6668M29.7281 9.60328C30.3025 9.02892 30.6252 8.24992 30.6252 7.43766C30.6252 6.62539 30.3025 5.84639 29.7281 5.27203C29.1538 4.69767 28.3748 4.375 27.5625 4.375C26.7502 4.375 25.9712 4.69767 25.3969 5.27203L13.125 17.5002V21.8752H17.5L29.7281 9.60328Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <?php

                if(empty($row['user_photo'])){
                    $src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzlxMKFGG-AjkcXZT4qWLD9xjdJVU5akjuxw&s";
                }else{
                    $src = './assets/images/users/'.$row['user_photo'];
                }
            ?>
            <div class="profile-section">
                <div class="img-container">
                    <img id="preview" class="profile-img" src="<?php echo $src; ?>" alt="Profile Image">
                    <div class="select-img" onclick="uploadfile()">
                        <svg width="28" height="22" viewBox="0 0 31 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26.2918 3.24984H23.8467L22.3051 1.70817C21.4125 0.815546 19.8461 0.166504 18.5835 0.166504H12.4168C11.1542 0.166504 9.58787 0.815546 8.69525 1.70817L7.15358 3.24984H4.7085C2.15858 3.24984 0.0834961 5.32492 0.0834961 7.87484V20.2082C0.0834961 22.7581 2.15858 24.8332 4.7085 24.8332H26.2918C28.8417 24.8332 30.9168 22.7581 30.9168 20.2082V7.87484C30.9168 5.32492 28.8417 3.24984 26.2918 3.24984ZM15.5002 18.6665C14.0689 18.6663 12.6963 18.0975 11.6844 17.0853C10.6725 16.0731 10.1041 14.7004 10.1043 13.2691C10.1045 11.8379 10.6733 10.4653 11.6855 9.45338C12.6977 8.44146 14.0704 7.87309 15.5017 7.87329C16.933 7.8735 18.3055 8.44227 19.3175 9.45447C20.3294 10.4667 20.8977 11.8394 20.8975 13.2707C20.8973 14.7019 20.3286 16.0745 19.3164 17.0864C18.3042 18.0983 16.9314 18.6667 15.5002 18.6665ZM24.7502 11.4191C24.487 11.4191 24.2264 11.3673 23.9832 11.2666C23.74 11.1659 23.5191 11.0182 23.333 10.8321C23.1469 10.646 22.9993 10.4251 22.8986 10.1819C22.7978 9.93877 22.746 9.67815 22.746 9.41496C22.746 9.15177 22.7978 8.89116 22.8986 8.648C22.9993 8.40484 23.1469 8.18391 23.333 7.9978C23.5191 7.8117 23.74 7.66407 23.9832 7.56335C24.2264 7.46263 24.487 7.4108 24.7502 7.4108C25.2817 7.4108 25.7915 7.62195 26.1673 7.9978C26.5432 8.37366 26.7543 8.88342 26.7543 9.41496C26.7543 9.9465 26.5432 10.4563 26.1673 10.8321C25.7915 11.208 25.2817 11.4191 24.7502 11.4191Z" fill="var(--primary-dark)"/>
                        </svg>
                        <input type="file" name="" id="fileInput" style="display:none">
                    </div>
                </div>
                
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo $row['fname'].' '.$row['lname']; ?> (<?php echo $row['role'] ?>)</h1>
                    <div class="profile-meta">
                        <div class="meta-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 11.5C11.337 11.5 10.7011 11.2366 10.2322 10.7678C9.76339 10.2989 9.5 9.66304 9.5 9C9.5 8.33696 9.76339 7.70107 10.2322 7.23223C10.7011 6.76339 11.337 6.5 12 6.5C12.663 6.5 13.2989 6.76339 13.7678 7.23223C14.2366 7.70107 14.5 8.33696 14.5 9C14.5 9.3283 14.4353 9.65339 14.3097 9.95671C14.1841 10.26 13.9999 10.5356 13.7678 10.7678C13.5356 10.9999 13.26 11.1841 12.9567 11.3097C12.6534 11.4353 12.3283 11.5 12 11.5ZM12 2C10.1435 2 8.36301 2.7375 7.05025 4.05025C5.7375 5.36301 5 7.14348 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 7.14348 18.2625 5.36301 16.9497 4.05025C15.637 2.7375 13.8565 2 12 2Z"/>
                            </svg>
                            <span><?php echo $row['place']; ?></span>
                        </div>
                        <div class="meta-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 14C7.71667 14 7.47933 13.904 7.288 13.712C7.09667 13.52 7.00067 13.2827 7 13C6.99933 12.7173 7.09533 12.48 7.288 12.288C7.48067 12.096 7.718 12 8 12C8.282 12 8.51967 12.096 8.713 12.288C8.90633 12.48 9.002 12.7173 9 13C8.998 13.2827 8.902 13.5203 8.712 13.713C8.522 13.9057 8.28467 14.0013 8 14ZM12 14C11.7167 14 11.4793 13.904 11.288 13.712C11.0967 13.52 11.0007 13.2827 11 13C10.9993 12.7173 11.0953 12.48 11.288 12.288C11.4807 12.096 11.718 12 12 12C12.282 12 12.5197 12.096 12.713 12.288C12.9063 12.48 13.002 12.7173 13 13C12.998 13.2827 12.902 13.5203 12.712 13.713C12.522 13.9057 12.2847 14.0013 12 14ZM16 14C15.7167 14 15.4793 13.904 15.288 13.712C15.0967 13.52 15.0007 13.2827 15 13C14.9993 12.7173 15.0953 12.48 15.288 12.288C15.4807 12.096 15.718 12 16 12C16.282 12 16.5197 12.096 16.713 12.288C16.9063 12.48 17.002 12.7173 17 13C16.998 13.2827 16.902 13.5203 16.712 13.713C16.522 13.9057 16.2847 14.0013 16 14ZM5 22C4.45 22 3.97933 21.8043 3.588 21.413C3.19667 21.0217 3.00067 20.5507 3 20V6C3 5.45 3.196 4.97934 3.588 4.588C3.98 4.19667 4.45067 4.00067 5 4H6V3C6 2.71667 6.096 2.47934 6.288 2.288C6.48 2.09667 6.71733 2.00067 7 2C7.28267 1.99934 7.52033 2.09534 7.713 2.288C7.90567 2.48067 8.00133 2.718 8 3V4H16V3C16 2.71667 16.096 2.47934 16.288 2.288C16.48 2.09667 16.7173 2.00067 17 2C17.2827 1.99934 17.5203 2.09534 17.713 2.288C17.9057 2.48067 18.0013 2.718 18 3V4H19C19.55 4 20.021 4.196 20.413 4.588C20.805 4.98 21.0007 5.45067 21 6V20C21 20.55 20.8043 21.021 20.413 21.413C20.0217 21.805 19.5507 22.0007 19 22H5ZM5 20H19V10H5V20Z"/>
                            </svg>
                            <span>Joined on 
                                <?php 
                                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['date']) ?: DateTime::createFromFormat('Y-m-d', $row['date']);
                                    echo $date->format('d-M-Y');
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <span class="notice">*Note : Complete your full profile to work as a match official.</span>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">FIRST NAME</label>
                    <input type="text" id="fname" class="form-input" value="<?php echo $row['fname']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">LAST NAME</label>
                    <input type="text" id="lname" class="form-input" value="<?php echo $row['lname']; ?>" disabled>
                </div>
            </div>
            <div id="error-name" class="error"></div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">EMAIL</label>
                    <input type="email" id="email" class="form-input" value="<?php echo $row['email']; ?>" disabled>
                </div>
            </div>
            <div id="error-email" class="error"></div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">GENDER</label>
                    <select class="form-select" id="gender" disabled>
                        <option value="" <?php if (empty($row['gender'])) echo 'selected'; ?>>NULL</option>
                        <option value="Male" <?php if ($row['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($row['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if ($row['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">CONTACT NO.</label>
                    <input type="tel" id="contact" class="form-input" value="<?php echo $row['phone']; ?>" disabled>
                </div>
            </div>
            <div id="error-phone" class="error"></div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ROLE</label>
                    <select class="form-select" id="role" disabled>
                        <option value="Admin" <?php if ($_SESSION['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                        <option value="User" <?php if ($_SESSION['role'] == 'user') echo 'selected'; ?>>User</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">PLACE</label>
                    <input type="text" id="place" class="form-input" value="<?php echo $row['place']; ?>" disabled>
                </div>
            </div>
        </div>
        
        <div class="actions-section">
            <button class="action-btn">Change Password</button>
            <button class="action-btn1" onclick='window.location.href="./support.php"'>Contact</button>
            
            <div style="text-align: center;margin-top: 30px;display: flex;flex-direction: column;">
                <button class="save-btn">SAVE CHANGES</button>
                <button class="logout-btn">LOGOUT</button>
                <a class="delete-account" onclick="">DELETE ACCOUNT</a>
            </div>
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
        let user_id = <?php echo json_encode($user_id) ?>;
        // Your existing JavaScript remains unchanged
        const toggle = document.querySelector(".edit-toggle");
        let edit = false;
        const inputs = document.querySelectorAll("input, select");
        const save_btn = document.querySelector(".save-btn");
        inputs.forEach(el => {
                    el.disabled = true;
                    el.style.background = "none";
                });
        const img = document.querySelector(".select-img");
        img.style.display = "none";
        save_btn.style.display = 'none';


        let uploadfile = () => {
            document.getElementById("fileInput").click();
        }

        document.getElementById('fileInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const imgPreview = document.getElementById('preview');
                imgPreview.src = URL.createObjectURL(file);
                console.log(imgPreview.src)
                imgPreview.style.display = 'block'; // Show the image
            }
        });

        let goBack = ()=>{
            window.history.back();
        }

        toggle.addEventListener("click",() => {
            edit = edit? false : true;

            if (edit)
            {
                toggle.innerHTML = '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.3227 15.3097L13.125 17.5002V21.8752H17.5L19.6773 19.6906M22.594 16.7593L29.7252 9.60328C30.2996 9.02892 30.6222 8.24992 30.6222 7.43766C30.6222 6.62539 30.2996 5.84639 29.7252 5.27203C29.1508 4.69767 28.3719 4.375 27.5596 4.375C26.7473 4.375 25.9683 4.69767 25.394 5.27203L18.2292 12.412M23.3333 7.29182L27.7083 11.6668M4.375 4.37516L30.625 30.6252" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
                inputs.forEach(el => {
                    el.disabled = false;
                });
                save_btn.style.display = 'block';
                img.style.display = "flex";
                document.querySelector('.container').classList.add('edit-mode');
            }
            else {
                toggle.innerHTML = '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2085 10.209H8.75016C7.97661 10.209 7.23475 10.5163 6.68777 11.0633C6.14079 11.6102 5.8335 12.3521 5.8335 13.1257V26.2507C5.8335 27.0242 6.14079 27.7661 6.68777 28.313C7.23475 28.86 7.97661 29.1673 8.75016 29.1673H21.8752C22.6487 29.1673 23.3906 28.86 23.9376 28.313C24.4845 27.7661 24.7918 27.0242 24.7918 26.2507V24.7923" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23.3333 7.29182L27.7083 11.6668M29.7281 9.60328C30.3025 9.02892 30.6252 8.24992 30.6252 7.43766C30.6252 6.62539 30.3025 5.84639 29.7281 5.27203C29.1538 4.69767 28.3748 4.375 27.5625 4.375C26.7502 4.375 25.9712 4.69767 25.3969 5.27203L13.125 17.5002V21.8752H17.5L29.7281 9.60328Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                inputs.forEach(el => {
                    el.disabled = true;
                    el.style.background = "none";
                });
                save_btn.style.display = 'none';
                img.style.display = "none";
                document.querySelector('.container').classList.remove('edit-mode');
            }
        } );

        const popupOverlay = document.getElementById("popupOverlay");
        const popupMessage = document.getElementById("popupMessage");
        const confirmBtn = document.getElementById("confirmBtn");
        const cancelBtn = document.getElementById("cancelBtn");

        // Messages for logout and delete
        const messages = {
        "logout": "Are you sure you want to log out?",
        "del": "Are you sure you want to delete your account?"
        };

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
                }
                console.log(data)})
            .catch(error => console.error(error));
        }

        save_btn.addEventListener("click", () => {
            let fname = document.getElementById("fname").value;
            let lname = document.getElementById("lname").value;
            let email = document.getElementById("email").value;
            let gender = document.getElementById("gender").value;
            let place = document.getElementById("place").value;
            let contact = document.getElementById("contact").value;
            let role = document.getElementById("role").value;
            let image = document.getElementById("fileInput").files[0];
        
            console.log(fname , lname, email, gender, place, contact, role, image);

            let formData = new FormData();

            formData.append('user_id', user_id);
            formData.append('fname', fname);
            formData.append('lname', lname);
            formData.append('email', email);
            formData.append('gender', gender);
            formData.append('place', place);
            formData.append('contact', contact);
            formData.append('role', role);
            formData.append('image', image);

            fetch('./Backend/update-user-profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
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
                }
                if(data.status == 200){
                    window.location.reload();
                }
            })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>