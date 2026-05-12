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

$displayLog = "none";
$displayLogColor = "green";
$message = "";

if ((isset($_GET["error"]) && $_GET["error"]) || (isset($_GET["succes"]) && $_GET["succes"])) {
    $displayLog = "flex";
    if (isset($_GET["error"]) && $_GET["error"]) $displayLogColor = "red";
    $message = $_GET["message"];
}

function getPwdString(string $password): string
{
    $r = substr($password, 0, 1);

    for ($i = 1; $i < strlen($password) - 1; $i++) {
        $r .= "*";
    }

    $r .= substr($password, strlen($password) - 1, 1);

    return $r;
}

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
        <h3 id="user-log" style="display: <?= $displayLog ?>; color: <?= $displayLogColor ?>;"><?= htmlspecialchars($message) ?></h3>
        <section>
            <div class="info-div">
                <h3>Username</h3>
                <hr>
                <h5><?= htmlspecialchars($username) ?></h5>
            </div>
            <div class="info-div">
                <h3>E-Mail</h3>
                <hr>
                <form action="profile_actions.php" method="get" class="change-form">
                    <input type="hidden" name="changeType" value="email">
                    <input type="hidden" name="userId" value="<?= $userId ?>">
                    <div>
                        <svg xmlns='http://www.w3.org/2000/svg' height='1.2rem' viewBox='0 -960 960 960' width='1.2rem' fill='#000000'>
                            <path d='M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z' />
                        </svg>
                        <input type="email" name="newEmail" id="newEmail" value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <button type="submit" name="change">Change</button>
                </form>
            </div>
            <div class="info-div">
                <h3>Password</h3>
                <hr>
                <form action="profile_actions.php" method="get" class="change-form">
                    <input type="hidden" name="changeType" value="pwd">
                    <input type="hidden" name="userId" value="<?= $userId ?>">
                    <div>
                        <svg xmlns='http://www.w3.org/2000/svg' height='1.2rem' viewBox='0 -960 960 960' width='1.2rem' fill='#000000'>
                            <path d='M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z' />
                        </svg>
                        <input type="text" name="newPwd" id="newPwd" value="<?= htmlspecialchars(getPwdString($password)) ?>">
                    </div>
                    <button type="submit" name="change">Change</button>
                </form>
            </div>
            <div class="info-div">
                <h3>Joined at</h3>
                <hr>
                <div class="info-bottom">
                    <h5><?= htmlspecialchars($creationDate) ?></h5>
                    <form action="delete.php" method="post">
                        <input type="hidden" name="deleteId" value="<?= $userId ?>">
                        <button type="submit" name="delete">Delete Account</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>

</html>