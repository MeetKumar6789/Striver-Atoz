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

$email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    echo "<script>alert('Please enter both Email and Password!'); window.history.back();</script>";
    exit();
}

$adminEmail = "24it053@charusat.edu.in";   
$adminPass  = "I9pkB6B7meet";            
if ($email === strtolower($adminEmail) && $password === $adminPass) {
    $_SESSION['email'] = $adminEmail;
    $_SESSION['name']  = "Administrator";
    header("Location: adashboard.html");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM student WHERE LOWER(Email)=LOWER(?) LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['Password'])) {
        $_SESSION['email'] = $row['Email'];
        $_SESSION['name']  = $row['Name'];

        $stmtRegC = $conn->prepare("SELECT COUNT(*) AS c FROM application WHERE TRIM(LOWER(Email)) = TRIM(LOWER(?))");
        $stmtRegC->bind_param("s", $email);
        $stmtRegC->execute();
        $regCntRes = $stmtRegC->get_result();
        $regCntRow = $regCntRes ? $regCntRes->fetch_assoc() : null;
        $regCount = $regCntRow ? (int)$regCntRow['c'] : 0;
        $stmtRegC->close();

        $stmtApplyC = $conn->prepare("SELECT COUNT(*) AS c FROM apply WHERE TRIM(LOWER(Email)) = TRIM(LOWER(?))");
        $stmtApplyC->bind_param("s", $email);
        $stmtApplyC->execute();
        $applyCntRes = $stmtApplyC->get_result();
        $applyCntRow = $applyCntRes ? $applyCntRes->fetch_assoc() : null;
        $applyCount = $applyCntRow ? (int)$applyCntRow['c'] : 0;
        $stmtApplyC->close();

        $isRegistered = $regCount > 0;
        $hasApplied   = $applyCount > 0;

        error_log('[login] email=' . $email . ' regCount=' . $regCount . ' applyCount=' . $applyCount);

        if ($isRegistered || $hasApplied) {
            header("Location: sdashboard.php");
            exit();
        }

        header("Location: registration.html");
        exit();
    } else {
        echo "<script>alert('Invalid Email or Password!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid Email or Password!'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>