<?php 
$hostname = "localhost"; // Host name 
$dbUser = "root";
$dbPassword = "";
$dbName = "bricoledb";
$conn = mysqli_connect($hostname, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
