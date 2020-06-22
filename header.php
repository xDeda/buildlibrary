<?php

$servername = "localhost";
$dbusername = "dbusername";
$dbpassword = "dbpassword";
$dbname = "buildlibrary";

$link = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

$site_domain = "http://niclasjensen.dk";
$site_path = "/buildlibrary";
$site_url = $site_domain . $site_path;

?>
