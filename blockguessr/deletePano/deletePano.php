<?php

session_start();
set_time_limit(0);

require __DIR__ . '/../../config/db_connect.php';
require __DIR__ . '/../../config/login_queries.php';
require __DIR__ . '/../../api/pano_data.php';
require __DIR__ . '/../../api/world_data.php';

$username = $_SESSION['username'];
$role = getUserData($username, $conn)[0][5];

if (!$_SESSION['loggedIn'] || $role != "admin") {
    header("Location: ../login/login.php");
    exit();
}

$panoData = getPanoData($conn);
$maps = getWorldData($conn, NULL);

if (isset($_POST["submit"])) {
    if (empty($_POST["id"])) {
        $_SESSION['error'] = "Missing Input";
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    }

    $id = (int)$_POST["id"];

    for ($i = 0; $i < count($panoData); $i++) {
        if ($id == (int)$panoData[$i][0]) break;
        if ($i == count($panoData) - 1) {
            $_SESSION['error'] = "Invalid ID";
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
    }

    deletePano($conn, $id);

    deleteIdFromMaps($conn, $id, $maps);

    $_SESSION['panoId'] = $id;
    $_SESSION['succes'] = true;
}

function deletePano($conn, $id)
{
    $stmt = $conn->prepare("DELETE FROM panodata WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

function deleteIdFromMaps($conn, $id, $maps)
{
    for ($i = 1; $i < count($maps); $i++) {
        $mapId = $maps[$i][0];
        $locations = explode(",", $maps[$i][2]);

        for ($k = 0; $k < count($locations); $k++) {
            if ((string)$id == $locations[$k]) {
                unset($locations[$k]);
            }
        }

        $newLocations = implode(",", $locations);

        $stmt = $conn->prepare("UPDATE maps SET locations = ? WHERE id = ?");
        $stmt->bind_param("si", $newLocations, $mapId);
        $stmt->execute();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="username" content="<?php echo htmlspecialchars($username) ?>">
    <link rel="stylesheet" href="deletePano.css">
    <link rel="stylesheet" href="../header/header.css">
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
    <title>BlockGuessr - Delete Panorama</title>
</head>

<body>
    <?php require_once '../header/header.php' ?>
    <main>
        <h1>Delete Panorama</h1>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="addForm" enctype="multipart/form-data">
            <input type="number" name="id" id="id" placeholder="Id to delete">
            <?php if (isset($_SESSION["error"])) {
                $error = $_SESSION["error"];
                echo "<p> $error </p>";
                unset($_SESSION["error"]);
            } ?>
            <?php

            if (isset($_SESSION["succes"])) {
                $panoId = $_SESSION['panoId'];
                echo "<p id='p' style='background-color: rgba(119, 207, 103, 1) !important;'>Deleted Pano #$panoId</p>";

                unset($_SESSION["succes"], $_SESSION['panoId']);
            } ?>
            <button type="submit" name="submit" id="submit">Delete Panorama</button>
        </form>
    </main>
</body>

</html>