

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security</title>

    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
       :root{
        --dark-blue:black;
        --light-blue:black;
        --yellow:#FFCE82;
        
       }

       p, h1, h2, h3, h4, h5, h6{
        margin: 0;
       }

       .appCapsule{
        max-width: 641px;
        margin: auto;

       }

       header{
        background-color: #005a36;

       }

       
       .appBody{
        padding: 4.5rem 0;
       }


       /* body{
        background-color: #009a5f;
       } */

       /* input-section================================  */
       

.inputBox .inner{
  display: flex; 
  justify-content: space-between;
  border-bottom:1px solid #aeaeae28;
  padding: 1rem 0;
}





/* modal open ============================  */
.modal-content{
  width: 300px !important;
  height:145px;
  /* background-color: red; */
  margin: auto;
  border-radius: 20px;
  overflow:hidden;
  

}

.

.modal-body{
  padding: 30px 0;
  border-bottom: 1px solid #dedede72;
}

  .modalFooter{
    display: flex;
  }

  .modalFooter .inner{
    width: 100%;
    text-align: center;
    padding: 10px 0;
    border-top:1px solid #ddd;
  }
  
  .modalFooter .inner:active{
      background-color:#ddd;
  }
  
  

</style>
    
  </head>
  <body>
<div class="appCapsule container">
  <header class="row fixed-top">
        <div class="text-white py-3">
            <a href="me.php" class="text-decoration-none px-3 text-white d-flex align-items-center justify-content-between">
                <div class="left d-flex align-items-center">
                    <i class="bi bi-chevron-left"></i>
                    <small>Back</small>
                </div>

                <h6>Security Setting</h6>

                <h6 class="px-3"></h6>
        </a>
        </div>

  </header>
          

  <div class="row appBody ">
    <div class="col-12  input-section px-4">
    <div class="inputBox bg-white p-3 rounded rounded-3">
    <a href="password.php" class="nav-link inner">
      <p>Modify login password</p>
      <div class="right">
        <i class="bi bi-chevron-right"></i>
      </div>
    </a>

    <a href="modifypassword.php" class="nav-link inner">
        <p>Modify payment password</p>
        <div class="right">
          <i class="bi bi-chevron-right"></i>
        </div>
      </a>

      <div class="nav-link inner">
        <p>Language</p>
        <div class="right">
           <select class="border-0">
               <option>English</option>
               <option>Hindi</option>
           </select>
          <i class="bi bi-chevron-right"></i>
        </div>
      </div>

      


    </div>
      
        <div class="inputBox-three mt-3">
     
            <div class="sub-btn px-3 d-grid mt-3">
                <button type="button" data-bs-toggle="modal" data-bs-target="#signoutModal" class="btn" style="background: black; color: white; border-radius: 30px; padding: 10px;">Sign out</button>
               </div> 
        </div>

        <!-- Button trigger modal -->

<!-- Modal -->



<?php


if(isset($_GET['logout'])){
    // Unset all session variables
    session_start();
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Popup message HTML
    $popupMessage = '<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 20px; border-radius: 10px; z-index: 9999;">Logout successful!</div>';

    // Display the popup message
    echo $popupMessage;

    // Wait for 5 seconds
    header("refresh:1; url=../login.php");
    exit;
}
?>


<div class="modal fade" id="signoutModal" tabindex="-1" aria-labelledby="signoutModalLabel" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
  
      <div class="modal-body text-center d-flex align-items-center justify-content-center">
       <b>Are you sure to sign out？</b>
      </div>

<style>
  
</style>
      <div class="modalFooter">
        <div class="inner">
          <button type="button" class="text-decoration-none" style="border:none; background-color:transparent;" data-bs-dismiss="modal">Cancel</button>
        </div>

        <div class="inner border-start">
          <!--<a href="user_menu/logout.php" class="btn text-danger">Confirm</a> -->
          <a href="?logout" class="text-decoration-none text-danger">Confirm</a>
        </div>

      </div>
    </div>
  </div>


</div>


        
    </div>
  </div>

  

 



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