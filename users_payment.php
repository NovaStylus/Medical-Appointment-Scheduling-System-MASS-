<?php
session_start();
require_once 'database.php';

// Handle date filtering if submitted
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$sql = "
    SELECT 
        p.id, 
        p.appointment_id,
        p.method, 
        p.card_number, 
        p.amount, 
        p.status, 
        p.created_at,
        a.department, 
        a.appointmentDate, 
        a.appointmentTime,
        u.fullName AS patient_name,
        u.email AS patient_email,
        u.phone AS patient_phone
    FROM payment p
    JOIN appointments a ON p.appointment_id = a.id
    JOIN users u ON a.user_id = u.id
";

if (!empty($start_date) && !empty($end_date)) {
    $sql .= " WHERE DATE(p.created_at) BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " ORDER BY p.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Payments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
   
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
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
            z-index: 1000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }
        .nav-links a, .nav-links button {
            color: white;
            margin-left: 15px;
            text-decoration: none;
            background: white;
            color: black;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
        }
        table {
            width: 98%;
            margin: 30px auto;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #10b981;
            color: white;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
        .filter-form {
            text-align: center;
            margin: 20px;
        }
        .filter-form input[type="date"], .filter-form button {
            padding: 8px;
            margin: 0 5px;
        }
        .print-btn {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
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
          <a class="nav-link active text-white" aria-current="page" href="rec_dashb.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>

<h2>All User Payment History</h2>

<div class="filter-form">
    <form method="get">
        <label>From:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        <label>To:</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
        <button type="submit">Filter</button>
        <button type="button" onclick="window.print()" class="print-btn">Print Results</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Payment ID</th>
            <th>Patient Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Appointment ID</th>
            <th>Method</th>
            <th>Card Number</th>
            <th>Amount (TSH)</th>
            <th>Status</th>
            <th>Department</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Paid At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= htmlspecialchars($row['patient_email']) ?></td>
                    <td><?= htmlspecialchars($row['patient_phone']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_id']) ?></td>
                    <td><?= htmlspecialchars($row['method']) ?></td>
                    <td><?= htmlspecialchars($row['card_number']) ?></td>
                    <td><?= number_format($row['amount']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['appointmentDate']) ?></td>
                    <td><?= htmlspecialchars($row['appointmentTime']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <button onclick="printRow(this)" style="padding: 5px 10px; background-color: #007bff; color: white; border: none; border-radius: 4px;">
                            Print
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="14">No payment records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
function printRow(button) {
    const row = button.closest('tr');
    const headers = Array.from(document.querySelectorAll('thead th')).slice(0, -1); // exclude Actions column
    const newTable = document.createElement('table');
    newTable.border = '1';
    newTable.style.borderCollapse = 'collapse';
    newTable.style.fontFamily = 'Arial, sans-serif';
    newTable.style.fontSize = '14px';
    newTable.style.margin = '20px auto';
    newTable.style.width = '90%';

    const headerRow = newTable.insertRow();
    headers.forEach(header => {
        const th = document.createElement('th');
        th.style.padding = '10px';
        th.style.backgroundColor = '#10b981';
        th.style.color = 'white';
        th.textContent = header.textContent;
        headerRow.appendChild(th);
    });

    const dataRow = newTable.insertRow();
    for (let i = 0; i < row.cells.length - 1; i++) {
        const td = document.createElement('td');
        td.style.padding = '10px';
        td.textContent = row.cells[i].textContent;
        dataRow.appendChild(td);
    }

    const printWindow = window.open('', '', 'width=900,height=600');
    printWindow.document.write('<html><head><title>Print Payment</title></head><body>');
    printWindow.document.write('<h3 style="text-align:center;">Payment Receipt</h3>');
    printWindow.document.write(newTable.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>

</body>
</html>
