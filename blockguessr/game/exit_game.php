<?php
session_start();

require './delete_panos.php';

deletePanos($_SESSION['username']);

header("Location: ../startpage/startpage.php");
