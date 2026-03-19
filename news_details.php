<?PHP 
include('user_menu/database_connect.php');
if(isset($_GET['pac']))
{
  $blog_id = mysqli_real_escape_string($con, $_GET['pac']); // Prevent SQL injection
  $blog_details = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `blog` WHERE `b_id`='$blog_id'"));
  if($blog_details) {
    $blog_title = htmlspecialchars($blog_details['b_title']);
    $blog_image = htmlspecialchars($blog_details['b_image']);
    $blog_des = $blog_details['b_details']; // Preserving HTML content for article body
    $blog_create_date = date('M d, Y', strtotime($blog_details['b_create_date']));
    $b_image2 = htmlspecialchars($blog_details['b_image2']);
  }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>News Details</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       :root {
          --brand-green: #007749;
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
          min-height: 100vh;
          background-color: var(--brand-green);
          padding-bottom: 40px;
       }

       .page-header {
          padding: 16px 16px 12px;
          position: sticky;
          top: 0;
          background: rgba(0, 119, 73, 0.95);
          backdrop-filter: blur(12px);
          -webkit-backdrop-filter: blur(12px);
          z-index: 50;
          display: flex;
          align-items: center;
          justify-content: space-between;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
       }

       .back-btn {
          color: white;
          text-decoration: none;
          display: flex;
          align-items: center;
          gap: 6px;
          font-weight: 500;
          font-size: 15px;
          transition: opacity 0.2s;
       }
       .back-btn:active {
           opacity: 0.7;
       }
       .back-btn i {
           font-size: 18px;
           margin-top: 1px;
       }

       .page-title {
          color: white;
          font-size: 17px;
          font-weight: 600;
          margin: 0;
          position: absolute;
          left: 50%;
          transform: translateX(-50%);
       }

       .header-spacer {
           width: 60px; /* balances out the back button width to keep title perfectly centered */
       }

       .article-container {
          padding: 16px;
       }

       .article-card {
          background: #ffffff;
          border-radius: 24px;
          padding: 24px;
          box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
          overflow: hidden;
       }

       .article-title {
          font-size: 22px;
          font-weight: 700;
          color: var(--text-main);
          margin-bottom: 12px;
          line-height: 1.35;
       }

       .article-meta {
          display: flex;
          align-items: center;
          gap: 8px;
          font-size: 13px;
          color: var(--text-muted);
          font-weight: 500;
          margin-bottom: 24px;
          padding-bottom: 16px;
          border-bottom: 1px solid #f1f5f9;
       }

       .article-meta i {
          color: var(--brand-green);
          font-size: 15px;
       }

       .article-image {
           width: 100%;
           height: auto;
           border-radius: 16px;
           margin-bottom: 24px;
           box-shadow: 0 4px 12px rgba(0,0,0,0.06);
           object-fit: cover;
           background-color: #f8fafc;
       }

       .article-content {
          font-size: 16px;
          line-height: 1.6;
          color: #334155;
       }
       
       .article-content p {
           margin-bottom: 16px;
       }

       .article-content img {
           max-width: 100%;
           border-radius: 12px;
           margin: 16px 0;
           box-shadow: 0 4px 12px rgba(0,0,0,0.06);
       }
    </style>
  </head>
  <body>
    <div class="appCapsule">
       <header class="page-header">
           <a href="javascript:history.back()" class="back-btn">
               <i class="bi bi-chevron-left"></i> Back
           </a>
           <h1 class="page-title">News Details</h1>
           <div class="header-spacer"></div>
       </header>

       <div class="article-container">
         <?php if(isset($blog_details) && $blog_details) { ?>
           <div class="article-card">
               <h2 class="article-title"><?php echo $blog_title; ?></h2>
               <div class="article-meta">
                   <i class="bi bi-calendar-event"></i>
                   <span>Published on <?php echo $blog_create_date; ?></span>
               </div>
               
               <?php if(!empty($blog_image)) { ?>
                   <img src="asupport/blog/<?php echo $blog_image; ?>" class="article-image" alt="Article Image">
               <?php } ?>

               <div class="article-content">
                   <?php echo $blog_des; ?>
               </div>
               
               <?php if(!empty($b_image2) && $b_image2 != 'hikoki_logo.png') { ?>
                   <img src="asupport/blog/<?php echo $b_image2; ?>" class="article-image" style="margin-top: 10px;" alt="Additional Image">
               <?php } ?>
           </div>
         <?php } else { ?>
             <div class="text-center text-white py-5">
                 <h4>Article not found</h4>
                 <p class="mt-3 opacity-75">The news article you are looking for does not exist or has been removed.</p>
                 <a href="javascript:history.back()" class="btn btn-light mt-4 px-4 btn-sm" style="border-radius: 20px;">Back to News</a>
             </div>
         <?php } ?>
       </div>
    </div>

    <!-- Bootstrap scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>