<?php

require_once '../config/db_connect.php';

$sql = "SELECT * FROM maplabels"; //make sql statement
$result = mysqli_query($conn, $sql); //query result
$labels = mysqli_fetch_all($result, MYSQLI_NUM); // fetch result into an array
mysqli_free_result($result); //free result from memory
mysqli_close($conn); //close conn
header("Content-Type: application");
echo json_encode($labels);
