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
    
$userid = $userid_access;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Product Details</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       :root {
          --brand-green: #007749;
          --font-dark: #1e293b;
          --font-muted: #64748b;
          --bg-gray: #f8fafc;
       }

       body, html {
          background-color: var(--brand-green) !important;
          font-family: 'Inter', sans-serif;
          -webkit-font-smoothing: antialiased;
          margin: 0;
          padding: 0;
       }

       .appCapsule {
          max-width: 641px;
          margin: auto;
          min-height: 100vh;
          background-color: var(--brand-green);
          padding-bottom: 120px; /* space for fixed bottom bar */
       }

       .page-header {
          padding: 16px;
          position: sticky;
          top: 0;
          background: rgba(0, 119, 73, 0.95);
          backdrop-filter: blur(12px);
          -webkit-backdrop-filter: blur(12px);
          z-index: 50;
          display: flex;
          align-items: center;
          justify-content: space-between;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
       }

       .header-btn {
          color: white;
          text-decoration: none;
          display: flex;
          align-items: center;
          gap: 6px;
          font-weight: 500;
          font-size: 15px;
          transition: opacity 0.2s;
       }
       .header-btn:active {
           opacity: 0.7;
       }
       .header-btn i {
           font-size: 18px;
           margin-top: 1px;
       }

       .page-title {
          color: white;
          font-size: 17px;
          font-weight: 600;
          margin: 0;
          position: absolute;
          left: 50%;
          transform: translateX(-50%);
       }

       .header-spacer {
           width: 60px;
       }

       /* Product Details Card */
       .product-card {
           background: #ffffff;
           border-radius: 24px;
           box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
           margin: 16px;
           overflow: hidden;
       }

       .product-image {
           width: 100%;
           height: auto;
           background-color: var(--bg-gray);
           padding: 24px;
           object-fit: contain;
           max-height: 240px;
           border-bottom: 1px solid #f1f5f9;
       }

       .product-info {
           padding: 24px;
       }

       .product-title {
           font-size: 22px;
           font-weight: 700;
           color: var(--font-dark);
           margin-bottom: 24px;
           line-height: 1.3;
       }

       .detail-row {
           display: flex;
           justify-content: space-between;
           padding: 16px 0;
           border-bottom: 1px dashed #e2e8f0;
           font-size: 14px;
       }
       .detail-row:last-child {
           border-bottom: none;
       }
       .detail-label {
           color: var(--font-muted);
           font-weight: 500;
       }
       .detail-value {
           color: var(--font-dark);
           font-weight: 600;
       }
       .highlight-val {
           color: var(--brand-green);
           font-weight: 700;
           font-size: 15px;
       }
       
       /* Project Description */
       .project-desc {
           margin-top: 32px;
       }
       .desc-title {
           font-size: 16px;
           font-weight: 700;
           color: var(--font-dark);
           margin-bottom: 12px;
           display: flex;
           align-items: center;
           gap: 8px;
       }
       .desc-title i {
           color: var(--brand-green);
       }
       .desc-content {
           font-size: 14px;
           color: var(--font-muted);
           line-height: 1.6;
       }

       /* Fixed Bottom Buy Button */
       .fixed-bottom-bar {
           position: fixed;
           bottom: 0;
           left: 0;
           right: 0;
           max-width: 641px;
           margin: auto;
           background: rgba(255, 255, 255, 0.95);
           backdrop-filter: blur(12px);
           -webkit-backdrop-filter: blur(12px);
           padding: 16px;
           padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 16px);
           border-top: 1px solid rgba(0,0,0,0.05);
           z-index: 40;
       }
       .invest-btn {
           background-color: var(--font-dark);
           color: white;
           text-align: center;
           border: none;
           border-radius: 16px;
           padding: 16px;
           font-size: 16px;
           font-weight: 600;
           width: 100%;
           transition: transform 0.2s, background-color 0.2s;
       }
       .invest-btn:active {
           transform: scale(0.98);
           background-color: #000;
       }

       /* New Modal bottom-sheet */
       .invest-modal-container {
           display: none;
           position: fixed;
           left: 0;
           top: 0;
           width: 100%;
           height: 100%;
           background: rgba(0,0,0,0.5);
           z-index: 9999;
           backdrop-filter: blur(4px);
           opacity: 0;
           transition: opacity 0.3s ease;
       }
       .invest-modal-content {
           position: absolute;
           bottom: 0;
           left: 0;
           right: 0;
           max-width: 641px;
           margin: 0 auto;
           background-color: #ffffff;
           border-top-left-radius: 24px;
           border-top-right-radius: 24px;
           padding: 24px 24px calc(env(safe-area-inset-bottom, 0px) + 24px);
           transform: translateY(100%);
           transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
       }
       .modal-close {
           position: absolute;
           top: 20px;
           right: 20px;
           background: #f8fafc;
           border-radius: 50%;
           width: 32px;
           height: 32px;
           display: flex;
           justify-content: center;
           align-items: center;
           color: var(--font-muted);
           cursor: pointer;
           transition: background 0.2s;
       }
       .modal-close:active {
           background: #e2e8f0;
       }
    </style>
  </head>
  <body>
    <div class="appCapsule">
       <header class="page-header">
           <a href="product.php" class="header-btn">
               <i class="bi bi-chevron-left"></i> Back
           </a>
           <h1 class="page-title">Product Details</h1>
           <div class="header-spacer"></div>
       </header>

       <div class="product-card">
           <img src="asupport/package/<?php echo htmlspecialchars($pa_image);?>" class="product-image" alt="Product Image">
           
           <div class="product-info">
               <h2 class="product-title"><?php echo htmlspecialchars($pa_name); ?></h2>
               
               <div class="detail-row">
                   <span class="detail-label">Price</span>
                   <span class="detail-value">₹<?php echo number_format((float)$pa_amount, 2); ?></span>
               </div>
               <div class="detail-row">
                   <span class="detail-label">Term</span>
                   <span class="detail-value"><?php echo htmlspecialchars($pa_day); ?> days</span>
               </div>
               <div class="detail-row">
                   <span class="detail-label">Purchase limit</span>
                   <span class="detail-value"><?php echo htmlspecialchars($pa_sponser); ?></span>
               </div>
               <div class="detail-row">
                   <span class="detail-label">Daily income</span>
                   <span class="detail-value highlight-val">₹<?php echo number_format((float)$pa_com_amount, 2); ?></span>
               </div>
               <div class="detail-row">
                   <span class="detail-label">Total revenue</span>
                   <span class="detail-value">₹<?php echo number_format((float)($pa_day * $pa_com_amount), 2); ?></span>
               </div>
               <div class="detail-row">
                   <span class="detail-label">Total yield</span>
                   <span class="detail-value highlight-val"><?php echo htmlspecialchars($pa_earn); ?>%</span>
               </div>

               <div class="project-desc">
                   <div class="desc-title"><i class="bi bi-info-circle-fill"></i> Project Description</div>
                   <div class="desc-content">
                       <?php echo nl2br(htmlspecialchars($pa_text)); ?>
                   </div>
               </div>
           </div>
       </div>

       <div class="fixed-bottom-bar">
           <button onclick="openModal()" class="invest-btn">Invest in this project</button>
       </div>
    </div>

    <!-- Modal Form element wraps only the modal logic -->
    <div id="InvestModalContainer" class="invest-modal-container" onclick="if(event.target===this) closeModal()">
        <div id="InvestModalContent" class="invest-modal-content">
            <div class="modal-close" onclick="closeModal()"><i class="bi bi-x-lg"></i></div>
            <h4 style="font-weight: 700; margin-bottom: 24px; font-size: 20px; color: var(--font-dark);">Invest Confirmation</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <!-- Current Wallet -->
                <div style="background: #f8fafc; border-radius: 16px; padding: 16px; border: 1px solid #e2e8f0;">
                    <p style="color: var(--font-muted); font-size: 12px; font-weight: 500; margin-bottom: 4px;">Current Wallet</p>
                    <p style="font-size: 16px; font-weight: 700; color: #3b82f6; margin-bottom: 12px;">₹<?php echo number_format((float)$current_bal, 2); ?></p>
                    <a href="invitiation.php" style="display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: var(--font-dark); font-size: 12px; font-weight: 600;">
                        <span style="display: flex; align-items: center; gap: 6px;"><i class="bi bi-person-plus-fill" style="color: var(--font-muted);"></i> Invite</span>
                        <i class="bi bi-chevron-right" style="color: #cbd5e1;"></i>
                    </a>
                </div>
                
                <!-- Recharge Wallet -->
                <div style="background: #f8fafc; border-radius: 16px; padding: 16px; border: 1px solid #e2e8f0;">
                    <p style="color: var(--font-muted); font-size: 12px; font-weight: 500; margin-bottom: 4px;">Recharge Wallet</p>
                    <p style="font-size: 16px; font-weight: 700; color: var(--brand-green); margin-bottom: 12px;">₹<?php echo number_format((float)$fran_bal, 2); ?></p>
                    <a href="recharge.php" style="display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: var(--font-dark); font-size: 12px; font-weight: 600;">
                        <span style="display: flex; align-items: center; gap: 6px;"><i class="bi bi-wallet-fill" style="color: var(--font-muted);"></i> Recharge</span>
                        <i class="bi bi-chevron-right" style="color: #cbd5e1;"></i>
                    </a>
                </div>
            </div>

            <!-- Pre-checkout Summary -->
            <div style="background: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9; padding: 0 16px;">
                <div class="detail-row">
                    <span class="detail-label">Price</span>
                    <span class="detail-value">₹<?php echo number_format((float)$pa_amount, 2); ?></span>
                </div>
                <a href="#" class="detail-row text-decoration-none" style="display: flex;">
                    <span class="detail-label">Discount coupon</span>
                    <span class="detail-value" style="color: #3b82f6;">Choose <i class="bi bi-chevron-right ms-1" style="font-size: 12px;"></i></span>
                </a>
                <div class="detail-row">
                    <span class="detail-label">Actual Amount Paid</span>
                    <span class="detail-value highlight-val">₹<?php echo number_format((float)$pa_amount, 2); ?></span>
                </div>
            </div>

            <!-- Confirm Button -->
            <form action="" method="post" enctype="multipart/form-data" class="mt-4">
                <input type="hidden" name="pa_amount" value="<?php echo htmlspecialchars($pa_amount); ?>">
                <input type="hidden" name="pa_day" value="<?php echo htmlspecialchars($pa_day); ?>">
                <button type="submit" name="bal_pay_cur" class="invest-btn" style="background-color: var(--brand-green);">Confirm Payment</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
      var InvestModalContainer = document.getElementById("InvestModalContainer");
      var InvestModalContent = document.getElementById("InvestModalContent");

      function openModal() {
          InvestModalContainer.style.display = "block";
          setTimeout(function () {
              InvestModalContainer.style.opacity = "1";
              InvestModalContent.style.transform = "translateY(0%)";
          }, 10);
      }

      function closeModal() {
          InvestModalContainer.style.opacity = "0";
          InvestModalContent.style.transform = "translateY(100%)";
          setTimeout(function () {
              InvestModalContainer.style.display = "none";
          }, 300);
      }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

<?php
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
                popupMessage.style.top = '10%';
                popupMessage.style.left = '50%';
                popupMessage.style.transform = 'translate(-50%, 0)';
                popupMessage.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                popupMessage.style.color = 'white';
                popupMessage.style.padding = '12px 24px';
                popupMessage.style.borderRadius = '24px';
                popupMessage.style.textAlign = 'center';
                popupMessage.style.zIndex = '99999';
                popupMessage.style.fontWeight = '500';
                popupMessage.style.fontFamily = 'Inter, sans-serif';
                popupMessage.style.boxShadow = '0 8px 16px rgba(0,0,0,0.1)';
                document.body.appendChild(popupMessage);

                setTimeout(function() {
                    popupMessage.style.opacity = '0';
                    popupMessage.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        document.body.removeChild(popupMessage);
                        window.open('product-details.php?pr_id=".$pac_id."','_self');
                    }, 500);
                }, 3000);
            </script>";
         }
    } else { 
        echo "<script>
            const popupMessage = document.createElement('div');
            popupMessage.textContent = 'Your balance is insufficient';
            popupMessage.style.position = 'fixed';
            popupMessage.style.top = '10%';
            popupMessage.style.left = '50%';
            popupMessage.style.transform = 'translate(-50%, 0)';
            popupMessage.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
            popupMessage.style.color = 'white';
            popupMessage.style.padding = '12px 24px';
            popupMessage.style.borderRadius = '24px';
            popupMessage.style.textAlign = 'center';
            popupMessage.style.zIndex = '99999';
            popupMessage.style.fontWeight = '500';
            popupMessage.style.fontFamily = 'Inter, sans-serif';
            popupMessage.style.boxShadow = '0 8px 16px rgba(0,0,0,0.1)';
            document.body.appendChild(popupMessage);

            setTimeout(function() {
                popupMessage.style.opacity = '0';
                popupMessage.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    document.body.removeChild(popupMessage);
                    window.open('product-details.php?pr_id=".$pac_id."','_self');
                }, 500);
            }, 3000);
        </script>";
     }
}
?>
