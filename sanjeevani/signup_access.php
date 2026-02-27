<?php
session_start();
include('user_menu/database_connect.php');

function pin_generate()
{
    global $con;
    $generated_pin = rand(10000,99999);
    $query = mysqli_query($con,"select * from user where email = '$generated_pin'");
    if(mysqli_num_rows($query)>0)
    {
        pin_generate();
    }
    else
    {
        return $generated_pin;
    }     
}
   $email = pin_generate();

?>
<?php
//User cliced on join
if(isset($_POST['register'])){		
	$side='';	
	$under_userids = mysqli_real_escape_string($con,$_POST['under_userid']);
	$under_userid= substr($under_userids, 0);
	$mobile = mysqli_real_escape_string($con,$_POST['mobile']);
	$password = mysqli_real_escape_string($con,$_POST['password']);   
	$conpassword = mysqli_real_escape_string($con,$_POST['password']); 	
	$vercode = mysqli_real_escape_string($con,$_POST['vercode']);
	//$name = mysqli_real_escape_string($con,$_POST['name']);
	$flag = 0;
	
	if($email!='' && $mobile!='' && $under_userid!='' ){
		//User filled all the fields.
		
			if(email_check($email)){			
				//Email is ok
				if(!email_check($under_userid)){
					//Under userid is ok
						if($password == $conpassword ){	
                                //mobile number check	
                                if(mobile_check($mobile)){
                                    //OTP is ok
                						$query_opt = mysqli_fetch_array(mysqli_query($con,"select * from mobileotp where otp_status='Active' AND otp_mobile='$mobile' order by `otp_id` desc LIMIT 0,1 "));
                		               
                		                $otp_verfied = $query_opt['otp_number'];
                		                if($vercode == $otp_verfied ) 
                    	                {
                    	                    mysqli_query($con,"update mobileotp set otp_status ='Deactive' where otp_mobile ='$mobile'");
                                	        $flag=1;  
                    	                }else{
                    	                     //echo "<script>alert('Please Check Opt , Try Again');</script>";
                							// echo "<script>window.open('register.php?mobile=".$mobile."','_self')</script>";
                							  header("Location: register.php?mobile=".$mobile."&&error=6");
                                                exit();
                    	                }
                                    }
                                else{
                                //echo '<script>alert("Mobile Number Already Register, Pleae Change Mobile Number");window.location.assign("signup.php");</script>';
                               // echo "<script>alert('This New Password and Confirm Password Not Match');</script>";
                                //echo "<script>window.open('register.php?mobile=".$mobile."','_self')</script>";
                                 header("Location: register.php?mobile=".$mobile."&&error=5");
                                exit();
                                }  	                
        					 }
						else{
							//echo '<script>alert("This New Password and Confirm Password Not Match");window.location.assign("register.php");</script>';
							//echo "<script>alert('This New Password and Confirm Password Not Match');</script>";
							echo "<script>window.open('register.php?mobile=".$mobile."','_self')</script>";
						}
					}
				else{
					//check under userid
				//	echo '<script>alert("Invalid Under userid.");window.location.assign("register.php");</script>';
				//	echo "<script>alert('Invalid Under userid.');</script>";
				//	echo "<script>window.open('register.php?mobile=".$mobile."','_self')</script>";
					 header("Location: register.php?mobile=".$mobile."&&error=3");
                        exit();
				}
			}
			else{
				//check email
				    //echo '<script>alert("This user id already availble.");window.location.assign("register.php");</script>';
				echo "<script>alert('This user id already availble.');</script>";
				echo "<script>window.open('register.php?mobile=".$mobile."','_self')</script>";
			}
		}
	
	else{
		//check all fields are fill
		    //echo '<script>alert("Please fill all the fields.");window.location.assign("register.php");</script>';
		//echo "<script>alert('Please fill all the fields.');</script>";
		//echo "<script>window.open('register.php?mobile=".$mobile."','_self')</script>";
		  header("Location: register.php?mobile=".$mobile."&&error=1");
        exit();
	}
	
	//Now we are heree
	//It means all the information is correct
	//Now we will save all the information
	if($flag==1)
	{
		//Insert into User profile
		$query = mysqli_query($con,"insert into user(`name`,`email`,`email1`,`password`,`withdrawal_passwrod`,`mobile`,`under_userid`,`join_date`) values('$name','$email','$email1','$password','$password','$mobile','$under_userid',Now())");
		
		//Inset into Icome
		$query = mysqli_query($con,"insert into income (`userid` , `current_bal`) values('$email','0')");	
		
		// Signup Bonus 68
		//$query = mysqli_query($con,"INSERT INTO `cashback`(`ca_userid`, `ca_order_id`,`ca_amount`, `ca_type`, `ca_status`) VALUES ('$email','$email','68','Register','Show')");
		//$query = mysqli_query($con,"INSERT INTO `transaction`(`t_userid`, `t_pay`, `t_details`, `t_amount`, `t_type`, `t_date`) VALUES ('$email','$email','Singup','68','Credit',NOW())");
		//Invite User earn 6
		/* $refer_count= mysqli_num_rows(mysqli_query($con, "select * from cashback where ca_userid ='$under_userid' AND ca_type='Invite Register'"));
		if($refer_count <=30){
		 $query = mysqli_query($con,"INSERT INTO `cashback`(`ca_userid`, `ca_order_id`,`ca_amount`, `ca_type`) VALUES ('$under_userid','$email','6','Invite Register')");
		} */
		// Auto Login After Successfully Registration
		$query = mysqli_query($con,"select * from user where email='$email' and password='$password'");
			if(mysqli_num_rows($query)>0)
			{
			   //  $query_order_book = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE mobile='$mobile' and password='$password'"));
		       // $customer_email = $query_order_book['email'];
		       // $_SESSION['userid'] = $customer_email;
				// Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["username"] = $email; 
				echo '<script>window.location.assign("me.php");</script>';
				 
            
			}		
	}
}
?><!--/join user-->
<?php 
//functions Code checker all condition here

// UserId Verfied Check 
function email_check($email)
{
	global $con;
	$query =mysqli_query($con,"select * from user where email='$email' AND user_type='User' ");
	if(mysqli_num_rows($query)>0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function getUnderId($userid)
{
	global $con;
	$query = mysqli_query($con,"select * from user where email='$userid'");
	$result = mysqli_fetch_array($query);
	return $result['under_userid'];
}

function getUnderIdPlace($userid)
{
	global $con;
	$query = mysqli_query($con,"select * from user where email='$userid'");
	$result = mysqli_fetch_array($query);
	return $result['side'];
}

function mobile_check($mobile)
{
	global $con;
	$query =mysqli_query($con,"select * from user where mobile='$mobile'");
	if(mysqli_num_rows($query)>0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

?>