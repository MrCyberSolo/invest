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
								<h4 class="mb-0">Withdrawl Paid List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
								<thead>
                                <tr>
                                        <th>Request ID</th>
										<th>Userid</th>
										<th>Amount</th>
										<th>Admin </th>
										<th>TDS </th>
										<th>Pay Amount </th>
										<th>Trx Id </th>
										<th>Payment Date</th>
									  </tr>
									</thead>
									 <tbody>
                                            <?php 
                                            $i=1;
                                            $query = mysqli_query($con,"select * from income_received  where status='Paid' order by id desc");
                                            if(mysqli_num_rows($query)>0){
                                                while($row=mysqli_fetch_array($query)){
                                                    $id = $row['id'];
                                                    $user_id = $row['userid'];
                                                        $amount = $row['amount'];
                                                    $donation = $row['donation'];
                                                    $tds = $row['tds'];
                                                    $f_amount = $row['f_amount'];
                                                    $trno = $row['trno'];
                                                    $date = $row['date'];
                                                    
                                            ?>
                                    
                                        <tr>
                                            <td><?php echo $id; ?></td>
                                            <td><a href='user_details.php?view=<?php echo $user_id;?>'><?php echo $user_id; ?></a></td>
                                            <td><?php echo $amount; ?></td>
                                            <td><?php echo $donation; ?></td>
                                            <td><?php echo $tds; ?></td>
                                            <td><?php echo $f_amount; ?></td>
                                            <td><?php echo $trno; ?></td>
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