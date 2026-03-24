<?php

session_start();

require_once '../config/db_connect.php';
require_once '../config/map_queries.php';
require_once '../api/pano_data.php';
require_once '../api/get_pwd.php';

$incorrectPwd = false;
$_SESSION["modify"] = $_SESSION["modify"] ?? false;

if (isset($_POST['pwd'])) {
    if ($_POST['enteredPwd'] == getPwd()) {
        $_SESSION["modify"] = true;
        header("Location: ./modify/modify.php");
        exit;
    } else {
        $incorrectPwd = true;
    }
}

//get borders

$borders = getBorders($conn);
$panoramas = getPanoData($conn);
$streets = getStreets($conn);
$polygons = getPolygons($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="wrongPwd" id="wrongPwd" content="<?= $incorrectPwd ?>">
    <link rel="shortcut icon" href="../img/fullServerMap.png" type="image/x-icon">
    <link rel="stylesheet" href="map.css">
    <title>Blockguessr - Map</title>
</head>

<body>
    <header>
        <a href="../index.php" id="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" height="85%" viewBox="0 -960 960 960" width="85%" fill="#1f1f1f" style="position: absolute">
                <path d="M400-240 160-480l240-240 56 58-142 142h486v80H314l142 142-56 58Z" />
            </svg>
        </a>BlockGuessr
        <svg id="menu" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#000000" style="position: absolute">
            <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
        </svg>
    </header>
    <main>
        <div id="map">
            <canvas id="measureCanvas" width="5120" height="5120"></canvas>
            <div class="cleanBorder" id="cB1"></div>
            <div class="cleanBorder" id="cB2"></div>
            <div class="cleanBorder" id="cB3"></div>
            <div class="cleanBorder" id="cB4"></div>
            <div id="streetLabelDivContainer"></div>
            <div id="panoramaDivContainer" style="display: none;">
                <?php

                for ($i = 0; $i < sizeof($panoramas); $i++) {
                    $panoId = $panoramas[$i][0];
                    $x = (int)$panoramas[$i][2] + 6 * 512 . "px";
                    $y = (int)$panoramas[$i][3] + 6 * 512 . "px";

                    $url = "./pano_viewer/pano_viewer.php?id=" . urldecode($panoId) . "&folderName=" . urldecode($panoramas[$i][1]) . "&x=" . urldecode($panoramas[$i][2]) . "&y=" . urldecode($panoramas[$i][3]) . "&province=" . urldecode($panoramas[$i][4]) . "&uploaded_at=" . urldecode($panoramas[$i][5]);

                    echo "<a href='$url' id='panoId_$panoId' class='panoLink' style='top: $y; left: $x;' target='_blank'></a>";
                }

                ?>
            </div>
            <svg viewBox="0 0 5120 5120" xmlns="http://www.w3.org/2000/svg" stroke="blue" class="borderSvg">
                <defs>
                    <pattern id="stripesNp" width="0.0001" height="0.005" patternUnits="objectBoundingBox" patternTransform="rotate(45)">
                        <rect width="10" height="0.01" fill="#CBBA9F" stroke="#CBBA9F" stroke-opacity="0.7"></rect>
                    </pattern>
                </defs>
                <?php
                for ($i = 0; $i < sizeof($borders); $i++) {
                    $pairs = explode((" "), $borders[$i]['coords']);
                    $transformedPairs = [];

                    foreach ($pairs as $pair) {
                        list($x, $y) = explode(",", $pair);

                        $x = (int)$x + 6 * 512;
                        $y = (int)$y + 6 * 512;

                        $transformedPairs[] = "$x,$y";
                    }

                    $transformedCoords = implode(" ", $transformedPairs);
                    echo "<polyline points='{$transformedCoords}' class='borderPolyline' fill='none' stroke='red' />";
                }
                ?>
                <?php
                for ($i = 0; $i < sizeof($streets); $i++) {
                    $stroke = $streets[$i]['color'];
                    $pairs = explode((" "), $streets[$i]['coords']);
                    $transformedPairs = [];

                    foreach ($pairs as $pair) {
                        list($x, $y) = explode(",", $pair);

                        $x = (int)$x + 6 * 512;
                        $y = (int)$y + 6 * 512;

                        $transformedPairs[] = "$x,$y";
                    }

                    $transformedCoords = implode(" ", $transformedPairs);
                    echo "<polyline points='{$transformedCoords}' class='streetPolyline' fill='none' stroke='{$stroke}' />";
                }
                ?>
                <?php
                for ($i = 0; $i < sizeof($polygons); $i++) {
                    $type = $polygons[$i]['type'];
                    $name = $polygons[$i]['name'];

                    if ($type == "np") {
                        $stroke = "#CBBA9F";
                        $strokeOpacity = "0.7";
                        $fill = "url(#stripesNp)";
                        $fillOpacity = "0.7";
                    }

                    $pairs = explode((" "), $polygons[$i]['coords']);
                    $transformedPairs = [];

                    foreach ($pairs as $pair) {

                        list($x, $y) = explode(",", $pair);

                        $x = (int)$x + 6 * 512;
                        $y = (int)$y + 6 * 512;

                        $transformedPairs[] = "$x,$y";
                    }

                    $transformedCoords = implode(" ", $transformedPairs);
                    echo "<polygon points='{$transformedCoords}' class='{$type}_polygons' fill='{$fill}' fill-opacity='{$fillOpacity}' stroke='{$stroke}' stroke-opacity='{$strokeOpacity}'/>";
                }
                ?>
            </svg>
        </div>
        <div id="settingsContainer">
            <div>
                <input type="checkbox" name="showLabel" id="province" value="provinces" class="inputCheckbox" checked>
                <label for="province" class="labelCheckbox">Province labels</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="town" value="towns" class="inputCheckbox" checked>
                <label for="town" class="labelCheckbox">Town labels</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="landscape" value="landscape" class="inputCheckbox" checked>
                <label for="landscape" class="labelCheckbox">Landscape labels</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="waters" value="waters" class="inputCheckbox" checked>
                <label for="waters" class="labelCheckbox">Water labels</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="point" value="points" class="inputCheckbox" checked>
                <label for="point" class="labelCheckbox">Point labels</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="nationalParks" value="nationalParks" class="inputCheckbox" checked>
                <label for="nationalParks" class="labelCheckbox">National Parks</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="street" value="streets" class="inputCheckbox" checked>
                <label for="street" class="labelCheckbox">Street labels</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="streetLine" value="streetLines" class="inputCheckbox">
                <label for="streetLine" class="labelCheckbox">Street lines</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="border" value="borders" class="inputCheckbox" checked>
                <label for="border" class="labelCheckbox">Borders</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="mapTile" value="mapTiles" class="inputCheckbox" checked>
                <label for="mapTile" class="labelCheckbox">Map tiles</label>
            </div>
            <div>
                <input type="checkbox" name="showLabel" id="panorama" value="panoramas" class="inputCheckbox">
                <label for="panorama" class="labelCheckbox">Coverage</label>
            </div>
            <hr>
            <div id="mapTypes">
                <img id="maps" src="../img/daymap.jpg" alt="daymap">
                <img id="nightMaps" src="../img/nightmap.jpg" alt="nightmap">
                <img id="terrainMaps" src="../img/terrainmap.jpg" alt="terrainmap">
                <img id="biomeMaps" src="../img/biomemap.jpg" alt="biomemap">
            </div>
            <hr>
            <div>
                <div class="labelCheckbox" id="measureDistance">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1.1em" viewBox="0 -960 960 960" width="1.1em" fill="#0000F5">
                        <path d="M120-240q-33 0-56.5-23.5T40-320q0-33 23.5-56.5T120-400h10.5q4.5 0 9.5 2l182-182q-2-5-2-9.5V-600q0-33 23.5-56.5T400-680q33 0 56.5 23.5T480-600q0 2-2 20l102 102q5-2 9.5-2h21q4.5 0 9.5 2l142-142q-2-5-2-9.5V-640q0-33 23.5-56.5T840-720q33 0 56.5 23.5T920-640q0 33-23.5 56.5T840-560h-10.5q-4.5 0-9.5-2L678-420q2 5 2 9.5v10.5q0 33-23.5 56.5T600-320q-33 0-56.5-23.5T520-400v-10.5q0-4.5 2-9.5L420-522q-5 2-9.5 2H400q-2 0-20-2L198-340q2 5 2 9.5v10.5q0 33-23.5 56.5T120-240Z" />
                    </svg>Measure Distance
                </div>
            </div>
            <div>
                <div class="labelCheckbox" id="measureArea">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1.1em" viewBox="0 -960 960 960" width="1.1em" fill="#0000F5">
                        <path d="M200-80q-50 0-85-35t-35-85q0-39 22.5-69.5T160-313v-334q-35-13-57.5-43.5T80-760q0-50 35-85t85-35q39 0 69.5 22.5T313-800h334q12-35 42.5-57.5T760-880q50 0 85 35t35 85q0 40-22.5 70.5T800-647v334q35 13 57.5 43.5T880-200q0 50-35 85t-85 35q-39 0-69.5-22.5T647-160H313q-13 35-43.5 57.5T200-80Zm0-640q17 0 28.5-11.5T240-760q0-17-11.5-28.5T200-800q-17 0-28.5 11.5T160-760q0 17 11.5 28.5T200-720Zm560 0q17 0 28.5-11.5T800-760q0-17-11.5-28.5T760-800q-17 0-28.5 11.5T720-760q0 17 11.5 28.5T760-720ZM313-240h334q9-26 28-45t45-28v-334q-26-9-45-28t-28-45H313q-9 26-28 45t-45 28v334q26 9 45 28t28 45Zm447 80q17 0 28.5-11.5T800-200q0-17-11.5-28.5T760-240q-17 0-28.5 11.5T720-200q0 17 11.5 28.5T760-160Zm-560 0q17 0 28.5-11.5T240-200q0-17-11.5-28.5T200-240q-17 0-28.5 11.5T160-200q0 17 11.5 28.5T200-160Zm0-600Zm560 0Zm0 560Zm-560 0Z" />
                    </svg>Measure Area
                </div>
            </div>
            <hr>
            <div id="modifyLinkDiv">
                <?php if (!$_SESSION["modify"]): ?>
                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="pwdForm">
                        <input type="password" name="enteredPwd" id="pwd" placeholder="Enter Password" autocomplete="on">
                        <input type="submit" id="modifySubmit" name="pwd" value="Modify labels">
                    </form>
                <?php else: ?>
                    <div id="modifyLink">
                        <a href="./modify/modify.php">Modify labels</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div id="coords">X: - | Z: -</div>
        <div id="escape">Press ESC to leave mode</div>
        <div id="result"></div>
        <div id="copyCoords">
            <input type="hidden" value="" id="coordsValue">
            Copy Coordinates
        </div>
        <div id="coordsArray"></div>
    </main>
</body>
<script src="map.js" type="module"></script>

</html>