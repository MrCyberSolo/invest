<?PHP 
include('user_menu/database_connect.php');
//$userid_access = $_SESSION['username'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>News</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --blue:#213955;
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule, .footerBox{
        max-width: 641px;
        margin: auto;
       }

       .appCapsule{
        padding-bottom: 4rem;
       }

       /* body{
        background-color: #009a5f;
       } */

       header .carousel img{
        max-height:250px ;
        height: auto;
        border-radius: 5px;
       }

       /* .headerBox .cardBox{
        background-color:white;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding: 16px 0;
        border-radius: 5px;
       }

       .headerBox img{
        width: 40px;
        height: 100%;

        
       }

       .headerBox .cardBox h6{
        margin: 0;
       } */

   

       .menuBox{
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        padding-top: 7px;
       }

       .menuBox .inner{
        width: 100%;
       }

       .menuBox img{
        width: 40px;
       }

       .menuBox p{
        font-size: 13px;
       }

       .textBox .inner{
        background-color: #007749;
        padding: 6px 10px;
        border-radius: 5px;
       }

       .textBox .inner i{
        color: #FFC983;
        font-size: 20px;
       }

       .newsSection a{
        text-decoration: none;
        border-bottom: 1px solid #dddddd5e;
        padding: 10px 5px;
       }





/* ==========================  */
/* footer section   */

.footerBox .active4{
  color:#007749 ;
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

    </style>
  </head>
  <body>
   <div class="appCapsule">
    <header class="container">

      <div class="header-title text-center py-3">
        <h5>News</h5>
      </div>

      <div class="row ">
          <div id="carouselExampleControls" class="carousel slide px-3" data-bs-ride="carousel">
              <!--<div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/s1.jpg" class="d-block w-100" alt="..." style="height: 200px;">
                </div>
                <div class="carousel-item">
                  <img src="img/s2.jpg" class="d-block w-100" alt="..." style="height: 200px;">
                </div>
                <div class="carousel-item">
                  <img src="img/s3.jpg" class="d-block w-100" alt="..." style="height: 200px;">
                </div>

                <div class="carousel-item">
                  <img src="img/s4.png" class="d-block w-100" alt="..." style="height: 200px;">
                </div> 

              </div>-->
              <!-- <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button> -->
              <!-- <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button> -->
            </div>
      </div>
      
  </header>



  <div class="container appContent mt-3">
    <div class="row videoSection mt-3">
      <div class="col-12 newsSection mt-2 px-3">

      <div class="newsBox  bg-white" style="border-bottom-right-radius: 8px; border-bottom-left-radius: 8px;">

      <?php 
        $i=1;
        $query = mysqli_query($con,"select * from blog order by b_id desc");
        if(mysqli_num_rows($query)>0){
            while($row=mysqli_fetch_array($query)){
                
                $blog_id= $row['b_id'];
                $blog_title = $row['b_title'];
                $blog_image = $row['b_image'];
                $blog_short = $row['b_details'];
                $blog_create_date = $row['b_create_date'];
          ?>
          <a href="news_details.php?pac=<?php echo $blog_id; ?>" class="inner d-flex justify-content-between ">
            <div class="text text-dark ">
              <p><?php echo $blog_title; ?></p>
              <br>
              <small class=""><?php echo $blog_create_date; ?></small>
            </div>
  
            <div class="imges">
              <img src="https://png.pngtree.com/png-vector/20231127/ourmid/pngtree-demo-red-flat-icon-isolated-demo-icon-png-image_10722763.png" class="img-fluid" alt="" style="max-width: 150px; border-radius: 8px;">
            </div>
          </a>
          <?php } } ?>
      </div>     
      <div class="py-3 text-center" style="margin-bottom: 1rem; color: white;">
        <p>No more</p>
      </div>
       
      </div>
    </div>


</div>



<!-- Footer Start Here -->
<?php include "user_menu/footer_menu.php";  ?>
<!-- Footer End Here -->
   

</div>    <!--  end appCapsule  -->
<!-- =================================== -->
    













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>