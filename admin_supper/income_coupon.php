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
<!-- Spooner Id Find Code Started  -->	
<script>
function showUser(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getuser.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>
<!-- Spooner Id Find Code End  -->
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
								<h4 class="mb-0">User Coupon Details</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="examples" class="table table-striped table-bordered" style="width:100%">
									<thead>
                                    <tr>
										<th>Sl.No</th>
										<th>UserID</th>
										<th>Amount</th>
										<th>Code</th>
										<th>Request Date</th>
										<th>Approved Date</th>
									  </tr>
									</thead>
									 <tbody>
                                        <?php 
                                        $i=1;
                                        $query = mysqli_query($con,"select * from capping_request where cr_status='Approved' order by cr_id desc");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                                $id = $row['cr_id'];
                                                $user_id = $row['cr_userid'];
                                                $cr_capping_amount	 = $row['cr_capping_amount'];
                                                $cr_amount = $row['cr_amount'];
                                                $cr_request_date = $row['cr_request_date'];
                                                $cr_approved_date = $row['cr_approved_date'];
                                                
                                        ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $user_id; ?></td>
                                        <td><?php echo $cr_amount; ?></td>
                                        <td><?php echo $cr_capping_amount; ?></td>
                                        <td><?php echo $cr_request_date; ?></td>
                                        <td><?php echo $cr_approved_date; ?></td>
                                        
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
            <!--start breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Public Coupon Details</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
                                    <tr>
										<th>Sl.No</th>
										<th>Userid</th>
										<th>Amount</th>
										<th>Code</th>
										<th>Date</th>
									  </tr>
									</thead>
									 <tbody>
                                        <?php 
                                        $i=1;
                                        $query = mysqli_query($con,"select * from coupan_tra");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                                $id = $row['ct_id'];
                                                $ct_userid = $row['ct_userid'];
                                                $ct_amount	 = $row['ct_amount'];
                                                $ct_dode = $row['ct_dode'];
                                                $ct_date = $row['ct_date'];
                                                
                                        ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $ct_userid; ?></td>
                                        <td><?php echo $ct_amount; ?></td>
                                        <td><?php echo $ct_dode; ?></td>
                                        <td><?php echo $ct_date; ?></td>
                                        
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
        $(document).ready(function () {
			//Default data table
			$('#examples').DataTable();
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
