<?php
session_start();

$choice = $_POST['choice'] ?? '';

if ($choice === 'Pay Now') {
    // Redirect to payment gateway or payment form
    header('Location: book.php');
    exit;
}

if ($choice === 'Pay Later') {
    // Show warning and give user option to go back or continue
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Pay Later Warning</title>
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
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 25px;
                border-radius: 12px;
                box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
                text-align: center;
            }
            .warning {
                background-color: #ffc107;
                border: 1px solid #ffeeba;
                padding: 20px;
                margin: 20px 0;
                border-radius: 10px;
                color: white;
            }
            .btn {
                padding: 12px 24px;
                margin: 10px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: bold;
                display: inline-block;
            }
            .btn-secondary {
                background-color: #6c757d;
                color: white;
            }
            .btn-success {
                background-color: #28a745;
                color: white;
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
    <div class="container my-5">
        <h2 >Pay Later Selected</h2>
        <div class="warning">
            Your appointment will be <strong>cancelled automatically</strong> if not paid within <strong>30 minutes</strong> from time of booking.
        </div>

        <a href="javascript:history.back()" class="btn btn-secondary">Return to Previous Page</a>
        <a href="books.php" class="btn btn-success">Continue and Book</a>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Fallback
header('Location: payment_option.php');
exit;
