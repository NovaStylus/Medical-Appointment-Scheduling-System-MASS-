<?php
session_start();
require_once "database.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = trim($_POST["question"]);
    $answer = trim($_POST["answer"]);

    if (!empty($question) && !empty($answer)) {
        $stmt = $conn->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)");
        $stmt->bind_param("ss", $question, $answer);
        $stmt->execute();
        $stmt->close();
        $success = "✅ FAQ added successfully!";
    } else {
        $error = "⚠️ Please fill in both question and answer.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add FAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f0f2f5, #dfe9f3);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
          
    .navbar {
      background-color: #007bff;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 1px 5px;
      align-items: center;
        background-color: #007bff;

  position: fixed;  /* <--- add this */
  top: 0;           /* <--- add this */
  left: 0;
  right: 0;
  z-index: 1000;    /* <--- add this */
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

        .container {
            background: white;
            padding: 30px 40px;
            max-width: 600px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            color: #007bff;
            font-size: 24px;
            margin-bottom: 25px;
            text-align: center;
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #333;
        }

        textarea, input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 5px;
            transition: border-color 0.3s ease;
            resize: vertical;
        }

        textarea:focus, input:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            margin-top: 20px;
            background: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .msg, .err {
            margin-top: 15px;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }

        .msg {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .err {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    
  <nav class="navbar">
        <div class="logo">+ MASS</div>
        <div class="nav-links">
               <button onclick="window.location.href='admin.php'" style="border-radius: 10px; padding: 8px 16px; background-color: white; color: black; border: none; cursor: pointer;">
  Home
   </button>
          
        </div>
    </nav>
    <div class="container">
        <h2>Post a New FAQ</h2>
        <form method="post">
            <label for="question">Question</label>
            <textarea name="question" id="question" rows="3" placeholder="Type the question..." required></textarea>

            <label for="answer">Answer</label>
            <textarea name="answer" id="answer" rows="5" placeholder="Provide the answer..." required></textarea>

            <button type="submit">➕ Add FAQ</button>

            <?php if (isset($success)) echo "<div class='msg'>$success</div>"; ?>
            <?php if (isset($error)) echo "<div class='err'>$error</div>"; ?>
        </form>
    </div>
</body>
</html>
