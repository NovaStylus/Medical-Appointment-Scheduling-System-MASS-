<?php
require_once "database.php";

$stmt = $conn->prepare("SELECT COUNT(*) as unread FROM admin_notifications WHERE is_read = 0");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
echo json_encode(["count" => $row['unread']]);
$stmt->close();
?>
