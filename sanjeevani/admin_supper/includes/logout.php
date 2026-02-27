<?php
session_start();
session_destroy();
echo '<script>window.location.assign("../access_login.php");</script>';
?>