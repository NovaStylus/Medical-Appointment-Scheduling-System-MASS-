<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_name("admin_session");
session_start();
require_once "database.php";

$userId = $_SESSION['admin_id'] ?? null;
if (!$userId) {
    die("Please login first.");
}


// Mark all admin notifications as read
$conn->query("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");

// Fetch all admin notifications
$result = $conn->query("SELECT * FROM admin_notifications ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  
    <style>
              body { font-family: sans-serif; background: #f0f0f0; padding: 20px; 
         margin: 0;
            padding: 0;}
        
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
    padding: 5px 10px;
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
        h3 {
            margin-bottom: 20px;
            color: #444;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            font-weight: 700;
        }
        .notification {
            background: #fff;
            border-left: 5px solid #28a745;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            padding: 15px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .notification:hover {
            background: #e9ffe9;
        }
        .message {
            font-weight: 600;
            font-size: 16px;
        }
        .date {
            color: #888;
            font-size: 13px;
            margin-top: 5px;
            font-style: italic;
        }
        .empty {
            text-align: center;
            color: #666;
            margin-top: 50px;
            font-style: italic;
        }
    </style>
</head>
<body>
 <div style="height: 70px;"></div>

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
          <a class="nav-link active text-white" aria-current="page" href="admin.php">HOME</a>
        </li>
        
        
      
      </ul>
     
    </div>
  </div>
</nav>
<h3>Admin Notifications</h3>

<?php
if ($result->num_rows === 0) {
    echo '<div class="empty">No notifications yet.</div>';
} else {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="notification">';
        echo '<div class="message">' . htmlspecialchars($row['message']) . '</div>';
        echo '<div class="date">Received on ' . date('F j, Y, g:i a', strtotime($row['created_at'])) . '</div>';
        echo '</div>';
    }
}
$conn->close();
?>

</body>
</html>
