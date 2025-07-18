<?php
session_start();
require_once "database.php";

$initial = '?';
if (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}

$success = "";
$error = "";

// Handle form submission early before any HTML
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["fullName"]);
    $phone = trim($_POST["phone"]);
    $department = $_POST["department"];

    $payments = [
        'General_medicine' => 10000,
        'Cardiology' => 20000,
        'Pediatrics' => 20000
    ];
    $payment = $payments[$department] ?? 0;

    if ($name && $phone && $department) {
        $stmt = $conn->prepare("INSERT INTO walkin_patients (fullName, phone, department, payment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $name, $phone, $department, $payment);

        if ($stmt->execute()) {
            $stmt->close();
            // Redirect before output
            header("Location: manage_walkins.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
        }
    } else {
        $error = "Please fill all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Walk-in Patient Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  background: url('img/doctorc.jpg') no-repeat center center fixed;
  background-size: cover;
  position: relative;
}

/* Use a container to hold the blur instead of fixed pseudo-elements */
body::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: inherit;
  filter: blur(6px);
  z-index: -2;
}

body::after {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.4); /* Overlay for contrast */
  z-index: -1;
}
    
    .navbar {
      background-color: #007bff;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 5px 10px;
      align-items: center;
    }

    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
    }

    .nav-links a:hover {
      text-decoration: underline;
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

    h2 {
      text-align: center;
      margin: 30px 0 10px;
      color: #333;
    }

    .dashboard-cards {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      max-width: 1000px;
      margin: 0 auto;
      gap: 20px;
      padding: 20px;
    }

    .card-link {
      flex: 1 1 calc(33% - 20px);
      text-decoration: none;
    }

    .card {
      background-color: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      text-align: center;
      transition: transform 0.2s;
      border: 1px solid #e0e0e0;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }

    .card strong {
      display: block;
      margin-top: 8px;
      font-size: 18px;
      color: #333;
    }

    .card-logo {
      font-size: 36px;
    }

    @media (max-width: 768px) {
      .card-link {
        flex: 1 1 100%;
      }
    }

    /* Dark mode support */
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }

    body.dark-mode .navbar {
      background-color: #1e1e1e;
    }

    body.dark-mode .card {
      background-color: #1f1f1f;
      border: 1px solid #444;
    }

    body.dark-mode .card strong,
    body.dark-mode h2 {
      color: #f0f0f0;
    }

    body.dark-mode .nav-links a,
    body.dark-mode .profile-circle,
    body.dark-mode #modeLabel {
      color: #f0f0f0;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
      margin-left: 15px;
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

    .container {
      max-width: 400px;
      margin: 50px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    h3 {
      text-align: center;
      color: #333;
    }
    label {
      display: block;
      margin-bottom: 5px;
      color: #333;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    input[readonly] {
      background-color: #e9ecef;
    }
    button {
      background-color: #007bff;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }
    button:hover {
      background-color: #0056b3;
    }
    .success {
      color: green;
      text-align: center;
      margin-bottom: 10px;
    }
    .error {
      color: red;
      margin-bottom: 10px;
    }
  </style>
  <script>
    function updatePayment() {
      const department = document.getElementById("department").value;
      const paymentField = document.getElementById("payment");
      let amount = 0;

      switch (department) {
        case 'General_medicine': amount = 10000; break;
        case 'Cardiology': amount = 20000; break;
        case 'Pediatrics': amount = 20000; break;
      }

      paymentField.value = amount.toLocaleString('en-TZ') + ' TZS';
    }
  </script>
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
          <a class="nav-link active text-white" aria-current="page" href="rec_dashb.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>

<div class="container">
  <form method="post" action="">
    <h3>Register Walk-in Patient</h3>

    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <label for="fullName">Full Name:</label>
    <input type="text" id="fullName" name="fullName" required>

    <label for="phone">Phone:</label>
    <input type="tel" id="phone" name="phone" required>

    <label for="department">Department:</label>
    <select id="department" name="department" onchange="updatePayment()" required>
      <option value="">--Select--</option>
      <option value="General_medicine">General Medicine</option>
      <option value="Cardiology">Cardiology</option>
      <option value="Pediatrics">Pediatrics</option>
    </select>

    <label for="payment">Payment Amount:</label>
    <input type="text" id="payment" readonly placeholder="Select department to see amount">

    <button type="submit">Register</button>
  </form>
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

toggle.addEventListener('change', function () {
  document.body.classList.toggle('dark-mode');
  const mode = document.body.classList.contains('dark-mode');
  localStorage.setItem('darkMode', mode);
  label.textContent = mode ? 'Dark Mode' : 'Light Mode';
});
</script>

<script>
function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
    }
}
</script>
</body>
</html>
