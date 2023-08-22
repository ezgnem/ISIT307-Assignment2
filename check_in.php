<?php
session_start();
include 'database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["serviceID"])) {
    $serviceID = $_POST["serviceID"];
    $datetime = $_POST["datetime"];
    $intendedUseDuration = $_POST['intendedUseDuration'];
    $userEmail = $_SESSION["user_email"];

    $insertRecordSQL = "INSERT INTO records (userEmail, serviceID, checkInTime, intendedUseDuration) VALUES (?, ?, ?, ?)";
    $stmt = $dbconn->prepare($insertRecordSQL);
    $stmt->bind_param("sisi", $userEmail, $serviceID, $datetime, $intendedUseDuration);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$dbconn->close();
?>
