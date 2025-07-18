<?php
require_once 'database.php';

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    $slot_check = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE department_id = ?");
    $slot_check->bind_param("i", $id);
    $slot_check->execute();
    $slot_result = $slot_check->get_result()->fetch_assoc();

    if ($slot_result['count'] == 0) {
        $conn->query("DELETE FROM department_slots WHERE id = $id");
    } else {
        echo "<script>alert('Cannot delete a slot that already has a booking.');</script>";
    }
}

$slots = $conn->query("
    SELECT department_slots.id, departments.name AS department, slot_date, slot_time
    FROM department_slots
    JOIN departments ON departments.id = department_slots.department_id
    ORDER BY departments.name ASC, slot_date ASC, slot_time ASC
");

$grouped_slots = [];
while ($slot = $slots->fetch_assoc()) {
    $dept = $slot['department'];
    $grouped_slots[$dept][] = $slot;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Appointment Slots</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  
    <style>
        
        body { font-family: sans-serif; background: #f0f0f0; padding: 20px; 
         margin: 0;
            padding: 0;}
            body.dark-mode {
    background-color: #121212;
    color: #f0f0f0;
}

body.dark-mode .container {
    background-color: #1e1e1e;
    color: #f0f0f0;
}

body.dark-mode .navbar {
    background-color: #1f2937;
}

body.dark-mode .navbar .nav-links a {
    color: #e0e0e0;
}

body.dark-mode table {
    background-color: #1e1e1e;
    color: #f0f0f0;
    border-color: #333;
}

body.dark-mode th {
    background-color: #2c2c2c;
}

body.dark-mode .date-header {
    background-color: #2a2a2a;
    color: #ddd;
}

body.dark-mode .back-btn {
    background-color: #333;
    color: #fff;
}

body.dark-mode .back-btn:hover {
    background-color: #111;
}

body.dark-mode a.danger {
    color: #f87171; /* soft red */
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
    padding: 15px 30px;
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

        .container { background: white; padding: 20px; border-radius: 10px; max-width: 1000px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #e0e0e0; }
        h3 { margin-top: 40px; background: #007bff; color: white; padding: 10px; border-radius: 5px; }
        .danger { color: red; text-decoration: none; }
        .back-btn { background: #555; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .back-btn:hover { background: #333; }
        .date-header { font-weight: bold; background-color: #f5f5f5; text-align: left; padding: 10px; }
      
    </style>
</head>
<body>
    <div style="height: 70px;"></div>

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
<div class="container my-4">
    <h2>Manage Existing Appointment Slots</h2>
    <a href="dep.php"><button class="back-btn">← Back to Department Settings</button></a>
    <br><br>

    <?php foreach ($grouped_slots as $department => $slot_list): ?>
        <h3><?= htmlspecialchars($department) ?> Department</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
            <?php
                $last_date = '';
                foreach ($slot_list as $slot):
                    $is_new_date = $slot['slot_date'] !== $last_date;
                    if ($is_new_date):
                        echo '<tr><td colspan="3" class="date-header">Date: <strong>' . $slot['slot_date'] . '</strong></td></tr>';
                        $last_date = $slot['slot_date'];
                    endif;
            ?>
            <tr>
                <td><?= $slot['id'] ?></td>
                <td>
    <?php
    $time = $slot['slot_time'];
    $hour = (int)substr($time, 0, 2);
    $suffix = ($hour < 12) ? 'AM' : 'PM';
    echo $time . ' ' . $suffix;
    ?>
</td>

                <td>
                    <a href="?delete_id=<?= $slot['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this slot?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('darkMode') === 'true') {
      document.body.classList.add('dark-mode');
    }
  });
</script>

</body>
</html>
