<?php include 'google_translate.php'; ?>
<?php
require_once "database.php";
session_name("user_session");
session_start();

if (!isset($_SESSION['user']['id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Normalize ID variable
$userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'];


$fullName = $email = $phone = "";
$message = "";

// Fetch existing user data
$stmt = $conn->prepare("SELECT fullName, email, phone FROM users WHERE id = ?");
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

    // Check if email is taken by other user
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $checkStmt->bind_param("si", $email, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Email already taken by another user
        $message = "⚠️ This email address is already in use by another account. Please use a different email.";
    } else {
        // Proceed with update
        $stmt = $conn->prepare("UPDATE users SET fullName=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $fullName, $email, $phone, $userId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $currentDateTime = date('Y-m-d H:i:s'); // current timestamp
                $successMessage = "Your details have been updated successfully on $currentDateTime.";

                // Save notification to database
                $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
                $notifStmt->bind_param("is", $userId, $successMessage);
                $notifStmt->execute();
                $notifStmt->close();

                $message = "✅ Details updated successfully.";
            } else {
                $message = "ℹ️ No changes made (data may be the same).";
            }
        } else {
            // For any other DB error, fallback to generic message
            $message = "❌ An unexpected error occurred while updating your details. Please try again.";
        }

        $stmt->close();
    }

    $checkStmt->close();
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
            margin-top: 80px;
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
            margin-top: 2px;
            font-weight: 500;
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
    padding: 15px 10px;
    align-items: center;
    z-index: 1000; /* stays above other content */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
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
            margin-top: 7px;
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


    </style>
</head>
<body>
         <nav class="navbar navbar-expand-lg bg-body-primary py-2 " >
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
          <a class="nav-link active text-white" aria-current="page" href="index.php">HOME</a>
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

        <form method="post" action="update.php">
            <label>Full Name</label>
            <input type="text" name="fullName" value="<?= htmlspecialchars($fullName) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label>Phone</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>" required>

            <button type="submit">Update Details</button>
        </form>
        <div class="settings-options">
            <a href="forgot-password.php">Change Password</a>
            <a href="delete_account.php">Delete Account</a>
        </div>
    </div>
       
</body>
</html>
