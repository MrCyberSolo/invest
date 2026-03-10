<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];

	date_default_timezone_set('Asia/Kolkata');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My coupon</title>

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

/* header buttons ===================  */

.header-button {
    padding: 10px 0;
    display: flex;
    justify-content: space-around;
}

.header-button a{
    text-decoration: none;
    font-size: 17px;
    color: black;

    
}

.header-button .active{
    color: #009a5f;
    border-bottom: 1px solid;
    border-width: 2px;
}

.couponBox .left{
    background-color: rgb(255, 255, 255);
    text-align: center;
    padding: 13px;
    border-radius: 10px;
    color: #009a5f;
}

.couponBox .left span{
    font-size: 15px;
    color: #009a5f;
}

.couponBox{
    display: flex;
    padding:8px 0;

}

.couponBox .right{
    width: 100%;
    background-color: rgb(255, 255, 255);
    padding: 10px ;
    border-radius: 10px;
    border-left: 1px solid #fff9f9;
}





    
    </style>
  </head>
  <body class="bg-light">
      
<div class="appCapsule container ">
  <header class="row fixed-top">
        <div class="text-white py-3">
            <div class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <a href="me.php" class="nav-link left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6>My coupon</h6>

                <div class="px-3"></div>

        </div>
        </div>

  </header>

  <div class="row appBody">
<div class="col-12 bg-white">
    <div class="header-button">
      <a href="mycoupon.php" class="active">Unused</a>
      <a href="used-mycoupon.php">Used</a>
      <a href="expired.php">Expired</a>
    </div>
</div>


<div class="col-12">
       <?php 
        $i=1;
        $query = mysqli_query($con,"select * from capping_request where cr_status='Pending' AND cr_userid='$userid_access' order by cr_id desc");
        if(mysqli_num_rows($query)>0){
            while($row=mysqli_fetch_array($query)){
                $id = $row['cr_id'];
                $user_id = $row['cr_userid'];
                $cr_capping_amount	 = $row['cr_capping_amount'];
                $cr_amount = $row['cr_amount'];
                $cr_request_date = $row['cr_request_date'];
                $cr_status = $row['cr_status'];
                
        ?>
    <div class="couponBox ">
        <div class="left">
            <h1><?php echo $cr_amount; ?></h1>
            <h6>Gift</h6>
        </div>
        <div class="right">
            <div class="rightBody d-flex justify-content-between" >
                <b>Coupon : <?php echo $cr_capping_amount; ?></b>
                <b>x1</b>
            </div>

            <div class="rightBod" >
               <small class="text-muted">Create: <?php echo $cr_request_date;?></small>
            </div>
        </div>
    </div>
    <?php } } ?>

  <!--
    <div class="couponBox ">
        <div class="left">
            <h1>3 <span>%</span></h1>
            <h6>Discount</h6>
        </div>

        <div class="right">
            <div class="rightBody d-flex justify-content-between" >
                <b>2% Coupon</b>
                <b>x1</b>
            </div>

            <div class="rightBod" >
               <small class="text-muted">All products</small>
               <br>
               <small class="text-muted">Expired: 25/01/2024 </small>
            </div>




        </div>

    </div>

    <div class="couponBox ">
        <div class="left">
            <h1>2 <span>%</span></h1>
            <h6>Discount</h6>
        </div>

        <div class="right">
            <div class="rightBody d-flex justify-content-between" >
                <b>2% Coupon</b>
                <b>x1</b>
            </div>

            <div class="rightBod" >
               <small class="text-muted">All products</small>
               <br>
               <small class="text-muted">Expired: 25/01/2024 </small>
            </div>




        </div>

    </div>

    <div class="couponBox ">
        <div class="left">
            <h1>3 <span>%</span></h1>
            <h6>Discount</h6>
        </div>

        <div class="right">
            <div class="rightBody d-flex justify-content-between" >
                <b>2% Coupon</b>
                <b>x1</b>
            </div>

            <div class="rightBod" >
               <small class="text-muted">All products</small>
               <br>
               <small class="text-muted">Expired: 25/01/2024 </small>
            </div>




        </div>

    </div>

    <div class="couponBox ">
        <div class="left">
            <h1>2 <span>%</span></h1>
            <h6>Discount</h6>
        </div>

        <div class="right">
            <div class="rightBody d-flex justify-content-between" >
                <b>2% Coupon</b>
                <b>x1</b>
            </div>

            <div class="rightBod" >
               <small class="text-muted">All products</small>
               <br>
               <small class="text-muted">Expired: 25/01/2024 </small>
            </div>




        </div>

    </div>

    <div class="couponBox ">
        <div class="left">
            <h1>3 <span>%</span></h1>
            <h6>Discount</h6>
        </div>

        <div class="right">
            <div class="rightBody d-flex justify-content-between" >
                <b>2% Coupon</b>
                <b>x1</b>
            </div>

            <div class="rightBod" >
               <small class="text-muted">All products</small>
               <br>
               <small class="text-muted">Expired: 25/01/2024 </small>
            </div>




        </div>

    </div>

    <div class="couponBox ">
        <div class="left">
            <h1>3 <span>%</span></h1>
            <h6>Discount</h6>
        </div>

        <div class="right">
            <div class="rightBody d-flex justify-content-between" >
                <b>2% Coupon</b>
                <b>x1</b>
            </div>

            <div class="rightBod" >
               <small class="text-muted">All products</small>
               <br>
               <small class="text-muted">Expired: 25/01/2024 </small>
            </div>




        </div>

    </div> -->
</div>











  </div>


  

 



</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  
  </body>
</html>