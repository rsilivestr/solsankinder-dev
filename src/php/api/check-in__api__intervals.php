<?php

include_once './check-in/get-data.php';

echo getIntervalsByDateId($_POST["date_id"]);

$GLOBALS['conn']->close();
