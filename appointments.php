<?php include 'google_translate.php'; ?>

<?php
session_name("user_session");
session_start();
$initial = '?';
if (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}
?>
<?php

require_once 'database.php'; // connection file

// Simulated patient ID (replace with session or login value)
$user_id = $_SESSION['user_id'] ?? null;


// Fetch appointments with payment info
$stmt = $conn->prepare("
    SELECT 
        a.*, 
        d.name AS department,   
        p.date AS payment_date, 
        p.amount AS payment_amount, 
        p.status AS payment_status
    FROM appointments a
    JOIN departments d ON a.department_id = d.id
    LEFT JOIN payments p ON a.id = p.appointment_id
    WHERE a.user_id = ?
      AND (a.status != 'Cancelled' OR a.user_hidden = 0)
    ORDER BY a.appointmentDate DESC
");


$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

// Auto-cancel pending appointments older than 30 minutes
$sql = "
    UPDATE appointments
    SET status = 'Cancelled'
    WHERE status = 'Pending'
    AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 30
";
$conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .actions button { margin-right: 6px; }
        .badge {
  padding: 6px 12px;
  border-radius: 6px;
  color: white;
  font-weight: bold;
  display: inline-block;
}

.pending {
  background-color: #f59e0b; /* orange */
}

.confirmed {
  background-color: #10b981; /* green */
}

.completed {
  background-color: #3b82f6; /* blue */
}

.cancelled {
  background-color: #ef4444; /* red */
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
    z-index: 1000; /* stays above other content */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
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
        .navbar .logo {
            font-size: 20px;
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
          <a class="nav-link active text-white" aria-current="page" href="index.php">HOME</a>
        </li>
        
        
      
      </ul>
      
    </div>
  
  </div>
</nav>
    <h2>My Appointments</h2>

    <?php if (count($appointments) == 0): ?>
        <p>No appointments found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                  <th>Appointment ID</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                      <td><?= htmlspecialchars($appointment['id']) ?></td>
                        <td><?= htmlspecialchars($appointment['department']) ?></td>
                        <td><?= date('Y-m-d', strtotime($appointment['appointmentDate'])) ?></td>
                        <td><?= date('H:i', strtotime($appointment['appointmentTime'])) ?></td>
                        <td>TSH <?= $appointment['amount'] ?></td>
                    <td>
    <?php
        $statusClass = strtolower($appointment['status']); // e.g., "Confirmed" => "confirmed"
    ?>
    <span class="badge <?= $statusClass ?>">
        <?= htmlspecialchars($appointment['status']) ?>
    </span>
</td>

<td class="actions">
    <?php if ($appointment['status'] === 'Pending'): ?>
        <form method="GET" action="reschedule.php" style="display:inline;">
            <input type="hidden" name="reschedule" value="<?= $appointment['id'] ?>">
            <button type="submit">Reschedule</button>
        </form>
        <form method="POST" action="cancel.php" style="display:inline;" onsubmit="return confirm('Cancel this appointment?');">
            <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
            <button type="submit" style="color: red;">Cancel</button>
        </form>
        <form method="GET" action="form.php" style="display:inline;">
            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
            <button type="submit" style="color: green;">Make Payment</button>
        </form>

   <?php elseif ($appointment['status'] === 'Confirmed'): ?>
    <?php
        $appointmentDateTime = new DateTime($appointment['appointmentDate'] . ' ' . $appointment['appointmentTime']);
        $now = new DateTime();

        if ($appointmentDateTime > $now) {
            $remaining = $now->diff($appointmentDateTime);

            if ($remaining->d > 0) {
                $timeLeft = "{$remaining->d} day(s), {$remaining->h} hr, {$remaining->i} min left";
            } elseif ($remaining->h > 0) {
                $timeLeft = "{$remaining->h} hr, {$remaining->i} min left";
            } elseif ($remaining->i > 0) {
                $timeLeft = "{$remaining->i} minute(s) left";
            } else {
                $timeLeft = "Starting soon";
            }
        } else {
            $timeLeft = "<span style='color: red; font-weight: bold;'>Already passed</span>";

        }
    ?>

    <div style="color: #555; margin-top: 5px;">
        ⏳  Time Remaining: <strong><?= $timeLeft ?></strong>
    </div>

    <form method="GET" action="reschedule.php" style="display:inline;">
        <input type="hidden" name="reschedule" value="<?= $appointment['id'] ?>">
        <button type="submit">Reschedule</button>
    </form>


    <?php elseif ($appointment['status'] === 'Cancelled'): ?>
        <form method="POST" action="cancel.php" style="display:inline;" onsubmit="return confirm('Delete this cancelled appointment?');">
            <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
            <button type="submit" style="color: red;">Delete</button>
        </form>

<?php elseif ($appointment['status'] === 'Completed'): ?>
    <?php
        // Combine date and time to calculate exact datetime
        $completedDateTime = date_create($appointment['appointmentDate'] . ' ' . $appointment['appointmentTime']);
        $now = new DateTime();
        $interval = $completedDateTime->diff($now);

        if ($now > $completedDateTime) {
            if ($interval->d > 0) {
                $elapsed = $interval->d . " day(s) ago";
            } elseif ($interval->h > 0) {
                $elapsed = $interval->h . " hour(s) ago";
            } elseif ($interval->i > 0) {
                $elapsed = $interval->i . " minute(s) ago";
            } else {
                $elapsed = "just now";
            }
        } else {
            $elapsed = "Upcoming";
        }
    ?>
    <span style="color: gray;">✅ Completed (<?= $elapsed ?>)</span>



    <?php else: ?>
        <em>No actions available</em>
    <?php endif; ?>
</td>




                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

        <script>
const toggle = document.getElementById('darkModeToggle');
const label = document.getElementById('modeLabel');

document.addEventListener('DOMContentLoaded', function () {
  if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
    toggle.checked = true;
    label.textContent = 'Dark Mode';
  }
});

toggle.addEventListener('change', function () {
  document.body.classList.toggle('dark-mode');
  const mode = document.body.classList.contains('dark-mode');
  localStorage.setItem('darkMode', mode);
  label.textContent = mode ? 'Dark Mode' : 'Light Mode';
});
</script>
</body>
</html>
