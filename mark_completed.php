<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $id = intval($_POST['appointment_id']);
    $stmt = $conn->prepare("UPDATE appointments SET status = 'Completed' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: receptionist.php");
exit;
