<?php

$folder = "./img/slideshow";
$count = count(glob($folder . "/*.{jpg}", GLOB_BRACE));

header("Content-Type: application");
echo json_encode($count);
