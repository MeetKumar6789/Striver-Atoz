<?php
session_start();

if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    echo "<script>alert('❌ OTP verification required!'); window.location='forgate.html';</script>";
    exit();
}

if (!isset($_SESSION['forgot_password_email'])) {
    echo "<script>alert('❌ Session expired!'); window.location='forgate.html';</script>";
    exit();
}

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
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['forgot_password_email'];

    // Validate passwords
    if ($new_password !== $confirm_password) {
        echo "<script>alert('❌ Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    if (strlen($new_password) < 8) {
        echo "<script>alert('❌ Password must be at least 8 characters long!'); window.history.back();</script>";
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $conn->prepare("UPDATE student SET Password = ? WHERE LOWER(Email) = LOWER(?)");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        // Clear session
        session_destroy();
        echo "<script>alert('✅ Password reset successfully!'); window.location='slogin.html';</script>";
    } else {
        echo "<script>alert('❌ Error updating password!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>