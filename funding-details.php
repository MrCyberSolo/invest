<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
$get_balance = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `income` WHERE `userid`='$userid_access'"));
	$current_bal = $get_balance['current_bal'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Funding Details</title>

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

       p, h1, h2, h3, h4, h5, h6, small{
        margin: 0;
        color:white;
       }

       .appCapsule{
        max-width: 641px;
        margin: auto;
        

       }

       header{
        background-color:#014f97;

       }

       
       .appBody{
        padding: 3.5rem 0;
       }


       .funding-section .card-body{
        display: flex; 
        justify-content: space-between;
       }

       .funding-section .cardBox{
        padding: 10px 0;
        border-bottom: 1px solid #f2f1f1;
       }

    
    </style>
  </head>
  <body>
<div class="appCapsule container ">
  <header class="row fixed-top">
        <div class="text-white py-3">
            <a href="me.php" class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <div class="left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </div>

                <h6>Funding Details</h6>

                <div class="px-3"></div>

        </a>
        </div>

  </header>

  <div class="row appBody">
    <div class="col-12 funding-section">
    <?php  
            $balance_amount =0;
         $result = mysqli_query($con,"SELECT * FROM `transaction` WHERE `t_userid`='$userid_access' order by t_id desc"); 
         if (mysqli_num_rows($result) > 0) {
           // Output data of each row
           while ($row = mysqli_fetch_assoc($result)) {
               //$balance_amount = $balance_amount+ $row["t_amount"];
         ?>
    <div class="cardBox  ">
        <div class="card-body">
            <div class="left">
                <h6><?php echo $row["t_details"]; ?></h6>
            </div>
            <div class="right">
                <?php if($row["t_type"] =='Credit'){ ?>
                    <p> <span style="color:#fff;">+ ₹<?php echo $row["t_amount"]; ?></span> </p>
                <?php }else { ?>
                    <p> <span style="color:#fff;">- ₹<?php echo $row["t_amount"]; ?></span> </p>
                <?php } ?>             
            </div>
        </div>
        <div class="card-body">
            <div class="left">
               <small class="text-light"><?php echo $row["t_date"]; ?></small>
            </div>
             <div class="right">
                <small class="text-light"><?php if($row["t_type"] =='Credit'){  $balance_amount = $balance_amount + $row["t_amount"] - $current_bal;}else{  $balance_amount = $balance_amount - $row["t_amount"]- $current_bal;} ?></small>
            </div>
        </div>      
    </div>
    <?php } } ?>   
    </div>
  </div>


  

 



</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


  </body>
</html>