<?php
session_name("user_session");
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $appointment_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("Unauthorized access.");
    }

    // Get appointment status
    $stmt = $conn->prepare("SELECT slot_id, status FROM appointments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $appointment = $result->fetch_assoc();
        $status = $appointment['status'];

        if ($status === 'Pending') {
            // Cancel pending appointment
            $cancelStmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
            $cancelStmt->bind_param("i", $appointment_id);

            if ($cancelStmt->execute()) {
                header("Location: appointments.php?cancelled=1");
                exit;
            } else {
                echo "Error cancelling appointment.";
            }

        } elseif ($status === 'Cancelled') {
            // Soft-delete for user only: mark as hidden
            $hideStmt = $conn->prepare("UPDATE appointments SET user_hidden = 1 WHERE id = ?");
            $hideStmt->bind_param("i", $appointment_id);

            if ($hideStmt->execute()) {
                header("Location: appointments.php?hidden=1");
                exit;
            } else {
                echo "Error hiding appointment.";
            }

        } else {
            echo "Only pending or cancelled appointments can be managed.";
        }
    } else {
        echo "Appointment not found.";
    }
} else {
    echo "Invalid request.";
}
