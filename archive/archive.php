<?php

session_start();

require '../api/get_pwd.php';

if (isset($_POST["submit"]) && isset($_SESSION['edit']) && $_SESSION['edit'] == true) {
    $file = $_FILES["file"];
    if (!empty($_POST['date'])) {
        $newFileName = "." . $_POST['date'] . "." . uniqid("", true) . ".png"; //creates unique id
    } else {
        $newFileName = ".unknown." . uniqid("", true) . ".png";
    }

    $fileDestination = "./uploads/" . $newFileName; // new file name
    move_uploaded_file($file["tmp_name"], $fileDestination);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$files = array_diff(scandir("./uploads"), array(".", ".."));
$images = [];

foreach ($files as $file) {
    $images[] = "./uploads/" . $file;
}


if (isset($_POST["submitPwd"])) {
    if ($_POST["pwd"] != getPwd()) {
        $errors["pwd"] = "Wrong password";
    } else {
        $_SESSION["edit"] = true;
    }
}

function getImgDate($i)
{
    $parts = explode(("."), $i);
    $date = $parts[2];
    return $date;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/fullServerMap.png" type="image/x-icon">
    <link rel="stylesheet" href="archive.css">
    <title>Blockguessr - Archive</title>
</head>

<body>
    <header>
        <a href="../index.php" id="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" height="85%" viewBox="0 -960 960 960" width="85%" fill="#1f1f1f" style="position: absolute">
                <path d="M400-240 160-480l240-240 56 58-142 142h486v80H314l142 142-56 58Z" />
            </svg>
        </a>
        Archive
        <div id="currentSymbol"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000ff">
                <path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z" />
            </svg></div>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="editForm">
            <input type="password" name="pwd" id="pwd" placeholder="Enter password" required>
            <button type="submit" name="submitPwd" id="submitPwd" style="display: none;"></button>
        </form>
    </header>
    <main>
        <div id="maxImage">
            <button id="close">×</button>
        </div>
        <div id="gallery">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="addForm" enctype="multipart/form-data" class="galleryElement">
                <label for="file">Select an image</label>
                <input type="file" name="file" id="file" accept="image/png" required>
                <input type="date" name="date" id="date">
                <button type="submit" name="submit">Add to Archive</button>
            </form>
            <?php
            foreach ($images as $img) {
                $filename = basename($img);
                $id = preg_replace("/[^a-zA-Z0-9_-]/", "", $filename);
                $date = getImgDate($img);

                echo "<div class='galleryElement' id='container_$id' onmouseover='showExtra(\"date_$id\", \"delete_$id\", \"container_$id\", \"download_$id\")'>
                        <img src='$img' class='galleryElement' alt='Image' id='$id' onclick='max(\"$img\")' loading='lazy'>
                        <div class='dateText' id='date_$id'>$date</div>
                        <a class='downloadImg' id='download_$id' href='$img' download><svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#FFFFFF'><path d='M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z'/></svg></a>
                        <form action='delete.php' method='post' class='deleteForm' id='delete_$id'>
                            <input type='hidden' value='$img' name='deleteId'>
                            <button type='submit' name='deleteBtn'><svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#FFFFFF'><path d='M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z'/></svg></button>
                        </form>
                      </div>";
            }
            ?>
        </div>
    </main>
</body>
<script src="./archive.js"></script>

<?php

if (!empty($errors["pwd"])) {
    echo "<script>
                  document.getElementById('pwd').style.backgroundColor = 'red';
                  edit();
            </script>";
}

if (isset($_SESSION['edit']) && $_SESSION['edit'] == true) {
    echo "<script>
                  document.getElementById('currentSymbol').innerHTML = \"<svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#75FB4C'><path d='M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z'/></svg>\";
                  document.getElementById('currentSymbol').style.pointerEvents = 'none';
            </script>";
}

?>

</html>