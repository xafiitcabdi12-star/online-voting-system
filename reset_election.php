<?php
include "includes/session_check.php";
include "config/db_connect.php";

if ($_SESSION['user_type'] !== 'admin') {
  header("Location: user_dashboard.php");
  exit();
}

if (!isset($_POST['confirm'])) {
  header("Location: admin_dashboard.php");
  exit();
}

// delete votes
mysqli_query($conn, "DELETE FROM votes");

// open voting again
mysqli_query($conn, "UPDATE settings SET value='1' WHERE `key`='voting_open'");

header("Location: admin_dashboard.php");
exit();
