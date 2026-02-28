<?php
// Database connection
$servername = "localhost";
$username = "u415747521_invest";
$password = "U415747521_invest";
$database = "u415747521_invest";

$con = mysqli_connect($servername, $username, $password, $database);

if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>