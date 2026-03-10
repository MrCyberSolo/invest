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
    background-image: url(img/bg.jpeg);
    background-color: #1a569d;
    background-size: cover;
    background-position: center;
    width: 100%;
    min-height: 100vh;
   }

   header {
    background-color: #1a569d;
    border-bottom: none;
   }

   .appBody {
    padding: 3.5rem 0;
   }

   .invite-card-wrapper {
        margin: 0 20px;
        position: relative;
        background: transparent;
   }

   .qr-top {
        background: white;
        border-radius: 12px 12px 0 0;
        padding: 30px;
        position: relative;
   }
   
   .qr-top-divider {
        position: relative;
        height: 1px;
        background: white;
   }

   .qr-top-divider::before, .qr-top-divider::after {
        content: '';
        position: absolute;
        top: -10px;
        width: 20px;
        height: 20px;
        background-color: #1a569d;
        border-radius: 50%;
        z-index: 2;
   }
   .qr-top-divider::before {
        left: -10px;
   }
   .qr-top-divider::after {
        right: -10px;
   }
   
   .dashed-line {
        position: absolute;
        top: 0;
        left: 15px;
        right: 15px;
        border-top: 1px dashed #ccc;
        z-index: 1;
   }

   .qr-bottom {
        background: white;
        border-radius: 0 0 12px 12px;
        padding: 20px;
   }

   .copy-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
   }

   .copy-row:last-child {
        border-bottom: none;
   }

   .copy-text {
        font-size: 13px;
        color: #333;
        word-break: break-all;
        padding-right: 15px;
   }

   .copy-btn {
        background-color: #398af2;
        color: white;
        border: none;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        flex-shrink: 0;
   }

   .tips-box {
        border: 1px dashed rgba(255,255,255,0.4);
        border-radius: 10px;
        padding: 15px;
        margin: 20px 20px 0 20px;
   }

   .tips-box h6 {
        color: white;
        margin-bottom: 10px;
        font-weight: 500;
   }

   .tips-box p {
        color: rgba(255,255,255,0.8);
        font-size: 11px;
        line-height: 1.4;
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

  <div class="row appBody">
    <div class="col-12 mt-4">
        <div class="invite-card-wrapper">
            <div class="qr-top d-flex justify-content-center">
                <img src="https://1.bp.blogspot.com/-Fskyl2EvIXY/UfV6GKoCkuI/AAAAAAAAALI/QgFLbAUMx0Y/s1600/qr_code.jpg" class="img-fluid" style="border-radius: 10px; width: 85%;" alt="QR Code">
            </div>
            
            <div class="qr-top-divider">
                <div class="dashed-line"></div>
            </div>

            <div class="qr-bottom">
                <div class="copy-row">
                    <div class="copy-text" id="inviteCodeText"><?php echo $userid_access;?></div>
                    <button class="copy-btn" id="copyCodeBtn">Copy</button>
                </div>
                <!-- Remove hardcoded domain if you have a variable or want relative base url, kept original for now -->
                <?php $inviteLinkFull = "https://www.boschch.com/bosch/register/" . $userid_access; ?>
                <div class="copy-row">
                    <div class="copy-text" id="inviteLinkText"><?php echo $inviteLinkFull; ?></div>
                    <button class="copy-btn" id="copyLinkBtn">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
      <div class="tips-box">
        <h6>Kind tips:</h6>
        <p>When you successfully invite friends to join, you will receive corresponding invitation bonuses and commissions.<br>
        When the B/C/D members of your team receive product income, the commission income will be automatically transferred to your account. The larger your team, the higher your commission income will be.<br>
        You get 10% commission from B-members<br>
        You get 5% commission from C-members<br>
        You get 2% commission from D-members</p>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script for copy functionality and pop-up -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var copyCodeBtn = document.getElementById('copyCodeBtn');
  var copyLinkBtn = document.getElementById('copyLinkBtn');
  var copyCodeText = document.getElementById('inviteCodeText');
  var copyLinkText = document.getElementById('inviteLinkText');

  copyCodeBtn.addEventListener('click', function() {
    var inviteCode = copyCodeText.innerText;
    navigator.clipboard.writeText(inviteCode).then(function() {
      showSuccessMessage("Copied: " + inviteCode);
    }).catch(function(err) {
      console.error('Error copying text: ', err);
    });
  });

  copyLinkBtn.addEventListener('click', function(event) {
    event.preventDefault(); 
    var inviteLink = copyLinkText.innerText;
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
