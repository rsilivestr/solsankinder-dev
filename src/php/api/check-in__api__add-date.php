<?php

include_once('../modules/SolCheckIn/conn.php');
include_once('../modules/SolCheckIn/table_actions.php');

if (isset($_POST['newDate'])) {
  $date = $_POST['newDate'];

  if (date_parse($date)['error_count'] == 0) {
    $res = addCheckInDate($conn, $date);

    echo $res;
  }
}

$conn->close();
