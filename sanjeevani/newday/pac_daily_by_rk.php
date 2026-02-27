<?php

date_default_timezone_set('Asia/Kolkata');

include('../user_menu/database_connect.php');

function user_date_diff($date)
{
	$now = new \DateTime();
	$end_date = new \DateTime($date);
	$difference = $now->diff($end_date);

	return $difference;
}

$query12 = mysqli_query($con,"select * from order_book");
//$query12 = mysqli_query($con,"select * from order_book order by o_id ASC LIMIT 2706,2000");

if(mysqli_num_rows($query12) > 0) {
    
    while($row = mysqli_fetch_array($query12)) {
        $ac_id = $row['o_id'];
		$pack_pur_date = $row['o_date'];
		$ac_package = $row['o_pac_id'];
		$ac_userid = $row['o_userid'];
		$ac_amount = $row['o_amount'];
		$ac_pay_status = $row['o_status'];
		$ac_percentage = $row['o_percentage'];
		$o_days = $row['o_days'];
		
		if($ac_pay_status == 'Credit') {
		    
		  $total_days =  mysqli_num_rows(mysqli_query($con, "select * from interest where int_userid='$ac_userid' AND int_acc_id='$ac_id'"));
		    
		    //$total_days = user_date_diff($pack_pur_date)->days;
		    
		    if($total_days >= 0 && $total_days <= $o_days) {
		        
		       // $percentage_amount = ($ac_amount * $ac_percentage) / 100;
		        $percentage_amount = $ac_percentage;
		        
		        $today_date = date('Y-m-d');
		        
		        $query13 = mysqli_query($con,"SELECT * FROM `interest` WHERE `int_userid` = $ac_userid AND `int_acc_id` = $ac_id AND `int_date` = '". $today_date ."' ORDER BY `int_id` DESC");
		        
		        if(mysqli_num_rows($query13) == 0) {
		            
		            echo 'User - '. $ac_userid .', Amt - '. $ac_amount .', Interest Amt - '. $percentage_amount .'<br>';
		            
		            $query_interest_create = mysqli_query($con,"INSERT INTO `interest`(`int_userid`, `int_acc_id`, `int_amount`, `int_date`) values('$ac_userid','$ac_id','$percentage_amount','$today_date')");
		            
		        }
		    }
		    
		}
    }
}

?>