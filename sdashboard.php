<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: slogin.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = strtolower(trim($_SESSION['email']));

$stmt = $conn->prepare("SELECT a.Fname AS ApplyFname, a.Email, a.Phone, a.Applyfor, a.Roomtype, a.Preferredfloor, a.Remarks,
                               r.Fname AS RegFname, r.Mname, r.Lname, r.Gender, r.DOB, r.Address, r.Branch, r.Semester, r.Photo
                        FROM apply a
                        LEFT JOIN application r ON TRIM(LOWER(r.Email)) = TRIM(LOWER(a.Email))
                        WHERE TRIM(LOWER(a.Email)) = TRIM(LOWER(?))
                        LIMIT 1");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fallback: if registration data missing, try to fetch directly by session email
if ($student && (empty($student['RegFname']) && empty($student['Gender']) && empty($student['DOB']) && empty($student['Branch']) && empty($student['Semester']))) {
    $stmt2 = $conn->prepare("SELECT Fname AS RegFname, Mname, Lname, Gender, DOB, Address, Branch, Semester, Photo FROM application WHERE TRIM(LOWER(Email)) = TRIM(LOWER(?)) LIMIT 1");
    if ($stmt2) {
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $reg = $res2 ? $res2->fetch_assoc() : null;
        $stmt2->close();
        if ($reg) {
            foreach ($reg as $k => $v) {
                if (!isset($student[$k]) || $student[$k] === null) {
                    $student[$k] = $v;
                }
            }
        }
    }
}

$stmt->close();
$approvalStatus = null;
$roomNo = null;
$allottedRoomType = null;
$allottedFloor = null;

// Fetch approval status
$stmt3 = $conn->prepare("SELECT Status FROM approvals WHERE TRIM(LOWER(Email)) = TRIM(LOWER(?)) LIMIT 1");
if ($stmt3) {
    $stmt3->bind_param("s", $email);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    $row3 = $res3 ? $res3->fetch_assoc() : null;
    if ($row3) {
        $approvalStatus = $row3['Status'];
    }
    $stmt3->close();
}

// Fetch allotment details
$stmt4 = $conn->prepare("SELECT RoomNo, RoomType, Floor FROM allotments WHERE TRIM(LOWER(Email)) = TRIM(LOWER(?)) LIMIT 1");
if ($stmt4) {
    $stmt4->bind_param("s", $email);
    $stmt4->execute();
    $res4 = $stmt4->get_result();
    $row4 = $res4 ? $res4->fetch_assoc() : null;
    if ($row4) {
        $roomNo = $row4['RoomNo'];
        $allottedRoomType = $row4['RoomType'];
        $allottedFloor = $row4['Floor'];
    }
    $stmt4->close();
}

$student['ApprovalStatus'] = $approvalStatus;
$student['RoomNo'] = $roomNo;
$student['AllottedRoomType'] = $allottedRoomType;
$student['AllottedFloor'] = $allottedFloor;
$conn->close();

// If no student record found, show a message (or redirect)
if (!$student) {
    // You can redirect instead: header("Location: somepage.php");
    echo "<p>No application found for the logged-in user.</p>";
    exit();
}

// For debugging: uncomment the line below to see returned column names/values
// echo '<pre>'; print_r($student); echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard - NivasGruh</title>
  <style>
    /* (your existing styles here) */
    body { margin:0; font-family:Arial, sans-serif; background:#f5f5f5; color:#0d1b2a; }
    header { background: linear-gradient(to right,#004e92,#000428); color:#fff; padding:20px; border-bottom:3px solid #00c853; display:flex; justify-content:space-between; align-items:center; }
    .logout-btn { background:#ff3d00; color:#fff; padding:8px 15px; border-radius:6px; text-decoration:none; font-weight:bold; transition:.3s; }
    .logout-btn:hover{ background:#d32f2f; }
    .dashboard-container{ padding:40px 30px 20px; max-width:900px; margin:auto; }
    .dashboard-container h2 { color:#004e92; margin-bottom:20px; }
    .info-box { background:#e9f5ff; border-left:5px solid #004e92; padding:15px 20px; margin-bottom:20px; border-radius:8px; }
    .info-box h4 { margin:0 0 8px; font-size:16px; color:#000428; }
    .info-box p { margin:0; font-size:15px; color:#333; }
  </style>
</head>
<body>

  <header>
    <h1>Student Dashboard</h1>
    <a href="logout.php" class="logout-btn">🚪 Logout</a>
  </header>

<div class="dashboard-container">
  <h2>Welcome, <?php echo htmlspecialchars(($student['RegFname'] ?? $student['ApplyFname']) ?? 'Student'); ?> 🎉</h2>

  <?php if (!empty($student['Photo'])): ?>
    <div class="info-box">
      <h4>Profile Photo</h4>
      <p>
        <?php
          $imgData = base64_encode($student['Photo']);
          echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="Profile Photo" style="max-width:150px;border-radius:8px;" />';
        ?>
      </p>
    </div>
  <?php endif; ?>

  <div class="info-box">
    <h4>Personal Information</h4>
    <p>
      Email: <?php echo htmlspecialchars($student['Email'] ?? 'N/A'); ?><br>
      Phone: <?php echo htmlspecialchars($student['Phone'] ?? 'N/A'); ?><br>
      Name: <?php echo htmlspecialchars(trim(($student['RegFname'] ?? '') . ' ' . ($student['Mname'] ?? '') . ' ' . ($student['Lname'] ?? '')) ?: 'N/A'); ?><br>
      Gender: <?php echo htmlspecialchars($student['Gender'] ?? 'N/A'); ?><br>
      DOB: <?php echo htmlspecialchars($student['DOB'] ?? 'N/A'); ?><br>
      Address: <?php echo htmlspecialchars($student['Address'] ?? 'N/A'); ?><br>
      Branch: <?php echo htmlspecialchars($student['Branch'] ?? 'N/A'); ?><br>
      Semester: <?php echo htmlspecialchars($student['Semester'] ?? 'N/A'); ?><br>
    </p>
  </div>

  <div class="info-box">
    <h4>Application Details</h4>
    <p>
      Apply For: <?php echo htmlspecialchars($student['Applyfor'] ?? 'N/A'); ?><br>
      Room Type: <?php echo htmlspecialchars($student['Roomtype'] ?? 'N/A'); ?><br>
      Preferred Floor: <?php echo htmlspecialchars($student['Preferredfloor'] ?? 'N/A'); ?><br>
      Status: <?php echo htmlspecialchars($student['ApprovalStatus'] ?? 'Pending'); ?><br>
      <?php if (!empty($student['RoomNo'])): ?>
        Allotted Room: <?php echo htmlspecialchars($student['RoomNo']); ?><?php echo !empty($student['AllottedRoomType']) ? ' (' . htmlspecialchars($student['AllottedRoomType']) . ')' : ''; ?><?php echo !empty($student['AllottedFloor']) ? ', ' . htmlspecialchars($student['AllottedFloor']) : ''; ?><br>
      <?php endif; ?>
      <?php if (strtoupper((string)($student['ApprovalStatus'] ?? '')) === 'APPROVED'): ?>
        <br>
        <a href="offer_letter.php" style="display:inline-block;background:#0072ff;color:#fff;padding:10px 16px;border-radius:6px;text-decoration:none;font-weight:bold;">Download Approval Letter (PDF)</a>
      <?php endif; ?>
    </p>
  </div>
</div>


</body>
</html>
