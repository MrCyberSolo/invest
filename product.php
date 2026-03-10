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
        margin: auto;
       }

       .appCapsule {
        padding-bottom: 5rem;
        background-color: #1a569d;
        min-height: 100vh;
       }

       body {
        background-color: #1a569d;
        color: white;
       }
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
.product-section .card{
  width: 100%;
  height: 100%;
  background-color: white;
  border-radius: 12px;
  border: none;
  overflow: hidden;
  position: relative;
}

  .product-section .card-title{
      font-weight: 700;
      font-size: 14px;
      color: #333;
      margin-bottom: 8px;
  }
  
    .product-section .card-img{
        width:100%;
        position: relative;
        padding-top: 10px;
        background: white;
        height: 120px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .product-section .card-img img {
        width: 80%;
        height: auto;
        object-fit: contain;
    }

    .image-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: red;
        color: white;
        font-size: 8px;
        font-weight: bold;
        padding: 2px 4px;
        border-radius: 2px;
    }

    .bosch-logo {
        position: absolute;
        top: 5px;
        display: flex;
        width: 100%;
        justify-content: center;
        align-items: center;
        gap: 5px;
    }
    
    .blue-banner {
        background: #1a569d;
        color: white;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        padding: 6px 4px;
        text-transform: uppercase;
    }

.product-section h6{
  color: rgb(75, 75, 75);
  font-weight: normal;
}

.product-section .card-body{
  padding: 12px;
  background-color: white;
}

.product-section .inner{
  display: flex;
  margin-bottom: 5px;
  font-size: 11px;
}

.product-section .inner small:first-child{
  color: #888;
  width: 75px;
}

.product-section .inner small:last-child{
  color: #444;
  font-weight: 500;
}

.btnBox a{
  background-color: #398af2;
  color: white;
  border-radius: 20px;
  font-size: 13px;
  padding: 8px;
  font-weight: 500;
}


.header-title-custom {
    text-align: center;
    color: white;
    font-size: 14px;
    margin-bottom: 15px;
    font-weight: 500;
}

.product-section a{
  text-decoration: none;
}
    
    </style>
  </head>
  <body>
<div class="appCapsule container">
    <header class="mt-3">
        <div class="header-title-custom">
            <span>H-power tools🔪</span>
        </div>
    </header>

<div class="row product-section gx-2 gy-2 px-2">
<?php
      $i=0;
    $result = mysqli_query($con,"SELECT * FROM `package` WHERE `status`='Active' AND `pa_type`='User'"); 
    if (mysqli_num_rows($result) > 0) {
      // Output data of each row
      while ($row = mysqli_fetch_assoc($result)) {
          
    ?>
 <div class="col-6 mb-2">
      <a href="product-details.php?pr_id=<?php echo $row["pa_id"]; ?>">
        <div class="card">
            
            <div class="card-img">
                <div class="image-badge">HEAVY<br>DUTY</div>
                <div class="bosch-logo">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/Bosch-Logo.svg/1024px-Bosch-Logo.svg.png" style="height: 14px; width: auto;" alt="">
                </div>
                <!-- Assuming the original image doesn't have the logo embedded directly based on the new UI screenshot -->
                <img src="asupport/package/<?php echo $row["pa_image"];?>" class="card-img-top" alt="...">
            </div>
            
            <div class="blue-banner">
                <?php echo $row["pa_name"]; ?>
            </div>
               
            <div class="card-body">
                <h6 class="card-title"><?php echo $row["pa_name"]; ?></h6>
                   
                <div class="inner">
                  <small>Price: </small>
                  <small><?php echo $row["pa_amount"]; ?>.00RS</small>
                </div>

                <div class="inner">
                  <small>Term:</small>
                  <small><?php echo $row["pa_day"]; ?> days</small>
                </div>

                <div class="inner">
                  <small>Daily income:</small>
                  <small><?php echo $row["pa_com_amount"]; ?>RS</small>
                </div>

                <div class="inner">
                  <small>Total profit:</small>
                  <small><?php echo  $row["pa_com_amount"] * $row["pa_day"]; ?>.00RS</small>
                </div>
                
                <div class="btnBox d-grid mt-3">
                  <a href="product-details.php?pr_id=<?php echo $row["pa_id"]; ?>" class="btn">View project</a>
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