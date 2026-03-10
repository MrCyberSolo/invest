<?PHP 
include('user_menu/database_connect.php');
//$userid_access = $_SESSION['username'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
       :root{
        --blue:#213955;
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
        padding-bottom: 4rem;
        
       }

   

       header .carousel img{
        max-height:250px ;
        height: auto;
        border-radius: 5px;
       }

       /* .headerBox .cardBox{
        background-color:white;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding: 16px 0;
        border-radius: 5px;
       }

       .headerBox img{
        width: 40px;
        height: 100%;

        
       }

       .headerBox .cardBox h6{
        margin: 0;
       } */

   

       .menuBox{
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        padding-top: 7px;
       }

       .menuBox a{
        width: 100%;
        text-decoration: none;
        color: #ddd;
        /* background-color: #007749; */
        width: 100%;
        height:80px;
        margin:0 5px;
        padding: 10px 0;
        border-radius: 10px;
        transition: .3s;
       }

       .menuBox a:hover{
        background-color: #0254a5;
       }

       .menuBox img{
        width: 40px;
       }

       .menuBox i{
        font-size: 25px;
        color:white; 
       }

       .menuBox p{
        font-size: 12px;
        color:white;
       }

       .textBox .inner{
        background-color: #0b60cc;
        padding: 8px 12px;
        border-radius: 8px;
       }

       .textBox .inner i{
        color: #ffb854;
        font-size: 22px;
       }

       .newsSection .inner{
        border-bottom: 1px solid #dddddd5e;
        padding: 10px 5px;
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

/*.footerBox img, i{*/
/*  width: 20px;*/
/*}*/

.footerBox .active1{
  color:#007749 ;
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

          .toggleContent{
            display: none;
          }


.headerBox{
    background-color:white;
    text-align: center;
    border-radius: 15px;
    padding: 25px;

}

.headerBox a{
    text-decoration: none;
    color: #272727;
}

.headerBox img{
    width: 2rem;
    margin-bottom: 5px;
}

.menuBody p{
    font-size: 12px;

}

.task-img img{
    width:100%;
    height:80%;
}

.floating-right-menu {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
    z-index: 10;
    align-items: flex-end;
}

.floating-btn {
    background: #009a5f;
    color: white;
    text-decoration: none;
    padding: 7px 8px 7px 12px;
    border-radius: 20px 0 0 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: -2px 2px 5px rgba(0,0,0,0.1);
    width: 110px;
}

.floating-btn i {
    font-size: 14px;
    color: white;
}

.floating-btn span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.news-card {
    border-radius: 10px;
    overflow: hidden;
    background-color: transparent;
    border: none;
    margin-bottom: 5px;
}

.news-card img {
    height: 110px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    width: 100%;
}

.news-card-body {
    background: linear-gradient(180deg, #4aa3fb, #166fe2);
    padding: 8px 5px;
    text-align: center;
    border-radius: 0 0 10px 10px;
}
    
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
   <div class="appCapsule">
    <header class="container">
<!-- 
      <div class="header-title text-center py-3">
        <h5>Home</h5>
      </div> -->

      <style>
.carousel-inner img{
  height: 220px !important;
  object-fit: cover;
}
      </style>
      <div class="pt-4 position-relative d-flex align-items-center" style="padding-left: 10px;">
          <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel" style="width: calc(100% - 118px);">
              <div class="carousel-inner" style="border-radius: 8px; overflow: hidden; box-shadow: 2px 4px 10px rgba(0,0,0,0.1);">
                <div class="carousel-item active">
                  <img src="img/1.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                  <img src="img/3.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                  <img src="img/2.jpg" class="d-block w-100" alt="...">
                </div>
              </div>
          </div>
          
          <div class="floating-right-menu">
              <a href="recharge.php" class="floating-btn"><i class="bi bi-wallet2"></i> <span>Recharge</span></a>
              <a href="withdraw.php" class="floating-btn"><i class="bi bi-cash"></i> <span>Withdraws</span></a>
              <a href="services.php" class="floating-btn"><i class="bi bi-chat-dots-fill"></i> <span>customer ...</span></a>
              <a href="invitiation.php" class="floating-btn"><i class="bi bi-person-plus-fill"></i> <span>Invitation</span></a>
              <a href="redeembonus.php" class="floating-btn"><i class="bi bi-gift-fill"></i> <span>Redeem b...</span></a>
              <a href="reward.php" class="floating-btn"><i class="bi bi-cloud-arrow-down-fill"></i> <span>App Down...</span></a>
          </div>
      </div>
      
  </header>



  <div class="container appContent mt-3">
    <!-- <div class="row headerBox px-2 gx-2">
        <div class="col-6">
            <div class="cardBox">
                <img src="img/icons/h1.png" alt="">
                <h6>Recharge</h6>
            </div>
        </div>

        <div class="col-6">
            <div class="cardBox">
                <img src="img/icons/h2.png" alt="">
                <h6>Recharge</h6>
            </div>
        </div>
    </div> -->


    <div class="row">
      <div class="col-12 textBox ">
        <div class="inner d-flex justify-content-center align-items-center gap-2">
          <i class="bi bi-volume-up-fill"></i>
          <?php $query_setting = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `notice`"));
            $notice_board = $query_setting['message'];
            ?>
          <marquee behavior="" direction="left" class="text-white" style="font-size: 17px;"><?php echo $notice_board; ?></marquee>
        </div>
      </div>
    </div>



    <div class="row topMenu mt-3">
      <div class="col-12">
        <div class="menuBox" style="display: flex; justify-content: space-around; flex-wrap: wrap;">
          <a href="myproducts.php" class="inner" style="width: auto;">
            <i class="bi bi-box2-heart-fill"></i>
            <p>My Product</p>
          </a>
          <a href="task_details3.php" class="inner" style="width: auto;">
            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            <p>Salary</p>
          </a>
          <a href="bindbank.php" class="inner" style="width: auto;">
             <i class="bi bi-bank2"></i>
            <p>Bank</p>
          </a>
          <a href="myteams.php" class="inner" style="width: auto;">
            <i class="bi bi-people-fill"></i>
            <p>Team</p>
          </a>
        </div>
      </div>
    </div>
    
    
    <div class="row videoSection mt-4">

    
        
      <div class="col-12 inner mb-3">
        <video src="img/video.mp4" class="img-fluid rounded" controls style="width: 100%; height: 300v;"></video>
       <!-- <iframe width="100%" height="315" src="https://www.youtube.com/embed/ezaD--_Ugd4?si=VGOcwb0rj8lwBVQP" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>-->
      </div>
      
        <div class="col-12 task mb-2 text-white">
            <h6>Task Bonus</h6>
        </div>    
<div class="col-6 task-img">
<a href="task-details1.php">
<img src="img/669803d7c6c3a.jpg" class="img-fluid img-thumbnail">
<p class="text-center text-white">How to make money?</p>
</a>

</div>


<div class="col-6 task-img">
<a href="task_details2.php">
<img src="img/gifts.jpeg" class="img-fluid img-thumbnail">
<p class="text-center text-white">Selfi Reward</p>
</a>


</div>


      <div class="col-12 newsSection mt-5">
        <div class="topInner d-flex justify-content-between mb-2">
          <h5 class="text-white border-start border-4 px-3">News</h5>       
          <a href="news.php" class="text-decoration-none d-flex gap-2 align-items-center text-white border-0" style="background-color: transparent;" onclick="toggleContent('toggleContent')">
            <p class="m-0">More</p>
            <i class="bi bi-chevron-right"></i>
          </a>
        </div> 
        <div class="row gx-2">
        <?php 
        $i=1;
        $query = mysqli_query($con,"select * from blog order by b_id desc");
        if(mysqli_num_rows($query)>0){
            while($row=mysqli_fetch_array($query)){
                
                $blog_id= $row['b_id'];
                $blog_title = $row['b_title'];
                $blog_image = $row['b_image'];
                $blog_short = $row['b_details'];
                $blog_create_date = $row['b_create_date'];
          ?> 
          <div class="col-6 mb-3">
            <a href="news_details.php?pac=<?php echo $blog_id; ?>" class="text-decoration-none">
              <div class="card news-card">
                <img src="asupport/blog/<?php echo $blog_image; ?>" alt="...">
                <div class="news-card-body">
                  <p class="text-white mb-0" style="font-size: 13px; font-weight: 500; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php echo $blog_title; ?></p>
                </div>
              </div>
            </a>
          </div>
        <?php } } ?>
        </div>
      </div>
    </div>


</div>
<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#homeModal">
  Launch demo modal
</button> -->

<!-- Modal -->
<style>
  /* Styles for the modal */
  .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
  }
  .modal-content {
      position: relative;
      top: 7rem;
      background-color: #fefefe;
      margin:auto;
      padding: 20px;
      border: 1px solid #888;
      width: 370px;
      border-radius: 13px;
      ma
  }


.modalBody p{
  font-size: 17px;
  text-align: justify;
}
</style>

<!-- Modal -->
<div id="myModal" class="modal">
  <div class="modal-content">
      <!-- <button type="button" class="close">&times;</button> -->
      <img src="img/logo.jpg" style="width: 150px; height: 150px; margin: auto;" class="img-fluid" alt="">
      
      <div class="modalBody">
          <p>The client himself, will be able to enhance the grace of the client company. There is no architect to meet with the requirements, it is the very labor of those who praise that the flight of features is most criticized, for those who like easy and apart from flattery</p>
          <div class="d-grid px-3 mt-3">
              <button type="button" class="close" style="background-color: #009a5f; border: none; color:white; padding: 5px; font-size: 17px; border-radius: 5px;  ">Close</button>

          </div>
      </div>
  </div>
</div>

<!-- <script>
  function openModalDelayed(delay) {
      setTimeout(function() {
          document.getElementById('myModal').style.display = 'block';
      }, delay);
  }
  
  document.querySelector('.close').addEventListener('click', function() {
      document.getElementById('myModal').style.display = 'none';
  });
  
  openModalDelayed(2000);
  </script> -->
<!-- Footer Start Here -->
<?php include "user_menu/footer_menu.php";  ?>
<!-- Footer End Here -->


   

</div>    <!--  end appCapsule  -->
<!-- =================================== -->
<?php
// Check if the HTTP_REFERER is set and not empty
if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
    $lastPage = $_SERVER['HTTP_REFERER'];
    $last_page_find = 'https://oceanfoodco.vip/login.php';
    if($lastPage == $last_page_find){
        // Popup message HTML
        $popupMessage = '<div id="popupMessage" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 10px; border-radius: 10px; z-index: 9999;">Login succes!</div>';

        // Display the popup message
        echo $popupMessage;

        // JavaScript to hide the popup after 3 seconds
        echo '<script>
                setTimeout(function(){
                    var popup = document.getElementById("popupMessage");
                    if(popup){
                        popup.style.display = "none";
                    }
                }, 2000); // 3 seconds delay
              </script>';
    }
}
?>
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>



    <!-- show hide content  -->
   <script>
    function toggleContent(className) {
      var contents = document.getElementsByClassName(className);
      for (var i = 0; i < contents.length; i++) {
          var content = contents[i];
          if (content.style.display === "none") {
              content.style.display = "block";
          } else {
              content.style.display = "none";
          }
      }
  }
  
  </script>
  

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <!-- <script>
   $(document).ready(function(){
    $('#homeModal').modal('show');
   })
  </script> -->
  </body>
</html>