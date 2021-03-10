<?php

include_once './check-in/get-data.php';

if (isset($_GET['all'])) {
  echo getUnits();
}

if (isset($_GET['active'])) {
  echo getActiveUnits();
}

$GLOBALS['conn']->close();
