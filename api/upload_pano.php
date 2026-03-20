<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

function upload($conn, $provinceAbbrevations, $name, $zipFile)
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
