<?php
session_name("user_session");
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$method = $_GET['method'] ?? '';
$card_number = $_GET['card_number'] ?? '';
$appointment_id = $_GET['appointment_id'] ?? '';

if (!$method || !$card_number || !$appointment_id) {
    header("Location: payment-method.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Enter Expiry & CVC</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f2f5;
    }
    #payment-box {
      background: #fff;
      padding: 25px;
      width: 360px;
      margin: 50px auto;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }
    input, button, select {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      box-sizing: border-box;
    }
    label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
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
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
  </style>
</head>
<body>
<div id="payment-box">
  <h2>Enter Expiry & CVC</h2>
  <form action="process-fake-payment.php" method="POST">
    <input type="hidden" name="method" value="<?= htmlspecialchars($method) ?>">
    <input type="hidden" name="card_number" value="<?= htmlspecialchars($card_number) ?>">
    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_id) ?>">

    <label for="amount">Select Amount (TZS):</label>
    <select name="amount" id="amount" required>
      <option value="" disabled selected>Select amount</option>
      <option value="10000">TZS 10,000</option>
      <option value="20000">TZS 20,000</option>
    </select>

    <label for="expiry">Expiry (MM/YY):</label>
    <input type="text" name="expiry" id="expiry" required placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/\d{2}">

    <label for="cvc">CVC:</label>
    <input type="text" name="cvc" id="cvc" required placeholder="123" maxlength="4" pattern="\d{3,4}">

    <button type="submit">Submit</button>
  </form>
</div>
</body>
</html>
