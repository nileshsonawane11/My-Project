<?php
    error_reporting(1);
    session_start();
    include '../config.php';

    $user_id = $_POST['user_id'] ?? '';
    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $gender = $_POST['gender'] ?? null;
    $role = $_POST['role'] ?? '';
    $place = $_POST['place'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $image = $_FILES['image'] ?? null ;

    if($fname == '' || $lname == ''){
        echo json_encode(['status' => 400, 'message' => 'Name Must be required', 'field' => 'name']);
        exit();
    }else if($email == ''){
        echo json_encode(['status' => 400, 'message' => 'Email Must be required', 'field' => 'email']);
        exit();
    }else if($contact == ''){
        echo json_encode(['status' => 400, 'message' => 'Contact Must be required', 'field' => 'email']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 409, 'message' => 'Enter Valid Email', 'field' => 'email']);
        exit();
    }

    $contact = trim($contact);
    $contact = preg_replace('/\D/', '', $contact);
    if (!preg_match('/^[0-9]{10}$/', $contact)) {
        echo json_encode(['status' => 409, 'message' => 'Enter Valid Phone Number', 'field' => 'phone']);
        exit();
    }

    $sql1 = "SELECT * FROM users WHERE email='$email' OR phone='$contact'";
    $result1 = mysqli_query($conn, $sql1);

    if (mysqli_num_rows($result1) > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
            if ($row['user_id'] != $user_id) {
                if ($row['email'] === $email && $row['email']) {
                    echo json_encode(['status' => 409, 'message' => 'Email already Used', 'field' => 'email']);
                    exit();
                }
            }
        }
    }

    if(strlen($contact)>10 or strlen($contact)<10){
        echo json_encode(['status' => 409, 'message' => 'Phone must be 10 digits long', 'field' => 'phone']);
        exit();
    }

    $image_file = ''; // default empty

        if (isset($image['name']) && $image['name'] != '') {
            $image_name = $user_id . '-' . basename($image['name']);
            $image_path = '../assets/images/users/' . $image_name;
            move_uploaded_file($image['tmp_name'], $image_path);
            $image_file = $image_name;
        }

    
    $_SESSION['role'] = $role;
    // Assume $conn is your active MySQLi connection

    if (!empty($image_file)) {

        // If new image is uploaded
        $stmt = $conn->prepare("UPDATE users SET email=?, lname=?, fname=?, role=?, gender=?, phone=?, place=?, user_photo=? WHERE user_id=?");

        if ($stmt) {
            $stmt->bind_param("sssssssss", $email, $lname, $fname, $role, $gender, $contact, $place, $image_file, $user_id);
        }
        
    } else {
        
        // No new image
        $stmt = $conn->prepare("UPDATE users SET email=?, lname=?, fname=?, role=?, gender=?, phone=?, place=? WHERE user_id=?");

        if ($stmt) {
            $stmt->bind_param("ssssssss", $email, $lname, $fname, $role, $gender, $contact, $place, $user_id);
        }
    }

    if ($stmt) {
        if ($stmt->execute()) {
            echo json_encode(["status" => 200, "message" => "Profile updated successfully.", "field" => "success"]);
        } else {
            echo json_encode(["status" => 400, "message" => "Execution failed: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => 400, "message" => "Statement preparation failed: " . $conn->error]);
    }


?>