<?php

session_start();

require '../../config/db_connect.php';
require '../../api/highscore_data.php';

$totalPoints = $_POST['totalPoints'] ?? 0;
$timePlayed = $_POST['timePlayed'] ?? 0;

if (checkForColumn($conn) == 0) {
    $mapName = $_SESSION['mapName'];

    $conn->query("ALTER TABLE highscores ADD `$mapName` varchar(255)");
}

$highscores = getHighscoreData($conn, $_SESSION["id"], true)[0];

if ($highscores[$_SESSION['mapName']] == NULL || ($highscores[$_SESSION['mapName']] != NULL && $totalPoints > $highscores[$_SESSION['mapName']])) {
    insertNewScore($conn, $totalPoints);
}

insertGameData($conn, $totalPoints, $timePlayed);

function insertNewScore(mysqli $conn, int $score)
{
    $mapName = $_SESSION['mapName'];
    $id = $_SESSION["id"];

    $stmt = $conn->prepare("UPDATE highscores SET `$mapName` = ? WHERE id = ?");
    $stmt->bind_param("ii", $score, $id);
    $stmt->execute();
    $stmt->close();
}

function checkForColumn(mysqli $conn)
{

    $tableName = "highscores";
    $dbName = "dbs14979406";
    $mapName = $_SESSION['mapName'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->bind_param("sss", $dbName, $tableName, $mapName);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_all(MYSQLI_NUM);
    $stmt->close();

    return $count[0][0];
}

function insertGameData(mysqli $conn, int $totalPoints, float $timePlayed)
{
    $player_id = $_SESSION["id"] ?? 0;
    $map_id = $_SESSION["mapId"] ?? 0;

    //add to games table
    $stmt = $conn->prepare("INSERT INTO games (player_id, map_id, score, time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $player_id, $map_id, $totalPoints, $timePlayed);
    $stmt->execute();
    $stmt->close();

    //get current xp from user
    $stmt = $conn->prepare("SELECT xp FROM users WHERE id = ?");
    $stmt->bind_param("i", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $xp = $result->fetch_all(MYSQLI_NUM)[0][0];
    $stmt->close();

    $newXp = $xp + round($totalPoints / 200);
    if ($totalPoints == 25000) $newXp += 50;

    //insert new xp
    $stmt = $conn->prepare("UPDATE users SET xp = ? WHERE id = ?");
    $stmt->bind_param("di", $newXp, $player_id);
    $stmt->execute();
    $stmt->close();
}
