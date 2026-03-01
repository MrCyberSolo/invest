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
								<h4 class="mb-0">Blog Create</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
											<div class="col-md-12">
												<label  class="form-label">Title</label>
												<input type="text" class="form-control" name="blog_title"   >
											</div>
											<div class="col-md-12">
												<label  class="form-label">Message</label>
											
												<textarea class="form-control" name="blog_des"></textarea>
											</div>
											<div class="col-md-12">
												<label  class="form-label">Image</label>
												<input type="file" class="form-control" name="image" accept="image/png, image/jpeg, image/jpg, image/gif, image/webp" >
											</div>
										
                                            
											<div class="col-12">
                                            <center> <button class="btn btn-primary" type="submit" name="submit" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Create</button></center>
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
	<!-- Place the first <script> tag in your HTML's <head> -->

</body>

</html>
  <?php
    
    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        
        
        $blog_title = $_POST['blog_title'];
        $blog_des = $_POST['blog_des'];
        
        $imageName = $_FILES['image']['name'];
        $imageType = $_FILES['image']['type'];
        $imageSize = $_FILES['image']['size'];
        $imageTmp = $_FILES['image']['tmp_name'];

        $allowed_extensions = array("jpg", "jpeg", "png", "gif", "webp", "bmp");
        $file_extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>alert('Invalid file format. Only JPG, JPEG, PNG, GIF, WEBP and BMP images are allowed.')</script>";
            echo "<script>window.open('blog_create.php','_self')</script>";
            exit();
        }

        // Move the uploaded image to a desired directory
        $uploadPath = "../asupport/blog/" . $imageName;
        if (move_uploaded_file($imageTmp, $uploadPath)) {
            // Insert the file details into the database
            //$sql = "INSERT INTO images (name, type, size, path) VALUES ('$imageName', '$imageType', $imageSize, '$uploadPath')";
            $sql = "INSERT INTO `blog`(`b_title`,`b_image`, `b_details`, `b_create_date`) VALUES('$blog_title', '$imageName', '$blog_des',NOW())";
            if ($con->query($sql) === TRUE) {
               echo "<script>alert('Blog Create Successfully')</script>";
                echo "<script>window.open('blog.php','_self')</script>";
            } else {
               echo "<script>alert('Error uploading Blog')</script>";
               echo "<script>window.open('blog_create.php','_self')</script>";
            }
        } else {
            echo "<script>alert('Error uploading image!')</script>";
            echo "<script>window.open('blog_create.php','_self')</script>";
            //echo "Error uploading image!";
        }
    }
    ?>
