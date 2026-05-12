<?php

session_start();

require_once '../../config/db_connect.php';
require_once '../../config/register_queries.php';

switch ($_GET["changeType"]) {
    case "email":
        changeEmail($conn);
        break;
    case "pwd":
        changePwd($conn);
        break;
    default:
        header("Location: ./profile.php?unathorized");
        die();
}

require_once '../../config/db_connect.php';

function changeEmail(mysqli $conn)
{
    $newEmail = $_GET["newEmail"] ?? "";
    $userId = $_GET["userId"];

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        header("Location: ./profile.php?error=true&type=email&message=Invalid%20Email");
        exit;
    }

    if (emailTaken($newEmail, $conn)) {
        header("Location: ./profile.php?error=true&type=email&message=Email%20Taken");
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $newEmail, $userId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['email'] = $newEmail;

    header("Location: ./profile.php?succes=true&message=Email%20updated%20to:%20$newEmail");
    exit;
}

function changePwd(mysqli $conn)
{
    $newPwd = $_GET["newPwd"] ?? "";
    $userId = $_GET["userId"];

    if (strlen($newPwd) < 6) {
        header("Location: ./profile.php?error=true&type=pwdl&message=Password%20must%20be%20atleast%20six%20characters%20long");
        exit;
    }

    $_SESSION['pwd'] = $newPwd;

    $newPwd = password_hash($newPwd, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET pwd = ? WHERE id = ?");
    $stmt->bind_param("si", $newPwd, $userId);
    $stmt->execute();
    $stmt->close();

    header("Location: ./profile.php?succes=true&message=Password%20updated%20succesfully");
    exit;
}
