<?php
session_start();
unset($_SESSION['form_submitted']); // reset the form submitted flag to allow new booking
?>
<!DOCTYPE html>
<html>
<head>
  <title>Enter Card Number</title>
 <style>
    body { font-family: Arial, sans-serif; }
    #payment-box {
      background: #f9f9f9;
      padding: 20px;
      width: 350px;
      margin: 50px auto;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }
    button {
      background-color: #28a745;
      color: white;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
<div id="payment-box">
  <h2>Enter Card Number</h2>
  <form action="enter_expiry.php" method="GET">
<input type="hidden" name="method" value="<?= htmlspecialchars($_GET['method']) ?>">
    <input type="hidden" name="method" value="<?= htmlspecialchars($_GET['method']) ?>">

    <label>Card Number:</label>
    <input type="text" name="card_number" required maxlength="19" placeholder="1234 5678 9012 3456">
    <button type="submit">Next</button>
  </form>
</div>
</body>
</html>
