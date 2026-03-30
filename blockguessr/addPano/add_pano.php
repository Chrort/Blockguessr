<?php

session_start();
set_time_limit(0);

require __DIR__ . '/../../api/upload_pano.php';
require __DIR__ . '/../../config/login_queries.php';
require __DIR__ . '/../../config/db_connect.php';
require __DIR__ . '/../../api/world_data.php';

if (!$_SESSION['loggedIn'] || getUserData($_SESSION['username'], $conn)[0][6] != "admin") {
    header("Location: ../login/login.php");
    exit();
}

$username = $_SESSION['username'];
$provinceAbbr = $_SESSION['provinceAbbr'] ?? "";

$provinceAbbrevations = array(
    "AM",
    "AN",
    "EG",
    "EL",
    "FM",
    "GM",
    "GL",
    "KT",
    "MT",
    "MG",
    "ML",
    "MW",
    "NR",
    "NP",
    "NF",
    "PH",
    "PV",
    "PL",
    "RO",
    "SM",
    "SL",
    "SH",
    "SV",
    "SA",
    "TW",
    "UP",
    "UT",
    "WL"
);

if (isset($_POST['submit'])) {

    if (empty($_FILES['file'])) {
        $_SESSION['error'] = "No files selected";
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    }
    $_SESSION['count'] = count($_FILES['file']['name']);
    $_SESSION['provinceAbbr'] = strtoupper(trim($_POST['province']));

    for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
        $map = getWorldDataByName($conn, getFullMapName($_SESSION['provinceAbbr']));
        upload($conn, $provinceAbbrevations, $_FILES['file']['name'][$i], $_FILES['file']['tmp_name'][$i], $map);
    }

    $_SESSION['succes'] = true;
}

function getFullMapName(string $provinceAbbr): string
{
    switch ($provinceAbbr) {
        case "AM":
            return "Amazonien";
            break;
        case "AN":
            return "Außengebiet Nord";
            break;
        case "EG":
            return "Eglynas";
            break;
        case "EL":
            return "Elarion";
            break;
        case "FM":
            return "Formosa";
            break;
        case "GM":
            return "Grönmark";
            break;
        case "GL":
            return "Gultland";
            break;
        case "KT":
            return "Khwati";
            break;
        case "MG":
            return "Mägismaa";
            break;
        case "MT":
            return "Maastik";
            break;
        case "MW":
            return "Morwyn";
            break;
        case "NR":
            return "Naryn";
            break;
        case "NP":
            return "Nationalpark";
            break;
        case "NF":
            return "Nordisches Flachland";
            break;
        case "PH":
            return "Pahia";
            break;
        case "PV":
            return "Pieva";
            break;
        case "PL":
            return "Polynesien";
            break;
        case "RO":
            return "Reota";
            break;
        case "SM":
            return "Samudra";
            break;
        case "SL":
            return "Südlande";
            break;
        case "SH":
            return "Sehloa";
            break;
        case "SV":
            return "Selvameer";
            break;
        case "SA":
            return "Soala";
            break;
        case "TW":
            return "Terra West";
            break;
        case "UP":
            return "Upland";
            break;
        case "UT":
            return "Uthlande";
            break;
        case "WL":
            return "Weißes Land";
            break;
        default:
            return "";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="username" content="<?php echo htmlspecialchars($username) ?>">
    <link rel="stylesheet" href="add_pano.css">
    <link rel="stylesheet" href="../header/header.css">
    <title>Blockguessr - Add Panorama</title>
    <link rel="icon" type="image/x-icon" href="../../img/fullServerMap.png">
</head>

<body>
    <?php require_once '../header/header.php' ?>
    <main>
        <h1>Add Panorama</h1>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="addForm" enctype="multipart/form-data">
            <input type="file" name="file[]" id="file" required multiple>
            <input list="types" name="province" id="province" placeholder="Province abbr." value="<?php echo htmlspecialchars($provinceAbbr) ?>" required>
            <datalist id="types">
                <option value="AM"></option>
                <option value="AN"></option>
                <option value="EG"></option>
                <option value="EL"></option>
                <option value="FM"></option>
                <option value="GM"></option>
                <option value="GL"></option>
                <option value="KT"></option>
                <option value="MT"></option>
                <option value="MG"></option>
                <option value="ML"></option>
                <option value="MW"></option>
                <option value="NR"></option>
                <option value="NP"></option>
                <option value="NF"></option>
                <option value="PH"></option>
                <option value="PV"></option>
                <option value="PL"></option>
                <option value="RO"></option>
                <option value="SM"></option>
                <option value="SL"></option>
                <option value="SH"></option>
                <option value="SV"></option>
                <option value="SA"></option>
                <option value="TW"></option>
                <option value="UP"></option>
                <option value="UT"></option>
                <option value="WL"></option>
            </datalist>
            <?php if (isset($_SESSION["error"])) {
                $error = $_SESSION["error"];
                echo "<p> $error </p>";
                unset($_SESSION["error"]);
            } ?>
            <?php

            if (isset($_SESSION["succes"])) {
                $c = $_SESSION['count'];

                echo "<p id='p' style='background-color: rgba(119, 207, 103, 1) !important;'>Uploaded $c files</p>";

                unset($_SESSION["succes"], $_SESSION['count']);
            } ?>
            <button type="submit" name="submit" id="submit">Add Panorama</button>
        </form>
    </main>
</body>
<script src="./add_pano.js"></script>

</html>