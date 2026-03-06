<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
if(isset($_GET['pac']))
{

    $blog_id = $_GET['pac'];
    $blog_details = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `blog` WHERE `b_id`='$blog_id'"));
	$blog_id = $blog_details['b_id'];
	$blog_title = $blog_details['b_title'];
	$blog_image = $blog_details['b_image'];
	$blog_image2 = $blog_details['b_image2'];
	$blog_des = $blog_details['b_details'];
	$blog_create_date = $blog_details['b_create_date'];
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
								<h4 class="mb-0">Blog View / Edit</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
											<div class="col-md-12">
												<label  class="form-label">Title</label>
												<input type="text" class="form-control" name="b_title" value="<?php echo $blog_title; ?>"   >
											</div>
											<div class="col-md-12">
												<label  class="form-label">Message</label>
											
												<textarea  class="form-control" name="b_details"><?php echo $blog_des; ?></textarea>
											</div>
											<div class="col-12">
                                            <center> <button class="btn btn-primary" type="submit" name="submit" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Edit</button></center>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
										    <div class="col-md-6">
											   <label  class="form-label">Image</label>
											     <input type="file" class="form-control" name="file1">
											  	<img src="../asupport/blog/<?php echo $blog_image; ?>" class="msg-avatar" style="width: 30%;">
											</div>
											<div class="col-md-6">
											    <label  class="form-label">Image</label>
											    <input type="file" class="form-control" name="file2">
											  	<img src="../asupport/blog/<?php echo $blog_image2; ?>" class="msg-avatar" style="width: 30%;">
											</div>
											<div class="col-12">
                                                <center> <button class="btn btn-primary" type="submit" name="submit2" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Edit</button></center>
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


</body>

</html>
<?php
    // Check if the form is submitted
    if (isset($_POST['submit'])) 
	{
        $blog_title = $_POST['b_title'];
        $blog_des = $_POST['b_details'];
        // Insert the file details into the database
			$sql=mysqli_query($con,"UPDATE `blog` SET `b_title`='$blog_title',`b_details`='$blog_des' WHERE `b_id`='$blog_id'");
			if ($sql === TRUE) {
               echo "<script>alert('Blog Edit Successfully')</script>";
                echo "<script>window.open('blog.php','_self')</script>";
            } else {
               echo "<script>alert('Error editing Blog')</script>";
               echo "<script>window.open('blog.php','_self')</script>";
            }        
    }
?>
Copy code
<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    // File upload handling for first image
    $target_dir = "../asupport/blog/";
    $allowed_extensions = array("jpg", "jpeg", "png", "gif", "webp", "bmp");
    $allowed_mime_types = array("image/jpeg", "image/png", "image/gif", "image/webp", "image/bmp");
     
    $file1_name = basename($_FILES["file1"]["name"]);
    $file2_name = basename($_FILES["file2"]["name"]);
    
    $imag_1 = $blog_image; // keep original by default
    $imag_2 = $blog_image2; // keep original by default
    
    // Process File 1 if uploaded
    if (!empty($file1_name) && $_FILES["file1"]["error"] === UPLOAD_ERR_OK) {
        $file1_tmp = $_FILES["file1"]["tmp_name"];
        $image1_info = @getimagesize($file1_tmp);
        if ($image1_info === false) {
            echo "<script>alert('Invalid image file 1. Corrupted or not a valid image.')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }

        $file1_ext = strtolower(pathinfo($file1_name, PATHINFO_EXTENSION));
        if (!in_array($file1_ext, $allowed_extensions) || !in_array($image1_info['mime'], $allowed_mime_types)) {
            echo "<script>alert('Invalid file format or malicious file detected for Image 1.')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
        
        $imag_1 = bin2hex(random_bytes(16)) . "." . $file1_ext;
        $target_file1 = $target_dir . $imag_1;
        move_uploaded_file($file1_tmp, $target_file1);
    }
    
    // Process File 2 if uploaded
    if (!empty($file2_name) && $_FILES["file2"]["error"] === UPLOAD_ERR_OK) {
        $file2_tmp = $_FILES["file2"]["tmp_name"];
        $image2_info = @getimagesize($file2_tmp);
        if ($image2_info === false) {
            echo "<script>alert('Invalid image file 2. Corrupted or not a valid image.')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }

        $file2_ext = strtolower(pathinfo($file2_name, PATHINFO_EXTENSION));
        if (!in_array($file2_ext, $allowed_extensions) || !in_array($image2_info['mime'], $allowed_mime_types)) {
            echo "<script>alert('Invalid file format or malicious file detected for Image 2.')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
        
        $imag_2 = bin2hex(random_bytes(16)) . "." . $file2_ext;
        $target_file2 = $target_dir . $imag_2;
        move_uploaded_file($file2_tmp, $target_file2);
    }

    // SQL query to update the blog table
    $sql = "UPDATE `blog` SET `b_image`='$imag_1', `b_image2`='$imag_2' WHERE `b_id`='$blog_id'";

    if ($con->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $con->error;
    }

    // The connect script usually keeps $con not $conn
    $con->close();
}
