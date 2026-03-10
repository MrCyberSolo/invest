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
    //label count
    $DB = $con;
    if($DB->connect_error) {
        die("Connection failed: " . $DB->connect_error);
    }
    function getParent($parent_id)
    {
        global $DB;
        $query = $DB->query("SELECT * FROM user WHERE email = $parent_id");
        $result = $query->fetch_object();
        $data = (!$result) ? null : $result;
        $query->free();
        return $data;
    }
    function getChildren($parent_id)
    {
        global $DB;
        $query = $DB->query("SELECT * FROM user WHERE under_userid = $parent_id ORDER BY user_id");
        $arr = [];
        while($row = $query->fetch_object()) {
            $arr[] = $row;
        }        
        $data = (count($arr) < 1) ? [] : $arr;        
        $query->free();        
        return $data;
    }
    function getLevels($parent_id)
    {
        $level_1 = getChildren($parent_id);
        $level_2 = [];
        $level_3 = [];
        $level_4 = [];
        $level_5 = [];	
        if(count($level_1) > 0) {
            foreach($level_1 as $level1) {
                $level_2_data = getChildren($level1->email);
                if(count($level_2_data) > 0) {
                    foreach($level_2_data as $data) {
                        $level_2[] = $data;
                    }
                }
            }
        }
        if(count($level_2) > 0) {
            foreach($level_2 as $level2) {
                $level_3_data = getChildren($level2->email);
                if(count($level_3_data) > 0) {
                    foreach($level_3_data as $data) {
                        $level_3[] = $data;
                    }
                }
            }
        }
        if(count($level_3) > 0) {
            foreach($level_3 as $level3) {
                $level_4_data = getChildren($level3->email);
                if(count($level_4_data) > 0) {
                    foreach($level_4_data as $data) {
                        $level_4[] = $data;
                    }
                }
            }
        }
        return [
            ['name' => 'Level 1', 'data' => $level_1],
            ['name' => 'Level 2', 'data' => $level_2],
            ['name' => 'Level 3', 'data' => $level_3]
        ];
    }
    $parent = getParent($userid_access);
    if($parent == null) {
        die('Parent user not found.');
    }
    $levels = getLevels($parent->email);    
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>me</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root {
        --dark-blue: #1a569d;
        --light-blue: #0061bf;
        --yellow: #FFCE82;
       }

       body {
        background-color: #1a569d;
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule, .footerBox{
        max-width: 641px;
        margin: auto;
       }

       .appCapsule{
        padding-bottom: 5rem;
        background-color: #1a569d;
        min-height: 100vh;
       }

/* ==========================  */
/* footer section   */

.footerBox .active3{
  color:#045EB6 ;
}



header .line{
  width: 30px;
  height: 2px;
  border-radius: 40px;
  background-color: var(--yellow);
  margin:5px auto;
}

       .footerBox{
  display: flex;
  background-color: white;
  justify-content: space-around;
  align-items: center;
  text-align: center;
  /* padding: 5px 0; */
  height: 60px;
  box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
}

.footerBox img{
  width: 24px;
}

  footer .inner p{
    font-size: 13px;
  }
  

.footerBox a{
  text-decoration: none;
  color: #CCCCCC;
  
}

.footerBox .me-icon img{
    width: 28px;

}


       
/* end footer section   */   
/* ==========================  */



/* me ====  */
    .action-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 15px;
    }
    
    .action-card {
        background: white;
        border-radius: 8px;
        flex: 1;
        padding: 15px 0;
        text-align: center;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        font-size: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .action-card i {
        font-size: 24px;
        color: #398af2;
        display: block;
        margin-bottom: 5px;
    }

    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Commission Section */
    .comm-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .comm-stat-box {
        text-align: center;
        flex: 1;
    }
    .comm-val {
        background: #f0f0f0;
        padding: 5px 0;
        border-radius: 4px;
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
    }
    .comm-label {
        font-size: 13px;
        color: #666;
    }
    .btn-view-comm {
        background-color: #398af2;
        color: white;
        border: none;
        border-radius: 6px;
        width: 100%;
        padding: 12px;
        font-weight: 500;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    /* Wallet Stats Section */
    .wallet-header {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .wallet-box {
        text-align: center;
        flex: 1;
    }
    .wallet-box p {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
    }
    .wallet-box h4 {
        color: #333;
        font-weight: bold;
        font-size: 18px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        text-align: center;
    }
    .stat-item p {
        font-size: 11px;
        color: #666;
        margin-bottom: 5px;
    }
    .stat-item h6 {
        font-weight: bold;
        color: #333;
        font-size: 14px;
    }
    
    /* Icon grid */
    .bottom-icons-wrap {
        margin-top: 20px;
    }
    .nav-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        text-align: center;
    }
    .nav-item {
        color: white;
        text-decoration: none;
    }
    .nav-item i {
        font-size: 24px;
        display: block;
        margin-bottom: 5px;
    }
    .nav-item p {
        font-size: 12px;
        color: rgba(255,255,255,0.9);
    }
    
    .floating-cs a {
        position: fixed;
        right: 10px;
        top: 30%;
        background-color: #398af2;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 1000;
        text-decoration: none;
    }

    </style>
  </head>
  <body>
<div class="appCapsule container">
    <div class="floating-cs">
        <a href="services.php"><i class="bi bi-headset"></i></a>
    </div>


  <div class="row gx-3 px-2">
    
    <!-- Action Cards -->
    <div class="col-12">
        <div class="action-row">
            <a href="recharge.php" class="action-card">
                <i class="bi bi-credit-card"></i>
                Recharge
            </a>
            <a href="withdraw.php" class="action-card">
                <i class="bi bi-wallet2"></i>
                Withdraws
            </a>
        </div>
    </div>

    <!-- Commission Card -->
    <div class="col-12">
        <div class="detail-card">
            <div class="comm-stats">
                <div class="comm-stat-box">
                    <div class="comm-val"><?php echo $direct_refer = mysqli_num_rows(mysqli_query($con, "select * from user where under_userid='$userid_access'")); ?></div>
                    <div class="comm-label">B-10%</div>
                </div>
                <div class="comm-stat-box" style="margin: 0 10px;">
                    <div class="comm-val">
                        <?php 
                        $total_count=0;
                        $c_count=0;
                        foreach($levels as $level) {
                            $firstValue = array_shift($level);
                            if($firstValue=='Level 2'){
                                $c_count = count($level['data']); 
                            }
                        }
                        echo $c_count;
                        ?>
                    </div>
                    <div class="comm-label">C-5%</div>
                </div>
                <div class="comm-stat-box">
                    <div class="comm-val">
                        <?php 
                        $d_count=0;
                        foreach($levels as $level) {
                            $firstValue = array_shift($level);
                            if($firstValue=='Level 3'){
                                $d_count = count($level['data']); 
                            }
                        }
                        echo $d_count;
                        ?>
                    </div>
                    <div class="comm-label">D-2%</div>
                </div>
            </div>
            <a href="commission-record.php" class="btn-view-comm">View team commissions</a>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="col-12">
        <div class="detail-card">
            <div class="wallet-header">
                <div class="wallet-box">
                    <p>Recharge wallet</p>
                    <h4><?php echo $fran_bal; ?>RS</h4>
                </div>
                <div class="wallet-box">
                    <p>Balance wallet</p>
                    <h4><?php echo $current_bal; ?>RS</h4>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <p>Total profit</p>
                    <h6><?php	
                        $interest_label = 0;
                        $query = mysqli_query($con,"select * from transaction where t_userid='$userid_access' AND t_type='Credit' AND t_details!='Account Recharge'");
                        if(mysqli_num_rows($query)>0) {
                            while($row=mysqli_fetch_array($query)) {
                                $interest_label = $interest_label + $row['t_amount'];
                            }
                        }
                        echo $interest_label;
                    ?></h6>
                </div>
                <div class="stat-item">
                    <p>Team income</p>
                    <h6><?php	
                        $interest_label = 0;
                        $query = mysqli_query($con,"select * from interest_label where int_userid='$userid_access'");
                        if(mysqli_num_rows($query)>0) {
                            while($row=mysqli_fetch_array($query)) {
                                $interest_label = $interest_label + $row['int_amount'];
                            }
                        }
                        echo sprintf("%0.2f", $interest_label);
                    ?></h6>
                </div>
                <div class="stat-item">
                    <p>Income today</p>
                    <h6><?php	
                        $today_date = date('Y-m-d');			
                        $interest_label = 0;
                        $query = mysqli_query($con,"select * from transaction where t_userid='$userid_access' AND t_type='Credit' AND t_details!='Account Recharge' AND t_date='$today_date'");
                        if(mysqli_num_rows($query)>0) {
                            while($row=mysqli_fetch_array($query)) {
                                $interest_label = $interest_label + $row['t_amount'];
                            }
                        }
                        echo $interest_label;
                    ?></h6>
                </div>
                <div class="stat-item">
                    <p>Total loss</p>
                    <h6>0.00</h6> <!-- Not implemented in db call -->
                </div>
                <div class="stat-item">
                    <p>Today's loss</p>
                    <h6>0.00</h6>
                </div>
                <div class="stat-item">
                    <p>Product</p>
                    <h6>0.00</h6>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Navigation Grid -->
    <div class="col-12">
        <div class="bottom-icons-wrap">
            <div class="nav-grid">
                <a href="myproducts.php" class="nav-item">
                    <i class="bi bi-grid"></i>
                    <p>My product</p>
                </a>
                <a href="mycoupon.php" class="nav-item">
                    <i class="bi bi-ticket-perforated"></i>
                    <p>Coupon</p>
                </a>
                <a href="funding-details.php" class="nav-item">
                    <i class="bi bi-receipt"></i>
                    <p>Funding details</p>
                </a>
                <a href="redeembonus.php" class="nav-item">
                    <i class="bi bi-gift"></i>
                    <p>Redeem bonus</p>
                </a>
                <a href="invitiation.php" class="nav-item">
                    <i class="bi bi-envelope-paper"></i>
                    <p>Invitation</p>
                </a>
                <a href="services.php" class="nav-item">
                    <i class="bi bi-chat-dots"></i>
                    <p>Customer service</p>
                </a>
            </div>
        </div>
    </div>

  </div>


<!-- open service modal box here ========================== -->
<style>
    .modal-container {
        display: none;
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        transition: opacity 0.3s ease;
        z-index: 9999;
    }

    .service-Content {
        position: relative;
        background-color: #fff;
        margin: auto;
        /* padding: 20px; */
        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
        transform: translateY(100%);
        transition: transform 0.3s ease;
        border-top-right-radius: 20px;
        border-top-left-radius: 20px;
        
    }

    /* .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    } */
</style>



<div id="serviceContainer" class="modal-container">

    <div id="serviceContent" class="service-Content bg-light p-2">
        
        <p class="text-muted text-center border-bottom pb-3 px-3 bg-white">Customer service</p>
        <div class="inner d-flex align-items-center justify-content-between bg-white px-3">
            <i class="bi bi-whatsapp" style="font-size: 35px; color: green ;"></i>

            <div class="right text-end ">
               <small>WhatsApp Customer Service</small>
                <br>
                <small class="text-muted"><a href="">+12512724799</a></small>
            </div>
        </div>
        <div class="inner d-flex align-items-center justify-content-between bg-white px-3">
            <i class="bi bi-telegram" style="font-size: 35px; color: green ;"></i>

            <div class="right text-end ">
               <small>Telegram Customer Service</small>
                <br>
                <small class="text-muted"><a href="">+12512724799</a></small>
            </div>
        </div>

        <div class=" text-center mt-2 bg-white px-3 d-grid">
            <span class="close-btn  py-3 text-muted text-center " onclick="closeModal()">Close</span>

        </div>

    </div>
</div>
<?php
// Check if the HTTP_REFERER is set and not empty
if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
    $lastPage = $_SERVER['HTTP_REFERER'];
    $last_page_find = 'https://oceanfoodco.vip/login.php';
    $last_page_signup = 'https://oceanfoodco.vip/signup_access.php';
    if($lastPage == $last_page_signup){
        // Popup message HTML
        $popupMessage = '<div id="popupMessage" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 10px; border-radius: 10px; z-index: 9999;">Register Success!</div>';

        // Display the popup message
        echo $popupMessage;

        // JavaScript to hide the popup after 3 seconds
        echo '<script>
                setTimeout(function(){
                    var popup = document.getElementById("popupMessage");
                    if(popup){
                        popup.style.display = "none";
                    }
                }, 2000); // 3 seconds delay
              </script>';
    }
   if($lastPage == $last_page_find){
        // Popup message HTML
        $popupMessage = '<div id="popupMessage" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 10px; border-radius: 10px; z-index: 9999;">Login Success!</div>';

        // Display the popup message
        echo $popupMessage;

        // JavaScript to hide the popup after 3 seconds
        echo '<script>
                setTimeout(function(){
                    var popup = document.getElementById("popupMessage");
                    if(popup){
                        popup.style.display = "none";
                    }
                }, 2000); // 3 seconds delay
              </script>';
    }
}
?>


<script>
    var serviceContainer = document.getElementById("serviceContainer");
    var serviceContent = document.getElementById("serviceContent");

    function openServicemodal() {
        serviceContainer.style.display = "block";
        setTimeout(function () {
            serviceContent.style.transform = "translateY(0%)";
        }, 10);
    }

    function closeModal() {
        serviceContent.style.transform = "translateY(100%)";
        setTimeout(function () {
            serviceContainer.style.display = "none";
        }, 300); 
    }
</script>
<!-- Footer Start Here -->
<?php include "user_menu/footer_menu.php";  ?>
<!-- Footer End Here -->

</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>