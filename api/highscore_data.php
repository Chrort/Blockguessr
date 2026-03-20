<?php

function getHighscoreData($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM highscores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $scores = $result->fetch_all(MYSQLI_ASSOC);

    return $scores;
}
