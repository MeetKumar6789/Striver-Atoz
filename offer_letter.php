<?php
session_start();

// Ensure student is logged in
if (!isset($_SESSION['email'])) {
  header('Location: slogin.html');
  exit();
}

$email = strtolower(trim($_SESSION['email']));

// DB connection
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'student';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

// Fetch approval + allotment + application/registration details
$sql = "SELECT 
          COALESCE(r.Fname, a.Fname) AS FirstName,
          r.Mname AS MiddleName,
          r.Lname AS LastName,
          COALESCE(r.Email, a.Email) AS Email,
          COALESCE(r.Phone, a.Phone) AS Phone,
          a.Applyfor,
          a.Roomtype AS RequestedRoomType,
          a.Preferredfloor AS PreferredFloor,
          appr.Status AS ApprovalStatus,
          al.RoomNo, al.RoomType AS AllottedRoomType, al.Floor AS AllottedFloor,
          r.Photo AS PhotoBlob
        FROM apply a
        LEFT JOIN application r ON TRIM(LOWER(r.Email)) = TRIM(LOWER(a.Email))
        LEFT JOIN approvals appr ON TRIM(LOWER(appr.Email)) = TRIM(LOWER(a.Email))
        LEFT JOIN allotments al ON TRIM(LOWER(al.Email)) = TRIM(LOWER(a.Email))
        WHERE TRIM(LOWER(a.Email)) = TRIM(LOWER(?))
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  $conn->close();
  die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!$row) {
  header('Content-Type: text/plain');
  echo 'No application found.';
  exit();
}

if (strtoupper((string)$row['ApprovalStatus']) !== 'APPROVED') {
  header('Content-Type: text/plain');
  echo 'Your application is not approved yet. PDF will be available after approval.';
  exit();
}

// Lazy dependency check for FPDF
if (!file_exists(__DIR__ . '/fpdf.php')) {
  header('Content-Type: text/html; charset=UTF-8');
  echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>PDF Error</title></head><body>';
  echo '<p>PDF generator missing. Please place <code>fpdf.php</code> (FPDF library) in the <code>/ng</code> folder.</p>';
  echo '<p>Download FPDF from <a href="https://www.fpdf.org/" target="_blank" rel="noopener noreferrer">fpdf.org</a>.</p>';
  echo '</body></html>';
  exit();
}

require __DIR__ . '/fpdf.php';

// Prepare data
$studentName = trim(($row['FirstName'] ?? '') . ' ' . ($row['MiddleName'] ?? '') . ' ' . ($row['LastName'] ?? ''));
$studentName = $studentName !== '' ? $studentName : 'Student';
$emailSafe = $row['Email'] ?? '';
$phoneSafe = $row['Phone'] ?? '';
$applyFor = $row['Applyfor'] ?? 'Hostel Accommodation';
$roomNo = $row['RoomNo'] ?? '';
$allottedType = $row['AllottedRoomType'] ?? '';
$allottedFloor = $row['AllottedFloor'] ?? '';
$today = date('d M Y');
$refNo = 'NG-' . date('Ymd') . '-' . substr(md5($email . $today), 0, 6);

// Extend FPDF to add a professional header/footer
class PDF extends FPDF {
  function Header() {
    $logoPath = __DIR__ . '/NG.png';
    $hasLogo = file_exists($logoPath);
    // Header background bar
    $this->SetFillColor(0, 78, 146);
    $this->Rect(0, 0, 210, 25, 'F');
    if ($hasLogo) {
      $this->Image($logoPath, 10, 5, 15, 15);
    }
    $this->SetTextColor(255, 255, 255);
    $this->SetFont('Arial', 'B', 16);
    $this->SetXY(0, 6);
    $this->Cell(0, 8, 'NivasGruh Hostel', 0, 1, 'C');
    $this->SetFont('Arial', '', 10);
    $this->Cell(0, 6, 'Near CHARUSAT University, Changa, Gujarat - 388421, India', 0, 1, 'C');
    $this->Ln(6);
  }
  function Footer() {
    $this->SetY(-20);
    $this->SetDrawColor(220, 220, 220);
    $this->Line(10, $this->GetY(), 200, $this->GetY());
    $this->SetY(-18);
    $this->SetTextColor(100, 100, 100);
    $this->SetFont('Arial', '', 9);
    $this->Cell(0, 5, 'Phone: +91 9574485195  |  Email: meet.nakarani@outlook.com', 0, 1, 'C');
    $this->Cell(0, 5, 'This is a system-generated document. No signature required.', 0, 1, 'C');
    $this->SetY(-10);
    $this->Cell(0, 5, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);

// Title & Ref no
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Room Allotment Letter', 0, 1, 'C');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Reference No: ' . $refNo, 0, 1, 'C');
$pdf->Ln(2);

// Track photo bottom to avoid overlap
$photoBottom = null;

// Student photo (top-right)
if (!empty($row['PhotoBlob'])) {
  $photoData = $row['PhotoBlob'];
  $ext = 'jpg';
  if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_buffer($finfo, $photoData) : null;
    if ($finfo) { finfo_close($finfo); }
    if ($mime === 'image/png') { $ext = 'png'; }
    elseif ($mime === 'image/jpeg' || $mime === 'image/jpg') { $ext = 'jpg'; }
  }
  $tmpPhoto = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ng_photo_' . uniqid('', true) . '.' . $ext;
  if (@file_put_contents($tmpPhoto, $photoData) !== false) {
    // Place image near the top-right; reserve fixed height to avoid overlap
    $photoY = 33; // just below header
    $photoW = 25; $photoH = 30;
    $pdf->Image($tmpPhoto, 175, $photoY, $photoW, $photoH);
    $photoBottom = $photoY + $photoH;
    register_shutdown_function(function() use ($tmpPhoto) { @unlink($tmpPhoto); });
  }
}

// Meta row (date) – keep away from photo area
$pdf->SetFont('Arial', '', 12);
if (!empty($photoBottom)) {
  // Place date on the left to avoid overlapping the fixed photo area
  $pdf->Cell(0, 7, 'Date: ' . $today, 0, 1, 'L');
} else {
  $pdf->Cell(0, 7, 'Date: ' . $today, 0, 1, 'R');
}
$pdf->Ln(1);

// Recipient block – ensure starting below photo
$pdf->SetFont('Arial', '', 12);
$startY = $pdf->GetY();
if (!empty($photoBottom) && $startY < $photoBottom) {
  $pdf->SetY($photoBottom + 2);
}
$pdf->Cell(0, 7, 'To,', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, $studentName, 0, 1);
$pdf->SetFont('Arial', '', 12);
if ($emailSafe !== '') { $pdf->Cell(0, 7, $emailSafe, 0, 1); }
if ($phoneSafe !== '') { $pdf->Cell(0, 7, 'Phone: ' . $phoneSafe, 0, 1); }
$pdf->Ln(2);

// Intro paragraph
$intro = "Congratulations! We are pleased to inform you that your application for NivasGruh Hostel has been approved. Please find your room allotment details below.";
$pdf->MultiCell(0, 7, $intro);
$pdf->Ln(2);

// Allotment detail table
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(233, 245, 255);
$pdf->SetDrawColor(0, 78, 146);
$pdf->SetLineWidth(0.3);
$pdf->Cell(0, 8, 'Room Allotment Details', 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 12);

// Two-column cells
function rowPair($pdf, $labelLeft, $valueLeft, $labelRight, $valueRight) {
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(45, 8, $labelLeft, 1, 0, 'L');
  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(55, 8, $valueLeft, 1, 0, 'L');
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(45, 8, $labelRight, 1, 0, 'L');
  $pdf->SetFont('Arial', '', 11);
  $pdf->Cell(45, 8, $valueRight, 1, 1, 'L');
}

rowPair($pdf, 'Room Number', ($roomNo !== '' ? $roomNo : 'TBD'), 'Room Type', ($allottedType !== '' ? $allottedType : 'TBD'));
rowPair($pdf, 'Floor', ($allottedFloor !== '' ? $allottedFloor : 'TBD'), 'Approved For', $applyFor);
$pdf->Ln(2);

// Additional guidance
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Reporting Instructions', 0, 1);
$pdf->SetFont('Arial', '', 11);
$guidance = [
  'Report to the hostel office with this letter and a valid photo ID.',
  'Complete fee formalities as instructed by the office.',
  'Room handover will be provided upon verification of documents.'
];
foreach ($guidance as $g) { $pdf->MultiCell(0, 6, '- ' . $g); }
$pdf->Ln(2);

// Notes
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Important Notes', 0, 1);
$pdf->SetFont('Arial', '', 11);
$notes = [
  'Carry a printed or digital copy of this offer letter during check-in.',
  'Adhere to hostel rules and maintain decorum inside premises.',
  'Any discrepancy in documents may lead to cancellation of allotment.',
  'For assistance, contact the hostel office at the details below.'
];
foreach ($notes as $n) { $pdf->MultiCell(0, 6, '- ' . $n); }
$pdf->Ln(4);

// Closing
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 7, 'Regards,');
$pdf->MultiCell(0, 7, 'Warden, Nivasgruh Hostel');

// Output download
$safeFile = 'Offer_Letter_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $studentName) . '_' . date('Ymd') . '.pdf';
$pdf->Output('D', $safeFile);
exit();
?>



