<?php
session_start();
$initial = '?';
if (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}
require_once "database.php";

// Payment values by department
$payments = [
    'General' => 10000,
    'Eye' => 20000,
    'Dental' => 20000
];

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $name = $_POST['fullName'];
    $phone = $_POST['phone'];
    $dept = $_POST['department'];
    $payment = $payments[$dept] ?? 0;

    $stmt = $conn->prepare("UPDATE walkin_patients SET fullName = ?, phone = ?, department = ?, payment = ? WHERE id = ?");
    $stmt->bind_param("sssdi", $name, $phone, $dept, $payment, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_walkins.php");
    exit();
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Walk-in Patients</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
   
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1250px;
      margin: 0px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    h3 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
    }
    th {
      background-color: #007bff;
      color: #fff;
    }
    a.btn, button.btn {
      padding: 6px 12px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      border: none;
      cursor: pointer;
    }
    a.btn:hover, button.btn:hover {
      background-color: #0056b3;
    }
    input[type="text"], select {
      padding: 6px;
      width: 90%;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    form {
      margin: 0;
    }
    .search-form {
      text-align: center;
      margin: 20px 0;
    }
    .search-form input[type="text"] {
      width: 300px;
      font-size: 16px;
    }
    .search-form input[type="submit"] {
      font-size: 16px;
      margin-left: 8px;
    }
  

.nav-links {
    display: flex;
    align-items: center;
    gap: 15px; /* spacing between links */
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

        .navbar .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }

        .navbar .nav-links a:hover {
            text-decoration: underline;
        }
  </style>
  <?php require_once 'darkmode.php'; ?> 
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

  <div class="container">
    <h3>Manage Walk-in Patients</h3>

    <!-- Search Form -->
    <form class="search-form" method="GET" action="">
      <input type="text" name="search" placeholder="Search by name, phone or department..." value="<?= htmlspecialchars($search) ?>">
      <input type="submit" value="Search" class="btn btn-primary">
    </form>

    <table>
      <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Phone</th>
        <th>Department</th>
        <th>Payment</th>
        <th>Actions</th>
      </tr>
      <?php
      if ($search !== '') {
          $like = "%" . $conn->real_escape_string($search) . "%";
          $stmt = $conn->prepare("SELECT * FROM walkin_patients WHERE fullName LIKE ? OR phone LIKE ? OR department LIKE ?");
          $stmt->bind_param("sss", $like, $like, $like);
          $stmt->execute();
          $result = $stmt->get_result();
      } else {
          $result = $conn->query("SELECT * FROM walkin_patients");
      }

      $counter = 1;
      while ($row = $result->fetch_assoc()):
        if (isset($_GET['edit']) && $_GET['edit'] == $row['id']):
      ?>
        <form method="post">
          <tr>
            <td><?= $counter++ ?></td>
            <td><input type="text" name="fullName" value="<?= htmlspecialchars($row['fullName']) ?>" required></td>
            <td><input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required></td>
            <td>
              <select name="department" required>
                <?php foreach ($payments as $dept => $amt): ?>
                  <option value="<?= $dept ?>" <?= $dept == $row['department'] ? 'selected' : '' ?>><?= $dept ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td><?= number_format($row['payment']) ?></td>
            <td>
              <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
              <button type="submit" class="btn">Save</button>
              <a href="manage_walkins.php" class="btn">Cancel</a>
            </td>
          </tr>
        </form>
      <?php else: ?>
        <tr>
          <td><?= $counter++ ?></td>
          <td><?= htmlspecialchars($row['fullName']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['department']) ?></td>
          <td><?= number_format($row['payment']) ?></td>
          <td>
            <a href="manage_walkins.php?edit=<?= $row['id'] ?>" class="btn">Edit</a>
          </td>
        </tr>
      <?php endif; endwhile; ?>
    </table>
  </div>
</body>
</html>
