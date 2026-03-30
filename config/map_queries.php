<?php

//get labels

use Monolog\Handler\PushoverHandler;

function getLabels(mysqli $conn, string $sort = "name", string $order = "ASC"): array
{
    $sql = "SELECT * FROM maplabels ORDER BY $sort $order";
    $elements = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
    return $elements;
}

//get borders
function getBorders(mysqli $conn, string $sort = "name", string $order = "ASC"): array
{
    $sql = "SELECT * FROM mapborders ORDER BY $sort $order";
    $borders = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
    return $borders;
}

//get streets
function getStreets(mysqli $conn, string $sort = "name", string $order = "ASC"): array
{

    $osort = $sort;
    if ($sort == "length") {
        $osort = $sort;
        $sort = "name";
    }

    $sql = "SELECT * FROM mapstreets ORDER BY $sort $order";
    $streets = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);

    for ($i = 0; $i < count($streets); $i++) {
        $length = 0;
        $coords = explode((" "), $streets[$i]['coords']);

        for ($j = 0; $j < count($coords) - 1; $j++) {
            $currentCoordPair = explode((","), $coords[$j]);
            $nextCoordPair = explode((","), $coords[$j + 1]);

            if ($currentCoordPair[0] != $nextCoordPair[0] && $currentCoordPair[1] != $nextCoordPair[1]) {
                $length += 2 ** 0.5;
            } elseif ($currentCoordPair[0] == $nextCoordPair[0] && $currentCoordPair[1] == $nextCoordPair[1]) {
                $length += 0;
            } else {
                $length++;
            }
        }

        $length *= 0.96;

        $streets[$i]['length'] = $length;
    }

    if ($osort == "length") $order == "ASC" ? array_multisort(array_column($streets, 'length'), SORT_ASC, $streets) : array_multisort(array_column($streets, 'length'), SORT_DESC, $streets);

    return $streets;
}

//get polygons
function getPolygons(mysqli $conn, string $sort = "name", string $order = "ASC"): array
{
    $sql = "SELECT * FROM mappolygons ORDER BY $sort $order";
    $polygons = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
    return $polygons;
}

//insert label
function modifyData(mysqli $conn, string $sql): void
{
    if (mysqli_query($conn, $sql)) {
        header("Location: ./modify.php");
    } else {
        echo 'query error: ' . mysqli_error($conn);
    }
}
