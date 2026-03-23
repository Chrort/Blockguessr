<?php

//get labels
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
    $sql = "SELECT * FROM mapstreets ORDER BY $sort $order";
    $streets = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
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
