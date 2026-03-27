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
