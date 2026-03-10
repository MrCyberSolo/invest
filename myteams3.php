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
$get_balance = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `income` WHERE `userid`='$userid_access'"));
	$current_bal = $get_balance['current_bal'];
	$fran_bal = $get_balance['fran_bal'];
	date_default_timezone_set('Asia/Kolkata');
    //label count
    $DB = $con;
    if($DB->connect_error) {
        die("Connection failed: " . $DB->connect_error);
    }
    function getParent($parent_id)
    {
        global $DB;
        $query = $DB->query("SELECT * FROM user WHERE email = $parent_id");
        $result = $query->fetch_object();
        $data = (!$result) ? null : $result;
        $query->free();
        return $data;
    }
    function getChildren($parent_id)
    {
        global $DB;
        $query = $DB->query("SELECT * FROM user WHERE under_userid = $parent_id ORDER BY user_id");
        $arr = [];
        while($row = $query->fetch_object()) {
            $arr[] = $row;
        }        
        $data = (count($arr) < 1) ? [] : $arr;        
        $query->free();        
        return $data;
    }
    function getLevels($parent_id)
    {
        $level_1 = getChildren($parent_id);
        $level_2 = [];
        $level_3 = [];
        $level_4 = [];
        $level_5 = [];	
        if(count($level_1) > 0) {
            foreach($level_1 as $level1) {
                $level_2_data = getChildren($level1->email);
                if(count($level_2_data) > 0) {
                    foreach($level_2_data as $data) {
                        $level_2[] = $data;
                    }
                }
            }
        }
        if(count($level_2) > 0) {
            foreach($level_2 as $level2) {
                $level_3_data = getChildren($level2->email);
                if(count($level_3_data) > 0) {
                    foreach($level_3_data as $data) {
                        $level_3[] = $data;
                    }
                }
            }
        }
        if(count($level_3) > 0) {
            foreach($level_3 as $level3) {
                $level_4_data = getChildren($level3->email);
                if(count($level_4_data) > 0) {
                    foreach($level_4_data as $data) {
                        $level_4[] = $data;
                    }
                }
            }
        }
        return [
            ['name' => 'Level 1', 'data' => $level_1],
            ['name' => 'Level 2', 'data' => $level_2],
            ['name' => 'Level 3', 'data' => $level_3]
        ];
    }
    $parent = getParent($userid_access);
    if($parent == null) {
        die('Parent user not found.');
    }
    $levels = getLevels($parent->email);    
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My teams</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#213955;
        --light-blue:#009a5f;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule, .footerBox{
        max-width: 641px;
        /* background-color: #009a5f; */
        margin: auto;

       }

       .appCapsule{
        padding-bottom: 1rem;
       }

       /* body{
        background-color: #009a5f;
       } */

       .appBody{
        margin-top: 4.2rem;
       }

       /* header .line{
  width: 30px;
  height: 2px;
  border-radius: 40px;
  background-color: var(--yellow);
  margin:5px auto;
} */

header{
  background-color:#005a36;
}

/* ==========================  */
/* footer section   */

header .line{
  width: 30px;
  height: 2px;
  border-radius: 40px;
  background-color: var(--yellow);
  margin:5px auto;
}

       .footerBox{
  display: flex;
  background-color: white;
  justify-content: space-around;
  align-items: center;
  text-align: center;
  /* padding: 5px 0; */
  height: 60px;
  box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
}

.footerBox img{
  width: 24px;
}

  footer .inner p{
    font-size: 13px;
  }
  

.footerBox a{
  text-decoration: none;
  color: #CCCCCC;
  
}

.footerBox .me-icon img{
    width: 28px;

}


       
/* end footer section   */   
/* ==========================  */

body{
    color: white;
}

.totalBox a{
    text-decoration: none;
    color: white;
    border: black solid 1px;
    background-color: transparent;
   
    width: 32%;
    padding: 5px ;
    border-radius: 6px; 
}



.tab {
  overflow: hidden;
}


/* Change background color of buttons on hover */
.tab a:hover {
    background-color: #005a36;
    color: white;
}

/* Create an active/current tablink class */
.tab a.active {
    background-color: #005a36;
    color: white;
}

/* Style the tab content */
.teamContent {
  /* display: none; */
  /*padding: 6px 12px;*/
  margin-top:10px;
  border-top: none;
}

.percent-section{
  height: 11vh;
}



/* member section ============== */
      .memberTab{
        display: flex;
        justify-content: space-between;
          
      }
.member-section button {
  text-decoration: none;
    color: white;
    border: black solid 1px;
    background-color: transparent;
   
    width: 49%;
    padding: 5px ;
    border-radius: 6px; 
}

.member-section button:hover {
  background-color: #005a36;
  color: white;

}

.member-section button.active {
  background-color: #005a36;
  color: white;
}

.member-section .tabcontent {
  /* display: none; */
  padding: 6px 12px;
  border-top: none;
}

.member-section{
  height: 74vh;
  margin-top: 10px;
}


    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container ">
  <div class="row">

        <header class="fixed-top">
        <div class="text-white py-3">
            <div class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <a href="me.php" class="nav-link left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6>My Teams</h6>

                <div class="px-3"></div>

        </div>
        </div>

  </header>

  </div>
 



  <div class="row appBody" >
    <div class="col-12 percent-section">

        <div class="totalBox tab d-flex justify-content-between text-center">
            <a href="myteams.php" type="button"  class="teamlink inner " onclick="openteam(event, 'first')">
                <p>B-10% (<?php 
								$total_count=0;
                                foreach($levels as $level) 
                                {
                                    $label_count= count($level['data']); 
                            	    $total_count = ($total_count + $label_count);
                            	      $firstValue = array_shift($level);
                            	     if($firstValue=='Level 1'){
                            	        echo $label_counts= count($level['data']); 
                            	     }
                                }
                                ?>)</p>
            </a>
    
            <a href="myteams2.php" type="button" class="teamlink inner " onclick="openteam(event, 'second')" >
                <p>C-5% (<?php 
								$total_count=0;
                                foreach($levels as $level) 
                                {
                                    $label_count= count($level['data']); 
                            	    $total_count = ($total_count + $label_count);
                            	      $firstValue = array_shift($level);
                            	     if($firstValue=='Level 2'){
                            	        echo $label_counts= count($level['data']); 
                            	     }
                                }
                                ?>)</p>
            </a>
    
            <a href="myteams3.php" type="button" class="teamlink inner active" onclick="openteam(event, 'third')">
                <p>D-2% (<?php 
								$total_count=0;
                                foreach($levels as $level) 
                                {
                                    $label_count= count($level['data']); 
                            	    $total_count = ($total_count + $label_count);
                            	      $firstValue = array_shift($level);
                            	     if($firstValue=='Level 3'){
                            	        echo $label_counts= count($level['data']); 
                            	     }
                                }
                                ?>)</p>
            </a>
        </div>

<style>
  .secondPerce, .thirdPerce, .secondMemb{
    display: none;
  }
</style>
      <div class="tabContent mt-3 text-center">
     
        <div id="first" class="teamContent ">
           
            <div class=" d-flex justify-content-center align-items-end gap-1">
                <p>Total: <h5><?php $tc=0;
                                foreach($levels as $level) 
                                {
                                  $tc = $tc + count($level['data']);
                                }
                               echo  $tc; ?></h5></p>
              </div>
          </div>
          
    </div>

      </div>



    <!-- member section  -->

    <div class="col-12 member-section">

<div class="memberTab">
  <button class="tablinks active" onclick="openMember(event, 'memberOne')">Invalid member</button>
  <button class="tablinks" onclick="openMember(event, 'memberTwo')">Valid member</button>
</div>


<style>
    .itemsOption p{
        
        width:100%;
        /*text-align:center;*/
        
    }
    
    .itemsOption{
        /*background-color:red;*/
        padding:8px 0;
        border-bottom:1px solid #ddd ;
    }
    
    .prod{
        position:relative;
        left:1.2rem;
    }
    
    
</style>

<div class="items">
  <div class=" d-flex justify-content-around text-center" style="padding-top: 10px;">
    <p>Account</p>
    <p>Registration time</p>
    <p>Product</p>
  </div>
 <div id="memberOne" class="tabcontent text-center ">
 
      <?php  foreach($levels as $level) { 
       $total_count = ($total_count + $label_count);
                            	      $firstValue = array_shift($level);
                            	     if($firstValue=='Level 3'){
                            	     ?>
       <?php foreach($level['data'] as $data) { 
      if($data->status==''){
      ?>
  <div class="itemsOption d-flex justify-content-around">
    <p><?php $mobile= $data->mobile ?><?php echo $maskedPhone = substr($mobile, 0, 2) . "****" . substr($mobile, 6, 4); ?></p>
    <p><?php echo $data->join_date ?></p>
    <p class="prod "> <?php echo $data->pac_offer ?></p>
 </div>
    <?php } } } } ?>
     
</div>
  <div id="memberTwo" class="tabcontent text-center secondMemb" style="padding-top: 10px;">
    <?php  foreach($levels as $level) { 
     $total_count = ($total_count + $label_count);
      $firstValue = array_shift($level);
     if($firstValue=='Level 3'){
                            	     ?>
       <?php foreach($level['data'] as $data) { 
      if($data->status=='Active'){
      ?>
  <div class="itemsOption d-flex justify-content-around">
    <p><?php $mobile= $data->mobile ?><?php echo $maskedPhone = substr($mobile, 0, 2) . "****" . substr($mobile, 6, 4); ?></p>
    <p><?php echo $data->join_date ?></p>
    <p class="prod"> <?php echo $data->pac_offer ?></p>
 </div>
    <?php } } } } ?>
  
  </div>
</div>

</div>


<script>
function openMember(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>






    </div>

    
    </div>
    
    </div>


<!-- Footer Start Here -->
<?php include "user_menu/footer_menu.php";  ?>
<!-- Footer End Here -->













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

       
  



  </body>
</html>