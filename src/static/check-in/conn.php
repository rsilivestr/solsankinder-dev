<?php

$cred = json_decode(file_get_contents('json/.cred.json'), true);

$GLOBALS['conn'] = new mysqli(
  $cred['host'],
  $cred['username'],
  $cred['password'],
  $cred['database'],
);

$GLOBALS['conn']->set_charset("utf8");

if ($GLOBALS['conn']->connect_error) {
  die("Connection failed: " . $GLOBALS['conn']->connect_error);
}
