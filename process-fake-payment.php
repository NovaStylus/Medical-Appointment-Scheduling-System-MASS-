<?php
session_name("user_session");
session_start();
require_once 'database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // ✅ Composer's autoloader
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}
$appointment_id = $_POST['appointment_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$appointment_id) {
    die("Missing appointment ID.");
}

$method = $_POST['method'] ?? '';
$card_number = $_POST['card_number'] ?? '';
$masked_card = '**** ' . substr(str_replace(' ', '', $card_number), -4);
$amount = $booking['amount'] ?? 10000; // or allow user input if you want

// Update appointment status & amount
$update_stmt = $conn->prepare("UPDATE appointments SET amount = ?, status = 'Confirmed' WHERE id = ? AND user_id = ?");
$update_stmt->bind_param("dii", $amount, $appointment_id, $user_id);

if ($update_stmt->execute()) {
   $created_at = date("Y-m-d H:i:s");
$status = 'Paid';
$pay_stmt = $conn->prepare("INSERT INTO payment (appointment_id, method, card_number, amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
$pay_stmt->bind_param("issdss", $appointment_id, $method, $masked_card, $amount, $status, $created_at);
$pay_stmt->execute();
$pay_stmt->close();


 // Fetch appointment details
$appt_stmt = $conn->prepare("SELECT department, appointmentDate, appointmentTime FROM appointments WHERE id = ? AND user_id = ?");
$appt_stmt->bind_param("ii", $appointment_id, $user_id);
$appt_stmt->execute();
$appt_stmt->bind_result($department_name, $appointmentDate, $appointmentTime);
$appt_stmt->fetch();
$appt_stmt->close();

// Patient notification
$message = "Your appointment for $department_name on $appointmentDate at $appointmentTime has been confirmed.";
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

// Notify receptionists
$rec_result = $conn->query("SELECT id FROM receptionist");
while ($row = $rec_result->fetch_assoc()) {
    $rec_id = $row['id'];
    $rec_message = "New appointment booked for $department_name on $appointmentDate at $appointmentTime by patient ID $user_id.";
    $rec_stmt = $conn->prepare("INSERT INTO recnotifications (receptionist_id, message) VALUES (?, ?)");
    $rec_stmt->bind_param("is", $rec_id, $rec_message);
    $rec_stmt->execute();
    $rec_stmt->close();
}


    unset($_SESSION['booking']);
    header("Location: index.php?success=1");
    exit;
} else {
    echo "Error updating appointment: " . $conn->error;
}

$update_stmt->close();
?>
