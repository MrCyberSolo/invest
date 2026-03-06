<?php 
$get = "select * from admin where userid='$userid'";
$run = mysqli_query($con, $get);  
$row_user= mysqli_fetch_array($run);   
    $name = $row_user['name'];
    $email = $row_user['userid'];
?>

<div class="wrapper">
		<!--sidebar-wrapper-->
		<div class="sidebar-wrapper" data-simplebar="true">
			<div class="sidebar-header">
				<div class="">
					<img src="../asupport/<?php echo $query_settings['s_logo']; ?>" width="80%" height="80px" alt="" />
				</div>
				<!-- <div>
					<h6 class="logo-text">Company Name</h6>
				</div> -->
				<a href="javascript:;" class="toggle-btn ms-auto"> <i class="bx bx-menu"></i>
				</a>
			</div>
			<!--navigation-->
			<ul class="metismenu" id="menu">
				<li>
					<a href="dashboard.php">
						<div class="parent-icon icon-color-3"> <i class="bx bx-home-alt"></i>
						</div>
						<div class="menu-title">Dashboard</div>
					</a>
				</li>
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="lni lni-cart"></i>
						</div>
						<div class="menu-title">Order Book</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="order_book.php"><i class="bx bx-right-arrow-alt"></i>Package Order </a>
						<li> <a href="order_level.php"><i class="bx bx-right-arrow-alt"></i>Level Order  </a>					
					<!-- 	<li> <a href="order_spo.php"><i class="bx bx-right-arrow-alt"></i>Finance Order  </a>					
					 --></ul>
				</li>
				
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="bx bx-user-circle"></i>
						</div>
						<div class="menu-title">Customer</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="user.php"><i class="bx bx-right-arrow-alt"></i>Customer List </a></li>
						<li> <a href="user_block.php"><i class="bx bx-right-arrow-alt"></i>Customer Block</a></li>
						<li> <a href="user_search.php"><i class="bx bx-right-arrow-alt"></i>Customer Search</a></li>
						<li> <a href="user_active.php"><i class="bx bx-right-arrow-alt"></i>Customer Active</a></li>
						<li> <a href="user_deactive.php"><i class="bx bx-right-arrow-alt"></i>Customer Deactive</a></li>
						
					</ul>
				</li>
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="fadeIn animated bx bx-basket"></i>
						</div>
						<div class="menu-title">Package</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="package.php"><i class="bx bx-right-arrow-alt"></i>Package List </a></li>
						<li> <a href="package_create.php"><i class="bx bx-right-arrow-alt"></i>Package Add  </a></li>					
						<!--<li> <a href="package_img.php"><i class="bx bx-right-arrow-alt"></i>Package Image Add  </a></li> -->					
					</ul>
				</li>
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="fadeIn animated bx bx-wallet-alt"></i>
						</div>
						<div class="menu-title">Fund</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="wallet.php"><i class="bx bx-right-arrow-alt"></i>Wallet Amount </a></li>
						<li> <a href="fund_recharge.php"><i class="bx bx-right-arrow-alt"></i>Recharge Fund </a></li>
						<li> <a href="fund_current.php"><i class="bx bx-right-arrow-alt"></i>Current Fund </a></li>
						<li> <a href="fund_request.php"><i class="bx bx-right-arrow-alt"></i>Fund Requst </a></li>
						<li> <a href="withdrawal_request.php"><i class="bx bx-right-arrow-alt"></i>Withdrawal Request </a></li>
						<li> <a href="withdrawal_paid.php"><i class="bx bx-right-arrow-alt"></i>Withdrawal Paid </a></li>
						<li> <a href="fund_paid.php"><i class="bx bx-right-arrow-alt"></i>Fund Request Paid </a></li>
						<li> <a href="fund_reject.php"><i class="bx bx-right-arrow-alt"></i>Fund Request Reject </a></li>
						<li> <a href="bonus_add.php"><i class="bx bx-right-arrow-alt"></i>Bonus Add </a></li>
						
					</ul>
				</li>
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="bx bx-line-chart"></i>
						</div>
						<div class="menu-title">Income</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="income_refer.php"><i class="bx bx-right-arrow-alt"></i>Refer Income </a></li>
						<li> <a href="income_daily.php"><i class="bx bx-right-arrow-alt"></i>Daily Income </a></li>
						<li> <a href="income_lebel.php"><i class="bx bx-right-arrow-alt"></i>Daily Level Income </a></li>
						<!-- <li> <a href="income_spo.php"><i class="bx bx-right-arrow-alt"></i>Finance Income </a></li>
						 --><li> <a href="income_coupon.php"><i class="bx bx-right-arrow-alt"></i>Coupon Income </a></li>
						<li> <a href="cashback.php"><i class="bx bx-right-arrow-alt"></i>Bonus Income </a></li>
						<!-- <li> <a href="withdrawl_scrren.php"><i class="bx bx-right-arrow-alt"></i>Screen Short Income </a></li>
						 --><li> <a href="reward_income.php"><i class="bx bx-right-arrow-alt"></i>Reward Income </a></li>
						
					</ul>
				</li>
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="fadeIn animated bx bx-message-detail"></i>
						</div>
						<div class="menu-title">Blog</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="blog.php"><i class="bx bx-right-arrow-alt"></i>Blog View </a></li>
						<li> <a href="blog_create.php"><i class="bx bx-right-arrow-alt"></i>Blog Add  </a></li>
						
					</ul>
				</li>
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="fadeIn animated bx bx-gift"></i>
						</div>
						<div class="menu-title">Coupon</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="coupon.php"><i class="bx bx-right-arrow-alt"></i>Coupon User </a></li> 
						<li> <a href="coupon_all.php"><i class="bx bx-right-arrow-alt"></i>Coupon Public </a></li>
						<!-- <li> <a href="#"><i class="bx bx-right-arrow-alt"></i>Gift Coupon  </a></li>
						 -->
					</ul>
				</li>
				<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon icon-color-10"><i class="fadeIn animated bx bx-layer"></i>
						</div>
						<div class="menu-title">Contact Us</div>
					</a>
					<ul class="mm-collapse">
						<li> <a href="bank.php"><i class="bx bx-right-arrow-alt"></i>Bank Add </a></li>
						<li> <a href="notice.php"><i class="bx bx-right-arrow-alt"></i>Notice Add</a></li>
						<li> <a href="mobile_otp.php"><i class="bx bx-right-arrow-alt"></i>Mobile OTP</a></li>
						<li> <a href="chat_details.php"><i class="bx bx-right-arrow-alt"></i>Chat</a></li>
						
					</ul>
				</li>
				
				<li>
					<a href="includes/logout.php" target="_blank">
						<div class="parent-icon"><i class="bx bx-power-off"></i>
						</div>
						<div class="menu-title">Logout</div>
					</a>
				</li>
			</ul>
			<!--end navigation-->
		</div>
        <!--end sidebar-wrapper-->

<header class="top-header">
    <nav class="navbar navbar-expand">
        <div class="left-topbar d-flex align-items-center">
            <a href="javascript:;" class="toggle-btn">	<i class="bx bx-menu"></i>
            </a>
        </div>
        
        <div class="right-topbar ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item dropdown dropdown-user-profile">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
                        <div class="d-flex user-box align-items-center">
                            <div class="user-info">
                                <p class="user-name mb-0"><?php echo $name; ?></p>
                                <p class="designattion mb-0"><?php echo $email; ?></p>
                            </div>
                            <img src="../asupport/<?php echo $query_settings['s_logo']; ?>" class="user-img" alt="user avatar">
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">	
                        <a class="dropdown-item" href="setting.php"><i
                                class="bx bx-cog"></i><span>Settings</span></a>
                        <a class="dropdown-item" href="dashboard.php"><i
                                class="bx bx-tachometer"></i><span>Dashboard</span></a>
                        <div class="dropdown-divider mb-0"></div>	<a class="dropdown-item" href="includes/logout.php"><i
                                class="bx bx-power-off"></i><span>Logout</span></a>
                    </div>
                </li>
                
            </ul>
        </div>
    </nav>
</header>