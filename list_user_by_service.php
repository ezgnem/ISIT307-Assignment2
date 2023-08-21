<!DOCTYPE html>
<html>
<head>
    <title>Checked-In Members for Service</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <?php session_start(); ?>
            <?php if ($_SESSION["user_type"] === "administrator") { ?>
                <a href="dashboard.php">Dashboard</a> |
                <a href="list_all_users.php">List All Users</a> |
            <?php } ?>
            <a href="logout.php">Logout</a>
        </nav>

        <?php
        include 'database_connection.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["serviceID"])) {
            $serviceID = $_POST["serviceID"];

            $getCheckedInUsersSQL = "
                SELECT users.name, users.surname, users.email
                FROM records
                INNER JOIN users ON records.userEmail = users.email
                WHERE records.serviceID = ? AND records.checkOutTime IS NULL
            ";

            $stmt = $dbconn->prepare($getCheckedInUsersSQL);
            $stmt->bind_param("i", $serviceID);

            $stmt->execute();
            $result = $stmt->get_result();

            // Display the list of checked-in users
            if ($result->num_rows > 0) {
                echo "<h2>Currently Checked-In Members for Service ID $serviceID</h2>";
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<h3>{$row['name']} {$row['surname']} - {$row['email']}</h3>";
                }
                echo "</ul>";
            } else {
                echo "<h2>No Members Checked-In for Service ID $serviceID</h2>";
            }

            $stmt->close();
        }

        $dbconn->close();
        ?>
    </div>
</body>
</html>
