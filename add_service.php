<?php
session_start();
include 'database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $capacity = $_POST["capacity"];
    $cost = $_POST["cost"];
    $lateCost = $_POST["late_cost"];

    // Insert new service into the database
    $insertServiceSQL = "INSERT INTO services (name, description, capacity, cost, lateCost) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbconn->prepare($insertServiceSQL);
    $stmt->bind_param("ssidd", $name, $description, $capacity, $cost, $lateCost);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    $dbconn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Service</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
        </nav>

        <h2>Add Service</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea><br><br>

            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" required><br><br>

            <label for="cost">Cost:</label>
            <input type="number" id="cost" name="cost" step="0.01" required><br><br>

            <label for="late_cost">Late Cost:</label>
            <input type="number" id="late_cost" name="late_cost" step="0.01" required><br><br>

            <input type="submit" value="Add Service">
        </form>
    </div>
</body>
</html>
