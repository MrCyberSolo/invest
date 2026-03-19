<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
include('user_menu/database_connect.php');
$userid_access = $_SESSION['username'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>commission record</title>

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

         
       .appBody{
        padding: 3.8rem 0;
       }


       /* body{
        background-color: #009a5f;
       } */


header{
  background-color: #005a36;
}




header .line{
  width: 30px;
  height: 2px;
  border-radius: 40px;
  background-color: var(--yellow);
  margin:5px auto;
}

/* ==========================  */
/* footer section   */
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

.totalBox button{
    text-decoration: none;
    color: rgb(0, 0, 0);
    border: black solid 1px;
    background-color: transparent;
   
    width: 50%;
    padding: 5px ;
}



.tab {
  overflow: hidden;
}


/* Change background color of buttons on hover */
.tab button:hover {
    background-color: rgb(81, 81, 81);
    color: rgb(0, 0, 0);
}

/* Create an active/current tablink class */
.tab button.active {
    background-color: black;
    color: rgb(255, 255, 255);
}

/* Style the tab content */
.teamContent {
  /* padding: 6px 12px; */
  border-top: none;
}

#second, #third{
    display: none;
}

.percent-section{
  height: 15vh;
}




.table>:not(caption)>*>*{
    background-color: transparent;
    border: none;
    font-size: 16px;
    padding-top:8px !important;
}

thead td{
    color: rgb(255, 255, 255) !important;
    font-weight: bold;
}

tbody td{
    color: rgb(255, 255, 255) !important;
}
</style>

  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container ">
  <header class="row fixed-top">
    <div class="text-white py-3">
        <a href="javascript:history.back()" class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
            <div class="left d-flex align-items-center">
                <i class="bi bi-chevron-left"></i>
                <small>Back</small>
            </div>

            <h6>Commission records</h6>

            <div class="px-3"></div>

    </a>
    </div>

</header>
 


  <div class="row appBody">
    <div class="col-12 percent-section">

        <div class="text-center">
            <b class="">Summary: ₹  <?php					
                               $current_bal = 0;
                               $query = mysqli_query($con,"select * from interest_label where int_userid ='$userid_access' AND int_status='Active'  ");
                               if(mysqli_num_rows($query)>0)
                               {
                                   while($row=mysqli_fetch_array($query))
                                   {
                                       $id = $row['int_id'];
                                       $current_bals = $row['int_amount'];
                                       
                                       $current_bal = $current_bal + $current_bals;
                                   }
                               }
                                        echo " "  .$current_bal.""
                            ?></b>

        </div>
        <div class="totalBox tab d-flex justify-content-between text-center">
            <button type="button"  class="teamlink inner active" onclick="openteam(event, 'first')">
                <p>B-10% </p>
            </button>
    
            <button type="button" class="teamlink inner" onclick="openteam(event, 'second')" >
                <p>C-5% </p>
            </button>
    
            <button type="button" class="teamlink inner" onclick="openteam(event, 'third')">
                <p>D-2% </p>
            </button>
        </div>


      <div class="tabContent mt-3 text-center">
     
        <div id="first" class="teamContent">
           
            <div class="  d-flex justify-content-center align-items-end gap-1">
                <table class="table">
                    <thead class="text-center">
                        <tr class="">
                            <td>get date</td>
                            <td>account</td>
                            <td>level</td>
                          
                            <td>to earn</td>
                          </tr>
                    </thead>
            
                    <tbody class="text-center">
                      <?php                                 
                       $query2 = mysqli_query($con,"select * from interest_label where int_userid = $userid_access AND int_level='0'");
                       if(mysqli_num_rows($query2) > 0) {
                           while($row = mysqli_fetch_array($query2)) {
                               
                               $amount = $row['int_amount'];
                               $int_status = $row['int_status'];
                               $int_date = $row['int_date'];
                               $int_acc_id = $row['int_acc_id'];
                               $date=date_create("$dates");
                               $income_date = date_format($date,"Y-m-d");
                               $query_order_label = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `order_book_label` WHERE `o_id`='$int_acc_id'"));
			                        $o_spo = $query_order_label['o_userid'];
                               $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$o_spo'"));
			                        $mobile = $query['mobile'];
                      ?>
                      <tr>
                        <td><?php echo $income_date; ?></td>
                        <td><?php echo $maskedPhone = substr($mobile, 0, 2) . "****" . substr($mobile, 6, 4); ?></td>
                        
                        <td>10%</td>
                        <td><?php echo $amount; ?></td>
                      </tr>       
                        <?php } } ?>            
                    </tbody>
                </table>
              </div>
          </div>
          
          <div id="second" class="teamContent">
            <div class=" d-flex justify-content-center align-items-end gap-1">
                <table class="table">
                    <thead class="text-center">
                        <tr>
                            <td>get date</td>
                            <td>account</td>
                            <td>level</td>
                           
                            <td>to earn</td>
                          </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php                                 
                       $query2 = mysqli_query($con,"select * from interest_label where int_userid = $userid_access AND int_level='1'");
                       if(mysqli_num_rows($query2) > 0) {
                           while($row = mysqli_fetch_array($query2)) {
                               
                               $amount = $row['int_amount'];
                               $int_status = $row['int_status'];
                               $int_date = $row['int_date'];
                               $int_acc_id = $row['int_acc_id'];
                               $date=date_create("$dates");
                               $income_date = date_format($date,"Y-m-d");
                               $query_order_label = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `order_book_label` WHERE `o_id`='$int_acc_id'"));
			                        $o_spo = $query_order_label['o_userid'];
                               $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$o_spo'"));
			                        $mobile = $query['mobile'];
                      ?>
                      <tr>
                        <td><?php echo $income_date; ?></td>
                        <td><?php echo $maskedPhone = substr($mobile, 0, 2) . "****" . substr($mobile, 6, 4); ?></td>
                       
                        <td>5%</td>
                        <td><?php echo $amount; ?></td>
                      </tr>       
                        <?php } } ?>            
                    </tbody>
                </table>
              </div>
          </div>
          
          <div id="third" class="teamContent">
            <div class=" d-flex justify-content-center align-items-end gap-1">
                <table class="table">
                    <thead class="text-center">
                        <tr>
                            <td>get date</td>
                            <td>account</td>
                            <td>level</td>
                            
                            <td>to earn</td>
                          </tr>
                    </thead>
            
                    <tbody class="text-center">
                      <?php                                 
                       $query2 = mysqli_query($con,"select * from interest_label where int_userid = $userid_access AND int_level='2'");
                       if(mysqli_num_rows($query2) > 0) {
                           while($row = mysqli_fetch_array($query2)) {
                               
                               $amount = $row['int_amount'];
                               $int_status = $row['int_status'];
                               $int_date = $row['int_date'];
                               $int_acc_id = $row['int_acc_id'];
                               $date=date_create("$dates");
                               $income_date = date_format($date,"Y-m-d");
                               $query_order_label = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `order_book_label` WHERE `o_id`='$int_acc_id'"));
			                        $o_spo = $query_order_label['o_userid'];
                               $query = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `user` WHERE `email`='$o_spo'"));
			                        $mobile = $query['mobile'];
                      ?>
                      <tr>
                        <td><?php echo $income_date; ?></td>
                        <td><?php echo $maskedPhone = substr($mobile, 0, 2) . "****" . substr($mobile, 6, 4); ?></td>
                        
                        <td>2%</td>
                        <td><?php echo $amount; ?></td>
                      </tr>       
                        <?php } } ?>            
                    </tbody>
                </table>
            </div>
          </div>
    </div>

    </div>

    <!-- data section  -->
<div class="col-12">
  
</div>
    






    
    </div>
    
    
   
     
   
    
    

    </div>



<!-- Footer Start Here -->
<?php include "user_menu/footer_menu.php";  ?>
<!-- Footer End Here -->

</div>    <!--  end appCapsule  -->
<!-- =================================== -->
    













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

       
    <script>
      function openteam(evt, teamName) {
        var i, teamContent, teamlink;
        teamContent = document.getElementsByClassName("teamContent");
        for (i = 0; i < teamContent.length; i++) {
          teamContent[i].style.display = "none";
        }
    
    
        teamlink = document.getElementsByClassName("teamlink");
        for (i = 0; i < teamlink.length; i++) {
          teamlink[i].className = teamlink[i].className.replace(" active", "");
        }
    
        document.getElementById(teamName).style.display = "block";
        evt.currentTarget.className += " active";
      }
      </script>



  </body>
</html>