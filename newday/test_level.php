<?php 
include('../includes/connect.php');
 $query13 = mysqli_query($con,"SELECT * FROM `interest_label` WHERE `int_userid` ='90684' AND `int_acc_id` ='6951' AND `int_date` ='2023-09-02' ORDER BY `int_id` DESC;");
		        
		        if(mysqli_num_rows($query13) == 2) {
		            
		            
		            $label_data_detlet = mysqli_query($con,"DELETE FROM `interest_label` WHERE `int_id`='6951'");
		        }else{
		            echo "2";
		        }
?>