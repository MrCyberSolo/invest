<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];

$userid = $userid_access;
$order_id = $_SESSION['order_id'];
$pacid = $_SESSION['pacid'];

    // Label Income Calculation Start Here
		insert_in_level_income($userid,$order_id,$pacid);

		function insert_in_level_income($userid,$order_id,$pacid){
			global $con;
			$i=0;
			$placember_id = $userid;
			$order_ids = $order_id;
			
			while ($i < 3 && $placember_id!=10000 )
			{	
				$placember_id =find_my_sponsor_id_user($placember_id);
				$income_refer =[10,5,2];
				//$income =[15,2,1];
			//	if(check_active_or_not($placember_id))
			//	{
					$package_table = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `package` WHERE `pa_id`='$pacid'"));
						$pa_amount = $package_table['pa_amount'];		
						$pa_com_amount = $package_table['pa_com_amount'];		
						$pa_day = $package_table['pa_day'];		
						$o_percentage =  $pa_com_amount * $income_refer[$i]/100;
    						$query57 = mysqli_query($con,"INSERT INTO `order_book_label`(`o_sep_order_id`, `o_userid`, `o_spo`, `o_user_type`,`o_pac_id`, `o_amount`, `o_percentage`, `o_days`, `o_date`)  values('$order_ids','$userid','$placember_id','$i','$pacid','$pa_amount','$o_percentage','$pa_day',Now())");
    					//$refer_income =  $pa_amount * $income[$i]/100;
    					//mysqli_query($con,"UPDATE `income` SET `current_bal`=`current_bal`+$refer_income WHERE `userid`='$placember_id'");
					    //mysqli_query($con,"insert into income_tras (`userid`, `payid`, `order_id`, `amount`, `level`) values('$placember_id', '$userid','$order_ids','$refer_income','$i')");
					    //mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_pay`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$placember_id','$userid','Product Refer','$refer_income','Credit',Now())");
			//	}
				++$i;
			}
			
		}
		
		function find_my_sponsor_id_user($placember_id)
		{
			global $con;
			$data_find_sponser=mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$placember_id'"));
			return $data_find_sponser['under_userid'];
		}
		function check_active_or_not($placember_id)
		{
			global $con;
			$data=mysqli_fetch_array(mysqli_query($con,"SELECT `status` FROM `user` WHERE `email`='$placember_id'"));
			return $data['status'];
		}
		// Label Income Calculation End Here
		// echo "<script>alert ('Success')</script>";
			//echo "<script>window.open('myproduct.php','_self')</script>";
?>
<script>
         const popupMessage = document.createElement('regtoast');
    popupMessage.textContent = 'Success !';
    popupMessage.classList.add('popup');
    document.body.appendChild(popupMessage);
    
    setTimeout(
        function(){
            window.location = "myproducts.php" 
        },
    3000);
    </script>
	
