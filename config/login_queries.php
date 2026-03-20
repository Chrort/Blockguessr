<?php

function userExists($username, $conn)
{
    $sql = "SELECT * FROM users WHERE name = '$username'";
    $result =  mysqli_query($conn, $sql);
    $return = false;
    mysqli_num_rows($result) > 0 ? $return = true : $return = false;
    mysqli_free_result($result);
    return $return;
}

function getUserData($username, $conn)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_NUM);
    $stmt->close();
    return $data;
}
