<?php 
include('user_menu/database_connect.php');
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>forget password</title>

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
    padding: 11px 20px;
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

.send at{
    background-color: #009a5f;
    padding: 4px 13px;
    text-decoration: none;
    color: white;
    border-radius:  5px;
    
}
.popup {
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background-color: rgba(0, 0, 0, 0.7);
	color: white;
	padding: 20px;
	border-radius: 10px;
	text-align: center;
}






    
    </style>
  </head>
  <body>
<div class="appCapsule container">
    <header>
        <div class="text-white py-2">
            <!-- <a href=""><i class="bi bi-gear text-white"></i></a> -->
            <a href="login.php" class="nav-link text-white d-flex align-items-center gap-1">
            <i class="bi bi-chevron-left"></i>
            <small>Back</small>
        </a>
        </div>

        
        <div class="header-title text-center">
            <!-- <h5>Register</h5> -->
            <img src="https://png.pngtree.com/png-vector/20231127/ourmid/pngtree-demo-red-flat-icon-isolated-demo-icon-png-image_10722763.png" class="img-fluid" style="width: 100px; margin: 10px 0; border-radius: 50%;" alt="">
        </div>

  </header>

  <div class="row mt-4 formBox px-2">
  <?php if(isset($_GET['mobile'])){
		$mobile = $_GET['mobile']; 
		?>
    <form action="" method="post" enctype="multipart/form-data">
    <div class="col-12">
        <div class="fomtBox">
            <div class="input-group form-control rounded-pill py-1 ">
              <img src="img/me/phone.png" alt="">
              <p>+ 91 </p>
                <input type="number" name="mobile" class="form-control border-0" value="<?php echo $mobile;?>" readonly required>
            </div>
            <div class="input-group mt-4 form-control rounded-pill py-1 ">
                <img src="img/me/capcha.png" alt="">
                <input type="text" name="vcode" class="form-control border-0 " placeholder="please enter" >
               <div class="send">
               <button type="submit" name="number" style="background-color: #009a5f; padding: 3px 12px; text-decoration: none; border:none; color: white; border-radius: 5px;" id="startButton"> <div id="timer">Send</div></button>
               </div>
                </div>
            </div>
            <div class="input-group mt-4 form-control rounded-pill py-1">
              <img src="img/me/password.png" alt="">
                <input type="password" name="password"  class="form-control border-0" placeholder="Please enter the new password" >
            </div>
            <div class="input-group mt-4 form-control rounded-pill py-1">
                <img src="img/me/invite.png" alt="">
                  <input type="password" name="c_password" class="form-control border-0" placeholder="Confirm new password" >
              </div>
            <div class="btnBox d-grid mt-4">
              <button style="background-color: #000;" name="change" class="btn text-white p-2 fs-5 rounded-pill">Retrieve password</button>
            </div>
          </div>        
    </div>
  </form>
    <?php }else{ ?>
      <form action="" method="post" enctype="multipart/form-data">
      <div class="col-12">
        <div class="fomtBox">
            <div class="input-group form-control rounded-pill py-1 ">
              <img src="img/me/phone.png" alt="">
              <p>+ 91 </p>
                <input type="number" name="mobile" class="form-control border-0" placeholder="Please enter mobile number" >
            </div>
            <div class="input-group mt-4 form-control rounded-pill py-1 ">
                <img src="img/me/capcha.png" alt="">
                <input type="password" class="form-control border-0 " placeholder="please enter code">
               <div class="send">
                <button type="submit" name="number" style="background-color: #009a5f; padding: 3px 12px; border-color: #0dcaf0; text-decoration: none; border:none; color: white; border-radius: 5px;">Send</button>
               </div>
                </div>
            </div>
            <div class="input-group mt-4 form-control rounded-pill py-1">
              <img src="img/me/password.png" alt="">
                <input type="password" class="form-control border-0" placeholder="Please enter the new password">
            </div>
            <div class="input-group mt-4 form-control rounded-pill py-1">
                <img src="img/me/invite.png" alt="">
                  <input type="password" class="form-control border-0" placeholder="Confirm new password">
              </div>
            <div class="btnBox d-grid mt-4">
              <button style="background-color: #000;" class="btn text-white p-2 fs-5 rounded-pill">Retrieve password</button>
            </div>
          </div>        
    </div>
    </form>
    <?php  } ?>
  </div>

<!--count down timer ================= -->
  <script>
function startCountdown() {
  let countdownTime = 60; 
  const timerDisplay = document.getElementById('timer');
  const startButton = document.getElementById('startButton');

  startButton.disabled = true;

  function countdown() {
    timerDisplay.textContent = formatTime(countdownTime);

    countdownTime--;

    if (countdownTime >= 0) {
      setTimeout(countdown, 1000);
    } else {
      timerDisplay.textContent = "Send";
      startButton.disabled = false;
    }
  }

  function formatTime(timeInSeconds) {
    const minutes = Math.floor(timeInSeconds / 60);
    const seconds = timeInSeconds % 60;
    return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
  }

  countdown();
}
  
document.getElementById('startButton').addEventListener('click', startCountdown);

// this is for refresh page code ====
window.onload = startCountdown;
 </script>









  

</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
<?php
if(isset($_POST['change']))
{
	$mobile = $_POST['mobile'];
	$vcode = $_POST['vcode'];
	$password = $_POST['password'];
	$c_password = $_POST['c_password'];
	if($password == $c_password ){
		$query_opt = mysqli_fetch_array(mysqli_query($con,"select * from mobileotp where otp_status='Active' AND otp_mobile='$mobile' order by `otp_id` desc LIMIT 0,1 "));
		$confirm_code = $query_opt['otp_number'];
		if($vcode == $confirm_code){
			mysqli_query($con,"UPDATE `user` SET `password`='$password' WHERE `mobile`='$mobile'");
			mysqli_query($con,"update mobileotp set otp_status ='Deactive' where otp_mobile ='$mobile'");
      echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					var popup = document.createElement('div');
					popup.className = 'popup';
					popup.innerHTML = 'Password updated successfully';
					document.body.appendChild(popup);
					setTimeout(function() {
						document.body.removeChild(popup);
						window.open('login.php','_self');
					}, 3000); // 3000 milliseconds = 3 seconds
				});
				</script>";
		}else{
			echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					var popup = document.createElement('div');
					popup.className = 'popup';
					popup.innerHTML = 'Verification code does not match';
					document.body.appendChild(popup);
					setTimeout(function() {
						document.body.removeChild(popup);
						window.open('forget-password.php?mobile=".$mobile."','_self');
					}, 3000);
				});
				</script>";
		}
	}else{
		echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Passwords do not match';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('forget-password.php?mobile=".$mobile."','_self');
				}, 3000);
			});
			</script>";
	}
}
?>


<?php
if(isset($_POST['number']))
{
	$mobile = $_POST['mobile'];
	if($mobile!=''){
    $new_pins = rand(1000,9999); 
		$query = mysqli_query($con,"insert into mobileotp (`otp_mobile`,`otp_number`,`otp_date`) values('$mobile','$new_pins',Now())");	
	//SMS Code Started here 
		$fields = array(
			"sender_id" => "198",
			"variables_values" => $new_pins,
			"numbers" => $mobile
		);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://blacksms.in/sms",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode($fields),
		CURLOPT_HTTPHEADER => array(
			"Authorization: df3d053b8399b02ed070a0b3b7e1b82f",
			"Content-Type: application/json"
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		 "cURL Error #:" . $err;
		} else {
		 $response;
		}
		//	echo "<script>window.open('forget-password.php?mobile=".$mobile."','_self')</script>";
			echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Otp Send Your Mobile Number';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('forget-password.php?mobile=".$mobile."','_self');
				}, 3000);
			});
			</script>";
}else{
    echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Missing Mobile Number';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('register.php','_self');
				}, 3000);
			});
			</script>";
}
    
}
		// -----------   DataBase Code End Here ------
?>