<?php
// Start session or retrieve booking data if needed
session_start();

// Optional: Store booking data in session for later processing
$_SESSION['booking_data'] = $_POST ?? [];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Payment Option</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
      body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }

        .navbar {
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 25px;
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn-warning {
            background-color: #ffc107;
            color: black;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            color: #856404;
        }
    </style>
     <?php require_once 'darkmode.php'; ?> 
</head>
<body>
  
   <nav class="navbar navbar-expand-lg bg-body-primary py-2">
  <div class="container-fluid">
    <div class="me-5">
     <img src="img/jk.png" alt="Bootstrap" width="65" height="34">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    </div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active text-white" aria-current="page" href="index.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>
<div class="container my-5" style="max-width: 420px;">
  <h2 class="mb-4 text-center">Choose Payment Option</h2>
  
  <form method="post" action="payment_choice.php" class="d-flex flex-column gap-3">
    <input type="submit" name="choice" value="Pay Now" class="btn btn-primary btn-lg">
    <input type="submit" name="choice" value="Pay Later" class="btn btn-warning btn-lg">
  </form>
</div>

</body>
</html>
