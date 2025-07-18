<?php
session_start();

// If form was already submitted, prevent access
if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] === true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Enter Expiry & CVC</title>
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
    input, button, select {
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
  <h2>Enter Expiry & CVC</h2>
  <form action="process_fake_payment.php" method="POST">
    <input type="hidden" name="method" value="<?= htmlspecialchars($_GET['method']) ?>">
    <input type="hidden" name="card_number" value="<?= htmlspecialchars($_GET['card_number']) ?>">

    <!-- User-selected Amount -->
    <label>Select Amount (TZS):</label>
    <select name="amount" required>
       <option value="" disabled selected>Select amount</option>
      <option value="10000">TZS 10,000</option>
      <option value="20000">TZS 20,000</option>
    </select>

    <label>Expiry (MM/YY):</label>
    <input type="text" name="expiry" required placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/\d{2}">

    <label>CVC:</label>
    <input type="text" name="cvc" required placeholder="123" maxlength="4" pattern="\d{3,4}">

    <button type="submit">Submit</button>
  </form>
</div>
</body>
</html>
