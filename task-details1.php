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
 
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My products</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:#009a5f;
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
    margin-top: 4rem;
}
       

       body{
        /* background-color: #009a5f; */
    /* color: rgb(0, 0, 0); */

       }



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

/* member section ============== */

      .memberTab{
        display: flex;
        justify-content: space-between;
        background-color:#dbdbdb;
        /* border: 1px solid black; */
        border-radius: 5px;
        padding: 1px;
        overflow: hidden;

          
      }
.member-section button {
  text-decoration: none;
    color: rgb(0, 0, 0);
    border: #ffffff solid 1px;
    border: none;
    background-color: transparent;
    width: 100%;
    padding: 7px ;

}

.member-section button:first-child{
  border-top-left-radius: 7px;
    border-bottom-left-radius: 7px;
}

.member-section button:last-child{
  border-top-right-radius: 7px;
    border-bottom-right-radius: 7px;
}


.member-section button:hover {
  background-color:#005a36;
  color: rgb(255, 255, 255);

}

.member-section button.active {
  background-color:#005a36;   color: rgb(255, 255, 255);
}

.member-section .tabcontent {
  /* display: none; */
  /* padding: 6px 12px; */
  border-top: none;
}

/* .member-section{
  height: 100vh;
  background-color: #009a5f;
} */

	.popup {
    position: fixed;
    bottom: 50%;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    z-index: 999;
    animation: fadeInOut 2s;
}



    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
<div class="appCapsule container ">

   <header class="row fixed-top">
        <div class="text-white py-3">
            <div class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <a href="javascript:history.back()" class="nav-link left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </a>

                <h6>How to make money</h6>

                <div class="px-3"></div>

        </div>
        </div>

  </header>
 
 



  <div class="row appBody">
      <div class="col-12">
          <img src="img/11.jpeg" style=" width: 100%; ">
          <img src="img/12.jpeg" style=" width: 100%; padding-top: 20px;">
          <img src="img/13.jpeg" style=" width: 100%; padding-top: 20px;">
          <br>
          <!--<p style=" color: #fff; font-size: 20px; font-weight: 500; ">Take a photo with our APP homepage and a selfie, and send it to your exclusive manager. You can get a 10rs task reward (each member can only receive it once)</p>-->
      </div>

    
    
   
     
   
    
    

    </div>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>





  </body>
</html>
