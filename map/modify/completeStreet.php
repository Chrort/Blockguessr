<?php

function completeStreet($street)
{
    $completedStreet = [];
    $pairs = explode((" "), $street);
    for ($i = 0; $i < count($pairs) - 1; $i++) {
        list($x1, $y1) = explode((","), $pairs[$i]);
        list($x2, $y2) = explode((","), $pairs[$i + 1]);

        $distanceX = $x2 - $x1;
        $distanceY = $y2 - $y1;
        $distance = round(sqrt($distanceX ** 2 + $distanceY ** 2), 2);

        for ($j = 0; $j < $distance; $j++) {
            $distance == 0 ? $newX = $x1 : $newX = round($x1 + $j * $distanceX / $distance, 0);
            $distance == 0 ? $newY = $y1 : $newY = round($y1 + $j * $distanceY / $distance, 0);

            $completedStreet[] = "$newX,$newY";
        }
        $completedStreet[] = "$x2,$y2";
    }
    return implode((" "), $completedStreet);
}
