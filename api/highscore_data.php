<?php

function getHighscoreData(mysqli $conn, int $id, bool $assoc)
{
    $stmt = $conn->prepare("SELECT * FROM highscores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($assoc) $scores = $result->fetch_all(MYSQLI_ASSOC);
    if (!$assoc) $scores = $result->fetch_all(MYSQLI_NUM);

    return $scores;
}
