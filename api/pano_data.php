<?php

function getPanoData($conn)
{

    $stmt = $conn->prepare("SELECT * FROM panodata");

    $stmt->execute();
    $result = $stmt->get_result();
    $panos = $result->fetch_all(MYSQLI_NUM);
    $stmt->close();

    return $panos;
}
