<?php
session_start();
if (!isset($_SESSION["user_email"]) || $_SESSION["user_type"] !== "administrator") {
    header("Location: login.php");
    exit;
}

include 'database_connection.php';

// Handle user search
$searchQuery = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search_query"])) {
    $searchQuery = $_POST["search_query"];
    $searchMembersSQL = "
        SELECT * FROM users
        WHERE type = 'member' AND (
            name LIKE '%$searchQuery%'
            OR surname LIKE '%$searchQuery%'
            OR phone LIKE '%$searchQuery%'
            OR email LIKE '%$searchQuery%'
        )
    ";
} else {
    // Retrieve all members from the database
    $searchMembersSQL = "SELECT * FROM users WHERE type = 'member'";
}

$membersResult = $dbconn->query($searchMembersSQL);
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
                <a href="dashboard.php">Dashboard</a> |
            <?php } ?>
            <a href="logout.php">Logout</a>
        </nav>

        <h2>List All Users</h2>
        <form action="" method="post">
            <label for="search_query">Search Users:</label>
            <input type="text" id="search_query" name="search_query" value="<?php echo $searchQuery; ?>">
            <input type="submit" value="Search">
        </form>

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
