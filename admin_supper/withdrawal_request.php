<?php
include('includes/connect.php');
include('includes/check-login.php');
$userid = $_SESSION['userid'];
$query_setting = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `settings`"));
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
								<h4 class="mb-0">Fund Request Paid List</h4>
							</div>
							<hr/>
							<div class="table-responsive">
								<table id="example" class="table table-striped table-bordered" style="width:100%">
								<thead>
                                <tr>
                                        <th>Request ID</th>
										<th>Userid</th>
										<th>Name</th>
										<th>Bank</th>
										<th>A/c No</th>
										<th>IFSC</th>
										<!--<th>UPI ID</th> -->
										<th>Pay Amount </th>
										<th>Payment Date</th>
											<!--<th>Status</th>-->
									<th>Online</th>
									<th>Offline</th>
									<th>Status</th>
									  </tr>
									</thead>
									 <tbody>
                                    <?php 
                                    $i=1;
                                    $query = mysqli_query($con,"select * from income_received INNER JOIN  user on income_received.userid = user.email where income_received.status='Pending' order by income_received.id desc");
                                    if(mysqli_num_rows($query)>0){
                                        while($row=mysqli_fetch_array($query)){
                                            $request_id = $row['id'];
                                            $user_id = $row['userid'];
                                                $amount = $row['amount'];
                                            $donation = $row['donation'];
                                            $tds = $row['tds'];
                                            $f_amount = $row['f_amount'];
                                            $trno = $row['trno'];
                                            $name = $row['name'];
                                            $bank_name = $row['bank_name'];
                                            $account_no = $row['account_no'];
                                            $ifsc_code = $row['ifsc_code'];
                                            $pay_date = $row['date'];
                                            $user_upi = $row['user_upi'];
                                            //$bank_account_name = $row['bank_account_name'];
                                            $bank_account_name = $row['name'];
                                            
                                            
                                    ?>
                            
                                <tr>
                                    <td><?php echo $request_id; ?></td>
                                    <td><?php echo $user_id; ?></td>
                                    <td><?php echo $bank_account_name; ?></td>
                                    <td><?php echo $bank_name; ?></td>
                                    <td><?php echo $account_no; ?></td>
                                    <td><?php echo $ifsc_code; ?></td>
                                <!-- <td><?php echo $user_upi; ?></td> -->
                                    <td><?php echo $f_amount; ?></td>
                                    <td><?php echo $pay_date; ?></td>
                                    <td>
                                        <form action="?online_pay_id=<?php echo $request_id ?>" method="post"><input type="submit" name="online_pay" class="btn btn-info" value="Online"></form>
                                        <!--<form action="?online_pay_status_id=<?php echo $request_id ?>" method="post"><input type="submit" name="online_pay_status" class="btn btn-success" value="Status Check"></form>-->
                                    </td>
                                    <td><form action="?paid_id=<?php echo $request_id ?>" method="post"><input type="submit" name="paid" class="btn btn-success" value="Offline"></form></td>
                                    <td>
										<form action="withdrawal_remark.php?reject_id=<?php echo $request_id ?>" method="post"><input type="submit" name="reject" class="btn btn-danger" value="Reject"></form>
                                    	
									</td>
                                  
                                </tr>
									<?php
									$i++;
								}
							}
							else{
							?>
                            	<tr>
                                	<td colspan="10">There are no message yet.</td>
                                </tr>
                            <?php
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
	<script src="assets/js/app.js"></script>
</body>

</html>
<?php if(isset($_POST['paid']))
{	
    $update_id = $_GET['paid_id'];
    $query = mysqli_query($con,"update income_received set status='Paid' where id='$update_id'");
    echo '<script>alert("Payment Paid Updated!");window.location.assign("withdrawal_request.php");</script>';
}
?>
<?php 
if(isset($_POST['online_pay']))
{	
    $update_id = $_GET['online_pay_id'];
    //$update_id = '1';
    $mch_transferId=$update_id;
    $query_payment = mysqli_fetch_array(mysqli_query($con,"select * from income_received INNER JOIN  user on income_received.userid = user.email where income_received.id ='$update_id' order by income_received.id desc"));
	$mobile_numbers = $query_payment['mobile'];
    $famount = $query_payment['f_amount'];
    $amounts = (round($famount));
    $beneficiary_names = $query_payment['name'];
   // $EMAIL = $query_payment['email1'];
    $EMAIL = 'info@sanjeevanifarming.com';
    $account_numbers = $query_payment['account_no'];
    $ifscs = $query_payment['ifsc_code'];
    $USERID = $query_payment['email'];
    $bank_name = $query_payment['bank_name'];
    $bank_list = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `bank_list` where bl_name='$bank_name'"));
    // ====== CONFIG ======
$api_url     = "https://api.watchglb.com/pay/transfer";
$merchant_id = "100888127";
$secret_key  = $query_setting['s_with_api'];   // FIXED


// ====== PAYOUT DATA ======
//$mch_transferId   = time() . rand(1000,9999);
$transfer_amount  = $famount;

$bank_code        = $bank_list['bl_ifsc'];   // Axis Code
$remark           = $ifscs; // IFSC
$receive_name     = $beneficiary_names;
$receive_account  = $account_numbers;
$back_url         = "https://sanjeevanifarming.com/with/payout-callback.php";


// ====== REQUEST DATA ======
$data = [
    "sign_type"        => "MD5",
    "mch_id"           => $merchant_id,
    "mch_transferId"   => $mch_transferId,
    "transfer_amount"  => $transfer_amount,
    "apply_date"       => date("Y-m-d H:i:s"),
    "bank_code"        => $bank_code,
    "receive_name"     => $receive_name,
    "receive_account"  => $receive_account,
    "remark"           => $remark,
    "back_url"         => $back_url,
];


// ===================== SIGN GENERATE =====================
$signData = $data;
unset($signData['sign'], $signData['sign_type']);
$signData = array_filter($signData, fn($v) => $v !== "");
ksort($signData);

$signStr = "";
foreach ($signData as $k => $v) {
    $signStr .= $k . "=" . $v . "&";
}
$signStr .= "key=" . $secret_key;

$data["sign"] = md5($signStr);


// ===================== SEND REQUEST =====================
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($ch);
curl_close($ch);


// ===================== DECODE RESPONSE =====================
$responseArr = json_decode($response, true);

// SAVE RESPONSE VALUES IN VARIABLES
$respCode        = $responseArr['respCode']        ?? '';
$errorMsg        = $responseArr['errorMsg']        ?? '';
$tradeNo         = $responseArr['tradeNo']         ?? '';
$tradeResult     = $responseArr['tradeResult']     ?? '';
$merTransferId   = $responseArr['merTransferId']   ?? '';
$transferAmount  = $responseArr['transferAmount']  ?? '';
$applyDate       = $responseArr['applyDate']       ?? '';


// ===================== SAVE IN DATABASE =====================
$sql = "INSERT INTO payout_log (
    mch_transferId,
    amount,
    resp_code,
    trade_no,
    trade_result,
    error_msg,
    response_json
) VALUES (
    '$mch_transferId',
    '$transfer_amount',
    '$respCode',
    '$tradeNo',
    '$tradeResult',
    '$errorMsg',
    '".mysqli_real_escape_string($con, $response)."'
)";
mysqli_query($con, $sql);
 if($respCode=='SUCCESS'){
    mysqli_query($con,"UPDATE `income_received` SET `status`='Paid',`trno`='$tradeNo',`msg`='$errorMsg' WHERE `id`='$mch_transferId'");
}else{
    mysqli_query($con,"UPDATE `income_received` SET `trno`='$tradeNo',`msg`='$errorMsg' WHERE `id`='$mch_transferId'");
}
// ===================== SHOW ON SCREEN =====================
/*echo "<h3>PAYOUT API RESPONSE</h3>";
echo "<pre>";
echo "REQUEST DATA:\n";
print_r($data);

echo "\nRESPONSE DATA:\n";
print_r($responseArr);
echo "</pre>";*/
$message = "Payment Paid Updated! $respCode or $errorMsg";
echo "<script>alert('$message'); window.location='withdrawal_request.php';</script>";

}
?>

