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
											<th>Order ID</th>
											<th>Package ID</th>
											<th>Userid</th>
											<th>Amount</th>
											<th>Daily</th>
											<th>Status </th>
											<th>Payment Date</th>
											<th>Action</th>
											<th>Access</th>
										
										</tr>
									</thead>
									<tbody>
                                    <?php 
                                        $i=1;
                                        $query = mysqli_query($con,"select * from order_book order by o_id desc");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                                $o_id = $row['o_id'];
                                                $user_id = $row['o_userid'];
                                                $o_package = $row['o_pac_id'];
                                                $o_amount = $row['o_amount'];
                                                $o_pay_status = $row['o_status'];
                                               	$o_date = $row['o_date'];
                                               	$o_percentage = $row['o_percentage'];
                                         ?>
                                    <tr>
                                        <td><a href="#"><?php echo $o_id; ?></a></td>
                                        <td><a href="#"><?php echo $o_package; ?></a></td>
                                        <td><a href="#"><?php echo $user_id; ?></a></td>
                                        <td><a href="#"><?php echo $o_amount; ?></a></td>
                                        <td><a href="#"><?php echo $o_percentage; ?></a></td>
                                       
                                        <td><?php echo $o_pay_status; ?></td>
                                        <td><?php echo $o_date; ?></td>
                                        <td><a href="interest_paid.php?pac=<?php echo $o_id; ?>" class="btn btn-primary"><i class="fa fa-fw fa-lg fa-check-circle"></i> View</a></td>
										<td><?php if($o_pay_status=='Credit'){ ?>
												<form action="?order_id=<?php echo $o_id ?>" method="post"><input type="submit" name="deactive" class="btn btn-danger" value="Deactive"></form> 
											<?php }else{?>
												<form action="?order_id_active=<?php echo $o_id ?>" method="post"><input type="submit" name="Active" class="btn btn-info" value="Active"></form>	
											<?php } ?></td>
									</tr>
                                    <?php
                                        $i++;
                                            }
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
<?php if(isset($_GET['order_id']))
{	
    $update_id = $_GET['order_id'];
    $query = mysqli_query($con,"update order_book set o_status='Deactive' where o_id='$update_id'");
    echo '<script>alert("Order Deactive!");window.location.assign("order_book.php","_self");</script>';
}
?>
<?php if(isset($_GET['order_id_active']))
{	
    $update_id = $_GET['order_id_active'];
    $query = mysqli_query($con,"update order_book set o_status='Credit' where o_id='$update_id'");
    echo '<script>alert("Order Active!");window.location.assign("order_book.php","_self");</script>';
}
?>