<?php
require_once '../config/db_connect.php';
require_once '../config/map_queries.php';
require_once '../api/pano_data.php';

$error = "";

if (isset($_POST['pwd'])) {
    if (hash("xxh3", $_POST['enteredPwd']) == hash("xxh3", "LeoO2Chrqrt")) {
        session_start();
        $_SESSION["modify"] = true;

        header("Location: ./modify/modify.php");
        exit;
    } else {
        $error = "Incorrect Password";
    }
}

//get borders

$borders = getBorders($conn);
$panoramas = getPanoData($conn);
$streets = getStreets($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/fullServerMap.png" type="image/x-icon">
    <link rel="stylesheet" href="map.css">
    <meta name="labelData" content="<?php echo htmlspecialchars($labels) ?>">
    <title>Blockguessr - Map</title>
</head>

<body>
    <header>
        <a href="../index.php" id="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" height="85%" viewBox="0 -960 960 960" width="85%" fill="#1f1f1f" style="position: absolute">
                <path d="M400-240 160-480l240-240 56 58-142 142h486v80H314l142 142-56 58Z" />
            </svg>
        </a>BlockGuessr
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
            </svg>
        </div>
        <div id="settingsContainer">
            <div id="modifyLinkDiv">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="pwdForm">
                    <input type="password" name="enteredPwd" id="pwd" placeholder="Enter Password" autocomplete="on">
                    <input type="submit" id="modifyLink" name="pwd" value="Modify labels">
                </form>
            </div>
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
            <div>
                <div class="labelCheckbox" id="measureDistance">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                        <path d="M120-240q-33 0-56.5-23.5T40-320q0-33 23.5-56.5T120-400h10.5q4.5 0 9.5 2l182-182q-2-5-2-9.5V-600q0-33 23.5-56.5T400-680q33 0 56.5 23.5T480-600q0 2-2 20l102 102q5-2 9.5-2h21q4.5 0 9.5 2l142-142q-2-5-2-9.5V-640q0-33 23.5-56.5T840-720q33 0 56.5 23.5T920-640q0 33-23.5 56.5T840-560h-10.5q-4.5 0-9.5-2L678-420q2 5 2 9.5v10.5q0 33-23.5 56.5T600-320q-33 0-56.5-23.5T520-400v-10.5q0-4.5 2-9.5L420-522q-5 2-9.5 2H400q-2 0-20-2L198-340q2 5 2 9.5v10.5q0 33-23.5 56.5T120-240Z" />
                    </svg>Measure Distance
                </div>
            </div>
            <div>
                <div class="labelCheckbox" id="measureArea">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0000F5">
                        <path d="M200-80q-50 0-85-35t-35-85q0-39 22.5-69.5T160-313v-334q-35-13-57.5-43.5T80-760q0-50 35-85t85-35q39 0 69.5 22.5T313-800h334q12-35 42.5-57.5T760-880q50 0 85 35t35 85q0 40-22.5 70.5T800-647v334q35 13 57.5 43.5T880-200q0 50-35 85t-85 35q-39 0-69.5-22.5T647-160H313q-13 35-43.5 57.5T200-80Zm0-640q17 0 28.5-11.5T240-760q0-17-11.5-28.5T200-800q-17 0-28.5 11.5T160-760q0 17 11.5 28.5T200-720Zm560 0q17 0 28.5-11.5T800-760q0-17-11.5-28.5T760-800q-17 0-28.5 11.5T720-760q0 17 11.5 28.5T760-720ZM313-240h334q9-26 28-45t45-28v-334q-26-9-45-28t-28-45H313q-9 26-28 45t-45 28v334q26 9 45 28t28 45Zm447 80q17 0 28.5-11.5T800-200q0-17-11.5-28.5T760-240q-17 0-28.5 11.5T720-200q0 17 11.5 28.5T760-160Zm-560 0q17 0 28.5-11.5T240-200q0-17-11.5-28.5T200-240q-17 0-28.5 11.5T160-200q0 17 11.5 28.5T200-160Zm0-600Zm560 0Zm0 560Zm-560 0Z" />
                    </svg>Measure Area
                </div>
            </div>
            <div>
                <div id="settings">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                        <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z" />
                    </svg>
                </div>
            </div>
        </div>
        <div id="mapType">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                <path d="m482-200 114-113-114-113-42 42 43 43q-28 1-54.5-9T381-381q-20-20-30.5-46T340-479q0-17 4.5-34t12.5-33l-44-44q-17 25-25 53t-8 57q0 38 15 75t44 66q29 29 65 43.5t74 15.5l-38 38 42 42Zm165-170q17-25 25-53t8-57q0-38-14.5-75.5T622-622q-29-29-65.5-43T482-679l38-39-42-42-114 113 114 113 42-42-44-44q27 0 55 10.5t48 30.5q20 20 30.5 46t10.5 52q0 17-4.5 34T603-414l44 44ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
            </svg>
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

<?php

if (!empty($error)) {
    echo "<script>
                  document.getElementById('settingsContainer').style.width = '300px';
                  document.getElementById('settingsContainer').style.height = '310px';
                  document.getElementById('pwd').style.backgroundColor = 'red';
            </script>";
}

?>

</html>