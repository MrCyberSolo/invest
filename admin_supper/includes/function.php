<?php
include('connect.php');
include('check-login.php');
$userid = $_SESSION['userid'];
if(isset($_GET['messages']))
{   
	$reply = mysqli_real_escape_string($con,$_GET['reply']);
	//$query = "INSERT INTO `message`(`user_id`, `subject`, `message`) VALUES('$userid','Customer','$reply')";
	$query_admin = mysqli_query($con,"INSERT INTO `message`(`user_id`,`subject`,`message`) VALUES ('$userid','Customer','$reply')");
	//$run_posts = mysqli_query($con,$query);
	echo "<script>window.open('../chat.php?admin=admin','_self')</script>";
}
?>