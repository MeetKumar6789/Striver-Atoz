<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "student";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE TABLE IF NOT EXISTS approvals (
  Email VARCHAR(255) PRIMARY KEY,
  Status ENUM('Approved','Rejected') NOT NULL,
  DecidedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS allotments (
  Email VARCHAR(255) PRIMARY KEY,
  RoomNo VARCHAR(50) NOT NULL,
  RoomType VARCHAR(50) NULL,
  Floor VARCHAR(50) NULL,
  CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? $_POST['action'] : '';
  if ($action === 'approve' || $action === 'reject') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if ($email !== '') {
      $status = $action === 'approve' ? 'Approved' : 'Rejected';
      $stmt = $conn->prepare("INSERT INTO approvals (Email, Status) VALUES (?, ?) ON DUPLICATE KEY UPDATE Status=VALUES(Status), DecidedAt=CURRENT_TIMESTAMP");
      $stmt->bind_param('ss', $email, $status);
      $stmt->execute();
      $stmt->close();
    }
  } elseif ($action === 'allot') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $roomNo = isset($_POST['room_no']) ? trim($_POST['room_no']) : '';
    $roomType = isset($_POST['room_type']) ? trim($_POST['room_type']) : null;
    $floor = isset($_POST['floor']) ? trim($_POST['floor']) : null;
    if ($email !== '' && $roomNo !== '') {
      $stmt = $conn->prepare("INSERT INTO allotments (Email, RoomNo, RoomType, Floor) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE RoomNo=VALUES(RoomNo), RoomType=VALUES(RoomType), Floor=VALUES(Floor), CreatedAt=CURRENT_TIMESTAMP");
      $stmt->bind_param('ssss', $email, $roomNo, $roomType, $floor);
      $stmt->execute();
      $stmt->close();
    }
  }
}


$sql = "SELECT a.Fname, a.Email, a.Phone, a.Applyfor, a.Roomtype, a.Preferredfloor, a.Remarks,
               appr.Status AS ApprovalStatus,
               al.RoomNo, al.RoomType AS AllottedRoomType, al.Floor AS AllottedFloor
        FROM apply a
        LEFT JOIN approvals appr ON LOWER(appr.Email) = LOWER(a.Email)
        LEFT JOIN allotments al ON LOWER(al.Email) = LOWER(a.Email)
        ORDER BY a.Fname";

$apps = [];
$res = $conn->query($sql);
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $apps[] = $row;
  }
  $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" href="NG2.png" type="image/png">
  <title>View Applications - Admin Panel</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; color: #0d1b2a; }
    header { background: linear-gradient(to right, #004e92, #000428); padding: 5px 10px; color: white; text-align: center; position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; border-bottom: 3px solid #00c853; }
    header h1 { margin: 0; font-size: 22px; }
    .container { max-width: 900px; margin: 120px auto 40px; padding: 0 20px; }
    .application-box { background: #ffffff; border-left: 6px solid #004e92; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
    .row p { margin: 6px 0; }
    .status { margin-top: 10px; font-weight: bold; }
    .button-group { margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap; }
    .button-group form { display: inline-flex; gap: 8px; align-items: center; }
    .button-group input[type="submit"], .button-group button { background-color: #0072ff; color: white; border: none; padding: 10px 14px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s ease; }
    .button-group input[type="submit"]:hover, .button-group button:hover { background-color: #005ecb; }
    .allot-fields input, .allot-fields select { padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
    .back-btn { display: block; width: fit-content; margin: 30px auto 10px; background-color: #004e92; color: white; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; transition: background 0.3s ease; }
    .back-btn:hover { background-color: #002d63; }
    @media screen and (max-width: 600px) {
      .container { margin: 120px auto 30px; }
      .row { grid-template-columns: 1fr; }
      .button-group { flex-direction: column; align-items: stretch; }
      .button-group form { width: 100%; }
      .button-group input[type="submit"], .button-group button { width: 100%; }
    }
  </style>
  </head>
  <body>
    <header>
      <h1>Student Applications</h1>
    </header>
    <div class="container">
      <?php if (empty($apps)) { ?>
        <div class="application-box"><p>No applications found.</p></div>
      <?php } else { foreach ($apps as $app) { ?>
        <div class="application-box">
          <div class="row">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($app['Fname'] ?? ''); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($app['Email'] ?? ''); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($app['Phone'] ?? ''); ?></p>
            <p><strong>Apply For:</strong> <?php echo htmlspecialchars($app['Applyfor'] ?? ''); ?></p>
            <p><strong>Requested Room:</strong> <?php echo htmlspecialchars($app['Roomtype'] ?? ''); ?></p>
            <p><strong>Preferred Floor:</strong> <?php echo htmlspecialchars($app['Preferredfloor'] ?? ''); ?></p>
            <?php if (!empty($app['Remarks'])) { ?>
              <p style="grid-column: 1 / -1;"><strong>Remarks:</strong> <?php echo htmlspecialchars($app['Remarks']); ?></p>
            <?php } ?>
          </div>
          <div class="status">
            Status: <?php echo $app['ApprovalStatus'] ? htmlspecialchars($app['ApprovalStatus']) : 'Pending'; ?>
            <?php if (!empty($app['RoomNo'])) { ?>
              | Allotted: Room <?php echo htmlspecialchars($app['RoomNo']); ?><?php echo $app['AllottedFloor'] ? ' ('.$app['AllottedFloor'].')' : ''; ?>
            <?php } ?>
          </div>
          <div class="button-group">
            <form method="post">
              <input type="hidden" name="email" value="<?php echo htmlspecialchars($app['Email']); ?>">
              <input type="hidden" name="action" value="approve">
              <input type="submit" value="Approve">
            </form>
            <form method="post">
              <input type="hidden" name="email" value="<?php echo htmlspecialchars($app['Email']); ?>">
              <input type="hidden" name="action" value="reject">
              <input type="submit" value="Reject">
            </form>
            <form method="post" class="allot-fields">
              <input type="hidden" name="email" value="<?php echo htmlspecialchars($app['Email']); ?>">
              <input type="hidden" name="action" value="allot">
              <input type="text" name="room_no" placeholder="Room No" value="<?php echo htmlspecialchars($app['RoomNo'] ?? ''); ?>" required>
              <select name="room_type">
                <option value="">Room Type (optional)</option>
                <option value="AC" <?php echo (isset($app['AllottedRoomType']) && $app['AllottedRoomType']==='AC') ? 'selected' : ''; ?>>AC</option>
                <option value="Non-AC" <?php echo (isset($app['AllottedRoomType']) && $app['AllottedRoomType']==='Non-AC') ? 'selected' : ''; ?>>Non-AC</option>
              </select>
              <input type="text" name="floor" placeholder="Floor (optional)" value="<?php echo htmlspecialchars($app['AllottedFloor'] ?? ''); ?>">
              <button type="submit">Allot Room</button>
            </form>
          </div>
        </div>
      <?php }} ?>
      <a class="back-btn" href="/ng/adashboard.html">Back to Dashboard</a>
    </div>
  </body>
  </html>
<?php $conn->close(); ?>