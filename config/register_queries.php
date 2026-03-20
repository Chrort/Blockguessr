<?php

function usernameTaken($username, $conn)
{
    $sql = "SELECT * FROM users WHERE name LIKE '$username'";
    $result =  mysqli_query($conn, $sql);
    $return = true;
    mysqli_num_rows($result) > 0 ? $return = true : $return = false;
    mysqli_free_result($result);
    return $return;
}

function emailTaken($email, $conn)
{
    $sql = "SELECT * FROM users WHERE email LIKE '$email'";
    $result =  mysqli_query($conn, $sql);
    $return = true;
    mysqli_num_rows($result) > 0 ? $return = true : $return = false;
    mysqli_free_result($result);
    return $return;
}

function getUserId($conn, $username)
{
    $sql = "SELECT id FROM users WHERE name = '$username'";
    $result =  mysqli_query($conn, $sql);
    $return = mysqli_fetch_all($result, MYSQLI_NUM);
    return $return[0][0];
}
