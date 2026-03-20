<?php

session_start();

if (isset($_POST['deleteBtn']) && isset($_SESSION['edit']) && $_SESSION['edit'] == true) {
    unlink($_POST['deleteId']);

    header("Location: archive.php?deletesucces");
} else {
    header("Location: archive.php?missing_permission");
}
