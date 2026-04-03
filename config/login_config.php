<?php

if (isset($_POST["submit"]) && $_POST["submit"] == "Login") {

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
    $_SESSION["date"] = $userData[5];
    $_SESSION["role"] = $userData[6];

    $_SESSION['loggedIn'] = true;
    header("Location: ../blockguessr/startpage/startpage.php");
    exit();
} else {
    $_SESSION["error"] = "Unauthorized";
    goBack();
}
function goBack()
{
    $error = $_SESSION['error'] ?? "";
    header("Location: ../blockguessr/login/login.php?error=$error");
    exit();
}
