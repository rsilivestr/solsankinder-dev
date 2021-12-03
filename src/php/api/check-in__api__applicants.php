<?php

include_once './check-in/get-data.php';

echo getApplicants();

$GLOBALS['conn']->close();
