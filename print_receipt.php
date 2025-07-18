<?php
require_once 'database.php';

if (!isset($_GET['payment_id'])) {
    die("Invalid request.");
}

$payment_id = intval($_GET['payment_id']);

$sql = "
    SELECT 
        p.*, 
        a.department, 
        a.appointmentDate, 
        a.appointmentTime
    FROM payment p
    JOIN appointments a ON p.appointment_id = a.id
    WHERE p.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Payment not found.");
}

$payment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
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
      padding: 5px 15px;
      align-items: center;
        background-color: #007bff;

  position: fixed;  /* <--- add this */
  top: 0;           /* <--- add this */
  left: 0;
  right: 0;
  z-index: 1000;    /* <--- add this */
}

        .navbar .logo {
            font-weight: bold;
            font-size: 18px;
        }

        .navbar button {
            border-radius: 10px;
            padding: 8px 16px;
            background-color: white;
            color: black;
            border: none;
            cursor: pointer;
        }

        .receipt-container {
            background: white;
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 10px;
        }

        .receipt-header h2 {
            margin: 0;
            color: #10b981;
        }

        .info p {
            margin: 10px 0;
            font-size: 15px;
        }

        .info strong {
            display: inline-block;
            width: 180px;
            color: #333;
        }

        .print-btn {
            text-align: center;
            margin-top: 30px;
        }

        .print-btn button {
            padding: 12px 24px;
            font-size: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .print-btn button:hover {
            background-color: #0056b3;
        }

        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 13px;
            color: #666;
        }

        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 60px;
            color: rgba(16, 185, 129, 0.1);
            z-index: 0;
            user-select: none;
        }

        @media print {
            .navbar,
            .print-btn {
                display: none;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
            }

            body {
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">+ MASS</div>
        <button onclick="window.location.href='index.php'">Home</button>
    </div>

    <div class="receipt-container">
        <div class="watermark">PAID</div>

        <div class="receipt-header">
            <h2>Payment Receipt</h2>
            <p>Thank you for your payment</p>
        </div>

        <div class="info">
            <p><strong>Receipt ID:</strong> <?= $payment['id'] ?></p>
            <p><strong>Appointment ID:</strong> <?= $payment['appointment_id'] ?></p>
            <p><strong>Department:</strong> <?= $payment['department'] ?></p>
            <p><strong>Appointment Date:</strong> <?= $payment['appointmentDate'] ?></p>
            <p><strong>Appointment Time:</strong> <?= $payment['appointmentTime'] ?></p>
            <p><strong>Payment Method:</strong> <?= $payment['method'] ?></p>
            <p><strong>Card Number:</strong> <?= $payment['card_number'] ?></p>
            <p><strong>Amount Paid:</strong> <?= number_format($payment['amount']) ?> TSH</p>
            <p><strong>Status:</strong> <?= strtoupper($payment['status']) ?></p>
            <p><strong>Payment Date:</strong> <?= $payment['created_at'] ?></p>
        </div>

        <div class="print-btn">
            <button onclick="window.print()">🖨️ Print Receipt</button>
        </div>

        <div class="footer-note">
            MASS SYSTEM &copy; <?= date("Y") ?> | This is a system-generated receipt
        </div>
    </div>
</body>
</html>
