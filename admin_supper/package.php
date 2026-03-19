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
								<h4 class="mb-0">Package List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
								<thead>
                                            <tr>
												<th width="20%">Sl No</th>
												<th width="20%">Type</th>
												<th width="30%">Name </th>
												<th width="30%">Amount </th>
												<th width="10%">Days</th>
												<th width="10%">ROI</th>
												<th width="10%">Bonus</th>
												<th width="10%">Limit</th>
												<th width="20%">Status</th>
												<th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
							$i=1;
							$query = mysqli_query($con,"select * from package order by pa_id desc");
							if(mysqli_num_rows($query)>0){
								while($row=mysqli_fetch_array($query)){
									$id = $row['pa_id'];
								
									$pa_name = $row['pa_name'];
									$pa_amount = $row['pa_amount'];
									$pa_day = $row['pa_day'];
									$pa_number = $row['pa_number'];
									$pa_sponser = $row['pa_sponser'];
									$status = $row['status'];
									$pa_com_amount = $row['pa_com_amount'];
									$pa_cash = $row['pa_cash'];
									$pa_type = $row['pa_type'];
								?>
                                    <tr class="odd gradeX">
                                        <td><a href="package_edit.php?pac=<?php echo $id; ?>"><?php echo $id; ?></a></td>
                                        <td><?php echo $pa_type; ?></td>
                                        <td><?php echo $pa_name; ?></td>
                                        <td><?php echo $pa_amount; ?></td>
                                         <td><?php echo $pa_day; ?></td>
                                         <td><?php echo $pa_com_amount; ?></td>
                                         <td><?php echo $pa_cash; ?></td>
                                         <td><?php echo $pa_sponser; ?></td>
                                         <td><?php echo $status; ?></td>
										 <?php if($status=='Active'){ ?>
                                        <td><form action="package.php?pay_id=<?php echo $id ?>" method="post"><input type="submit" name="deactive" class="btn btn-danger" value="Deactive"></form></td>
                                        <?php } else{ ?>
										<td><form action="package.php?pay_id_active=<?php echo $id ?>" method="post"><input type="submit" name="active" class="btn btn-info" value="Active"></form></td>
                                      	<?php } ?>
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
<?php if(isset($_GET['pay_id']))
{	
    $update_id = $_GET['pay_id'];
    $query = mysqli_query($con,"update package set status='Deactive' where pa_id='$update_id'");
    echo '<script>alert("Package Deactive!");window.location.assign("package.php");</script>';
}
?>
<?php if(isset($_GET['pay_id_active']))
{	
    $update_id = $_GET['pay_id_active'];
    $query = mysqli_query($con,"update package set status='Active' where pa_id='$update_id'");
    echo '<script>alert("Package Active!");window.location.assign("package.php");</script>';
}
?>