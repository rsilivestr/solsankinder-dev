<?php

include_once './check-in/insert-data.php';

echo deleteEvent($_POST['id']);

$GLOBALS['conn']->close();
