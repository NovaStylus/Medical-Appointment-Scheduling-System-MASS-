<?php
session_name("admin_session");
session_start();
require_once "database.php";

// Get role and table dynamically
$role = $_GET['role'] ?? 'user';
$table = ($role === 'receptionist') ? 'receptionist' : 'users';

// Delete user/receptionist
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Block user/receptionist
if (isset($_GET['block'])) {
    $id = intval($_GET['block']);
    $stmt = $conn->prepare("UPDATE $table SET is_blocked = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Unblock user/receptionist
if (isset($_GET['unblock'])) {
    $id = intval($_GET['unblock']);
    $stmt = $conn->prepare("UPDATE $table SET is_blocked = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Fetch users
$userResult = $conn->query("SELECT id, fullName, email, phone, is_blocked, 'User' as role FROM users");

// Fetch receptionists
$receptionistResult = $conn->query("SELECT id, fullName, email, phone, is_blocked, 'Receptionist' as role FROM receptionist");

// Merge both results
$allUsers = [];
if ($userResult) {
    while ($row = $userResult->fetch_assoc()) {
        $allUsers[] = $row;
    }
}
if ($receptionistResult) {
    while ($row = $receptionistResult->fetch_assoc()) {
        $allUsers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Users</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  
  <style>
    body { font-family: sans-serif; background: #f0f0f0; margin: 0; padding: 0; }
    .navbar {
        background-color: #007bff;
        color: white;
        display: flex;
        justify-content: space-between;
        padding: 10px 30px;
        align-items: center;
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
        
        margin:20px 20px;
    }
    table {
        width: 100%;
        background-color: #fff;
        border-collapse: collapse;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }
    th {
        background-color: #007bff;
        color: white;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
    tr.receptionist {
        background-color: #e0f7fa;
    }
    .action-btn {
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 5px;
    }
    .delete-btn { background-color: red; color: white; }
    .delete-btn:hover { background-color: darkred; }
    .block-btn { background-color: orange; color: white; }
    .block-btn:hover { background-color: darkorange; }
    .unblock-btn { background-color: green; color: white; }
    .unblock-btn:hover { background-color: darkgreen; }
  </style>
  
</head>
<body>
   <nav class="navbar navbar-expand-lg bg-body-primary">
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

  <h2>Registered Users & Receptionists</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Role</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($allUsers as $row): ?>
        <tr class="<?= $row['role'] === 'Receptionist' ? 'receptionist' : '' ?>">
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><strong><?= $row['role'] ?></strong></td>
          <td><?= htmlspecialchars($row['fullName']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= $row['is_blocked'] ? 'Blocked' : 'Active' ?></td>
          <td>
            <a href="?delete=<?= $row['id'] ?>&role=<?= strtolower($row['role']) ?>" onclick="return confirm('Are you sure you want to delete this <?= $row['role'] ?>?');">
              <button class="action-btn delete-btn">Delete</button>
            </a>
            <?php if ($row['is_blocked']): ?>
              <a href="?unblock=<?= $row['id'] ?>&role=<?= strtolower($row['role']) ?>">
                <button class="action-btn unblock-btn">Unblock</button>
              </a>
            <?php else: ?>
              <a href="?block=<?= $row['id'] ?>&role=<?= strtolower($row['role']) ?>" onclick="return confirm('Are you sure you want to block this <?= $row['role'] ?>?');">
                <button class="action-btn block-btn">Block</button>
              </a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>
