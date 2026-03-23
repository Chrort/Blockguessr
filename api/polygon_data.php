<?php

require_once '../config/db_connect.php';

$sql = "SELECT * FROM mappolygons";
$result = mysqli_query($conn, $sql);
$polygons = mysqli_fetch_all($result, MYSQLI_NUM);
mysqli_free_result($result);
mysqli_close($conn);
header("Content-Type: application");
echo json_encode($polygons);
