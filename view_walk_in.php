<?php
session_start();
$initial = '?';
if (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}
require_once "database.php";

// Delete logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM walkin_patients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_walk_in.php");
    exit();
}

// Fetch all walk-in patients
$result = $conn->query("SELECT * FROM walkin_patients");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Delete Walk-in Patients</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  
  <style>
    body { font-family: sans-serif; background: #f0f0f0; padding: 20px; 
         margin: 0;
            padding: 0;}
        
.nav-links {
    display: flex;
    align-items: center;
    gap: 15px; /* spacing between links */
}


        .navbar {
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 10px 30px;
            align-items: center;
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
    a.btn {
      padding: 6px 12px;
      background-color: #dc3545;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      border: none;
    }
    a.btn:hover {
      background-color: #c82333;
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
      transition: background-color 0.3s, color 0.3s;
    }
    .profile-circle:hover {
      background-color: #007bff;
      color: white;
    }

    .navbar {
      background-color: #007bff;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 15px 30px;
      align-items: center;
    }

    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
    }
  </style>
  
</head>
<body>
 
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
          <a class="nav-link active text-white" aria-current="page" href="admin.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>



  <div class="container my-4">
    <h3>Delete Walk-in Patients</h3>
    <table>
      <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Phone</th>
        <th>Department</th>
        <th>Payment</th>
        <th>Action</th>
      </tr>
      <?php
      if ($result->num_rows > 0) {
        $i = 1;
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>" . $i++ . "</td>
                  <td>" . htmlspecialchars($row['fullName']) . "</td>
                  <td>" . htmlspecialchars($row['phone']) . "</td>
                  <td>" . htmlspecialchars($row['department']) . "</td>
                  <td>" . number_format($row['payment']) . " TZS</td>
                  <td><a class='btn' href='?delete={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a></td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='6'>No walk-in patients found.</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>
