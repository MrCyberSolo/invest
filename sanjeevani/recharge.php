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
$get_balance = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `income` WHERE `userid`='$userid_access'"));
	$current_bal = $get_balance['current_bal'];
	$fran_bal = $get_balance['fran_bal'];
	date_default_timezone_set('Asia/Kolkata');
	?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recharge</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#07CCFF;
        --light-blue:#0061bf;
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
        /* background-color: #07CCFF; */
        background-color: #014f97;

       }

       .appBody{
        padding: 4.5rem 0;
       }

       


       /* body{
        background-color: #07CCFF;
       } */

       /* amount card =========================== */
       .amtTab{
        display: flex; justify-content: space-around;
        gap: 15px;

       }

       .amtTab button{
        text-decoration: none;
        color:rgb(19, 19, 19);
        background-color: #FFF1F1;
        width: 100%;
        text-align: center;
        margin: 5px 0;
        padding: 8px;
        border-radius: 4px;
        border: none;

       } 

.amtTab button:hover {
  background-color: #E48B00;
}

/* Create an active/current amtTablink class */
.amtTab button.active {
  background-color: #E48B00;
}

.amtTabcontent{
  background-color:#FCFAED;
  height: 50px;
  padding: 9px;
  border-radius: 8px;
}
.amtTabcontent .inner {
  display: none;
  padding: 6px 12px;
  border-top: none;
}


/* PAYMENT CHANNEL SECTION ============================ */
.payOption .checkbox {
          position: relative;
          overflow: hidden;
        }
        
        .payOption .checkbox__input {
          position: absolute;
          top: -100px;
          left: -100px;
        }
         
        .payOption .checkbox__inner {
          display: inline-block;
          width: 20px;
          height: 20px;
          border-radius: 50%;
          border: 1px solid #626262;
          background: transparent no-repeat center;
        }
        
        .payOption .checkbox__input:checked + .checkbox__inner {
          /* border-color: #52C6C4; */
          background-color: #242a26;
          background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='UTF-8'%3F%3E%3Csvg width='14px' height='10px' viewBox='0 0 14 10' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'%3E%3C!-- Generator: Sketch 59.1 (86144) - https://sketch.com --%3E%3Ctitle%3Echeck%3C/title%3E%3Cdesc%3ECreated with Sketch.%3C/desc%3E%3Cg id='Page-1' stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'%3E%3Cg id='ios_modification' transform='translate(-27.000000, -191.000000)' fill='%23FFFFFF' fill-rule='nonzero'%3E%3Cg id='Group-Copy' transform='translate(0.000000, 164.000000)'%3E%3Cg id='ic-check-18px' transform='translate(25.000000, 23.000000)'%3E%3Cpolygon id='check' points='6.61 11.89 3.5 8.78 2.44 9.84 6.61 14 15.56 5.05 14.5 4'%3E%3C/polygon%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
          background-size: 12px 12px;
        }
        
        
        
        
          .payOption p{
                margin: 0;
            }
        
            .payOption{
                padding: 10px 0;
                
            }
        
        
            .payOption .inner{
                border-bottom:1px solid #aeaeae28;
                padding: 1rem ;
                

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


       
    
    </style>
  </head>
  <body>
<div class="appCapsule container">
  <header class="row fixed-top">
        <div class="text-white py-3 ">
          <div   class="px-3 text-decoration-none  text-white d-flex align-items-center justify-content-between">
               <a href="me.php" class="left d-flex align-items-center nav-link">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
               </a>

                <h6>Recharge</h6>
              <a href="recharge-records.php" class="nav-link">
                <h6>Records</h6>
              </a>
        </div>
        </div>

  </header>

  <div class="row appBody px-3">
    <div class="col-12 tab-btn p-3 rounded rounded-3 bg-white ">
        
        <form action="" method="post" enctype="multipart/form-data">
    <div class="col-12 tab-btn px-3">
        <b>Amount</b>
        <div class="amtTab">
            <button type="button" class="amtTablinks" onclick="setAmount(620)">620</button>
            <button type="button" class="amtTablinks" onclick="setAmount(2560)">2560</button>
            <button type="button" class="amtTablinks" onclick="setAmount(3000)">3000</button>
   
        </div>
        <div class="amtTab">
            <button type="button" class="amtTablinks" onclick="setAmount(7800)">7800</button>
            <button type="button" class="amtTablinks" onclick="setAmount(18500)">18500</button>
            <button type="button" class="amtTablinks" onclick="setAmount(27500)">27500</button>
        </div>
    </div>
    <div class="col-12 mt-3">
        <input type="number" class="form-control" id="amount" name="amount" placeholder="Please enter amount">
    </div>

    <div class="col-12">
        <div class="payOption">
            <h5 class="px-3 pt-2">Payment channel</h5>
            

            <div class="inner text-dark d-flex justify-content-between">
                <div class="chTitle d-flex gap-2 align-items-center">
                    <img src="img/me/rs.png" alt="" style="width: 27px;">
                    <p class="m-0">P-pay1</p>
                </div>
                <label class="checkbox">
                    <input type="radio" name="ptype" value="offline" class="checkbox__input"/>
                    <span class="checkbox__inner"></span>
                </label>
            </div>
           <!-- <div class="inner text-dark d-flex justify-content-between">
                <div class="chTitle d-flex gap-2 align-items-center">
                    <img src="img/me/rs.png" alt="" style="width: 27px;">
                    <p class="m-0">U-Pay</p>
                </div>
                <label class="checkbox">
                    <input type="radio" name="ptype" value="online" class="checkbox__input"/>
                    <span class="checkbox__inner"></span>
                </label>
            </div>-->
          
              <div class="inner text-dark d-flex justify-content-between">
                <div class="chTitle d-flex gap-2 align-items-center">
                    <img src="img/me/rs.png" alt="" style="width: 27px;">
                    <p class="m-0">S-Pay2</p>
                </div>
                <label class="checkbox">
                    <input type="radio" name="ptype" value="online2" class="checkbox__input"/>
                    <span class="checkbox__inner"></span>
                </label>
            </div>
            <!-- <div class="inner text-dark d-flex justify-content-between">
                <div class="chTitle d-flex gap-2 align-items-center">
                    <img src="img/me/rs.png" alt="" style="width: 27px;">
                    <p class="m-0">U-Pay</p>
                </div>
                <label class="checkbox">
                    <input type="radio" name="ptype" value="online" class="checkbox__input"/>
                    <span class="checkbox__inner"></span>
                </label>
            </div>-->
            
             <!--<div class="inner text-dark d-flex justify-content-between">
                <div class="chTitle d-flex gap-2 align-items-center">
                    <img src="img/me/rs.png" alt="" style="width: 27px;">
                    <p class="m-0">P-Pay4</p>
                </div>
                <label class="checkbox">
                    <input type="radio" name="ptype" value="offline3" class="checkbox__input"/>
                    <span class="checkbox__inner"></span>
                </label>
            </div>-->
        </div> <!-- payOption end -->

        <div class="sub-btn px-3 d-grid mt-2">
            <button type="submit" name="upload_pay" class="btn" style="background: black; color: white; border-radius: 30px; padding: 10px;">Submit</button>
        </div>
    </div>
</form>

<script>
    function setAmount(value) {
        document.getElementById('amount').value = value;
    }
</script>

       <div class="tips mt-3 ">
        <p>Imporatant tip:</p>
        <br>
        <p>
            Please do not save the same payment account and make repeated payments.</p>
            <br>
            <p>
                For each payment, please click to enter the payment channel and copy the latest payment account</p>
       </div>
  </div>


</div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- amount tab ===================== -->
  </body>
</html>
<?php 
 if(isset($_POST['upload_pay']))
 {
     $pay_id = $userid;	
     $ptype = $_POST['ptype'];
     $amount = $_POST['amount'];
     if($amount >=500){
     if($amount!=''){
     if($ptype!=''){
     if($ptype=='online'){
         $_SESSION['amount'] = $amount;
         
         //$URL = "payment/recharge_online.php?amount=$amount";
		//	echo "<script>location.href='$URL'</script>";
			echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Recharge Order Create Success';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('payment/recharge_online.php?amount=".$amount."','_self');
				}, 1000);
			});
			</script>";
			
     }elseif($ptype=='online2'){
         $_SESSION['amount'] = $amount;
         //$URL = "payment_getway.php?amount=$amount";
		//	echo "<script>location.href='$URL'</script>";
				echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Recharge Order Create Success';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('payment_getway.php?amount=".$amount."','_self');
				}, 1000);
			});
			</script>";
			
     }elseif($ptype=='offline'){
         $_SESSION['amount'] = $amount;
         //$URL = "upi-pay2.php?amount=$amount";
		//	echo "<script>location.href='$URL'</script>";
				echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Recharge Order Create Success';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('payment3/payment3.php?amount=".$amount."','_self');
				}, 1000);
			});
			</script>";
			
     }elseif($ptype=='online22'){
         $_SESSION['amount'] = $amount;
         //$URL = "shopkiing/index.php?amount=$amount";
		 //echo "<script>location.href='$URL'</script>";
		 	echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Recharge Order Create Success';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('shopkiing/index.php?amount=".$amount."','_self');
				}, 1000);
			});
			</script>";
     }else{
          $_SESSION['amount'] = $amount;
        // $URL = "upi.php?amount=$amount";
		//	echo "<script>location.href='$URL'</script>";
			echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Recharge Order Create Success';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('upi.php?amount=".$amount."','_self');
				}, 1000);
			});
			</script>";
			
         
     }
     }else{
         echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Select Payment Option';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('recharge.php','_self');
				}, 1000);
			});
			</script>";
     }
 }else{
     echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Please Enter Amount';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('recharge.php','_self');
				}, 1000);
			});
			</script>";
 }
     }else{
     echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Recharge amount is too small';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('recharge.php','_self');
				}, 1000);
			});
			</script>";
 }
 }
?>