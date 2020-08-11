<?php

include_once('../modules/SolCheckIn/conn.php');
include_once('../modules/SolCheckIn/date_time.php');

$dates = getAvailDates($conn);

$res = json_encode($dates);

echo $res;

$conn->close();
