<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php include "includes/header.php";  ?>
</head>

<body>
	<!-- wrapper -->
	
		
		<!--header-->
		<?php include "includes/header_menu.php";  ?>
		<!--end header-->
		<!--page-wrapper-->
		<div class="page-wrapper">
			<!--page-content-wrapper-->
			<div class="page-content-wrapper">
				<div class="page-content">
					<div class="row">
						
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-voilet">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php  echo mysqli_num_rows(mysqli_query($con, "select * from user")); ?>
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class='lni  lni-network'></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total User</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-voilet">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php  echo mysqli_num_rows(mysqli_query($con, "select * from user where status='Active'")); ?>
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class='lni  lni-network'></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total Active</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-voilet">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php  echo mysqli_num_rows(mysqli_query($con, "select * from user where status=''")); ?>
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class='lni  lni-network'></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total Deactive</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
													$order_book_total = 0;
													$query_order = mysqli_query($con,"select * from order_book where o_user_type='User' order by o_id desc");
													if(mysqli_num_rows($query_order)>0)
													{
														while($row_order=mysqli_fetch_array($query_order))
														{
															$o_id = $row_order['o_id'];
															$o_amount = $row_order['o_amount'];
															
															$order_book_total = $order_book_total + $o_amount;
														}
													}
															echo $order_book_total;
												?>	
											 </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="lni lni-cart"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Order Book</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												$fund_approval = 0;
												$query = mysqli_query($con,"select * from fpay  order by id desc");
												if(mysqli_num_rows($query)>0)
												{
													while($row=mysqli_fetch_array($query))
													{
														$fund_id = $row['id'];
														$fund_amount = $row['amount'];
														$fund_approval = $fund_approval + $fund_amount;
													}
												}
												echo $fund_approval;														
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-wallet-alt"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Amount Transfer</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												$int_amount = 0;
												$query = mysqli_query($con,"select * from interest order by int_id desc");
												if(mysqli_num_rows($query)>0)
												{
													while($row=mysqli_fetch_array($query))
													{
														$int_id = $row['int_id'];
														$int_amounts = $row['int_amount'];
														
														$int_amount = $int_amount + $int_amounts;
													}
												}
														echo number_format($int_amount, 2);
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-wallet-alt"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Daily Income</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												$int_level_amount = 0;
												$query = mysqli_query($con,"select * from interest_label order by int_id desc");
												if(mysqli_num_rows($query)>0)
												{
													while($row=mysqli_fetch_array($query))
													{
														$int_id = $row['int_id'];
														$int_amounts = $row['int_amount'];
														
														$int_level_amount = $int_level_amount + $int_amounts;
													}
												}
														echo number_format($int_level_amount, 2);
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-wallet-alt"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Daily Level Income</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												$refer_income = 0;
												$query = mysqli_query($con,"select * from income_tras order by id desc");
												if(mysqli_num_rows($query)>0)
												{
													while($row=mysqli_fetch_array($query))
													{
														$refer_id = $row['id'];
														$refer_amount = $row['amount'];
														
														$refer_income = $refer_income + $refer_amount;
													}
												}
													echo $refer_income;
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-wallet-alt"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Refer Income</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												$with_paid = 0;
												$query = mysqli_query($con,"select * from income_received where status='Paid' order by id desc");
												if(mysqli_num_rows($query)>0)
												{
													while($row=mysqli_fetch_array($query))
													{
														$refer_amount = $row['amount'];
														$with_paid = $with_paid + $refer_amount;
													}
												}
													echo $with_paid;
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-wallet-alt"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total Withdrawl Paid</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
							<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												$with_paid = 0;
												$query = mysqli_query($con,"select * from income_received where status='Pending' order by id desc");
												if(mysqli_num_rows($query)>0)
												{
													while($row=mysqli_fetch_array($query))
													{
														$refer_amount = $row['amount'];
														$with_paid = $with_paid + $refer_amount;
													}
												}
													echo $with_paid;
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-wallet-alt"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total Withdrawl Pending</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
					<!--	<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											5000		
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-comment-dots"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total SMS</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>		
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												echo $toal_sms =50 + $otp =  mysqli_num_rows(mysqli_query($con, "select * from mobileotp")); 
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-comment-dots"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total Used SMS</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>	-->	
						<div class="col-12 col-lg-3">
							<div class="card radius-15 bg-primary-blue">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											<?php					
												$coupon_income = 0;
												$query = mysqli_query($con,"select * from coupan_tra order by ct_id desc");
												if(mysqli_num_rows($query)>0)
												{
													while($row=mysqli_fetch_array($query))
													{
														$ct_id = $row['ct_id'];
														$ct_amount = $row['ct_amount'];
														
														$coupon_income = $coupon_income + $ct_amount;
													}
												}
														echo $coupon_income;
											?>	
											<i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class="fadeIn animated bx bx-gift"></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Coupon Income</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>				

					</div>
					<!--end row-->
				</div>
			</div>
			<!--end page-content-wrapper-->
		</div>
		<!--end page-wrapper-->
		<!--start overlay-->
		<div class="overlay toggle-btn-mobile"></div>
		<!--end overlay-->
		<!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->
		<!--footer -->
		<div class="footer">
		</div>
		<!-- end footer -->
	</div>
	
	<!-- JavaScript -->
	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<!-- Vector map JavaScript -->
	<script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-in-mill.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-us-aea-en.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-uk-mill-en.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-au-mill.js"></script>
	<script src="assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
	<script src="assets/js/index2.js"></script>
	<!-- App JS -->
	<script src="assets/js/app.js"></script>
		<script>
        //Pass the id of the <input> element to be copied as a parameter to the copy()
        let copy = (textId) => {
          //Selects the text in the <input> elemet
          document.getElementById(textId).select();
          //Copies the selected text to clipboard
          document.execCommand("copy");
        };
      </script>
</body>

</html>