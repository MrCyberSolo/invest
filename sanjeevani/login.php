<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HOC Global Share Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #fff;
      overflow-x: hidden;
    }

    /* Gold curved header */
    .header-bg {
      background: radial-gradient(circle at center, #d4af37, #b88a3b);
      height: 150px;
      width: 100%;
      border-bottom-left-radius: 50% 25%;
      border-bottom-right-radius: 50% 25%;
      position: relative;
      overflow: hidden;
    }

    .header-bg::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url('https://www.transparenttextures.com/patterns/dark-mosaic.png');
      opacity: 0.1;
    }

    .back-btn {
      position: absolute;
      top: 20px;
      left: 20px;
      color: #fff;
      text-decoration: none;
      font-size: 16px;
    }

    .back-btn:hover {
      color: #fff8d0;
    }

    /* Login box */
    .login-box {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 90%;
      max-width: 380px;
      margin: 20px auto 20px;
      padding: 30px;
    }

    .form-control {
      border: none;
      border-bottom: 1px solid #ccc;
      border-radius: 0;
      padding-left: 0;
      font-size: 15px;
      box-shadow: none;
    }

    .form-control:focus {
      border-color: #b58a33;
      box-shadow: none;
    }

    .btn-gold {
      background: linear-gradient(to right, #d4af37, #b88a3b);
      border: none;
      border-radius: 50px;
      color: #fff;
      font-weight: 600;
      padding: 10px 0;
      width: 100%;
      transition: 0.3s;
    }

    .btn-gold:hover {
      opacity: 0.9;
    }

    .captcha-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .captcha-img {
      background: #f8f8f8;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 5px 10px;
      font-weight: 600;
      color: #6b2680;
      letter-spacing: 2px;
    }

    .footer {
      text-align: center;
      margin-top: 25px;
      font-size: 14px;
      color: #333;
    }

    .links a {
      color: #555;
      text-decoration: none;
      font-size: 14px;
    }

    .links a:hover {
      color: #b58a33;
    }
  </style>
</head>
<body>

  <div class="header-bg">
    <a href="#" class="back-btn"><i class="bi bi-chevron-left"></i>Back</a>
  </div>

  <div class="login-box">
    <form>
      <div class="mb-3">
        <label class="form-label">Phone</label>
        <div class="input-group">
          <span class="input-group-text border-0 bg-white">+91</span>
          <input type="text" class="form-control" placeholder="Please enter mobile number">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" placeholder="Please enter the login password">
      </div>

      <div class="mb-3">
        <label class="form-label">Graphic verification code</label>
        <div class="captcha-box">
          <input type="text" class="form-control me-2" placeholder="please enter">
          <div class="captcha-img" id="captchaCode">2449</div>
        </div>
      </div>

      <button type="submit" class="btn btn-gold mt-4">Sign in</button>

      <div class="d-flex justify-content-between mt-3 links">
        <a href="#">Forget password</a>
        <a href="#">Register now</a>
      </div>
    </form>
  </div>

  <div class="footer">HOC Global Share © 2025</div>

  <script>
    // Optional: random captcha generator
    const captcha = document.getElementById('captchaCode');
    captcha.textContent = Math.floor(1000 + Math.random() * 9000);
  </script>

</body>
</html>
