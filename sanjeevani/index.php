<?php
session_start();

// Check if user is already logged in
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: me");
    exit;
}else{
    header("location: login");
}
?>