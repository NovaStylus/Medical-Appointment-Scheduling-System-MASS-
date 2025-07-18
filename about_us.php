<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - JK Hospital</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #e9f0f7, #f4f7fa);
            color: #333;
        }

        .navbar {
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 5px 15px;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .navbar strong {
            font-size: 20px;
            letter-spacing: 1px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .navbar a:hover {
            opacity: 0.8;
        }

        .about-container {
            max-width: 900px;
            margin: 60px auto;
            padding: 40px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .about-container h1 {
            font-size: 36px;
            color: #007bff;
            margin-bottom: 25px;
        }

        .about-container p {
            font-size: 17px;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        footer {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 25px;
            font-size: 15px;
            box-shadow: 0 -1px 8px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar a {
                margin-left: 0;
                margin-top: 10px;
            }

            .about-container {
                padding: 25px;
                margin: 30px 15px;
            }

            .about-container h1 {
                font-size: 28px;
            }

            .about-container p {
                font-size: 16px;
            }
        }
    </style>
    
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-primary py-3">
  <div class="container-fluid">
    <div class="me-5">
     <img src="img/jk.png" alt="Bootstrap" width="65" height="34">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    </div>
   
  </div>
</nav>

    <div class="about-container">
        <h1 style="text-align:center;">About Us</h1>
        <p>
            <strong>JK Hospital</strong> is a reputable healthcare provider affiliated with the University of Dar es Salaam. Our mission is to deliver top-quality healthcare services while promoting medical research, health education, and community well-being.
        </p>
        <p>
            We are proud to be at the forefront of digital healthcare innovation through our <strong>Medical Appointment Scheduling System (MASS)</strong>. This platform allows patients to effortlessly book, reschedule, or cancel appointments with our experienced medical team—including general doctors, dentists, and eye specialists.
        </p>
        <p>
            MASS simplifies the patient journey by reducing wait times, improving service delivery, and providing secure access to healthcare services from anywhere. Whether it's for routine check-ups or specialist care, our goal is to make healthcare efficient and accessible.
        </p>
        <p>
            At JK Hospital, we believe in compassionate care, patient safety, and continuous improvement. We combine state-of-the-art technology with a human-centered approach to serve our diverse community with integrity and excellence.
        </p>
    </div>

    <footer>
        &copy; <?= date('Y') ?> JK Hospital. All rights reserved.
    </footer>

</body>
</html>
