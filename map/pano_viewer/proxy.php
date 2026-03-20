<?php
$file = $_GET['file'];
$url = "https://storage.googleapis.com/panorama-blockguessr-bucket/" . rawurlencode($file);

header("Content-Type: image/png");
header("Cache-Control: no-cache, no-store, must-revalidate");
echo file_get_contents($url);
