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
        --light-blue:#009a5f;
        --yellow:#009a5f;
        
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
        padding-bottom: 5rem;
       }

       /* body{
        background-color: #009a5f;
       } */

       


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
  background-color: #fff;
  justify-content: space-around;
  align-items: center;
  text-align: center;
  /* padding: 5px 0; */
  height: 60px;   box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
}

.footerBox img{
  width: 25px;
}

.footerBox a{
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
}
       
/* end footer section   */   
/* ==========================  */

/* product page ================  */
.product-section .card{
  width: 100%;
  height: 100%;
  padding: 0 !important;
  
}

.product-section h6{
  color: rgb(75, 75, 75);
  font-weight: normal;
  
}
.product-section .card-body{
  padding: 0;
  padding-top: 5px;
  background-color: #F5F5F5;
  
}

.product-section .inner small{
  color: #424242;
}

.product-section .inner{
  margin-top: 5px;
}


/* .product-section .inner small:first-child{
  color: #CCCCCC;
  font-size: 12px;
}

.product-section .inner small:last-child{
  color:#009a5f;
  font-size: 13px;
} */

.product-section img{
  width: 100%;
  height: 100%;
}

.product-section .btnBox a{
  padding: 10px;
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
  background-color: #F5F5F5;
}

.headerTab a:hover{
  background-color: #F5F5F5;
}

.product-section a{
  text-decoration: none;
}
    
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container">
    <header class="mt-3">
        <!-- <div class="header-title text-center py-3">
            <h5>Product</h5>
            <div class="line"></div>
        </div> -->

        <div class="headerTab d-flex justify-content-around">
          <a href="product.php" >
            <i class="bi bi-gear-fill"></i> Smart products</a>

          <a href="fashion.php" class="active">Fashion Tech</a>
        </div>

      
  </header>


<div class="row product-section gx-2 gy-2">

<div class="col-6">
  <a href="product-details.php">
    <div class="card p-2">
        <img src="img/p1.jpg" class="card-img-top rounded" alt="...">
        <div class="card-body p-2">
          <h6 class="card-title m-0">🏅Costco-550</h6>
          <div class="card-text ">
            <div class="inner">
              <small class="">Price: </small>
              <small> ₹520.00</small>
            </div>

            <div class="inner">
              <small>Term:</small>
              <small>43days</small>
            </div>

            <div class="inner">
              <small>Daily Income</small>
              <small>₹21</small>
            </div>

            <div class="inner">
              <small>Total revenue:</small>
              <small>₹916.76</small>
            </div>
            <!-- <div class="inner" style="background-color: #009a5f; font-size: 8px; text-align: end; padding: 0 10px; color: white; border-radius: 20px;">
              <p class="">100%</p>
            </div> -->

            <div class="btnBox d-grid mt-1">
              <a href="" class="btn text-white" style="background-color: #009a5f; font-size: 15px; padding: 5px;">See details</a>
            </div>

            
        </div>
      </div>
  </div>
</a>


</div>


<div class="col-6">
  <a href="product-details.php">
    <div class="card p-2">
        <img src="img/p1.jpg" class="card-img-top rounded" alt="...">
        <div class="card-body p-2">
          <h6 class="card-title m-0">🏅Costco-550</h6>
          <div class="card-text ">
            <div class="inner">
              <small class="">Price: </small>
              <small> ₹520.00</small>
            </div>

            <div class="inner">
              <small>Term:</small>
              <small>43days</small>
            </div>

            <div class="inner">
              <small>Daily Income</small>
              <small>₹21</small>
            </div>

            <div class="inner">
              <small>Total revenue:</small>
              <small>₹916.76</small>
            </div>
            <!-- <div class="inner" style="background-color: #009a5f; font-size: 8px; text-align: end; padding: 0 10px; color: white; border-radius: 20px;">
              <p class="">100%</p>
            </div> -->

            <div class="btnBox d-grid mt-1">
              <a href="" class="btn text-white" style="background-color: #009a5f; font-size: 15px; padding: 5px;">See details</a>
            </div>

            
        </div>
      </div>
  </div>
</a>


</div>




</div>





<footer class="footerBox fixed-bottom">
  <a href="home.php" class="inner">
    <img src="img/icons/download.png" alt="">
    <p>Home</p>
  </a>

  <a href="product.php" class="inner">
    <img src="img/icons/download (4).png" alt="">
    <p>Product</p>
  </a>

  <a href="me.php" class="inner me-icon ">
    <img src="img/icons/download (1).png" alt="">
    <p>Me</p>
  </a>

  <a href="myteams.php" class="inner">
    <img src="img/icons/download (2).png" alt="">
    <p>My Teams </p>
  </a>

  <a href="news.php" class="inner">
    <img src="img/icons/download (3).png" alt="">
    <p>News</p>
  </a>
</footer>
   

</div>    <!--  end appCapsule  -->
<!-- =================================== -->
    













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>