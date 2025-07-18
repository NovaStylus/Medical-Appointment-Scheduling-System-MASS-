
<?php
require 'database.php'; // or db config connection

header('Content-Type: application/json');

$sql = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo json_encode(['count' => (int)$data['count']]);
?>
