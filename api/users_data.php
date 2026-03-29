<?php

function getUsernameById(mysqli $conn, int $id)
{

    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $name = $result->fetch_all(MYSQLI_NUM);

    return $name[0][0];
}

function getUserXp(mysqli $conn, int $id)
{
    $stmt = $conn->prepare("SELECT xp FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $name = $result->fetch_all(MYSQLI_NUM);

    return $name[0][0];
}

function getUserLevelInfo(int $xp)
{
    (int)$level = floor(0.1 * ($xp) ** 0.5);

    $xpToNext = ((100 * ($level + 1) ** 2) - (100 * ($level) ** 2));

    return [$level, $xpToNext];
}
