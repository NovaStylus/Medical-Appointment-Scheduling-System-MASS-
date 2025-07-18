<?php
// form.php
$appointment_id = $_GET['appointment_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enter Appointment ID</title>
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
    label {
      display: block;
      text-align: left;
      margin-bottom: 8px;
      font-weight: bold;
      color: #333;
    }
    input[type="number"], button {
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
</head>
<body>
  <div id="payment-box">
    <h2>Verify Your Appointment ID</h2>
    <form method="GET" action="payment-process.php">
        <label for="appointment_id">Appointment ID:</label>
        <input type="number" name="appointment_id" id="appointment_id" required value="<?= htmlspecialchars($appointment_id) ?>">
        <button type="submit">Go to Payment</button>
    </form>
  </div>
</body>
</html>
