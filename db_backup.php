<?php
$host = "127.0.0.1";
$user = "u643166794_livestrike";
$pass = "Livestrike@123";
$db   = "u643166794_livestrike";
$port = "3306";

$backup_db = $db . "_backup"; // Fixed backup name

// Connect to MySQL
$conn = mysqli_connect($host, $user, $pass, '', $port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Drop old backup if exists
mysqli_query($conn, "DROP DATABASE IF EXISTS `$backup_db`");

// Create new backup
if (!mysqli_query($conn, "CREATE DATABASE `$backup_db`")) {
    die("Error creating backup DB: " . mysqli_error($conn));
}

// Select original DB
mysqli_select_db($conn, $db);

// Get all table names
$tables_result = mysqli_query($conn, "SHOW TABLES");
if (!$tables_result) {
    die("Error fetching tables: " . mysqli_error($conn));
}

// Duplicate each table structure and data
while ($row = mysqli_fetch_array($tables_result)) {
    $table = $row[0];

    // Get CREATE TABLE statement
    $create_table_result = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
    $create_table_row = mysqli_fetch_array($create_table_result);
    $create_table_sql = $create_table_row[1];

    // Create table in backup DB
    mysqli_query($conn, "USE `$backup_db`");
    if (!mysqli_query($conn, $create_table_sql)) {
        echo "Error creating table $table: " . mysqli_error($conn) . "<br>";
        continue;
    }

    // Copy data from original
    mysqli_query($conn, "INSERT INTO `$backup_db`.`$table` SELECT * FROM `$db`.`$table`");
}

// Success message
echo "✅ Database '$db' duplicated successfully as '$backup_db'";
?>
