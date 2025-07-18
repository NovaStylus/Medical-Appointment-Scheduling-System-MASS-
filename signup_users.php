<?php
session_start();
$initial = '?';
if (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Users</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
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

    .nav-links {
      display: flex;
      align-items: center;
      gap: 15px;
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

    .navbar .nav-links a {
      color: white;
      margin-left: 15px;
      text-decoration: none;
    }

    .navbar .nav-links a:hover {
      text-decoration: underline;
    }

    .container {
      max-width: 1250px;
      margin: 0px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    h3 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .error {
      color: red;
      text-align: center;
    }

    form {
      text-align: center;
      margin-bottom: 20px;
    }

    input[type="text"] {
      padding: 8px;
      width: 250px;
    }

    button {
      padding: 8px 12px;
      height:45px;
      cursor: pointer;
    }

    /* Dark mode */
    body.dark-mode {
      background-color: #121212;
    }

    body.dark-mode a {
      color: #90caf9;
    }

    body.dark-mode header,
    body.dark-mode footer {
      background-color: #1e1e1e;
    }

    body.dark-mode input,
    body.dark-mode button {
      background-color: #2c2c2c;
      color: #fff;
      border: 1px solid #444;
    }

    body.dark-mode h2 {
      color: #f0f0f0;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
      margin-left: 15px;
      vertical-align: middle;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      background-color: #ccc;
      border-radius: 34px;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      border-radius: 50%;
      background-color: white;
      left: 3px;
      bottom: 3px;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:checked + .slider:before {
      transform: translateX(24px);
    }
  </style>
   
</head>
<body>
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

  <div class="container my-4">
    <h3>Registered Users</h3>

    <!-- 🔍 Search form -->
    <form method="GET" action="">
      <input type="text" name="search" placeholder="Search by name, email or phone" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
      <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php
    require_once "database.php";

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    if (!empty($search)) {
      $searchEscaped = $conn->real_escape_string($search);
      $sql = "SELECT id, fullName, email, phone, created_at 
              FROM users 
              WHERE fullName LIKE '%$searchEscaped%' 
                 OR email LIKE '%$searchEscaped%' 
                 OR phone LIKE '%$searchEscaped%' 
              ORDER BY id DESC";
    } else {
      $sql = "SELECT id, fullName, email, phone, created_at 
              FROM users 
              ORDER BY id DESC";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      echo "<table>
              <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Signup Date</th>
              </tr>";
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['fullName']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['phone']) . "</td>
                <td>" . htmlspecialchars(date("d M Y H:i", strtotime($row['created_at']))) . "</td>
              </tr>";
      }
      echo "</table>";
    } else {
      echo "<p class='error'>No users found.</p>";
    }

    $conn->close();
    ?>
  </div>

  <script>
    const toggle = document.getElementById('darkModeToggle');
    const label = document.getElementById('modeLabel');

    document.addEventListener('DOMContentLoaded', function () {
      if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
        toggle.checked = true;
        label.textContent = 'Dark Mode';
      }
    });

    if (toggle) {
      toggle.addEventListener('change', function () {
        document.body.classList.toggle('dark-mode');
        const mode = document.body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', mode);
        label.textContent = mode ? 'Dark Mode' : 'Light Mode';
      });
    }
  </script>
</body>
</html>
