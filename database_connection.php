<?php
include 'database_config.php';

$dbconn = new mysqli(
    $databaseConfig['servername'],
    $databaseConfig['username'],
    $databaseConfig['password'],
    $databaseConfig['database']
);

if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}
?>
