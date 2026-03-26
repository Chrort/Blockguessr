<?php

session_start();
if (!$_SESSION['loggedIn']) {
    header("Location: ../login/login.php");
    exit();
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
$password = $_SESSION['pwd'];
$userId = $_SESSION['id'];
$creationDate = $_SESSION['date'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="username" content="<?php echo htmlspecialchars($username) ?>">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="../header/header.css">
    <title>Blockguessr - Profile</title>
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
</head>

<body>
    <?php require_once '../header/header.php' ?>
    <main>
        <h1>Profile Information</h1>
        <h3>Username: <?php echo htmlspecialchars($username) ?></h3>
        <h3>E-Mail: <?php echo htmlspecialchars($email) ?></h3>
        <h3>Password: <?php echo htmlspecialchars($password) ?></h3>
        <h3>User-Id: <?php echo htmlspecialchars($userId) ?></h3>
        <h3>Profile created: <?php echo htmlspecialchars($creationDate) ?></h3>
        <form action="delete.php" method="post">
            <input type="hidden" name="deleteId" value="<?php echo htmlspecialchars($userId) ?>">
            <button type="submit" name="delete">Delete Account</button>
        </form>
    </main>
</body>

</html>