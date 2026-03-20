<?php
session_start();
require_once '../config/db_connect.php';

if (!empty($_SESSION['mapLocations'])) {
    $locations = $_SESSION['mapLocations'];

    $stmt = $conn->prepare("SELECT * FROM panodata WHERE id IN (?,?,?,?,?) ORDER BY RAND()");
    $stmt->bind_param("iiiii", ...$locations);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_all(MYSQLI_NUM);
    header("Content-Type: application; charset=utf-8");
    echo json_encode($result);
}
