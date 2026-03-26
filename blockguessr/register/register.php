<?php
session_start();

$username = $_SESSION["username"] ?? "";
$email = $_SESSION["email"] ?? "";
$pwd = $_SESSION["pwd"] ?? "";
$pwdRepeat = $_SESSION["pwdRepeat"] ?? "";

unset($_SESSION["username"], $_SESSION["email"], $_SESSION["pwd"], $_SESSION["pwdRepeat"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../img/fullServerMap.png" type="image/x-icon">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="../header/header.css">
    <title>BlockGuessr - Register</title>
</head>

<body>
    <header>
        <a href="../login/login.php" id="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" height="85%" viewBox="0 -960 960 960" width="85%" fill="#1f1f1f" style="position: absolute">
                <path d="M400-240 160-480l240-240 56 58-142 142h486v80H314l142 142-56 58Z" />
            </svg>
        </a>BlockGuessr
    </header>
    <main>
        <h1>Register</h1>
        <form action="../../config/register_config.php" method="post">
            <input type="text" name="username" id="username" placeholder="Username..." value="<?php echo htmlspecialchars($username) ?>">
            <input type="email" name="email" id="email" placeholder="E-Mail..." value="<?php echo htmlspecialchars($email) ?>">
            <input type="password" name="pwd" id="pwd" placeholder="Password..." value="<?php echo htmlspecialchars($pwd) ?>">
            <input type="password" name="pwdRepeat" id="pwdRepeat" placeholder="Repeat Password..." value="<?php echo htmlspecialchars($pwdRepeat) ?>">
            <?php if (isset($_SESSION["error"])) {
                $error = $_SESSION["error"];
                echo "<p> $error </p>";
                unset($_SESSION["error"]);
            } ?>
            <button type="submit" name="submit" id="submit">Create Account</button>
        </form>
        <h4>Already have an account? <a href="../login/login.php">Login</a></h4>
    </main>

</body>

</html>