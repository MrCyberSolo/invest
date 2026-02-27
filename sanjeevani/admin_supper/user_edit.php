<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
if(isset($_GET['view']))
{
 $user_edit_id = $_GET['view'];
}
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
								<h4 class="mb-0">Customer Profile</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                                <?php 
                                    $get = "select * from user where email='$user_edit_id'";
                                    $run = mysqli_query($con, $get);  
                                    $row_user= mysqli_fetch_array($run);   
                                        $name = $row_user['name'];
                                        $email = $row_user['email'];
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
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
											<div class="col-md-4">
												<label  class="form-label">UserID</label>
												<input type="text" class="form-control" name="email"  value="<?php echo $email; ?>" readonly>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Mobile No</label>
												<input type="number" class="form-control" name="mobile" value="<?php echo $mobile; ?>" readonly >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Name</label>
												<input type="text" class="form-control" name="name"  value="<?php echo $name; ?>" >
											</div>
                                            <div class="col-md-4">
												<label  class="form-label">Email</label>
												<input type="text" class="form-control" name="email1"  value="<?php echo $email1; ?>" >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Address</label>
												<input type="text" class="form-control" name="address"  value="<?php echo $address; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">City</label>
												<input type="text" class="form-control" name="city"  value="<?php echo $city; ?>">
											</div>
                                            <div class="col-md-4">
												<label class="form-label">State</label>
												<select class="form-control" name="state" id="exampleSelect1" required>
                                                    <option selected="selected" value=" " >-- Select State  -- </option>
                                                    <option selected="selected" value="<?php echo $state; ?>" ><?php echo $state; ?></option>
                                                    <option value="ANDAMAN NICOBAR">ANDAMAN &amp; NICOBAR IS</option>
                                                    <option value="ANDHRA PRADESH">ANDHRA PRADESH</option>
                                                    <option value="ARUNACHAL PRADESH">ARUNACHAL PRADESH</option>
                                                    <option value="ASSAM">ASSAM</option>
                                                    <option value="BIHAR">BIHAR</option>
                                                    <option value="CHHATTISH GARH">CHHATTISH GARH</option>
                                                    <option value="DADRA AND NAGAR HAVELI ">DADRA &amp; NAGAR HAVELI</option>
                                                    <option value="DAMAN And DIU">DAMAN &amp; DIU</option>
                                                    <option value="DELHI">DELHI</option>
                                                    <option value="GOA">GOA</option>
                                                    <option value="GUJRAT">GUJRAT</option>
                                                    <option value="HARYANA">HARYANA</option>
                                                    <option value="HIMACHAL PRADESH">HIMACHAL PRADESH</option>
                                                    <option value="JAMMU AND KASHMIR ">JAMMU &amp; KASHMIR</option>
                                                    <option value="JHARKHAND">JHARKHAND</option>
                                                    <option value="KARNATAKA">KARNATAKA</option>
                                                    <option value="KERALA">KERALA</option>
                                                    <option value="LAKSHADWEEP">LAKSHADWEEP</option>
                                                    <option value="MADHYA PRADESH">MADHYA PRADESH</option>
                                                    <option value="MAHARASHTRA">MAHARASHTRA</option>
                                                    <option value="MANIPUR">MANIPUR</option>
                                                    <option value="MEGHALAYA">MEGHALAYA</option>
                                                    <option value="MIZORAM">MIZORAM</option>
                                                    <option value="NAGALAND">NAGALAND</option>
                                                    <option value="ORISSA">ORISSA</option>
                                                    <option value="PONDICHERRY">PONDICHERRY</option>
                                                    <option value="PUNJAB">PUNJAB </option>
                                                    <option value="RAJASTHAN">RAJASTHAN</option>
                                                    <option value="SIKKIM">SIKKIM</option>
                                                    <option value="TAMIL NADU">TAMIL NADU</option>
                                                    <option value="TRIPURA">TRIPURA</option>
                                                    <option value="UTTAR PRADESH">UTTAR PRADESH</option>
                                                    <option value="UTTARAKHAND">UTTARAKHAND</option>
                                                    <option value="WEST BENGAL">WEST BENGAL</option>
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Pin Code</label>
												<input type="text" class="form-control" name="pincode"  value="<?php echo $pincode; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Password</label>
												<input type="text" class="form-control" name="password"  value="<?php echo $password; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Withdrawal Password</label>
												<input type="text" class="form-control" name="withdrawal_passwrod"  value="<?php echo $withdrawal_passwrod; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Nominee</label>
												<input type="text" class="form-control" name="nominee"  value="<?php echo $nominee; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Nominee Relation</label>
												<input type="text" class="form-control" name="nrelation"  value="<?php echo $nrelation; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">PAN Card</label>
												<input type="text" class="form-control" name="pan_card"  value="<?php echo $pan_card; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Bank Name</label>
												<input type="text" class="form-control" name="bank_name"  value="<?php echo $bank_name; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Account Number</label>
												<input type="text" class="form-control" name="account_no"  value="<?php echo $account_no; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">IFSC Code</label>
												<input type="text" class="form-control" name="ifsc_code"  value="<?php echo $ifsc_code; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">UPI Id</label>
												<input type="text" class="form-control" name="user_upi"  value="<?php echo $user_upi; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Bank Branch</label>
												<input type="text" class="form-control" name="bank_branch"  value="<?php echo $bank_branch; ?>">
											</div>
                                            
											<div class="col-12">
                                            <center> <button class="btn btn-primary" type="submit" name="user_update" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Save</button></center>
											</div>
										</form>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
                    <!--end breadcrumb-->   
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
<?php 	
	if(isset($_POST['user_update'])){
		
		$name = $_POST['name'];
		$email1 = $_POST['email1'];
		$mobile = $_POST['mobile'];
		$city = $_POST['city'];
		$pincode = $_POST['pincode'];
		$address = $_POST['address'];
		$state = $_POST['state'];
		$password = $_POST['password'];
		$withdrawal_passwrod = $_POST['withdrawal_passwrod'];
		$nominee = $_POST['nominee'];
		$nrelation = $_POST['nrelation'];
		$pan_card = $_POST['pan_card'];
		$bank_name = $_POST['bank_name'];
		$account_no = $_POST['account_no'];
		$ifsc_code = $_POST['ifsc_code'];
		$user_upi = $_POST['user_upi'];
		$bank_branch = $_POST['bank_branch'];
		$query = mysqli_query($con,"UPDATE `user` SET `name`='$name',`email1`='$email1',`city`='$city',`pincode`='$pincode',`state`='$state',`password`='$password',`withdrawal_passwrod`='$withdrawal_passwrod',`address`='$address',`nominee`='$nominee',`nrelation`='$nrelation',`pan_card`='$pan_card',`bank_account_name`='$bank_account_name',`bank_name`='$bank_name',`account_no`='$account_no',`ifsc_code`='$ifsc_code',`user_upi`='$user_upi',`bank_branch`='$bank_branch' WHERE `email`='$user_edit_id'");
	
	echo "<script>alert('User Detail has been updated!')</script>";
	echo "<script>window.open('user.php','_self')</script>";
}
	?>
