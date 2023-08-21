<?php
session_start();
include 'database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["serviceID"])) {
    $serviceID = $_POST["serviceID"];
    $datetime = $_POST["datetime"];
    $userEmail = $_SESSION["user_email"];

    // Update the checkOutTime for the corresponding record
    $updateRecordSQL = "UPDATE records SET checkOutTime = ? WHERE userEmail = ? AND serviceID = ? AND checkOutTime IS NULL";
    $stmt = $dbconn->prepare($updateRecordSQL);
    $stmt->bind_param("ssi", $datetime, $userEmail, $serviceID);

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
