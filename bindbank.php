<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
$get_user = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$userid_access'"));
	$name = $get_user['name'];
	$mobile = $get_user['mobile'];
    $withdrawal_passwrod = $get_user['withdrawal_passwrod'];
    $bank_name = $get_user['bank_name'];
    $account_no = $get_user['account_no'];
    $ifsc_code = $get_user['ifsc_code'];
	date_default_timezone_set('Asia/Kolkata');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bind bank account</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --yellow:#FFCE82;
        --aqua-blue:#009a5f;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule{
        max-width: 641px;
        margin: auto;

       }

       header{
       
        background-color:#005a36;
       }

       
       .appBody{
        padding: 3.2rem 0;
       }

   /* body{
         background-color: #009a5f;
       } */


       /* input-section================================  */
       

.inputBox .inner{
  display: flex; 
  border-bottom:1px solid #aeaeae28;
  padding: 10px 0;
}

.inputBox p{
  width: 40%;
  margin-right: 1rem;
  color: #000;
  display:flex;
  align-items:center;
}
.inputBox .right{
  width: 100%;
}
.inputBox input{
  border: 0;
  width: 100%;
  background-color:transparent;
  color:#000;
}
.popup {
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background-color: rgba(0, 0, 0, 0.7);
	color: white;
	padding: 20px;
	border-radius: 10px;
	text-align: center;
}
    
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container mt-4">
  <header class="row fixed-top">
        <div class="text-white py-3 ">
            <div class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <a href="javascript:history.back()" class="nav-link left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6>Bind bank account</h6>

                <h6 class="px-3"></h6>
        </div>
        </div>

  </header>

  <div class="row appBody px-3">
    <div class="col-12  input-section px-4 bg-white rounded rounded--3 p-3">
    <?php
            $query1 =  mysqli_num_rows(mysqli_query($con, "SELECT * FROM user WHERE email ='$userid_access' AND ifsc_code=''"));
             
            if($query1 >=1)           
            {
        ?>
    <form  action="" method="post" enctype="multipart/form-data">   
    <div class="inputBox">
    <div class="inner">
      <p>Realname</p>
      <div class="right">
        <input type="text" reuired name="name" class="form-control" placeholder="Please enter the name code">
      </div>
    </div>
    <!--<div class="inner">
      <p>Bank</p>
      <div class="right">
          
        <select name="bank_name" reuired style=" max-width:200px;">
        <option value=""></option>
        <?php					
            $query = mysqli_query($con,"select * from bank_list");
            if(mysqli_num_rows($query)>0)
            {
                while($row=mysqli_fetch_array($query))
                {
                    $bank_name = $row['bl_name'];
          ?>
          <option value="<?php echo $bank_name; ?>"><?php echo $bank_name; ?></option>
          <?php }} ?>
        </select>
      </div>    
    </div>-->
    <div class="inner">
      <p>Bank Name</p>
      <div class="right">
        <input type="text" reuired name="bank_name" class="form-control" placeholder="Please enter the bank Number">
      </div>
    </div>
    <div class="inner">
      <p>Bank Account</p>
      <div class="right">
        <input type="text" reuired name="account_no" class="form-control" placeholder="Please enter the bank account number">
      </div>
    </div>
    <div class="inner">
      <p>IFSC</p>
      <div class="right">
        <input type="text" reuired name="ifsc_code" class="form-control" placeholder="Please enter the IFSC code">
      </div>
    </div>
    </div>      
        <div class="inputBox-three mt-3">
          <div class="sub-btn px-3 d-grid mt-3">
            <button class="btn" name="user_update"  style="background: #000000; color: white; border-radius: 30px; padding: 10px; ;;">Submits</button>
          </div>   
        </div>
    </div>
  </div>
  </form>
  <?php } else{ ?>
    <form  action="" method="post" enctype="multipart/form-data">   
    <div class="inputBox">
    <div class="inner">
      <p>Realname</p>
      <div class="right">
        <input type="text" reuired name="name" class="form-control" value="<?php echo $name; ?>" >
        
      </div>
    </div>
    <div class="inner">
      <p>Bank</p>
      <div class="right">
      <input type="text" reuired name="bank_name" class="form-control" value="<?php echo $bank_name; ?>" >
      </div>    
    </div>
    <div class="inner">
      <p>Bank Account</p>
      <div class="right">
        <input type="text" reuired name="account_no" class="form-control" value="<?php echo $account_no; ?>" >
      </div>
    </div>
    <div class="inner">
      <p>IFSC</p>
      <div class="right">
        <input type="text" reuired name="ifsc_code" class="form-control" value="<?php echo $ifsc_code; ?>" >
      </div>
    </div>
    </div>      
        <div class="inputBox-three mt-3">
          <div class="sub-btn px-3 d-grid mt-3">
            <button class="btn" name="user_update"  style="background: #000000; color: white; border-radius: 30px; padding: 10px; ;;">Submit</button>
          </div>  
        </div> 
    </div>
  </div>
  </form>
    <?php } ?>
</div>    <!--  end container appCapsule  -->
<!-- =================================== -->
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- amount tab ===================== -->
    <script>
        function openAmt(evt, amtName) {
          var i, inner, amtTablinks;
          inner = document.getElementsByClassName("inner");
          for (i = 0; i < inner.length; i++) {
            inner[i].style.display = "none";
          }
          amtTablinks = document.getElementsByClassName("amtTablinks");
          for (i = 0; i < amtTablinks.length; i++) {
            amtTablinks[i].className = amtTablinks[i].className.replace(" active", "");
          }
          document.getElementById(amtName).style.display = "block";
          evt.currentTarget.className += " active";
        }
        </script>
  </body>
</html>
<?php 	
if(isset($_POST['user_update']))
	{
		$name = $_POST['name'];
		$bank_name = $_POST['bank_name'];
		$account_no = $_POST['account_no'];
		$ifsc_code = $_POST['ifsc_code'];
		if($name!='' OR $bank_name!='' OR $account_no!='' OR $ifsc_code!=''){
		//$enter_password = $_POST['enter_password'];
		//if($withdrawal_passwrod == $enter_password){
		$query = mysqli_query($con,"UPDATE `user` SET `name`='$name', `bank_name`='$bank_name',`account_no`='$account_no',`ifsc_code`='$ifsc_code', `kyc_status`='Paid' WHERE `email`='$userid_access'");  
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
      var popup = document.createElement('div');
      popup.className = 'popup';
      popup.innerHTML = 'Submitted successfully';
      document.body.appendChild(popup);
      setTimeout(function() {
        document.body.removeChild(popup);
        window.open('me.php','_self');
      }, 3000); // 3000 milliseconds = 3 seconds
    });
    </script>";
	   
	 }else{
	    echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					var popup = document.createElement('div');
					popup.className = 'popup';
					popup.innerHTML = 'All Field Required';
					document.body.appendChild(popup);
					setTimeout(function() {
						document.body.removeChild(popup);
						window.open('bindbank.php','_self');
					}, 3000); // 3000 milliseconds = 3 seconds
				});
				</script>";
		}
}
?>