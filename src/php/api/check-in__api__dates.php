<?php

include_once './check-in/get-data.php';

echo getDateByUnit($_POST["unit_id"]);

$GLOBALS['conn']->close();
