<?php 
session_name("user_session");
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please login.");
}

if (!isset($_GET['appointment_id'])) {
    die("No appointment ID specified.");
}

$appointment_id = (int)$_GET['appointment_id'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Select Payment Method</title>
  <style>
      <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    #payment-box {
      background: #f9f9f9;
      padding: 20px;
      width: 350px;
      margin: 80px auto;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
      text-align: center;
    }
    h2 {
      margin-bottom: 20px;
      color: #333;
    }
    select, button {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      box-sizing: border-box;
    }
    button {
      background-color: #28a745;
      color: white;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
  </style>
</head>
<body>
<div id="payment-box">
  <h2>Select Payment Method</h2>
  <form action="enter-card.php" method="GET">
    <!-- Pass the appointment id forward -->
    <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
    
    <select name="method" required>
      <option value="">-- Choose --</option>
      <option value="debit">Debit Card</option>
      <option value="credit">Credit Card</option>
    </select>
    <button type="submit">Next</button>
  </form>
</div>
</body>
</html>
