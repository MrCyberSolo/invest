<?php
session_start();
	date_default_timezone_set('Asia/Kolkata');
// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
$mysqltime = date('Y-m-d H:i:s');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>reward</title>

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
        padding-bottom: 1rem;
       }

       
.appBody{
    margin-top: 4rem;
}
       

       body{
        /* background-color: #07CCFF; */
    /* color: rgb(0, 0, 0); */

       }



header{
  background-color:#014f97;
}


/* ==========================  */
/* footer section   */



/* .footerBox a{
  text-decoration: none;
  color: #CCCCCC;
}
       .footerBox{
  display: flex;
  background-color: #fff;
  justify-content: space-around;
  align-items: center;
  text-align: center;
  height: 60px; 
  box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
}

.footerBox img{
  width: 25px;
}




.footerBox button{
  text-decoration: none;
  color: #CCCCCC;
}

.footerBox .me-icon img{
width: 45px;
height: 45px;
background-color: white;
border-radius: 50%;
}

.footerBox .me-icon{
position: relative;
bottom: 10px;
} */
       
/* end footer section   */   
/* ==========================  */


/* member section ============== */

      /* .memberTab{
        display: flex;
        justify-content: space-between;
        background-color:#dbdbdb;
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
  background-color:#014f97;
  color: rgb(255, 255, 255);

} */

/* .member-section button.active {
  background-color:#014f97;   color: rgb(255, 255, 255);
} */

.member-section .tabcontent {
  /* display: none; */
  /* padding: 6px 12px; */
  border-top: none;
}

/* .member-section{
  height: 100vh;
  background-color: #07CCFF;
} */

.popup {
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background-color: rgba(0, 0, 0, 0.7);
	color: white;
	padding: 20px;
	border-radius: 10px;
	text-align: center;
}



    </style>
  </head>
  <body>
<div class="appCapsule container ">
  <div class="row">
   <header class="fixed-top">
       <div class="text-white py-3">
            <div class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <a href="me.php" class="nav-link left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6>Reward</h6>

                <div class="px-3"></div>

        </div>
        </div>
  </header>
  </div>
 



  <div class="row appBody">
    <!-- member section  -->

    <div class="col-12 member-section">

<!-- <div class="memberTab">
  <button class="tablinks active" onclick="openMember(event, 'memberOne')">unexpired product</button>
  <button class="tablinks" onclick="openMember(event, 'memberTwo')">expired product</button>
</div>

<div class=" text-center py-2">
    <small>Total: ₹0.00</small>
</div> -->

<style>
.myproduct-section img{
  width: 90px;
  border-radius: 20px;

}

.myproduct-section .right{
  width:80% ;
  padding: 5px 5px;
  font-size: 14px;
}

.myproduct-section .inner{
  padding: 3px 0;
  display: flex;
  justify-content: center;
  align-items: center;

}

.myproduct-section .inner p:last-child{
  color: blue;
}

.myproduct-section{
  border: none;
  background-color: #ececec;
  padding: 10px 7px;
  border-radius: 10px;
  /* background-color: red; */
  margin-top: 10px;
}





</style>
<div id="memberOne" class="tabcontent ">
<!-- <p class="text-center">No more</p> -->
<?php 
        $i=1;
         //$refer_user=  mysqli_num_rows(mysqli_query($con, "SELECT * FROM user WHERE `under_userid`='$userid_access' AND  DATE(act_date) >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) + 1 DAY - INTERVAL 0 WEEK"));
         $refer_user=  mysqli_num_rows(mysqli_query($con, "SELECT * FROM user WHERE `under_userid`='$userid_access' AND status='Active'"));
        $query = mysqli_query($con,"select * from reward_plan");
        if(mysqli_num_rows($query)>0){
            while($row=mysqli_fetch_array($query)){
                
                $reward_id= $row['rp_id'];
                $rp_business = $row['rp_business'];
                $rp_cash = $row['rp_cash'];

          ?>
<div class="card myproduct-section"  style="position: relative;">
  <div class="card-body d-flex p-1 align-items-center justify-content-between gap-1" >
    <div class="left img-fluid">
      <img src="https://static.fotor.com/app/features/img/aiface/3d/2.png" class="p-1" alt="">
    </div>
    <div class="right ">
        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#rewardModal" style="position: absolute; color: red; border: none; right: 0px; top:0px;  font-size: 20px;">
            <i class="bi bi-question-circle" ></i>
        </button>
      <div class="inner d-flex justify-content-between pe-4">
        <p>Invite: <?php echo $refer_user;  ?> / <?php echo $rp_business; ?></p>
        <b><?php echo $rp_cash; ?>RS</b>
      </div>
      <div class="inner d-flex justify-content-between">
        <p>Direct & Team invitation</p>
        <?php 
        // $order_count = mysqli_num_rows(mysqli_query($con,"select * from reward where userid = '$userid_access' AND reward='$reward_id' AND  DATE(date) >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) + 1 DAY - INTERVAL 0 WEEK"));
         $order_count = mysqli_num_rows(mysqli_query($con,"select * from reward where userid = '$userid_access' AND reward='$reward_id' AND  DATE(date) >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) + 1 DAY - INTERVAL 0 WEEK"));
         
        if($refer_user >= $rp_business){
          if($order_count == 0){ ?>
          <a href="?reward_id=<?php echo $reward_id ?>" class="btn" style="background-color: #07ccff; padding: 5px 19px; border-radius: 24px; color: rgb(236, 236, 236);;">Receive</a>
        <?php }else{?> 
          <button class="btn" style="background-color: #a3a3a3; padding: 5px 19px; border-radius: 24px; color: rgb(236, 236, 236);;">Received</button>
          <?php } }else{ ?>
          <button class="btn" style="background-color: #a3a3a3; padding: 5px 19px; border-radius: 24px; color: rgb(236, 236, 236);;">Receive</button>
        <?php } ?>
      </div>
    </div>
    
    <!-- <div class="rec-btn">
      <button type="button" class="btn " style="font-size: 14px; border-radius: 20px; padding: 5px 19px; background-color: #CCCCCC;">Receive</button>
    </div> -->

  </div>
</div>
<?php } } ?>
</div>




<!-- question mark modal popup  -->
<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rewardModal">
    Launch demo modal
  </button> -->
  
  <!-- Modal -->
  <div class="modal fade" id="rewardModal" tabindex="-1" aria-labelledby="rewardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3">
      <div class="reward-modal-content bg-white py-3" style="border-radius: 20px;">
        <div class="modal-header border-0">
          <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
          
        </div>
        <p class="fs-5 text-center px-5">The tast will end on sunday at 11:59pm</p>

        <div class="modal-body text-center border-0">
         <p>Invite 3 people to join this week, you can receive 150RS.Invite 5 people to join this week, you can receive 150+200=350RS. Click to receive, and the account will be automatically credited.</p>
        </div>
        <div class="modalFooter border-0 d-grid px-4 pb-3">
          <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
          <button type="button" class="btn btn-primary rounded-pill" style="background: linear-gradient(to right, #FC6130, red); border: 0;">Save changes</button>
        </div>
      </div>
    </div>
  </div>


    </div>


<script>
// function openMember(evt, cityName) {
//   var i, tabcontent, tablinks;
//   tabcontent = document.getElementsByClassName("tabcontent");
//   for (i = 0; i < tabcontent.length; i++) {
//     tabcontent[i].style.display = "none";
//   }
//   tablinks = document.getElementsByClassName("tablinks");
//   for (i = 0; i < tablinks.length; i++) {
//     tablinks[i].className = tablinks[i].className.replace(" active", "");
//   }
//   document.getElementById(cityName).style.display = "block";
//   evt.currentTarget.className += " active";
// }
// </script>

    </div>
   </div>
    
    </div>

<!-- footer  -->
<!-- <footer class="footerBox fixed-bottom">
  <a href="home.php" class="inner">
    <img src="img/icons/download.png" alt="">
    <p>Home</p>
  </a>

  <a href="product.php" class="inner">
    <img src="img/icons/product1.png" alt="">
    <p>Product</p>
  </a>

  <a href="me.php" class="inner me-icon">
    <img src="img/icons/download (1).png" alt="">
    <p>Me</p>
  </a>

  <a href="myteams.php" class="inner">
    <img src="img/icons/download (6).png" alt="">
    <p>My Teams </p>
  </a>

  <a href="news.php" class="inner">
    <img src="img/icons/download (3).png" alt="">
    <p>News</p>
  </a>
</footer>
    -->

</div>    <!--  end appCapsule  -->
<!-- =================================== -->
    













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

if(isset($_GET['reward_id']))
{
    $cr_ca_id = $_GET['reward_id'];
   $reward_count= mysqli_num_rows(mysqli_query($con, "select * from reward where  reward='$cr_ca_id' AND userid='$userid_access' AND  DATE(date) >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) + 1 DAY - INTERVAL 0 WEEK")); 
   if($reward_count=='0'){
    $query = mysqli_query($con,"select * from reward_plan where rp_id='$cr_ca_id'");
	if(mysqli_num_rows($query)>0)
	{
		while($row=mysqli_fetch_array($query))
		{
				$rp_business = $row['rp_business'];
				$rp_cash = $row['rp_cash'];
				$rp_gift = $row['rp_gift'];
				$rp_days = $row['rp_days'];
				$rp_type = $row['rp_type'];
		}
	}
  if($refer_user>=$rp_business){
      
		$query2 = mysqli_query($con,"INSERT INTO `reward`(`userid`, `reward`, `reward_plan`, `reward_type`, `amount`, `status`, `date`) values('$userid_access','$cr_ca_id','$rp_type','Cash','$rp_cash','Pending',Now())");
	    mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_pay`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES('$userid_access','$userid_access','Reward Bonus','$rp_cash','Credit','$mysqltime')"); 
	    mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+$rp_cash WHERE `userid`='$userid_access'"); 
		//$run_posts = mysqli_query($con,$query2);
		echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Salary Reqest Accepted';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('me.php','_self');
				}, 3000);
			});
			</script>";
    }else{
      echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Retry Salary Request';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('reward.php','_self');
				}, 3000);
			});
			</script>";
    }
}
else{
      echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				var popup = document.createElement('div');
				popup.className = 'popup';
				popup.innerHTML = 'Allready request';
				document.body.appendChild(popup);
				setTimeout(function() {
					document.body.removeChild(popup);
					window.open('reward.php','_self');
				}, 3000);
			});
			</script>";
}
}
?>