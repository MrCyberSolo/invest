<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
if(isset($_GET['view']))
{
 $user_edit_id = $_GET['view'];
}
$get = "select * from user where mobile='$user_edit_id'";
$run = mysqli_query($con, $get);  
$row_user= mysqli_fetch_array($run);   
   echo $name_user = $row_user['name'];
    $user_id = $row_user['email'];
    $under_userid = $row_user['under_userid'];
    $user_status = $row_user['status'];
    $profile_img = $row_user['profile_img'];
    $mobile = $row_user['mobile'];
    $join_date = $row_user['join_date'];
    $email1 = $row_user['email1'];
    $city = $row_user['city'];
    $dist = $row_user['dist'];
    $pincode = $row_user['pincode'];
    $state = $row_user['state'];
    $nominee = $row_user['nominee'];
    $nrelation = $row_user['nrelation'];
    $address = $row_user['address'];
    $password = $row_user['password'];
    $withdrawal_passwrod = $row_user['withdrawal_passwrod'];
    $pan_card = $row_user['pan_card'];
    $bank_name = $row_user['bank_name'];
    $account_no = $row_user['account_no'];
    $ifsc_code = $row_user['ifsc_code'];
    $user_upi = $row_user['user_upi'];
    $bank_branch = $row_user['bank_branch'];
    
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
                    <div class="card radius-15">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">User Profile</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th scope="col">Userid</th>
											<th scope="col">Name</th>
											<th scope="col">Email Id</th>
											<th scope="col">Mobile</th>
											<th scope="col">Under Userid</th>
											<th scope="col">Joining Date</th>
											<th scope="col">Status</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo $user_id ?> </td>
											<td><?php echo $name_user ?></td>
											<td><?php echo $email1 ?></td>
											<td><?php echo $mobile ?></td>
											<td><?php echo $under_userid ?></td>
											<td><?php echo $join_date ?></td>
											<td><?php echo $user_status ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!--end breadcrumb-->
                    <!--breadcrumb-->
                    <div class="card radius-15">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">User Address</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th scope="col">Address</th>
											<th scope="col">City</th>
											<th scope="col">State</th>
											<th scope="col">PinCode</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo $address ?> </td>
											<td><?php echo $city ?></td>
											<td><?php echo $state ?></td>
											<td><?php echo $pincode ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!--end breadcrumb-->
                    
                    <!--breadcrumb-->
                    <div class="card radius-15">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">User Bank Details</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th scope="col">Bank Name</th>
											<th scope="col">Account No</th>
											<th scope="col">IFSC Code</th>
											<th scope="col">UPI ID</th>
											<th scope="col">Password</th>
											<th scope="col">Withdrawl Password</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo $bank_name ?> </td>
											<td><?php echo $account_no ?></td>
											<td><?php echo $ifsc_code ?></td>
											<td><?php echo $user_upi ?></td>
											<td><?php echo $password ?></td>
											<td><?php echo $withdrawal_passwrod
                                             ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!--end breadcrumb-->
                    	<!--start breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Refer and Level Payment Details</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
								    <thead>
                                        <tr>
                                            <th>Sl No</th>
                                            <th>UserID</th>
                                            <th>Amount</th>
                                            <th>Level </th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                           <?php 
							$i=1;
							$query = mysqli_query($con,"SELECT * FROM `interest_label` where int_userid=''");
							if(mysqli_num_rows($query)>0){
								while($row=mysqli_fetch_array($query)){
									$int_amount = $row['int_amount'];
									$int_level = $row['int_level'];
									$int_date = $row['int_date'];
								
									$int_acc_id = $row['int_acc_id'];
									$query_lebel = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `order_book_label` WHERE `o_id`='$int_acc_id'"));
		                            	$level_user = $query_lebel['o_userid'];
								
								?>
                                    <tr class="odd gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $level_user; ?></td>
                                        <td><?php echo $int_amount; ?></td>
                                        <td><?php echo $int_level; ?></td>
                                        <td><?php echo $int_date; ?></td>
                                       
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
            <!--start breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">User List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
								<thead>
                                <tr>
									<th>Sl No</th>
									<th>Name</th>
									<th>Mobile</th>
									<th>User Id</th>
									<th>Activation Date</th>
									<th>Joining Date</th>
								</tr>
							</thead>
							<tbody>
								<?php 
							$i=1;
							$query = mysqli_query($con,"SELECT * FROM `user` where under_userid='$user_id' order BY user_id ASC");
							if(mysqli_num_rows($query)>0){
								while($row=mysqli_fetch_array($query)){
									$id = $row['user_id'];
									$name = $row['name'];
									$email = $row['email'];
									$mobile = $row['mobile'];
									$address = $row['address'];
									$under_userid = $row['under_userid'];
                                    $act_date = $row['act_date'];
									$join_date = $row['join_date'];
								?>
                                    <tr class="odd gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $name; ?></td>
                                        <td><?php echo $mobile; ?></td>
                                        <td><?php echo $email; ?></td>
                                        <td><?php echo $act_date; ?></td>
                                        <td><?php echo $join_date; ?></td>
                                       
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
            <!--start breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Transaction Details</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Tr ID</th>
                                            <th>Details</th>
                                            <th>User ID</th>
                                            <th>Amount</th>
                                            <th>Status </th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                <tbody>
                        <?php
                        $i=1;
                        $query = mysqli_query($con,"select * from transaction where t_userid ='$user_id'  order by t_id desc");
                        if(mysqli_num_rows($query)>0){
                            while($row=mysqli_fetch_array($query)){
                                $t_id  = $row['t_id'];
                                $t_details = $row['t_details'];
                                $t_amount = $row['t_amount'];
                                $t_type = $row['t_type'];
                                $t_date = $row['t_date'];
                                ?> 
							        <tr class="odd gradeX">
                                    <td><?php echo $t_id; ?></td>
                                    <td><?php echo $t_details; ?></td>
                                    <td><?php echo $user_id; ?></td>
                                    <td><?php echo $t_amount; ?></td>
                                    <td><?php echo $t_type; ?></td>
                                    <td><?php echo $t_date; ?></td>
                                       
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
