<?php
session_start();
$initial = '?';
if (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}

require_once 'database.php';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepare SQL with optional search filter
$sql = "SELECT a.*, u.fullName AS patient_name 
        FROM appointments a 
        JOIN users u ON a.user_id = u.id";

if ($search !== '') {
    $searchTerm = "%{$search}%";
    $sql .= " WHERE 
                u.fullName LIKE ? OR 
                a.department LIKE ? OR 
                a.status LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql .= " ORDER BY a.appointmentDate DESC, a.appointmentTime DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receptionist - Appointments</title>
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
    z-index: 1000; /* stays above other content */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }
        .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }
        .profile-circle {
            width: 35px;
            height: 35px;
            background-color: black;
            color: #007bff;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
            margin-left: auto;
            border: 2px solid #007bff;
            user-select: none;
        }
        .profile-circle:hover {
            background-color: #007bff;
            color: white;
        }
        h2 {
            text-align: center;
            margin: 30px 0 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        .badge {
            padding: 5px 10px;
            color: #fff;
            border-radius: 5px;
        }
        .badge.pending { background-color: orange; }
        .badge.confirmed { background-color: green; }
        .badge.completed { background-color: blue; }
        .badge.cancelled { background-color: red; }
        button {
            padding: 6px 10px;
            background: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        form.search-form {
            text-align: center;
            margin: 10px 0 20px;
        }
        input[type="text"] {
            padding: 8px 12px;
            width: 280px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 8px 15px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 8px;
        }
    </style>
    
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
          <a class="nav-link active text-white" aria-current="page" href="rec_dashb.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>

<h2>All Appointments</h2>

<!-- 🔍 Search form -->
<form class="search-form" method="GET" action="">
    <input type="text" name="search" placeholder="Search by patient, department, or status..." value="<?= htmlspecialchars($search) ?>">
    <input type="submit" value="Search">
</form>

<?php if ($result->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>Patient</th>
            <th>Department</th>
            <th>Date</th>
            <th>Time</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Mark as Completed</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['patient_name']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['appointmentDate']) ?></td>
            <td><?= htmlspecialchars($row['appointmentTime']) ?></td>
            <td><?= htmlspecialchars($row['amount']) ?> TSH</td>
            <td><span class="badge <?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
            <td>
                <?php if ($row['status'] === 'Confirmed'): ?>
                    <form action="mark_completed.php" method="POST" onsubmit="return confirm('Mark as completed?');">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-primary">✅ Complete</button>
                    </form>
                <?php elseif ($row['status'] === 'Pending'): ?>
                    ⏳ Waiting Confirmation
                <?php elseif ($row['status'] === 'Completed'): ?>
                    ✅ Done
                <?php elseif ($row['status'] === 'Cancelled'): ?>
                    ❌ Cancelled
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <p style="text-align:center;">No appointments found.</p>
<?php endif; ?>

</body>
</html>
