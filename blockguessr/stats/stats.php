<?php

session_start();
if (!$_SESSION['loggedIn']) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../../config/db_connect.php';
require_once '../../api/games_data.php';
require_once '../../api/highscore_data.php';
require_once '../../api/users_data.php';
require_once '../../api/world_data.php';

$userId = $_SESSION['id'];
$username = $_SESSION['username'];

$highscores = getHighscoreData($conn, $userId, false)[0];

$diffTypes = 0;

$xp = getUserXp($conn, $userId);
$levelData = getUserLevelInfo($xp);

array_shift($highscores);
sort($highscores, SORT_DESC);

$games = getGamesData($conn, $userId);
$worlds = getWorldData($conn, null);

function totalPoints(array $games): int
{
    $total = 0;
    for ($i = 0; $i < count($games); $i++) {
        $total += $games[$i]['score'];
    }
    return $total;
}

function countMedals(array $highscores): array
{
    $medals = [0, 0, 0, 0, 0];
    for ($i = 0; $i < count($highscores); $i++) {
        switch (true) {
            case $highscores[$i] == NULL || $highscores[$i] < 5000:
                $medals[0]++;
                break;
            case $highscores[$i] < 15000:
                $medals[1]++;
                break;
            case $highscores[$i] < 22500:
                $medals[2]++;
                break;
            case $highscores[$i] < 25000:
                $medals[3]++;
                break;
            case $highscores[$i] == 25000:
                $medals[4]++;
                break;
            default:
                $medals[0]++;
        }
    }
    return $medals;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="username" content="<?php echo htmlspecialchars($username) ?>">
    <link rel="stylesheet" href="stats.css">
    <link rel="stylesheet" href="../header/header.css">
    <title>Blockguessr - Statistics</title>
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
</head>

<body>
    <?php require_once '../header/header.php' ?>
    <main>
        <h1>Statistic for <?= $username ?></h1>
        <div id="xp">
            <div id="currentLevel"><abbr title="Level <?= $levelData[0] ?> at <?= 100 * $levelData[0] ** 2 ?>xp"><?= $levelData[0] ?></abbr></div>
            <div id="bar">
                <div id="currentBar" style="width: <?= ($xp - (100 * ($levelData[0]) ** 2)) / $levelData[1] * 100 ?>%"></div>
                <p id="progress"><?= round(($xp - (100 * ($levelData[0]) ** 2)) / $levelData[1] * 100, 2) ?>%</p>
            </div>
            <div id="nextLevel"><abbr title="Level <?= $levelData[0] + 1 ?> at <?= 100 * ($levelData[0] + 1) ** 2 ?>xp"><?= $levelData[0] + 1 ?></abbr></div>
        </div>
        <div id="medals">
            <?php for ($i = count(countMedals($highscores)) - 1; $i > -1; $i--): ?>
                <?php if (countMedals($highscores)[$i] > 0): ?>
                    <?php   ?>
                    <div class="medal medal_<?= $i ?>" style="width: <?= 100 * countMedals($highscores)[$i] / count($highscores) ?>%;"><?= countMedals($highscores)[$i] ?></div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <section id="stats">
            <div>Total Games: <?= count($games) ?></div>
            <div>Total Points: <?= totalPoints($games) ?></div>
            <div>Average Points: <?= count($games) > 0 ? round(totalPoints($games) / count($games), 2) : 0 ?></div>
            <div>Total XP: <?= $xp ?></div>
        </section>
        <section id="recentGames">
            <h2>Recent Games</h2>
            <table id="games">
                <thead>
                    <tr>
                        <th>Map</th>
                        <th>Score</th>
                        <th>Time</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = count($games) - 1; $i >= 0; $i--): ?>
                        <tr>
                            <td><?= getMapNameById($conn, $games[$i]['map_id'])[0] ?></td>
                            <td><?= $games[$i]['score'] ?></td>
                            <td><?= $games[$i]['time'] ?>s</td>
                            <td><?= $games[$i]['played_at'] ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>