<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Image Slider in Card</title>
<style>
  /* Styles for card */
  .card {
    width: 300px;
    border: 1px solid #ccc;
    border-radius: 5px;
    overflow: hidden;
    margin: 20px;
  }
  
  /* Styles for slider container */
  .slider-container {
    width: 100%;
    overflow: hidden;
    position: relative;
  }
  
  /* Styles for slider */
  .slider {
    display: flex;
    transition: transform 0.5s ease-in-out;
  }
  
  /* Styles for slider items */
  .slide {
    min-width: 100%;
    overflow: hidden;
  }
  
  /* Styles for image */
  .slide img {
    width: 100%;
    height: auto;
  }
</style>
</head>
<body>

<div class="card">
  <div class="slider-container">
    <div class="slider">
      <div class="slide"><img src="https://miro.medium.com/max/2400/0*hDAyhnOx767w5qma.jpg" alt="Image 1"></div>
      <div class="slide"><img src="https://miro.medium.com/max/2400/0*hDAyhnOx767w5qma.jpg" alt="Image 2"></div>
      <div class="slide"><img src="https://miro.medium.com/max/2400/0*hDAyhnOx767w5qma.jpg" alt="Image 3"></div>
    </div>
  </div>
</div>

<script>
  // JavaScript for slider functionality
  const slider = document.querySelector('.slider');
  const slides = document.querySelectorAll('.slide');
  const totalSlides = slides.length;
  let currentIndex = 0;

  function showSlide(index) {
    if (index < 0) {
      index = totalSlides - 1;
    } else if (index >= totalSlides) {
      index = 0;
    }

    const offset = -index * 100;
    slider.style.transform = `translateX(${offset}%)`;
    currentIndex = index;
  }

  // Automatic sliding
  setInterval(() => {
    showSlide(currentIndex + 1);
  }, 3000); // Change slide every 3 seconds
</script>

</body>
</html>
