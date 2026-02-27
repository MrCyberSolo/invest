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
								<h4 class="mb-0">Withdrawl Screen Short Request List</h4>
							</div>
							<hr/>
							<div class="table-responsives">
								<table id="examples" class="table table-striped table-bordered" style="width:100%">
								<thead>
                                    <tr>
                                        <th>Userid</th>
                                        <th>Request</th>
                                        <th>Recived</th>
                                        <th>Date</th>
                                        <th>Accept</th>
                                        <th>Reject</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        				<?php 
                        					$i=1;
                        					$query = mysqli_query($con,"select * from images_with where status='Pending' order by id desc");
                        					if(mysqli_num_rows($query)>0){
                        						while($row=mysqli_fetch_array($query)){
                        							$id = $row['id'];
                        							$user_id = $row['userid'];
                        							$first_image_name = $row['first_image_name'];
                        							$second_image_name = $row['second_image_name'];
                        							$date = $row['date'];                       							
                        				?>                                       
                                          <tr>
                                            <td><?php echo $user_id; ?></td>
                                            <td><a href="../asupport/withdrawal/<?php echo $first_image_name; ?>" target="_blank"><img class="user-img" src="../asupport/withdrawal/<?php echo $first_image_name;  ?>" height="70" width="100"></a></td>
                                            <td><a href="../asupport/withdrawal/<?php echo $second_image_name; ?>" target="_blank"><img class="user-img" src="../asupport/withdrawal/<?php echo $second_image_name;  ?>" height="70" width="100"></a></td>
                                            <td><?php echo $date; ?></td>
                                            <td>
                                                <form action="" method="post">
                                                    <input class="form-control"  type="hidden" name="update_id" value='<?php echo $id; ?>'  >
                                                    <input class="form-control"  type="hidden" name="userid" value='<?php echo $user_id; ?>'  >
                                                     <input class="form-control"  type="hidden" name="pay_amount" value='20'  >
                                                    <input type="submit" name="update" class="btn btn-primary" value="Accept">
                                                </form>
                                           </td> 
                                            <td>   
                                                <form action="?reject_id=<?php echo $id ?>" method="post"><input type="submit" name="reject" class="btn btn-danger" value="Reject"></form>
                                            </td>
                                          </tr>
									<?php
									$i++;
								}
							}
							else{
							?>
                            	<tr>
                                	<td colspan="10">There are no message yet.</td>
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
						
					<!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Withdrawl Screen Short Request List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
								<thead>
                                    <tr>
                                        <th>Userid</th>
                                        <th>Request</th>
                                        <th>Recived</th>
                                        <th>Date</th>
                                       
                                        </tr>
                                    </thead>
                                    <tbody>
                        				<?php 
                        					$i=1;
                        					$query = mysqli_query($con,"select * from images_with where status!='Pending' order by id desc");
                        					if(mysqli_num_rows($query)>0){
                        						while($row=mysqli_fetch_array($query)){
                        							$id = $row['id'];
                        							$user_id = $row['userid'];
                        							$first_image_name = $row['first_image_name'];
                        							$second_image_name = $row['second_image_name'];
                        							$date = $row['date'];                       							
                        				?>                                       
                                          <tr>
                                            <td><?php echo $user_id; ?></td>
                                            <td><a href="../asupport/withdrawal/<?php echo $first_image_name; ?>" target="_blank"><img class="user-img" src="../asupport/withdrawal/<?php echo $first_image_name;  ?>" height="70" width="100"></a></td>
                                            <td><a href="../asupport/withdrawal/<?php echo $second_image_name; ?>" target="_blank"><img class="user-img" src="../asupport/withdrawal/<?php echo $second_image_name;  ?>" height="70" width="100"></a></td>
                                            <td><?php echo $date; ?></td>
                                           
                                          </tr>
									<?php
									$i++;
								}
							}
							else{
							?>
                            	<tr>
                                	<td colspan="10">There are no message yet.</td>
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
			var table = $('#example2').DataTable({
				lengthChange: false,
				buttons: ['copy', 'excel', 'pdf', 'print', 'colvis']
			});
			table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
		});
        $(document).ready(function () {
			//Default data table
			$('#examples').DataTable();
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

<?php 
// Payment Reject
if(isset($_POST['reject']))
{	
    $update_id = $_GET['reject_id'];
    $query = mysqli_query($con,"update images_with set status='Reject' where id='$update_id' ");
    echo '<script>alert("Wallet Request Has Been Reject Successfully");window.location.assign("withdrawl_scrren.php");</script>';
}
?>
	<?php
 
	
 if(isset($_POST['update']))
 {
     $update_id = $_POST['update_id'];
     $transfer_user = $_POST['userid'];   
     $amount = $_POST['pay_amount'];

    // mysqli_query($con,"INSERT INTO `fpay`(`userid`,`send_userid`,`amount`,`status`,`pay_type`,`pay_user`,`date`) values('$transfer_user','Admin','$amount','Accept','$pay_type','$userid',Now())");
     mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+$amount WHERE `userid`='$transfer_user'");
     mysqli_query($con,"update images_with set status='Paid' where id='$update_id'");
         echo "<script>alert ('Wallet Payment has been Send!')</script>";
         echo "<script>window.open('withdrawl_scrren.php','_self')</script>";     
         
 }
     ?>