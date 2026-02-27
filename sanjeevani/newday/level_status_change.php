 <?php 
 date_default_timezone_set('Asia/Kolkata');

include('../user_menu/database_connect.php');
	
    $i=1;
    $today_date = date("Y-m-d");
    $query = mysqli_query($con,"select * from order_book_label order by o_id desc");
    if(mysqli_num_rows($query)>0){
    	while($row=mysqli_fetch_array($query)){
    		$o_id = $row['o_id'];
    		$user_id = $row['o_spo'];
    		$o_package = $row['o_pac_id'];
    		$o_amount = $row['o_amount'];
    		$o_pay_status = $row['o_status'];
    		$o_pay_type = $row['o_pay_type'];
    		$o_tran_id = $row['o_payment_id'];
    		$o_date = $row['o_date'];
    		echo $count_order = mysqli_num_rows(mysqli_query($con,"SELECT * FROM `order_book` WHERE `o_userid`='$user_id' AND `o_amount`='$o_amount'"));
    			echo "<br>";
            if($count_order>0){
    		    $query_deactive = mysqli_query($con,"UPDATE `order_book_label` SET `o_status`='Credit' WHERE `o_spo`='$user_id' AND `o_amount`='$o_amount'");
    		    echo "Credit";
    		    echo "<br>";
    		}else{
    		   $query_deactive = mysqli_query($con,"UPDATE `order_book_label` SET `o_status`='Deactive' WHERE `o_spo`='$user_id' AND `o_amount`='$o_amount'");
    		   echo "Deactive";
    		}
    	}
        
    }
    ?>