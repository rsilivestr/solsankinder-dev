<?php

include_once('../modules/SolCheckIn/conn.php');
include_once('../modules/SolCheckIn/date_time.php');

if (!isset($_POST['chosenDate'])) {
  echo '{"error": "date is not set"}';
} else {
  $date = $_POST['chosenDate'];

  $hours = getAvailHours($conn, $date);

  echo json_encode($hours);
}

$conn->close();
