<?php include 'google_translate.php'; ?>

<?php
require_once 'database.php';

// Fetch all departments for the dropdown
$departments_result = $conn->query("SELECT DISTINCT departments.id, departments.name FROM departments JOIN department_slots ON departments.id = department_slots.department_id ORDER BY departments.name ASC");

// Get selected filters
$selected_dept = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$selected_date = isset($_GET['slot_date']) ? $_GET['slot_date'] : '';

$query = "
    SELECT ds.id, d.name AS department, ds.slot_date, ds.slot_time
    FROM department_slots ds
    JOIN departments d ON d.id = ds.department_id
    WHERE ds.is_booked = 0
";



$params = [];
$types = "";

if ($selected_dept > 0) {
    $query .= " AND ds.department_id = ?";
    $types .= "i";
    $params[] = $selected_dept;
}

if (!empty($selected_date)) {
    $query .= " AND ds.slot_date = ?";
    $types .= "s";
    $params[] = $selected_date;
}

$query .= " ORDER BY d.name ASC, ds.slot_date ASC, ds.slot_time ASC";


// Prepare and bind
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Group results
$grouped_slots = [];
while ($slot = $result->fetch_assoc()) {
    $dept = $slot['department'];
    $grouped_slots[$dept][] = $slot;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Appointment Timetable</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        h3 {
            margin-top: 40px;
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #e0e0e0;
        }
        .date-header {
            background: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 10px;
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
    z-index: 1000; /* stays above other content */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .back-btn {
            background: #555;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .back-btn:hover {
            background: #333;
        }
        .filter-form {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-form select, .filter-form button {
            padding: 8px;
            margin: 0 5px;
        }
        body.dark-mode {
            background-color: #121212;
            color: #f0f0f0;
        }
        body.dark-mode .container {
            background-color: #1e1e1e;
        }
        body.dark-mode table,
        body.dark-mode th,
        body.dark-mode td {
            background-color: #2c2c2c;
            border-color: #444;
            color: #f0f0f0;
        }
        body.dark-mode .date-header {
            background-color: #333;
            color: #ddd;
        }
    </style>
    
</head>
<body>
    <div style="height: 70px;"></div>

      <nav class="navbar navbar-expand-lg bg-body-primary py-3">
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
        <h2>Available Slots by Department & Date</h2>

        <!-- Filter form -->
        <form method="GET" class="filter-form">
            <label for="department">Department:</label>
            <select name="department" id="department">
                <option value="0">-- All Departments --</option>
                <?php while ($dept = $departments_result->fetch_assoc()): ?>
                    <option value="<?= $dept['id'] ?>" <?= $selected_dept == $dept['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dept['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="slot_date">Date:</label>
            <input type="date" name="slot_date" id="slot_date" value="<?= htmlspecialchars($selected_date) ?>">

            <button type="submit">Filter</button>
        </form>

        <?php if (empty($grouped_slots)): ?>
            <p style="text-align:center;">No slots found for your selected filters.</p>
        <?php else: ?>
            <?php foreach ($grouped_slots as $department => $slot_list): ?>
                <h3><?= htmlspecialchars($department) ?> Department</h3>
                <table>
                    <tr>
                        <th>Slot ID</th>
                        <th>Time</th>
                    </tr>
                    <?php
                    $last_date = '';
                    foreach ($slot_list as $slot):
                        $is_new_date = $slot['slot_date'] !== $last_date;
                        if ($is_new_date):
                            echo '<tr><td colspan="2" class="date-header">Date: <strong>' . $slot['slot_date'] . '</strong></td></tr>';
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
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="payment_option.php"><button class="back-btn">← Back to Booking</button></a>
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
