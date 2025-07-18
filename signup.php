<?php include 'google_translate.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
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
      padding: 5px 30px;
      align-items: center;
      z-index: 1000;
      /* ensures it stays above everything */
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
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
/* Signup form */
    .container {
      max-width: 450px;
      margin: 25px auto;
      padding: 25px;
      zoom:78%;
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
      margin-bottom: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    button[type="submit"] {
      background-color: #007bff;
      color: #fff;
      padding: 15px 20px;
       margin-top: 5px;
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
    }

    .error-message {
      color: red;
      font-size: 0.85em;
      margin-top: -8px;
      margin-bottom: 10px;
    }

    input[type="password"] {
      padding-right: 35px;
    }
  </style>
</head>

<body>
  <div style="height: 70px;"></div>
  <nav class="navbar sticky-top  bg-primary py-0 " data-bs-theme="dark">

    <span class="navbar-brand mb-0 h1 d-flex align-items-center ps-2">
      <img src="img/jk.png" alt="MASS Logo" class="me-2 img-fluid" style="width: 80px; height: 40px;" />
    </span>


  </nav>
  <div class="container">
    <?php
    require_once "database.php";
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $fullName = trim($_POST["fullName"]);
      if (!preg_match('/^([A-Za-z\'\-]{2,}\s+){1,}[A-Za-z\'\-]{2,}$/', $fullName)) {
        $errors[] = "Please enter a valid full name (e.g., Joe Hart).";
      }

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
        $stmt = $conn->prepare("INSERT INTO users (fullName, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullName, $email, $phone, $hashedPassword);

        if ($stmt->execute()) {
          $user_id = $stmt->insert_id;

          $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
          $message = "Welcome to MASS! Your account has been successfully created.";
          $notif_stmt->bind_param("is", $user_id, $message);
          $notif_stmt->execute();
          $notif_stmt->close();

          $admin_message = "New user signed up: $fullName ($email)";
          $admin_stmt = $conn->prepare("INSERT INTO admin_notifications (message) VALUES (?)");
          $admin_stmt->bind_param("s", $admin_message);
          $admin_stmt->execute();
          $admin_stmt->close();

          header('location: login.php');
          exit();
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
    ?>

    <form method="post" action="signup.php" id="signupForm">
      <h3>Signup</h3>

      <label for="fullName">Full Name:</label>
      <input type="text" id="fullName" name="fullName" placeholder="Enter full name" required>
      <div class="error-message" id="fullNameError"></div>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" placeholder="Enter email" required>
      <div class="error-message" id="emailError"></div>

      <label for="phone">Phone:</label>
      <input type="tel" id="phone" name="phone" pattern="0[67][0-9]{8}" maxlength="10" required
        placeholder="e.g., 0655123456">
      <div class="error-message" id="phoneError"></div>

      <label for="password">Password:</label>
      <div style="position: relative;">
        <input type="password" id="password" name="password" placeholder="Enter password" required>
        <span onclick="togglePassword('password')"
          style="position: absolute; right: 10px; top: 10px; cursor: pointer;">👁️</span>
      </div>
      <div class="error-message" id="passwordError"></div>

      <label for="confirmPassword">Confirm Password:</label>
      <div style="position: relative;">
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
        <span onclick="togglePassword('confirmPassword')"
          style="position: absolute; right: 10px; top: 10px; cursor: pointer;">👁️</span>
      </div>
      <div class="error-message" id="confirmPasswordError"></div>

      <button type="submit">Sign Up</button>
      <div style="text-align: center; margin-top: 20px;">
        <p>OR</p>
        <a href="google-login.php">
          <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png"
            alt="Sign in with Google" style="cursor: pointer;">
        </a>
      </div>

    </form>

  </div>

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === "password" ? "text" : "password";
    }

    document.getElementById('phone').addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    document.getElementById('signupForm').addEventListener('submit', function (e) {
      let valid = true;

      // Clear all error messages
      ['fullName', 'email', 'phone', 'password', 'confirmPassword'].forEach(field => {
        document.getElementById(field + 'Error').textContent = '';
      });

      const fullName = document.getElementById('fullName').value.trim();
      const email = document.getElementById('email').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      const nameRegex = /^([A-Za-z'\-]{2,}\s+){1,}[A-Za-z'\-]{2,}$/;
      const emailRegex = /^[a-zA-Z0-9._%+-]{4,}@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      if (!nameRegex.test(fullName)) {
        document.getElementById('fullNameError').textContent = "Please enter your full name (e.g., Joe Hart).";
        valid = false;
      }

      if (!emailRegex.test(email) || /^(aa+|abc|test|example)@/i.test(email)) {
        document.getElementById('emailError').textContent = "Enter a valid and realistic email address.";
        valid = false;
      }

      if (!/^0[67][0-9]{8}$/.test(phone)) {
        document.getElementById('phoneError').textContent = "Use a valid Tanzanian number e.g., 0655123456.";
        valid = false;
      }

      if (password.length < 6 || !/[a-zA-Z]/.test(password) || !/[!@#$%^&*()]/.test(password)) {
        document.getElementById('passwordError').textContent = "Min 6 characters with letters and a special character.";
        valid = false;
      }

      if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = "Passwords do not match.";
        valid = false;
      }

      if (!valid) e.preventDefault();
    });
  </script>
</body>

</html>