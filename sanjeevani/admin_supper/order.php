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
				
					<!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Order Book List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Order id</th>
											<th>Product Name</th>
											<th>Price</th>
											<th>Date</th>
											<th>Status</th>
										
										</tr>
									</thead>
									<tbody>
                                        <?php 
										$i=1;
										$query = mysqli_query($con,"select * from order_book order by o_id desc");
										if(mysqli_num_rows($query)>0){
											while($row=mysqli_fetch_array($query)){
												$o_id = $row['o_id'];
												$o_c_id = $row['o_c_id'];
												$o_price = $row['o_price'];
												$o_discount = $row['o_discount'];
												$o_pay = $row['o_pay'];
												$o_status = $row['o_status'];
												$o_create = $row['o_create'];
                                                $query_course = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `package` WHERE p_id='$o_c_id'"));
			                                    $p_name = $query_course['p_name'];
										?>
										<tr>
											
											<td><?php echo $o_id; ?></td>
											<td><?php echo $p_name; ?></td>
											<td>₹ <?php echo $o_pay; ?></td>
											<td><?php echo $o_create; ?></td>
											<td>
                                            <?php if($o_status=='Success'){?>    
                                                <a href="invoice.php?o_id=<?php echo $o_id; ?>" class="btn btn-sm btn-light-success btn-block radius-30"><?php echo $o_status; ?></a>
                                            <?php }elseif($o_status=='Failed'){ ?>
                                                <a href="invoice.php?o_id=<?php echo $o_id; ?>" class="btn btn-sm btn-light-danger btn-block radius-30"><?php echo $o_status; ?></a>
                                            <?php }else{ ?>
                                                <a href="invoice.php?o_id=<?php echo $o_id; ?>" class="btn btn-sm btn-light-warning btn-block radius-30"><?php echo $o_status; ?></a>
                                            <?php }?> 
											</td>
										</tr>
										<?php } } ?>
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
