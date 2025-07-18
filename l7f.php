
<?php
session_name("receptionist_session");
session_start();

include 'database.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Generate random OTP
    function generateRandomOTP()
    {
        $length = 6;
        $characters = '0123456789';
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $otp;
    }
    $hiddenOTP = generateRandomOTP();

    // Check if email exists
    $sql = "SELECT * FROM receptionist WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_num_rows($result);

        if ($row == 1) {
            $_SESSION['email'] = $email;

            // Use NOW() function to store current timestamp in otp_generated_time column
            $updateSql = "UPDATE receptionist SET otp = '$hiddenOTP', otp_generated_time = NOW() WHERE 
            email = '$email'";

            if (mysqli_query($conn, $updateSql)) {
                header('location: l12f.php?email=' . $email);
            } else {
                echo "ERROR: Could not update OTP." . mysqli_error($conn);
            }
        } else {
            echo "Email not found in the database.";
        }
    } else {
        echo "ERROR: Could not execute $sql." . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>