<?php

session_start();
if (!$_SESSION['loggedIn']) {
    header("Location: ../login/login.php");
    exit();
}

require '../../config/db_connect.php';
require '../../api/users_data.php';

$id = $_SESSION['id'];
$xp = getUserXp($conn, $id);
$levelData = getUserLevelInfo($xp);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta id="levelData0" content="<?= $levelData[0] ?>">
    <meta id="levelData1" content="<?= $levelData[1] ?>">
    <meta id="userXp" content="<?= $xp ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="summary.css">
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
    <title>Blockguessr - Summary</title>
</head>

<body>
    <main>
        <h1>Game Summary</h1>
        <table>
            <tr>
                <th>Round</th>
                <th>Distance</th>
                <th>Points</th>
            </tr>
            <?php

            for ($i = 0; $i < 5; $i++) {
                echo "<tr id='tr_$i'></tr>";
            }

            ?>

            <tr id='tr_5'>
                <td>Average / Total</td>
                <td id='average'></td>
                <td id='total'></td>
            </tr>
        </table>

        <div id="xp">
            <div id="currentLevel"><?= $levelData[0] ?></div>
            <div id="bar">
                <div id="currentBar" style="width: <?= ($xp - (100 * ($levelData[0]) ** 2)) / $levelData[1] * 100 ?>%"></div>
                <p id="progress"></p>
            </div>
            <div id="nextLevel"><?= $levelData[0] + 1 ?></div>
        </div>

        <div id="exitToStartpage">
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 -960 960 960" width="1em" fill="#1f1f1f">
                <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
            </svg>
            Startpage
        </div>
    </main>
</body>
<script src="summary.js" type="module"></script>

</html>