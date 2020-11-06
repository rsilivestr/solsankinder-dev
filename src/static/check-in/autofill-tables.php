<?php

require "conn.php";

$json = file_get_contents('json/districts.json');
$GLOBALS['assoc'] = json_decode($json, TRUE);
$GLOBALS['districts'] = array_keys($GLOBALS['assoc']);

function fillDistrictsTable() {
  foreach ($GLOBALS['districts'] as $district) {
    $sql = "SELECT * FROM districts
      WHERE district_name='$district'";

    $res = $GLOBALS['conn']->query($sql);

    if (!$res->num_rows > 0) {
      $sql = "INSERT INTO districts (district_name) VALUES ('$district')";

      $GLOBALS['conn']->query($sql);
    }
  }
}

function fillClinicsTable() {
  foreach ($GLOBALS['districts'] as $district) {
    $sql = "SELECT id FROM districts
      WHERE district_name='$district'";

    if ($res = $GLOBALS['conn']->query($sql)) {
      $id = $res->fetch_assoc()['id'];

      foreach ($GLOBALS['assoc']["$district"] as $clinic) {
        $sql = "INSERT INTO clinics
          (clinic_name, district_id)
          VALUES ('$clinic', '$id')";

        $GLOBALS['conn']->query($sql);
      }
    }
  }
}

function fillUnitsTable() {
  $units = [
    "1 отделение, гастроэнтерология",
    "2 отделение, психоневрология",
    "3 отделение, нефрология",
    "4 отделение, \"Мать и дитя\"",
    "4 отделение, пульмонология",
    "5 отделение, онкология",
    "6 отделение, п. Ушково, пульмонология",
  ];

  foreach ($units as $unit) {
    $sql = "SELECT id FROM units WHERE unit_name = '$unit'";

    // $sql = "SELECT id FROM units WHERE unit_name = ?";
    // $stmt = !$GLOBALS['conn']->prepare($sql);
    // $stmt->bind_param('s', $unit);
    // $stmt->execute();
    // $res = $stmt->get_result();
    // $stmt->close();

    if (!$GLOBALS['conn']->query($sql)->num_rows > 0) {
      $sql = "INSERT INTO units (unit_name) VALUES ('$unit')";
      $GLOBALS['conn']->query($sql);
    }
  }
}

function fillIntervalsTable() {
  $intervals = [
    0 => ['09:00', '11:00'],
    1 => ['11:00', '13:00'],
    2 => ['13:00', '15:00'],
    3 => ['15:00', '16:00'],
  ];

  foreach ($intervals as $int) {
    $sql = "SELECT start_time FROM ci_intervals
      WHERE start_time='$int[0]'";

    $res = $GLOBALS['conn']->query($sql);

    if (!$res->num_rows > 0) {
      $sql = "INSERT INTO ci_intervals
        (start_time, end_time, is_active)
        VALUES ('$int[0]', '$int[1]', 1)";

      $GLOBALS['conn']->query($sql);
    }
  }
}
