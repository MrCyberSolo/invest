<?php
include('../includes/connect.php');
include('../includes/check-login.php');
$userid = $_SESSION['userid'];
//$userid = '10001';
$amount = $_SESSION['amount'];

if(isset($_POST['recharge'])) {
    $amount = $_POST['amount'];
    $query = mysqli_query($con,"insert into payment(`user_id`,`pay_type`,`amount`,`pay_date`) values('$userid','UPI','$amount',Now())");

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
        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" data-react-helmet="true" />
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.11.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
        	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

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
                max-width: 375px;
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
.input-button {
   border-radius: 4px;
    border: 1px solid #fe0074;
    line-height: 28px;
    padding: 0 4px;
    font-size: 12px;
    color: #fff;
    width: 162px;
    background-color: #fe0074;
    margin: 4px;
}          
            
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

    <body>
        <div class="logo">
            <span>PAYMENT OF : <?php echo $amount; ?></span>
        </div>
        <div class="title">
            <!-- <h5>Deposit the Payment amount to below this upi or bank account below. </h5> -->
        </div>
<!-- BANK AND UPI SECTION START  -->
<div class="container">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#home">UPI PAY</a></li>
       
    </ul>
      <div id="appCapsule" class="pb-2">
       
        <div class="appContent pb-0 mt-3">
             <div class="p-1" x-data="customerPortalForm()">
                        
                         <template x-if="qrcodeScreen">
                             
                            <div class="qrcode-holder">
                                <img src="upi.png" style="width: 100%; padding-top: 2px; padding-left: 0px;"><br>
                                <!--<center><h1>BharatPe QR Code</h1></center>-->
                                <div class="form-group">
                                        <div class="input-container">
                                            <input type="text" class="input-field form-control" value="BHARATPE09917801988@yesbankltd" id="text-1" />
                                            <button class="input-button" onclick="copy('text-1')" type="submit">Copy UPI ID</button>
                                        </div>
									</div>
                               <center> <div>
                                    <p><strong x-text="qrcodeScreenData.qrdata.pn"></strong></p> 
                                    <img x-bind:src="qrcodeScreenData.qrcode" alt="qrcode" />
                                   <p><strong x-text="qrcodeScreenData.qrdata.pa"></strong></p>
                                    <p><small x-text="qrcodeScreenData.qrtime"></small></p>
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
                                    <div class="mt-2">
                                        <button type="submit" :disabled="loading" style=" background: #fe0074; color: #fff; " x-text="verifyButtonLabel" class="btn cst-btn btn-lg btn-block">Recharge Verify</button>
                                    </div>-->
                                    <div class="form-group">
                                        <div class="input-container">
                                            <input type="text" class="input-field form-control" name="utr_number" placeholder="Please enter UTR number..." required />
                                            <button class="input-button" :disabled="loading" style=" background: #fe0074; color: #fff; " x-text="verifyButtonLabel" type="submit">Recharge Verify</button>
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
                            <form @submit.prevent="submitRechargeForm">
                                 <img src="upi.png" style="width: 100%; padding-top: 2px; padding-left: 0px;"><br>
                                <!--<div class="text-center">
                                    <h2 class="text-primary">Payment Request Verify</h2>
                                    <p>Recharge </p>
                                </div>-->
                                <div class="form-group basic animated">
                                    <div class="input-wrapper">
                                        <label class="label" for="name2">Enter Your Amunt</label>
                                        <input type="number" class="form-control"  name="amount" id="name2" value="<?php echo $amount; ?>" required>
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                                <div class="mt-2" style="margin: 30px;">
                                    <button type="submit" name="upload_pay" style=" background: #fe0074; color: #fff; border-radius: 30px; " class="btn cst-btn btn-lg btn-block">Pay Now</button>
                                </div>
                            </form>
                          
                        </template> <br>   
                          <center><a href="javascript:history.back()">Back to Account</a></center>
                    </div>
                </div>


    </div>