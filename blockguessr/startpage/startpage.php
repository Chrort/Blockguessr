<?php

session_start();

if (!$_SESSION['loggedIn']) {
    header("Location: ../login/login.php");
    exit();
}

require '../../api/world_data.php';
require '../../api/highscore_data.php';
require '../../config/db_connect.php';
require './leaderboard.php';

$worldData = getWorldData($conn, null);
$globalMaps = [];
$provinceMaps = [];
$otherMaps = [];

$highscoreData = getHighscoreData($conn, $_SESSION["id"], true)[0];

foreach ($worldData as $world) {
    if ($world[3] == "1") {
        array_push($globalMaps, $world);
    } elseif ($world[3] == "0") {
        array_push($provinceMaps, $world);
    } elseif ($world[3] == "2") {
        array_push($otherMaps, $world);
    }
}

$username = $_SESSION['username'];

function getClass($score)
{
    switch (true) {
        case $score == "Not played yet":
            return "noBorder";
        case $score < 5000:
            return "noBorder";
        case $score < 15000:
            return "bronzeBorder";
        case $score < 22500:
            return "silverBorder";
        case $score < 25000:
            return "goldBorder";
        case $score == 25000:
            return "platinBorder";
        default:
            return "noBorder";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="username" content="<?php echo htmlspecialchars($username) ?>">
    <link rel="stylesheet" href="./startpage.css">
    <link rel="stylesheet" href="../header/header.css">
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <title>Blockguessr - Startpage</title>
</head>

<body>
    <?php require_once '../header/header.php' ?>
    <div id="panorama"></div>
    <main>
        <div id="loadingScreenDiv">Loading locations</div>
        <h1 id="helloText">Hello <?php echo htmlspecialchars($username) ?> 👋!</h1><br>
        <div class="mapsText">
            <div class="line"></div>
            <h1>Global Maps</h1>
            <div class="line"></div>
        </div>
        <div id="worldMap">
            <?php
            foreach ($globalMaps as $map) {
                $locationCount = count(explode((","), $map[2]));

                $highscore;
                if (array_key_exists($map[1], $highscoreData) && $highscoreData[$map[1]] != NULL) {
                    $highscore = $highscoreData[$map[1]];
                } else {
                    $highscore = "Not played yet";
                }

                $class = getClass($highscore);
            ?>
                <div class='map <?= $class ?>'>
                    <p class='mapName'> <?= $map[1] ?> </p>
                    <div class="toggleLeaderboard">
                        <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960" width="35px" fill="#000000">
                            <path d="M160-200h160v-320H160v320Zm240 0h160v-560H400v560Zm240 0h160v-240H640v240ZM80-120v-480h240v-240h320v320h240v400H80Z" />
                        </svg>
                    </div>
                    <p class='mapLocations'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                            <path d='M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z' />
                        </svg>
                        <?= $locationCount ?>
                    </p>
                    <p class='mapHighscore'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                            <path d='M200-160v-80h560v80H200Zm0-140-51-321q-2 0-4.5.5t-4.5.5q-25 0-42.5-17.5T80-680q0-25 17.5-42.5T140-740q25 0 42.5 17.5T200-680q0 7-1.5 13t-3.5 11l125 56 125-171q-11-8-18-21t-7-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820q0 15-7 28t-18 21l125 171 125-56q-2-5-3.5-11t-1.5-13q0-25 17.5-42.5T820-740q25 0 42.5 17.5T880-680q0 25-17.5 42.5T820-620q-2 0-4.5-.5t-4.5-.5l-51 321H200Zm68-80h424l26-167-105 46-133-183-133 183-105-46 26 167Zm212 0Z' />
                        </svg>
                        <?= $highscore ?>
                    </p>
                    <form action='../game/prep_game.php' method='post'>
                        <input type='hidden' name='id' value='<?= $map[0] ?>'>
                        <input type='submit' name='submit' value='Play' onclick='loadingScreen()'>
                    </form>
                    <?php displayLeaderboard($conn, $map[0]) ?>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="mapsText">
            <div class="line"></div>
            <h1>Province Maps</h1>
            <div class="line"></div>
        </div>
        <div id="provinceMaps">
            <?php
            foreach ($provinceMaps as $map) {
                $locationCount = count(explode((","), $map[2]));
                if ($locationCount >= 5) {

                    $highscore;
                    if (array_key_exists($map[1], $highscoreData) && $highscoreData[$map[1]] != NULL) {
                        $highscore = $highscoreData[$map[1]];
                    } else {
                        $highscore = "Not played yet";
                    }

                    $class = getClass($highscore);
            ?>
                    <div class='map <?= $class ?>'>
                        <p class='mapName'> <?= $map[1] ?> </p>
                        <div class="toggleLeaderboard">
                            <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960" width="35px" fill="#000000">
                                <path d="M160-200h160v-320H160v320Zm240 0h160v-560H400v560Zm240 0h160v-240H640v240ZM80-120v-480h240v-240h320v320h240v400H80Z" />
                            </svg>
                        </div>
                        <p class='mapLocations'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                                <path d='M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z' />
                            </svg>
                            <?= $locationCount ?>
                        </p>
                        <p class='mapHighscore'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                                <path d='M200-160v-80h560v80H200Zm0-140-51-321q-2 0-4.5.5t-4.5.5q-25 0-42.5-17.5T80-680q0-25 17.5-42.5T140-740q25 0 42.5 17.5T200-680q0 7-1.5 13t-3.5 11l125 56 125-171q-11-8-18-21t-7-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820q0 15-7 28t-18 21l125 171 125-56q-2-5-3.5-11t-1.5-13q0-25 17.5-42.5T820-740q25 0 42.5 17.5T880-680q0 25-17.5 42.5T820-620q-2 0-4.5-.5t-4.5-.5l-51 321H200Zm68-80h424l26-167-105 46-133-183-133 183-105-46 26 167Zm212 0Z' />
                            </svg>
                            <?= $highscore ?>
                        </p>
                        <form action='../game/prep_game.php' method='post'>
                            <input type='hidden' name='id' value='<?= $map[0] ?>'>
                            <input type='submit' name='submit' value='Play' onclick='loadingScreen()'>
                        </form>
                        <?php displayLeaderboard($conn, $map[0]) ?>
                    </div>
                <?php
                } else {
                    $highscore = "Not played yet";
                ?>
                    <div class='map'>
                        <p class='mapName'> <?= $map[1] ?> </p>
                        <p class='mapLocations'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                                <path d='M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z' />
                            </svg>
                        </p>
                        <p class='mapHighscore'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                                <path d='M200-160v-80h560v80H200Zm0-140-51-321q-2 0-4.5.5t-4.5.5q-25 0-42.5-17.5T80-680q0-25 17.5-42.5T140-740q25 0 42.5 17.5T200-680q0 7-1.5 13t-3.5 11l125 56 125-171q-11-8-18-21t-7-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820q0 15-7 28t-18 21l125 171 125-56q-2-5-3.5-11t-1.5-13q0-25 17.5-42.5T820-740q25 0 42.5 17.5T880-680q0 25-17.5 42.5T820-620q-2 0-4.5-.5t-4.5-.5l-51 321H200Zm68-80h424l26-167-105 46-133-183-133 183-105-46 26 167Zm212 0Z' />
                            </svg>
                            <?= $highscore ?>
                        </p>
                        <form action='#' method='post'>
                            <input type='hidden' name='id' value='$map[0]'>
                            <input type='submit' name='submit' value='N/A' style='background-color: gray'>
                        </form>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <div class="mapsText">
            <div class="line"></div>
            <h1>Other Maps</h1>
            <div class="line"></div>
        </div>
        <div id="otherMaps">
            <?php
            foreach ($otherMaps as $map) {
                $locationCount = count(explode((","), $map[2]));

                $highscore;
                if (array_key_exists($map[1], $highscoreData) && $highscoreData[$map[1]] != NULL) {
                    $highscore = $highscoreData[$map[1]];
                } else {
                    $highscore = "Not played yet";
                }

                $class = getClass($highscore);
            ?>
                <div class='map <?= $class ?>'>
                    <p class='mapName'><?= $map[1] ?></p>
                    <div class="toggleLeaderboard">
                        <svg xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 -960 960 960" width="35px" fill="#000000">
                            <path d="M160-200h160v-320H160v320Zm240 0h160v-560H400v560Zm240 0h160v-240H640v240ZM80-120v-480h240v-240h320v320h240v400H80Z" />
                        </svg>
                    </div>
                    <p class='mapLocations'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                            <path d='M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z' />
                        </svg>
                        <?= $locationCount ?>
                    </p>
                    <p class='mapHighscore'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='30px' viewBox='0 -960 960 960' width='30px' fill='#1f1f1f'>
                            <path d='M200-160v-80h560v80H200Zm0-140-51-321q-2 0-4.5.5t-4.5.5q-25 0-42.5-17.5T80-680q0-25 17.5-42.5T140-740q25 0 42.5 17.5T200-680q0 7-1.5 13t-3.5 11l125 56 125-171q-11-8-18-21t-7-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820q0 15-7 28t-18 21l125 171 125-56q-2-5-3.5-11t-1.5-13q0-25 17.5-42.5T820-740q25 0 42.5 17.5T880-680q0 25-17.5 42.5T820-620q-2 0-4.5-.5t-4.5-.5l-51 321H200Zm68-80h424l26-167-105 46-133-183-133 183-105-46 26 167Zm212 0Z' />
                        </svg>
                        <?= $highscore ?>
                    </p>
                    <form action='../game/prep_game.php' method='post'>
                        <input type='hidden' name='id' value='<?= $map[0] ?>'>
                        <input type='submit' name='submit' value='Play' onclick='loadingScreen()'>
                    </form>
                    <?php displayLeaderboard($conn, $map[0]) ?>
                </div>
            <?php } ?>

        </div>
    </main>
</body>
<script src="startpage.js"></script>

</html>