<?php

include_once './check-in/get-data.php';

$date = (new DateTime($_POST['ci_date']))->format('Y-m-d');

echo getEvents($date);

$GLOBALS['conn']->close();
