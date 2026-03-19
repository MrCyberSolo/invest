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
    
    //label count
    $DB = $con;
    if($DB->connect_error) {
        die("Connection failed: " . $DB->connect_error);
    }
    function getParent($parent_id)
    {
        global $DB;
        $query = $DB->query("SELECT * FROM user WHERE email = $parent_id");
        $result = $query->fetch_object();
        $data = (!$result) ? null : $result;
        $query->free();
        return $data;
    }
    function getChildren($parent_id)
    {
        global $DB;
        $query = $DB->query("SELECT * FROM user WHERE under_userid = $parent_id ORDER BY user_id");
        $arr = [];
        while($row = $query->fetch_object()) {
            $arr[] = $row;
        }        
        $data = (count($arr) < 1) ? [] : $arr;        
        $query->free();        
        return $data;
    }
    function getLevels($parent_id)
    {
        $level_1 = getChildren($parent_id);
        $level_2 = [];
        $level_3 = [];
        $level_4 = [];
        $level_5 = [];	
        if(count($level_1) > 0) {
            foreach($level_1 as $level1) {
                $level_2_data = getChildren($level1->email);
                if(count($level_2_data) > 0) {
                    foreach($level_2_data as $data) {
                        $level_2[] = $data;
                    }
                }
            }
        }
        if(count($level_2) > 0) {
            foreach($level_2 as $level2) {
                $level_3_data = getChildren($level2->email);
                if(count($level_3_data) > 0) {
                    foreach($level_3_data as $data) {
                        $level_3[] = $data;
                    }
                }
            }
        }
        if(count($level_3) > 0) {
            foreach($level_3 as $level3) {
                $level_4_data = getChildren($level3->email);
                if(count($level_4_data) > 0) {
                    foreach($level_4_data as $data) {
                        $level_4[] = $data;
                    }
                }
            }
        }
        return [
            ['name' => 'Level 1', 'data' => $level_1],
            ['name' => 'Level 2', 'data' => $level_2],
            ['name' => 'Level 3', 'data' => $level_3]
        ];
    }
    $parent = getParent($userid_access);
    if($parent == null) {
        die('Parent user not found.');
    }
    $levels = getLevels($parent->email);    
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <title>Profile</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       :root {
          --brand-green: #007749;
          --brand-dark: #005a36;
          --font-dark: #1e293b;
          --font-muted: #64748b;
          --bg-gray: #f8fafc;
          --accent-blue: #0ea5e9;
          --accent-gold: #f59e0b;
          --card-radius: 24px;
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
          overflow-x: hidden;
       }

       /* Profile Header */
       .profile-header {
           padding: max(24px, env(safe-area-inset-top)) 24px 24px;
           display: flex;
           justify-content: space-between;
           align-items: center;
           position: relative;
           z-index: 10;
       }

       .user-profile {
           display: flex;
           align-items: center;
           gap: 16px;
       }

       .avatar {
           width: 60px;
           height: 60px;
           border-radius: 50%;
           border: 3px solid rgba(255,255,255,0.8);
           background: white;
           padding: 6px;
           object-fit: contain;
           box-shadow: 0 8px 16px rgba(0,0,0,0.1);
       }

       .user-info h5 {
           color: white;
           font-weight: 700;
           font-size: 20px;
           margin-bottom: 2px;
       }

       .user-info p {
           color: rgba(255,255,255,0.85);
           font-size: 13px;
           font-weight: 500;
           background: rgba(0,0,0,0.15);
           padding: 2px 10px;
           border-radius: 100px;
           display: inline-block;
       }

       .settings-icon {
           color: white;
           font-size: 24px;
           width: 44px;
           height: 44px;
           display: flex;
           justify-content: center;
           align-items: center;
           background: rgba(255,255,255,0.15);
           border-radius: 50%;
           backdrop-filter: blur(8px);
           transition: transform 0.2s;
       }

       .settings-icon:active {
           transform: rotate(45deg);
       }

       /* Main Sheet */
       .main-sheet {
           background: #ffffff;
           border-radius: 36px 36px 0 0;
           min-height: calc(100vh - 120px);
           padding: 24px 20px;
           box-shadow: 0 -8px 24px rgba(0,0,0,0.1);
           position: relative;
           z-index: 5;
       }

       /* Balance Overview */
       .balance-grid {
           display: grid;
           grid-template-columns: 1fr 1fr;
           gap: 12px;
           margin-bottom: 24px;
           margin-top: -30px; 
           position: relative;
           z-index: 20;
       }

       .bal-card {
           border-radius: 20px;
           padding: 16px;
           display: flex;
           flex-direction: column;
           gap: 4px;
           box-shadow: 0 10px 20px rgba(0,0,0,0.15);
           position: relative;
           overflow: hidden;
       }

       .primary-card {
           background: linear-gradient(135deg, #1e293b, #334155);
       }

       .secondary-card {
           background: linear-gradient(135deg, var(--brand-green), #059669);
       }

       .bal-card::after {
           content: '';
           position: absolute;
           right: -20px;
           bottom: -20px;
           width: 80px;
           height: 80px;
           background: rgba(255,255,255,0.06);
           border-radius: 50%;
       }

       .card-top {
           display: flex;
           align-items: center;
           gap: 6px;
           position: relative;
           z-index: 2;
       }

       .card-top i {
           font-size: 15px;
           color: rgba(255,255,255,0.85);
       }

       .bal-label {
           color: rgba(255,255,255,0.85);
           font-size: 13px;
           font-weight: 500;
       }

       .bal-amount {
           color: #ffffff;
           font-size: 21px;
           font-weight: 800;
           letter-spacing: -0.5px;
           position: relative;
           z-index: 2;
           padding-top: 4px;
       }

       /* Action Buttons */
       .action-buttons {
           display: flex;
           gap: 12px;
           margin-bottom: 28px;
       }

       .btn-action {
           flex: 1;
           display: flex;
           justify-content: center;
           align-items: center;
           gap: 8px;
           padding: 14px 0;
           border-radius: 16px;
           font-weight: 600;
           font-size: 15px;
           color: white;
           transition: transform 0.2s, background 0.2s;
       }

       .btn-action:active {
           transform: scale(0.96);
       }

       .btn-deposit {
           background: var(--brand-green);
           box-shadow: 0 8px 16px rgba(0,119,73,0.2);
       }
       .btn-deposit:hover { background: var(--brand-dark); }

       .btn-withdraw {
           background: #1e293b;
           box-shadow: 0 8px 16px rgba(30,41,59,0.2);
       }
       .btn-withdraw:hover { background: #0f172a; }

       .btn-action i {
           font-size: 18px;
       }

       /* Stats Grid */
       .stats-panel {
           background: var(--bg-gray);
           border-radius: var(--card-radius);
           padding: 20px;
           display: grid;
           grid-template-columns: repeat(3, 1fr);
           gap: 16px;
           margin-bottom: 24px;
           border: 1px solid rgba(0,0,0,0.03);
       }

       .stat-item {
           text-align: center;
           display: flex;
           flex-direction: column;
           gap: 4px;
       }

       .stat-val {
           color: var(--font-dark);
           font-size: 15px;
           font-weight: 700;
       }

       .stat-label {
           color: var(--font-muted);
           font-size: 12px;
           font-weight: 500;
       }

       /* Team Commission */
       .team-panel {
           background: #ffffff;
           border: 1px solid rgba(0,0,0,0.06);
           border-radius: var(--card-radius);
           padding: 20px;
           margin-bottom: 28px;
           box-shadow: 0 4px 12px rgba(0,0,0,0.02);
       }

       .team-header {
           display: flex;
           justify-content: space-between;
           align-items: center;
           margin-bottom: 16px;
       }

       .team-header h3 {
           font-size: 16px;
           font-weight: 700;
           color: var(--font-dark);
       }

       .team-link {
           font-size: 13px;
           font-weight: 600;
           color: var(--accent-blue);
           display: flex;
           align-items: center;
           gap: 4px;
       }

       .team-stats {
           display: flex;
           justify-content: space-between;
           gap: 10px;
       }

       .team-box {
           flex: 1;
           background: var(--bg-gray);
           padding: 12px 0;
           border-radius: 12px;
           text-align: center;
           display: flex;
           flex-direction: column;
           gap: 4px;
       }

       .t-val {
           font-size: 16px;
           font-weight: 700;
           color: var(--brand-green);
       }

       .t-label {
           font-size: 12px;
           font-weight: 600;
           color: var(--font-muted);
       }

       /* Menu List */
       .menu-list {
           display: flex;
           flex-direction: column;
           gap: 8px;
       }

       .menu-item {
           display: flex;
           align-items: center;
           background: #ffffff;
           padding: 16px 20px;
           border-radius: 16px;
           color: var(--font-dark);
           font-weight: 600;
           font-size: 15px;
           text-decoration: none;
           border: 1px solid rgba(0,0,0,0.04);
           transition: background 0.2s, transform 0.2s;
       }

       .menu-item:active {
           background: var(--bg-gray);
           transform: scale(0.98);
       }

       .m-icon {
           width: 32px;
           height: 32px;
           border-radius: 8px;
           display: flex;
           justify-content: center;
           align-items: center;
           margin-right: 16px;
           font-size: 16px;
       }

       .bg-green { background: rgba(0, 119, 73, 0.1); color: var(--brand-green); }
       .bg-blue { background: rgba(14, 165, 233, 0.1); color: var(--accent-blue); }
       .bg-orange { background: rgba(245, 158, 11, 0.1); color: var(--accent-gold); }
       .bg-purple { background: rgba(147, 51, 234, 0.1); color: #9333ea; }
       .bg-red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

       .m-arrow {
           margin-left: auto;
           color: #cbd5e1;
           font-size: 18px;
       }

       /* Floating Customer Service */
       .floating-cs a {
           position: fixed;
           right: 16px;
           bottom: 100px;
           background-color: var(--brand-green);
           color: white;
           width: 56px;
           height: 56px;
           border-radius: 50%;
           display: flex;
           align-items: center;
           justify-content: center;
           font-size: 24px;
           box-shadow: 0 8px 24px rgba(0,119,73,0.3);
           z-index: 1000;
           transition: transform 0.2s;
       }
       .floating-cs a:active { transform: scale(0.9); }

       /* Toast Configuration */
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
    </style>
  </head>
  <body>
    <!-- Top Header -->
    <header class="profile-header">
        <div class="user-profile">
            <img src="img/hikoki_logo.png" class="avatar" alt="Avatar">
            <div class="user-info">
                <h5><?php echo htmlspecialchars($mobile); ?></h5>
                <p>ID: <?php echo htmlspecialchars($userid_access); ?></p>
            </div>
        </div>
        <a href="security.php" class="settings-icon">
            <i class="bi bi-gear-fill"></i>
        </a>
    </header>

    <div class="appCapsule">
        
        <!-- Floating CS -->
        <div class="floating-cs">
            <a href="services.php"><i class="bi bi-headset"></i></a>
        </div>

        <div class="main-sheet">
            <!-- Balances -->
            <div class="balance-grid">
                <div class="bal-card primary-card">
                    <div class="card-top">
                        <i class="bi bi-wallet2"></i>
                        <span class="bal-label">Balance</span>
                    </div>
                    <span class="bal-amount">₹<?php echo number_format((float)$current_bal, 2); ?></span>
                </div>
                <div class="bal-card secondary-card">
                    <div class="card-top">
                        <i class="bi bi-safe"></i>
                        <span class="bal-label">Recharge</span>
                    </div>
                    <span class="bal-amount">₹<?php echo number_format((float)$fran_bal, 2); ?></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="recharge.php" class="btn-action btn-deposit">
                    <i class="bi bi-wallet2"></i> Recharge
                </a>
                <a href="withdraw.php" class="btn-action btn-withdraw">
                    <i class="bi bi-cash-stack"></i> Withdraw
                </a>
            </div>

            <!-- Income Stats -->
            <div class="stats-panel">
                <div class="stat-item">
                    <span class="stat-val">₹<?php	
                        $interest_label = 0;
                        $query = mysqli_query($con,"SELECT * FROM transaction WHERE t_userid='$userid_access' AND t_type='Credit' AND t_details!='Account Recharge'");
                        if(mysqli_num_rows($query)>0) {
                            while($row=mysqli_fetch_array($query)) {
                                $interest_label = $interest_label + $row['t_amount'];
                            }
                        }
                        echo number_format((float)$interest_label, 2);
                    ?></span>
                    <span class="stat-label">Total profit</span>
                </div>
                
                <div class="stat-item">
                    <span class="stat-val">₹<?php	
                        $interest_label = 0;
                        $query = mysqli_query($con,"SELECT * FROM interest_label WHERE int_userid='$userid_access'");
                        if(mysqli_num_rows($query)>0) {
                            while($row=mysqli_fetch_array($query)) {
                                $interest_label = $interest_label + $row['int_amount'];
                            }
                        }
                        echo number_format((float)$interest_label, 2);
                    ?></span>
                    <span class="stat-label">Team inc.</span>
                </div>

                <div class="stat-item">
                    <span class="stat-val">₹<?php	
                        $today_date = date('Y-m-d');			
                        $interest_label = 0;
                        $query = mysqli_query($con,"SELECT * FROM transaction WHERE t_userid='$userid_access' AND t_type='Credit' AND t_details!='Account Recharge' AND t_date='$today_date'");
                        if(mysqli_num_rows($query)>0) {
                            while($row=mysqli_fetch_array($query)) {
                                $interest_label = $interest_label + $row['t_amount'];
                            }
                        }
                        echo number_format((float)$interest_label, 2);
                    ?></span>
                    <span class="stat-label">Today's inc.</span>
                </div>

                <div class="stat-item">
                    <span class="stat-val">₹0.00</span>
                    <span class="stat-label">Total loss</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">₹0.00</span>
                    <span class="stat-label">Today loss</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val">₹0.00</span>
                    <span class="stat-label">Product</span>
                </div>
            </div>

            <!-- Team Commission Overview -->
            <div class="team-panel">
                <div class="team-header">
                    <h3>My Team</h3>
                    <a href="commission-record.php" class="team-link">Details <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="team-stats">
                    <div class="team-box">
                        <span class="t-val"><?php echo $direct_refer = mysqli_num_rows(mysqli_query($con, "SELECT * FROM user WHERE under_userid='$userid_access'")); ?></span>
                        <span class="t-label">B-10%</span>
                    </div>
                    <div class="team-box">
                        <span class="t-val">
                            <?php 
                            $total_count=0;
                            $c_count=0;
                            foreach($levels as $level) {
                                $firstValue = array_shift($level);
                                if($firstValue=='Level 2'){
                                    $c_count = count($level['data']); 
                                }
                            }
                            echo $c_count;
                            ?>
                        </span>
                        <span class="t-label">C-5%</span>
                    </div>
                    <div class="team-box">
                        <span class="t-val">
                            <?php 
                            $d_count=0;
                            foreach($levels as $level) {
                                $firstValue = array_shift($level);
                                if($firstValue=='Level 3'){
                                    $d_count = count($level['data']); 
                                }
                            }
                            echo $d_count;
                            ?>
                        </span>
                        <span class="t-label">D-2%</span>
                    </div>
                </div>
            </div>

            <!-- Modern Menu List -->
            <div class="menu-list">
                <a href="myproducts.php" class="menu-item">
                    <div class="m-icon bg-green"><i class="bi bi-grid-fill"></i></div>
                    My product
                    <i class="bi bi-chevron-right m-arrow"></i>
                </a>
                <a href="mycoupon.php" class="menu-item">
                    <div class="m-icon bg-blue"><i class="bi bi-ticket-perforated-fill"></i></div>
                    Coupon
                    <i class="bi bi-chevron-right m-arrow"></i>
                </a>
                <a href="funding-details.php" class="menu-item">
                    <div class="m-icon bg-orange"><i class="bi bi-receipt"></i></div>
                    Funding details
                    <i class="bi bi-chevron-right m-arrow"></i>
                </a>
                <a href="redeembonus.php" class="menu-item">
                    <div class="m-icon bg-purple"><i class="bi bi-gift-fill"></i></div>
                    Redeem bonus
                    <i class="bi bi-chevron-right m-arrow"></i>
                </a>
                <a href="invitiation.php" class="menu-item">
                    <div class="m-icon bg-red"><i class="bi bi-people-fill"></i></div>
                    Invitation
                    <i class="bi bi-chevron-right m-arrow"></i>
                </a>
            </div>

        </div> <!-- end main-sheet -->

        <!-- Footer Start Here -->
        <?php include "user_menu/footer_menu.php";  ?>
        <!-- Footer End Here -->

    </div> <!-- end appCapsule -->

    <!-- Login Toast Logic -->
    <?php
    if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
        $lastPage = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
        
        $message = "";
        if(strpos($lastPage, 'signup_access.php') !== false){
            $message = "Register Success!";
        } else if(strpos($lastPage, 'login.php') !== false){
            $message = "Login Success!";
        }

        if(!empty($message)) {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const popupMessage = document.createElement("div");
                        popupMessage.textContent = "'.$message.'";
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>