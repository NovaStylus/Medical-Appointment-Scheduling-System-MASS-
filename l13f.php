<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $otp = $_POST["otp"];

    // Check if the OTP exists and is not expired
    $sql = "SELECT * FROM receptionist WHERE otp = '$otp' AND TIMESTAMPDIFF(SECOND, otp_generated_time, NOW()) <= 60";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // ✅ Set the full receptionist session
        $_SESSION['receptionist'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['fullName'],
        ];

        header('location: delete_rec_account.php');
        exit();
    } else {
        echo 'Wrong OTP number or OTP has expired.';
    }
}
mysqli_close($conn);

?>