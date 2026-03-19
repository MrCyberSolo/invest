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
  $direct_refer_count =  mysqli_num_rows(mysqli_query($con, "select * from user where under_userid='$userid_access' AND status='Active'"));
  if(isset($_GET['pr_id'])){
    $pac_id = $_GET['pr_id'];
}

    $get_package = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `package` WHERE `pa_id`='$pac_id'"));  
    $pac_id = $get_package['pa_id'];
    $pa_amount = $get_package['pa_amount'];
    $pa_day = $get_package['pa_day'];
    $pa_number = $get_package['pa_number'];
    $pa_sponser = $get_package['pa_sponser'];
    $status = $get_package['status'];
    $pa_type = $get_package['pa_type'];
    $pa_com_amount = $get_package['pa_com_amount'];
    $pa_name = $get_package['pa_name'];
    $pa_text = $get_package['pa_text'];
    $pa_image = $get_package['pa_image'];
    $pa_cash = $get_package['pa_cash'];
     $pa_limit = $get_package['pa_limit'];
     $pa_earn = $get_package['pa_earn'];
    
   //echo $currentURL = "http://$_SERVER[HTTP_HOST]";
   $userid = $userid_access;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product details</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#213955;
        --light-blue:#009a5f;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule{
        padding-bottom: 5rem;
        max-width: 641px;
       }

       header h5{
        font-size: 18px;
       }

       

       body{
        /* background-color: #009a5f; */
        color: white;
       }

       

.product-details .card{
    background-color: transparent;
    border: none;
    color: white;
}

.product-details .card-body{
  padding:  10px !important;
  color: white;
}

.product-details img{
  border-radius: 10px;
}

.product-details .inner{
  margin-bottom: 7px;
}

/* .product-details .inner p{
  color: #ffffff;
  font-size: 15px;
}

.product-details .items p{
  color: #fff;
} */



    
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container">
  <div class="row bg-dark fixed-top">
    <header class="d-flex justify-content-between " style="padding: 10px 0; ">

      <div class="hellow"> <a href="product.php" class="nav-link d-flex align-items-center ps-3">
        <i class="bi bi-chevron-left"></i>
        <p>Back</p>
        </a>
      </div>

      <h5 >Product</h5>

      <div class="px-4"></div>

      <!-- <div class="text-white py-2 ">
          <a href="product.php" class="nav-link text-white d-flex align-items-center">
          <i class="bi bi-chevron-left"></i>
          <p>Back</p> 
          </a>
      </div>

      <div class="header-title text-center pt-3 ">
          <h5 class="mb-2">Product</h5>
          <div class="line"></div>
      </div>

      <div class="px-3"></div> -->

    
</header>
  </div>

  <div style="background-color: #007749; padding-top: 60px; padding-bottom: 20px; margin: 0 -12px;">
  <div class="row product-details px-3">
    <div class="col-12 mx-auto px-0">
        <div class="inner">
            <img src="" class="img-fluid" alt="">

            <div class="card">
                <img src="asupport/package/<?php echo $pa_image;?>" class="card-img-top" alt="...">
                <div class="card-body px-0 py-2">
                  <h4 class="card-text"><?php echo $pa_name; ?></h4>
                  <div class="items">

                    <div class="inner d-flex justify-content-between">
                      <p>Price</p>
                      <p><?php echo $pa_amount; ?></p>
                    </div>
  
                    <div class="inner d-flex justify-content-between">
                      <p>Term</p>
                      <p><?php echo $pa_day; ?> days</p>
                    </div>
  
                    <div class="inner d-flex justify-content-between">
                      <p>purchase limit</p>
                      <p><?php echo $pa_sponser; ?></p>
                    </div>
  
                    <div class="inner d-flex justify-content-between">
                      <p>Daily income</p>
                      <p><?php echo $pa_com_amount; ?> RS</p>
                    </div>
  
                    <div class="inner d-flex justify-content-between">
                      <p>Total revenue</p>
                      <p><?php echo $total_revenu = $pa_day * $pa_com_amount; ?>RS</p>
                    </div>
  
                    <div class="inner d-flex justify-content-between">
                      <p>total yield</p>
                      <p><?php echo $pa_earn; ?>%</p>
                    </div>

                  </div>

                <hr class="m-0 bg-dark">

                  <div class="description mt-3">
                    <h4>Project description</h4>
                    <p><?php echo $pa_text; ?></p>
                  </div>
                </div>
              </div>
        </div>
    </div>
    </div>
  </div> <!-- Close green wrapper -->

  <div class="btnBox d-grid fixed-bottom p-2 mb-2" style="max-width: 641px; margin: auto;">
    <button onclick="openModal()" type="button" class="btn text-white" style="background-color: #131313; border-radius: 20px; padding: 10px; border: none; font-size: 16px;"> Invest this project</button>
  </div>
  </div>



  <!-- modal open bottom  -->
  <style>

  
    /* Styling for modal container */
    .invest-modal-container {
        display: none;
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        transition: opacity 0.3s ease;
        z-index: 9999;
    }

    /* Styling for modal content */
    .invest-modal-content {
        position: relative;
        background-color: #fff;
        margin: auto;
        padding: 10px;
        width: 100%;
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

     /* Styling for close button */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }

    .invest-modal-container p{
        margin: 0;
        line-height: 17px;
        font-size: 13px;
        
    }

    .invest-modal-container .inner {
        width: 100%;
    }

    .invest-modal-container .inner .top{
        background-color: #5564E6;
        padding: 8px;
        color: white;


    }

    .invest-modal-container img{
        width: 17px;
    }

    .invest-modal-container .botom{
        font-size: 17px;
        background-color: #FCFAED;
    }

    .invest-modal-container .botom p, .right{
        color: #EBA73C;
    }

    .modal-body .inner{
        display: flex; 
        justify-content: space-between;
        padding: 20px 0;
        border-top: 1px solid #efefef;
        margin-top: 5px;

        
    }

    .modal-body .inner p{
        font-size: 14px;
        color: black;
    }

    .modal-body .inner p:first-child{
        color: rgb(149, 149, 149);
    }

    .invest-modal-container .modal-header{
        padding-top: 40px;
    }


   
</style>


  <div id="InvestModalContainer" class="invest-modal-container ">
    <div id="InvestModalContent" class="invest-modal-content  ">

        <span class="close-btn text-end fs-5 text-secondary" onclick="closeModal()"><i class="bi bi-x-circle"></i></span>

        <div class="modal-header d-flex justify-content-between gap-2">
            <div class="inner">
                <div class="top">
                    <p><?php echo $current_bal; ?>RS</p>
                    <p >Current wallet</p>
                </div>
                <div class="botom   d-flex justify-content-between align-items-center px-2">
                    <div class="left">
                  <a href="invitiation.php" style="text-decoration: none;">
                    <img src="img/icons/invitation.png" alt="" >
                    <p>invitation</p>
                    </div>
                    <div class="right">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    </a>
                </div>
               
            </div>

            <div class="inner">
                <div class="top">
                    <p><?php echo $fran_bal; ?>RS</p>
                    <p>Recharge wallet</p>
                </div>
                <div class="botom   d-flex justify-content-between align-items-center px-2">
                <div class="left">  
                <a href="recharge.php" style="text-decoration: none;">    
                    
                      <img src="img/icons/recharge.png" alt="" >
                      <p>Recharge</p>
                    </div>
                    <div class="right ">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                  </a>
                </div>
               
            </div>
        </div>


        <div class="modal-body">
           <div class="inner">
            <p>Price</p>
            <p><?php echo $pa_amount; ?>RS</p>
           </div>

           <a href="" class="inner text-decoration-none text-dark">
            <p>Discount coupon</p>
            <p>please choose ></p>
           </a>

           <div class="inner">
            <p>The amount actually paid</p>
            <p><?php echo $pa_amount; ?>RS</p>
           </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
        		<input type="hidden"  name="pa_amount" value="<?php echo $pa_amount; ?>">
        		<input type="hidden"  name="pa_day" value="<?php echo $pa_day; ?>">
            <div class="modalFooter d-grid pb-2">
              <button type="submit" name="bal_pay_cur" class="btn btn-dark rounded-pill py-2">Confirm</button>
            </div>        		
		    </form>
     
        
    </div>
</div>
<!-- modal open bottom  -->
<script>
  var InvestModalContainer = document.getElementById("InvestModalContainer");
  var InvestModalContent = document.getElementById("InvestModalContent");

  function openModal() {
      InvestModalContainer.style.display = "block";
      setTimeout(function () {
          InvestModalContent.style.transform = "translateY(0%)";
      }, 10);
  }

  function closeModal() {
      InvestModalContent.style.transform = "translateY(100%)";
      setTimeout(function () {
          InvestModalContainer.style.display = "none";
      }, 300);
  }
</script>










</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
<?php
//$order_count = mysqli_num_rows(mysqli_query($con,"select * from order_book where o_userid = '$userid' AND o_pac_id='$pac_id'"));
//$pa_sponser;
//$query_refer = mysqli_num_rows(mysqli_query($con, "select * from user where email='$userid' AND status='Active'"));
if(isset($_POST['bal_pay_cur']))
{
    if($fran_bal >= $pa_amount )
    {   
        $order_count = mysqli_num_rows(mysqli_query($con,"select * from order_book where o_userid = '$userid' AND o_pac_id='$pac_id'"));
        if($order_count < $pa_sponser){
            $pa_amount = $_POST['pa_amount'];
            $pa_day = $_POST['pa_day'];

            // Get user Details        
            $get_user = "select * from user where email='$userid'";
            $run_user = mysqli_query($con, $get_user);  
            $row_user= mysqli_fetch_array($run_user);   
            $pay_spo_id = $row_user['under_userid'];
            
            $mysqltime = date('Y-m-d H:i:s');
            $today= date('Y-m-d');
            mysqli_query($con,"INSERT INTO `order_book`(`o_userid`, `o_underid`, `o_user_type`,`o_pac_id`, `o_amount`, `o_percentage`, `o_days`, `o_date`)  values('$userid','$pay_spo_id','$pa_type','$pac_id','$pa_amount','$pa_com_amount','$pa_day', Now())");
            mysqli_query($con,"UPDATE `income` SET `fran_bal`=`fran_bal`- $pa_amount ,`account_limit`='$pa_limit' WHERE `userid`='$userid'");
            mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$userid','Product Purchase','$pa_amount','Debit','$mysqltime')");
            mysqli_query($con,"UPDATE `user` SET `status`='Active',`package`='$pa_amount',`pac_offer`=`pac_offer`+$pa_amount,`act_date`='$today' WHERE `email`='$userid'");
           // mysqli_query($con,"UPDATE `user` SET `status`='Active' WHERE `email`='$pay_spo_id'");
             $query_refer = mysqli_num_rows(mysqli_query($con, "select * from order_book where o_userid='$userid'"));
            if($query_refer=='1')
            {
                mysqli_query($con,"INSERT INTO `cashback`(`ca_userid`, `ca_order_id`,`ca_amount`, `ca_type`) VALUES ('$userid','$userid','110','Joining Bonus')");   
                mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_pay`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES('$userid','$userid','Joining Bonus','110','Credit','$mysqltime')");   
                mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+ 110 WHERE `userid`='$userid'");
                
                mysqli_query($con,"INSERT INTO `cashback`(`ca_userid`, `ca_order_id`,`ca_amount`, `ca_type`) VALUES ('$pay_spo_id','$userid','$pa_cash','Invitation bonus')");   
                mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_pay`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES('$pay_spo_id','$userid','Invitation bonus','$pa_cash','Credit','$mysqltime')"); 
                mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+ $pa_cash WHERE `userid`='$pay_spo_id'");
            } 
               
            
            $order_find = mysqli_fetch_array(mysqli_query($con,"select * from order_book where o_userid='$userid' order by o_id DESC LIMIT 0,1 "));
            $order_id = $order_find['o_id'];
               
            $_SESSION['order_id'] = $order_id;    
            $_SESSION['total_amount'] = $pa_amount;    
            $_SESSION['pacid'] = $pac_id;    
            include("earn_dis.php");
        
        }else{
            echo"<script>
                const popupMessage = document.createElement('div');
                popupMessage.textContent = 'Purchase Limit Over';
                popupMessage.style.position = 'fixed';
                popupMessage.style.top = '50%';
                popupMessage.style.left = '50%';
                popupMessage.style.transform = 'translate(-50%, -50%)';
                popupMessage.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                popupMessage.style.color = 'white';
                popupMessage.style.padding = '5px';
                popupMessage.style.borderRadius = '10px';
                popupMessage.style.textAlign = 'center';
                document.body.appendChild(popupMessage);

                setTimeout(function() {
                    document.body.removeChild(popupMessage);
                    window.open('product-details.php?pr_id=".$pac_id."','_self');
                }, 3000);
            </script>";
         }
    } else { 
        echo "<script>
            const popupMessage = document.createElement('div');
            popupMessage.textContent = 'Your balance is insufficient';
            popupMessage.style.position = 'fixed';
            popupMessage.style.top = '50%';
            popupMessage.style.left = '50%';
            popupMessage.style.transform = 'translate(-50%, -50%)';
            popupMessage.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            popupMessage.style.color = 'white';
            popupMessage.style.padding = '5px';
            popupMessage.style.borderRadius = '10px';
            popupMessage.style.textAlign = 'center';
            document.body.appendChild(popupMessage);

            setTimeout(function() {
                document.body.removeChild(popupMessage);
                window.open('product-details.php?pr_id=".$pac_id."','_self');
            }, 3000);
        </script>";
     }
     
}
?>
