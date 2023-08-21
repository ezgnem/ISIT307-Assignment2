<?php
session_start();
if (!isset($_SESSION["user_email"]) || $_SESSION["user_type"] !== "administrator") {
    header("Location: login.php");
    exit;
}

include 'database_connection.php';

// Retrieve all members from the database
$getMembersSQL = "SELECT * FROM users WHERE type = 'member'";
$membersResult = $dbconn->query($getMembersSQL);
?>

<!DOCTYPE html>
<html>
<head>
    <title>List All Users</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <?php if ($_SESSION["user_type"] === "administrator") { ?>
                <a href="dashboard.php">Dashboard</a> | <!-- Update this line -->
                <a href="list_all_users.php">List All Users</a> |
            <?php } ?>
            <a href="logout.php">Logout</a>
        </nav>

        <h2>List All Users</h2>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>
            <?php
            while ($row = $membersResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['surname'] . "</td>";
                echo "<td>" . $row['phone'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
