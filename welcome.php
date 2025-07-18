<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Online Hospital Appointment</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: #f0f8ff;
    }

    .hero {
      width: 100%;
      height: 100vh;
      background: url('img/1.jpg') no-repeat center center/cover;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 50, 0.4); /* darken image slightly */
      z-index: 1;
    }

    .content-box {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      padding: 40px 30px;
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      text-align: center;
      color: #fff;
      animation: fadeIn 2.2s ease-in-out;
      max-width: 90%;
    }

    .content-box h1 {
      font-size: 2.8rem;
      margin-bottom: 20px;
    }

    .content-box p {
      font-size: 1.2rem;
      max-width: 600px;
      margin: 0 auto 30px;
    }

    .get-started-btn {
      background-color: #00b4d8;
      color: white;
      padding: 14px 28px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .get-started-btn:hover {
      background-color: #0096c7;
      transform: scale(1.05);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      .content-box h1 {
        font-size: 2rem;
      }
      .content-box p {
        font-size: 1rem;
      }
      .get-started-btn {
        width: 100%;
        font-size: 1.1rem;
      }
    }
    .blink {
  animation: blinkText 1s infinite;
}

@keyframes blinkText {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

  </style>
</head>
<body>

  <div class="hero">
    <div class="overlay"></div>
    <div class="content-box">
      <h1>Welcome to JK Hospital</h1>
      <p>Book appointments online with experienced doctors. Fast, easy, and secure.</p>
      <button class="get-started-btn blink" onclick="window.location.href='login.php'">
  Get Started
</button>

    </div>
  </div>

</body>
</html>
