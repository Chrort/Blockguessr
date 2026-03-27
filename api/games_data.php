<?php

function getGamesData(mysqli $conn, int $id)
{
    $stmt = $conn->prepare("SELECT * FROM games WHERE player_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $games = $result->fetch_all(MYSQLI_ASSOC);

    return $games;
}

function getGamesDataByMapId(mysqli $conn, int $mapId): array
{
    $stmt = $conn->prepare("SELECT * FROM games WHERE map_id = ? ORDER BY score DESC, time ASC, played_at DESC");
    $stmt->bind_param("i", $mapId);
    $stmt->execute();
    $result = $stmt->get_result();
    $games = $result->fetch_all(MYSQLI_ASSOC);

    return $games;
}
