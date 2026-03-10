<?php
session_start();

// Check if user is already logged in
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: home.php");
    exit;
}
include('user_menu/database_connect.php');

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $vcode = $_POST['vcode'];
    $code = $_POST['code'];

    // Prepare a SQL statement
    $sql = "SELECT user_id, mobile, password FROM user WHERE mobile = ?";
    
    if($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;
        
        if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            // Check if username exists, then verify password
            if(mysqli_stmt_num_rows($stmt) == 1) {                    
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if(mysqli_stmt_fetch($stmt)) {
                  $query_user = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `mobile`='$username'"));
			              $data_base_password = $query_user['password'];
			              $email = $query_user['email'];
			              $access_login = $query_user['user_access'];
                    if($password== $data_base_password) {

                             if($vcode== $code) {
                              // Password is correct, start a new session
                              if($access_login=='access'){
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $email;    
                    		      // Redirect user to dashboard page
                              //$popupMessage = '<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 20px; border-radius: 10px; z-index: 9999;">Login success!</div>';

                                // Display the popup message
                                //echo $popupMessage;
                            
                                // Wait for 5 seconds
                                //header("refresh:3; url=home.php");
                                header("location: home.php");
                              }else{
                                  $login_err = "This account is forbidden to log in";
                              }
                         }else{
                          $login_err = "Please enter the graphic verification code";
                         }                   	    
                        
                    } else {
                        // Password is not valid
                        $login_err = "Please enter login password";
                    }
                }
            } else {
                // Username doesn't exist
                $login_err = "Wrong account or password ";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($con);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>login</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#009a5f;
        --light-blue:#009a5f;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule, .footerBox{
        max-width: 641px;
        /* background-color: #009a5f; */
        margin: auto;

       }

       .appCapsule{
        padding-bottom: 5rem;
       }

       /* body{
        background-color: #009a5f;
       } */

       

header .line{
  width: 30px;
  height: 2px;
  border-radius: 40px;
  background-color: var(--yellow);
  margin:5px auto;
}



.formBox input{
    padding: 11px 10px;
    outline: transparent;
    border: transparent;
    display: flex;
    align-items: center;
    box-shadow:none !important;
    
}



.formBox img{
  width: 30px;
  height: 30px;
}


.input-group{
  display: flex;
  justify-content: center;
  align-items: center;
}

.formBox ::placeholder{
  font-size: 15px;
}

.btnBox a{
  background-color: #202020;
}
.popup {
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background-color: rgba(0, 0, 0, 0.7);
	color: white;
	padding: 7px;
	border-radius: 3px;
	text-align: center;
}

    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container">
    <header>
        <!-- <div class="setting text-white text-end py-3 fs-5 px-2">
            <a href="login.php"><i class="bi bi-gear text-white"></i></a>
        </div> -->
        <div class="header-title text-center pt-5" >
            <!-- <h5>Login</h5> -->
            <img src="img/hikoki_logo.png" class="img-fluid" style="width: 100px; margin: 10px 0; border-radius: 50%;" alt="">
        </div>
        <center style="color: red">
          <?php
            if(isset($login_err)) {
               // echo '<p>' . $login_err . '</p>';
           
            echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					var popup = document.createElement('div');
					popup.className = 'popup';
					popup.innerHTML = '$login_err';
					document.body.appendChild(popup);
					setTimeout(function() {
						document.body.removeChild(popup);
						window.open('login.php','_self');
					}, 3000); // 3000 milliseconds = 3 seconds
				});
				</script>";
      }
          ?>
        </center>

  </header>

  <div class="row mt-4 formBox px-2">
    <div class="col-12">
        <div class="fomtBox">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group form-control rounded-pill py-1 ">
              <img src="img/me/phone.png" alt="">
              <p>+ 91 </p>
                <input type="text" name="username" class="form-control border-0" placeholder="Please enter mobile number">
            </div>

            <div class="input-group mt-4 form-control rounded-pill py-1">
              <img src="img/me/password.png" alt="">
                <input type="password" class="form-control border-0" name="password" placeholder="Please enter the login password">
            </div>

            <div class="input-group mt-4 form-control rounded-pill py-1 ">
              <img src="img/me/capcha.png" alt="">
              <input type="text" class="form-control border-0 " name="vcode" placeholder="please enter">
              <div class="chaptch px-2" style="background-image: url(img/me/captcha-bg.png); color:sienna; font-weight: bold;">
               <?php $captcha_views = rand(1000,9999); ?>  
               <input type="hidden" class="form-control border-0" name="code" value="<?php echo $captcha_views; ?>">
                <p class="fs-4 m-0"><?php echo $captcha_views; ?></p>
              </div>
            </div>

            <div class="btnBox d-grid mt-4">
              <button type="submit" value="Login" class="btn text-white p-2 fs-5 rounded-pill" style="background-color: #202020;">Sign in</button>
            </div>
          </form>
            <div class="botlink mt-2 d-flex justify-content-between">
              <a href="forget-password.php" class="nav-link text-light">Forget password</a>
              <a href="register.php" class="nav-link text-light">Register now</a>
            </div>
        </div>        
    </div>
  </div>
</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>