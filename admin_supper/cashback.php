<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include "includes/header.php";  ?>
<!--Data Tables -->
<link href="assets/plugins/datatable/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
	<link href="assets/plugins/datatable/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <!-- Font Awesome Icon Library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
.checked {
  color: orange;
}
</style>    
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
					<!--breadcrumb-->
                    <div class="row">
						
						<div class="col-12 col-lg-4">
							<div class="card radius-15 bg-voilet">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
										  <?php					
        										$pac_amount = 0;
        										$query = mysqli_query($con,"select * from cashback where ca_status='Show' order by ca_id  desc");
        										if(mysqli_num_rows($query)>0)
        										{
        											while($row=mysqli_fetch_array($query))
        											{
        												$id = $row['ca_id'];
        												$pac_amounts = $row['ca_amount'];
        												
        												$pac_amount = $pac_amount + $pac_amounts;
        											}
        										}
        												echo $pac_amount 
        									?>	
                                            <i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class='bx bx-rupee'></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total Paid Income</p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
                        <div class="col-12 col-lg-4">
							<div class="card radius-15 bg-voilet">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">
											  <?php					
        										$pac_amount = 0;
        										$query = mysqli_query($con,"select * from cashback where ca_status='Hiden' order by ca_id  desc");
        										if(mysqli_num_rows($query)>0)
        										{
        											while($row=mysqli_fetch_array($query))
        											{
        												$id = $row['ca_id'];
        												$pac_amounts = $row['ca_amount'];
        												
        												$pac_amount = $pac_amount + $pac_amounts;
        											}
        										}
        												 echo  $pac_amount
        									?>
                                            <i class='bx bxs-down-arrow-alt font-14 text-white'></i> </h2>
										</div>
										<div class="ms-auto font-35 text-white"><i class='bx bx-rupee'></i>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">Total Pending Income </p>
										</div>
										<div class="ms-auto font-14 text-white"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Refer Income List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
								<thead>
                                    <tr>
                                        <th>Sl.No</th>
                                        <th>UserID</th>
                                        <th>Income Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Date</th>
                                    </tr>
								</thead>
								<tbody>
                                     <?php 
                    					$i=1;
                    					$query = mysqli_query($con,"select * from cashback order by ca_id  desc");
                    					if(mysqli_num_rows($query)>0){
                    						while($row=mysqli_fetch_array($query)){
                    							$id = $row['ca_id'];
                    							$user_id = $row['ca_userid'];
                    							$ca_order_id = $row['ca_order_id'];
                    							$reward_type = $row['ca_type'];
                    							$amount = $row['ca_amount'];
                    							$date = $row['ca_date'];
                    							$ca_status = $row['ca_status'];
                    							
                    					?>
                                      <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $user_id; ?></td>
                                        <td><?php echo $reward_type; ?></td>
                                        <td><?php echo $amount; ?></td>
                                        <td><?php echo $ca_status; ?></td>
                                        <td><?php echo $date; ?></td>
                    
                                    </tr>
                                                <?php
                                                $i++;
                                            }
                                        }
                                        else{
                                        ?>
                                            <tr>
                                                <td colspan="4">There are no message yet.</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>    
									</tbody>
								</table>
							</div>
						</div>
					</div>
			<!--end page-content-wrapper-->
					
		</div>
		<!--end page-wrapper-->
		<!--start overlay-->
		<div class="overlay toggle-btn-mobile"></div>
		<!--end overlay-->
		<!--Start Back To Top Button--> <a href="javascript:history.back()" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
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
    <!--Data Tables js-->
	<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready(function () {
			//Default data table
			$('#example').DataTable();
			$('#examples').DataTable();
			$('#exampless').DataTable();
			var table = $('#example2').DataTable({
				lengthChange: false,
				buttons: ['copy', 'excel', 'pdf', 'print', 'colvis']
			});
			table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
		});
	</script>
	<script src="assets/js/app.js"></script>
</body>

</html>
