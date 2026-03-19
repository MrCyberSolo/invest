<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
 $userid_access = $_SESSION['username'];
$get_user = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$userid_access'"));
	$name = $get_user['name'];
	$mobile = $get_user['mobile'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Retrive password</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:black;
        --light-blue:#009a5f;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule{
        max-width: 641px;
        margin: auto;

       }

       header{
        background-color: #005a36;

       }

       
       .appBody{
        padding: 4.5rem 0;
       }

       /* body{
        background-color: #009a5f;
       } */

       /* input-section================================  */
       

.inputBox .inner{
  display: flex; 
  align-items: center;
  border-bottom:1px solid #aeaeae84;
  padding: 8px 0;
}

.inputBox p{
  width: 30%;
  margin-right: 1rem;
  color: #7c7c7c;
}
.inputBox .right{
  width: 100%;
  /*background-color:red;*/
}
.inputBox input{
  border: 0;
  width: 100%;
  background-color: transparent;
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
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container">
  <header class="row fixed-top">
        <div class="text-white py-3">
            <a href="javascript:history.back()" class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <div class="left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </div>

                <h5>Retrive password</h5>

                <div class="px-3"></div>

        </a>
        </div>

  </header>
	<?php if(isset($_GET['mobile'])){
		$mobile = $_GET['mobile']; 
		?>
		
		
  <div class="row appBody ">
  <form action="" method="post" enctype="multipart/form-data">
    <div class="col-12  input-section px-4" style="background-color: #f8f9fa; padding: 10px; ">
      <div class="inputBox">
        <div class="inner">
          <p>Phone</p>
          <div class="right">
            <input type="text" class="form-control" name="mobile" value="<?php echo $mobile; ?>" readonly>
          </div>
        </div>
        <div class="inner d-flex align-items-center">
            <p>SMS</p>
          <div class="right d-flex gap-2 align-items-center">
            <input type="text" class="form-control" name="vcode" placeholder="Varification code">
            <button type="submit" name="number"  style="border: 1px solid red; color: red; background-color: transparent; border-radius: 5px; font-size: 10px; height: 28px; padding: 0 10px;;"id="startButton"> <div id="timer">Send</div></button>
          </div>
        </div>
        <div class="inner">
          <p>Password</p>
          <div class="right">
            <input type="password" name="password" class="form-control" placeholder="New password">
          </div>
        </div>
        <div class="inner">
          <p>Confirm</p>
          <div class="right">
            <input type="password" name="c_password" class="form-control" placeholder="Confirm password" >
          </div>
        </div>
        </div>
        <div class="inputBox-three mt-3">
          <div class="sub-btn px-3 d-grid mt-3">
            <button type="submit" name="change"  class="btn" style="background: black; color: white; border-radius: 30px; padding: 10px; ;;">Change</button>
          </div>
        </div>
      </form>       
    </div>
  </div>
  <?php } else{ ?> 
    <div class="row appBody ">
  <form action="" method="post" enctype="multipart/form-data">
    <div class="col-12  input-section px-4" style="background-color: #f8f9fa; padding: 10px; ">
      <div class="inputBox">
        <div class="inner">
          <p>Phone</p>
          <div class="right">
            <input type="text" class="form-control" name="mobile" value="<?php echo $mobile; ?>" readonly>
          </div>
        </div>
        <div class="inner d-flex align-items-center">
            <p>SMS</p>
          <div class="right d-flex gap-2 align-items-center">
            <input type="text" class="form-control" placeholder="Varification code">
            <button type="submit" name="number" class="" style="border: 1px solid red; color: red; background-color: transparent; border-radius: 5px; font-size: 10px; height: 28px; padding: 0 10px;;">Send</button>
          </div>
        </div>
        <div class="inner">
          <p>Password</p>
          <div class="right">
            <input type="text" class="form-control" placeholder="New password">
          </div>
        </div>
        <div class="inner">
          <p>Confirm</p>
          <div class="right">
            <input type="text" class="form-control" placeholder="Confirm password" >
          </div>
        </div>
        </div>
        <div class="inputBox-three mt-3">
          <div class="sub-btn px-3 d-grid mt-3">
            <button class="btn" style="background: black; color: white; border-radius: 30px; padding: 10px; ;;">Submit</button>
          </div>
        </div>
      </form>       
    </div>
  </div>
  
    <?php } ?>

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

    <!-- amount tab ===================== -->
    <script>
        function openAmt(evt, amtName) {
          var i, inner, amtTablinks;
          inner = document.getElementsByClassName("inner");
          for (i = 0; i < inner.length; i++) {
            inner[i].style.display = "none";
          }
          amtTablinks = document.getElementsByClassName("amtTablinks");
          for (i = 0; i < amtTablinks.length; i++) {
            amtTablinks[i].className = amtTablinks[i].className.replace(" active", "");
          }
          document.getElementById(amtName).style.display = "block";
          evt.currentTarget.className += " active";
        }
        </script>
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
			mysqli_query($con,"UPDATE `user` SET `password`='$password' WHERE `email`='$userid_access'");
			mysqli_query($con,"update mobileotp set otp_status ='Deactive' where otp_mobile ='$mobile'");
      echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					var popup = document.createElement('div');
					popup.className = 'popup';
					popup.innerHTML = 'Password updated successfully';
					document.body.appendChild(popup);
					setTimeout(function() {
						document.body.removeChild(popup);
						window.open('security.php','_self');
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
						window.open('password.php?mobile=".$mobile."','_self');
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
					window.open('password.php?mobile=".$mobile."','_self');
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
    $new_pins = rand(1000,9999); 
		$query = mysqli_query($con,"insert into mobileotp (`otp_mobile`,`otp_number`,`otp_date`) values('$mobile','$new_pins',Now())");	
		//SMS Code Started here 
		// API endpoint URL
$url = 'https://login.99smsservice.com/sms/api?action=send-sms';

// API key (replace 'your_api_key' with your actual API key)
$api_key = 'd3JMYkFnYklJempmTHRwdXNmY3A=';

// Sender ID
$from = 'TKINEN';

// Recipient's phone number
//$to = '918478884111';
$country_code = '91';
$to = $country_code . $mobile;

// Message content
$message = 'Dear User, Your OTP is '."$new_pins".'. Valid for 30 minutes. Please do not share this OTP. Regards Ocean Food T.K.INDUSTRIAL';

// Entity ID
$p_entity_id = '1201162643300643505';

// Template ID
$temp_id = '1207169657387094956';

// Build the request parameters
$params = array(
    'api_key' => $api_key,
    'to' => $to,
    'from' => $from,
    'sms' => $message,
    'p_entity_id' => $p_entity_id,
    'temp_id' => $temp_id
);

// Initialize cURL session
$ch = curl_init($url);

// Set the request method to POST
curl_setopt($ch, CURLOPT_POST, 1);

// Set the POST data
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

// Set the response output to a variable
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL session and fetch the response
$response = curl_exec($ch);

// Check for cURL errors
if(curl_errno($ch)){
    echo 'Error: ' . curl_error($ch);
}

// Close the cURL session
curl_close($ch);

// Display the API response
 $response;
			//echo "<script>window.open('password.php?mobile=".$mobile."','_self')</script>";
				echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Otp Send Your Mobile Number';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('password.php?mobile=".$mobile."','_self');
				}, 3000);
			});
			</script>";
}
		// -----------   DataBase Code End Here ------
?>