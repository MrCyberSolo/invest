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
    $withdrawal_passwrod = $get_user['withdrawal_passwrod'];
    $bank_name = $get_user['bank_name'];
    $account_no = $get_user['account_no'];
    $ifsc_code = $get_user['ifsc_code'];
	date_default_timezone_set('Asia/Kolkata');
  $get_balance = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `income` WHERE `userid`='$userid_access'"));
	$current_bal = $get_balance['current_bal'];
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Withdraws</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --light-blue:#07CCFF;
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
        background-color: #014f97;

       }

       
       .appBody{
        padding: 4.5rem 0;
       }


       /* body{
        background-color: #07CCFF;
       } */

       /* input-section================================  */
       .input-section .inner{
        display: flex;
        justify-content: space-between;
        padding:10px 0;
        border-bottom:1px solid #aeaeae8e;
       }

       /* .input-section .inner p:last-child{
        color: #a1a1a1;
       } */


/*  
       .inputBox-three{
        padding: 0 10px;
       } */

            .inputBox-three input{
                background-color: #FCFAED;
                padding: 12px 10px;
                border-radius: 25px;
                margin-top: 5px;
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
    
      </style>
  </head>
  <body>
<div class="appCapsule container">
  <header class="row fixed-top">
    <div class="text-white py-3">
            <div class=" px-3 text-decoration-none text-white d-flex align-items-center justify-content-between">
                <a href="me.php" class="nav-link left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
            </a>

                <h6>Withdraws</h6>

                <a href="withdraw-record.php" class="nav-link">
                <h6>Records</h6>
              </a>
        </div>
        </div>

  </header>

  <div class="row appBody px-2">
    <div class="col-12  input-section px-4" style="background-color:white; border-radius:10px; padding:15px;  ">
        <div class="inputBox mt-2">
            <h6>Bank account</h6>
            <div class="inner ">
                <p>Bank</p>
                <p><?php echo $bank_name; ?></p>
            </div>
    
            <div class="inner">
                <p>Rename</p>
                <p><?php echo $name; ?></p>
            </div>
    
            <div class="inner">
                <p>Bank account </p>
                <p><?php echo $account_no; ?></p>
            </div>
    
            <div class="inner">
                <p>IFSC</p>
                <p><?php echo $ifsc_code; ?></p>
            </div>
    
        </div>

        <div class="inputBox2 mt-2">
            <b>Balance: <?php echo $current_bal;?> RS</b>
             <button id="allButton" style="color: white; background: rgb(255, 34, 34); border: none; border-radius: 5px;">All</button>

        </div>

            
        <div class="inputBox-three mt-3 ">
        <form action="" method="post" enctype="multipart/form-data">   
            <div class="inne">
                <label for="amount" class="fw-bold mb-1">Amount:</label>
                <input type="text" class="form-control" name="pay_amount" id="amount" placeholder="Please enter the amount">
            </div>

            <div class="inne mt-3">
                <label for="inptwo" class="fw-bold mb-1">Payment password: </label>
                <input type="password"   name="enterpassword" class="form-control" name="" id="inptwo" placeholder="Plese enter the payment password">
            </div>

            <div class="sub-btn px-3 d-grid mt-3">
                <button class="btn" type="submit" name="bal_pay"  style="background: black; color: white; border-radius: 30px; padding: 10px; ;;">Submit</button>
            </div>
          </form>

               <div class="tips mt-3">
                <p style=" text-align: justify; font-size: 14px; ">
                    1: Valid members can apply for withdrawal. The number of withdrawals is unlimited. The minimum withdrawal amount is 110rs.

                    <br>                    
                    2: IFSC should be 11 characters and 5th character should be 0. If you fill in wrong bank information, your withdrawal will fail.

                    <br>
                    3: Withdrawal fee: 6%
                    <br>
                    4:Withdrawal time:24-48hours
                    <br>
                   <!-- Notice:
                    <br>
                    IFSC must be 11 characters, with the 5th character being 0. If the bank information is filled in incorrectly, the withdrawal will fail.

                    <br>
                    The maximum time for banks to process orders is 3 working days, and for smaller banks it is 3-7 working days. Please wait patiently for the bank's processing results. If you want faster payments, try to use a very large bank for withdrawals. If you have any questions, you can contact your customer service manager, thank you for your support!

                </p> -->
               </div>
        </div>       
    </div>
  </div>

</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Get the "All" button element
        const allButton = document.getElementById('allButton');

        // Add click event listener to the button
        allButton.addEventListener('click', function() {
            // Get the input field
            const amountInput = document.getElementById('amount');
            // Set the input field value to 500 (or any default value)
            amountInput.value = <?php echo $current_bal;?>;
        });
    </script>
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
$account_limit = 0;
$query = mysqli_query($con,"select * from income where userid='$userid_access' order by id desc");
if(mysqli_num_rows($query)>0)
{
	while($row=mysqli_fetch_array($query))
	{
		$id = $row['id'];
		$account_limit = $row['account_limit'];
		
		//$account_limit = $account_limit + $account_limits;
	}
}
		
?>
<?php					
$kyc_status = 0;
$query = mysqli_query($con,"select * from user where email='$userid_access' order by user_id desc");
if(mysqli_num_rows($query)>0)
{
	while($row=mysqli_fetch_array($query))
	{
		$user_ids = $row['user_id'];
		$kyc_status = $row['kyc_status'];
		$password = $row['withdrawal_passwrod'];
		$status = $row['status'];
		
		//$account_limit = $account_limit + $account_limits;
	}
}
		
?>

		<?php

if(isset($_POST['bal_pay']))
{
	$pay_id = $userid_access;	
	$amount = $_POST['pay_amount'];
	$enterpassword = $_POST['enterpassword'];
if($amount>=110)
    {
	if($password == $enterpassword ) 
        {
        if($amount <= $current_bal) 
            {
            //if($amount  <= $account_limit )
               // {
                if($kyc_status == 'Paid' || $kyc_status=='Pending' )
                    {
                    if($status =='Active' || $status =='Deactive'){
                       		$admin_bal = $amount * 6/100;
							$tds_amount = $amount * 0/100;
							$pay_bal =  $amount - ($admin_bal + $tds_amount );
							$today = date('Y-m-d');
							$todaytime =date('Y-m-d H:i:s');
							$mysqltime = date('Y-m-d H:i:s');
							mysqli_query($con,"INSERT INTO `income_received`(`userid`, `amount`, `donation`, `tds`, `f_amount`,`date`, `updated_date`) values('$pay_id','$amount','$admin_bal','$tds_amount','$pay_bal','$today','$todaytime')");
    						mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`-$amount WHERE `userid`='$pay_id'");
    						mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$userid_access','Withdrawal Amount','$amount','Debit','$mysqltime')");
	
    						
							//echo '<script> swal({title: "Payment Request Submit Succesfully",text: "Click Ok ",text: "ok",icon: "success",}).then(function(){window.location = "withdrawl.php" });</script>';
							// echo "<script>alert ('Payment Request Submit Succesfully')</script>";
                        //echo "<script>window.open('withdrawl.php','_self')</script>"; 
     ?>
     <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Success !';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "withdraw.php" 
        },
    2000);
    </script>
                    
                  <?php   }
                    
                else
                    {  
                       // echo "<script>alert ('Your account is not active.')</script>";
                       // echo "<script>window.open('withdrawl.php','_self')</script>"; 
                    ?>
                    <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Withdrawal requires purchase of atleast one product';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "withdraw.php" 
        },
    2000);
    </script>
                    
                    <?php
                    }
                    
                }
                else
                    {
                        //echo '<script> swal({title: "Your Kyc Status is Pending Updated Bank & Document Details",text: "Click Ok ",text: "Try Again",icon: "error",}).then(function(){window.location = "withdrawl.php" });</script>';
     
                   // echo "<script>alert ('Your Kyc Status is Pending Updated Bank & Document Details')</script>";
                  //  echo "<script>window.open('withdrawl.php','_self')</script>"; 
                         ?>
              <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Your Kyc Status is Pending Updated Bank & Document Details';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "withdraw.php" 
        },
    2000);
    </script>
            <?php
                    } 
                //}
            //else
                //{
                    //echo '<script> swal({title: "Your Account Limited Higher than your request amount Please Check Amount Limited",text: "Click Ok ",text: "Try Again",icon: "error",}).then(function(){window.location = "withdrawl.php" });</script>';
     
                // echo "<script>alert ('Your Account Limited Higher than your request amount Please Check Amount Limited')</script>";
                //echo "<script>window.open('withdrawl.php','_self')</script>";
                //}
            }
        else 
            {
                //echo '<script> swal({title: "In suffecent balance Please Check Amount",text: "Click Ok ",text: "Try Again",icon: "error",}).then(function(){window.location = "withdrawl.php" });</script>';
     
             //echo "<script>alert ('In suffecent balance Please Check Amount')</script>";
           // echo "<script>window.open('withdrawl.php','_self')</script>";
            ?>
              <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'In suffecent balance';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "withdraw.php" 
        },
    2000);
    </script>
            <?php
            }
        }
    else
        {
            //echo '<script> swal({title: "Please Withdrawal  Password Check",text: "Click Ok ",text: "Try Again",icon: "error",}).then(function(){window.location = "withdrawl.php" });</script>';
     
             //echo "<script>alert ('Please Password Check')</script>";
            //echo "<script>window.open('withdrawl.php','_self')</script>"; 
            ?>
              <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Please Password Check';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "withdraw.php" 
        },
    2000);
    </script>
            <?php
            
        }
    }
    else{
       // echo '<script> swal({title: "Minume Withdrawl Request 150/-",text: "Click Ok ",text: "Try Again",icon: "error",}).then(function(){window.location = "withdrawl.php" });</script>';
     
        // echo "<script>alert ('Minume Withdrawl Request 110/-')</script>";
        //echo "<script>window.open('withdrawl.php','_self')</script>"; 
        ?>
             <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Minimum Withdrawl Rs.110';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "withdraw.php" 
        },
    2000);
    </script>
        <?php
    }
}


	?>


