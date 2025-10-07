<?php
session_start();
if (!isset($_SESSION['signup_data'])) {
    header("Location: signup.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Verify OTP</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; display:flex; justify-content:center; align-items:center; height:100vh;}
    .otp-box { background:white; padding:30px; border-radius:10px; box-shadow:0 0 10px #aaa; width:350px; text-align:center;}
    input[type="text"] { padding:10px; width:80%; font-size:16px; margin:10px 0; border:1px solid #ccc; border-radius:6px; }
    input[type="submit"] { background:#0072ff; color:white; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; }
    input[type="submit"]:hover { background:#0057cc; }
  </style>
</head>
<body>
  <div class="otp-box">
    <h2>Email Verification</h2>
    <p>Please enter the OTP sent to your email.</p>
    <form method="post" action="verifyprocess.php">
      <input type="text" name="otp" placeholder="Enter OTP" required>
      <br>
      <input type="submit" value="Verify OTP">
    </form>
  </div>
</body>
</html>
