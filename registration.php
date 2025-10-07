<?php
session_start();
// Require login to submit registration
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first.'); window.location.href='slogin.html';</script>";
    exit();
}
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "student";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$fname     = $_POST['first_name'];
$mname     = $_POST['middle_name'];
$lname     = $_POST['last_name'];
$email     = strtolower(trim($_SESSION['email']));
$phone     = $_POST['phone'];
$gender    = $_POST['gender'];
$dob       = $_POST['dob'];
$address   = $_POST['address'];
$branch    = $_POST['branch'];
$semester  = $_POST['semester'];

// Handle photo upload
$photo = null;
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
    $photo = file_get_contents($_FILES['profile_photo']['tmp_name']);
}

// Prevent duplicate registration by email
$check = $conn->prepare("SELECT 1 FROM application WHERE LOWER(Email) = LOWER(?) LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$checkRes = $check->get_result();
if ($checkRes && $checkRes->num_rows > 0) {
    error_log('[registration] duplicate email=' . $email);
    // Already registered; decide redirect based on apply state
    $checkApply = $conn->prepare("SELECT 1 FROM apply WHERE LOWER(Email) = LOWER(?) LIMIT 1");
    $checkApply->bind_param("s", $email);
    $checkApply->execute();
    $applyRes = $checkApply->get_result();

    if ($applyRes && $applyRes->num_rows > 0) {
        echo "<script>alert('You are already registered and have applied.'); window.location.href='sdashboard.php';</script>";
    } else {
        echo "<script>alert('You are already registered.'); window.location.href='apply.html';</script>";
    }
    $checkApply->close();
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Prepare SQL
$sql = "INSERT INTO application 
        (Fname, Mname, Lname, Email, Phone, Gender, DOB, Address, Branch, Semester, Photo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// Use all strings ("s")
$stmt->bind_param("sssssssssss", 
    $fname, $mname, $lname, $email, $phone, $gender, $dob, $address, $branch, $semester, $photo
);

// Execute
if ($stmt->execute()) {
    error_log('[registration] inserted email=' . $email);
    echo "<script>alert('Registration Successful!'); window.location.href='apply.html';</script>";
} else {
    error_log('[registration] insert_error email=' . $email . ' err=' . $stmt->error);
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
