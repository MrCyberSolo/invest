<html>
    <head></head>
    <body>
    <?php
session_start();
require('connect.php');

$email = mysqli_real_escape_string($con,$_POST['email']);
//$email= substr($emails, 3);
$password = mysqli_real_escape_string($con,$_POST['password']);
$captcha = mysqli_real_escape_string($con,$_POST['captcha']); 
$captcha_views = mysqli_real_escape_string($con,$_POST['captcha_views']); 
if($captcha == $captcha_views)
{
    $query = mysqli_query($con,"select * from admin where userid='$email' and password='$password'");
    if(mysqli_num_rows($query)>0)
        {
            $_SESSION['userid'] = $email;
            $_SESSION['id'] = session_id();
            $_SESSION['login_type'] = "user";
            
            echo '<script>window.location.assign("../dashboard.php");</script>';
        }
    else
        {	
            echo '<script>alert("Email id or password is worng.");window.location.assign("../access_login.php");</script>';
        }
}else{
    echo '<script>alert("Please Check captcha Code!");window.location.assign("../access_login.php");</script>';
}
?>
   
</body>
</html>
