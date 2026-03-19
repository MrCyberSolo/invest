<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
if(isset($_GET['pac']))
{

    $agent_id = $_GET['pac'];
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
								<h4 class="mb-0">Package Create</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
											<div class="col-md-4">
												<label class="form-label">Type</label>
												<select class="form-control" name="pa_type" id="exampleSelect1" required>
                                                    <option selected="selected" value=" " >-- Select Type  -- </option>
                                                                                              
                                                    <option value="User">User</option>                                            
                                                    <option value="User 2">User 2</option>                                            
                                                    <!--<option value="Month">Month</option>                                            
                                                    <option value="Finance">Finance</option>  -->                                        
                                                </select>
											</div>	
											<div class="col-md-8">
												<label  class="form-label">Package Name</label>
												<input type="text" class="form-control" name="pa_name"   >
											</div>
                                            
                                            
                                            <div class="col-md-4">
												<label  class="form-label">Amount</label>
												<input type="number" class="form-control" name="pa_amount"   >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Days</label>
												<input type="number" class="form-control" name="pa_day" >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Daily Income</label>
												<input type="text" class="form-control" name="pa_com_amount"  >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Bonus</label>
												<input type="text" class="form-control" name="pa_cash"  >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Limit</label>
												<select class="form-control" name="pa_sponser" id="exampleSelect1" required>
                                                    <option selected="selected" value=" " >-- Select Type  -- </option>
                                                    <option value="1">1</option>                                            
                                                    <option value="2">2</option>                                            
                                                    <option value="3">3</option>                                            
                                                    <option value="4">4</option>                                            
                                                    <option value="5">5</option>                                            
                                                    <option value="6">6</option>                                            
                                                    <option value="7">7</option>                                            
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Image</label>
												<input type="file" class="form-control" name="file"  >
											</div>
                                            <div class="col-md-12">
												<label class="form-label">Details</label>
													<textarea class="form-control" name="pa_text"></textarea>											
																						
											</div>
                                            
											<div class="col-12">
                                            <center> <button class="btn btn-primary" type="submit" name="package_create" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Create Package</button></center>
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
			var table = $('#example2').DataTable({
				lengthChange: false,
				buttons: ['copy', 'excel', 'pdf', 'print', 'colvis']
			});
			table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
		});
	</script>

	<script src="assets/js/app.js"></script>
	<script src="https://cdn.tiny.cloud/1/pqx9aq5lf8r9v00twtag1srklg5ramshw0ju5qzpv09m25k6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</body>

</html>
<?php
if(isset($_POST['package_create']))
{
    $pa_amount = $_POST['pa_amount'];		
    $pa_day = $_POST['pa_day'];		
    $pa_type = $_POST['pa_type'];				
    $pa_com_amount = $_POST['pa_com_amount'];				
    $pa_name = $_POST['pa_name'];				
    $pa_text = $_POST['pa_text'];
    $pa_cash = $_POST['pa_cash'];
    $pa_sponser = $_POST['pa_sponser'];
    
    $pa_level1 =  $pa_com_amount * 10/100;
    $pa_level2 =  $pa_com_amount * 3/100;
    $pa_level3 =  $pa_com_amount * 2/100;
    
    //$user_id = $userid;	
	$file_tmp = $_FILES['file']['tmp_name'];
	$file_size = $_FILES['file']['size'];
	$file_error = $_FILES['file']['error'];

	$folder="../asupport/package/";

	// 1. Check for upload errors
	if ($file_error !== UPLOAD_ERR_OK) {
		echo "<script>alert('Upload error occurred.')</script>";
		echo "<script>window.history.back();</script>";
		exit();
	}

	// 2. Validate MIME type & Image Integrity using getimagesize
	$image_info = @getimagesize($file_tmp);
	if ($image_info === false) {
		echo "<script>alert('Invalid image file. The file is corrupted or not a valid image.')</script>";
		echo "<script>window.history.back();</script>";
		exit();
	}

	// 3. Strict extension check
	$allowed_extensions = array("jpg", "jpeg", "png", "gif", "webp", "bmp");
	$file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

	if (!in_array($file_extension, $allowed_extensions)) {
		echo "<script>alert('Invalid file format. Only JPG, JPEG, PNG, GIF, WEBP and BMP images are allowed.')</script>";
		echo "<script>window.history.back();</script>";
		exit();
	}

	// 4. Validate MIME Type strictly
	$allowed_mime_types = array("image/jpeg", "image/png", "image/gif", "image/webp", "image/bmp");
	if (!in_array($image_info['mime'], $allowed_mime_types)) {
		echo "<script>alert('Invalid MIME type. Malicious file detected.')</script>";
		echo "<script>window.history.back();</script>";
		exit();
	}

    // 5. Generate secure, random file name preventing directory traversal attacks
	$final_file = bin2hex(random_bytes(16)) . "." . $file_extension;

	if(move_uploaded_file($file_tmp, $folder.$final_file)){
	    mysqli_query($con,"INSERT INTO `package`(`pa_amount`, `pa_day`, `pa_com_amount`, `pa_level1`, `pa_level2`, `pa_level3`, `pa_cash`, `pa_sponser`, `pa_type`,`pa_name`, `pa_text`, `pa_image`) VALUES('$pa_amount','$pa_day','$pa_com_amount','$pa_level1','$pa_level2','$pa_level3','$pa_cash','$pa_sponser','$pa_type','$pa_name','$pa_text','$final_file')");
		
			echo "<script>alert ('Package create Successfull')</script>";
			echo "<script>window.open('package.php','_self')</script>";
    }
    
}
?>

