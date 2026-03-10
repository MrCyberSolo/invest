<?php
session_start();

// Check if user is logged in
/* if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
} */
include('user_menu/database_connect.php');
//$userid_access = $_SESSION['username'];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#213955;
        --light-blue:#014f97;
        --yellow:#014f97;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule, .footerBox{
        max-width: 641px;
        /* background-color: #014f97; */
        margin: auto;

       }

       .appCapsule{
        padding-bottom: 5rem;
       }

       /* body{
        background-color: #014f97;
       } */

       


/* ==========================  */
/* footer section   */
.footerBox .active2{
  color:#014f97 ;
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

/* product page ================  */
/* product page ================  */
.product-section {
  padding: 0 10px;
}

.product-section .card {
  width: 100%;
  height: 100%;
  padding: 0 !important;
  background-color: white;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
}

.product-section .card-img {
    width: 100%;
    position: relative;
    background: white;
    padding: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.product-section img {
    max-width: 100%;
    max-height: 120px;
    object-fit: contain;
}

.product-name-banner {
    background-color: #014f97;
    color: white;
    text-align: center;
    padding: 6px 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.product-section .card-text {
    width: 100%;
    background-color: white;
    padding: 10px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-section .card-title {
    font-weight: bold;
    color: #333;
    font-size: 14px;
    margin-bottom: 8px;
}

.product-section .card-body {
  padding: 0;
  background-color: transparent;
}

.product-section .inner small {
  color: #666;
  font-size: 11px;
}

.product-section .inner small.val {
  color: #333;
  font-weight: 500;
}

.product-section .inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 4px;
}

.view-project-btn {
    background-color: #398af2; 
    color: white !important;
    border-radius: 20px;
    font-size: 12px;
    padding: 6px 0;
    text-align: center;
    text-decoration: none;
    width: 100%;
    margin-top: 10px;
    display: inline-block;
    border: none;
}


.headerTab a{
  text-decoration: none;
  color: white;
  padding: 7px;
  width: 47%;
  text-align: center;
  border-radius: 8px;
  margin-bottom: 1rem;
  background-color: #ffffff;
  color:black;
  transition: .4s;

}

.headerTab .active{
      background-color: #ff1300;
    color: #fff;
}

.headerTab a:hover{
  background-color: #F5F5F5;
}

.product-section a{
  text-decoration: none;
}
    
    </style>
  </head>
  <body>
<div class="appCapsule container">
    <header class="mt-3">
        <!-- <div class="header-title text-center py-3">
            <h5>Product</h5>
            <div class="line"></div>
        </div> -->

        <div class="headerTab d-flex justify-content-around">
          <a href="product.php" class="active">
            <i class="bi bi-gear-fill"></i> H-power</a>
         <!-- <a href="product_2.php">LAY'S</a>-->
        </div>

      
  </header>



<div class="row product-section gx-2 gy-2">
<?php
      $i=0;
    $result = mysqli_query($con,"SELECT * FROM `package` WHERE `status`='Active' AND `pa_type`='User'"); 
    if (mysqli_num_rows($result) > 0) {
      // Output data of each row
      while ($row = mysqli_fetch_assoc($result)) {
          
    ?>
 <div class="col-6 mb-2 mt-2">
      <a href="product-details.php?pr_id=<?php echo $row["pa_id"]; ?>" class="text-decoration-none">
        <div class="card">
            
            <div class="card-img">
                 <img src="asupport/package/<?php echo $row["pa_image"];?>" alt="Product Image">
            </div>

            <!-- Note: Replaced 🏅 with the screenshot's design (just name and blue banner above it if possible, here using banner for pa_name since it matches the layout better) -->
            <div class="product-name-banner">
                <?php echo $row["pa_name"]; ?>
            </div>
            
            <div class="card-text">
               <h6 class="card-title"><?php echo $row["pa_name"]; ?> - <?php echo $row["pa_amount"]; ?></h6>
               
                <div class="inner">
                  <small>Price:</small>
                  <small class="val"><?php echo $row["pa_amount"]; ?>.00RS</small>
                </div>

                <div class="inner">
                  <small>Term:</small>
                  <small class="val"><?php echo $row["pa_day"]; ?> days</small>
                </div>

                <div class="inner">
                  <small>Daily income:</small>
                  <small class="val"><?php echo $row["pa_com_amount"]; ?>RS</small>
                </div>

                <div class="inner">
                  <small>Total profit:</small>
                  <small class="val"><?php echo number_format((float)($row["pa_com_amount"] * $row["pa_day"]), 2, '.', ''); ?>RS</small>
                </div>

                <div class="mt-auto">
                    <button class="view-project-btn">View project</button>
                </div>                
            </div>
        </div>
      </a>   
</div>
<?php  $i++; } } ?>   
</div>
<!-- Footer Start Here -->
<?php include "user_menu/footer_menu.php";  ?>
<!-- Footer End Here -->
   

</div>    <!--  end appCapsule  -->
<!-- =================================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>