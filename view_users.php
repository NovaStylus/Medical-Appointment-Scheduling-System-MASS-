<?php
session_name("admin_session");
session_start();
require_once 'database.php';

// Restrict access to admins only
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchEscaped = $conn->real_escape_string($search);

// SQL with optional search
$query = "
    SELECT 
        p.fullName, p.email, p.phone, 
        a.department, a.appointmentDate, a.appointmentTime, a.amount, a.status 
    FROM 
        appointments a
    JOIN 
        users p ON a.user_id = p.id
";

if (!empty($search)) {
    $query .= " WHERE 
        p.fullName LIKE '%$searchEscaped%' OR 
        p.email LIKE '%$searchEscaped%' OR 
        p.phone LIKE '%$searchEscaped%' OR 
        a.department LIKE '%$searchEscaped%'";
}

$query .= " ORDER BY a.appointmentDate DESC, a.appointmentTime DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin: User Appointments</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  
    <style>
        body {
            font-family: sans-serif;
            background: #f0f0f0;
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
    z-index: 1000; /* ensures it stays above everything */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .navbar .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }

        .navbar .nav-links a:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            margin: 30px 0 10px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 280px;
        }

        button {
            padding: 8px 12px;
            cursor: pointer;
            height:45px;
        }

        table {
            width: 95%;
            margin: 0 auto 40px;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status {
            font-weight: bold;
        }

        .status.Pending {
            color: orange;
        }

        .status.Paid {
            color: green;
        }

        .status.Cancelled {
            color: red;
        }

        .no-data {
            text-align: center;
            color: red;
            font-weight: bold;
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
          <a class="nav-link active text-white" aria-current="page" href="admin.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>

    <h2>All Appointments with Patient Info</h2>

    <!-- 🔍 Search form -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by name, email, phone, or department" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Date</th>
                <th>Time</th>
                <th>Amount (TSH)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fullName']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['appointmentDate']) ?></td>
                        <td><?= htmlspecialchars($row['appointmentTime']) ?></td>
                        <td><?= number_format($row['amount']) ?></td>
                        <td class="status <?= htmlspecialchars($row['status']) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" class="no-data">No appointments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
