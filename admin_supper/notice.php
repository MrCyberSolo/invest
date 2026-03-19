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
								<h4 class="mb-0">Notice Create</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
											<div class="col-md-12">
												<label  class="form-label">Message</label>
											    <textarea class="form-control" name="reply"></textarea>
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
								<h4 class="mb-0">Notice List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Notice ID</th>
											<th>Title</th>									
											<th>Create Date</th>
											<th>Action</th>
										
										</tr>
									</thead>
									<tbody>
                                    <?php 
                                        $i=1;
                                        $query = mysqli_query($con,"select * from notice order by id desc");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                               
                                                $id= $row['id'];
                                                $message = $row['message'];
                                                $date = $row['date'];
                                         ?>
                                    <tr>
                                        <td><a href="#"><?php echo $id; ?></a></td>
                                        <td><a href="#"><?php echo $message; ?></a></td>
                                        <td><a href="#"><?php echo $date; ?></a></td>                                        
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
        $reply = mysqli_real_escape_string($con,$_POST['reply']);
	    $run_posts = mysqli_query($con,"INSERT INTO notice(message) VALUES('$reply')");
		    echo "<script>alert ('Notice Updated!')</script>";
		    echo "<script>window.open('notice.php','_self')</script>";	
	}	
?>
<?php 
if(isset($_GET['del']))
{
    $id = $_GET['del'];
	$data_del = mysqli_query($con, "DELETE FROM notice WHERE id='$id'");
	echo "<script>alert('Notice Successfully deleted!')</script>";
	echo "<script>window.open('notice.php','_self')</script>";	
}
?>