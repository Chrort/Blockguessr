<?php

require_once '../../config/db_connect.php';
require_once '../../api/world_data.php';
require '../../vendor/autoload.php';
require './delete_panos.php';

use Google\Cloud\Storage\StorageClient;

if (isset($_POST['submit'])) {
    session_start();

    deletePanos($_SESSION['username']);

    unset($_SESSION['mapId'], $_SESSION['mapName'], $_SESSION['locations'], $_SESSION['panoramas'], $_SESSION['expansion']);

    $id = $_SESSION['mapId'] = $_POST['id'];

    $worldData = getWorldData($conn, $id);

    $_SESSION['mapName'] = $worldData[0][1];

    $allLocations = explode(",", $worldData[0][2]);

    $_SESSION['expansion'] = getExpansion($conn, $allLocations);

    $locations = [];
    for ($i = 0; $i < 5; $i++) {
        array_push($locations, (int)array_splice($allLocations, rand(0, count($allLocations) - 1), 1)[0]);
    }

    shuffle($locations);

    $_SESSION['mapLocations'] = $locations;

    getPano($conn, $locations);

    header("Location: ./game.php");
    exit();
} else {
    header("Location: ../startpage/startpage.php");
    exit();
}

function getPano($conn, $locations)
{
    $panoramas = [];

    $projectId = 'blockguessr-475818';
    $bucketName = 'panorama-blockguessr-bucket';

    $storage = new StorageClient([
        'projectId' => $projectId,
        'keyFilePath' => '../../api/blockguessr-475818-11356824dac5.json',
    ]);

    $bucket = $storage->bucket($bucketName);

    for ($i = 0; $i < 5; $i++) {
        $stmt = $conn->prepare('SELECT id, folderName, x, y FROM panodata WHERE id = ?');
        $stmt->bind_param("i", $locations[$i]);
        $stmt->execute();
        $result = $stmt->get_result();
        $pano = $result->fetch_assoc();
        array_push($panoramas, $pano);
    }

    foreach ($panoramas as $pano) {

        if (!is_dir(__DIR__ . '/panoramas' . '/' . $_SESSION['username'] . '/' . $pano['folderName'])) {
            mkdir(__DIR__ . '/panoramas' . '/' . $_SESSION['username'] . '/' . $pano['folderName'], 0777, true);
        }

        for ($i = 0; $i < 6; $i++) {
            $object = $bucket->object($pano['folderName'] . "panorama_$i.png");
            $object->downloadToFile(__DIR__ . '/panoramas' . '/' . $_SESSION['username'] . '/' . $pano['folderName'] . "/panorama_$i.png");
        }
    }

    $_SESSION['panoramas'] = $panoramas;
}

function getExpansion($conn, $locations)
{
    $idList = implode(',', array_map('intval', $locations));

    $stmt = $conn->prepare("SELECT x FROM panodata WHERE id IN ($idList) ORDER BY x ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $coords = $result->fetch_all(MYSQLI_ASSOC);

    $maxX = $coords[count($coords) - 1]['x'];
    $minX = $coords[0]['x'];

    $stmt = $conn->prepare("SELECT y FROM panodata WHERE id IN ($idList) ORDER BY y ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $coords = $result->fetch_all(MYSQLI_ASSOC);

    $maxY = $coords[count($coords) - 1]['y'];
    $minY = $coords[0]['y'];

    $line = abs($maxX - $minX) > abs($maxY - $minY) ? abs($maxX - $minX) : abs($maxY - $minY);

    return [sqrt(($maxX - $minX) ** 2 + ($maxY - $minY) ** 2), $minX, $minY, $line, $maxX, $maxY];
}
