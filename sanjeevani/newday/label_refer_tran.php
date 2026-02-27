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

$query12 = mysqli_query($con,"SELECT * FROM `order_book_label` ORDER BY `o_id` ASC LIMIT 6950,905'");

if(mysqli_num_rows($query12) > 0) {
    
    while($row = mysqli_fetch_array($query12)) {
        $ac_id = $row['o_id'];
		$pack_pur_date = $row['o_date'];
		$ac_package = $row['o_pac_id'];
		$ac_userid = $row['o_spo'];
		$ac_amount = $row['o_amount'];
		$ac_pay_status = $row['o_status'];
		$ac_percentage = $row['o_percentage'];
		$o_days = $row['o_days'];
		
		if($ac_pay_status == 'Credit') {
		 //$total_days =  mysqli_num_rows(mysqli_query($con, "select * from interest_spo where int_userid='$ac_userid'"));
		 $total_days = user_date_diff($pack_pur_date)->days;
		    
		    if($total_days >= 0 && $total_days <= $o_days) {
		        
		        //$percentage_amount = ($ac_amount * $ac_percentage) / 100;
		        $percentage_amount = $ac_percentage;
		        
		      	$today_date = date('Y-m-d');
		        
		        $query13 = mysqli_query($con,"SELECT * FROM `interest_label` WHERE `int_userid` = $ac_userid AND `int_acc_id` = $ac_id AND `int_date` = '". $today_date ."' ORDER BY `int_id` DESC");
		        
		        if(mysqli_num_rows($query13) == 2) {
		            
		            echo 'User - '. $ac_userid .', Amt - '. $ac_amount .', Interest Amt - '. $percentage_amount .'<br>';
		            
		            $label_data_detlet = mysqli_query($con,"DELETE FROM `interest_label` WHERE `int_id`='$ac_id'");
		            //$label_data_detlet = mysqli_query($con,"DELETE FROM `transaction` WHERE `int_id`='$ac_id'");
				   
		
		          
                    //update income
                	$query22 = mysqli_query($con,"select * from income where userid='$ac_userid' order by id desc");
                	if(mysqli_num_rows($query22)>0)
                	{
                		while($income_data=mysqli_fetch_array($query22))
                		{
                			$new_current_bal = $income_data['current_bal']- $percentage_amount;
                			$new_total_bal = $income_data['total_bal']- $percentage_amount;
                		}		
                		$query33 = mysqli_query($con,"update income set  current_bal='$new_current_bal', total_bal='$new_total_bal' where userid='$ac_userid' ");
                		//$query33_updated = mysqli_query($con,$query33);
                	}   
                	            
		            
		        }
		    }
		    
		}
    }
}

?>