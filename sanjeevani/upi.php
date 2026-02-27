<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
$amount = $_SESSION['amount'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment UPI</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <meta name="viewport" content="initial-scale=1, maximum-scale=1, width=device-width">
	<style>
		.quick_amount p {
		    display: inline-block;
		    text-align: center;
		    font-size: 16px;
		    border: 1px #dcdada solid;
		    color: #000;
		    width: 32%;
		    background: #f7f7f7;
		    border-radius: 4px;
			padding: 6px 0;
		}
		.quick_amount {
		    width: 100%;
		}
		.chen{font-weight: 600;}
		.chen i{display: block;font-size: 12px;font-style: normal;font-weight: 100;}
		.quick_amount p.active {
		     border: #0160bf 1px solid;
		     color: #fff;
		     border-radius: 4px;
		     background: #0160bf;
		    
		}
		
		.quick_method p {
		    display: inline-block;
		    text-align: center;
		    font-size: 16px;
		    border: 1px #dcdada solid;
		    color: #000;
		    width: 32%;
		    background: #f7f7f7;
		    border-radius: 4px;
			padding: 6px 0;
		}
		.quick_method {
		    width: 100%;
		}
	
		.quick_method p.active {
    border: #0160bf 1px solid;
    color: #fff;
    border-radius: 4px;
    background: #0160bf;
		    
		}
		
		.btn-lg {
		    height: 34px;
		    padding: 0px 0px;
		    font-size: 14px;
		    border-radius: 30px;
		}
		.appContent {
		
		    border-radius: 16px 16px 0 0;
		}
		.upi-btn{
    color: #fe6700;
    border: solid 1px #fe6700;
    padding:10px;
    border-radius: 10px;
    text-align:center;
}
.upi-btn:hover {
   background-image: linear-gradient(to right, #fe6700 0%, #fe6700  51%, #fe6700 100%);
   
    color: #fff;
    border: none;
    border-radius: 10px;
}
.form-control {
    box-shadow: none !important;
    color: #000;
    background: #f3f2f2;
    height: 50px;
    font-size: 16px;
    padding: 10px 16px;
    border-radius: 6px;
    border: 0;
    border: none;
    text-align: center;
}

.cst-btn {
    background-image: linear-gradient(to right, #fe6700 0%, #fe6700  51%, #fe6700  100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    /*padding: 25px;*/
    width:150px;
    font-size:18px;
    height:50px;
    margin:auto;
}
.appheaders {
   background-image: linear-gradient(to right, #d52305 0%, #f85a02 51%, #d52305 100%);
    color: #fff;
    font-size: 20px;
    height: 106px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9990;
    box-shadow: 0px 0px 9px 2px rgba(0, 0, 0, 0.06);
}
.appcapule{
    background-color: #f1f1f1;
    padding: 64px 0;
}
	.popup {
    position: fixed;
    bottom: 50%;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    z-index: 999;
    animation: fadeInOut 2s;
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
</head>

<body >

    <!-- Page loading -->
    <div class="loading">
        <div class="spinner-grow"></div>
    </div>
    <!-- * Page loading -->

    <!-- App Header -->
    <div class="appheaders container">
        <div class="left">
            <!--<a href="account.php" class="icon goBack">
                <i class="icon ion-ios-arrow-back"></i>
            </a>-->
        </div>
        <div class="pageTitle"> UPIPAY UPI Cashier</div>
		<div class="right">
            <!--<a href="/index/pay/rechargeinfo.html" class="link">Information</a>-->
        </div> 
    </div>
    <div class="appcapule container" class="pb-2">
       
        <div class="pb-0 mt-3">
  <?php 
   $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `bank`"));
   $bank_upi = $query['bank_upi'];
   $bank_scan = $query['bank_scan'];
   ?>
   
   <?php
    if(isset($_GET['amount'])) {
        $amounts = $_GET['amount'];
        
    }
    
        
       
    ?>
    
    
    	<div id="London" class="tabcontent" style="display: block;" >
					<ul class="transaction-list list-unstyled mt-2">
						<li>
							<div class="d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center">
									<div class="ml-10" style=" padding-top: 15px; ">

										<h4 class="coin-name">OrderSn : 859874585846 </h4><br>
										<h4 class="coin-name">Amount : ₹<?php echo $amount; ?>.00</h4>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>
				
				<div id="London" class="tabcontent" style="display: block;" >
					<ul class="transaction-list list-unstyled mt-2">
						<li>
									<div class="ml-10">
                                 <form>
                            <div class="form-group basic animated">
                                <div class="input-wrapper">
                                    <label style="color:blue; font-size:12px; ">Step 1: Transfer <span style="color:red; font-weight:600">₹<?php echo $amount; ?>.00 to the following UPI</span></label>
                                    <input type="text" value="<?php echo $bank_upi;?>" id="copyText" class="form-control" readonly>
                                            
                                     <!--<input type="text" value="<?php echo $bank_upi;?>" id="text-1" class="form-control"> -->
                                    <i class="clear-input">
                                        <ion-icon name="close-circle"></ion-icon>
                                    </i>
                                </div>
                            </div>
                            <div class="col text-center">
                                <button id="copyButton" class="btn upi-btn btn-sm">Copy Beneficiary UPI</button>
                                <!--<button type="button" onclick="copy('text-1')" class="btn upi-btn btn-sm">Copy Beneficiary UPI</button>-->
                            </div>
                        </form> 
                        <label style="color:grey; font-size:12px; text-align:center; ">Open your UPI wallet and complete the transfer Record you UTR(Reference No) after payment</label>
                        
									</div>
						</li>
					</ul>
				</div>
				<div id="London" class="tabcontent" style="display: block;" >
					<ul class="transaction-list list-unstyled mt-2">
						<li>
									<div class="ml-10">
                            <div class="form-group basic animated">
                                <div class="input-wrapper">
                                    <label style="color:blue; font-size:12px; ">Step 2: Submit UTR/Reference No/Ref No</label>
                                    <form  action="" method="post" enctype="multipart/form-data">
				
                                    <div class="form-group">
                                        <input type="hidden" class="form-control"  id="amount" name="amount" value="<?php echo $amount ?>" required>
                                    </div>
			
                    				 <div class="form-group">
                                        <input type="text" class="form-control" minlength="12" maxlength="13" name="utr" placeholder="Input 12-digit here" style=" height: 100px; font-size: 36px; " required>
                                    </div>
                                    <hr>
                                    <label style="color:grey; font-size:10px; text-align:center; ">Generally, your transfer will be confirmed within 1 minutes</label>
                                  
                                    <div style="" class=" text-center">
                                        <button type="submit" name="upload_pay" class="btn cst-btn btn-xxl ">
                                           Submit UTR
                                        </button>
                                    </div>
                             </form>
                                </div>
                            </div>
                           
							</div>
						</li>
					</ul>
				</div>
				
			<div id="London" class="tabcontent" style="display: block; background:#f1f1f1; padding:6px;" >
							<div class="ml-10">
                            <div class="form-group basic animated">
                               <label style="color:grey; font-size:12px; ">Where to find UTR.</label>
                            </div>
                           <img src="asupport/images/upi/demo-1.png" style="width:100%; padding:2px;">
                           <img src="asupport/images/upi/demo-2.png" style="width:100%; padding:2px;">
                           <img src="asupport/images/upi/demo-3.png" style="width:100%; padding:2px;">
                           <img src="asupport/images/upi/demo-4.png" style="width:100%; padding:2px;">
                           <img src="asupport/images/upi/demo-5.png" style="width:100%; padding:2px;">
                           <img src="asupport/images/upi/demo-6.png" style="width:100%; padding:2px;">
							</div>
						
				</div>	
				
    <!--<center><img src="img/<?php echo $bank_scan; ?>"  width="200" height="200"><!--<h6 style="color:#fff">UPI ID : BHARATPE09907391811@yesbankltd</h6></center> -->
   
  
                        
            

          

        </div>


    </div>
    <script src="static/js/jquery-3.4.1.min.js"></script>
    <script src="static/js/app.js"></script>
	<script src="static/js/mui.min.js"></script>
	<script src="static/js/mui.loading.js"></script>
	<script src="static/js/base.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
			var $amountInput = $('[type="number"]');
			var amount = '';

			$(".quick_amount p").off("click").on("click", function() {
				amount = $(this).attr('data-item');
				if (!$(this).hasClass('active')) {
					$(this).addClass('active').siblings().removeClass('active');
					$amountInput.val(amount);
				} else {
					$(this).removeClass('active');
					$amountInput.val('');
				}
			})
			
			$(".quick_method p").off("click").on("click", function() {
				$(this).addClass('active').siblings().removeClass('active')
				$("#pay_type").val($(this).attr('data-type'));
			})
			
			
			$amountInput.on('input propertychange', function() {

				if ($(this).val() !== $('.quick_amount p.active').text()) {
					$('.quick_amount p').removeClass('active');
				}
			})


		</script>
			<script>
        //Pass the id of the <input> element to be copied as a parameter to the copy()
        let copy = (textId) => {
          //Selects the text in the <input> elemet
          document.getElementById(textId).select();
          //Copies the selected text to clipboard
          document.execCommand("copy");
          
          
        };
      </script>
<script>
    // Get references to the elements
    const copyText = document.getElementById('copyText');
    const copyButton = document.getElementById('copyButton');

    // Add a click event listener to the button
    copyButton.addEventListener('click', () => {
        // Select the text in the input field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected text to the clipboard
        document.execCommand('copy');

        // Display a popup message
        const popupMessage = document.createElement('div');
        popupMessage.textContent = 'Copied Successfully';
        popupMessage.classList.add('popup');
        document.body.appendChild(popupMessage);

        // Remove the popup after a delay
        setTimeout(() => {
            popupMessage.remove();
        }, 5000); // 5000 milliseconds = 5 seconds
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>
<?php


 if(isset($_POST['upload_pay']))
 {
     $pay_id = $userid_access;	
     $ptype = 'UPI';
     $amount = $_POST['amount'];
     $utr = $_POST['utr'];
   if(email_check($utr)){  
  
        $query = mysqli_query($con,"insert into payment(`user_id`,`pay_type`,`utr`,`amount`,`pay_date`) values('$pay_id','$ptype','$utr','$amount',Now())");
             //echo "<script>alert ('Payment Updated!')</script>";
            // echo "<script>window.open('details.php','_self')</script>"; 
             ?>
             <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Payment Success';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "details.php" 
        },
    2000);
    </script>
        <?php
   }
 else
 {
             //check email
        // echo '<script>alert("This UTR Already availble. Please Check UTR and Updated");</script>';
         ?>
             <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'This UTR Already availble. Please Check UTR and Updated';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "upi.php" 
        },
    2000);
    </script>
        <?php
 }

 }
 
 if(isset($_POST['upload_payss']))
 {
     $pay_id = $userid_access;	
     
     $ptype = $_POST['ptype'];
     
     $amount = $_POST['amount'];
     $utr = $_POST['utr'];
     
   
  if($utr!='') {
      
     $file = rand(1000,100000)."-".$_FILES['file']['name'];
     $file_loc = $_FILES['file']['tmp_name'];
     $file_size = $_FILES['file']['size'];
     $file_type = $_FILES['file']['type'];
     $folder="img/payment/";
     // new file size in KB
     $new_size = $file_size/1024;  
     // new file size in KB
 
     // make file name in lower case
     $new_file_name = strtolower($file);
     // make file name in lower case
 
     $final_file=str_replace(' ','-',$new_file_name);
 
 
 
     if(move_uploaded_file($file_loc,$folder.$final_file))
     {
 
         $query = mysqli_query($con,"insert into payment(`user_id`,`pay_type`,`utr`,`amount`,`file`,`pay_date`) values('$pay_id','$ptype','$utr','$amount','$final_file',Now())");
 
         //$query = "update user set profile_img ='$final_file' where email = '$user_id'";
 
         //$run_posts = mysqli_query($con,$query);
        // echo '<script> swal({title: "Payment Slip Updated!",text: "Click Ok ",text: "Click Ok",icon: "success",}).then(function(){window.location = "pay.php" });</script>';
	
              echo "<script>alert ('Payment Request Successfully!')</script>";
             echo "<script>window.open('details.php','_self')</script>"; 
     }
  }else{
      
            echo "<script>alert ('UTR Required')</script>";
            echo "<script>window.open('pay.php','_self')</script>"; 
  }


 }
 
 
 function email_check($utr){
     global $con;
     
     $query_utr =mysqli_query($con,"SELECT * FROM `payment` WHERE `utr`='$utr'");
     if(mysqli_num_rows($query_utr)>0){
         return false;
     }
     else{
         return true;
     }
 }
     ?>