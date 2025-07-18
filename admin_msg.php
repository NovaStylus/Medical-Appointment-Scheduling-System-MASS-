<?php
require 'database.php'; // Make sure this connects correctly

// Mark all messages as read
$conn->query("UPDATE contact_messages SET is_read = 1 WHERE is_read = 0");
?>

<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'database.php';

// Fetch all messages
$sql = "SELECT * FROM contact_messages ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - View Messages</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }
      .profile-circle {
    width: 35px;
    height: 35px;
    background-color: black;
    color: #007bff;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    cursor: pointer;
    text-decoration: none; /* since now it is an <a> */
    margin-left: auto; /* push to the far right */
    border: 2px solid #007bff;
    user-select: none;
    transition: background-color 0.3s, color 0.3s;
}
.profile-circle:hover {
    background-color: #007bff;
    color: white;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 15px; /* spacing between links */
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
    padding: 5px 15px;
    align-items: center;
    z-index: 1000; /* ensures it stays above everything */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}


        .navbar .logo {
            font-size: 22px;
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

        h2 {
            text-align: center;
            margin: 30px 0 10px;
            color: #333;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 800px;
            margin: 0 auto;
            gap: 20px;
            padding: 20px;
        }

        .card-link {
            flex: 1 1 calc(50% - 20px); /* 2 cards per row */
            text-decoration: none;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.2s;
            border: 1px solid #e0e0e0;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        .card strong {
            display: block;
            margin-top: 8px;
            font-size: 18px;
            color: #333;
        }

        .card-logo {
            font-size: 36px;
        }

        @media (max-width: 600px) {
            .card-link {
                flex: 1 1 100%;
            }
        }
        .nav-links a:first-child {
    font-size: 20px;
    margin-right: 20px;
}
/* Dark mode styling */
body.dark-mode {
  background-color: #121212;
  color: #f0f0f0;
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

/* Fix heading color in dark mode */
body.dark-mode h2 {
  color: #f0f0f0;
}

    #modeLabel {
      color: white;
      font-size: 14px;
      margin-left: 10px;
    }

    .container {
      max-width: 90%;
      margin: 30px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      overflow-x: auto;
    }

    h2 {
      text-align: center;
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th, table td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    table th {
      background-color: #007bff;
      color: white;
    }

    /* Dark Mode */
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }


    body.dark-mode .container {
      background-color: #1e1e1e;
      color: #f0f0f0;
    }

    body.dark-mode table th {
      background-color: #333;
    }

    body.dark-mode table td {
      border-color: #555;
    }
    table {
    width: 100%;
    border-collapse: collapse;
}

td, th {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;

    /* Important: wrap and break long content */
    word-wrap: break-word;
    word-break: break-word;
    white-space: pre-wrap;
    max-width: 300px; /* or adjust to your preferred width */
}

  </style>
   
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-body-primary">
  <div class="container-fluid ">
    <div class="me-5">
     <img src="img/jk.png" alt="Bootstrap" width="65" height="34">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    </div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active text-white" aria-current="page" href="admin.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>
  <div class="container" style="margin-top: 80px;">
    <h2>Contact Messages</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Date Sent</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['subject']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
          <td><?= isset($row['submitted_at']) ? date('F j, Y, g:i a', strtotime($row['submitted_at'])) : 'N/A' ?>
</td>

        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>No messages found.</p>
    <?php endif; ?>
  </div>

  
</body>
</html>
