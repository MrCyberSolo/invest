<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('../user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
//$userid = '10001';
$amount = $_SESSION['amount'];

if(isset($_POST['recharge'])) {
    $amount = $_POST['amount'];
    $query = mysqli_query($con,"insert into payment(`user_id`,`pay_type`,`amount`,`pay_date`) values('$userid_access','UPI','$amount',Now())");

    echo '<script> swal({title: "Payment Slip Updated!",text: "Click Ok ",text: "Click Ok",icon: "success",}).then(function(){window.location = "recharge.php" });</script>';
}

?>
<?php
    if(isset($_GET['amount'])) {
        $amount = $_GET['amount'];
    }
    ?>
<!doctype html>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
       
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
       <title>Make Payment</title>
       
           <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" data-react-helmet="true" />
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.11.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
        /*	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap'); */
        	@import url('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=');

            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins', sans-serif !important;
            }
            body{
                background-color: #FFF;
                margin: 0 auto;
                padding: 0;
                /*max-width: 375px;*/
            }
            .logo{
                font-size: 26px;
                color: #fe0074;
                padding: 20px 0;
                font-weight: bold;
                text-align: center;
                text-shadow: 1px 1px 1px rgba(0,0,0,.1);
            }
            .nav-tabs>li>a{
                color: black ;
                font-weight: bold !important;
            }
            .active>a{
                color: #fe0074 !important;
            }
            h1{
                width: 100%;
                font-size: 16px;
               
                color: #121212;
                margin: 0 auto;
                font-weight: 400;
            }
            h5{
                padding: 30px 20px 0 20px;
                width: 500px;
                margin: 0 auto;
                font-size: 14px;
                color: #121212;
            }
            h2{
                width: 320px;
                font-size: 12px;
                color: #999;
                font-weight: 400;
                padding: 0 10px;
                margin: 0 auto;
                line-height: 1.5;
            }

            h2 span{
                color: #666;
            }
            .box{
                height: auto;
                margin: 20px auto;
                border: 3px solid #0081FF !important;
                box-sizing: border-box;
                /*box-shadow: none;*/
                border-radius: 5px;
            }
            .box p{
                border-bottom: 1px solid #CCC;
                margin: 0;
                line-height: 36px;
                padding: 5px 10px;
            }
            .box p:last-child{
                border: 0 none;
            }
            .box .label{
                font-size: 14px;
                color: #666;
                display: block;
                text-align: left;
                margin-right: 10px;
                line-height: 1.2;
                vertical-align: middle;
            }
            .box .value{
                font-size: 14px;
                color: #000;
                display: inline-block;
                width:auto;
                font-weight: bold;;
                line-height: 1.2;
                vertical-align: middle;
            }
            .box button{
                vertical-align: middle;;
            }
            .toast{
                height: 60px;
                width: 140px;
                background-color: rgba(0,0,0,.5);
                border-radius: 5px;
                position:absolute;
                left: 50%;
                top: 30%;
                margin-left: -70px;
                margin-top: -30px;
                line-height: 60px;
                color: #FFF;
                text-align: center;
                white-space: nowrap;
            }
            
            .center {
              line-height: 1.5;
              display: inline-block;
              vertical-align: middle;
            }
            .container{
                width :100%;
                padding: 0 !important;
            }
            .tab-content{
                width: 100% !important;
            }
            .tab-content>div>h1{
                width: 100% !important;
            }
            .box{
                width: 100% !important;
                padding: 10px ;
            }
            .value{
                padding: .2em .6em .3em !important;
            }
            .copyBtn{
                float: right;
                background-color: #0081FF !important;
                color: white !important;
                font-weight: bold;
            }
            .copyBtn1{
               
                background-color: #0081FF !important;
                color: white !important;
                font-weight: bold;
            }
            .paymentRefForm{
                display: flex;
                flex-direction: column;
            }
            .paymentRefForm>.form-group>label{
                font-weight: bold !important;
                padding-left: 7px;
            }
            .paymentRefForm>.form-group>input{
                background-color: white;
                font-size: 12px;
                border: none;
                box-shadow: 0px 1px 50px 1px rgba(0, 0, 0, 0.1) !important;
                border-radius: 5px;
            }
            .submitBtn{
                margin: 0 auto !important;
                background-color: #0081FF !important;
                color: white;
                font-weight:bold;
                margin-top: 16px !important;
            }
  
.input-container {
   display: flex;
    width: 100%;
    justify-content: space-between;
    padding-left: 0px;
    background-color: #ffde1100;
    border-radius: 4px;
    border: 1px solid #737374;
    height: 45px;
    color: #fff;
}
.input-container input:focus, .input-container input:active {
    outline: none;
}
.input-container input {
    border: none;
    padding-left: 8px;
    background-color: #fff0;
    height: 42px;
}
/*.input-button {*/
/*   border-radius: 4px;*/
/*    border: 1px solid #fe0074;*/
/*    line-height: 28px;*/
/*    padding: 0 4px;*/
/*    font-size: 12px;*/
/*    color: #fff;*/
/*    width: 162px;*/
/*    background-color: #fe0074;*/
/*    margin: 4px;*/
/*}          */
            
.form-control:focus {
    border-color: #fff;
    outline: 0;
    -webkit-box-shadow: none;
    box-shadow: none;
}
.form-control {
    padding: 2px 12px;
    font-size: 12px;
}
        </style>
    </head>
<body>

<style>
      .appCapsule{
        max-width: 641px;
        margin: auto;

       }

       body{
        background-color: #ededed;
       }

       header{
        background: linear-gradient(to bottom right, #79b8ff, #5ea3f2, #2E6DB7);
       }

       
       .appBody{
        padding: 3.2rem 0;
       }

       /*input::placeholder{*/
       /* color: rgba(222, 222, 222, 0.507) !important;*/
       /*}*/

   
       input{
        border: 1px solid #dddddd90 !important;
       }

       input:focus{
        box-shadow: none !important;
        border: 1px solid #ddd;
       }

       button:active{
        border: 1px solid rgba(0, 0, 0, 0) !important;
       }


.code-text{
    background-color:transparent;
    font-weight:normal;
    color:#31363F;
    font-size:11px;
}

</style>
    <body>
          <header class="row ">
        <div class="text-white text-center py-4 px-0 ">
        <h6 class="fs-4 m-0">Ptmpay Cashier</h6>
        </div>
    
       </header>
        <div class="logo bg-white py-4 px-3 d-flex align-items-center justify-content-between">
            <p class="m-0" style="color: #07ccff; font-weight: 500; font-size:20px;"> ₹ <?php echo $amount; ?></p>
            <code class="code-text">SECURITY BY PAYMENT UPI</code>
        </div>
        
        
        <div class="title">
            <!-- <h5>Deposit the Payment amount to below this upi or bank account below. </h5> -->
        </div>
        
        
<!-- BANK AND UPI SECTION START  -->
<div class="container">
<!--    <div class="col-12 px-3 mt-3 py-3 bg-white">-->

<!--<div class="inner text-center py-3">-->
<!--    <img src="https://paytmblogcdn.paytm.com/wp-content/uploads/2022/04/paytm-se-upi-logo.png" style="width: 270px;" class="img-fluid" alt="">-->

<!--    <div class="btnBox mt-3 d-grid ">-->
<!--        <i class="mb-2" style="color: #00479859; font-weight: 500; font-size:12">85%choose</i>-->
<!--        <button style="border: none; font-size:15px; background: linear-gradient(to bottom right, #79b8ff, #5ea3f2, #2E6DB7); color: white ; border-radius: 3px; padding: 8px 0; font-weight: 500;">Click here to start Paytm</button>-->
<!--    </div>-->
<!--</div>-->
<!--</div>-->
 
      <div id="appCapsule" class="pb-2">
       
        <div class="appContent pb-0 mt-3">
             <div class="p-1" x-data="customerPortalForm()">
                        
                         <template x-if="qrcodeScreen">
                             
                            <div class="qrcode-holder bg-white px-3 py-4">
                                 <div class="logo bg-white py-4 px-3 ">
            <p style="font-size:13px; color:#31363F; font-weight:500;">Amount Payable</p>
            <p class="m-0" style="color: #07ccff;"> ₹ <?php echo $amount; ?></p>
            
            <div class="countdown-container">
  <div class="countdown" id="countdown" style="font-size:13px; color:#31363F; font-weight:500;">00:00</div>

   <p style="font-size:16px; margin-top:13px; color:#31363F; font-weight:normal;">Use Mobile Scane code to pay</p>
   <!--<img x-bind:src="qrcodeScreenData.qrcode" />-->
   <img src="48.png" class="img-fluid" style="width:180px;">
</div>

<style>

.countdown-container {
  text-align: center;
}

.countdown {
  font-size: 36px;
  font-weight: bold;
}

</style>
<script>
   
var targetTime = new Date();
targetTime.setMinutes(targetTime.getMinutes() + 30);


var countdown = setInterval(function() {
   
    var currentTime = new Date().getTime();
    
   
    var timeRemaining = targetTime - currentTime;
    
   
    var minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
   
    document.getElementById("countdown").innerHTML = minutes + ":" + seconds + "s ";
    
  
    if (timeRemaining < 0) {
        clearInterval(countdown);
        document.getElementById("countdown").innerHTML = "Countdown finished!";
    }
}, 1000);
 </script>   
 <p class="text-start" style="font-size:16px; margin-top:13px; color:#31363F; font-weight:normal;">Manual transfer</p>
                                <div class="form-group">
                                     
                                      <p class="text-start" style="font-size:16px; margin-top:13px; color: #0d6efd;  font-weight:normal;">1. copy the below given UPI</p>
                                        <div class="input-container" style=" border:none;">
                                            <input type="text" class="input-field form-control" value="BHARATPE09907391811@yesbankltd" id="text-1" / readonly>
                                            <button class="input-button" onclick="copy('text-1')" type="submit" style="border: none; background: linear-gradient(to bottom right, #79b8ff, #5ea3f2, #2E6DB7); color: white ; border-radius: 3px; padding: 8px 15px; font-size:13px; font-weight: 500;">Copy</button>
                                        </div>
									</div>
                               <center> <div>
                                   
                                   
                                   <!-- <p><strong x-text="qrcodeScreenData.qrdata.pn"></strong></p> 
                                   <img x-bind:src="qrcodeScreenData.qrcode" alt="qrcode" />
                                   <p><strong x-text="qrcodeScreenData.qrdata.pa"></strong></p>
                                    <p><small x-text="qrcodeScreenData.qrtime"></small></p> -->
                                    <!--<form>
                                        <div class="form-group basic animated">
                                            <div class="input-wrapper">
                                                
                                                 <input type="text" value="BHARATPE09907650353@yesbankltd" id="text-1" class="form-control">
                                                <i class="clear-input">
                                                    <ion-icon name="close-circle"></ion-icon>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                           <button type="button" onclick="copy('text-1')" style=" background: #0081ff; color: #fff; " class="btn cst-btn btn-lg btn-block">Copy UPI ID</button>
                                        </div>
                                    </form> -->
                                    
                                </div>
                                </center>
                                <form @submit.prevent="submitRechargeVerifyForm">
                                   <!-- <div class="form-group basic animated">
                                        <div class="input-wrapper">
                                            <label class="label" for="name2">UTR No</label>
                                            <input type="text" class="form-control"  name="utr_number" placeholder="Please enter UTR number..." required>
                                            <i class="clear-input">
                                                <ion-icon name="close-circle"></ion-icon>
                                            </i>
                                        </div>
                                    </div>
                                  
                                    -->
                                    <div class="form-group">
                                         <p class="text-start" style="font-size:16px; margin-top:13px; color: #0d6efd;  font-weight:normal;">2. Need to enter your 12 Ref No. (UTR) </p>
                                         
                                        <div class="input-container" style="border:none;" >
                                           
                                            <!--<input type="text" class="input-field form-control"  name="utr_number" placeholder="Please enter UTR number..." required />-->
                                            
                                            <input type="text" class="input-field form-control"  name="utr_number" placeholder="Ref No is required" required />
                                      
                                        </div>
                                        
                                          <div class="mt-2">
                                                <p class="text-start" style="font-size:12px; margin-top:13px; color: #FDA403;  font-weight:normal;">TIP: Open your UPI wallet and complete the transfer record your refernce No.(Ref No.) after payment</p>
                                                
                                                <br>
                                        <button type="submit" :disabled="loading" style="border: none; background: linear-gradient(to bottom right, #79b8ff, #5ea3f2, #2E6DB7); color: white ; border-radius: 3px; padding: 8px 0; font-weight: 500; padding:10px; font-size:15px;" x-text="verifyButtonLabel" class="btn cst-btn btn-lg btn-block">Submit</button>
                                    </div>
                                  </div>
                                </form>
                                 <!--<center><h1><b>Use Mobile Scan Code to Pay</b></h1>
                                <p style="padding: 10px;">or take a screenshot and save then open payment app to scan</p></center>
                                <img src="qrcode.jpg" style="width: 100%; padding-right: 65px; padding-left: 65px;">
                                <center><p style="color: #993c1f; margin: 0 0 5px; font-size: 12px;">Dont use the same qr code to pay multiple times</p>
                                    <p style="font-size: 14px;">Scan code supported APP</p></center>
                                    <img src="footer.jpg" style="width: 100%; padding-right: 45px; padding-left: 45px;">-->
                                    
                            </div>
                        </template>
                
                
                        <template x-if="!qrcodeScreen">
                            <div class="bg-white px-3 col-12">
                                 
        <li class="active"  style="list-style:none;"><a data-toggle="tab" href="#home">UPI PAY</a></li>
       
 
    
                                 <form @submit.prevent="submitRechargeForm" class="text-center py-1">
                                    <img src="https://paytmblogcdn.paytm.com/wp-content/uploads/2022/04/paytm-se-upi-logo.png" style="width: 270px; " class="img-fluid" alt="">
                                <!--<div class="text-center">
                                    <h2 class="text-primary">Payment Request Verify</h2>
                                    <p>Recharge </p>
                                </div>-->
                                <div class="form-group basic animated">
                                    <div class="input-wrapper">
                                        <label class="label" for="name2">Enter Your Amunt</label>
                                        <input type="hidden" class="form-control"  name="amount" id="name2" value="<?php echo $amount; ?>" required>
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                                
                                  <i class="mb-2" style="color: #00479859; font-weight: 500;">85%choose</i>
                                <div class="mt-2 d-grid" style="margin: 30px;">
                                    <button type="submit" name="upload_pay" style="border: none; background: linear-gradient(to bottom right, #79b8ff, #5ea3f2, #2E6DB7); color: white ; border-radius: 3px; padding: 8px; font-weight: 500; font-size:15px;">Click here to start Pay</button>
                                </div>
                            </form>
                            </div>
                           
                          
                        </template> <br>   
                          <center><a href="../recharge.php">Back to Account</a></center>
                    </div>
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
        function customerPortalForm()
        {
            return {
                loading: false,
                buttonLabel: 'Recharge',
                verifyButtonLabel: 'Submit',
                qrcodeScreen: false,
                qrcodeScreenData: {},
                submitRechargeForm(el) {
                    const self = this;
                    const formEl = el.target;

                    const formData = new FormData(formEl);

                    self.loading = true;
                    self.buttonLabel = 'Submitting...';

                    fetch('recharge-api.php', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        method: 'POST',
                        body: new URLSearchParams(formData).toString()
                    })
                    .then((response) => response.json())
                    .then((responseJson) => {
                        if(responseJson.status) {
                            formEl.reset();

                            let qrdata = responseJson.qrdata;
                            let qrcode = responseJson.qrcode;
                            let qrtime = responseJson.qrtime;

                            self.qrcodeScreen = true;
                            self.qrcodeScreenData = {
                                qrdata: qrdata,
                                qrcode: qrcode,
                                qrtime: qrtime,
                            };
                        } else {
                            Swal.fire('Oops...', responseJson.message, 'error');
                        }
                    })
                    .catch((error) => {
                        // 'Ooops! Something went wrong!'
                        Swal.fire('Oops...', error, 'error');
                    })
                    .finally(() => {
                        self.loading = false;
                        self.buttonLabel = 'Recharge'
                    });
                },
                submitRechargeVerifyForm(el) {
                    const self = this;
                    const formEl = el.target;

                    const formData = new FormData(formEl);

                    self.loading = true;
                    self.verifyButtonLabel = 'Submitting...';

                    fetch('recharge-verify-api.php', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        method: 'POST',
                        body: new URLSearchParams(formData).toString()
                    })
                    .then((response) => response.json())
                    .then((responseJson) => {
                        if(responseJson.status) {
                            formEl.reset();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: responseJson.message,
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: false,
                            });

                            setTimeout(function() {
                                window.location.href = '../recharge-records.php';
                            }, 3500);
                        } else {
                            Swal.fire('Oops...', responseJson.message, 'error');
                        }
                    })
                    .catch((error) => {
                        // 'Ooops! Something went wrong!'
                       // Swal.fire('Oopss...', error, 'error');
                            Swal.fire("Success", "", "success");
                             setTimeout(function() {
                                window.location.href = '../recharge-records.php';
                            }, 3500);
                    })
                    .finally(() => {
                        self.loading = false;
                        self.verifyButtonLabel = 'Recharge Verify'
                    });
                }
            };
        }
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
   
</body>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>


</html>
