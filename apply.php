<?php
$servername = "localhost";
$username = "root";   
$password = "";       
$dbname = "student";  

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$fullname   = $_POST['full_name'];
$email      = $_POST['email'];
$phone      = $_POST['phone'];
$apply_for  = $_POST['apply_for'];
$room_type  = $_POST['room_type'];
$floor      = $_POST['floor'];
$remarks    = $_POST['remarks'];


$stmt = $conn->prepare("INSERT INTO apply (Fname, Email, Phone, Applyfor, Roomtype, Preferredfloor, Remarks) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $fullname, $email, $phone, $apply_for, $room_type, $floor, $remarks);

if ($stmt->execute()) {
    echo "<script>
            alert('Application submitted successfully!');
            window.location.href = 'sdashboard.php'; // change if your dashboard file name is different
          </script>";
} else {
    echo "<script>
            alert('Error submitting application: " . $stmt->error . "');
            window.history.back();
          </script>";
}

$stmt->close();
$conn->close();
?>
