<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
     body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  background: url('img/signup.jpeg') no-repeat center center fixed;
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
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: #007bff;
    color: white;
    display: flex;
    justify-content: space-between;
    padding: 5px 10px;
    align-items: center;
    z-index: 1000; /* ensures it stays above everything */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
    }

    .navbar .nav-links {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .navbar .nav-links a {
      color: white;
      text-decoration: none;
    }
    .container {
      max-width: 400px;
      margin: 30px auto;
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
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button[type="submit"] {
      background-color: #007bff;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }
    button[type="submit"]:hover {
      background-color: #0056b3;
    }
    .error {
      color: red;
      margin-bottom: 10px;
      text-align: center;
    }
    input[type="password"] {
      padding-right: 35px;
    }
  </style>
</head>
<body>
<div class="container">
   <div style="height: 70px;"></div>
  <nav class="navbar sticky-top bg-primary py-1" data-bs-theme="dark">
  
    <span class="navbar-brand mb-0 h1 d-flex align-items-center ps-2">
      <img src="img/jk.png" alt="MASS Logo"
       class="me-2 img-fluid" 
       style="width: 80px; height: 40px;" />
    </span>
     <div class="nav-links">
    <button class="btn btn-sm" onclick="window.location.href='admin_login.php'" style="border-radius: 10px; padding: 8px 16px; background-color: white; color: black; border: none; cursor: pointer;">
      Login here
    </button>
  </div>
</nav>


<?php
  require_once "database.php";
  $errors = [];
  $adminExists = false;

  // Check if an admin account already exists
  $checkStmt = $conn->prepare("SELECT COUNT(*) FROM admin");
  $checkStmt->execute();
  $checkStmt->bind_result($count);
  $checkStmt->fetch();
  $checkStmt->close();

  if ($count >= 1) {
      $adminExists = true;
  }

  // Handle form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST" && !$adminExists) {
      $fullName = trim($_POST["fullName"]);
      $email = trim($_POST["email"]);
      $phone = trim($_POST["phone"]);
      $password = $_POST["password"];
      $confirmPassword = $_POST["confirmPassword"];

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $errors[] = "Invalid email format.";
      }

      if (!preg_match('/^0[67][0-9]{8}$/', $phone)) {
          $errors[] = "Phone number must be a valid Tanzanian number (e.g., 0655123456).";
      }

      if (strlen($password) < 6 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/[!@#$%^&*()]/', $password)) {
          $errors[] = "Password must be at least 6 characters long, contain letters and a special character.";
      }

      if ($password !== $confirmPassword) {
          $errors[] = "Passwords do not match.";
      }

      if (empty($errors)) {
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          $stmt = $conn->prepare("INSERT INTO admin (fullName, email, phone, password) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("ssss", $fullName, $email, $phone, $hashedPassword);
          if ($stmt->execute()) {
              header('location: admin_login.php');
              exit;
          } else {
              echo "<p class='error'>Error: " . $stmt->error . "</p>";
          }
          $stmt->close();
      } else {
          foreach ($errors as $e) {
              echo "<p class='error'>$e</p>";
          }
      }
  }

  // Display message if admin exists
  if ($adminExists) {
      echo "<p class='error'>Admin account already exists. You cannot create another one.</p>";
  }
?>

<?php if (!$adminExists): ?>
  <form method="post" action="admin_signup.php">
    <h3>Signup Form</h3>

    <label for="fullName">Full Name:</label>
    <input type="text" id="fullName" name="fullName" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="phone">Phone:</label>
    <input type="tel" id="phone" name="phone" pattern="0[67][0-9]{8}" maxlength="10" required placeholder="e.g., 0655123456">

    <label for="password">Password:</label>
    <div style="position: relative;">
      <input type="password" id="password" name="password" required>
      <span onclick="togglePassword('password')" style="position: absolute; right: 10px; top: 10px; cursor: pointer;">👁️</span>
    </div>

    <label for="confirmPassword">Confirm Password:</label>
    <div style="position: relative;">
      <input type="password" id="confirmPassword" name="confirmPassword" required>
      <span onclick="togglePassword('confirmPassword')" style="position: absolute; right: 10px; top: 10px; cursor: pointer;">👁️</span>
    </div>

    <button type="submit">Sign Up</button>
  </form>
<?php endif; ?>
</div>

<script>
  function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
  }

  document.getElementById('phone')?.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
  });
</script>
</body>
</html>
