<style>
    .active {
        color: #0dcaf0; /* Change the color to red */
    }
    
.footerBox i{
  font-size: 22px;
}



</style>
<?php
$currentFile = $_SERVER['PHP_SELF'];
 $currentPage = basename($currentFile); ?>
 
<footer class="footerBox fixed-bottom">
  <a href="home.php" class="inner active1"  <?php if($currentPage == 'home.php') echo 'active'; ?>">
    
    <!--<img src="img/icons/download.png" alt="">-->
    <i class="bi bi-house-door-fill"></i>
    <p>Home</p>
  </a>

  <a href="product.php" class="inner active2"  <?php if($currentPage == 'product.php') echo 'active'; ?>">
    <!--<img src="img/icons/product1.png" alt="">-->
    <i class="bi bi-grid-fill"></i>
    <p>Product</p>
  </a>

  <a href="me.php" class="inner me-icon active3" style="position:relative; bottom:15px; "   <?php if($currentPage == 'me.php') echo 'active'; ?>">
    <img src="img/icons/download (1).png" alt=""  style="width:50px; background-color:#005a36; padding:2px; border-radius:50%; margin-bottom:7px ; border:5px solid white; " >
    <p>Me</p>
  </a>


  <a href="news.php" class="inner active4"  <?php if($currentPage == 'news.php') echo 'active'; ?>">
    <!--<img src="img/icons/download (3).png" alt="">-->
    <i class="bi bi-file-earmark-richtext-fill"></i>
    <p>News</p>
  </a>

    
  <a href="spinner.php" class="inner active5"  <?php if($currentPage == 'spinner.php') echo 'active'; ?>">
  <i class="bi bi-dice-5-fill"></i>
  <p>Spinner</p>
  </a>



  
</footer>