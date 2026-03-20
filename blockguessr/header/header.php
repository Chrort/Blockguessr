<?php

$username = $_SESSION["username"];

function getFirstLetter($string)
{
    return substr($string, 0, 1);
}


?>

<header>
    <a href="../startpage/startpage.php" id="goBack">
        <svg xmlns="http://www.w3.org/2000/svg" height="85%" viewBox="0 -960 960 960" width="85%" fill="#1f1f1f" style="position: absolute">
            <path d="M400-240 160-480l240-240 56 58-142 142h486v80H314l142 142-56 58Z" />
        </svg>
    </a>
    <a href="../startpage/startpage.php">BlockGuessr</a>
    <div id="userIcon"><?php echo htmlspecialchars(getFirstLetter($username)); ?></div>
    <nav>
        <div id="dropdown">
            <a href="../startpage/startpage.php" class="dropdownElement firstElement">Startpage</a>
            <a href="../profile/profile.php" class="dropdownElement">Profile</a>
            <a href="../addPano/add_pano.php" class="dropdownElement">Add Panorama</a>
            <a href="../deletePano/deletePano.php" class="dropdownElement">Delete Panorama</a>
            <a href="../startpage/logout.php" class="dropdownElement lastElement" id="logout">Logout</a>
        </div>
    </nav>
</header>

<script src="../header/header.js"></script>