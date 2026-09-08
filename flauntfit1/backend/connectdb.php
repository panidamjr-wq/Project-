<?php
$servername = "localhost";
$username = "root";
$pwd="";
$dbname = "flauntfin";

// เชื่อมต่อ
$conn = mysqli_connect($servername, $username, $pwd, $dbname);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
