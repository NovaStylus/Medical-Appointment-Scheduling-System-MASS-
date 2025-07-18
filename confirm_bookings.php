<?php 
session_name("user_session");
session_start();

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_name = $_POST['department'];
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];

    if (!isset($_SESSION['user_id'])) {
        die("Error: User not logged in.");
    }

    $user_id = $_SESSION['user_id'];

    // 🛑 Check if user has already booked within last 24 hours
$check_stmt = $conn->prepare("SELECT COUNT(*) AS recent FROM appointments WHERE user_id = ? AND created_at >= NOW() - INTERVAL 6 HOUR AND booking_method = 'pay_later'");

    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($check_result['recent'] > 0) {
echo "<h3 style='font-family: Arial, sans-serif; color: #d9534f;'>You have already booked an appointment within the last 6 hours. Please wait before booking again.</h3>";

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

    // Get department ID
    $stmt = $conn->prepare("SELECT id FROM departments WHERE name = ?");
    $stmt->bind_param("s", $department_name);
    $stmt->execute();
    $stmt->bind_result($department_id);
    $stmt->fetch();
    $stmt->close();

    if (!$department_id) {
        die("Invalid department.");
    }

    // ✅ Find slot_id based on date, time, department and not already booked
    $slot_stmt = $conn->prepare("SELECT id FROM department_slots WHERE department_id = ? AND slot_date = ? AND slot_time = ? AND is_booked = 0 LIMIT 1");
    $slot_stmt->bind_param("iss", $department_id, $appointmentDate, $appointmentTime);
    $slot_stmt->execute();
    $slot_result = $slot_stmt->get_result();
    $slot = $slot_result->fetch_assoc();
    $slot_stmt->close();

    if (!$slot) {
        die("This slot has already been booked.");
    }

    $slot_id = $slot['id'];
    $status = 'Pending';

    // ✅ Insert appointment with slot_id
    $booking_method = $_POST['booking_method'] ?? 'pay_later';

$stmt = $conn->prepare("
    INSERT INTO appointments 
    (user_id, department_id, department, appointmentDate, appointmentTime, slot_id, status, booking_method, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("iisssiss", $user_id, $department_id, $department_name, $appointmentDate, $appointmentTime, $slot_id, $status, $booking_method);


    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id;

        // ✅ Mark slot as booked
        $update_slot = $conn->prepare("UPDATE department_slots SET is_booked = 1 WHERE id = ?");
        $update_slot->bind_param("i", $slot_id);
        $update_slot->execute();
        $update_slot->close();

        // ✅ Notify user
        $message = "Your appointment for $department_name on $appointmentDate at $appointmentTime has been booked.";
        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif_stmt->bind_param("is", $user_id, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        // ✅ Notify all receptionists
        $rec_result = $conn->query("SELECT id FROM receptionist");
        while ($row = $rec_result->fetch_assoc()) {
            $receptionist_id = $row['id'];
            $rec_message = "New appointment booked for $department_name on $appointmentDate at $appointmentTime by patient ID $user_id.";

            $rec_stmt = $conn->prepare("INSERT INTO recnotifications (receptionist_id, message) VALUES (?, ?)");
            $rec_stmt->bind_param("is", $receptionist_id, $rec_message);
            $rec_stmt->execute();
            $rec_stmt->close();
        }

        header("Location: appointments.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
