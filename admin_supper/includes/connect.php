<?php
$host = "localhost";
$user = "u415747521_invest";
$password = "U415747521_invest";
$db = "u415747521_invest";


$con = mysqli_connect($host,$user,$password) or die("Could not connect to database");
mysqli_select_db($con,$db) or die("No database selected");

?>