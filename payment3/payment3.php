<?php
	date_default_timezone_set('Asia/Kolkata');
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('../user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
$userid = $userid_access;
$amount = $_SESSION['amount'];
 $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `bank`"));
   $bank_upi = $query['bank_upi'];
   $bank_scan = $query['bank_scan'];
   
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- b icon  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <style>

        body{
            background-image: linear-gradient(#ffdff775 0%, #fdcbf179 1%, #d7f2ff86 100%);
            padding-bottom: 20px;
            
        }
        header .amtBox{
            background-color: #19B2FE;
            padding: 2.5rem ;
            text-align: center;
            color: aliceblue;
            border-top-left-radius: 30px;
            border-bottom-right-radius: 30px;

        }

        .amtBox h1{
            font-size: 3rem;
            font-weight: bold;
        }

        .mainBox1{
            background-color: #fff;
            padding:30px;
            border-radius: 13px;

        }

        .mainBox1 .paytm{
            border: 1px solid rgb(165, 165, 165);
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .mainBox1 .cardbox2 img{
            width: 70px;
        }

        .mainBox1 .cardbox2{
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .mainBox1 .cardbox2 .inner{
            border: 1px solid rgb(165, 165, 165);
            width: 100%;
            height: 50px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding:0 15px;
            margin-top: 15px;
            
        }



        /* mainbox 2 ============================== */
        

                 /* .copybox{
                    position: relative;
                } */

                .copybox button{
                    /* position: absolute;
                    right: 0; */
                    border: none;
                    background-color: #19B2FE;
                    padding: 7px 20px;
                    color: white;
                    border-top-right-radius: 20px;
                    border-bottom-right-radius: 20px;

                }

                input{
                    border: 1px solid #858585;
                }

                .blue{
                    color: #19B2FE;
                }

                a{
                    text-decoration: none;
                }

                 button:active{
                    transform: scale(1.03);
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
  <body class="">
    
    <header class="container py-4  px-4">
        <div class="amtBox">
            <h1>₹ <?php echo $amount; ?>.00</h1>
        </div>
    </header>

   
    <main class="container ">
        <div class="row px-3">
            <div class="col-12 mainBox1">
                
                <div class="cardbox">
                    <b>Method 1: Fast Payment (Choose App)</b>
                    <a class="paytm mt-3">
                        <img src="img/paytm.png" alt="" class="img-fluid">
                        <b>Recommend <i class="bi bi-chevron-right ps-1"></i></b>

                    </a>
                </div>

                <div class="cardbox2">

                    <a class="left inner" href="phonepe://pay">
                        <img src="img/PhonePe.png" alt="" style="width: 100px;" class="img-fluid">
                        <i class="bi bi-chevron-right" ></i>
                    </a>

                    <a class="left inner" href="gpay://upi">
                        <img src="img/gpay.png" alt=""   class="img-fluid">
                        <i class="bi bi-chevron-right"></i>
                    </a>

                </div>

                <div class="cardbox2 pb-3">

                    <a class="left inner" href="upi://pay" >
                        <img src="img/MobiKwik.png"  alt="" class="img-fluid">
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a class="left inner" href="upi://pay" >
                        <img src="img/pay5.png" alt=""   class="img-fluid">
                        <i class="bi bi-chevron-right"></i>
                    </a>

                </div>


            </div>

            
            <!-- mainbox 2=======================  -->
            <div class="col-12 mainBox1 mainBox2 mt-3">

                <div class="cardbox">
                    <b>Method 1: Fast Payment (Choose App)</b>
                    <br>
                    <span class="blue">1. copy the below Beneficiary UPI</span>
                    <div class="copybox d-flex mt-1">
                    
                    <input type="text" value="<?php echo $bank_upi; ?>" style="width: 100% ;border-bottom-left-radius: 20px; border-top-left-radius: 20px; padding: 5px 10px; text-align: center;" id="copyText" readonly>

                    <button id="copyButton"  onclick="copy('text-1')" class="copy-btn ">Copy</button>

                    </div>

                    <div class="submitBox mt-3">
                    <span class="blue">2. Submit UTR/Reference No/Ref No.</span>
                     <form action="" method="post" enctype="multipart/form-data">
                    <input type="text" name="refrence" placeholder="Input 12-digit here" style="width: 100%; border-radius: 20px; padding: 5px 10px; text-align: center;" id="" class="mt-1">
                    <span>Tips: Open your UPI Wallet and complete the transfer Record your Reference No.(Ref No.) after payment</span>
<br>
                    <button style="width: 100%; border: none; background-color: #19B2FE; margin: 10px 0; padding: 6px; border-radius: 20px; color: white; " name="upload_pay">submit</button>
                     <form>
                </div>

                <div class="notice  mt-1">
                    <p>1. Please contact your customer care if you have any issue. Your ID: <br>
                    <a href="" class="">987654321123456</a>
                <br>
                2. Please select the payment method you need and make sure the app has been installed.
            </p>
                </div>

                </div>
            

            </div>
        </div>



       
    </main>
    
  



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
    popupMessage.textContent = '<?php echo $bank_upi; ?>';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    // Remove the popup after a delay
    setTimeout(() => {
        popupMessage.remove();
    }, 2000); // Adjust the delay as needed
});

      </script>
  </body>
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
         $query = mysqli_query($con,"insert into payment(`user_id`,`pay_type`,`utr`,`amount`,`pay_date`) values('$pay_id','$ptype','$utr','$amount',Now())");    
        // echo '<script> swal({title: "Submit Successfully, please wait ",text: "Click Ok ",text: "Click Ok",icon: "success",}).then(function(){window.location = "../details.php" });</script>';
                echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Submit Successfully, please wait ';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('../recharge-records.php','_self');
				}, 3000);
			});
			</script>";
	
            /*  echo "<script>alert ('Payment Slip Updated!')</script>";
             echo "<script>window.open('recharge_records.php','_self')</script>"; */
 }
 else
 {
             //check email
            // echo '<script> swal({title: "This UTR Already availble. Please Check UTR and Updated",text: "Click Ok ",text: "Click Ok",icon: "info",}).then(function(){window.location = "../pay.php" });</script>';
              echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'This UTR Already availble. Please Check UTR and Updated';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('payment3.php?amount=".$amount."','_self');
				}, 3000);
			});
			</script>";
	
         //echo '<script>alert("This UTR Already availble. Please Check UTR and Updated");</script>';
         } }else{
              echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Must Enter 12 digit UTR';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('payment3.php?amount=".$amount."','_self');
				}, 3000);
			});
			</script>";
            // echo '<script> swal({title: "Must Enter 12 digit UTR",text: "Click Ok ",text: "Click Ok",icon: "info",}).then(function(){window.location = "../pay.php" });</script>';
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