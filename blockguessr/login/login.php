<?php
session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
    header("Location: ../startpage/startpage.php");
    exit();
}

$username = $_SESSION["username"] ?? "";
$pwd = $_SESSION["pwd"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../img/fullServerMap.png" type="image/x-icon">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="../header/header.css">
    <title>BlockGuessr - Login</title>
    <link rel="icon" type="image/x-icon" href="../../img/favicon.png">
</head>

<body>
    <header>
        <a href="../../index.php" id="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" height="85%" viewBox="0 -960 960 960" width="85%" fill="#1f1f1f" style="position: absolute">
                <path d="M400-240 160-480l240-240 56 58-142 142h486v80H314l142 142-56 58Z" />
            </svg>
        </a>BlockGuessr
    </header>
    <main>
        <h1>Login</h1>
        <form action="../../config/login_config.php" method="post">
            <input type="text" name="username" id="username" placeholder="Username..." value="<?php echo htmlspecialchars($username) ?>">
            <input type="password" name="pwd" id="pwd" placeholder="Password..." value="<?php echo htmlspecialchars($pwd) ?>">
            <?php if (isset($_SESSION["error"])) {
                $error = $_SESSION["error"];
                echo "<p> $error </p>";
                unset($_SESSION["error"]);
            } ?>
            <input type="submit" name="submit" id="submit" value="Login">
        </form>
        <h4>Don't have an account? <a href="../register/register.php">Register</a></h4>
    </main>

</body>

</html>

<?php
unset($_SESSION["username"], $_SESSION["email"], $_SESSION["pwd"]);
?>