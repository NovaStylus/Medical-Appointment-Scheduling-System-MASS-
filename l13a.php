<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $otp = $_POST["otp"];

    // Check if the OTP exists and if it's not expired (within 5 minutes)
    $sql = "SELECT * FROM users WHERE otp = '$otp' AND TIMESTAMPDIFF(SECOND,
     otp_generated_time, NOW()) <= 60";
    
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_num_rows($result);

        if ($row == 1) {
            $_SESSION['otp'] = $otp;
            header('location: reset.php');
        } else {
            echo 'Wrong OTP number or OTP has expired.';
        }
    }
}
mysqli_close($conn);
?>