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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My products</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#009a5f;
        --light-blue:#009a5f;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule, .footerBox{
        max-width: 641px;
        /* background-color: #009a5f; */
        margin: auto;

       }

       .appCapsule{
        padding-bottom: 1rem;
       }

       
.appBody{
    margin-top: 4rem;
}
       

       body{
        /* background-color: #009a5f; */
    /* color: rgb(0, 0, 0); */

       }



header{
  background-color:#005a36;
}


/* ==========================  */
/* footer section   */

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

/* member section ============== */

      .memberTab{
        display: flex;
        justify-content: space-between;
        background-color:#dbdbdb;
        /* border: 1px solid black; */
        border-radius: 5px;
        padding: 1px;
        overflow: hidden;

          
      }
.member-section button {
  text-decoration: none;
    color: rgb(0, 0, 0);
    border: #ffffff solid 1px;
    border: none;
    background-color: transparent;
    width: 100%;
    padding: 7px ;

}

.member-section button:first-child{
  border-top-left-radius: 7px;
    border-bottom-left-radius: 7px;
}

.member-section button:last-child{
  border-top-right-radius: 7px;
    border-bottom-right-radius: 7px;
}


.member-section button:hover {
  background-color:#005a36;
  color: rgb(255, 255, 255);

}

.member-section button.active {
  background-color:#005a36;   color: rgb(255, 255, 255);
}

.member-section .tabcontent {
  /* display: none; */
  /* padding: 6px 12px; */
  border-top: none;
}

/* .member-section{
  height: 100vh;
  background-color: #009a5f;
} */

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
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container ">

   <header class="row fixed-top">
        <div class="text-white py-3">
            <div class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <a href="me.php" class="nav-link left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6>My Product</h6>

                <div class="px-3"></div>

        </div>
        </div>

  </header>
 
 



  <div class="row appBody">
    <!-- member section  -->

    <div class="col-12 member-section">

<div class="memberTab">
  <button class="tablinks active" onclick="openMember(event, 'memberOne')">unexpired product</button>
  <button class="tablinks" onclick="openMember(event, 'memberTwo')">expired product</button>
</div>

<div class=" text-center py-2">
  <?php
  $total_order=0;
  $query = mysqli_query($con,"select * from order_book where  o_userid ='$userid_access' order by o_id desc");
  if(mysqli_num_rows($query)>0){
    while($row=mysqli_fetch_array($query)){
      $o_amount = $row['o_amount'];
      $total_order =$total_order + $o_amount;
     }} ?>
    <small>Total: ₹<?php echo $total_order; ?></small>
</div>

<style>
.myproduct-section img{
  width: 90px;

}

.myproduct-section .right{
  width:80% ;
  padding: 5px 5px;
  font-size: 13px;
}

.myproduct-section .inner{
  border-bottom: 1px solid #CCCCCC;
  padding: 3px 0;

}

.myproduct-section .inner p:last-child{
  color: blue;
}

.myproduct-section{
  border: none;
  margin-top: 10px;
  background-color: #ececec;
  padding: 10px 7px;
  border-radius: 10px;
  /* background-color: red; */
}





</style>
<div id="memberOne" class="tabcontent ">
<?php 
        $i=0;
      $query = mysqli_query($con,"select * from order_book where o_userid='$userid_access' AND o_status='Credit' order by o_id desc");
        if(mysqli_num_rows($query)>0){
            while($row=mysqli_fetch_array($query)){
                $o_id = $row['o_id'];
                $o_userid = $row['o_userid'];
                $o_package = $row['o_pac_id'];
                $o_amount = $row['o_amount'];
                $o_percentage = $row['o_percentage'];
                $o_pay_status = $row['o_status'];
                $o_user_type = $row['o_user_type'];
                $o_date = $row['o_date'];
                $o_days = $row['o_days'];
                $i++;

				$get = "select * from package where pa_id='$o_package'";
					$run = mysqli_query($con, $get);  
					$row= mysqli_fetch_array($run); 
					$pa_image = $row['pa_image'];
						$pa_type = $row['pa_type'];
						$pa_name = $row['pa_name'];
					 $pay_date= mysqli_num_rows(mysqli_query($con, "select * from interest where int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_status='Active'"));
					$total_income = $pay_date * $o_percentage;
				
				?>

<!-- <p class="text-center">No more</p> -->
<a href="" class="text-decoration-none">
<div class="card myproduct-section">
  <div class="card-body d-flex p-1 align-items-center justify-content-between gap-1">
    <div class="left img-fluid">
      <img src="asupport/package/<?php echo $pa_image; ?>" class="p-1" alt="">
    </div>

    <div class="right ">
      <h5 class="title d-flex align-items-center"><span style="font-size: 15px;">🏆</span><?php echo $pa_name;?></h5>

      <div class="inner d-flex justify-content-between">
        <p>Price</p>
        <p><?php echo $o_amount; ?> RS</p>
      </div>

      <div class="inner d-flex justify-content-between">
        <p>Term</p>
        <p><?php echo $pay_date; ?>/<?php echo $o_days; ?>days</p>
      </div>

      <div class="inner d-flex justify-content-between">
        <p>Total earning</p>
        <p><?php echo $total_income; ?> Rs</p>
      </div>


    </div>
    <?php
    $today= date('Y-m-d');
    // $pay_today= mysqli_num_rows(mysqli_query($con, "select * from interest where int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_date='$today' AND int_status='Active'"));
    //if($pay_today=='1'){
    $today_date = date('Y-m-d');
    $query_order_book_interest = mysqli_fetch_array(mysqli_query($con,"select * from interest where int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_date='$today_date' order by int_id desc"));
    	$int_amount = $query_order_book_interest['int_amount'] ?? '';
    	$date = $query_order_book_interest['int_date'] ?? '';
    	$int_id = $query_order_book_interest['int_id'] ?? '';
    	$user_id = $query_order_book_interest['int_userid'] ?? '';
    	$int_status = $query_order_book_interest['int_status'] ?? '';
    if($int_status =="Pending"){ ?>
    <form action="" method="post">
    	<input class="form-control"  type="hidden" name="roi_id" value='<?php echo $int_id; ?>'  >
    	<input class="form-control"  type="hidden" name="userid" value='<?php echo $userid_access; ?>'  >
    	<input class="form-control"  type="hidden" name="int_acc_id" value='<?php echo $o_id; ?>'  >
    	<input class="form-control"  type="hidden" name="pay_amount" value='<?php echo $int_amount; ?>'  >
    <div class="rec-btn">
      <button  class="btn " type="submit"  name="int"  style="font-size: 14px; padding: 20px 8px; background-color: #009a5f;">Receive</button>
    </div>
    </form>
      <?php }else{?> 
        <div class="rec-btn">
          <button type="button" class="btn " style="font-size: 14px; padding: 20px 8px; background-color: #CCCCCC;">Receive</button>
        </div>
      
      <?php } ?>
  </div>
</div>
</a>

<?php  } } ?>

</div>

<div id="memberTwo" class="tabcontent">
   <?php 
        $i=0;
      $query = mysqli_query($con,"select * from order_book where o_userid='$userid_access' AND o_status='Deactive' order by o_id desc");
        if(mysqli_num_rows($query)>0){
            while($row=mysqli_fetch_array($query)){
                $o_id = $row['o_id'];
                $o_userid = $row['o_userid'];
                $o_package = $row['o_pac_id'];
                $o_amount = $row['o_amount'];
                $o_percentage = $row['o_percentage'];
                $o_pay_status = $row['o_status'];
                $o_user_type = $row['o_user_type'];
                $o_date = $row['o_date'];
                $o_days = $row['o_days'];
                $i++;

				$get = "select * from package where pa_id='$o_package'";
					$run = mysqli_query($con, $get);  
					$row= mysqli_fetch_array($run); 
					$pa_image = $row['pa_image'];
						$pa_type = $row['pa_type'];
						$pa_name = $row['pa_name'];
					 $pay_date= mysqli_num_rows(mysqli_query($con, "select * from interest where int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_status='Active'"));
					$total_income = $pay_date * $o_percentage;
				
				?>

<!-- <p class="text-center">No more</p> -->
<a href="" class="text-decoration-none">
<div class="card myproduct-section">
  <div class="card-body d-flex p-1 align-items-center justify-content-between gap-1">
    <div class="left img-fluid">
      <img src="asupport/package/<?php echo $pa_image; ?>" class="p-1" alt="">
    </div>

    <div class="right ">
      <h5 class="title d-flex align-items-center"><span style="font-size: 15px;">🏆</span><?php echo $pa_name;?></h5>

      <div class="inner d-flex justify-content-between">
        <p>Price</p>
        <p><?php echo $o_amount; ?> RS</p>
      </div>

      <div class="inner d-flex justify-content-between">
        <p>Term</p>
        <p><?php echo $pay_date; ?>/<?php echo $o_days; ?>days</p>
      </div>

      <div class="inner d-flex justify-content-between">
        <p>Total earning</p>
        <p><?php echo $total_income; ?> Rs</p>
      </div>


    </div>
    <?php
    $today= date('Y-m-d');
    // $pay_today= mysqli_num_rows(mysqli_query($con, "select * from interest where int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_date='$today' AND int_status='Active'"));
    //if($pay_today=='1'){
    $today_date = date('Y-m-d');
    $query_order_book_interest = mysqli_fetch_array(mysqli_query($con,"select * from interest where int_userid='$userid_access' AND int_acc_id ='$o_id' AND int_date='$today_date' order by int_id desc"));
    	$int_amount = $query_order_book_interest['int_amount'] ?? '';
    	$date = $query_order_book_interest['int_date'] ?? '';
    	$int_id = $query_order_book_interest['int_id'] ?? '';
    	$user_id = $query_order_book_interest['int_userid'] ?? '';
    	$int_status = $query_order_book_interest['int_status'] ?? '';
    if($int_status =="Pending"){ ?>
    <form action="" method="post">
    	<input class="form-control"  type="hidden" name="roi_id" value='<?php echo $int_id; ?>'  >
    	<input class="form-control"  type="hidden" name="userid" value='<?php echo $userid_access; ?>'  >
    	<input class="form-control"  type="hidden" name="int_acc_id" value='<?php echo $o_id; ?>'  >
    	<input class="form-control"  type="hidden" name="pay_amount" value='<?php echo $int_amount; ?>'  >
    <div class="rec-btn">
      <button  class="btn " type="submit"  name="int"  style="font-size: 14px; padding: 20px 8px; background-color: #009a5f;">Receive</button>
    </div>
    </form>
      <?php }else{?> 
        <div class="rec-btn">
          <button type="button" class="btn " style="font-size: 14px; padding: 20px 8px; background-color: #CCCCCC;">Receive</button>
        </div>
      
      <?php } ?>
  </div>
</div>
</a>

<?php  } } ?>
</div>





</div>


<script>
function openMember(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>






    </div>

    
    </div>
    
    
   
     
   
    
    

    </div>



<!-- footer  -->
<!--<footer class="footerBox fixed-bottom">-->
<!--  <a href="home.php" class="inner">-->
<!--    <img src="img/icons/download.png" alt="">-->
<!--    <p>Home</p>-->
<!--  </a>-->

<!--  <a href="product.php" class="inner">-->
<!--    <img src="img/icons/product1.png" alt="">-->
<!--    <p>Product</p>-->
<!--  </a>-->

<!--  <a href="me.php" class="inner me-icon">-->
<!--    <img src="img/icons/download (1).png" alt="">-->
<!--    <p>Me</p>-->
<!--  </a>-->

<!--  <a href="myteams.php" class="inner">-->
<!--    <img src="img/icons/download (6).png" alt="">-->
<!--    <p>My Teams </p>-->
<!--  </a>-->

<!--  <a href="news.php" class="inner">-->
<!--    <img src="img/icons/download (3).png" alt="">-->
<!--    <p>News</p>-->
<!--  </a>-->
<!--</footer>-->
   

</div>    <!--  end appCapsule  -->
<!-- =================================== -->
    




<!-- Footer Start Here -->
<?php include "user_menu/footer_menu.php";  ?>
<!-- Footer End Here -->








    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

       
    <script>
      function openteam(evt, teamName) {
        var i, teamContent, teamlink;
        teamContent = document.getElementsByClassName("teamContent");
        for (i = 0; i < teamContent.length; i++) {
          teamContent[i].style.display = "none";
        }
    
    
        teamlink = document.getElementsByClassName("teamlink");
        for (i = 0; i < teamlink.length; i++) {
          teamlink[i].className = teamlink[i].className.replace(" active", "");
        }
    
        document.getElementById(teamName).style.display = "block";
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
    //update income
   $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `interest` WHERE  `int_id`='$update_id'"));
	$int_status = $query['int_status'];
    if($int_status =='Pending'){
        mysqli_query($con,"UPDATE `interest` SET `int_status`='Active' WHERE `int_id`='$update_id'");
        mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+$percentage_amount WHERE `userid`='$ac_userid'");
        mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$userid_access','Daily Product Income','$percentage_amount','Credit','$today_date')");
    }		
        //echo '<script> swal({title: "Daily ROI Deposit in Current Wallet",text: "Click Ok ",icon: "success",}).then(function(){window.location = "myproduct.php" });</script>';
        //echo '<script>alert("Daily ROI Deposit in Current Wallet");window.location.assign("myproduct.php");</script>';
          ?>
             <script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Received';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "myproducts.php" 
        },
    2000);
    </script>
        <?php
     
    
}
?>