<?php 

session_name("user_session");
session_start();

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_name = $_POST['department'];
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : null;
    $sex = $_POST['sex'] ?? null;
    $age = isset($_POST['age']) ? (int)$_POST['age'] : null;

if ($age < 1 || $age > 120) {
    die("Invalid age. Please enter a value between 1 and 120.");
}


    if (!isset($_SESSION['user_id'])) {
        die("Error: User or patient not logged in.");
    }

    $user_id = $_SESSION['user_id'];

    // Get department ID by name
    $stmt = $conn->prepare("SELECT id FROM departments WHERE name = ?");
    $stmt->bind_param("s", $department_name);
    $stmt->execute();
    $stmt->bind_result($department_id);
    $stmt->fetch();
    $stmt->close();

    if (!$department_id) {
        die("Invalid department.");
    }

    // Store all booking data in session, including sex and age
    $_SESSION['booking'] = [
        'user_id' => $user_id,
        'department_id' => $department_id,
        'department' => $department_name,
        'appointmentDate' => $appointmentDate,
        'appointmentTime' => $appointmentTime,
        'amount' => $amount,
        'sex' => $sex,
        'age' => $age
    ];

    // Redirect to payment page
    header("Location: payment_method.php");
    exit();
}
?>
