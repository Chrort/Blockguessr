<?php

require_once(__DIR__ . "/completeStreet.php");

class Street
{
    public function __construct(public $name, public $color, public $coordinates) {}
}

class Node
{
    public function __construct(public $x, public $y) {}
}

function toNodeArray($street)
{
    $result = [];
    $pairs = array_unique(explode(" ", $street));
    foreach ($pairs as $pair) {
        $pair = explode(",", $pair);
        $result[] = new Node((int) $pair[0], (int) $pair[1]);
    }

    return $result;
}

function matchCoords($coords1, array $nodes, $depth)
{
    if (count($coords1) < $depth || count($nodes) < $depth) {
        return false;
    }

    for ($i = 0; $i < $depth; $i++) {
        if ($coords1[$i]["x"] != $nodes[$i]->x || $coords1[$i]["y"] != $nodes[$i]->y) {
            return false;
        }
    }
    return true;
}

function write($streetPHP)
{
    $json = file_get_contents(__DIR__ . "/../streets.json");
    if ($json === false) {
        throw new RuntimeException("Could not read streets.json");
    }
    $data = json_decode($json, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE || !isset($data["streets"]) || !is_array($data["streets"])) {
        throw new RuntimeException("Invalid JSON: " . json_last_error_msg());
    }

    $street = new Street($streetPHP["nameStreet"], $streetPHP["colorStreet"], toNodeArray($streetPHP["coordsStreet"]));
    $data["streets"][] = $street;
    file_put_contents(__DIR__ . "/../streets.json", json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
}

function delete($coords)
{
    $json = file_get_contents(__DIR__ . "/../streets.json");
    $data = json_decode($json, true);
    $streetIndex = find($coords, $data["streets"]);
    if ($streetIndex === null) {
        return false;
    }
    array_splice($data["streets"], $streetIndex, 1);
    file_put_contents(__DIR__ . "/../streets.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return true;
}

function find($coords, $streets, $depth = 5)
{
    while ($depth <= count($coords)) {
        $matches = [];

        foreach ($streets as $index => $street) {
            if (matchCoords($coords, $street["coordinates"], $depth)) {
                $matches[$index] = $street;
            }
        }

        if (count($matches) === 1) {
            return array_key_first($matches);
        }

        if (count($matches) === 0) {
            return null;
        }

        $streets = $matches;
        $depth++;
    }

    return null;
}
