<?php
	date_default_timezone_set('Asia/Kolkata');
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
echo $userid_access = $_SESSION['username'];
if(isset($_GET['rec_id'])){
 $rec_id = $_GET['rec_id'];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order detials</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:black;
        --light-blue:black;
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
        background-color: #005a36;

       }

       
       .appBody{
        padding: 4.5rem 0;
       }


       /* body{
        background-color: #009a5f;
       } */

       /* input-section================================  */
       

.inputBox .inner{
  display: flex; 
  justify-content: space-between;
  border-bottom:1px solid #aeaeae28;
  padding: 1rem 0;
}






</style>
    
  </head>
  <body>
<div class="appCapsule container">
  <header class="row fixed-top">
        <div class="text-white py-3">
            <a href="recharge-records.php" class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <div class="left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </div>

                <h6>Order details</h6>

                <h6 class="px-3"></h6>
        </a>
        </div>

  </header>
   
    <div class="row appBody ">
        <div class="col-12  input-section px-4">
            
            <div class="inputBox">
                 <?php   
                    $row = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `payment` WHERE `user_id`='$userid_access' AND id='$rec_id'"));
		           
                ?>
                <div class="nav-link inner">
                    <p>Order number</p>
                    <div class="right">
                        <p><?php echo $row["id"]; ?>5c<?php echo $row["id"]; ?>dfas<?php echo $add =6+$row["id"]; ?></p>
                    </div>
                </div>
                <div class="nav-link inner">
                    <p>Amount</p>
                    <div class="right">
                        <p><?php echo $row["amount"]; ?>RS</p>
                    </div>
                </div>
                <div class="nav-link inner">
                    <p>Payment method</p>
                    <div class="right">
                        <p>Online payment</p>
                    </div>
                </div>
                <div class="nav-link inner">
                    <p>Order time</p>
                    <div class="right">
                        <p><?php echo $row["date"]; ?></p>
                       
                    </div>
                </div>
                <div class="nav-link inner">
                    <p>Status</p>
                    <div class="right">
                        <?php $status = $row["status"];
                        if($status=='Pending'){?>
                        <p>Waiting Payment</p>
                        <?php }elseif($status=='Reject'){ ?>
                          <p>Reject Payment</p> 
                        <?php }else{ ?>
                            <p>Passed Payment</p> 
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
   

  

 



</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


  </body>
</html>