<?php
session_name("user_session");
session_start();
require_once 'database.php';

if (!isset($_GET['appointment_id']) || empty($_GET['appointment_id'])) {
    die("No appointment ID specified.");
}
$appointment_id = (int) $_GET['appointment_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Validate appointment
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ? AND user_id = ? AND status = 'Pending'");
$stmt->bind_param("ii", $appointment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid appointment ID or this appointment cannot be paid.");
}

// ✅ Appointment is valid – redirect to payment-method.php
header("Location: payment-method.php?appointment_id=" . $appointment_id);
exit;
?>
