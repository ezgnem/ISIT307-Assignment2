<?php
session_start();
if (!isset($_SESSION["user_email"]) || !isset($_SESSION["user_type"])) {
    header("Location: login.php");
    exit;
}

$userType = $_SESSION["user_type"];

include 'database_connection.php';

$searchConditions = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchName = $_POST["search_name"];
    $searchDescription = $_POST["search_description"];
    $searchCapacity = $_POST["search_capacity"];
    $searchCost = $_POST["search_cost"];
    $searchLateCost = $_POST["search_late_cost"];

    if (!empty($searchName)) {
        $searchConditions[] = "name LIKE '%$searchName%'";
    }
    if (!empty($searchDescription)) {
        $searchConditions[] = "description LIKE '%$searchDescription%'";
    }
    if (!empty($searchCapacity)) {
        $searchConditions[] = "capacity >= $searchCapacity";
    }
    if (!empty($searchCost)) {
        $searchConditions[] = "cost <= $searchCost";
    }
    if (!empty($searchLateCost)) {
        $searchConditions[] = "lateCost <= $searchLateCost";
    }
}

$whereClause = "";
if (!empty($searchConditions)) {
    $whereClause = "WHERE " . implode(" AND ", $searchConditions);
}

$getServicesSQL = "SELECT * FROM services $whereClause";
$servicesResult = $dbconn->query($getServicesSQL);

$dbconn->close();


function isCheckedIn($userEmail, $serviceID) {
    include 'database_connection.php'; // Include your database connection here

    $checkInStatusSQL = "
        SELECT * FROM records
        WHERE userEmail = ? AND serviceID = ? AND checkOutTime IS NULL
    ";

    $stmt = $dbconn->prepare($checkInStatusSQL);
    $stmt->bind_param("si", $userEmail, $serviceID);

    $stmt->execute();
    $result = $stmt->get_result();

    $isCheckedIn = $result->num_rows > 0;

    $stmt->close();
    $dbconn->close();

    return $isCheckedIn;
}

function getCurrentCapacity($serviceID) {
    include 'database_connection.php'; // Include your database connection here

    $countCheckedInSQL = "
        SELECT COUNT(*) AS checkedInCount FROM records
        WHERE serviceID = ? AND checkOutTime IS NULL
    ";

    $stmt = $dbconn->prepare($countCheckedInSQL);
    $stmt->bind_param("i", $serviceID);

    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $currentCapacity = $row['checkedInCount'];

    $stmt->close();
    $dbconn->close();

    return $currentCapacity;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <?php if ($userType === "administrator") { ?>
                <a href="list_all_users.php">List All Users</a> |
            <?php } ?>
            <a href="logout.php">Logout</a>
        </nav>

        <h2>Welcome to the Dashboard, <?php echo $userType; ?></h2>

        <h3>Search Services</h3>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <table>
                <tr>
                    <th>
                        <label for="search_name">Name:</label>
                        <input type="text" id="search_name" name="search_name">
                    </th>
                    <th>
                        <label for="search_description">Description:</label>
                        <input type="text" id="search_description" name="search_description">
                    </th>
                    <th>
                        <label for="search_capacity">Capacity:</label>
                        <input type="number" id="search_capacity" name="search_capacity">
                    </th>
                    <th>
                        <label for="search_cost">Cost (Max):</label>
                        <input type="number" id="search_cost" name="search_cost">
                    </th>
                    <th>
                        <label for="search_late_cost">Late Cost (Max):</label>
                        <input type="number" id="search_late_cost" name="search_late_cost">
                    </th>
                    <th>
                        <input type="submit" value="Search">
                    </th>
                </tr>
            </table>
        </form>

        <h3>Filtered Services</h3>
        <table border="1">
            <tr>
                <th>Service ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Capacity</th>
                <th>Cost</th>
                <th>Late Cost</th>
                <?php if ($userType === "member") { ?>
                    <th>Time</th>
                    <th>Check In/Check Out</th>
                <?php } else if ($userType === "administrator") { ?>
                    <th>Check Users</th>
                <?php } ?>
            </tr>
            <?php
            while ($row = $servicesResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['serviceID'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";
                echo "<td>" . $row['cost'] . "</td>";
                echo "<td>" . $row['lateCost'] . "</td>";

                if ($userType === "member") {
                    if (isCheckedIn($_SESSION['user_email'], $row['serviceID'])) {
                        echo "<form action='check_out.php' method='post'>";
                        echo "<input type='hidden' name='serviceID' value='" . $row['serviceID'] . "'>";
                        echo "<td><input type='datetime-local' name='datetime'></td>";
                        echo "<td><input type='submit' value='Check-Out'></td>";
                        echo "</form>";
                    } else if (getCurrentCapacity($row['serviceID']) < $row['capacity']) {
                        echo "<form action='check_in.php' method='post'>";
                        echo "<input type='hidden' name='serviceID' value='" . $row['serviceID'] . "'>";
                        echo "<td><input type='datetime-local' name='datetime'></td>";
                        echo "<td><input type='submit' value='Check-In'></td>";
                        echo "</form>";
                    }
                } else if ($userType === "administrator") {
                    echo "<form action='list_user_by_service.php' method='post'>";
                    echo "<input type='hidden' name='serviceID' value='" . $row['serviceID'] . "'>";
                    echo "<td><input type='submit' value='Check Users'></td>";
                    echo "</form>";
                }
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
