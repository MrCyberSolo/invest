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

      /* Spinner Container */
      .spinner-container {
        position: relative;
        width: 300px;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      /* Spinner Image */
      .spinner-wheel {
        width: 100%;
        height: 100%;
        background: url('img/round.png')
          no-repeat center center;
        background-size: contain;
        border-radius: 50%;
        transform: rotate(0deg);
        transition: transform 3s cubic-bezier(0.2, 0.6, 0.4, 1.4);
        position: absolute;
      }

      /* Center Circle */
      .spinner-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        /*  background: #222;*/
        border-radius: 50%;
        z-index: 10;
        cursor: pointer;
      }

      .spinner-center:hover {
        /*  background: #444;*/
      }

      /* Spin Button Hover Effect */
      .spinner-center:active {
        transform: translate(-50%, -50%) scale(0.9);
      }

      /* Result Display */
      #result {
        background: #222222cc;
        padding: 20px;
        border-radius: 50px;
        margin-top: 20px;
        font-size: 1.5rem;
        text-align: center;
        text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
      }

      /* Audio Files (Hidden) */
      audio {
        display: none;
      }
    </style>
  </head>
  <body>
    <header>
      <h1>Spin the Wheel and Win Big!</h1>
    </header>

    <!-- Spinner -->
    <div class="spinner-container">
      <div id="spinner" class="spinner-wheel"></div>
      <?php 
      $count='1';
      if($count=='1'){ ?>
      <div class="spinner-center" id="spin-btn" onclick="spin()"></div>
      <?php } ?>
    </div>

    <!-- Result Display -->
    <div id="result"></div>

    <!-- Audio Effects -->
    <audio id="applause" src="./applause.mp3" type="audio/mp3"></audio>
    <audio id="wheel" src="./wheel.mp3" type="audio/mp3"></audio>
    <br>
    <div style=" background: #4e4148; padding: 10px; "><p><b>Lottery rules:</b></p>

<p>1: New members can get corresponding (big wheel) lucky draw opportunity by purchasing and activating any product.</p>

<p>2: Invite friends to join any product through the exclusive link, and you will get corresponding lucky draw opportunity (big wheel)</p>

<p>3: When you get the lottery opportunity, please be sure to participate in the lottery in time. The (big wheel lucky draw) opportunity is valid for 1 day. The system will automatically clear it at 00:00am.</p></div>
    <script>
      function spin() {
        // Disable the spin button
        const spinButton = document.getElementById("spin-btn");
        spinButton.style.pointerEvents = "none";

        // Play spin sound
        document.getElementById("wheel").play();

        // Generate a random spin angle
        const spinAngle = Math.floor(Math.random() * 360 + 3600); // At least 10 full spins
        const spinner = document.getElementById("spinner");
        spinner.style.transform = `rotate(${spinAngle}deg)`;

        // Make a POST request to PHP to get spin result
        fetch("", {
          method: "POST",
        })
          .then((response) => response.json())
          .then((data) => {
            // Wait for the animation to finish
            setTimeout(() => {
              // Play applause sound
              document.getElementById("applause").play();

              // Show the result
              document.getElementById(
                "result"
              ).innerText = `🎉 You won ₹${data.amount}!`;

              // Enable the spin button
              spinButton.style.pointerEvents = "auto";
            }, 3000);
          })
          .catch((error) => console.error("Error:", error));
      }
    </script>
  </body>
</html>
