<?php

//get labels
function getLabels(mysqli $conn): array
{
    $sql = "SELECT * FROM maplabels ORDER BY name ASC";
    $elements = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
    return $elements;
}

//get borders
function getBorders(mysqli $conn): array
{
    $sql = "SELECT * FROM mapborders ORDER BY name ASC";
    $borders = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
    return $borders;
}

//get streets
function getStreets(mysqli $conn): array
{
    $sql = "SELECT * FROM mapstreets ORDER BY name ASC";
    $streets = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
    return $streets;
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
