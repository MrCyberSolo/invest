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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invitation</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
   :root {
    --dark-blue: #213955;
    --light-blue: #0061bf;
    --yellow: #FFCE82;
   }

   p, h1, h2, h3, h4, h5, h6 {
    margin: 0;
   }

   .appCapsule {
    max-width: 641px;
    margin: auto;
    background-image: url(img/invite.jpg);
    background-size: cover;
    background-position: center;
    width: 100%;
    min-height: 100vh;
   }

   .appBody {
    padding: 3.5rem 0;
   }

   .scaneCard {
    background-image: url(img/invite_box.png);
    padding: 50px;
    max-width: 100%;
    background-repeat: no-repeat;
    height: 300px;
   }

   .scaner-section .card {
    border-top-right-radius: 40px;
    border-bottom-left-radius: 40px;
   }

</style>
</head>
<body>

<div class="appCapsule container">
  <header class="row fixed-top">
    <div class="text-white py-3">
      <div class="px-3 d-flex align-items-center justify-content-between">
        <a href="home.php" class="text-white text-decoration-none left d-flex align-items-center">
          <i class="bi bi-chevron-left"></i>
          <small>Back</small>
        </a>
        <h5>Invitation</h5>
        <div class="px-3"></div>
      </div>
    </div>
  </header>

  <div class="row appBody d-flex justify-content-center">
    <div class="col-10 scaner-section d-flex justify-content-center mt-3">
      <div class="card border-0">
        <div class="card-inner px-4 py-3">
          <img src="https://1.bp.blogspot.com/-Fskyl2EvIXY/UfV6GKoCkuI/AAAAAAAAALI/QgFLbAUMx0Y/s1600/qr_code.jpg" class="img-fluid" alt="">
          <!-- Copy option 1 -->
          <a href="#" class="inv-link d-flex justify-content-center gap-1" style="color: #0061bf; text-decoration: none;">
            <h5 id="inviteCode"><?php echo $userid_access;?></h5>
            <i class="bi bi-copy" style="color: rgb(0, 185, 185);"></i>
          </a>
          <br>
          <!-- Copy option 2 -->
          <a href="https://www.youtdomain.com/register?inviteCode=<?php echo $userid_access;?>" id="inviteLink" class="nav-link" type="button" class="btn" style="text-align: center; border: none; background-color: #0061bf; color: white; width: 100%; padding: 7px; font-size: 14px; border-radius: 20px; margin: 5px 0;">Copy invitation link</a>
        </div>
      </div>
    </div>

    <div class="col-12 mt-5 p-2 px-4" style="background-color: rgba(255, 255, 255, 0.382);">
      <div class="tips">
        <b>Kind Tips</b>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore dignissimos sunt ad</p>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script for copy functionality and pop-up -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var copyOption1 = document.getElementById('inviteCode');
  var copyOption2 = document.getElementById('inviteLink');

  copyOption1.addEventListener('click', function() {
    var inviteCode = copyOption1.innerText;
    navigator.clipboard.writeText(inviteCode).then(function() {
      showSuccessMessage("Copied: " + inviteCode);
    }).catch(function(err) {
      console.error('Error copying text: ', err);
    });
  });

  copyOption2.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default link behavior
    var inviteLink = copyOption2.getAttribute('href');
    navigator.clipboard.writeText(inviteLink).then(function() {
      showSuccessMessage("Copied: " + inviteLink);
    }).catch(function(err) {
      console.error('Error copying text: ', err);
    });
  });

  function showSuccessMessage(text) {
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();

    setTimeout(function() {
      successModal.hide();
    }, 3000);
  }
});
</script>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background-color: rgba(0, 0, 0, 0.7); color: white; margin: 110px; ">
      <div class="modal-body text-center" style="padding: 5px;">
        <p id="successMessage">Copy succeeded </p>
      </div>
    </div>
  </div>
</div>

</body>
</html>
