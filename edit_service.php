<?php
session_start();
include 'database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["serviceID"])) {
    $serviceID = $_GET["serviceID"];
    
    // Retrieve the service details
    $getServiceSQL = "SELECT * FROM services WHERE serviceID = ?";
    $stmt = $dbconn->prepare($getServiceSQL);
    $stmt->bind_param("i", $serviceID);
    $stmt->execute();
    $serviceResult = $stmt->get_result();
    $service = $serviceResult->fetch_assoc();
    $stmt->close();

} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["serviceID"])) {
    $serviceID = $_POST["serviceID"];

    // Update service details
    $name = $_POST["name"];
    $description = $_POST["description"];
    $capacity = $_POST["capacity"];
    $cost = $_POST["cost"];
    $lateCost = $_POST["late_cost"];
        
    $updateServiceSQL = "UPDATE services SET name = ?, description = ?, capacity = ?, cost = ?, lateCost = ? WHERE serviceID = ?";
    $stmt = $dbconn->prepare($updateServiceSQL);
    $stmt->bind_param("ssiddi", $name, $description, $capacity, $cost, $lateCost, $serviceID);

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
    <title>Edit Service</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
        </nav>

        <h2>Edit Service</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="serviceID" value="<?php echo $serviceID; ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $service['name']; ?>" required><br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo $service['description']; ?></textarea><br><br>

            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" value="<?php echo $service['capacity']; ?>" required><br><br>

            <label for="cost">Cost:</label>
            <input type="number" id="cost" name="cost" step="0.01" value="<?php echo $service['cost']; ?>" required><br><br>

            <label for="late_cost">Late Cost:</label>
            <input type="number" id="late_cost" name="late_cost" step="0.01" value="<?php echo $service['lateCost']; ?>" required><br><br>

            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>
