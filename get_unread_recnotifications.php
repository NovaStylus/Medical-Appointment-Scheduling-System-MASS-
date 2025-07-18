<?php
session_start();
require_once "database.php";

// Check if the receptionist is logged in
if (!isset($_SESSION['receptionist_id'])) {
    echo json_encode(["count" => 0]);
    exit();
}

$receptionist_id = $_SESSION['receptionist_id'];

// Fetch unread notification count for that specific receptionist
$stmt = $conn->prepare("SELECT COUNT(*) as unread FROM recnotifications WHERE receptionist_id = ? AND is_read = 0");
$stmt->bind_param("i", $receptionist_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["count" => $row['unread']]);
$stmt->close();
?>
