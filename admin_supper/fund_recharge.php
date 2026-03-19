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
								<h4 class="mb-0">Recharge Fund Transfer User</h4>
							</div>
							<hr/>
							<div class="table-responsive">
                            <div class="card">
								<div class="card-body">
                              
									<div class="p-4 border rounded">
										<form class="row g-3 needs-validation"  action="" method="post" enctype="multipart/form-data">
										      
                                            <div class="col-md-4">
												<label class="form-label">Userid</label>
                                                <input type="text" class="form-control" name="userid" onchange="showUser(this.value)">
                                                <br><div id="txtHint"></div>
											</div>	
											
											<div class="col-md-4">
												<label  class="form-label">Amount</label>
												<input type="text" class="form-control" name="amount"   >
											</div>
                                            <div class="col-md-4">
												<label class="form-label">Payment Type</label>
												<select class="form-control" name="pa_type" id="exampleSelect1" required>
                                                    <option selected="selected" value=" " >-- Select Payment Type  -- </option>
                                                    <option value="Credit">Credit</option>  
                                                    <option value="Debit">Debit</option>                                          
                                                </select>
											</div>
											<div class="col-12">
                                                <center> <button class="btn btn-primary" type="submit" name="bal_pay" ><i class="fa fa-fw fa-lg fa-check-circle"></i>Transfer Amount</button></center>
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
								<h4 class="mb-0">Admin Fund Credit Transfer</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="examples" class="table table-striped table-bordered" style="width:100%">
									<thead>
                                    <tr>
										<th>PayID</th>
										<th>UserID</th>
										<th>Amount</th>
										<th>Status</th>
										<th>Pay type</th>
										<th>Pay user</th>
										<th>Payment Date</th>
									  </tr>
									</thead>
									 <tbody>
                                        <?php 
                                        $i=1;
                                        $query = mysqli_query($con,"select * from fpay where send_userid='Admin' AND pay_type='Credit' order by id desc");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                                $id = $row['id'];
                                                $user_id = $row['userid'];
                                                $amount = $row['amount'];
                                                $status = $row['status'];
                                                $pay_type = $row['pay_type'];
                                                $date = $row['date'];
                                                $pay_user = $row['pay_user'];
                                        ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $user_id; ?></td>
                                        <td><?php echo $amount; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td><?php echo $pay_type; ?></td>
                                        <td><?php echo $pay_user; ?></td>
                                        <td><?php echo $date; ?></td>
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
            <!--end breadcrumb-->
					<div class="card">
						<div class="card-body">
							<div class="card-title">
								<h4 class="mb-0">Admin Fund Debit Transfer</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
									<thead>
                                    <tr>
										<th>PayID</th>
										<th>UserID</th>
										<th>Amount</th>
										<th>Status</th>
										<th>Pay type</th>
										<th>Pay user</th>
										<th>Payment Date</th>
									  </tr>
									</thead>
									 <tbody>
                                        <?php 
                                        $i=1;
                                        $query = mysqli_query($con,"select * from fpay where send_userid='Admin' AND pay_type='Debit' order by id desc");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                                $id = $row['id'];
                                                $user_id = $row['userid'];
                                                $amount = $row['amount'];
                                                $status = $row['status'];
                                                $pay_type = $row['pay_type'];
                                                $date = $row['date'];
                                                $pay_user = $row['pay_user'];
                                        ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $user_id; ?></td>
                                        <td><?php echo $amount; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td><?php echo $pay_type; ?></td>
                                        <td><?php echo $pay_user; ?></td>
                                        <td><?php echo $date; ?></td>
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
<?php
if(isset($_POST['bal_pay']))
{
     $amount = $_POST['amount'];
     $pay_type = $_POST['pa_type'];
     $transfer_user = mysqli_real_escape_string($con,$_POST['userid']);
     //$userids= substr($under_userids, 3);
    if(!email_check($transfer_user)){  
        if($pay_type == 'Credit' ) 
        {
            mysqli_query($con,"INSERT INTO `fpay`(`userid`,`send_userid`,`amount`,`status`,`pay_type`,`pay_user`,`date`) values('$transfer_user','Admin','$amount','Accept','$pay_type','$userid',Now())");
            mysqli_query($con,"UPDATE `income` SET `fran_bal`=`fran_bal`+$amount WHERE `userid`='$transfer_user'");
                echo "<script>alert ('Wallet Payment has been Send!')</script>";
                echo "<script>window.open('fund_recharge.php','_self')</script>";
        }
        else
        {
            mysqli_query($con,"INSERT INTO `fpay`(`userid`,`send_userid`,`amount`,`status`,`pay_type`,`pay_user`,`date`) values('$transfer_user','Admin','$amount','Accept','$pay_type','$userid',Now())");
            mysqli_query($con,"UPDATE `income` SET `fran_bal`=`fran_bal`-$amount WHERE `userid`='$transfer_user'");
                echo "<script>alert ('Wallet Payment has been Send!')</script>";
                echo "<script>window.open('fund_recharge.php','_self')</script>";         
        }
    }
    else
    {
        echo '<script>alert("This user id Not availble. Please Check Userid");</script>';
        echo "<script>window.open('fund_recharge.php','_self')</script>"; 
    }
} 
function email_check($transfer_user){
     global $con;
     $query =mysqli_query($con,"select * from user where email='$transfer_user'");
     if(mysqli_num_rows($query)>0){
         return false;
     }
     else{
         return true;
     }
 }
     ?>