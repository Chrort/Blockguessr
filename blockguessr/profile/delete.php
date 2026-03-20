<?php

require_once '../../config/db_connect.php';

if (isset($_POST['deleteId'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $_POST['deleteId']);
    $stmt->execute();
    header("Location: ../startpage/logout.php");
    exit();
} else {
    header("Location: ./profile.php");
    exit();
}
