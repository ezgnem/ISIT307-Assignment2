<?php
session_start();
include 'database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $getUserSQL = "SELECT email, password, type FROM users WHERE email=?";
    $stmt = $dbconn->prepare($getUserSQL);
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_type"] = $user["type"];
            header("Location: dashboard.php");
            exit;
        } else {
            $loginError = "Invalid credentials.";
        }
    } else {
        $loginError = "Email not found.";
    }

    $stmt->close();
}

$dbconn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Sign In</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <nav>
        <a href="register.php">Register</a> |
        <a href="login.php">Login</a>
    </nav>

    <div class="container">
        <h2>User Sign In</h2>
        <?php if (isset($loginError)) { echo "<p class='error'>$loginError</p>"; } ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Sign In">
        </form>
    </div>
</body>
</html>
