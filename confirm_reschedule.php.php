<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = (int)$_POST['appointment_id'];
    $new_date = $_POST['slot_date'];
    $new_time = $_POST['slot_time'];

    // Update the appointment
    $stmt = $conn->prepare("UPDATE appointments SET appointmentDate = ?, appointmentTime = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_date, $new_time, $appointment_id);
    if ($stmt->execute()) {
        echo "<p>Appointment successfully rescheduled to $new_date at $new_time.</p>";
    } else {
        echo "<p>Failed to reschedule appointment.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>
