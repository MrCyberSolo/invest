<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transparent Popup with Copy Functionality</title>
<style>
  /* Styles for the overlay */
  .overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Transparent black overlay */
    z-index: 9999;
    justify-content: center;
    align-items: center;
    text-align: center;
  }

  /* Styles for the popup */
  .popup {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  }

  /* Styles for the success message */
  .success-message {
    background-color: rgba(0, 255, 0, 0.7); /* Transparent green */
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
  }
</style>
</head>
<body>

<!-- Link to trigger the popup -->
<a href="#" id="popup-link">Open Popup</a>

<!-- Overlay and Popup -->
<div class="overlay" id="overlay">
  <div class="popup">
    <h2>This is a Popup Message</h2>
    <p>This is a transparent popup message.</p>
    <form id="copy-form">
      <label for="userInput">Enter something:</label>
      <input type="text" id="userInput" name="userInput" value="anupsingh
