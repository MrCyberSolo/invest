<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
$get_user = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$userid_access'"));
$name = $get_user['name'];
$mobile = $get_user['mobile'];
$get_balance = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `income` WHERE `userid`='$userid_access'"));
$current_bal = $get_balance['current_bal'];
$fran_bal = $get_balance['fran_bal'];
date_default_timezone_set('Asia/Kolkata');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <title>Monthly Salary Tasks</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       :root {
          --brand-green: #007749;
          --font-dark: #1e293b;
          --font-muted: #64748b;
          --bg-gray: #f8fafc;
          --accent-gold: #f59e0b;
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
          padding-bottom: env(safe-area-inset-bottom);
       }

       .page-header {
          padding: 16px;
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
          padding-top: max(16px, env(safe-area-inset-top));
       }

       .header-btn {
          color: white;
          text-decoration: none;
          display: flex;
          align-items: center;
          gap: 6px;
          font-weight: 500;
          font-size: 15px;
          transition: opacity 0.2s;
       }
       .header-btn:active {
           opacity: 0.7;
       }
       .header-btn i {
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
           width: 60px;
       }

       .content-wrapper {
           padding: 20px 16px;
       }

       .salary-card {
           background: #ffffff;
           border-radius: 24px;
           overflow: hidden;
           box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
           animation: slideUp 0.4s ease-out;
       }

       @keyframes slideUp {
           from { opacity: 0; transform: translateY(20px); }
           to { opacity: 1; transform: translateY(0); }
       }

       .image-wrapper {
           width: 100%;
           height: auto;
           background: #e2e8f0;
           position: relative;
       }

       .image-wrapper img {
           width: 100%;
           display: block;
           object-fit: cover;
       }

       .salary-content {
           padding: 24px 20px;
       }

       .section-title {
           font-size: 18px;
           font-weight: 700;
           color: var(--font-dark);
           margin-bottom: 20px;
           display: flex;
           align-items: center;
           gap: 8px;
       }

       .section-title i {
           color: var(--accent-gold);
       }

       .salary-tier {
           display: flex;
           gap: 16px;
           padding: 16px;
           background: var(--bg-gray);
           border-radius: 16px;
           margin-bottom: 12px;
           border: 1px solid rgba(0,0,0,0.03);
           transition: transform 0.2s;
       }

       .salary-tier:active {
           transform: scale(0.98);
       }

       .tier-icon {
           width: 48px;
           height: 48px;
           background: rgba(0, 119, 73, 0.1);
           color: var(--brand-green);
           border-radius: 14px;
           display: flex;
           justify-content: center;
           align-items: center;
           font-size: 22px;
           flex-shrink: 0;
       }

       .tier-text {
           display: flex;
           flex-direction: column;
           justify-content: center;
           gap: 4px;
       }

       .tier-text strong {
           color: var(--brand-green);
       }

       .tier-text .reqs {
           font-size: 14.5px;
           font-weight: 600;
           color: var(--font-dark);
           line-height: 1.4;
       }

       .tier-text .reward {
           font-size: 14px;
           font-weight: 600;
           color: var(--accent-gold);
           display: flex;
           align-items: center;
           gap: 6px;
       }

       .reward i {
           font-size: 15px;
       }

    </style>
  </head>
  <body>
    <div class="appCapsule">
       <header class="page-header">
           <a href="javascript:history.back()" class="header-btn">
               <i class="bi bi-chevron-left"></i> Back
           </a>
           <h1 class="page-title">Monthly Salary</h1>
           <div class="header-spacer"></div>
       </header>

       <div class="content-wrapper">
           <div class="salary-card">
               <div class="image-wrapper">
                   <img src="img/13.jpeg" alt="Monthly Salary Overview">
               </div>
               
               <div class="salary-content">
                   <h3 class="section-title"><i class="bi bi-star-fill"></i> Salary Tiers & Rewards</h3>
                   
                   <div class="salary-tier">
                       <div class="tier-icon"><i class="bi bi-people-fill"></i></div>
                       <div class="tier-text">
                           <div class="reqs">Invite <strong>5</strong> B-level + <strong>15</strong> C+D members</div>
                           <div class="reward"><i class="bi bi-cash-stack"></i> 300₹ Personal + 300₹ Team Salary</div>
                       </div>
                   </div>

                   <div class="salary-tier">
                       <div class="tier-icon"><i class="bi bi-people-fill"></i></div>
                       <div class="tier-text">
                           <div class="reqs">Invite <strong>10</strong> B-level + <strong>40</strong> C+D members</div>
                           <div class="reward"><i class="bi bi-cash-stack"></i> 800₹ Personal + 800₹ Team Salary</div>
                       </div>
                   </div>

                   <div class="salary-tier">
                       <div class="tier-icon"><i class="bi bi-people-fill"></i></div>
                       <div class="tier-text">
                           <div class="reqs">Invite <strong>20</strong> B-level + <strong>80</strong> C+D members</div>
                           <div class="reward"><i class="bi bi-cash-stack"></i> 1500₹ Personal + 1500₹ Team Salary</div>
                       </div>
                   </div>

                   <div class="salary-tier">
                       <div class="tier-icon"><i class="bi bi-people-fill"></i></div>
                       <div class="tier-text">
                           <div class="reqs">Invite <strong>30</strong> B-level + <strong>250</strong> C+D members</div>
                           <div class="reward"><i class="bi bi-cash-stack"></i> 2500₹ Personal + 2500₹ Team Salary</div>
                       </div>
                   </div>

                   <div class="salary-tier">
                       <div class="tier-icon"><i class="bi bi-people-fill"></i></div>
                       <div class="tier-text">
                           <div class="reqs">Invite <strong>40</strong> B-level + <strong>500</strong> C+D members</div>
                           <div class="reward"><i class="bi bi-cash-stack"></i> 3500₹ Personal + 3500₹ Team Salary</div>
                       </div>
                   </div>

                   <div class="salary-tier">
                       <div class="tier-icon"><i class="bi bi-award-fill"></i></div>
                       <div class="tier-text">
                           <div class="reqs">Invite <strong>50</strong> B-level + <strong>1000</strong> C+D members</div>
                           <div class="reward"><i class="bi bi-cash-stack"></i> 5000₹ Personal + 5000₹ Team Salary</div>
                       </div>
                   </div>

               </div>
           </div>
       </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
