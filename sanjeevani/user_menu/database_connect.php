<?php
// Database connection
$servername = "localhost";
$username = "u515176669_demo34";
$password = "2;ic362;=M";
$database = "u515176669_demo34";

$con = mysqli_connect($servername, $username, $password, $database);

if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>