<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';
require __DIR__ . '/phpmailer/src/Exception.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check in `student` table
        $stmt = $conn->prepare("SELECT Email FROM student WHERE LOWER(Email)=LOWER(?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['forgot_password_otp'] = $otp;
            $_SESSION['forgot_password_email'] = $email;
            $_SESSION['otp_expiry'] = time() + 600; // 10 minutes expiry

            // Send OTP using PHPMailer
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;
                $mail->Username = "nivasgruh@gmail.com";   // your Gmail
                $mail->Password = "lwltpmtmwnptuhsn";      // Google App Password
                $mail->SMTPSecure = "tls";
                $mail->Port = 587;

                $mail->setFrom("sutariyadhruv20@gmail.com", "NiwasGruh");
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Password Reset OTP - NiwasGruh";
                $mail->Body = "
                    <h3>Password Reset Request</h3>
                    <p>Your OTP for password reset is:</p>
                    <h2>$otp</h2>
                    <p>This OTP will expire in 10 minutes.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                ";

                $mail->send();
                echo "<script>alert('✅ OTP sent to your email!'); window.location='verify_otp_forgot.html';</script>";
            } catch (Exception $e) {
                echo "❌ Mail Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "<script>alert('❌ Email not found!'); window.location='forgate.html';</script>";
        }
    } else {
        echo "<script>alert('⚠️ Please enter your email!'); window.location='forgate.html';</script>";
    }
}
?>