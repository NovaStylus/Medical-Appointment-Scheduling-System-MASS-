<?php
session_name("user_session");
session_start();
require_once 'database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // ✅ Composer's autoloader


if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking'])) {
    echo "<h3 style='font-family: Arial, sans-serif; color: #d9534f;'>Session expired. Please restart the booking process from the beginning.</h3>";
    echo "<a href='index.php' style='
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        font-family: Arial, sans-serif;
        margin-top: 15px;
        transition: background-color 0.3s ease;
    ' onmouseover=\"this.style.backgroundColor='#0056b3'\" onmouseout=\"this.style.backgroundColor='#007bff'\">Return to homepage</a>";
    exit;
}

$user_id = $_SESSION['user_id'];
$booking = $_SESSION['booking'];

$department_id = $booking['department_id'];
$department_name = $booking['department'];
$appointmentDate = $booking['appointmentDate'];
$appointmentTime = $booking['appointmentTime'];
$amount = $booking['amount'];
$status = 'Pending';
$created_at = date("Y-m-d H:i:s");
$booking_method = 'pay_now';


$stmt = $conn->prepare("INSERT INTO appointments 
    (user_id, department_id, department, appointmentDate, appointmentTime, amount, status, booking_method, created_at)
    VALUES (?, ?, ?, ?, ?, ?, 'confirmed', ?, ?)"
);

$stmt->bind_param("iisssiss", 
    $user_id, 
    $department_id, 
    $department_name, 
    $appointmentDate,
    $appointmentTime, 
    $amount, 
    $booking_method, 
    $created_at
);


if ($stmt->execute()) {
    $appointment_id = $stmt->insert_id;

    $method = $_POST['method'];
    $card_number = $_POST['card_number'];
    $masked_card = '**** ' . substr(str_replace(' ', '', $card_number), -4);
    $created_at = date("Y-m-d H:i:s");
    $status = 'Paid';

    // Save payment
    $pay_stmt = $conn->prepare("INSERT INTO payment (appointment_id, method, card_number, amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $pay_stmt->bind_param("issdss", $appointment_id, $method, $masked_card, $amount, $status, $created_at);
    $pay_stmt->execute();
    $pay_stmt->close();

    // Mark slot as booked
    $update_slot_stmt = $conn->prepare("UPDATE department_slots SET is_booked = 1 WHERE department_id = ? AND slot_date = ? AND slot_time = ?");
    $update_slot_stmt->bind_param("iss", $department_id, $appointmentDate, $appointmentTime);
    $update_slot_stmt->execute();
    $update_slot_stmt->close();

    // Insert patient in-app notification
    $message = "Your appointment for $department_name on $appointmentDate at $appointmentTime has been booked successfully.";
    $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $notif_stmt->bind_param("is", $user_id, $message);
    $notif_stmt->execute();
    $notif_stmt->close();

    // Send email to patient
    $user_result = $conn->query("SELECT email, fullName FROM users WHERE id = $user_id");
    if ($user_result && $user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $patient_email = $user_data['email'];
        $patient_name = $user_data['fullName'];

        require 'PHPMailer.php';
        require 'SMTP.php';
        require 'Exception.php';

        $mail = new PHPMailer(); // ✅ No namespace needed

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mussamo0201@gmail.com';
        $mail->Password   = 'wpsjzduwhktgkxyy'; // ⚠️ Store in config for safety
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('mussamo0201@gmail.com', 'MASS Booking System');
        $mail->addAddress($patient_email, $patient_name);

        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation - MASS';
        $mail->Body    = "
            <h3 style='color:#10b981;'>Appointment Confirmed</h3>
            <p>Dear <strong>$patient_name</strong>,</p>
            <p>Your appointment has been successfully booked with the following details:</p>
            <ul>
                <li><strong>Department:</strong> $department_name</li>
                <li><strong>Date:</strong> $appointmentDate</li>
                <li><strong>Time:</strong> $appointmentTime</li>
                <li><strong>Amount Paid:</strong> " . number_format($amount) . " TSH</li>
            </ul>
            <p>Thank you for using MASS!</p>
            <p style='color:#777;font-size:13px;'>MASS Team, " . date("Y") . "</p>
        ";

        try {
            $mail->send();
        } catch (Exception $e) {
            error_log("Email failed to send: " . $mail->ErrorInfo);
        }
    }

    // Notify all receptionists
    $rec_result = $conn->query("SELECT id FROM receptionist");
    while ($row = $rec_result->fetch_assoc()) {
        $receptionist_id = $row['id'];
        $rec_message = "New appointment booked for $department_name on $appointmentDate at $appointmentTime by patient ID $user_id.";
        $rec_stmt = $conn->prepare("INSERT INTO recnotifications (receptionist_id, message) VALUES (?, ?)");
        $rec_stmt->bind_param("is", $receptionist_id, $rec_message);
        $rec_stmt->execute();
        $rec_stmt->close();
    }

    $_SESSION['form_submitted'] = true;
    unset($_SESSION['booking']);
    header("Location: index.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>
