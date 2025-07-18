<?php include 'google_translate.php'; ?>
<?php
session_name("user_session");
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your payments.");
}

$user_id = $_SESSION['user_id'];

// Fetch payment info with appointment details
$sql = "
    SELECT 
        p.id, 
        p.appointment_id,
        p.method, 
        p.card_number, 
        p.expiry,
        p.cvc,
        p.amount, 
        p.status, 
        p.created_at,
        a.department, 
        a.appointmentDate, 
        a.appointmentTime
    FROM payment p
    JOIN appointments a ON p.appointment_id = a.id
    WHERE a.user_id = ?
    ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Payments</title>
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
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: #007bff;
    color: white;
    display: flex;
    justify-content: space-between;
    padding: 15px 30px;
    align-items: center;
    z-index: 1000; /* stays above other content */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }

        table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #10b981;
            color: white;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        .print-button {
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
  
</head>
<body>
      <div style="height: 70px;"></div>
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


    <h2>My Payment History</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Appointment ID</th>
                <th>Method</th>
                <th>Card Number</th>
                <th>Amount (TSH)</th>
                <th>Status</th>
                <th>Department</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['appointment_id']) ?></td>
                        <td><?= htmlspecialchars($row['method']) ?></td>
                        <td><?= htmlspecialchars($row['card_number']) ?></td>
                        <td><?= number_format($row['amount']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['appointmentDate']) ?></td>
                        <td><?= htmlspecialchars($row['appointmentTime']) ?></td>
                        <td>
                            <a class="print-button" href="print_receipt.php?payment_id=<?= $row['id'] ?>" target="_blank">Print</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10">No payments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
