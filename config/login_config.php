<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    session_start();

    unset($_SESSION["username"], $_SESSION["email"], $_SESSION["pwd"]);

    $username = $_POST["username"];
    $pwd = $_POST["pwd"];

    $_SESSION["username"] = $username;
    $_SESSION["pwd"] = $pwd;

    require_once './login_queries.php';
    require_once './db_connect.php';

    // ERROR HANDLERS

    if (empty($username) || empty($pwd)) {
        $_SESSION["error"] = "Missing input";
        goBack();
    }

    if (!userExists($username, $conn)) {
        $_SESSION["error"] = "User doesn't exists";
        goBack();
    }

    $userData = getUserData($username, $conn)[0];

    if (!password_verify($pwd, $userData[3])) {
        $_SESSION["error"] = "Wrong password or username";
        goBack();
    }

    $_SESSION["id"] = $userData[0];
    $_SESSION["username"] = $userData[1];
    $_SESSION["email"] = $userData[2];
    $_SESSION["pwd"] = $pwd;
    $_SESSION["date"] = $userData[4];

    $_SESSION['loggedIn'] = true;
    header("Location: ../blockguessr/startpage/startpage.php");
    exit();
} else {
    goBack();
}

function goBack()
{
    header("Location: ../blockguessr/login/login.php?failed");
    exit();
}
