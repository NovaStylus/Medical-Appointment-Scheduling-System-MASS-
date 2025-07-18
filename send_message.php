<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: contact.php?error=invalid_email");
        exit();
    }

    // Insert into contact_messages table
    $sql = "INSERT INTO contact_messages (name, email, subject, message) 
            VALUES ('$name', '$email', '$subject', '$message')";
    if ($conn->query($sql)) {

        // Insert notification for admin
        $admin_message = "New contact message from: $name ($email)";
        $admin_stmt = $conn->prepare("INSERT INTO admin_notifications (message) VALUES (?)");
        $admin_stmt->bind_param("s", $admin_message);
        $admin_stmt->execute();
        $admin_stmt->close();

        header("Location: contact.php?success=1");
        exit();
    } else {
        error_log("Database error: " . $conn->error);
        header("Location: contact.php?error=database_error");
        exit();
    }
} else {
    header("Location: contact.php");
    exit();
}
