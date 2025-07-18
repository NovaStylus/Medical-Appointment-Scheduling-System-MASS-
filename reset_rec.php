<?php
session_start();
include 'database.php';

$error = "";
$success = "";

if (!isset($_SESSION['otp'])) {
    echo "Unauthorized access. Please verify OTP first.";
    exit();
}

$otp = $_SESSION['otp'];

// Get the email using the OTP
$sql = "SELECT email FROM receptionist WHERE otp = ? AND TIMESTAMPDIFF(SECOND, otp_generated_time, NOW()) <= 300";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $otp);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "OTP expired or invalid.";
    exit();
}

$email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $newPassword = $_POST['new_password'];
    $retypePassword = $_POST['retype_password'];

    if ($newPassword !== $retypePassword) {
        $error = "❌ Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $newPassword)) {
        $error = "❌ Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE receptionist SET password = ?, otp = NULL, otp_generated_time = NULL WHERE email = ?");
        $update->bind_param("ss", $hashedPassword, $email);

        if ($update->execute()) {
            unset($_SESSION['otp']);
            $success = "✅ Password has been successfully reset. You can now <a href='rec_login.php'>login</a>.";
        } else {
            $error = "❌ Failed to update password. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 450px;
            margin: 60px auto;
            background: #fff;
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

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
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

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 5px;
            font-size: 15px;
        }

        .error {
            background-color: #ffe0e0;
            color: #b30000;
        }

        .success {
            background-color: #e0f5e9;
            color: #267d44;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>

    <?php if (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= $success ?></div>
    <?php else: ?>
        <form method="post">
            <label>New Password:</label>
            <input type="password" name="new_password" required
                   pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}"
                   title="At least 8 characters, including uppercase, lowercase, number, and special character">

            <label>Retype New Password:</label>
            <input type="password" name="retype_password" required>

            <input type="submit" value="Reset Password">
        </form>
    <?php endif; ?>
</div>
</body>
</html>
