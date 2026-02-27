<?php
$host = "localhost";
$user = "u515176669_demo34";
$password = "2;ic362;=M";
$db = "u515176669_demo34";


$con = mysqli_connect($host,$user,$password) or die("Could not connect to database");
mysqli_select_db($con,$db) or die("No database selected");

?>