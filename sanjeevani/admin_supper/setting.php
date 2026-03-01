<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
$query_setting = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `settings`"));

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
								<h4 class="mb-0">Company Setting</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
											<!-- <div class="col-md-4">
												<label class="form-label">Type</label>
												<select class="form-control" name="pa_type" id="exampleSelect1" required>
                                                    <option selected="selected" value=" " >-- Select Type  -- </option>
                                                    <option value="User">User</option>                                            
                                                </select>
											</div> -->	
											<div class="col-md-8">
												<label  class="form-label">Company Name</label>
												<input type="text" class="form-control" name="s_name" value="<?php echo $query_setting['s_name']; ?>"  >
											</div>
                                            
                                            
                                            <div class="col-md-4">
												<label  class="form-label">Telegram</label>
												<input type="text" class="form-control" name="s_telegram" value="<?php echo $query_setting['s_telegram']; ?>"  >
											</div>
										 	<div class="col-md-4">
												<label  class="form-label">Whatsapp</label>
												<input type="text" class="form-control" name="s_whatsapp" value="<?php echo $query_setting['s_whatsapp']; ?>" >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Telegram  Customer Services</label>
												<input type="text" class="form-control" name="s_whatsapp_group"  value="<?php echo $query_setting['s_whatsapp_group']; ?>" >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Min Withdrawl </label>
												<input type="text" class="form-control" name="s_withdrawl" value="<?php echo $query_setting['s_withdrawl']; ?>" >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Payment Start Time</label>
												<select class="form-control" name="s_start_time" id="exampleSelect1" required>
                                                    <option value="<?php echo $query_setting['s_start_time']; ?>"><?php echo $query_setting['s_start_time']; ?></option>                                            
                                                    <option value="1">1</option>                                            
                                                    <option value="2">2</option>                                            
                                                    <option value="3">3</option>                                            
                                                    <option value="4">4</option>                                            
                                                    <option value="5">5</option>                                            
                                                    <option value="6">6</option>                                            
                                                    <option value="7">7</option>                                            
                                                    <option value="8">8</option>                                            
                                                    <option value="9">9</option>                                            
                                                    <option value="10">10</option>                                            
                                                    <option value="11">11</option>                                            
                                                    <option value="12">12</option>                                            
                                                    <option value="13">13</option>                                            
                                                    <option value="14">14</option>                                            
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Payment End Time</label>
												<select class="form-control" name="s_end_time" id="exampleSelect1" required>
													<option value="<?php echo $query_setting['s_end_time']; ?>"><?php echo $query_setting['s_end_time']; ?></option> 
                                                    <option value="15">15</option>                                            
                                                    <option value="16">16</option>                                            
                                                    <option value="17">17</option>                                            
                                                    <option value="18">18</option>                                            
                                                    <option value="19">19</option>                                            
                                                    <option value="20">20</option>                                            
                                                    <option value="21">21</option>                                            
                                                    <option value="22">22</option>                                            
                                                    <option value="23">23</option>                                            
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Payout Close</label>
												<select class="form-control" name="s_payout_close" id="exampleSelect1" required>
                                                    <!-- <option selected="selected" value=" " >-- Select Type  -- </option> -->
													<option value="<?php echo $query_setting['s_payout_close']; ?>"><?php echo $query_setting['s_payout_close']; ?></option> 
                                                    <option value="Sunday">Sunday</option>                                            
                                                    <option value="Saturday">Saturday & Sunday</option>                                               
                                                    <option value="Everyday">Everyday</option>                                               
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Withdrawl Close</label>
												<select class="form-control" name="s_withdrawal_status" id="exampleSelect1" required>
												<option value="<?php echo $query_setting['s_withdrawal_status']; ?>"><?php echo $query_setting['s_withdrawal_status']; ?></option> 
                                                    <option value="Active">Active</option>                                            
                                                    <option value="Deactive">Deactive</option>                                               
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Payment Type</label>
												<select class="form-control" name="s_recharge" id="exampleSelect1" required>
												<option value="<?php echo $query_setting['s_recharge']; ?>"><?php echo $query_setting['s_recharge']; ?></option> 
                                                    <option value="Both">Both</option>                                            
                                                    <option value="Online">Online</option>                                               
                                                    <option value="Offline">Offline</option>                                               
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Logo </label>
												<input type="file" class="form-control" name="file" accept="image/png, image/jpeg, image/jpg, image/gif, image/webp" >
											</div>
                                            
											<div class="col-12">
                                            <center> <button class="btn btn-primary" type="submit" name="user_setting" ><i class="fa fa-fw fa-lg fa-check-circle"></i>Account Setting</button></center>
											</div>
										</form>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
                    <!--end breadcrumb-->  
                    <!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Change Account Password</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  name="chngpwd" action="" method="post" onSubmit="return valid();">
											<div class="col-md-4">
												<label  class="form-label">Old Password</label>
												<input type="password" class="form-control" name="opwd" type="password" required>
												
											</div>
											<div class="col-md-4">
												<label  class="form-label">New Password</label>
												<input type="password" class="form-control" name="npwd" type="password" required>
												
											</div>
											<div class="col-md-4">
												<label  class="form-label">Confirm Password</label>
												<input type="password" class="form-control" name="cpwd" type="password" required>
											</div>
                                           

											<div class="col-12">
                                            
                                                <center> <button class="btn btn-primary" type="submit" name="Submit" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Change Password</button> </center> 
                                            </div>
										</form>
									</div>
								</div>
							</div>
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
	<script src='https://cdn.tiny.cloud/1/vdqx2klew412up5bcbpwivg1th6nrh3murc6maz8bukgos4v/tinymce/5/tinymce.min.js' referrerpolicy="origin"></script>
	<script>
		tinymce.init({
		  selector: '#mytextarea'
		});
		tinymce.init({
		  selector: '#mytextareas'
		});
	</script>
	<script src="assets/js/app.js"></script>
</body>

</html>
<?php
if(isset($_POST['user_setting']))
{
    $s_name = $_POST['s_name'];    
    $s_telegram = $_POST['s_telegram'];    
    $s_whatsapp = $_POST['s_whatsapp'];    
    $s_whatsapp_group = $_POST['s_whatsapp_group'];    
    $s_withdrawl = $_POST['s_withdrawl'];    
    $s_start_time = $_POST['s_start_time'];    
    $s_end_time = $_POST['s_end_time'];    
    $s_payout_close = $_POST['s_payout_close'];    
    $s_withdrawal_status = $_POST['s_withdrawal_status'];    
    $s_recharge = $_POST['s_recharge'];    

    //$user_id = $userid;	
	$file = rand(1000,100000)."-".$_FILES['file']['name'];
    $file_loc = $_FILES['file']['tmp_name'];
	$file_size = $_FILES['file']['size'];
	$file_type = $_FILES['file']['type'];
	$folder="../asupport/";
	
    $allowed_extensions = array("jpg", "jpeg", "png", "gif", "webp", "bmp");
    $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "<script>alert('Invalid file format. Only JPG, JPEG, PNG, GIF, WEBP and BMP images are allowed.')</script>";
        echo "<script>window.open('setting.php','_self')</script>";
        exit();
    }
	// new file size in KB
	$new_size = $file_size/1024;  
	// new file size in KB
	
	// make file name in lower case
	$new_file_name = strtolower($file);
	// make file name in lower case
	
	$final_file=str_replace(' ','-',$new_file_name);

	if(move_uploaded_file($file_loc,$folder.$final_file)){
	    mysqli_query($con,"UPDATE `settings` SET `s_name`='$s_name',`s_logo`='$final_file',`s_telegram`='$s_telegram',`s_whatsapp`='$s_whatsapp',`s_whatsapp_group`='$s_whatsapp_group',`s_recharge`='$s_recharge',`s_withdrawl`='$s_withdrawl',`s_start_time`='$s_start_time',`s_end_time`='$s_end_time',`s_payout_close`='$s_payout_close',`s_withdrawal_status`='$s_withdrawal_status' WHERE `s_id`='1'");
		
			echo "<script>alert ('Account Updated Successfull')</script>";
			echo "<script>window.open('setting.php','_self')</script>";
    }
    
}
?>
<?php
//include("dbconnection.php");
if(isset($_POST['Submit']))
{
     $oldpass=$_POST['opwd'];
     $newpassword=$_POST['npwd'];
     $cpwd=$_POST['cpwd'];
     
    if($newpassword==$cpwd){
        $sql=mysqli_query($con,"SELECT password FROM admin where password='$oldpass' && id='1'");
        $num=mysqli_fetch_array($sql);
    if($num>0)
    {
        $con=mysqli_query($con,"UPDATE `admin` SET `password`='$newpassword' WHERE `id`='1'");
        echo "<script>alert('Password Changed Successfully !!')</script>";
    }
    else
    {
        echo "<script>alert('Old Password not match !!')</script>";
    }
         
     }else{
        echo "<script>alert('New Password and Confirm password not match !!')</script>";
    }
}
?>
