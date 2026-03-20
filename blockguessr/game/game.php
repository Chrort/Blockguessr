<?php

session_start();

if (!$_SESSION['loggedIn']) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../../config/db_connect.php';
require_once '../../config/map_queries.php';

$mapId = $_SESSION['mapId'];
$mapName = $_SESSION['mapName'];

$borders = getBorders($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="username" content="<?php echo $_SESSION['username'] ?>" id="usernameMeta">
    <title>Blockgussr - Map - <?php echo $mapName ?></title>
    <link rel="stylesheet" href="game.css">
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
</head>

<body>
    <main>
        <div id="gameInfo">
            <span>Map: <?php echo $mapName ?></span> <span id="roundInfo">Round: 1/5</span> <span id="scoreInfo">Score: 0</span>
        </div>
        <div id="panorama"></div>
        <div id="guessContainer">
            <div id="mapContainer">
                <div id="map">
                    <div class="cleanBorder" id="cB1"></div>
                    <div class="cleanBorder" id="cB2"></div>
                    <div class="cleanBorder" id="cB3"></div>
                    <div class="cleanBorder" id="cB4"></div>
                    <div id="streetLabelDivContainer"></div>
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
                        <line x1="0" y1="0" x2="0" y2="0" style="stroke:red;stroke-width:5" id="idLine" />
                    </svg>
                    <div id="pin"></div>
                    <div id="location">
                        <svg xmlns="http://www.w3.org/2000/svg" height="100%" viewBox="0 -960 960 960" width="100%" fill="#FFFFFF">
                            <path d="M200-120v-680h360l16 80h224v400H520l-16-80H280v280h-80Zm300-440Zm86 160h134v-240H510l-16-80H280v240h290l16 80Z" />
                        </svg>
                    </div>
                </div>
            </div>
            <button id="guess">Guess</button>
        </div>
        <div id="resultScreen">
            <div id="nextRound">
                <div id="distance"></div>
                <div id="nextRoundBtn">Next Round</div>
                <div id="score"></div>
            </div>
        </div>
        <a id="exitGame" href="./exit_game.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#c61d1dff">
                <path d="M200-120q-33 0-56.5-23.5T120-200v-160h80v160h560v-560H200v160h-80v-160q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm220-160-56-58 102-102H120v-80h346L364-622l56-58 200 200-200 200Z" />
            </svg>
        </a>
    </main>
    <script src="./game.js" type="module"></script>
</body>

</html>