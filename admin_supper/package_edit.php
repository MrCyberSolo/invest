<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
if(isset($_GET['pac']))
{
    $pac_id = $_GET['pac'];
    $query_package = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `package` WHERE `pa_id`='$pac_id'"));
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
                                                    <option value="<?php echo $query_package['pa_type']; ?>"><?php echo $query_package['pa_type']; ?></option>                                            
                                                    <option value="User">User</option>                                            
                                                    <option value="Month">Month</option>                                            
                                                    <option value="Finance">Finance</option>                                            
                                                </select>
											</div>	
											<div class="col-md-8">
												<label  class="form-label">Package Name</label>
												<input type="text" class="form-control" name="pa_name" value="<?php echo $query_package['pa_name']; ?>"  >
											</div>
                                            <div class="col-md-4">
												<label  class="form-label">Amount</label>
												<input type="number" class="form-control" name="pa_amount"  value="<?php echo $query_package['pa_amount']; ?>" >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Days</label>
												<input type="number" class="form-control" name="pa_day" value="<?php echo $query_package['pa_day']; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Daily Income</label>
												<input type="text" class="form-control" name="pa_com_amount"  value="<?php echo $query_package['pa_com_amount']; ?>">
											</div>
											<div class="col-md-4">
												<label  class="form-label">Bonus</label>
												<input type="text" class="form-control" name="pa_cash" value="<?php echo $query_package['pa_cash']; ?>" >
											</div>
											<div class="col-md-4">
												<label  class="form-label">Limit</label>
												<select class="form-control" name="pa_sponser" id="exampleSelect1" required>
                                                    <option selected="selected" value="<?php echo $query_package['pa_sponser']; ?>" >Limit <?php echo $query_package['pa_sponser']; ?></option>
                                                    <option value="1">Limit 1</option>                                            
                                                    <option value="2">Limit 2</option>                                            
                                                    <option value="3">Limit 3</option>                                            
                                                    <option value="4">Limit 4</option>                                            
                                                    <option value="5">Limit 5</option>                                            
                                                    <option value="6">Limit 6</option>                                            
                                                    <option value="7">Limit 7</option>                                            
                                                </select>
											</div>
											<div class="col-md-4">
												<label  class="form-label">Image</label>
												<input type="file" class="form-control" name="file"  required>
											</div>
                                            <div class="col-md-8">
												<label class="form-label">Details</label>
													<textarea id="mytextarea" class="form-control" name="pa_text"><?php echo $query_package['pa_text']; ?></textarea>											
											</div>
                                            
                                            <div class="col-md-4"><br>
                                                <img src="../asupport/package/<?php echo $query_package['pa_image']; ?>" style=" width: 100%; height: 200px ">
											</div>
                                            
											<div class="col-12">
                                            <center> <button class="btn btn-primary" type="submit" name="package_create" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Updated Package</button></center>
											</div>
										</form>
									</div>
								</div>
							</div>
                            <!-- <div class="card">
								<div class="card-body">
                                    <div class="p-4 border rounded">
                                        <form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
										    <div class="col-md-6">
												<label  class="form-label">Image</label>
												<input type="file" class="form-control" name="file"  required>
											</div>                                            
											<div class="col-6">
                                                <br>
                                                <center> <button class="btn btn-primary" type="submit" name="package_create" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Package Image</button></center>
											</div>
										</form>
								    </div>
							    </div>
							</div> -->
						</div>
					</div>
                    <!--end breadcrumb-->   
			<!--end page-content-wrapper-->
                                <!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Image List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Sl ID</th>
											<th>Image</th>								
											<th>Create Date</th>
											<th>Action</th>										
										</tr>
									</thead>
									<tbody>
                                    <?php 
                                       $i=1;
                                       $query = mysqli_query($con,"select * from package_img where pi_pa_id='$pac_id' order by pi_id  desc");
                                       if(mysqli_num_rows($query)>0){
                                           while($row=mysqli_fetch_array($query)){
                                               $pi_id = $row['pi_id'];
                                               $pi_pa_id = $row['pi_pa_id'];
                                               $pi_img = $row['pi_img'];
                                               $pi_date = $row['pi_date'];
                                         ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><img src="../asupport/package/img/<?php echo $pi_img; ?>" style="Width : 200px; height : 100px;"></td>
                                        <td><a href="#"><?php echo $pi_date; ?></a></td>                                        
                                        <td>
                                            <a href="?del=<?php echo $pi_id; ?>" class="btn btn-danger mb-2 mr-1" style="color:#fff;"><span class="glyphicon glyphicon-edit"></span> Delete</a>
                                        </td>
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
	<script src="https://cdn.tiny.cloud/1/pqx9aq5lf8r9v00twtag1srklg5ramshw0ju5qzpv09m25k6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
  });
</script>
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
        
    //$user_id = $userid;	
	$file_tmp = $_FILES['file']['tmp_name'];
	$file_size = $_FILES['file']['size'];
	$file_error = $_FILES['file']['error'];

	$folder="../uploads/20230328/";
	
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
	    mysqli_query($con,"UPDATE `package` SET  `pa_amount`='$pa_amount',`pa_day`='$pa_day',`pa_com_amount`='$pa_com_amount',`pa_cash`='$pa_cash',`pa_sponser`='$pa_sponser',`pa_name`='$pa_name',`pa_text`='$pa_text',`pa_image`='$final_file' WHERE `pa_id`='$pac_id'");
		
			echo "<script>alert ('Package Edit Successfull')</script>";
			echo "<script>window.open('package.php','_self')</script>";
    }
    
}
?>
<?php 
if(isset($_GET['del']))
{
    $res=mysqli_query($con, "SELECT * FROM package_img WHERE bank_id =".$_GET['del']);
	$_SERVER=mysqli_fetch_array($res);
	mysqli_query($con, "DELETE FROM package_img WHERE pi_id =".$_GET['del']);
	unlink("../asupport/package/img/".$_SERVER['pi_img']);
	echo "<script>alert('Product Slider Image deleted!')</script>";
	echo "<script>window.open('package.php','_self')</script>";
}
?> 