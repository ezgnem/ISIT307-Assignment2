<?php
include 'database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $type = $_POST["type"];

    // Check if the email already exists in the database
    $checkEmailSQL = "SELECT email FROM users WHERE email=?";
    $stmtCheckEmail = $dbconn->prepare($checkEmailSQL);
    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $stmtCheckEmail->store_result();

    if ($stmtCheckEmail->num_rows > 0) {
        $registrationError = "Email already exists. Please choose a different email.";
    } else {
        // Hash the password before storing (you should use a secure hashing mechanism)
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertUserSQL = "
            INSERT INTO users (email, name, surname, phone, password, type)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $dbconn->prepare($insertUserSQL);
        $stmt->bind_param("ssssss", $email, $name, $surname, $phone, $password, $type);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $stmtCheckEmail->close();
}

$dbconn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <nav>
        <a href="register.php">Register</a> |
        <a href="login.php">Login</a>
    </nav>

    <div class="container">
        <h2>User Registration</h2>
        <?php if (isset($registrationError)) { echo "<p class='error'>$registrationError</p>"; } ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="surname">Surname:</label>
            <input type="text" id="surname" name="surname" required><br><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="type">User Type:</label>
            <select id="type" name="type">
                <option value="member">Member</option>
                <option value="administrator">Administrator</option>
            </select><br><br>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>

