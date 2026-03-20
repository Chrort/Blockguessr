<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    session_start();

    unset($_SESSION["username"], $_SESSION["email"], $_SESSION["pwd"], $_SESSION["pwdRepeat"]);

    $username = $_SESSION["username"] = $_POST["username"];
    $email = $_SESSION["email"] = $_POST["email"];
    $pwd = $_SESSION["pwd"] = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdRepeat"];

    require_once './register_queries.php';
    require_once './db_connect.php';

    // ERROR HANDLERS
    if (empty($username) || empty($email) || empty($pwd) || empty($pwdRepeat)) {
        $_SESSION["error"] = "Missing input";
        goBack();
    }

    if (usernameTaken($username, $conn)) {
        $_SESSION["error"] = "Username already exists";
        goBack();
    }

    if (strlen($username) > 15) {
        $_SESSION["error"] = "Username too long";
        goBack();
    }

    if (strlen($username) < 3) {
        $_SESSION["error"] = "Username too short";
        goBack();
    }

    if (emailTaken($email, $conn)) {
        $_SESSION["error"] = "Email already exists";
        goBack();
    }

    if ($pwd != $pwdRepeat) {
        $_SESSION["error"] = "Passwords aren't matching";
        goBack();
    }

    if (strlen($pwd) < 6) {
        $_SESSION["error"] = "Password must be atleast 6 characters";
        goBack();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    try {

        $stmt = $conn->prepare("INSERT INTO users (name, email, pwd) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPwd);
        $stmt->execute();
        $stmt->close();

        addHighscoreRow($conn, $username);

        session_destroy();
        header("Location: ../blockguessr/login/login.php?Registration_succes!");
        exit();
    } catch (Exception $e) {
        header("Location: ../blockguessr/register/register.php?Registration_failed!");
        echo $e;
        exit();
    }
} else {
    goBack();
}

function goBack()
{
    header("Location: ../blockguessr/register/register.php");
    exit();
}

function addHighscoreRow($conn, $username)
{

    $id = getUserId($conn, $username);

    $stmt = $conn->prepare("INSERT INTO highscores (id) VALUES (?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
