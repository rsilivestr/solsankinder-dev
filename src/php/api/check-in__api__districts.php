<?php

include_once './check-in/get-data.php';

echo getDistricts();

$GLOBALS['conn']->close();
