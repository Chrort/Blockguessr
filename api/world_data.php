<?php

function getWorldData($conn, $id)
{

    if ($id === null) {
        $stmt = $conn->prepare("SELECT * FROM maps ORDER BY name ASC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM maps WHERE id = ?");
        $stmt->bind_param("i", $id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $maps = $result->fetch_all(MYSQLI_NUM);

    return $maps;
}
