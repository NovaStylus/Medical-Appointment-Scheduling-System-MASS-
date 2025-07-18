<?php
session_start();
require 'database.php';

$response = ['count' => 0];

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT COUNT(*) AS unread FROM users WHERE email = ? AND is_read = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $response['count'] = $result['unread'];
    $stmt->close();
}

echo json_encode($response);
