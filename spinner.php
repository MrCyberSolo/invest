<?php
// Start session to store spin results
session_start();

// Check if spin is initiated via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a random number between 0 and 500
    $spinResult = rand(0, 500);

    // Store the spin result in a session variable
    if (!isset($_SESSION['spin_results'])) {
        $_SESSION['spin_results'] = [];
    }
    $_SESSION['spin_results'][] = $spinResult;

    // Return result as JSON
    header('Content-Type: application/json');
    echo json_encode(['amount' => $spinResult]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Spin the Wheel</title>
    <!-- bootstrap icons link  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
      body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #007749;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        color: #fff;
      }

      /* Header Styling */
      header h1 {
        margin: 0;
        padding: 10px;
        text-align: center;
        font-size: 2rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
      }

      /* Lottery Grid Container */
      .lottery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        width: 320px;
        margin: 20px auto;
        padding: 15px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(255, 206, 130, 0.4);
      }

      /* Lottery Box */
      .lottery-box {
        background: #fff;
        border-radius: 12px;
        height: 90px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #333;
        font-weight: 600;
        font-size: 13px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        border: 2px solid transparent;
        transition: all 0.1s ease-in-out;
      }

      .lottery-box i {
        font-size: 30px;
        margin-bottom: 3px;
        color: #007749;
      }

      /* Active State Highlighting */
      .lottery-box.active {
        background: #FFCE82;
        border: 2px solid #fff;
        transform: scale(1.05);
        box-shadow: 0 0 15px #FFCE82;
        z-index: 2;
      }

      .lottery-box.active i {
        color: #fff;
      }

      /* Start Button */
      .lottery-start {
        background: linear-gradient(135deg, #009a5f, #005a36);
        color: white;
        cursor: pointer;
        border: 2px solid #FFCE82;
      }

      .lottery-start:active {
        transform: scale(0.95);
      }

      .lottery-start i {
        color: white;
      }

      /* Result Modal */
      #result {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 90, 54, 0.95);
        border: 3px solid #FFCE82;
        padding: 40px 30px;
        border-radius: 20px;
        font-size: 1.8rem;
        text-align: center;
        text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
        box-shadow: 0 10px 40px rgba(0,0,0,0.6);
        z-index: 1000;
        color: white;
        min-width: 250px;
        cursor: pointer;
      }

      /* Audio Files (Hidden) */
      audio {
        display: none;
      }
    </style>
  
    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>
  <body>
    <header>
      <h1>Spin the Wheel and Win Big!</h1>
    </header>

    <!-- Lottery Grid -->
    <div class="lottery-grid" id="lotteryGrid">
      <div class="lottery-box" id="box-0"><i class="bi bi-ticket-perforated"></i> Ticket</div>
      <div class="lottery-box" id="box-1"><i class="bi bi-gift"></i> Voucher</div>
      <div class="lottery-box" id="box-2"><i class="bi bi-phone"></i> Phone</div>
      
      <div class="lottery-box" id="box-7"><i class="bi bi-smartwatch"></i> Watch</div>
      <?php 
      $count='1';
      if($count=='1'){ ?>
      <div class="lottery-box lottery-start" id="spin-btn" onclick="spin()"><i class="bi bi-play-circle-fill"></i> START</div>
      <?php } else { ?>
      <div class="lottery-box lottery-start" style="background:#888; border-color:#888;"><i class="bi bi-play-circle-fill"></i> START</div>
      <?php } ?>
      <div class="lottery-box" id="box-3"><i class="bi bi-cash-stack"></i> Cash</div>
      
      <div class="lottery-box" id="box-6"><i class="bi bi-camera"></i> Camera</div>
      <div class="lottery-box" id="box-5"><i class="bi bi-earbuds"></i> Earbuds</div>
      <div class="lottery-box" id="box-4"><i class="bi bi-speaker"></i> Speaker</div>
    </div>

    <!-- Result Display -->
    <div id="result"></div>

    <!-- Audio Effects -->
    <audio id="applause" src="./applause.mp3" type="audio/mp3"></audio>
    <audio id="wheel" src="./wheel.mp3" type="audio/mp3"></audio>
    <br>
    <div style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 12px; margin: 0 15px; font-size: 14px; line-height: 1.5;">
        <p style="margin-bottom: 10px; color: #FFCE82;"><b>Lottery rules:</b></p>
        <p style="margin-bottom: 8px;">1: New members can get corresponding (big wheel) lucky draw opportunity by purchasing and activating any product.</p>
        <p style="margin-bottom: 8px;">2: Invite friends to join any product through the exclusive link, and you will get corresponding lucky draw opportunity (big wheel)</p>
        <p style="margin-bottom: 0;">3: When you get the lottery opportunity, please be sure to participate in the lottery in time. The (big wheel lucky draw) opportunity is valid for 1 day. The system will automatically clear it at 00:00am.</p>
    </div>
    <script>
      const numBoxes = 8;
      let currentIdx = 0;
      let finalAmount = 0;
      let spinning = false;

      function spin() {
        if (spinning) return;
        spinning = true;

        const spinButton = document.getElementById("spin-btn");
        if(spinButton) spinButton.style.pointerEvents = "none";

        // Start wheel sound loop
        const wheelAudio = document.getElementById("wheel");
        wheelAudio.loop = true;
        wheelAudio.play().catch(e => console.log(e));

        // Fetch result immediately
        fetch("", { method: "POST" })
          .then((response) => response.json())
          .then((data) => {
            finalAmount = data.amount;
            startGridAnimation();
          })
          .catch((error) => {
            console.error("Error:", error);
            spinning = false;
            if(spinButton) spinButton.style.pointerEvents = "auto";
            wheelAudio.pause();
          });
      }

      function startGridAnimation() {
        let speed = 40; // initial fast speed in ms
        let steps = 0;
        let maxSteps = 40 + Math.floor(Math.random() * 10); // Randomize number of steps before slowing down
        
        function animateCycle() {
          // Remove active from all
          for(let i=0; i<numBoxes; i++) {
            document.getElementById(`box-${i}`).classList.remove('active');
          }
          // Add active to current
          document.getElementById(`box-${currentIdx}`).classList.add('active');
          
          currentIdx = (currentIdx + 1) % numBoxes;
          steps++;

          if (steps > maxSteps) {
            speed += 20; // Start decelerating
          }

          if (speed >= 350) {
            // Stop spinning
            const wheelAudio = document.getElementById("wheel");
            wheelAudio.pause();
            wheelAudio.currentTime = 0;
            
            document.getElementById("applause").play().catch(e => console.log(e));

            // Show Result
            setTimeout(() => {
                const resultBox = document.getElementById("result");
                resultBox.innerHTML = `🎉 You won <br> <b>₹${finalAmount}!</b><br><small style="font-size: 14px; opacity: 0.8;">(Tap to close)</small>`;
                resultBox.style.display = "block";
                
                // Clicking anywhere on modal hides it
                resultBox.onclick = function() {
                  resultBox.style.display = "none";
                  const spinButton = document.getElementById("spin-btn");
                  if(spinButton) spinButton.style.pointerEvents = "auto";
                  spinning = false;
                  // Remove highlight
                  for(let i=0; i<numBoxes; i++) {
                    document.getElementById(`box-${i}`).classList.remove('active');
                  }
                };
            }, 500);
            return;
          }

          setTimeout(animateCycle, speed);
        }

        animateCycle();
      }
    </script>
  </body>
</html>
