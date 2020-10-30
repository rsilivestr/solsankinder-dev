<?php

include_once './check-in/get-data.php';

$date = (new DateTime($_POST['ci_date']))->format('Y-m-d');

echo getEvents($date, $_POST['interval_id']);

$GLOBALS['conn']->close();
