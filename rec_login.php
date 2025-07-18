<?php
session_name("receptionist_session");
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
        // Use receptionist table
        $stmt = $conn->prepare("SELECT * FROM receptionist WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $receptionist = $res->fetch_assoc();

            // Check if blocked
            if ($receptionist['is_blocked'] == 1) {
                $errors['email'] = "Your account has been blocked. Please contact admin.";
            } elseif (password_verify($password, $receptionist['password'])) {
                $_SESSION['receptionist'] = $receptionist;
                $_SESSION['receptionist_id'] = $receptionist['id'];
                $_SESSION['email'] = $receptionist['email'];

                header("Location: rec_dashb.php");
                exit();
            } else {
                $errors['password'] = "Incorrect password.";
            }
        } else {
            $errors['email'] = "No receptionist account found with that email.";
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
    <title>Reception Login - MASS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* ... same CSS as before ... */
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
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            align-items: center;
        }
        .navbar .logo { font-size: 20px; font-weight: bold; }
        .navbar .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }
        .form-container {
            max-width: 400px;
            background: white;
            padding: 30px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .login-form h2 { text-align: center; margin-bottom: 25px; color: #333; }
        label { display: block; margin: 12px 0 6px; font-weight: 500; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;
        }
        .error {
            color: red; font-size: 14px; margin-top: 5px; margin-bottom: -5px;
        }
        button {
            margin-top: 20px; width: 100%; padding: 12px;
            background-color: #007bff; color: white; border: none;
            font-size: 16px; border-radius: 5px; cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
        .forgot-password { margin-top: 15px; text-align: right; }
        .forgot-password a {
            color: #007bff; text-decoration: none;
        }
    </style>
</head>
<body>
     <nav class="navbar  bg-primary py-1" data-bs-theme="dark">
  
    <span class="navbar-brand mb-0 h1 d-flex align-items-center ps-2">
      <img src="img/jk.png" alt="MASS Logo"
       class="me-2 img-fluid" 
       style="width: 80px; height: 40px;" />
    </span>
     

</nav>

    <div class="form-container">
        <form method="POST" class="login-form">
            <h2>Reception Login</h2>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="Enter email"required>
            <p class="error"><?= $errors['email'] ?></p>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
            <p class="error"><?= $errors['password'] ?></p>

            <button type="submit">Login</button>

            <div class="forgot-password">
                <a href="forgot-rec-password.php">Forgot Password?</a>
            </div>
            <div class="signup-prompt d-grid gap-2 col-6 mx-auto m-2">
            <p>Don't have an account?</p>
           <button class="btn btn-lg m-1"style="background-color: #28a745;color:#FFFFFF;"onclick="window.location.href='rec_signup.php'">Sign up</button>
        </div>
        </form>
    </div>
</body>
</html>
