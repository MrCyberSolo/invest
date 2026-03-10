<style>
    .footerBox {
        background-color: white !important;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-around;
        align-items: flex-end;
        padding: 8px 0;
        z-index: 1000;
    }
    
    .footerBox .inner {
        color: #A0A0A0; /* Gray for inactive */
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 20%;
    }

    .footerBox .inner i {
        font-size: 22px;
        margin-bottom: 2px;
        color: #A0A0A0;
    }

    .footerBox .inner.active {
        color: #007749; /* Green for active */
    }
    
    .footerBox .inner.active i {
        color: #007749;
    }

    .footerBox .inner p {
        font-size: 12px;
        margin: 0;
    }

    .me-icon-container {
        width: 58px;
        height: 58px;
        background-color: #007749;
        border-radius: 50%;
        border: 5px solid white;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 2px;
    }
    .me-icon-container i {
        color: #A0A0A0 !important;
        font-size: 32px !important;
        margin-bottom: 0 !important;
    }
    .inner.active .me-icon-container i {
        color: white !important;
    }
</style>

<?php
$currentFile = $_SERVER['PHP_SELF'];
$currentPage = basename($currentFile); 
?>
 
<footer class="footerBox fixed-bottom">
  <a href="home.php" class="inner <?php if($currentPage == 'home.php') echo 'active'; ?>">
    <i class="bi bi-house-door-fill"></i>
    <p>Home</p>
  </a>

  <a href="product.php" class="inner <?php if($currentPage == 'product.php') echo 'active'; ?>">
    <i class="bi bi-grid-fill"></i>
    <p>Product</p>
  </a>

  <a href="me.php" class="inner <?php if($currentPage == 'me.php') echo 'active'; ?>" style="position:relative; bottom: 15px;">
    <div class="me-icon-container">
        <i class="bi bi-person-fill"></i>
    </div>
    <p style="position:relative; top: -5px;">Me</p>
  </a>

  <a href="news.php" class="inner <?php if($currentPage == 'news.php') echo 'active'; ?>">
    <i class="bi bi-file-earmark-richtext-fill"></i>
    <p>News</p>
  </a>

  <a href="spinner.php" class="inner <?php if($currentPage == 'spinner.php') echo 'active'; ?>">
    <i class="bi bi-dice-5-fill"></i>
    <p>Spinner</p>
  </a>
</footer>