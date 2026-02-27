<?php

date_default_timezone_set('Asia/Kolkata');

include('../includes/connect.php');

function user_date_diff($date)
{
	$now = new \DateTime();
	$end_date = new \DateTime($date);
	$difference = $now->diff($end_date);

	return $difference;
}
	$today_date = date('Y-m-d');
    $today_current_date = date('Y-m-d h:i:s');
    
 $query_interest_create = mysqli_query($con,"INSERT INTO `interest_spo`(`int_userid`, `int_acc_id`, `int_amount`, `int_date`, `int_up_date`) values('10001','10001','50',Now(),'$today_current_date')");
?>