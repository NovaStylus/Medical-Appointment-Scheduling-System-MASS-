<?php 
session_name("user_session");
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$method = $_GET['method'] ?? '';
if (!$method) {
    header("Location: payment-method.php");
    exit;
}

$appointment_id = $_GET['appointment_id'] ?? '';
if (!$appointment_id) {
    die("No appointment ID specified.");
}
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
  <h2>Enter Card Number (<?= htmlspecialchars(ucfirst($method)) ?>)</h2>
  <form action="enter-expiry.php" method="GET">
    <input type="hidden" name="method" value="<?= htmlspecialchars($method) ?>">
    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_id) ?>">
    
    <label>Card Number:</label>
    <input type="text" name="card_number" required maxlength="19" placeholder="1234 5678 9012 3456" pattern="\d{13,19}">
    <button type="submit">Next</button>
  </form>
</div>
</body>
</html>
