<?php
session_start();
if(isset($_SESSION['id']) && $_SESSION['login_type']=='user'){
}
else{
	//echo '<script>alert("Access denied");window.location.assign("login.php");</script>';
	header('Location: access_login.php');
}
?>