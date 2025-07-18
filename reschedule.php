<?php include 'google_translate.php'; ?>
<?php
session_name("user_session");
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in.");
}

$user_id = $_SESSION['user_id'];
$available_slots = [];
$selected_appt = null;

// Step 1: Show available slots after new date is selected
if (isset($_GET['appointment_id'], $_GET['new_date'])) {
    $appointment_id = (int)$_GET['appointment_id'];
    $new_date = $_GET['new_date'];

    // Get current appointment
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selected_appt = $result->fetch_assoc();
    $stmt->close();

    if ($selected_appt) {
        $department_id = $selected_appt['department_id'];

        // Get available slots
        $stmt = $conn->prepare("
            SELECT slot_time FROM department_slots
            WHERE department_id = ? AND slot_date = ?
            AND NOT EXISTS (
                SELECT 1 FROM appointments
                WHERE appointmentDate = department_slots.slot_date
                AND appointmentTime = department_slots.slot_time
                AND department_id = ?
            )
            ORDER BY slot_time ASC
        ");
        $stmt->bind_param("isi", $department_id, $new_date, $department_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $available_slots[] = $row['slot_time'];
        }
        $stmt->close();
    }
}

// Step 2: Confirm rescheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];

    $stmt = $conn->prepare("
        UPDATE appointments 
        SET appointmentDate = ?, appointmentTime = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ssii", $new_date, $new_time, $appointment_id, $user_id);

    if ($stmt->execute()) {
        $department_name = $selected_appt['department'] ?? 'Unknown Department';
$appointmentDate = $selected_appt['appointmentDate'] ?? '';
$appointmentTime = $selected_appt['appointmentTime'] ?? '';

$message = "Your appointment for $department_name on $appointmentDate at $appointmentTime has been rescheduled to $new_date at $new_time.";

        $success = "Appointment rescheduled successfully.";
        $message = "Your appointment for $department_name on $appointmentDate at $appointmentTime has been rescheduled to $new_date at $new_time.";

        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif_stmt->bind_param("is", $user_id, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        // Redirect after 3 seconds
        echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 3000);</script>";
    } else {
        $error = "Failed to reschedule: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all user appointments
$stmt = $conn->prepare("
    SELECT a.id, d.name AS department, a.department_id, a.appointmentDate, a.appointmentTime, a.status 
    FROM appointments a 
    JOIN departments d ON a.department_id = d.id 
    WHERE a.user_id = ?
    ORDER BY a.appointmentDate DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reschedule Appointment</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
       body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
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
        .container {
            max-width: 900px;
            margin:30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f7f7f7;
        }
        input[type="date"], select, button {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            margin: 5px 0;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            transition: 0.3s ease;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            text-align: center;
            font-size: 16px;
            padding: 10px;
            margin-top: 10px;
            border-radius: 8px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form.inline {
            display: inline;
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
    <h2>Reschedule Appointment</h2>

    <?php if (isset($success)): ?>
        <div class="message success"><?= htmlspecialchars($success) ?> Redirecting to dashboard...</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($appointments->num_rows > 0): ?>
        <table>
            <tr>
                <th>Department</th>
                <th>Current Date</th>
                <th>Current Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $appointments->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['appointmentDate']) ?></td>
                    <td><?= htmlspecialchars($row['appointmentTime']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <form class="inline" method="GET">
                            <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                            <input type="date" name="new_date" required>
                            <button type="submit">Get Slots</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="message error">No appointments found.</p>
    <?php endif; ?>

    <?php if ($selected_appt && !empty($available_slots)): ?>
        <h3>Available slots on <?= htmlspecialchars($new_date) ?> (<?= htmlspecialchars($selected_appt['department']) ?>)</h3>
        <form method="POST">
            <input type="hidden" name="appointment_id" value="<?= $selected_appt['id'] ?>">
            <input type="hidden" name="new_date" value="<?= htmlspecialchars($new_date) ?>">
            <label for="new_time">Choose Time Slot:</label>
            <select name="new_time" required>
                <option value="">-- Select Slot --</option>
                <?php foreach ($available_slots as $time): ?>
                    <option value="<?= htmlspecialchars($time) ?>"><?= htmlspecialchars($time) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Confirm Reschedule</button>
        </form>
    <?php elseif ($selected_appt && empty($available_slots)): ?>
        <p class="message error">No available slots for that date.</p>
    <?php endif; ?>
</div>
</body>
</html>
