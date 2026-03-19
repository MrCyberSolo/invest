<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
date_default_timezone_set('Asia/Kolkata');

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redeem bonus</title>

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

       .appCapsule{
        max-width: 641px;
        margin: auto;
        background-image: url(img/bonus.png);
        min-height: 100vh;
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        justify-content: center;


       }
/* 
       header{
        background-color: #009a5f;

       } */
       
         body{
        background-color: #F6932C;

       }
       
       .appBody{
        padding: 4.5rem 0;
        justify-content: center;
        display: flex;
        align-items: end;
        
       }

       .appBody .inputBox{
        max-width: 350px;
        position: relative;
        /* bottom: 3rem; */
        top: 2rem;
       }

       .appBody .inputBox .bord{
        width: 100%;
       
       }

       .rec-btn button:active{
        transform: scale(1.1);
      
       }



.popup {
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background-color: rgba(0, 0, 0, 0.7);
	color: white;
	padding: 5px;
	border-radius: 10px;
	text-align: center;
}
@keyframes fadeInOut {
    0%, 100% {
        opacity: 0;
    }
    10%, 90% {
        opacity: 1;
    }
}

	 
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container">
  <header class="row fixed-top">
    <div class="text-white py-3">
            <div class=" px-3  d-flex align-items-center justify-content-between">
                <a href="javascript:history.back()" class="text-decoration-none text-white left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6>Redeem bonus</h6>

                <a href="funding-details.php" style="text-decoration: none; color:#fff"><h6></h6></a>
        </div>
        </div>

  </header>

  <div class="row appBody">
   <div class="col-12 inputBox px-3 text-center" >
      <form action="" method="post" enctype="multipart/form-data">
        <div class="code" style="position: relative; top: 2rem; left: 3rem; display: inline;">
            <img src="img/bonuscode.png"  alt="" style="width: 150px;">
            <b class="" style="position: relative;right: 7.4rem ; color: white;">Bonus Code</b>
        </div>
        <div class="bordBox">
            <img src="img/bonusboard.png" class="bord" alt="">
            <input type="text" placeholder="Please enter red envelope code"  name="code" class="form-control"  style="position: relative; width: 290px;  bottom: 6rem; left: 1rem;">
        </div>
        <div class="rec-btn">
          <button type="submit" class="btn border-0" name="gift_public" >
            <img src="img/bonusreceive.png" alt="" class="img-fluid">
            <p style="position: relative; bottom: 2.6rem; font-size: 18px; color: white;;"  >Receive</p>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

   
  </body>
</html>
<?php 
if(isset($_POST['gift_public'])){

$mysqltime = date('Y-m-d H:i:s');
	$code = $_POST['code'];
	if($code!=''){
	$gift_code =  mysqli_num_rows(mysqli_query($con,"SELECT * FROM `coupan_all` WHERE `ca_code`='$code' AND ca_status='Active'"));
		if($gift_code=='1'){
	$query12 =  mysqli_num_rows(mysqli_query($con,"SELECT * FROM `coupan_tra` WHERE `ct_userid`='$userid_access' AND `ct_dode`='$code'"));
	if($query12 <= 0){
	 
		    $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `coupan_all` WHERE `ca_code`='$code'"));
		    $ca_amount = $query['ca_amount'];
		    $today_date = date("Y-m-d");
	            mysqli_query($con,"INSERT INTO `coupan_tra`(`ct_userid`, `ct_dode`, `ct_amount`) VALUES('$userid_access','$code','$ca_amount')");
	            mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+$ca_amount WHERE `userid`='$userid_access'");
	            mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$userid_access','Redeem Gift','$ca_amount','Credit','$mysqltime')");
	
	       // echo "<script>alert ('Your Gift Card Add Succeffully')</script>";
			//echo "<script>window.open('gift.php','_self')</script>";
            echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Received Successfully';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('redeembonus.php','_self');
				}, 3000);
			});
			</script>";
}else{
         echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Invaild enveloped code';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('redeembonus.php','_self');
				}, 3000);
			});
			</script>";
}}else{
            echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'The red envelope code is incorrect';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('redeembonus','_self');
				}, 3000);
			});
			</script>";
}}else{
     echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Please enter Red envelope code';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('redeembonus','_self');
				}, 3000);
			});
			</script>";
}
    
}

?>