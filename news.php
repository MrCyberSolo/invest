<?php 
include('user_menu/database_connect.php');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>News</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       :root {
          --brand-green: #007749;
          --brand-dark: #005a36;
          --text-main: #1e293b;
          --text-muted: #64748b;
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
          padding-bottom: 90px;
          min-height: 100vh;
          background-color: var(--brand-green);
       }

       .page-header {
          padding: 24px 16px 16px;
          position: sticky;
          top: 0;
          background: rgba(0, 119, 73, 0.95);
          backdrop-filter: blur(10px);
          -webkit-backdrop-filter: blur(10px);
          z-index: 50;
       }

       .page-title {
          color: white;
          font-size: 20px;
          font-weight: 700;
          margin: 0;
          letter-spacing: 0.3px;
          text-align: center;
       }

       .news-list {
          padding: 8px 16px;
          display: flex;
          flex-direction: column;
          gap: 16px;
       }

       .news-card {
          background: #ffffff;
          border-radius: 20px;
          padding: 16px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          text-decoration: none;
          box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
          transition: transform 0.2s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.2s ease;
          -webkit-tap-highlight-color: transparent;
       }

       .news-card:active {
          transform: scale(0.97);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
       }

       .news-content {
          flex: 1;
          padding-right: 16px;
       }

       .news-title {
          font-size: 16px;
          font-weight: 600;
          color: var(--text-main);
          margin-bottom: 8px;
          line-height: 1.4;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
       }

       .news-date {
          font-size: 13px;
          font-weight: 500;
          color: var(--text-muted);
          display: flex;
          align-items: center;
          gap: 6px;
       }

       .news-image {
          width: 84px;
          height: 84px;
          border-radius: 16px;
          object-fit: cover;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
          background-color: #f8fafc;
       }

       .end-of-list {
          text-align: center;
          padding: 24px 0;
          color: rgba(255, 255, 255, 0.7);
          font-size: 14px;
          font-weight: 500;
       }
    </style>
  </head>
  <body>
    <div class="appCapsule">
       <header class="page-header">
         <h1 class="page-title">News</h1>
       </header>

       <div class="news-list">
         <?php 
            $query = mysqli_query($con,"SELECT * FROM blog ORDER BY b_id DESC");
            if(mysqli_num_rows($query) > 0){
                while($row = mysqli_fetch_array($query)){
                    $blog_id = $row['b_id'];
                    $blog_title = htmlspecialchars($row['b_title']);
                    $blog_image = htmlspecialchars($row['b_image']);
                    $blog_create_date = date('Y-m-d', strtotime($row['b_create_date']));
         ?>
         <a href="news_details.php?pac=<?php echo urlencode($blog_id); ?>" class="news-card">
            <div class="news-content">
               <h3 class="news-title"><?php echo $blog_title; ?></h3>
               <div class="news-date">
                  <i class="bi bi-calendar3"></i> <?php echo $blog_create_date; ?>
               </div>
            </div>
            <img src="asupport/blog/<?php echo $blog_image; ?>" class="news-image" alt="News Image">
         </a>
         <?php } } else { ?>
             <div class="text-center text-white py-4">
                 <p>No news available at the moment.</p>
             </div>
         <?php } ?>
       </div>

       <div class="end-of-list">
          <p>No more</p>
       </div>
    </div>

    <!-- Footer Start Here -->
    <?php include "user_menu/footer_menu.php"; ?>
    <!-- Footer End Here -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>