<?php

require __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/./world_data.php';

use Google\Cloud\Storage\StorageClient;

function upload($conn, $provinceAbbrevations, $name, $zipFile, $map)
{
    $zip = new ZipArchive(); //create new archive
    if ($zip->open($zipFile) === true) {
        $zip->extractTo(__DIR__ . '/../blockguessr/addPano/extractFolder'); //moves files to temp location
        $zip->close(); //closes active zip archive

        $images = glob(__DIR__ . '/../blockguessr/addPano/extractFolder/' . '*.{jpg,jpeg,webp,png}', GLOB_BRACE);

        //Error handlers

        if (!in_array(strtoupper(trim($_SESSION['provinceAbbr'])), $provinceAbbrevations)) {
            throwError("Invalid province abbreviation");
        }

        if (count($images) !== 6) {
            throwError("Upload exactly six images");
        }

        $id = uniqid('panorama_');

        try {
            uploadToCloud($images, $id);
            insertInDatabase($id, $conn, $name);
            addIdtoProvinceMaps($conn, $id, $map);
        } catch (Exception $e) {
            throwError("Process failed: " . $e->getMessage());
        }
    } else {
        throwError("Could not extract uploaded file" . $zipFile);
    }
}

function throwError($errorMessage)
{
    $_SESSION['error'] = $errorMessage;
    header('Location: ../addPano/add_pano.php');
    exit();
}

function uploadToCloud($images, $id)
{
    $projectId = 'blockguessr-475818';
    $bucketName = 'panorama-blockguessr-bucket';

    $storage = new StorageClient([ //class storage client ^^
        'projectId' => $projectId,
        'keyFilePath' => __DIR__ . '/blockguessr-475818-11356824dac5.json',
    ]);

    $bucket = $storage->bucket($bucketName);

    foreach ($images as $image) {
        $objectName = 'panorama-folder/' . $id . '/' . basename($image);
        $bucket->upload(
            fopen($image, 'r'),
            ['name' => $objectName]
        );
        unlink($image);
    }
}

function insertInDatabase($id, $conn, $name)
{
    $name = explode("_", $name);

    $x = (int)trim($name[0], "x");
    $y = (int)trim($name[1], "z");

    $folderName = 'panorama-folder/' . $id . '/';
    $province = $_SESSION['provinceAbbr'];

    $stmt = $conn->prepare("INSERT INTO panodata (foldername, x, y, province) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $folderName, $x, $y, $province);
    $stmt->execute();
    $stmt->close();
}

function addIdtoProvinceMaps($conn, $folder_id, $map)
{

    $folderName = 'panorama-folder/' . $folder_id . '/';

    $stmt = $conn->prepare("SELECT id FROM panodata WHERE folderName = ?");
    $stmt->bind_param("s", $folderName);
    $stmt->execute();
    $result = $stmt->get_result();
    $pano_id = $result->fetch_all(MYSQLI_NUM)[0][0];

    $mapId = $map[0];
    $locations = explode(",", $map[2]);

    array_push($locations, $pano_id);

    $newLocations = implode(",", $locations);

    $stmt = $conn->prepare("UPDATE maps SET locations = ? WHERE id = ?");
    $stmt->bind_param("si", $newLocations, $mapId);
    $stmt->execute();

    //update world

    $worldMap = getWorldDataByName($conn, "World");

    $mapId = $worldMap[0];
    $locations = explode(",", $worldMap[2]);

    array_push($locations, $pano_id);

    $newLocations = implode(",", $locations);

    $stmt = $conn->prepare("UPDATE maps SET locations = ? WHERE id = ?");
    $stmt->bind_param("si", $newLocations, $mapId);
    $stmt->execute();
}
