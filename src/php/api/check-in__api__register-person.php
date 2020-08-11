<?php

include_once('../modules/SolCheckIn/conn.php');
include_once('../modules/SolCheckIn/table_actions.php');

$missingData = [];

if (!isset($_POST["fio"])) $missingData[] = "fio";
if (!isset($_POST["age"])) $missingData[] = "age";
if (!isset($_POST["tel"])) $missingData[] = "tel";
if (!isset($_POST["date"])) $missingData[] = "date";
if (!isset($_POST["hours"])) $missingData[] = "hours";
if (!isset($_POST["unit"])) $missingData[] = "unit";
if (!isset($_POST["district"])) $missingData[] = "district";
if (!isset($_POST["clinic"])) $missingData[] = "clinic";

if (sizeof($missingData) > 0) {
  echo '{
    "status": "error",
    "message": "Missing person data in request: ' . implode($missingData, ", ") . '
  }';
} else {
  $person_data = [
    "fio" => $_POST["fio"],
    "age" => $_POST["age"],
    "tel" => $_POST["tel"],
    "unit" => $_POST["unit"],
    "district" => $_POST["district"],
    "clinic" => $_POST["clinic"],
    "checkin_date" => $_POST["date"],
    "checkin_time" => $_POST["hours"]
  ];

  $res = registerPerson($conn, $person_data);

  echo $res;
}

$conn->close();
