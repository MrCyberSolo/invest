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
				
					<!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Blog List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Blog ID</th>
											<th>Title</th>
											<th>Short</th>										
											<th>Create Date</th>
											<th>Action</th>
										
										</tr>
									</thead>
									<tbody>
                                    <?php 
                                        $i=1;
                                        $query = mysqli_query($con,"select * from blog order by b_id desc");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                               
                                                $blog_id= $row['b_id'];
                                                $blog_title = $row['b_title'];
                                                $blog_image = $row['b_image'];
                                                $blog_short = $row['b_details'];
                                                $blog_create_date = $row['b_create_date'];
                                         ?>
                                    <tr>
                                        <td><a href="#"><?php echo $blog_id; ?></a></td>
                                        <td><a href="#"><?php echo $blog_title; ?></a></td>
                                        <td><a href="#"><?php echo substr($blog_short, 0, 60); ?></a></td>
                                       
                                        
                                        <td><?php echo $blog_create_date; ?></td>
                                        
                                        <td>
											<a href="blog_view.php?pac=<?php echo $blog_id; ?>" class="btn btn-primary"><i class="fa fa-fw fa-lg fa-check-circle"></i> View</a>
											<a href="?delete_id=<?php echo $blog_id; ?>" class="btn btn-danger"><i class="lni lni-close"></i> Deleted</a>
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
<?php 
if(isset($_GET['delete_id']))
{
    $id = $_GET['delete_id'];
	$data_del = mysqli_query($con, "DELETE FROM blog WHERE b_id='$id'");
	echo "<script>alert('Blog Successfully deleted!')</script>";
	echo "<script>window.open('blog.php','_self')</script>";	
}
?>