<?php

$panoInfo = ["Id: " . $_GET["id"], $_GET["folderName"], "X: " . $_GET["x"], "Y: " . $_GET["y"], "Province: " . $_GET["province"], "Uploaded at: " . $_GET["uploaded_at"]];

$url = urlencode($panoInfo[1]);

$currentUrl;

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $currentUrl = "https";
} else {
    $currentUrl = "http";
}

$currentUrl .= "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../img/fullServerMap.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css" />
    <link rel="stylesheet" href="pano_viewer.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <title>Blockguessr - Pano Viewer</title>
</head>

<body>
    <main>
        <div id="panorama"></div>
    </main>
    <footer>

        <?php

        for ($i = 0; $i < sizeof($panoInfo); $i++) {
            if ($i == 1) continue;
            echo "<p> $panoInfo[$i] </p>";
        }

        ?>

        <div id="share">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffffff">
                <path d="M680-80q-50 0-85-35t-35-85q0-6 3-28L282-392q-16 15-37 23.5t-45 8.5q-50 0-85-35t-35-85q0-50 35-85t85-35q24 0 45 8.5t37 23.5l281-164q-2-7-2.5-13.5T560-760q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35q-24 0-45-8.5T598-672L317-508q2 7 2.5 13.5t.5 14.5q0 8-.5 14.5T317-452l281 164q16-15 37-23.5t45-8.5q50 0 85 35t35 85q0 50-35 85t-85 35Zm0-80q17 0 28.5-11.5T720-200q0-17-11.5-28.5T680-240q-17 0-28.5 11.5T640-200q0 17 11.5 28.5T680-160ZM200-440q17 0 28.5-11.5T240-480q0-17-11.5-28.5T200-520q-17 0-28.5 11.5T160-480q0 17 11.5 28.5T200-440Zm480-280q17 0 28.5-11.5T720-760q0-17-11.5-28.5T680-800q-17 0-28.5 11.5T640-760q0 17 11.5 28.5T680-720Zm0 520ZM200-480Zm480-280Z" />
            </svg>
        </div>
    </footer>
</body>
<script>
    pannellum.viewer('panorama', {
        "type": "cubemap",
        "autoLoad": true,
        "showControls": false,
        "cubeMap": [
            `proxy.php?file=<?php echo $url ?>panorama_0.png`,
            `proxy.php?file=<?php echo $url ?>panorama_1.png`,
            `proxy.php?file=<?php echo $url ?>panorama_2.png`,
            `proxy.php?file=<?php echo $url ?>panorama_3.png`,
            `proxy.php?file=<?php echo $url ?>panorama_4.png`,
            `proxy.php?file=<?php echo $url ?>panorama_5.png`,
        ]
    })

    document.getElementById("share").addEventListener("click", () => {

        navigator.clipboard.writeText("<?php echo $currentUrl ?>");

        alert("Copied url succesfully");
    })
</script>

</html>