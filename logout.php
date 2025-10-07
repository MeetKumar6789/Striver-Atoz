<?php
session_start();
session_unset();
session_destroy();
header("Location: slogin.html"); // Redirect to login after logout
exit();
?>
