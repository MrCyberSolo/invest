<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
$amount_deposite = $_SESSION['amount'];
   $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `bank`"));
   $bank_upi = $query['bank_upi'];
   $bank_scan = $query['bank_scan'];
   $query_user = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user`"));
   $mobile = $query_user['mobile'];
   
   if(isset($_SESSION['amount'])){
   // $amount_deposite = $_POST['pay_amount'];
    $amount_deposite = $_SESSION['amount'];
    
}else{
    echo "<script>window.open('pay.php','_self')</script>";
}
  
  
   
   ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YessPay</title>
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script src="assets/js/clipboard.min.js"></script>

    <style>
        .head {
            text-align: center;
            padding: 1rem 0;
        }

        .item {
            padding: 0.5rem 1rem;
        }

        img {
            height: 3rem;
        }

        .tips:before{
            position: absolute;
            right: 10px;
            left: 10px;
            height: 2px;
            background: -webkit-repeating-linear-gradient(135deg,#ff6c6c,#ff6c6c 20%,transparent 0,transparent 25%,#1989fa 0,#1989fa 45%,transparent 0,transparent 50%);
            background-size: 80px;
            content: "";
        }
        .tips:after{
            position: absolute;
            right: 10px;
            left: 10px;
            height: 2px;
            background: -webkit-repeating-linear-gradient(135deg,#ff6c6c,#ff6c6c 20%,transparent 0,transparent 25%,#1989fa 0,#1989fa 45%,transparent 0,transparent 50%);
            background-size: 80px;
            content: "";
        }

        .loading {
            width: 100%;
            height: 100%;
            font-family: Helvetica,arial,sans-serif;
            background-color: rgba(0,0,0,.7);
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 999;
        }
        .lading-body {
            width: auto;
            max-width: 260px;
            min-width: 40px;
            margin: 0 auto;
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
        }

        .loading-icon {
            font-size: 0;
            text-align: center;
            position: relative;
        }

        .loading-text {
            font-size: 14px;
            color: #fff;
            text-align: center;
            margin-top: 6px;
        }

        .contentDiv {
            width: 95%;
            display: block;
            margin: auto;
            margin-left: 2.5%;
            margin-right: 2.5%;
            margin-top: 0.7rem;
        }
        .shier-title {
            color: rgba(85, 140, 244, 1);
            font-weight: 700;
            font-size: .9375rem;
            padding-top: 0.5rem;
            line-height: 1.125rem;
        }
        .disabled {
            color: rgba(255, 255, 255, 0.3);
        }

        .enable {
            color: #ffffff;
        }
        .alertMessage {
            max-width: 70%;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            text-align: center;
            position: fixed;
            top: 40%;
            left: 35%;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            display: none;
        }
        .upiInput {
            margin-left: 0.5rem;
            width: 50%;
            display: inline-block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .refnum {
            border: none;
            height: 40px;
            left: 40px;
            width: 80%;
            padding-left: 5px;
            color: #558CF4;
            font-size: 1rem;
        }
        .shier-hint {
            color: rgba(235, 120, 36, 1);
            font-size: 0.8125rem;
            line-height: 0.9375rem;
        }
        .upiBut {
            float: right;
            border: none;
            background-color: #558CF4;
            width: 3.75rem;
            height: 2.5rem;
            border-radius: 0px 5px 5px 0px;
            color: #fff;
        }
        .submitBut {
            background-color: #558CF4;
            border-radius: 0.3125rem;
            border: none;
            width: 100%;
            height: 2.75rem;
            margin-top: 1.5rem;
        }
        .refnumDiv {
            height: 2.5rem;
            border-radius: 0.3125rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: #fff;
            font-size: 0.8125rem;
            width: 97.5%;
            line-height: 2.5rem;
            padding-left: 0.5rem;
            font-size: 0.875rem;
            margin-right: 2.5%;
        }
        .upiInputDiv {
            height: 2.5rem;
            border-radius: 0.3125rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: #fff;
            font-size: 0.8125rem;
            width: 100%;
            line-height: 2.5rem;
            font-size: 0.875rem;
            margin-bottom: .75rem;

        }
        .closeHelp {
            position: absolute;
            top: 0;
            right: 1rem;
        }
        
        /*my code */
       
                .refnumDiv input{
                    border:none;
                    outline:0;
                    
                    
                }
                
                
         
    </style>
    <script>
        var get = function (url, onload) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = onload
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
        }

        function refreshStatus() {
            $.post('/findOrderStatus?orderId=' + document.getElementById("tradeId").value, {"orderId": $('#orderId').val()}, function (res) {
                if (0 === res.code && res.data) {
                    window.location.href = res.data;
                }
            });
        }

        var payAddr = ""
        var st = 0;

        function selectItem(e) {
            if(e == 1){
                document.getElementById("loading").style.display = "block";

                payAddr = document.getElementById("paytm1").value;
            }else if(e == 2){
                var phonePeAlert = document.getElementById("phonePeAlert").value;
                if (phonePeAlert === "1") {
                    document.getElementById("phonePeAlertModal").style.display = "block";
                    return;
                }else {
                    document.getElementById("loading").style.display = "block";
                    payAddr = document.getElementById("phonepe1").value;
                }
            }else if(e == 3){
                document.getElementById("loading").style.display = "block";

                payAddr = document.getElementById("googlepay1").value;
            }else if(e == 4){
                document.getElementById("loading").style.display = "block";

                payAddr = document.getElementById("other1").value;
            }else if(e == 5){
                document.getElementById("loading").style.display = "block";

                payAddr = document.getElementById("mobikwik1").value;
            }

            var upiLink = payAddr

            var jump = document.getElementById("jump")
            jump.setAttribute('href', upiLink);
            jump.click();

            if(st == 0){
                setInterval(refreshStatus, 5000);
            }

            st = 1;
        }

        function giveUp() {
            document.getElementById("loading").style.display = "none";
        }

        function closePhonePeAlertModal() {
            document.getElementById("phonePeAlertModal").style.display = "none";
        }

        function continuePhonePeAlertModal() {
            document.getElementById("phonePeAlertModal").style.display = "none";

            payAddr = document.getElementById("phonepe1").value;
            var upiLink = payAddr

            var jump = document.getElementById("jump")
            jump.setAttribute('href', upiLink);
            jump.click();
        }
    </script>

    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
<body style="background-color: #eee;">
<input id="orderId" name="orderId" type="hidden" value="6597a997e4b06502dc8c778f">
<div style="background-image: linear-gradient(to bottom,#566aff,#a7cbf3);
    border-radius: 0 0 40% 40%;
    padding-bottom: 1px;">
    <div style="text-align: center;
    font-size: 18px;
    padding: 6px 0;
    font-weight: 700;
    line-height: 1.6;
    color: #fff;
    background: #566aff;">
        <span style="height: 50px;line-height: 50px;">payment</span>
    </div>

    <div class="head" style="border-radius: 10px;text-align: center;">
<!--        <p style="color: #000000;padding-left: 20px;">Amount Payable</p>-->
        <b style="font-size: 1.5rem;padding-left: 20px;color: #566aff;">₹<?php echo $amount_deposite; ?></b>

    </div>
</div>

<div class="head" style="background-color: white;border-radius: 10px;text-align: left;">
    <div class="head" style="text-align: left;padding-left: 10px;">
        Choose a payment method to pay
    </div>
    <div style="width: 100%;display: flex;">
       
        <div style="width: 40%;margin-left: 5%;border:solid 1px #dddddd;border-radius: 8px;">
            <div class="item">
                 <a href="phonepe://pay">
                <div style="float: left;width: 60%;height: 3rem;">
                    <img src="asupport/images/dida_phonepe.png" style="height: 40px;"/>
                </div>
                <div style="float: right;width: 20%;height: 3rem;">
                    <img src="asupport/images/grzx_38.png"
                         style="margin: 1rem;width: 1rem;height: 1rem"/>
                </div>
                 </a>
            </div>
        </div>
       

        <div style="width: 40%;margin-left: 5%;border:solid 1px #dddddd;border-radius: 8px;">
            <div class="item">
                 <a href="paytmmp://upi">
                <div style="float: left;width: 60%;height: 3rem;">
                    <img src="asupport/images/dida_paytm.png" style="height: 32px;"/>
                </div>
                <div style="float: right;width: 20%;height: 3rem;">
                    <img src="asupport/images/grzx_38.png"
                         style="margin: 1rem;width: 1rem;height: 1rem"/>
                </div>
                </a>
            </div>
        </div>

    </div>


    <div style="width: 100%;display: flex;margin-top: 20px;">

        <div style="width: 40%;margin-left: 5%;border:solid 1px #dddddd;border-radius: 8px;">
            <div class="item">
                 <a href="gpay://upi">
                <div style="float: left;width: 60%;height: 3rem;">
                    <img src="asupport/images/dida_gpay.png" style="height: 40px;"/>
                </div>
                <div style="float: right;width: 20%;height: 3rem;">
                    <img src="asupport/images/grzx_38.png"
                         style="margin: 1rem;width: 1rem;height: 1rem"/>
                </div>
                </a>
            </div>
        </div>

        <div style="width: 40%;margin-left: 5%;border:solid 1px #dddddd;border-radius: 8px;">
            <div class="item">
                 <a href="upi://pay">
                <div style="float: left;width: 60%;height: 3rem;">
                    <img src="asupport/images/dida_other.png"/>
                </div>
                <div style="float: right;width: 20%;height: 3rem;">
                    <img src="asupport/images/grzx_38.png"
                         style="margin: 1rem;width: 1rem;height: 1rem"/>
                </div>
                </a>
            </div>
        </div>

    </div>

    <div style="box-shadow: 2px 4px 4px 4px #f0f0f0;border-radius: 6px;text-align: center;margin: 20px auto;">
        <div style="text-align: left;padding-left: 10px;">
            Or Use Mobile Scan code to pay
        </div>
        <img style="width: 150px;height: 150px;margin: 0 auto;" src="asupport/<?php echo $bank_scan; ?>">
    </div>

    <div class="upiContent">
        <div style="text-align: left;padding-left: 10px;">
            Or Manual transfer
        </div>
        <div class="contentDiv">
            <p class="shier-title">1. Copy the below given UPI</p>
            <div class="upiInputDiv">
                <span type="text" class="upiInput" id="upiInput"><?php echo $bank_upi; ?></span>
                <button class="upiBut" data-clipboard-text="<?php echo $bank_upi; ?>" id="upiCopyBut">Copy</button>
            </div>
            <p class="shier-hint">Tip: Dont save the UPI, get new UPI every time.</p>
        </div>
        
        <div class="contentDiv">
            <p class="shier-title"> 2. Need to enter your 12 Ref No (UTR)</p>
           
            <div class="refnumDiv">
                  <form action="" method="post" enctype="multipart/form-data">
                Ref No.<input type="number" name="refrence" class="refrence" placeholder="Ref No is required">
                <!--                <img src="assets/images/help.png" alt="what is UTR" style="height: .8rem" id="help">-->
                
            </div>
            <p class="shier-hint">Tip: Open your UPI wallet and complete the transfer Record your reference No.(Ref No.) after payment.</p>
        </div>
        <div class="contentDiv">
            <button type="submit" class="submitBut" name="upload_pay">Submit</button>
        </div>
        <form>
    </div>
    

<!--    <div class="tips" style="color: #ff7f9f;font-weight: 700;font-style: italic;line-height: 1.8;font-size: 14px;margin-top: 50px;padding-left: 10px;padding-right: 10px;padding-bottom: 10px;">-->
<!--        <p style="padding-top: 10px;">1.Please make sure you have installed the app</p>-->
<!--        <p >2.Don't pay for the same link repeatedly</p>-->
<!--    </div>-->


    <div style="color: #666666;padding: 10px;padding-bottom: 20px; margin-top: 10px;">
        <p style="font-weight: 700;">Notice:</p>
        <p >1. please, contact us if you have any payment issue: <a href="#">#</a></p>
        <p >2. Please select the payment method you need and make sure your phone has the corresponding wallet software installed.</p>
    </div>

</div>



<div id="contact" style="padding: 1rem 1rem;text-align: center;color:#b2b2ab;font-size:0.8rem">

</div>
<a href="" id="jump" style="display: none">to pay</a>

<div id="loading" class="loading" style="display: none;">
    <div class="lading-body" style="background-color: white;border-radius: 20px;text-align: center;">
        <div class="loading-icon"><i></i><i></i><i></i></div>
        <p class="loading-text" style="color: black;font-weight: bold;margin-top: 20px;">Wait payment response</p>
        <img src="assets/images/rings.svg"/>
        <hr>
        <p class="loading-text" style="color: black;font-weight: bold;" onclick="giveUp()">Give up waiting</p>
    </div>
</div>
<div id="phonePeAlertModal" class="loading" style="display: none;">
    <div class="lading-body" style="background-color: white;border-radius: 10px;text-align: center;">
        <div class="loading-icon"><i></i><i></i><i></i></div>
        <p class="loading-text" style="color: black;margin-top: 20px;">Single order amount over 2000rs, pls try other payments, phone pe is not suggested.</p>
        <div style="">
            <p class="loading-text" style="color: #DCDFE6
;width: 100px;float: left;padding:10px;background-color: #E6A23C
;border-radius: 8px;margin:5px;" onclick="closePhonePeAlertModal()">Change</p>
            <p class="loading-text" style="color: #DCDFE6
;width: 100px;float:right;padding:10px;background-color: #67C23A
;border-radius: 8px;margin:5px;" onclick="continuePhonePeAlertModal()">Continue Pay</p>
        </div>
    </div>
</div>

<script>
    $(function () {
        var isBaackground = false;
        document.addEventListener("visibilitychange", function (e) {
            if (document["hidden"] == true) {
                isBaackground = true;
            }
        });
        var $upiBut = new Clipboard(".upiBut")
        $upiBut.on("success", function () {
            showDialog("Copy Success")
        })

        $("#help").click(function () {
            $("#layui-layer1").show()
        })

        $(".closeHelp").click(function () {
            $("#layui-layer1").hide()
        })

        $(".refnum").on("input", function () {
            var val = $(".refnum").val();
            if (!val || val.length < 12) {
                $(".submitBut").addClass("disabled")
                $(".submitBut").removeClass("enable")
            } else if (val.length > 12) {
                $(".refnum").val(val.slice(0, 12))
            } else {
                $(".submitBut").removeClass("disabled")
                $(".submitBut").addClass("enable")
            }
        })

        $(".submitBut").click(function () {
            var utr = $(".refnum").val();
            if (utr.length != 12) {
                showDialog("Must enter 12 digit UTR");
            }


            if ($(this).hasClass("disabled")) {
                return false;
            } else {
                $(".submitBut").addClass("disabled")
            }


            $.post("/uploadUtr", {
                utr: utr,
                orderId: $('#orderId').val(),
            }).then((res) => {
                if (res.code != 0) {
                    // layer.msg(res.message);
                    showDialog(res.msg);
                    //alert(res.message)
                } else {
                    if (res.msg == "success") {
                        location.href = successUrl
                    } else if (res.msg == "pedding") {
                        //alert("Submitted Successfully, Please Wait")
                        showDialog("Submitted Successfully, Please Wait");
                        $(".submitBut").addClass("disabled")
                        $(".submitBut").removeClass("enable")
                    } else {
                        location.href = failUrl
                    }
                }
            })
        })

        setInterval("checkchannel()", 10000);

    })

    function showDialog(message) {
        if ($(".alertMessage").hasClass("showMsg")) {
            return;
        }
        $(".alertMessage").text(message)
        $(".alertMessage").addClass("showMsg")
        var alertWidth = $(".alertMessage").width();
        var bodyWidth = $(".head").width();
        $(".alertMessage").css("left", (bodyWidth - alertWidth) / 2 - 30)
        $(".alertMessage").fadeIn(300, function () {
            $(".alertMessage").fadeOut(5000)
            $(".alertMessage").removeClass("showMsg")
        });
    }

    var parseParam = function (param, key) {
        var paramStr = "";
        if (param instanceof String || param instanceof Number || param instanceof Boolean) {
            paramStr += "&" + key + "=" + encodeURIComponent(param);
        } else {
            $.each(param, function (i) {
                var k = key == null ? i : key + (param instanceof Array ? "[" + i + "]" : "." + i);
                paramStr += '&' + parseParam(this, k);
            });
        }
        return paramStr.substr(1);
    };

    function checkchannel() {
        $.post("../../findOrderStatus", {
            orderId: $('#orderId').val(),
        }).then((res) => {
            if(res.code == 0){
                location.href = res.data;
            }
        });
    }
</script>
</body>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div class="loading" style="display: none;">
    <p>wait...<span></span></p>
</div>

<div class="alertMessage"></div>
</html>
		<?php
 if(isset($_POST['upload_pay']))
 {
     $pay_id = $userid;	
     
     $ptype = 'UPI';
          $utr = $_POST['refrence'];
         echo $digitCount = strlen($utr);
          $utr_count ='12';
  if($digitCount==$utr_count){
 if(email_check($utr)){
         $query = mysqli_query($con,"insert into payment(`user_id`,`pay_type`,`utr`,`amount`,`pay_date`) values('$pay_id','$ptype','$utr','$amount_deposite',Now())");    
         echo '<script> swal({title: "Submit Successfully, please wait ",text: "Click Ok ",text: "Click Ok",icon: "success",}).then(function(){window.location = "details.php" });</script>';
	
            /*  echo "<script>alert ('Payment Slip Updated!')</script>";
             echo "<script>window.open('recharge_records.php','_self')</script>"; */
 }
 else
 {
             //check email
             echo '<script> swal({title: "This UTR Already availble. Please Check UTR and Updated",text: "Click Ok ",text: "Click Ok",icon: "info",}).then(function(){window.location = "pay.php" });</script>';
	
         //echo '<script>alert("This UTR Already availble. Please Check UTR and Updated");</script>';
         } }else{
             echo '<script> swal({title: "Must Enter 12 digit UTR",text: "Click Ok ",text: "Click Ok",icon: "info",}).then(function(){window.location = "pay.php" });</script>';
         }
 }
 

 function email_check($utr){
     global $con;
     
     $query =mysqli_query($con,"select * from payment where utr='$utr'");
     if(mysqli_num_rows($query)>0){
         return false;
     }
     else{
         return true;
     }
 }
     ?>