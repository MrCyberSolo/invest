<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Withdrawal records</title>

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
        padding: 3.5rem 0;
       }


       .recharge-section .card-body{
        display: flex; 
        justify-content: space-between;
       }

       .recharge-section .cardBox{
        padding: 13px 0;
        border-bottom: 1px solid #f2f1f1;
       }

       /* body{
        background-color: #009a5f;
       } */

    
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container ">
  <header class="row fixed-top">
        <div class="text-white py-3">
            <a href="javascript:history.back()" class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <div class="left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </div>

                <h6>Withdrawal records</h6>

                <div class="px-3"></div>

        </a>
        </div>

  </header>

  <div class="row appBody">
    <div class="col-12 recharge-section">
    <?php         
         $result = mysqli_query($con,"SELECT * FROM `income_received` WHERE `userid`='$userid_access' order by id desc"); 
         if (mysqli_num_rows($result) > 0) {
           // Output data of each row
           while ($row = mysqli_fetch_assoc($result)) {
         ?>
    <div class="cardBox" style=" color: #fff; ">
        <div class="card-body">
            <div class="left">
                <h6><?php echo $row["id"]; ?>5c<?php echo $row["id"]; ?>c73<?php echo $row["id"]; ?>hh<?php echo $row["id"]; ?>dfhas<?php echo $row["id"]; ?></h6>
            </div>
            <div class="right">
              <p><?php echo $row["amount"]; ?>RS</p>
            </div>
        </div>
        <div class="card-body">
            <div class="left">
               <small style=" color: #fff; "><?php echo $row["updated_date"]; ?></small>
            </div>
            <div class="right m-1">
                <a href="#" style="text-decoration: none; background-color: black; color: #ffffff; padding: 4px 6px; border-radius: 2px;"><?php $status = $row["status"]; if($status=='Pending'){ echo "Pending"; }elseif($status=='Reject'){ echo "Fail"; }else{ echo "Success"; } ?></a>
            </div>
        </div>      
    </div>
    <?php } } ?>


       <div class="nomore text-center py-3">
        <p>No more</p>
       </div>
    </div>
  </div>
</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


  </body>
</html>