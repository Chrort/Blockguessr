<?php

session_start();
if (!$_SESSION['loggedIn']) {
    header("Location: ../login/login.php");
    exit();
}

require '../../config/db_connect.php';
require '../../api/highscore_data.php';

$totalPoints = $_POST['totalPoints'] ?? 0;

if (checkForColumn($conn) == 0) {
    $mapName = $_SESSION['mapName'];

    $conn->query("ALTER TABLE highscores ADD `$mapName` varchar(255)");
}

$highscores = getHighscoreData($conn, $_SESSION["id"])[0];

if ($highscores[$_SESSION['mapName']] == NULL || ($highscores[$_SESSION['mapName']] != NULL && $totalPoints > $highscores[$_SESSION['mapName']])) {
    insertNewScore($conn, $totalPoints);
}

function insertNewScore($conn, $score)
{
    $mapName = $_SESSION['mapName'];
    $id = $_SESSION["id"];

    $stmt = $conn->prepare("UPDATE highscores SET `$mapName` = ? WHERE id = ?");
    $stmt->bind_param("ii", $score, $id);
    $stmt->execute();
    $stmt->close();
}

function checkForColumn($conn)
{

    $tableName = "highscores";
    $dbName = "blockguessr";
    $mapName = $_SESSION['mapName'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->bind_param("sss", $dbName, $tableName, $mapName);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_all(MYSQLI_NUM);
    $stmt->close();

    return $count[0][0];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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