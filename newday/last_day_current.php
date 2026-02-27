<?php
include('../user_menu/database_connect.php');
	
	 //update income
	 
	$query22 = mysqli_query($con,"select * from income");
	if(mysqli_num_rows($query22)>0)
	{
		while($income_data=mysqli_fetch_array($query22))
		{
			
			$userid = $income_data['userid'];
			$new_today_bal = $income_data['current_bal'];
			$new_current_bal = $income_data['last_day_interest'];
			
			$new_total_current_bal = $new_today_bal;	
		//	$new_total_current_bal = $new_today_bal + $new_current_bal;	
			//echo $new_total_current_bal; 
			$query33 = mysqli_query($con,"update income set last_day_interest= '$new_total_current_bal' where userid='$userid'");
			//$query33_updated = mysqli_query($con,$query33);
		}		
	}
	
	
	
?>
