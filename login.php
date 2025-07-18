
<?php
session_name("user_session");
session_start();
$errors = ['email' => '', 'password' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'database.php';

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email.";
    }

    if (empty($errors['email'])) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

     if ($res->num_rows === 1) {
    $user = $res->fetch_assoc();

    if ($user['is_blocked']) {
        $errors['email'] = "Your account has been blocked. Please contact the administrator.";
    } elseif (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['patient_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        // Reminder notification logic
require_once 'database.php';

$user_id = $_SESSION['user_id'];

$now = new DateTime();
$in3Hours = clone $now;
$in3Hours->add(new DateInterval('PT15H'));

$nowStr = $now->format('Y-m-d H:i:s');
$in3HoursStr = $in3Hours->format('Y-m-d H:i:s');

// Get confirmed appointments in next 3 hours, with no reminder yet
$sql = "
    SELECT a.id, a.appointmentDate, a.appointmentTime, d.name AS department
    FROM appointments a
    JOIN departments d ON a.department_id = d.id
    WHERE a.user_id = ?
      AND a.status = 'Confirmed'
      AND CONCAT(a.appointmentDate, ' ', a.appointmentTime) BETWEEN ? AND ?
      AND NOT EXISTS (
        SELECT 1 FROM notifications 
        WHERE user_id = a.user_id 
          AND message LIKE CONCAT('%', a.appointmentDate, '%', a.appointmentTime, '%')
          AND message LIKE '%reminder%'
      )
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $nowStr, $in3HoursStr);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $appointmentDate = $row['appointmentDate'];
    $appointmentTime = $row['appointmentTime'];
    $department = $row['department'];

    $message = "⏰ Reminder: Your appointment for $department is at $appointmentTime on $appointmentDate. Please arrive on time.";

    $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");
    $notif_stmt->bind_param("is", $user_id, $message);
    $notif_stmt->execute();
    $notif_stmt->close();
}

$stmt->close();


        if ($email === "admin@example.com") {
            header("Location: admindashboard.php");
        } elseif ($email === "reception@example.com") {
            header("Location: receptionistdashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $errors['password'] = "Incorrect password.";
    }
} else {
    $errors['email'] = "No account found with that email.";
}

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MASS Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --main-blue: #007bff;
            --main-dark: #0056b3;
            --background: #f0f4f8;
        }

       body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  background: url('img/3.jpg') no-repeat center center fixed;
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
            background: var(--main-blue);
            color: #fff;
            display: flex;
            justify-content: space-between;
            padding: 2px 30px;
            align-items: center;
        }

        .navbar .logo {
            font-size: 22px;
            font-weight: 700;
        }

        .navbar .nav-links button {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 25px;
            background: #fff;
            color: var(--main-blue);
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .navbar .nav-links button:hover {
            background: #e2e8f0;
        }

      

        .form-container {
            max-width: 380px;
            background: #fff;
            padding: 35px;
            margin: 50px auto;
            zoom:95%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            border-color: var(--main-blue);
            outline: none;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .highlight-error {
            border-color: red;
        }

        button[type="submit"] {
            width: 100%;
            padding: 13px;
            font-size: 16px;
            background: var(--main-blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button[type="submit"]:hover {
            background: var(--main-dark);
        }

        .forgot-password {
            text-align: right;
            margin-top: 12px;
            margin-bottom:10px;
        }

        .forgot-password a {
            font-size: 14px;
            color: var(--main-blue);
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .form-container {
                margin: 30px 15px;
                padding: 25px;
            }
        }
       
    </style>
</head>
<body>

   <nav class="navbar sticky-top bg-primary py-1" data-bs-theme="dark">
  
    <span class="navbar-brand mb-0 h1 d-flex align-items-center ps-2">
      <img src="img/jk.png" alt="MASS Logo"
       class="me-2 img-fluid" 
       style="width: 80px; height: 40px;" />
    </span>     
</nav>

    <div class="form-container">
        <form method="POST" action="login.php" class="login-form">
            <h2>Login</h2>

            <label>Email</label>
            <input 
                type="email" 
                name="email" 
                placeholder="Enter email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                class="<?= $errors['email'] ? 'highlight-error' : '' ?>" 
                required
            >
            <p class="error"><?= $errors['email'] ?></p>

            <label>Password</label>
            <input 
                type="password" 
                name="password" 
                 placeholder="Enter password"
                class="<?= $errors['password'] ? 'highlight-error' : '' ?>" 
                required
            >
            <p class="error"><?= $errors['password'] ?></p>

            <button type="submit">Login</button>

            <div class="forgot-password">
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
             <div class="signup-prompt d-grid gap-1 col-6 mx-auto">
            <p style="white-space:pre;">Don't have an account?</p>
           <button class="btn btn-lg btn-block" style="background-color: #28a745;color:#FFFFFF;" onclick="window.location.href='signup.php'">Sign up</button>
        </div>
        </form>
    </div>

</body>
</html>
