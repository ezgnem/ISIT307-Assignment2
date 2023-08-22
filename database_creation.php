<?php
include 'database_config.php';

$dbconn = new mysqli(
    $databaseConfig['servername'],
    $databaseConfig['username'],
    $databaseConfig['password']
);

if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

$createDatabaseSQL = "CREATE DATABASE IF NOT EXISTS " . $databaseConfig['database'];
if ($dbconn->query($createDatabaseSQL) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $dbconn->error;
}

$dbconn->close();

include 'database_connection.php';

$useDatabaseSQL = "USE " . $databaseConfig['database'];
if ($dbconn->query($useDatabaseSQL) === TRUE) {
    echo "Using database\n";
} else {
    echo "Error using database: " . $dbconn->error;
}

$createUsersTableSQL = "
    CREATE TABLE IF NOT EXISTS users (
        email VARCHAR(255) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        surname VARCHAR(255) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        password VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL
    )
";

$createServicesTableSQL = "
    CREATE TABLE IF NOT EXISTS services (
        serviceID INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        capacity INT NOT NULL,
        cost DECIMAL(10, 2) NOT NULL,
        lateCost DECIMAL(10, 2) NOT NULL
    )
";

$createRecordsTableSQL = "
    CREATE TABLE IF NOT EXISTS records (
        recordID INT AUTO_INCREMENT PRIMARY KEY,
        userEmail VARCHAR(255) NOT NULL,
        serviceID INT NOT NULL,
        checkInTime DATETIME NOT NULL,
        checkOutTime DATETIME,
        intendedUseDuration INT,
        FOREIGN KEY (userEmail) REFERENCES users(email),
        FOREIGN KEY (serviceID) REFERENCES services(serviceID)
    )
";

if ($dbconn->query($createUsersTableSQL) === TRUE) {
    echo "Users table created successfully\n";
} else {
    echo "Error creating Users table: " . $dbconn->error;
}

if ($dbconn->query($createServicesTableSQL) === TRUE) {
    echo "Services table created successfully\n";
} else {
    echo "Error creating Services table: " . $dbconn->error;
}

if ($dbconn->query($createRecordsTableSQL) === TRUE) {
    echo "Records table created successfully\n";
} else {
    echo "Error creating Records table: " . $dbconn->error;
}

$dbconn->close();

header("Location: login.php");
exit;
?>
