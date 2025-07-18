<?php
require_once "database.php";
session_name("receptionist_session");
session_start();

if (!isset($_SESSION['receptionist_id'])) {
    header("Location: rec_login.php");
    exit();
}
$userId = $_SESSION['receptionist_id'];


$fullName = $email = $phone = "";
$message = "";

// Fetch existing user data
$stmt = $conn->prepare("SELECT fullName, email, phone FROM receptionist WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fullName = $user['fullName'];
    $email = $user['email'];
    $phone = $user['phone'];
}
$stmt->close();

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["fullName"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);

    $stmt = $conn->prepare("UPDATE receptionist SET fullName=?, email=?, phone=? WHERE id=?");
    $stmt->bind_param("sssi", $fullName, $email, $phone, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $message = "✅ Details updated successfully.";
        } else {
            $message = "ℹ️ No changes made (data may be the same).";
        }
    } else {
        $message = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
   
    <style>
  body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  background: url('img/doctor.jpg') no-repeat center center fixed;
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



        .container {
            max-width: 450px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
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
            font-size: 20px;
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

        input[type="text"], input[type="email"], input[type="tel"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f1f1f1;
            font-size: 15px;
        }

        .message strong {
            display: block;
        }

        @media (max-width: 500px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }

        .settings-options {
    max-width: 450px;       /* same width as container */
    margin: 20px auto 50px; /* space above and below, centered */
    display: flex;
    justify-content: space-between;
    gap: 15px;
}

.settings-options a {
    flex: 1;
    text-align: center;
    padding: 12px 0;
    background-color: #007bff;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    user-select: none;
}

.settings-options a:hover {
    background-color: #0056b3;
}

@media (max-width: 500px) {
    .settings-options {
        flex-direction: column;
    }
    .settings-options a {
        flex: unset;
        margin-bottom: 10px;
    }
}
        /* Dark mode styling */
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

/* Toggle switch */
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
        <h2>Update Your Details</h2>

        <?php if ($message): ?>
            <div class="message"><strong><?= htmlspecialchars($message) ?></strong></div>
        <?php endif; ?>

        <form method="post" action="update_rec.php">
            <label>Full Name</label>
            <input type="text" name="fullName" value="<?= htmlspecialchars($fullName) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label>Phone</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>" required>

            <button type="submit">Update Details</button>
        </form>
        <div class="settings-options">
            <a href="forgot-rec-password.php">Change Password</a>
            <a href="delete_rec.php">Delete Account</a>
        </div>
    </div>
     
</html>
