<?php
session_start();

if (!isset($_SESSION['forgot_password_otp']) || !isset($_SESSION['forgot_password_email'])) {
    echo "<script>alert('❌ Invalid request!'); window.location='forgate.html';</script>";
    exit();
}

// Check OTP expiry
if (time() > $_SESSION['otp_expiry']) {
    session_destroy();
    echo "<script>alert('❌ OTP has expired!'); window.location='forgate.html';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);
    
    if ($entered_otp == $_SESSION['forgot_password_otp']) {
        // OTP verified, redirect to password reset page
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.html");
        exit();
    } else {
        echo "<script>alert('❌ Invalid OTP!'); window.history.back();</script>";
        exit();
    }
}
?>