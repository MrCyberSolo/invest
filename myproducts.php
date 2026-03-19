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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>My Products</title>

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
          padding-bottom: 90px;
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

       .tabs-container {
           padding: 16px;
       }

       .tabs-wrapper {
           background: rgba(255, 255, 255, 0.15);
           border-radius: 16px;
           display: flex;
           padding: 6px;
           gap: 6px;
       }

       .tab-btn {
           flex: 1;
           background: transparent;
           border: none;
           color: rgba(255, 255, 255, 0.7);
           font-weight: 600;
           font-size: 14px;
           padding: 12px 0;
           border-radius: 12px;
           transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
       }
       
       .tab-btn.active {
           background: #ffffff;
           color: var(--brand-green);
           box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
       }

       .total-amount-box {
           text-align: center;
           color: rgba(255, 255, 255, 0.9);
           font-weight: 500;
           font-size: 13px;
           margin-top: 12px;
       }

       .total-amount-box span {
           font-weight: 700;
           color: #ffffff;
           font-size: 15px;
       }

       .content-wrapper {
           padding: 0 16px 16px;
       }

       .tabcontent {
           display: none;
           animation: fadeIn 0.3s ease;
       }

       @keyframes fadeIn {
           from { opacity: 0; transform: translateY(10px); }
           to { opacity: 1; transform: translateY(0); }
       }

       .product-card {
           background: #ffffff;
           border-radius: 20px;
           padding: 16px;
           display: flex;
           gap: 16px;
           margin-bottom: 16px;
           box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
           text-decoration: none;
       }

       .product-image-box {
           width: 90px;
           height: 90px;
           background: var(--bg-gray);
           border-radius: 16px;
           display: flex;
           justify-content: center;
           align-items: center;
           padding: 8px;
           box-shadow: inset 0 2px 8px rgba(0,0,0,0.02);
       }

       .product-image-box img {
           max-width: 100%;
           max-height: 100%;
           object-fit: contain;
       }

       .product-details {
           flex: 1;
           display: flex;
           flex-direction: column;
       }

       .product-name {
           font-size: 16px;
           font-weight: 700;
           color: var(--font-dark);
           margin-bottom: 12px;
           display: flex;
           align-items: center;
           gap: 6px;
       }

       .detail-row {
           display: flex;
           justify-content: space-between;
           font-size: 13px;
           margin-bottom: 6px;
       }

       .detail-label {
           color: var(--font-muted);
           font-weight: 500;
       }

       .detail-value {
           color: var(--font-dark);
           font-weight: 600;
       }

       .amount-yield {
           color: var(--brand-green);
           font-weight: 700;
       }

       .action-row {
           margin-top: 12px;
           display: flex;
           justify-content: flex-end;
           border-top: 1px dashed #e2e8f0;
           padding-top: 12px;
       }

       .receive-btn {
           background: var(--brand-green);
           color: white;
           border: none;
           padding: 10px 24px;
           border-radius: 12px;
           font-size: 13px;
           font-weight: 600;
           cursor: pointer;
           transition: transform 0.2s, background 0.2s;
       }

       .receive-btn:active {
           transform: scale(0.95);
           background: var(--brand-dark);
       }

       .receive-btn.disabled {
           background: #cbd5e1;
           color: white;
           pointer-events: none;
       }

       .no-data {
           text-align: center;
           color: rgba(255, 255, 255, 0.8);
           padding: 40px 0;
           font-weight: 500;
       }

       /* Modern Popup Toast */
       .modern-toast {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translate(-50%, 0);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 12px 24px;
            border-radius: 24px;
            text-align: center;
            z-index: 99999;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            transition: opacity 0.5s ease;
       }
    </style>
  </head>
  <body>
    <div class="appCapsule">
       <header class="page-header">
           <a href="javascript:history.back()" class="header-btn">
               <i class="bi bi-chevron-left"></i> Back
           </a>
           <h1 class="page-title">My Products</h1>
           <div class="header-spacer"></div>
       </header>

       <div class="tabs-container">
           <div class="tabs-wrapper">
               <button class="tab-btn active" onclick="openTab(event, 'unexpired')">Unexpired</button>
               <button class="tab-btn" onclick="openTab(event, 'expired')">Expired</button>
           </div>
           
           <div class="total-amount-box">
             <?php
             $total_order=0;
             $query = mysqli_query($con,"SELECT * FROM order_book WHERE o_userid ='$userid_access' ORDER BY o_id desc");
             if(mysqli_num_rows($query)>0){
               while($row=mysqli_fetch_array($query)){
                 $o_amount = $row['o_amount'];
                 $total_order += $o_amount;
                }
             } 
             ?>
               Total investment: <span>₹<?php echo number_format((float)$total_order, 2); ?></span>
           </div>
       </div>

       <div class="content-wrapper">
           <!-- Unexpired Tab -->
           <div id="unexpired" class="tabcontent" style="display: block;">
               <?php 
               $query = mysqli_query($con,"SELECT * FROM order_book WHERE o_userid='$userid_access' AND o_status='Credit' ORDER BY o_id desc");
               if(mysqli_num_rows($query)>0){
                   while($row=mysqli_fetch_array($query)){
                       $o_id = $row['o_id'];
                       $o_package = $row['o_pac_id'];
                       $o_amount = $row['o_amount'];
                       $o_percentage = $row['o_percentage'];
                       $o_days = $row['o_days'];

                       $get = "SELECT * FROM package WHERE pa_id='$o_package'";
                       $run = mysqli_query($con, $get);  
                       $pa_row = mysqli_fetch_array($run); 
                       if ($pa_row) {
                           $pa_image = $pa_row['pa_image'];
                           $pa_name = $pa_row['pa_name'];
                       } else {
                           $pa_image = 'default.png';
                           $pa_name = 'Unknown Product';
                       }
                       
                       $pay_date_cnt = mysqli_num_rows(mysqli_query($con, "SELECT * FROM interest WHERE int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_status='Active'"));
                       $total_income = $pay_date_cnt * $o_percentage;
               ?>
               <div class="product-card">
                   <div class="product-image-box">
                       <img src="asupport/package/<?php echo htmlspecialchars($pa_image); ?>" alt="Product">
                   </div>
                   <div class="product-details">
                       <h4 class="product-name"><i class="bi bi-award" style="color: #f59e0b;"></i> <?php echo htmlspecialchars($pa_name); ?></h4>
                       <div class="detail-row">
                           <span class="detail-label">Price</span>
                           <span class="detail-value">₹<?php echo number_format((float)$o_amount, 2); ?></span>
                       </div>
                       <div class="detail-row">
                           <span class="detail-label">Term</span>
                           <span class="detail-value"><?php echo $pay_date_cnt; ?> / <?php echo $o_days; ?> days</span>
                       </div>
                       <div class="detail-row">
                           <span class="detail-label">Total earned</span>
                           <span class="detail-value amount-yield">₹<?php echo number_format((float)$total_income, 2); ?></span>
                       </div>
                       
                       <div class="action-row">
                           <?php
                           $today_date = date('Y-m-d');
                           $query_interest = mysqli_query($con,"SELECT * FROM interest WHERE int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_date='$today_date' ORDER BY int_id desc");
                           $int_row = mysqli_fetch_array($query_interest);
                           if ($int_row) {
                               $int_amount = $int_row['int_amount'];
                               $int_id = $int_row['int_id'];
                               $int_status = $int_row['int_status'];
                           } else {
                               $int_status = '';
                           }
                           
                           if($int_status == "Pending"){ ?>
                           <form action="" method="post" style="margin: 0;">
                               <input type="hidden" name="roi_id" value='<?php echo htmlspecialchars($int_id); ?>'>
                               <input type="hidden" name="userid" value='<?php echo htmlspecialchars($userid_access); ?>'>
                               <input type="hidden" name="int_acc_id" value='<?php echo htmlspecialchars($o_id); ?>'>
                               <input type="hidden" name="pay_amount" value='<?php echo htmlspecialchars($int_amount); ?>'>
                               <button type="submit" name="int" class="receive-btn">Receive</button>
                           </form>
                           <?php } else { ?>
                               <button type="button" class="receive-btn disabled">Received</button>
                           <?php } ?>
                       </div>
                   </div>
               </div>
               <?php } } else { ?>
                   <div class="no-data">No unexpired products found.</div>
               <?php } ?>
           </div>

           <!-- Expired Tab -->
           <div id="expired" class="tabcontent">
               <?php 
               $query = mysqli_query($con,"SELECT * FROM order_book WHERE o_userid='$userid_access' AND o_status='Deactive' ORDER BY o_id desc");
               if(mysqli_num_rows($query)>0){
                   while($row=mysqli_fetch_array($query)){
                       $o_id = $row['o_id'];
                       $o_package = $row['o_pac_id'];
                       $o_amount = $row['o_amount'];
                       $o_percentage = $row['o_percentage'];
                       $o_days = $row['o_days'];

                       $get = "SELECT * FROM package WHERE pa_id='$o_package'";
                       $run = mysqli_query($con, $get);  
                       $pa_row = mysqli_fetch_array($run); 
                       if ($pa_row) {
                           $pa_image = $pa_row['pa_image'];
                           $pa_name = $pa_row['pa_name'];
                       } else {
                           $pa_image = 'default.png';
                           $pa_name = 'Unknown Product';
                       }
                       
                       $pay_date_cnt = mysqli_num_rows(mysqli_query($con, "SELECT * FROM interest WHERE int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_status='Active'"));
                       $total_income = $pay_date_cnt * $o_percentage;
               ?>
               <div class="product-card" style="opacity: 0.85;">
                   <div class="product-image-box">
                       <img src="asupport/package/<?php echo htmlspecialchars($pa_image); ?>" alt="Product">
                   </div>
                   <div class="product-details">
                       <h4 class="product-name"><i class="bi bi-award" style="color: #94a3b8;"></i> <?php echo htmlspecialchars($pa_name); ?></h4>
                       <div class="detail-row">
                           <span class="detail-label">Price</span>
                           <span class="detail-value">₹<?php echo number_format((float)$o_amount, 2); ?></span>
                       </div>
                       <div class="detail-row">
                           <span class="detail-label">Term</span>
                           <span class="detail-value"><?php echo $pay_date_cnt; ?> / <?php echo $o_days; ?> days</span>
                       </div>
                       <div class="detail-row">
                           <span class="detail-label">Total earned</span>
                           <span class="detail-value" style="color: var(--font-muted);">₹<?php echo number_format((float)$total_income, 2); ?></span>
                       </div>
                       
                       <div class="action-row">
                           <button type="button" class="receive-btn disabled">Expired</button>
                       </div>
                   </div>
               </div>
               <?php } } else { ?>
                   <div class="no-data">No expired products found.</div>
               <?php } ?>
           </div>
       </div>

    </div>

    <!-- Footer Start Here -->
    <?php include "user_menu/footer_menu.php";  ?>
    <!-- Footer End Here -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
          tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
          tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
      }
    </script>
  </body>
</html>

<?php 
  $today_date = date('Y-m-d');
  if(isset($_POST['int']))
  {	
      $update_id = $_POST['roi_id'];
      $ac_userid = $_POST['userid'];
      $percentage_amount = $_POST['pay_amount'];
      $int_acc_id = $_POST['int_acc_id'];
      
      $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `interest` WHERE `int_id`='$update_id'"));
      $int_status = $query['int_status'];
      if($int_status =='Pending'){
          mysqli_query($con,"UPDATE `interest` SET `int_status`='Active' WHERE `int_id`='$update_id'");
          mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+$percentage_amount WHERE `userid`='$ac_userid'");
          mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$userid_access','Daily Product Income','$percentage_amount','Credit','$today_date')");
      }		
?>
    <script>
        const popupMessage = document.createElement('div');
        popupMessage.textContent = 'Received Successfully!';
        popupMessage.classList.add('modern-toast');
        document.body.appendChild(popupMessage);
        
        setTimeout(function(){
            popupMessage.style.opacity = '0';
            setTimeout(function(){
                window.location = "myproducts.php";
            }, 500);
        }, 2000);
    </script>
<?php
  }
?>