<?php
require_once 'database.php';

// Get departments
$departments = $conn->query("SELECT * FROM departments ORDER BY name ASC");

// Fetch available slots based on selected department and date
$slots = [];
$department_name = "";
if (isset($_GET['department_id']) && isset($_GET['slot_date'])) {
    $department_id = (int)$_GET['department_id'];
    $slot_date = $_GET['slot_date'];

    // Get department name
    $dept_result = $conn->query("SELECT name FROM departments WHERE id = $department_id LIMIT 1");
    $department_row = $dept_result->fetch_assoc();
    $department_name = $department_row['name'];

    $stmt = $conn->prepare("
        SELECT department_slots.id, department_slots.slot_time
        FROM department_slots
        WHERE department_slots.department_id = ?
        AND department_slots.slot_date = ?
        AND NOT EXISTS (
            SELECT 1 FROM appointments 
            WHERE appointments.appointmentDate = department_slots.slot_date
              AND appointments.appointmentTime = department_slots.slot_time
              AND appointments.department_id = ?
        )
        ORDER BY slot_time ASC
    ");
    $stmt->bind_param("isi", $department_id, $slot_date, $department_id);

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
       <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
           body { font-family: sans-serif; background: #f0f0f0; 
         margin: 0;
            padding: 0;
            
        }

        
.nav-links {
    display: flex;
    align-items: center;
    gap: 15px; /* spacing between links */
}


  .navbar {
    background-color: #007bff;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
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

        
    
        .container { background: white; padding: 20px; border-radius: 10px; max-width: 700px; margin: auto; }
        label, select, input { display: block; margin-bottom: 10px; width: 100%; padding: 8px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    </style>
</head>
<body>
         
<nav class="navbar navbar-expand-lg bg-primary py-3  position-relative w-100" data-bs-theme="dark">
  <div class="d-flex justify-content-between align-items-center w-100 px-3">

    <!-- Left Side: Logo + Home -->
    <div class="d-flex align-items-center">
      <img src="img/jk.png" alt="Logo" width="65" height="34" class="img-fluid me-5" />
      <a class="nav-link text-white fw-bold" href="index.php">HOME</a>
      <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <!-- Center: Clock -->
    <div class="position-absolute top-50 start-50 translate-middle text-white fw-bold" id="clock" style="font-size: 18px;"></div>

    <!-- Right Side: Optional collapse content -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Add right-side nav items here -->
    </div>

  </div>
</nav>


<div class="container my-4">
    <h2>Book Appointment</h2>
    <form method="GET" action="">
        <label for="department_id">Select Department:</label>
        <select name="department_id" id="department_id" required>
            <option value="">--Choose Department--</option>
            <?php
            // Reset pointer if needed
            $departments->data_seek(0);
            while ($dept = $departments->fetch_assoc()): ?>
                <option value="<?= $dept['id'] ?>"
                        data-name="<?= htmlspecialchars($dept['name']) ?>"
                        <?= (isset($_GET['department_id']) && $_GET['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="slot_date">Choose Date:</label>
        <input type="date" name="slot_date" required value="<?= isset($_GET['slot_date']) ? $_GET['slot_date'] : '' ?>">

        <button type="submit">Check Available Slots</button>
    </form>

    <?php if (!empty($slots)): ?>
        <h3>Available Slots on <?= htmlspecialchars($_GET['slot_date']) ?>:</h3>
        <form method="POST" action="confirm_bookings.php">
            <input type="hidden" name="department" value="<?= htmlspecialchars($department_name) ?>">
            <input type="hidden" name="appointmentDate" value="<?= htmlspecialchars($_GET['slot_date']) ?>">

            <label>Choose a Time Slot:</label>
            <select name="appointmentTime" required>
                <option value="">--Select Time--</option>
                <?php foreach ($slots as $slot): ?>
                   <option value="<?= $slot['slot_time'] ?>">
    <?= date("h:i A", strtotime($slot['slot_time'])) ?>
</option>

                <?php endforeach; ?>
            </select>

            <label for="amount">Enter Amount:</label>
            <input type="number" name="amount" required min="0">
            <input type="hidden" name="booking_method" value="pay_later">


            <br>
            <button type="submit">Confirm Booking</button>
        </form>
    <?php elseif (isset($_GET['department_id']) && isset($_GET['slot_date'])): ?>
        <p><strong>No available slots on this date.</strong></p>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deptSelect = document.getElementById('department_id');
    const amountInput = document.querySelector('input[name="amount"]');

    deptSelect.addEventListener('change', function () {
        const selectedOption = deptSelect.options[deptSelect.selectedIndex];
        const deptName = selectedOption.getAttribute('data-name');

        if (!amountInput) return;

        if (deptName && deptName.toLowerCase().includes('general medicine')) {
            amountInput.value = 10000;
        } else {
            amountInput.value = 20000;
        }

        amountInput.readOnly = true;  // 👈 prevent manual editing
    });

    // Auto-trigger if a department is already selected
    if (deptSelect.value && amountInput) {
        const event = new Event('change');
        deptSelect.dispatchEvent(event);
    }
});

</script>
<script>
function updateClock() {
    const clock = document.getElementById('clock');
    if (!clock) return;

    const now = new Date();
    // Format time as HH:MM:SS AM/PM
    let hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();
    const ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12; // 0 => 12

    const twoDigit = (num) => num.toString().padStart(2, '0');

    const timeString = `${twoDigit(hours)}:${twoDigit(minutes)}:${twoDigit(seconds)} ${ampm}`;
    clock.textContent = timeString;
}

// Update clock every second
setInterval(updateClock, 1000);
updateClock();  // initial call
</script>

</body>
</html>
