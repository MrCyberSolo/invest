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
       :root{
        --dark-blue:#07CCFF;
        --light-blue:#0061bf;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule, .footerBox{
        max-width: 641px;
        /* background-color: #07CCFF; */
        margin: auto;

       }

       .appCapsule{
        padding-bottom: 5rem;
       }

       /* body{
        background-color: #07CCFF;
       } */

       


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
.headerBox{
    background-color:white;
    text-align: center;
    border-radius: 6px;
    padding: 15px 0;

}

.headerBox a{
    text-decoration: none;
    color: #272727;
}

.headerBox img{
    width: 4rem;
    margin-bottom: 5px;
}

.menuBody p{
    font-size: 12px;

}


/* ============== icon menus */
        .icon-menu img{
            width: 30px;
        }

        .icon-menu .inner{
            width: 100%;
            margin: 7px 0;
        }

        .icon-menu a{
            text-decoration: none;
            color: #272727;
        }

        .menuBox2 {
            background:none; 
        }

        .menuBox2 p{
            color:white; 
        }

        .commissionBox .inner{
            background-color:#014f97; 
            border-radius: 10px; 
            color:white; 
            padding:10px; 
            display: flex; 
            justify-content:space-between; 
            margin-bottom: 10px; 
        }


    
    </style>
  </head>
  <body>
<div class="appCapsule container">
    <header>
        <div class="setting text-white text-end py-1 fs-5 px-2 py-2 ">
            <a href="security.php">
               <i class="bi bi-gear" style="width: 1.6rem; height: 1.6rem; color:white; "></i> 
                <!-- <i class="bi bi-gear text-white"></i> -->
            
            </a>
        </div>
        <!-- <div class="header-title text-center py-1">
            <h5>Me</h5>
            <div class="line"></div>
        </div> -->

        <div class="id-num text-white text-center d-flex align-items-center gap-3">
            <img src="https://png.pngtree.com/png-vector/20231127/ourmid/pngtree-demo-red-flat-icon-isolated-demo-icon-png-image_10722763.png" class="img-fluid" style="width: 90px; height: 90px; border-radius: 50%;" alt="">
            <div class="right">
                <p><?php echo $mobile; ?></p>
                <p>ID: <?php echo $userid_access; ?></p>
            </div>

        </div>

      
  </header>


  <div class="row gx-3 mt-3">
    <div class="col-6 ">
        <div class="headerBox">
            <a href="recharge.php">
                <img src="img/me/recharge.png" alt="">
                <h6>Recharge</h6>
            </a>
         
        </div>
    </div>

    <div class="col-6 ">
        <div class="headerBox">
            <a href="withdraw.php">
                <img src="img/me/withdraw.png" alt="">
                <h6>Withdraws</h6>
            </a>
     
        </div>
    </div>

    <div class="col-12 mt-3">
        <div class="card menuBox p-2 py-3">
            <div class="menuBody d-flex text-dark text-center justify-content-evenly gap-3">
                <div class="inner">
                    <p>Recharge wallet</p>
                    <b><?php echo $fran_bal; ?>RS</b>
                </div>
                <div class="inner">
                    <p>Balance wallet</p>
                    <b><?php echo $current_bal; ?>RS</b>
                </div>
                <div class="inner">
                    <p>Total Withdrawl</p>
                    <b>
                    <?php					
                        $income_with = 0;
                        $query = mysqli_query($con,"select * from income_received where userid='$userid_access' AND status='Paid' order by id desc");
                        if(mysqli_num_rows($query)>0)
                        {
                            while($row=mysqli_fetch_array($query))
                            {
                                $amount = $row['amount'];                                        
                                $income_with = $income_with + $amount;
                            }
                        }
                        echo " "  .$income_with.""
                    ?>RS</b>
                </div>
               
            </div>
    
            <div class="menuBody d-flex text-dark text-center justify-content-evenly gap-3 mt-3">
                <div class="inner">
                    <p>Today Income</p>
                    <b><?php	
                                $today_date = date('Y-m-d',strtotime("-1 days"));			
                                $interest_label = 0;
                                $query = mysqli_query($con,"select * from order_book where o_userid='$userid_access' ");
                                if(mysqli_num_rows($query)>0)
                                {
                                    while($row=mysqli_fetch_array($query))
                                    {
                                        $o_amount = $row['o_amount'];
                                        $interest_label = $interest_label + $o_amount;
                                    }
                                }
                                //echo " "  .$interest_label.""
                                 $interest_label;
                              //echo   $formatted = sprintf("%0.2f", $interest_label);
                            ?>
                            </b><b><?php	
                        $today_date = date('Y-m-d');			
                        $interest_label = 0;
                        $query = mysqli_query($con,"select * from transaction where t_userid='$userid_access' AND t_type='Credit' AND t_details!='Account Recharge' AND t_date='$today_date'");
                        if(mysqli_num_rows($query)>0)
                        {
                            while($row=mysqli_fetch_array($query))
                            {
                                $o_amount = $row['t_amount'];                                        
                                $interest_label = $interest_label + $o_amount;
                            }
                        }
                        echo " "  .$interest_label.""
                            ?>
                            RS</b>
                </div>
                <div class="inner">
                    <p>Team Income</p>
                    <b><?php	
                                $today_date = date('Y-m-d',strtotime("-1 days"));			
                                $interest_label = 0;
                                $query = mysqli_query($con,"select * from interest_label where int_userid='$userid_access'");
                                if(mysqli_num_rows($query)>0)
                                {
                                    while($row=mysqli_fetch_array($query))
                                    {
                                        $o_id = $row['int_id'];
                                        $o_amount = $row['int_amount'];
                                        
                                        $interest_label = $interest_label + $o_amount;
                                    }
                                }
                                //echo " "  .$interest_label.""
                              echo   $formatted = sprintf("%0.2f", $interest_label);
                            ?>RS</b>
                </div>
                <div class="inner">
                    <p>Total Income</p>
                    <b><?php	
                        $today_date = date('Y-m-d');			
                        $interest_label = 0;
                        $query = mysqli_query($con,"select * from transaction where t_userid='$userid_access' AND t_type='Credit' AND t_details!='Account Recharge'");
                        if(mysqli_num_rows($query)>0)
                        {
                            while($row=mysqli_fetch_array($query))
                            {
                                $o_amount = $row['t_amount'];                                        
                                $interest_label = $interest_label + $o_amount;
                            }
                        }
                        echo " "  .$interest_label.""
                            ?>RS</b>
                </div>
               
            </div>
        </div>
        
    </div>

    <!-- <div class="col-12 mt-3">
        <div class="card">
            <a href="" class="text-decoration-none card-body d-flex justify-content-between">
                <p>My integral</p>
                <div class="right d-flex align-items-center gap-2">
                    <b class="">0</b>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </a>
           
        </div>
    </div> -->

    <div class="col-12 commissionBox mt-3">
        <div class="card">
            <div class="card-body">
                <div class="inner">
                <p>B-10%</p>
                    <p><?php echo $direct_refer=  mysqli_num_rows(mysqli_query($con, "select * from user where under_userid='$userid_access'")); ?></p>
                </div>

                <div class="inner">
                <p>C-5%</p>

                    <p><?php 
                        $total_count=0;
                        
                        foreach($levels as $level) 
                        {

                            $label_count= count($level['data']); 
                            $total_count = ($total_count + $label_count);
                                $firstValue = array_shift($level);
                                if($firstValue=='Level 2'){
                                 echo $label_counts= count($level['data']); 
                                }
                        }
                        ?></p>
                </div>

                <div class="inner">
                <p>D-2%</p>

                    <p><?php 
                    $total_count=0;
                    foreach($levels as $level) 
                    {
                        $label_count= count($level['data']); 
                        $total_count = ($total_count + $label_count);
                            $firstValue = array_shift($level);
                            if($firstValue=='Level 3'){
                            echo $label_counts= count($level['data']); 
                            }
                    }
                    ?></p>
                </div>

               
            </div>

            <div class="btnBox d-grid px-3">
                <a href="commission-record.php" class="btn bg-dark rounded-pill mb-3 text-white text-center" style="font-size: 17px; padding: 10px 0;">View team commissions</a>
            </div>
        </div>
    </div>

    
    <!-- icons menus  -->
    <div class="col-12 mt-3 icon-menu">
        <div class="card menuBox2 p-2 py-3">
            <div class="menuBody d-flex text-dark text-center justify-content-evenly gap-2">
                <a href="myproducts.php" class="inner">
                    <img src="img/iconme/1.png" alt="">
                    <p>My product</p>
                  
                </a>
                <a href="mycoupon.php" class="inner">
                    <img src="img/iconme/2.png" alt="">
                    <p>Coupon</p>
                  
                </a>
                <a href="funding-details.php" class="inner">
                    <img src="img/iconme/3.png" alt="">
                    <p>Funding details</p>
                  
                </a>
                <a href="bindbank.php" class="inner">
                    <img src="img/iconme/4.png" alt="">
                    <p>Bind bank account</p>
                  
                </a>
               
            </div>
    
            <div class="menuBody d-flex text-dark text-center justify-content-evenly gap-2">
                <a href="redeembonus.php" class="inner">
                    <img src="img/iconme/5.png" alt="">
                    <p>Redeem bonus</p>
                  
                </a>
                <a href="invitiation.php"  class="inner">
                    <img src="img/iconme/6.png" alt="">
                    <p>Invitation</p>
                  
                </a>
                <a href="myteams.php"  class="inner">
                    <img src="img/iconme/7.png" alt="">
                    <p>My teams</p>
                  
                </a>
                <a href="services.php"  class="inner">
                     <img src="img/iconme/8.png" alt="">
                    <p>Customer service</p>
                </a>
                

            <!--<button class="inner" onclick="openServicemodal()" style="background-color: transparent; border: 0;">
                 <img src="img/iconme/8.png" alt="">
                 <p>   Customer service</p>
            </button> -->
                
               
            </div>

            <div class="menuBody d-flex text-dark text-center justify-content-evenly gap-2">
               <!-- <a href="reward.php"  class="inner text-center">
                    <img src="img/iconme/10.png" class="text-center" alt="">
                    <p>Reward </p>
                  
                </a>-->
                 <a href="oceanfoodco.apk"  class="inner" target="_blank">
                    <img src="img/iconme/9.png" alt="">
                    <p>App</p>
                  
                </a>
                <a href="" class="inner"></a>
                <a href="" class="inner"></a>
               
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