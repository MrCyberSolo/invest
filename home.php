<?php 
session_start();
include('user_menu/database_connect.php');
if(isset($_SESSION['username'])) {
    $userid_access = $_SESSION['username'];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <title>Home</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       :root {
          --brand-green: #007749;
          --font-dark: #1e293b;
          --font-muted: #64748b;
          --bg-gray: #f8fafc;
          --accent-blue: #0284c7;
          --accent-gold: #f59e0b;
       }

       body, html {
          background-color: var(--brand-green) !important;
          font-family: 'Inter', sans-serif;
          -webkit-font-smoothing: antialiased;
          margin: 0;
          padding: 0;
       }

       p, h1, h2, h3, h4, h5, h6 { margin: 0; }
       a { text-decoration: none; }

       .appCapsule {
          max-width: 641px;
          margin: auto;
          min-height: 100vh;
          background-color: var(--brand-green);
          padding-bottom: calc(90px + env(safe-area-inset-bottom));
          padding-top: max(16px, env(safe-area-inset-top));
          overflow-x: hidden;
       }

       /* Top Carousel Area */
       .hero-section {
           display: flex;
           align-items: center;
           padding: 0 0 0 16px;
           margin-bottom: 24px;
       }
       
       .carousel-container {
           width: calc(100% - 110px);
       }

       .carousel-inner {
           border-radius: 20px;
           box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
           overflow: hidden;
       }

       .carousel-item img {
           height: 220px;
           object-fit: cover;
           width: 100%;
       }

       /* Floating Side Menu */
       .floating-right-menu {
           width: 110px;
           display: flex;
           flex-direction: column;
           gap: 8px;
           align-items: flex-end;
           padding-left: 10px;
       }

       .floating-btn {
           background: rgba(255, 255, 255, 0.15);
           backdrop-filter: blur(10px);
           -webkit-backdrop-filter: blur(10px);
           color: white;
           padding: 8px 8px 8px 12px;
           border-radius: 24px 0 0 24px;
           font-size: 11.5px;
           font-weight: 500;
           display: flex;
           align-items: center;
           gap: 6px;
           box-shadow: -4px 4px 12px rgba(0,0,0,0.1);
           border: 1px solid rgba(255,255,255,0.15);
           border-right: none;
           width: 100%;
           transition: transform 0.2s, background 0.2s;
       }

       .floating-btn:active {
           transform: scale(0.95) translateX(-5px);
           background: rgba(255, 255, 255, 0.25);
       }

       .floating-btn i {
           font-size: 15px;
           color: #fff;
       }

       .floating-btn span {
           white-space: nowrap;
           overflow: hidden;
           text-overflow: ellipsis;
       }

       /* Notice Marquee */
       .notice-bar {
           background: rgba(255, 255, 255, 0.1);
           backdrop-filter: blur(10px);
           margin: 0 16px 24px;
           border-radius: 16px;
           padding: 12px 16px;
           display: flex;
           align-items: center;
           gap: 12px;
           border: 1px solid rgba(255,255,255,0.05);
       }

       .notice-icon {
           color: var(--accent-gold);
           font-size: 20px;
           display: flex;
           align-items: center;
       }

       .notice-text {
           flex: 1;
           color: white;
           font-size: 15px;
           font-weight: 500;
           white-space: nowrap;
           overflow: hidden;
       }

       /* Main Quick Actions Grid */
       .quick-actions-grid {
           display: grid;
           grid-template-columns: repeat(4, 1fr);
           gap: 12px;
           padding: 0 16px;
           margin-bottom: 32px;
       }

       .action-item {
           background: #ffffff;
           border-radius: 20px;
           padding: 16px 8px;
           display: flex;
           flex-direction: column;
           align-items: center;
           justify-content: center;
           gap: 8px;
           box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
           transition: transform 0.2s;
       }

       .action-item:active {
           transform: scale(0.92);
       }

       .action-icon {
           width: 46px;
           height: 46px;
           border-radius: 14px;
           display: flex;
           align-items: center;
           justify-content: center;
           font-size: 24px;
       }

       .icon-green { background: rgba(0, 119, 73, 0.1); color: var(--brand-green); }
       .icon-blue { background: rgba(2, 132, 199, 0.1); color: var(--accent-blue); }
       .icon-orange { background: rgba(245, 158, 11, 0.1); color: var(--accent-gold); }
       .icon-purple { background: rgba(147, 51, 234, 0.1); color: #9333ea; }

       .action-label {
           color: var(--font-dark);
           font-size: 13px;
           font-weight: 600;
           text-align: center;
       }

       /* Task & Video Section */
       .media-section {
           background: #ffffff;
           border-radius: 32px 32px 0 0;
           padding: 32px 20px 20px;
           min-height: 400px;
           box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.1);
       }

       .section-title {
           font-size: 18px;
           font-weight: 700;
           color: var(--font-dark);
           margin-bottom: 16px;
           display: flex;
           align-items: center;
           justify-content: space-between;
       }

       .section-title-link {
           font-size: 14px;
           color: var(--font-muted);
           font-weight: 600;
           display: flex;
           align-items: center;
           gap: 4px;
       }

       .video-wrapper {
           border-radius: 20px;
           overflow: hidden;
           background: #000;
           margin-bottom: 24px;
           box-shadow: 0 8px 24px rgba(0,0,0,0.1);
           position: relative;
           padding-top: 56.25%; /* 16:9 Aspect Ratio */
       }

       .video-wrapper video {
           position: absolute;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           object-fit: cover;
       }

       .task-banners {
           display: grid;
           grid-template-columns: 1fr 1fr;
           gap: 16px;
           margin-bottom: 32px;
       }

       .task-card {
           background: var(--bg-gray);
           border-radius: 16px;
           overflow: hidden;
           box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
           display: flex;
           flex-direction: column;
           transition: transform 0.2s;
       }

       .task-card:active {
           transform: scale(0.96);
       }

       .task-card img {
           width: 100%;
           height: 100px;
           object-fit: cover;
       }

       .task-card-label {
           padding: 12px 8px;
           text-align: center;
           font-size: 13px;
           font-weight: 600;
           color: var(--font-dark);
       }

       /* News Items */
       .news-list {
           display: flex;
           flex-direction: column;
           gap: 16px;
       }

       .news-item {
           display: flex;
           gap: 16px;
           background: white;
           border: 1px solid rgba(0,0,0,0.04);
           border-radius: 16px;
           padding: 12px;
           align-items: center;
           box-shadow: 0 4px 12px rgba(0,0,0,0.02);
           transition: transform 0.2s;
       }

       .news-item:active {
           transform: scale(0.97);
       }

       .news-thumb {
           width: 80px;
           height: 80px;
           border-radius: 12px;
           object-fit: cover;
       }

       .news-info {
           flex: 1;
           display: flex;
           flex-direction: column;
           justify-content: center;
       }

       .news-title {
           font-size: 15px;
           font-weight: 600;
           color: var(--font-dark);
           line-height: 1.4;
           display: -webkit-box;
           -webkit-line-clamp: 2;
           -webkit-box-orient: vertical;
           overflow: hidden;
       }

       /* Modern Popup Toast */
       .modern-toast {
            position: fixed;
            top: 15%;
            left: 50%;
            transform: translate(-50%, 0);
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            color: white;
            padding: 14px 28px;
            border-radius: 30px;
            text-align: center;
            z-index: 99999;
            font-weight: 500;
            font-size: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            animation: slideDownToast 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
       }

       @keyframes slideDownToast {
           from { opacity: 0; transform: translate(-50%, -20px); }
           to { opacity: 1; transform: translate(-50%, 0); }
       }
       
       /* Modal */
       .modal {
           display: none;
           position: fixed;
           z-index: 9999;
           left: 0;
           top: 0;
           width: 100%;
           height: 100%;
           background-color: rgba(0,0,0,0.6);
           backdrop-filter: blur(4px);
       }

       .modal-content {
           background-color: #fefefe;
           margin: 20vh auto;
           padding: 24px;
           width: 85%;
           max-width: 400px;
           border-radius: 24px;
           box-shadow: 0 24px 48px rgba(0,0,0,0.2);
           text-align: center;
           position: relative;
           animation: zoomIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
       }

       @keyframes zoomIn {
           from { opacity: 0; transform: scale(0.9); }
           to { opacity: 1; transform: scale(1); }
       }

       .modal-logo {
           width: 120px;
           height: 120px;
           margin: -60px auto 16px;
           background: white;
           padding: 10px;
           border-radius: 50%;
           box-shadow: 0 8px 24px rgba(0,0,0,0.1);
           object-fit: contain;
       }

       .modal-text {
           font-size: 15px;
           color: var(--font-muted);
           line-height: 1.6;
           margin-bottom: 24px;
       }

       .modal-close-btn {
           background: var(--brand-green);
           color: white;
           border: none;
           padding: 12px 32px;
           border-radius: 100px;
           font-weight: 600;
           font-size: 15px;
           width: 100%;
       }
    </style>
  </head>
  <body>
    <div class="appCapsule">

      <!-- Hero Slider & Floating Menu -->
      <div class="hero-section">
          <div class="carousel-container">
            <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <img src="img/1.jpg" alt="Banner 1">
                  </div>
                  <div class="carousel-item">
                    <img src="img/3.jpg" alt="Banner 2">
                  </div>
                  <div class="carousel-item">
                    <img src="img/2.jpg" alt="Banner 3">
                  </div>
                </div>
            </div>
          </div>
          
          <div class="floating-right-menu">
              <a href="recharge.php" class="floating-btn"><i class="bi bi-wallet2"></i> <span>Recharge</span></a>
              <a href="withdraw.php" class="floating-btn"><i class="bi bi-cash"></i> <span>Withdraws</span></a>
              <a href="services.php" class="floating-btn"><i class="bi bi-chat-dots-fill"></i> <span>Customer</span></a>
              <a href="invitiation.php" class="floating-btn"><i class="bi bi-person-plus-fill"></i> <span>Invitation</span></a>
              <a href="redeembonus.php" class="floating-btn"><i class="bi bi-gift-fill"></i> <span>Redeem bonus</span></a>
              <a href="reward.php" class="floating-btn"><i class="bi bi-cloud-arrow-down-fill"></i> <span>App Download</span></a>
          </div>
      </div>

      <!-- Notice Bar -->
      <div class="notice-bar">
          <div class="notice-icon"><i class="bi bi-megaphone-fill"></i></div>
          <div class="notice-text">
            <?php 
              $query_setting = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `notice`"));
              $notice_board = $query_setting['message'];
            ?>
            <marquee behavior="scroll" direction="left" scrollamount="4"><?php echo htmlspecialchars($notice_board); ?></marquee>
          </div>
      </div>

      <!-- Quick Actions Grid -->
      <div class="quick-actions-grid">
          <a href="myproducts.php" class="action-item">
              <div class="action-icon icon-green"><i class="bi bi-box2-heart-fill"></i></div>
              <span class="action-label">My Product</span>
          </a>
          <a href="task_details3.php" class="action-item">
              <div class="action-icon icon-blue"><i class="bi bi-card-checklist"></i></div>
              <span class="action-label">Salary</span>
          </a>
          <a href="bindbank.php" class="action-item">
              <div class="action-icon icon-orange"><i class="bi bi-bank2"></i></div>
              <span class="action-label">Bank</span>
          </a>
          <a href="myteams.php" class="action-item">
              <div class="action-icon icon-purple"><i class="bi bi-people-fill"></i></div>
              <span class="action-label">Team</span>
          </a>
      </div>

      <!-- White Background Media Section -->
      <div class="media-section">
          
          <!-- Video -->
          <div class="video-wrapper">
              <video src="img/video.mp4" controls preload="metadata" poster="img/3.jpg"></video>
          </div>
          
          <!-- Tasks -->
          <div class="section-title">Task Bonus</div>
          <div class="task-banners">
              <a href="task-details1.php" class="task-card">
                  <img src="img/669803d7c6c3a.jpg" alt="How to make money">
                  <div class="task-card-label">How to make money?</div>
              </a>
              <a href="task_details2.php" class="task-card">
                  <img src="img/gifts.jpeg" alt="Selfi Reward">
                  <div class="task-card-label">Selfi Reward</div>
              </a>
          </div>

          <!-- News -->
          <div class="section-title">
              Latest News
              <a href="news.php" class="section-title-link">More <i class="bi bi-chevron-right"></i></a>
          </div>
          <div class="news-list">
              <?php 
              $query = mysqli_query($con,"SELECT * FROM blog ORDER BY b_id DESC LIMIT 4");
              if(mysqli_num_rows($query)>0){
                  while($row=mysqli_fetch_array($query)){
                      $blog_id= $row['b_id'];
                      $blog_title = $row['b_title'];
                      $blog_image = $row['b_image'];
              ?> 
              <a href="news_details.php?pac=<?php echo htmlspecialchars($blog_id); ?>" class="news-item">
                  <img src="asupport/blog/<?php echo htmlspecialchars($blog_image); ?>" alt="News" class="news-thumb">
                  <div class="news-info">
                      <div class="news-title"><?php echo htmlspecialchars($blog_title); ?></div>
                  </div>
              </a>
              <?php } } else { ?>
                  <p style="color: var(--font-muted); text-align:center; padding: 20px 0;">No new announcements today.</p>
              <?php } ?>
          </div>

      </div> <!-- end media-section -->
    </div> <!-- end appCapsule -->

    <!-- Hidden Info Modal -->
    <div id="myModal" class="modal">
      <div class="modal-content">
          <img src="img/hikoki_logo.png" class="modal-logo" alt="Logo">
          <p class="modal-text">The client himself, will be able to enhance the grace of the client company. There is no architect to meet with the requirements, it is the very labor of those who praise that the flight of features is most criticized, for those who like easy and apart from flattery</p>
          <button type="button" class="modal-close-btn" onclick="document.getElementById('myModal').style.display='none'">Close</button>
      </div>
    </div>

    <!-- Footer Menu -->
    <?php include "user_menu/footer_menu.php"; ?>

    <!-- Login Toast Logic -->
    <?php
    if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
        $lastPage = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
        if(strpos($lastPage, 'login.php') !== false){
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const popupMessage = document.createElement("div");
                        popupMessage.textContent = "Login Successful!";
                        popupMessage.classList.add("modern-toast");
                        document.body.appendChild(popupMessage);
                        
                        setTimeout(function(){
                            popupMessage.style.opacity = "0";
                            setTimeout(function(){
                                if(popupMessage.parentNode) popupMessage.parentNode.removeChild(popupMessage);
                            }, 500);
                        }, 2500);
                    });
                  </script>';
        }
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Modal Trigger (Currently disabled in original code, can be enabled via logic)
        // $(document).ready(function(){ $('#myModal').css('display', 'block'); });
    </script>
  </body>
</html>