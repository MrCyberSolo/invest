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
								<h4 class="mb-0">Bank Create</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
											<div class="col-md-12">
												<label  class="form-label">Bank UPI</label>
											    <textarea class="form-control" name="bank_upi"></textarea>
											</div>
                                            <div class="col-md-12">
												<label  class="form-label">Image</label>
												<input type="file" class="form-control" name="file"  >
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
                    <!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Bank List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Bank ID</th>
											<th>Address</th>
											<th>Scan Code</th>								
											<th>Create Date</th>
											<th>Action</th>
										
										</tr>
									</thead>
									<tbody>
                                    <?php 
                                       $i=1;
                                       $query = mysqli_query($con,"select * from bank order by bank_id  desc");
                                       if(mysqli_num_rows($query)>0){
                                           while($row=mysqli_fetch_array($query)){
                                               $id = $row['bank_id'];
                                               $bank_upi = $row['bank_upi'];
                                               $image = $row['bank_scan'];
                                               $bank_date = $row['bank_date'];
                                         ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $bank_upi; ?></td>
                                        <td><img src="../asupport/<?php echo $image; ?>" style="Width : 200px; height : 100px;"></td>
                                        <td><a href="#"><?php echo $bank_date; ?></a></td>                                        
                                        <td>
                                            <a href="?del=<?php echo $id; ?>" class="btn btn-danger mb-2 mr-1" style="color:#fff;"><span class="glyphicon glyphicon-edit"></span> Delete</a>
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
	if(isset($_POST['submit']))
    {
        $bank_upi = $_POST['bank_upi'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_error = $_FILES['file']['error'];
        $folder="../asupport/";
        
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
        
        
        $query = "INSERT INTO `bank`(`bank_scan`, `bank_upi`) VALUES('$final_file','$bank_upi')";
        
        $run_posts = mysqli_query($con,$query);
            
            echo "<script>alert ('Bank Image Updated!')</script>";
            echo "<script>window.open('bank.php','_self')</script>";
        }
	}	
?>
<?php 
if(isset($_GET['del']))
{
    $res=mysqli_query($con, "SELECT * FROM bank WHERE bank_id =".$_GET['del']);
	$_SERVER=mysqli_fetch_array($res);
	mysqli_query($con, "DELETE FROM bank WHERE bank_id =".$_GET['del']);
	unlink("../asupport/".$_SERVER['bank_scan']);
	echo "<script>alert('Bank successfully deleted!')</script>";
	echo "<script>window.open('bank.php','_self')</script>";
}
?>  