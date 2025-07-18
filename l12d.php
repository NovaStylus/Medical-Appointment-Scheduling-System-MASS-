

<?php
include 'database.php';

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Debug email passed in
    echo "Target email: " . $email . "<br>";

    $result = mysqli_query($conn, "SELECT * FROM receptionist WHERE email='$email'");
    if (!$result || mysqli_num_rows($result) === 0) {
        echo "No user found with that email.";
        exit;
    }

    $fetch = mysqli_fetch_assoc($result);
    $username = $fetch['username'];
    $otp = $fetch['otp'];

    // Debug user data
    echo "Username: $username, OTP: $otp<br>";

    include 'PHPMailer.php';
    include 'Exception.php';
    include 'SMTP.php';

    $mailer = new PHPMailer;
    $mailer->isSMTP();
    $mailer->Host = 'smtp.gmail.com';
    $mailer->SMTPAuth = true;
    $mailer->Username = 'mussamo0201@gmail.com';
    $mailer->Password = 'wpsjzduwhktgkxyy'; // Use env or config file
    $mailer->SMTPSecure = 'tls';
    $mailer->Port = 587;

    $mailer->setFrom('mussamo0201@gmail.com', 'MASS');
    $mailer->addAddress($email); // Use dynamic email
    $mailer->isHTML(true);
    $mailer->Subject = 'VERIFICATION CODE';

    $mailer->Body = "
                <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body>
        <h3 style='background-color:blue;color:white;text-align:center;font-size:0.4cm;'>Website Verification Code</h3>
            <p>HI! <strong>" . $username . ",</strong></p>
            <p>We received a request to access your website account through your email address:</p>
            <p><strong><span style='color: blue;'>" . $email . "</span></strong></p>
            <p>Your verification OTP is:</p>
            <p><strong>" . $otp . "</strong></p>
            <p>If you did not request this OTP, it is possible that someone is trying to access your account.</p>
            <p>Sincerely yours,<br>The JK TEAM</p>
        </body>
        </html>
    ";

    if ($mailer->send()) {
        header("Location: l14d.php");
        exit;
    } else {
        echo 'Email not sent: ' . $mailer->ErrorInfo;
    }
}
?>
