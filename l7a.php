<?php
session_start();
include 'database.php';

// If POST, handle the OTP logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Generate OTP
    function generateRandomOTP()
    {
        return strval(rand(100000, 999999)); // simpler + shorter
    }

    $hiddenOTP = generateRandomOTP();

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $_SESSION['email'] = $email;

        // Update OTP and timestamp
        $updateSql = "UPDATE users SET otp = ?, otp_generated_time = NOW() WHERE email = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "ss", $hiddenOTP, $email);

        if (mysqli_stmt_execute($updateStmt)) {
            header("Location: l12.php?email=" . urlencode($email));
            exit();
        } else {
            $error = "Could not update OTP.";
        }
    } else {
        $error = "Email not found in the database.";
    }

    mysqli_close($conn);
}
?>

<!-- HTML Form for entering email -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password (Patient)</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        input[type="email"], button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="box">
    <h3>Forgot Password (Patient)</h3>
    <form method="post">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        <button type="submit">Send OTP</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</div>
</body>
</html>
