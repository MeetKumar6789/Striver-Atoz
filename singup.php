<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';
require __DIR__ . '/phpmailer/src/Exception.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fullname = $_POST['fullname'];
$email = strtolower(trim($_POST['email']));
$phone = $_POST['phone'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];



$check = $conn->prepare("SELECT 1 FROM student WHERE LOWER(Email)=LOWER(?) LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();
if ($res && $res->num_rows > 0) {
    echo "<script>alert('You already signed up with this email. Please login.'); window.location='slogin.html';</script>";
    exit();
}
$check->close();

$otp = rand(100000, 999999);

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$_SESSION['signup_data'] = [
    'fullname' => $fullname,
    'email'    => $email,
    'phone'    => $phone,
    'password' => $hashed_password, 
    'otp'      => $otp
];

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "nivasgruh@gmail.com";  
    $mail->Password = "lwltpmtmwnptuhsn"; 
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("yourgmail@gmail.com", "NiwasGruh Hostel");
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Your OTP for Signup Verification";
    $mail->Body    = "<h3>Hello $fullname,</h3>
                      <p>Your OTP for verification is:</p>
                      <h2>$otp</h2>
                      <p>This OTP will expire in 10 minutes.</p>";

    $mail->send();
    header("Location: otpverify.php");
    exit();
} catch (Exception $e) {
    echo "❌ Mail Error: {$mail->ErrorInfo}";
}
?>