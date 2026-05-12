<?php

require_once '../config/db_connect.php';
require_once '../config/map_queries.php';

$streets = getStreets($conn);

for ($i = 0; $i < count($streets); $i++) {
    var_dump($streets[$i]);
    echo "<br><br><hr><br>";
}
