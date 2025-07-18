<?php
session_name("user_session");
session_start();
require_once "database.php";

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(["count" => 0]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["count" => $row['unread']]);
$stmt->close();
?>
