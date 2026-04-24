<?php

session_start();

require __DIR__ . '/../../config/login_queries.php';
require __DIR__ . '/../../config/db_connect.php';

if (!$_SESSION['loggedIn'] || getUserData($_SESSION['username'], $conn)[0][6] != "admin") {
    header("Location: ../login/login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="username" content="<?php echo htmlspecialchars($username) ?>">
    <link rel="stylesheet" href="../header/header.css">
    <link rel="stylesheet" href="./achievment.css">
    <title>Blockguessr - Achievments</title>
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
</head>

<body>
    <?php require_once '../header/header.php' ?>
    <main>
        <h1>Achievments of <?= $username ?></h1>
    </main>
</body>

</html>