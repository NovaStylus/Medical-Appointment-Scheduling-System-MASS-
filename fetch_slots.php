<?php
require_once 'database.php';

if (isset($_GET['department_id'], $_GET['date'])) {
    $dept_id = (int)$_GET['department_id'];
    $date = $_GET['date'];

    // Get booked times
    $booked = [];
    $stmt = $conn->prepare("SELECT appointment_time FROM appointments WHERE department_id = ? AND appointment_date = ?");
    $stmt->bind_param("is", $dept_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($r = $result->fetch_assoc()) {
        $booked[] = $r['appointment_time'];
    }

    // Get available slots from admin-defined department_slots
    $available = [];
    $stmt2 = $conn->prepare("SELECT slot_time FROM department_slots WHERE department_id = ? AND slot_date = ?");
    $stmt2->bind_param("is", $dept_id, $date);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($r = $res2->fetch_assoc()) {
        if (!in_array($r['slot_time'], $booked)) {
            $available[] = $r['slot_time'];
        }
    }

    echo json_encode($available);
}
?>
