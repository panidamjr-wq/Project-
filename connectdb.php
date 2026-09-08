<?php
$servername = "localhost";
$username = "root";
$pwd = "";
$dbname = "flauntfin";

$conn = mysqli_connect($servername, $username, $pwd, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>