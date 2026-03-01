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
								<h4 class="mb-0">Package Image Add</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                                    <div class="p-4 border rounded">
                                        <form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
										    <div class="col-md-4">
												<label  class="form-label">Package Name/Id</label>
												<select class="form-control" name="pa_id" id="exampleSelect1" required>
                                                    <option selected="selected" value="" >Select Package Name/Id</option>
                                                    <?php 
                                                    $query = mysqli_query($con,"select * from package");
                                                    if(mysqli_num_rows($query)>0)
                                                    {
                                                        while($row=mysqli_fetch_array($query))
                                                        {
                                                            $pa_id = $row['pa_id'];
                                                            $pa_name = $row['pa_name'];
                                                    ?>
                                                    <option value="<?php echo $pa_id; ?>">(<?php echo $pa_id; ?>) <?php echo $pa_name; ?>  </option> 
                                                    <?php 
                                                        }
                                                    }
                                                    ?>                                            
                                                </select>
											</div>                                          
										    <div class="col-md-4">
												<label  class="form-label">Image</label>
												<input type="file" class="form-control" name="file" accept="image/png, image/jpeg, image/jpg, image/gif, image/webp" required>
											</div>                                            
											<div class="col-4">
                                                <br>
                                                <center> <button class="btn btn-primary" type="submit" name="submit" ><i class="fa fa-fw fa-lg fa-check-circle"></i> Package Image</button></center>
											</div>
										</form>
								    </div>
							    </div>
							</div>
						</div>
					</div>
                    <!--end breadcrumb-->   
			<!--end page-content-wrapper-->
                                <!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Package Image List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Sl ID</th>
											<th>Package Name</th>								
											<th>Image</th>								
											<th>Create Date</th>
											<th>Action</th>
										
										</tr>
									</thead>
									<tbody>
                                    <?php 
                                       $i=1;
                                       $query = mysqli_query($con,"select * from package_img order by pi_id  desc");
                                       if(mysqli_num_rows($query)>0){
                                           while($row=mysqli_fetch_array($query)){
                                               $pi_id = $row['pi_id'];
                                               $pi_pa_id = $row['pi_pa_id'];
                                               $pi_img = $row['pi_img'];
                                               $pi_date = $row['pi_date'];
                                               $query_package = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `package` WHERE `pa_id`='$pi_pa_id'"));
                                               $pa_name = $query_package['pa_name'];
                                         ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $pa_name; ?></td>
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
</body>

</html>
<?php
	if(isset($_POST['submit']))
    {
        $pa_id = $_POST['pa_id'];
        $file = rand(1000,100000)."-".$_FILES['file']['name'];
        $file_loc = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];
        $folder="../asupport/package/img/";
        
        $allowed_extensions = array("jpg", "jpeg", "png", "gif", "webp", "bmp");
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>alert('Invalid file format. Only JPG, JPEG, PNG, GIF, WEBP and BMP images are allowed.')</script>";
            echo "<script>window.open('package_img.php','_self')</script>";
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
        
        
        $query = "INSERT INTO `package_img`(`pi_pa_id`, `pi_img`) VALUES('$pa_id','$final_file')";
        
        $run_posts = mysqli_query($con,$query);
            
            echo "<script>alert ('Package Image Add Succesfully')</script>";
            echo "<script>window.open('package_img.php','_self')</script>";
        }
	}	
?>
<?php 
if(isset($_GET['del']))
{
    $res=mysqli_query($con, "SELECT * FROM package_img WHERE pi_id =".$_GET['del']);
	$_SERVER=mysqli_fetch_array($res);
	mysqli_query($con, "DELETE FROM package_img WHERE pi_id =".$_GET['del']);
	unlink("../asupport/package/img/".$_SERVER['pi_img']);
	echo "<script>alert('Package Image successfully deleted!')</script>";
	echo "<script>window.open('package_img.php','_self')</script>";
}
?> 