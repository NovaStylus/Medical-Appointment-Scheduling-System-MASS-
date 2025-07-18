<?php
session_name("admin_session");
session_start();
require_once 'database.php';

if (!isset($_SESSION['admin_id'])) {
    die("Access denied.");
}

// Date range filters
$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';

// Total bookings per department
$query1 = "
    SELECT d.name AS department, COUNT(a.id) AS total_bookings
    FROM appointments a
    JOIN departments d ON a.department_id = d.id
    GROUP BY a.department_id
    ORDER BY total_bookings DESC
";
$result1 = $conn->query($query1);
$dept_labels = [];
$dept_counts = [];
while ($row = $result1->fetch_assoc()) {
    $dept_labels[] = $row['department'];
    $dept_counts[] = (int)$row['total_bookings'];
}

// Booking counts by status
$query2 = "
    SELECT status, COUNT(*) AS count
    FROM appointments
    GROUP BY status
";
$result2 = $conn->query($query2);
$status_labels = [];
$status_counts = [];
while ($row = $result2->fetch_assoc()) {
    $status_labels[] = ucfirst($row['status']);
    $status_counts[] = (int)$row['count'];
}

// Bookings per day (last 7 days)
$query3 = "
    SELECT appointmentDate, COUNT(*) AS count
    FROM appointments
    WHERE appointmentDate >= CURDATE() - INTERVAL 6 DAY
    GROUP BY appointmentDate
    ORDER BY appointmentDate ASC
";
$result3 = $conn->query($query3);
$date_labels = [];
$date_counts = [];
while ($row = $result3->fetch_assoc()) {
    $date_labels[] = $row['appointmentDate'];
    $date_counts[] = (int)$row['count'];
}

// Popular time slots per department (top 3)
$query4 = "
    SELECT d.name AS department, a.appointmentTime, COUNT(*) AS count
    FROM appointments a
    JOIN departments d ON a.department_id = d.id
    GROUP BY a.department_id, a.appointmentTime
    ORDER BY d.name, count DESC
";
$result4 = $conn->query($query4);
$popular_slots = [];
while ($row = $result4->fetch_assoc()) {
    $dept = $row['department'];
    if (!isset($popular_slots[$dept])) {
        $popular_slots[$dept] = [];
    }
    if (count($popular_slots[$dept]) < 3) {
        $popular_slots[$dept][] = [
            'time' => $row['appointmentTime'],
            'count' => (int)$row['count']
        ];
    }
}

// For grouped bar chart
$time_slots = [];
foreach ($popular_slots as $dept => $slots) {
    foreach ($slots as $slot) {
        if (!in_array($slot['time'], $time_slots)) {
            $time_slots[] = $slot['time'];
        }
    }
}
sort($time_slots);

// Prepare datasets for each time slot with booking counts per department
$datasets = [];
foreach ($time_slots as $slot_time) {
    $data = [];
    foreach ($dept_labels as $dept) {
        $count = 0;
        if (isset($popular_slots[$dept])) {
            foreach ($popular_slots[$dept] as $slot) {
                if ($slot['time'] === $slot_time) {
                    $count = $slot['count'];
                    break;
                }
            }
        }
        $data[] = $count;
    }
    $datasets[] = [
        'label' => $slot_time,
        'data' => $data,
        'backgroundColor' => 'rgba(' . rand(50, 200) . ',' . rand(50, 200) . ',' . rand(50, 200) . ', 0.7)'
    ];
}

// Filtered total amount paid per department
$query5 = "
    SELECT d.name AS department, SUM(p.amount) AS total
    FROM payment p
    JOIN appointments a ON p.appointment_id = a.id
    JOIN departments d ON a.department_id = d.id
    WHERE p.status = 'paid'
";

if (!empty($start) && !empty($end)) {
    $query5 .= " AND DATE(p.created_at) BETWEEN '$start' AND '$end'";
}

$query5 .= " GROUP BY a.department_id";

$result5 = $conn->query($query5);
$dept_payment_labels = [];
$dept_payment_totals = [];
while ($row = $result5->fetch_assoc()) {
    $dept_payment_labels[] = $row['department'];
    $dept_payment_totals[] = (float)$row['total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Report with Charts</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            
        }
        .container {
            max-width: 960px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
        h2 {
            color: #333;
            margin-top: 40px;
        }
        canvas {
            max-width: 100%;
            margin-top: 0px;
        }
        #statusChart {
  max-width: 500px;
  max-height: 500px;
  margin: auto;
    padding-left: 20px;
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
            font-size: 20px;
            font-weight: bold;
        }
        .navbar .nav-links a, .navbar .nav-links button {
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
        .filter-form {
            text-align: center;
            margin: 20px 0;
        }
        .filter-form input[type="date"], .filter-form button {
            padding: 6px 10px;
            margin: 0 5px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <?php require_once 'darkmode.php'; ?> 
</head>
<body>
    <div style="height: 70px;"></div>
 <nav class="navbar navbar-expand-lg bg-body-primary">
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
          <a class="nav-link active text-white" aria-current="page" href="admin.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>

<div class="container" style="margin-top: 80px;">
    <!-- Date Filter Form -->
    <form method="get" class="filter-form">
        <label>From: </label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($start) ?>">
        <label>To: </label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($end) ?>">
        <button type="submit">Apply Filter</button>
        <button type="button" onclick="window.print()" style="background-color: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 6px; margin-left: 10px;">Print</button>
    </form>

    <h2>Total Bookings per Department</h2>
    <canvas id="deptChart"></canvas>

    <h2>Booking Counts by Status</h2>
    <canvas id="statusChart" ></canvas>

    <h2>Bookings Per Day (Last 7 Days)</h2>
    <canvas id="dayChart"></canvas>

    <h2>Top 3 Popular Time Slots per Department</h2>
    <canvas id="slotsChart"></canvas>

    <h2>Total Payments by Department<?= ($start && $end) ? " (Filtered: $start to $end)" : "" ?></h2>
    <canvas id="paymentChart"></canvas>
</div>

<script>
const deptCtx = document.getElementById('deptChart').getContext('2d');
new Chart(deptCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($dept_labels) ?>,
        datasets: [{
            label: 'Total Bookings',
            data: <?= json_encode($dept_counts) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, precision: 0 }
        }
    }
});

const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($status_labels) ?>,
        datasets: [{
            label: 'Booking Status',
            data: <?= json_encode($status_counts) ?>,
            backgroundColor: [
                'rgba(56, 123, 224, 0.93)',
                'rgba(231, 44, 44, 0.7)',
                'rgba(38, 163, 86, 0.7)',
                'rgba(153, 102, 255, 0.7)',
            ],
            borderColor: 'white',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true
    }
});


const dayCtx = document.getElementById('dayChart').getContext('2d');
new Chart(dayCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($date_labels) ?>,
        datasets: [{
            label: 'Bookings',
            data: <?= json_encode($date_counts) ?>,
            fill: false,
            borderColor: 'rgba(255, 159, 64, 1)',
            tension: 0.2,
            pointBackgroundColor: 'rgba(255, 159, 64, 0.7)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, precision: 0 }
        }
    }
});

const slotsCtx = document.getElementById('slotsChart').getContext('2d');
new Chart(slotsCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($dept_labels) ?>,
        datasets: <?= json_encode($datasets) ?>
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, precision: 0 },
            x: { stacked: true }
        },
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

const paymentCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($dept_payment_labels) ?>,
        datasets: [{
            label: 'Total Amount Paid (TZS)',
            data: <?= json_encode($dept_payment_totals) ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.7)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                precision: 0,
                title: {
                    display: true,
                    text: 'Amount in TZS'
                }
            }
        },
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Total Payments by Department'
            }
        }
    }
});
</script>
</body>
</html>
