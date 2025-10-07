<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['signup_data'])) {
    echo "<script>alert('Session expired. Please sign up again.'); window.location='signup.html';</script>";
    exit();
}

$userOtp = trim($_POST['otp']);
$storedOtp = $_SESSION['signup_data']['otp'];

if ($userOtp == $storedOtp) {
    $fullname = $_SESSION['signup_data']['fullname'];
    $email    = $_SESSION['signup_data']['email'];
    $phone    = $_SESSION['signup_data']['phone'];
    $password = $_SESSION['signup_data']['password'];

    // Insert user
    $stmt = $conn->prepare("INSERT INTO student (Name, Email, Phone, Password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $phone, $password);

    if ($stmt->execute()) {
        unset($_SESSION['signup_data']); // clear session
        echo "<script>alert('✅ Signup successful! Please login.'); window.location='slogin.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "<script>alert('❌ Invalid OTP. Try again!'); window.location='otpverify.php';</script>";
}
$conn->close();
?>
