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
$labels = getLabels($conn);

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
        <svg id="menu" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#000000" style="position: absolute;">
            <path d="M160-240q-17 0-28.5-11.5T120-280q0-17 11.5-28.5T160-320h640q17 0 28.5 11.5T840-280q0 17-11.5 28.5T800-240H160Zm0-200q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520h640q17 0 28.5 11.5T840-480q0 17-11.5 28.5T800-440H160Zm0-200q-17 0-28.5-11.5T120-680q0-17 11.5-28.5T160-720h640q17 0 28.5 11.5T840-680q0 17-11.5 28.5T800-640H160Z" />
        </svg>
    </header>
    <main>
        <div id="navigationBar" class="hideNavbar">
            <svg xmlns="http://www.w3.org/2000/svg" height="4vh" viewBox="0 -960 960 960" width="4vh" fill="#000000" id="navigateSvg">
                <path d="m319-280 161-73 161 73 15-15-176-425-176 425 15 15ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
            </svg>
<<<<<<< HEAD
            <div id="deleteRoute">
                <svg xmlns="http://www.w3.org/2000/svg" height="4vh" viewBox="0 -960 960 960" width="4vh" fill="#ff0000">
                    <path d="m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                </svg>
            </div>
=======
            <abbr title="Delete Route">
                <div id="deleteRoute">
                    <svg xmlns="http://www.w3.org/2000/svg" height="4vh" viewBox="0 -960 960 960" width="4vh" fill="#ff0000">
                        <path d="m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                    </svg>
                </div>
            </abbr>
>>>>>>> e8f6b9d8bd2a0fc1683856b29b65948d6da680ad
            <label for="start">From: </label>
            <input list="labels" type="text" name="start" id="start" placeholder="Al-Sahrawia">
            <label for="destination">To: </label>
            <input list="labels" type="text" name="destination" id="destination" placeholder="1093,-664">
            <button id="navigateBtn">
                <svg xmlns="http://www.w3.org/2000/svg" height="3vh" viewBox="0 -960 960 960" width="3vh" fill="#000000">
                    <path d="M320-360h80v-120h140v100l140-140-140-140v100H360q-17 0-28.5 11.5T320-520v160ZM480-80q-15 0-29.5-6T424-104L104-424q-12-12-18-26.5T80-480q0-15 6-29.5t18-26.5l320-320q12-12 26.5-18t29.5-6q15 0 29.5 6t26.5 18l320 320q12 12 18 26.5t6 29.5q0 15-6 29.5T856-424L536-104q-12 12-26.5 18T480-80ZM320-320l160 160 320-320-320-320-320 320 160 160Zm160-160Z" />
                </svg>Navigate
            </button>
            <div id="errorLog" style="color: red; cursor: default !important"></div>
            <datalist id="labels">
                <?php for ($i = 0; $i < count($labels); $i++): ?>
                    <option value="<?= $labels[$i]['name'] ?>"></option>
                <?php endfor; ?>
            </datalist>
        </div>
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

                    for ($j = 0; $j < count($pairs); $j += 4) {
                        list($x, $y) = explode(",", $pairs[$j]);

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
            <section id="heading">
                <svg xmlns="http://www.w3.org/2000/svg" height="1.6rem" viewBox="0 -960 960 960" width="1.6rem" fill="#000000">
                    <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z" />
                </svg>
                Settings (s)
            </section>
            <hr>
            <section id="checkboxes">
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
            </section>
            <hr>
            <section id="mapTypes">
                <img id="maps" src="../img/daymap.jpg" alt="daymap">
                <img id="nightMaps" src="../img/nightmap.jpg" alt="nightmap">
                <img id="terrainMaps" src="../img/terrainmap.jpg" alt="terrainmap">
                <img id="biomeMaps" src="../img/biomemap.jpg" alt="biomemap">
            </section>
            <hr>
            <section id="tools">
                <div>
                    <div class="labelCheckbox toolIcon" id="navigation">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1.1em" viewBox="0 -960 960 960" width="1.1em" fill="#000000">
                            <path d="m200-120-40-40 320-720 320 720-40 40-280-120-280 120Zm84-124 196-84 196 84-196-440-196 440Zm196-84Z" />
                        </svg>Navigation (n)
                    </div>
                </div>
                <div>
                    <div class="labelCheckbox toolIcon" id="measureDistance">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1.1em" viewBox="0 -960 960 960" width="1.1em" fill="#000000">
                            <path d="M120-240q-33 0-56.5-23.5T40-320q0-33 23.5-56.5T120-400h10.5q4.5 0 9.5 2l182-182q-2-5-2-9.5V-600q0-33 23.5-56.5T400-680q33 0 56.5 23.5T480-600q0 2-2 20l102 102q5-2 9.5-2h21q4.5 0 9.5 2l142-142q-2-5-2-9.5V-640q0-33 23.5-56.5T840-720q33 0 56.5 23.5T920-640q0 33-23.5 56.5T840-560h-10.5q-4.5 0-9.5-2L678-420q2 5 2 9.5v10.5q0 33-23.5 56.5T600-320q-33 0-56.5-23.5T520-400v-10.5q0-4.5 2-9.5L420-522q-5 2-9.5 2H400q-2 0-20-2L198-340q2 5 2 9.5v10.5q0 33-23.5 56.5T120-240Z" />
                        </svg>Measure Distance
                    </div>
                </div>
                <div>
                    <div class="labelCheckbox toolIcon" id="measureArea">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1.1em" viewBox="0 -960 960 960" width="1.1em" fill="#000000">
                            <path d="M200-80q-50 0-85-35t-35-85q0-39 22.5-69.5T160-313v-334q-35-13-57.5-43.5T80-760q0-50 35-85t85-35q39 0 69.5 22.5T313-800h334q12-35 42.5-57.5T760-880q50 0 85 35t35 85q0 40-22.5 70.5T800-647v334q35 13 57.5 43.5T880-200q0 50-35 85t-85 35q-39 0-69.5-22.5T647-160H313q-13 35-43.5 57.5T200-80Zm0-640q17 0 28.5-11.5T240-760q0-17-11.5-28.5T200-800q-17 0-28.5 11.5T160-760q0 17 11.5 28.5T200-720Zm560 0q17 0 28.5-11.5T800-760q0-17-11.5-28.5T760-800q-17 0-28.5 11.5T720-760q0 17 11.5 28.5T760-720ZM313-240h334q9-26 28-45t45-28v-334q-26-9-45-28t-28-45H313q-9 26-28 45t-45 28v334q26 9 45 28t28 45Zm447 80q17 0 28.5-11.5T800-200q0-17-11.5-28.5T760-240q-17 0-28.5 11.5T720-200q0 17 11.5 28.5T760-160Zm-560 0q17 0 28.5-11.5T240-200q0-17-11.5-28.5T200-240q-17 0-28.5 11.5T160-200q0 17 11.5 28.5T200-160Zm0-600Zm560 0Zm0 560Zm-560 0Z" />
                        </svg>Measure Area
                    </div>
                </div>
            </section>
            <hr>
            <section id="form">
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
            </section>
        </div>
        <div id="routeInfo">
<<<<<<< HEAD
            <div id="distanceInfo">433m</div>
            <div id="streetsInfo">2 -> 4</div>
            <div id="travelTimeInfo">4.2s</div>
            <div id="executionTimeInfo">453ms</div>
=======
            <svg xmlns="http://www.w3.org/2000/svg" height="4vh" viewBox="0 -960 960 960" width="4vh" fill="#000000" id="routeSvg">
                <path d="M270-186.17q-42-42.18-42-101.4V-581q-37-12-60.5-44T144-696q0-50 35.5-85t85-35q49.5 0 84.5 35t35 85q0 41-24 72t-60 42v294.06q0 29.67 21.21 50.81 21.21 21.13 51 21.13T423-237.15q21-21.15 21-50.85v-384q0-60 42-102t102-42q60 0 102 42t42 102v294q36 11 60 42t24 72q0 50-35 85t-85 35q-49 0-84.5-35T576-264q0-38 24-71t60-43.77v-293.6Q660-702 638.79-723q-21.21-21-51-21T537-722.85Q516-701.7 516-672v384q0 60-42 102t-102 42q-60 0-102-42.17ZM264-648q20.4 0 34.2-13.8Q312-675.6 312-696q0-20.4-13.8-34.2Q284.4-744 264-744q-20.4 0-34.2 13.8Q216-716.4 216-696q0 20.4 13.8 34.2Q243.6-648 264-648Zm432 432q20.4 0 34.2-13.8Q744-243.6 744-264q0-20.4-13.8-34.2Q716.4-312 696-312q-20.4 0-34.2 13.8Q648-284.4 648-264q0 20.4 13.8 34.2Q675.6-216 696-216ZM264-696Zm432 432Z" />
            </svg>
            <div id="headerRoute">
                <div id="streetsInfo">Start → End</div>
                <div id="distanceInfo"></div>
            </div>
            <div id="horseTimeInfo">
                <svg xmlns="http://www.w3.org/2000/svg" height="3vh" viewBox="0 -960 960 960" width="3vh" fill="#000000">
                    <path d="M216-96v-171q0-21.47 11-38.73Q238-323 257-332l175-85v-63l-111 59q-11.29 6-23.53 9-12.23 3-24.47 3-28.12 0-53.06-15T181-466q-11-23-10.5-49t14.5-49l115-192-84-108h240q119.7 0 203.85 84Q744-696 744-576v480H216Zm72-72h384v-408q0-90-63-153t-153-63h-93l24 30-140 234q-4 7.27-4.5 15t3.5 15q5 9 12.48 13t14.52 4q4 0 14-4l217-115v228L288-267v99Zm144-312Z" />
                </svg>
            </div>
            <div id="sprintTimeInfo">
                <svg xmlns="http://www.w3.org/2000/svg" height="3vh" viewBox="0 -960 960 960" width="3vh" fill="#000000">
                    <path d="m216-160-56-56 384-384H440v80h-80v-160h233q16 0 31 6t26 17l120 119q27 27 66 42t84 16v80q-62 0-112.5-19T718-476l-40-42-88 88 90 90-262 151-40-69 172-99-68-68-266 265Zm-96-280v-80h200v80H120ZM40-560v-80h200v80H40Zm739-80q-33 0-57-23.5T698-720q0-33 24-56.5t57-23.5q33 0 57 23.5t24 56.5q0 33-24 56.5T779-640Zm-659-40v-80h200v80H120Z" />
                </svg>
            </div>
            <div id="walkTimeInfo">
                <svg xmlns="http://www.w3.org/2000/svg" height="3vh" viewBox="0 -960 960 960" width="3vh" fill="#000000">
                    <path d="m298-96 93-476-79 34v106h-72v-154l185-78q10-4 19.5-6t18.5-2q26 0 46 11.5t34 31.5l9 13q23 35 51.5 73.5T720-504v72q-65 0-115.5-24T527-522l-21 114 70 70v242h-72v-215l-73-56-62 271h-71Zm170.5-624.5Q444-745 444-780t24.5-59.5Q493-864 528-864t59.5 24.5Q612-815 612-780t-24.5 59.5Q563-696 528-696t-59.5-24.5Z" />
                </svg>
            </div>
            <abbr title="Calculation time">
                <div id="executionTimeInfo">Failed</div>
            </abbr>
>>>>>>> e8f6b9d8bd2a0fc1683856b29b65948d6da680ad
        </div>
        <div id="coords">X: - | Y: -</div>
        <div id="escape">Press ESC to leave mode</div>
        <div id="result"></div>
        <div id="copyCoords">
            <input type="hidden" value="" id="coordsValue">
            Copy Coordinates
        </div>
    </main>
</body>
<script src="map.js" type="module"></script>
<script src="navigation.js" type="module"></script>

</html>