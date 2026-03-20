<?php

session_start();

$expansion = $_SESSION['expansion'];

header("Content-Type: application");
echo json_encode($expansion);
