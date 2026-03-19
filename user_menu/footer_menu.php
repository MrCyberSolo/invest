<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

    .footerBox {
        font-family: 'Inter', sans-serif;
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.06);
        border-top: 1px solid rgba(255, 255, 255, 0.5);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 6px 10px;
        padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 6px);
        z-index: 1030;
        width: 100%;
        max-width: 641px;
        margin: 0 auto;
        left: 0;
        right: 0;
    }
    
    .footerBox .inner {
        color: #94a3b8; /* Subtle blue-gray */
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 20%;
        min-height: 48px; /* Touch target size */
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-radius: 12px;
        -webkit-tap-highlight-color: transparent;
    }

    .footerBox .inner:active {
        transform: scale(0.92);
    }

    .footerBox .inner i {
        font-size: 22px;
        margin-bottom: 2px;
        color: #94a3b8;
        transition: all 0.3s ease;
    }

    .footerBox .inner p {
        font-size: 11px;
        font-weight: 500;
        margin: 0;
        letter-spacing: 0.2px;
        transition: all 0.3s ease;
    }

    /* Active State */
    .footerBox .inner.active {
        color: #007749; 
    }
    
    .footerBox .inner.active i {
        color: #007749;
        transform: translateY(-2px);
        text-shadow: 0 4px 10px rgba(0, 119, 73, 0.25);
    }

    .footerBox .inner.active p {
        font-weight: 600;
        color: #007749;
    }

    /* Raised Me Button */
    .footerBox .me-tab {
        position: relative;
        top: -15px; 
    }
    
    .me-icon-container {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, #009a5f, #007749);
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 16px rgba(0, 119, 73, 0.25);
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 4px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .me-tab:active .me-icon-container {
        transform: scale(0.92) translateY(2px);
        box-shadow: 0 4px 8px rgba(0, 119, 73, 0.2);
    }

    .me-icon-container i {
        color: rgba(255, 255, 255, 0.95) !important;
        font-size: 26px !important;
        transform: translateY(1px); /* optical alignment */
        margin-bottom: 0 !important;
    }

    .me-tab.active .me-icon-container {
        border-color: #f0fdf4;
        box-shadow: 0 10px 20px rgba(0, 119, 73, 0.4);
    }

    .me-tab.active .me-icon-container i {
        color: #ffffff !important;
        transform: translateY(1px) scale(1.05); /* Slight bump up */
        text-shadow: none;
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

  <a href="me.php" class="inner me-tab <?php if($currentPage == 'me.php') echo 'active'; ?>">
    <div class="me-icon-container">
        <i class="bi bi-person-fill"></i>
    </div>
    <p>Me</p>
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