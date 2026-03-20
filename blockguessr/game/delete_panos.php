<?php

function deletePanos($username)
{
    $baseDir = './panoramas/' . $username . '/' . 'panorama-folder/';

    if (!is_dir($baseDir)) {
        return;
    }

    $dir = scandir($baseDir);

    for ($i = 2; $i < 7; $i++) {
        $files = scandir('./panoramas/' . $username . '/' . 'panorama-folder/' . $dir[$i]);

        foreach ($files as $file) {
            unlink('./panoramas/' . $username . '/' . 'panorama-folder/' . $dir[$i] . '/' . $file);
        }

        rmdir('./panoramas/' . $username . '/' . 'panorama-folder/' . $dir[$i]);
    }
    rmdir('./panoramas/' . $username . '/' . 'panorama-folder/');
    rmdir('./panoramas/' . $username);
}
