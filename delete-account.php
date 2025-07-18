<?php
require_once "database.php";
session_name("user_session");
session_start();

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        // Delete user from database
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            $stmt->close();
            // Destroy session and redirect to login page with success message
            session_destroy();
            header("Location: signup.php?message=Your account has been deleted successfully.");
            exit();
        } else {
            $message = "❌ Sorry, we couldn't delete your account. Please try again later.";
        }
    } else {
        // User cancelled deletion, redirect to update details page or dashboard
        header("Location: update.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Delete Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 450px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #c0392b;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #333;
        }
        button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 10px;
            color: white;
        }
        .btn-delete {
            background-color: #c0392b;
        }
        .btn-delete:hover {
            background-color: #922b21;
        }
        .btn-cancel {
            background-color: #7f8c8d;
        }
        .btn-cancel:hover {
            background-color: #636e72;
        }
        .message {
            margin-bottom: 20px;
            font-size: 15px;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delete Account</h2>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <p>Are you sure you want to delete your account? This action <strong>cannot</strong> be undone.</p>

        <form method="post" action="delete-account.php">
            <button type="submit" name="confirm_delete" value="yes" class="btn-delete">Yes, Delete My Account</button>
            <button type="submit" name="confirm_delete" value="no" class="btn-cancel">Cancel</button>
        </form>
    </div>
</body>
</html>
