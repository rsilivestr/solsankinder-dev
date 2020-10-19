<?php

include_once './check-in/get-data.php';

echo getClinics($_POST["district_id"]);

$GLOBALS['conn']->close();
