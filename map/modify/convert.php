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

/**
 * @param Node[] $nodes
 */
function matchCoords($coords1, array $nodes, $depth)
{
    $count = 0;
    for ($i = 0; $i < $depth; $i++) {
        if ($coords1[$i]["x"] == $nodes[$i]->x && $coords1[$i]["y"] == $nodes[$i]->y) {
            $count++;
        }
    }
    return $count == $depth;
}

function write($streetPHP)
{
    $json = file_get_contents(__dIR__ . "/../streets.json");
    $data = json_decode($json, true);

    $street = new Street($streetPHP["nameStreet"], $streetPHP["colorStreet"], toNodeArray($streetPHP["coordsStreet"]));
    $data["streets"][] = $street;
    file_put_contents(__DIR__ . "/../streets.json", json_encode($data, JSON_PRETTY_PRINT));
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
    $res = [];
    $size = count($streets);
    for ($i = 0; $i < $size; $i++) {
        if (matchCoords($streets[$i]["coordinates"], $coords, $depth)) {
            $res[$i] = $streets[$i];
        }
    }
    if (count($res) == 1) {
        return array_key_first($res);
    }
    if ($depth == count($res) || count($res) == 0) {
        return null;
    }
    return find($coords, $res, $depth + 1);
}
