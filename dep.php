<?php
session_start();
require_once 'database.php';

// Add department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $dept = trim($_POST['department_name']);
    if ($dept !== "") {
        $stmt = $conn->prepare("INSERT IGNORE INTO departments (name) VALUES (?)");
        $stmt->bind_param("s", $dept);
        $stmt->execute();
    }
}

// Remove department
// Remove department
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Delete dependent slots first
    $conn->query("DELETE FROM department_slots WHERE department_id = $id");

    // Then delete department
    $conn->query("DELETE FROM departments WHERE id = $id");

    header("Location: dep.php");
    exit;
}


// Set slots
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_slots'])) {
    $department_id = (int)$_POST['department_id'];
    $conn->query("DELETE FROM department_slots WHERE department_id = $department_id"); // Clear old slots

    foreach ($_POST['slots'] as $slot) {
        [$date, $time] = explode('|', $slot);
        $stmt = $conn->prepare("INSERT IGNORE INTO department_slots (department_id, slot_date, slot_time) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $department_id, $date, $time);
        $stmt->execute();
    }
}

// Prepare data
$departments = $conn->query("SELECT * FROM departments ORDER BY name ASC");
$timeSlots = [];
$start = strtotime("00:00");
$end = strtotime("23:30");
while ($start <= $end) {
    $timeSlots[] = date("H:i", $start);
    $start = strtotime("+30 minutes", $start);
}

// Current slots
$currentSlots = [];
if (!empty($_POST['department_id'])) {
    $did = (int)$_POST['department_id'];
    $res = $conn->query("SELECT slot_date, slot_time FROM department_slots WHERE department_id = $did");
    while ($r = $res->fetch_assoc()) {
        $currentSlots[$r['slot_date']][] = $r['slot_time'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments & Slots</title>
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
         { font-family: sans-serif; background: #f0f0f0; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 10px; max-width: 900px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer;}
        .danger { color: red; text-decoration: none; }
        .slot-box { display: inline-block; margin: 2px; }
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
<div class="container">
    <h2>Manage Departments</h2>
    <form method="POST">
       <div class="row g-2 align-items-center">
  <div class="col-auto">
    <input type="text" name="department_name" class="form-control" placeholder="New department..." required>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary" name="add_department">Add Department</button>
  </div>
</div>

    </form>

    <table>
        <tr><th>ID</th><th>Name</th><th>Action</th></tr>
        <?php while ($dept = $departments->fetch_assoc()): ?>
        <tr>
            <td><?= $dept['id'] ?></td>
            <td><?= htmlspecialchars($dept['name']) ?></td>
            <td><a href="?delete=<?= $dept['id'] ?>" class="danger" onclick="return confirm('Delete this department?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Set Available Slots</h2>
    <form method="POST">
        <label>Select Department:</label>
        <select name="department_id" required onchange="this.form.submit()">
            <option value="">-- Choose Department --</option>
            <?php
            $departments = $conn->query("SELECT * FROM departments ORDER BY name ASC");
            while ($d = $departments->fetch_assoc()):
            ?>
                <option value="<?= $d['id'] ?>" <?= (isset($_POST['department_id']) && $_POST['department_id'] == $d['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <?php if (!empty($_POST['department_id'])): ?>
            <table>
                <tr>
                    <th>Time / Day</th>
                    <?php for ($i = 0; $i < 7; $i++): 
                        $date = date('Y-m-d', strtotime("+$i days"));
                        $label = date('D (m-d)', strtotime($date));
                    ?>
                        <th><?= $label ?><br><small><?= $date ?></small></th>
                    <?php endfor; ?>
                </tr>
                <?php foreach ($timeSlots as $time): ?>
                    <tr>
                        <td><?= $time ?></td>
                        <?php for ($i = 0; $i < 7; $i++): 
                            $date = date('Y-m-d', strtotime("+$i days"));
                            $checked = (isset($currentSlots[$date]) && in_array($time, $currentSlots[$date])) ? 'checked' : '';
                        ?>
                            <td>
                                <input type="checkbox" name="slots[]" value="<?= $date . '|' . $time ?>" <?= $checked ?>>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit" name="set_slots">Save Slots</button>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
