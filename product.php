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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Product</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       :root {
          --brand-green: #007749;
          --brand-dark: #005a36;
          --text-main: #1e293b;
          --text-muted: #64748b;
          --card-bg: #ffffff;
       }

       body, html {
          background-color: var(--brand-green) !important;
          font-family: 'Inter', sans-serif;
          -webkit-font-smoothing: antialiased;
          margin: 0;
          padding: 0;
       }

       .appCapsule {
          max-width: 641px;
          margin: auto;
          min-height: 100vh;
          background-color: var(--brand-green);
          padding-bottom: 90px;
       }

       .page-header {
          padding: 16px;
          position: sticky;
          top: 0;
          background: rgba(0, 119, 73, 0.95);
          backdrop-filter: blur(12px);
          -webkit-backdrop-filter: blur(12px);
          z-index: 50;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
       }

       .headerTab {
          display: flex;
          justify-content: center;
          gap: 12px;
          margin: 0;
       }

       .headerTab a {
          text-decoration: none;
          color: var(--brand-green);
          background-color: #ffffff;
          padding: 10px 24px;
          border-radius: 24px; /* pill shape */
          font-weight: 600;
          font-size: 15px;
          display: flex;
          align-items: center;
          gap: 8px;
          transition: transform 0.2s, box-shadow 0.2s;
          box-shadow: 0 4px 12px rgba(0,0,0,0.1);
          -webkit-tap-highlight-color: transparent;
       }

       .headerTab a:active {
           transform: scale(0.95);
       }

       .products-grid {
           padding: 16px;
           display: grid;
           grid-template-columns: repeat(2, 1fr);
           gap: 16px;
       }

       .product-card {
           background: var(--card-bg);
           border-radius: 20px;
           overflow: hidden;
           box-shadow: 0 8px 24px rgba(0,0,0,0.08);
           display: flex;
           flex-direction: column;
           text-decoration: none;
           transition: transform 0.2s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.2s ease;
           -webkit-tap-highlight-color: transparent;
           height: 100%;
       }

       .product-card:active {
           transform: scale(0.96);
           box-shadow: 0 4px 12px rgba(0,0,0,0.05);
       }

       .product-image-wrapper {
           background-color: #f8fafc;
           padding: 16px;
           text-align: center;
           display: flex;
           justify-content: center;
           align-items: center;
           height: 120px;
       }

       .product-image {
           max-width: 100%;
           max-height: 100px;
           object-fit: contain;
           filter: drop-shadow(0 4px 8px rgba(0,0,0,0.08));
       }

       .product-content {
           padding: 16px;
           display: flex;
           flex-direction: column;
           flex: 1;
       }

       .product-title {
           font-size: 15px;
           font-weight: 700;
           color: var(--text-main);
           margin-bottom: 12px;
           line-height: 1.3;
           display: -webkit-box;
           -webkit-line-clamp: 2;
           -webkit-box-orient: vertical;
           overflow: hidden;
       }

       .product-stats {
           display: flex;
           flex-direction: column;
           gap: 8px;
           margin-bottom: 16px;
           flex: 1;
       }

       .stat-row {
           display: flex;
           justify-content: space-between;
           align-items: center;
           font-size: 12px;
       }

       .stat-label {
           color: var(--text-muted);
           font-weight: 500;
       }

       .stat-value {
           color: var(--text-main);
           font-weight: 600;
       }

       .val-highlight {
           color: var(--brand-green);
           font-weight: 700;
       }

       .view-btn {
           background-color: var(--brand-green);
           color: white;
           text-align: center;
           padding: 10px 0;
           border-radius: 12px;
           font-size: 13px;
           font-weight: 600;
           margin-top: auto;
           display: block;
           transition: background-color 0.2s;
       }
    </style>
  </head>
  <body>
    <div class="appCapsule">
       <header class="page-header">
          <div class="headerTab">
            <a href="product.php" class="active">
              <i class="bi bi-gear-fill"></i> H-power
            </a>
          </div>
       </header>

       <div class="products-grid">
       <?php
          $result = mysqli_query($con,"SELECT * FROM `package` WHERE `status`='Active' AND `pa_type`='User'"); 
          if (mysqli_num_rows($result) > 0) {
             while ($row = mysqli_fetch_assoc($result)) {
       ?>
          <a href="product-details.php?pr_id=<?php echo htmlspecialchars($row["pa_id"]); ?>" class="product-card">
              <div class="product-image-wrapper">
                   <img src="asupport/package/<?php echo htmlspecialchars($row["pa_image"]);?>" class="product-image" alt="Product Image">
              </div>
              <div class="product-content">
                 <h3 class="product-title"><?php echo htmlspecialchars($row["pa_name"]); ?></h3>
                 
                 <div class="product-stats">
                    <div class="stat-row">
                      <span class="stat-label">Price</span>
                      <span class="stat-value">₹<?php echo number_format((float)$row["pa_amount"], 2); ?></span>
                    </div>

                    <div class="stat-row">
                      <span class="stat-label">Term</span>
                      <span class="stat-value"><?php echo htmlspecialchars($row["pa_day"]); ?> days</span>
                    </div>

                    <div class="stat-row">
                      <span class="stat-label">Daily</span>
                      <span class="stat-value val-highlight">₹<?php echo number_format((float)$row["pa_com_amount"], 2); ?></span>
                    </div>

                    <div class="stat-row">
                      <span class="stat-label">Profit</span>
                      <span class="stat-value">₹<?php echo number_format((float)($row["pa_com_amount"] * $row["pa_day"]), 2); ?></span>
                    </div>
                 </div>

                 <div class="view-btn">View project</div>                
              </div>
          </a>   
       <?php  } } else { ?>
           <div style="grid-column: 1 / -1; text-align: center; color: white; padding: 40px 20px;">
               No active products found.
           </div>
       <?php } ?>   
       </div>

    </div>

    <!-- Footer Start Here -->
    <?php include "user_menu/footer_menu.php";  ?>
    <!-- Footer End Here -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>