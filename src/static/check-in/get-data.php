<?php

include_once "conn.php";

function makeArr($queryRes) {
  $rows = $queryRes->fetch_all();

  $res = [];
  foreach ($rows as $row) {
    array_push($res, $row[0]);
  }

  return $res;
}

function getColumn($tableName, $colName, $condition = null) {
  $sql = "SELECT id, $colName FROM $tableName";
  if ($condition) {
    $sql .= " WHERE $condition";
  }

  $res = $GLOBALS['conn']->query($sql)->fetch_all();

  $res = array_map(function ($val) {
    return [$val[0], $val[1]];
  }, $res);

  return json_encode($res);
}

function getAllDates() {
  return getColumn("ci_dates", "ci_date");
}

function getDates() {
  return getColumn("ci_dates", "ci_date", "is_active=1");
}

function getDateByUnit($unit_id = NULL) {
  if (NULL === $unit_id) {
    return;
  }

  $sql = "SELECT id, ci_date FROM ci_dates WHERE is_active=1 AND unit_id=?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("i", $unit_id);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_row();
  $stmt->close();

  return json_encode($res);
}

function getIntervals($date) {
  $sql = "SELECT id FROM ci_dates WHERE ci_date=?";

  if (!$stmt = $GLOBALS['conn']->prepare($sql)) {
    return '{ "status": "error", "message": "Error while preparing SQL statement." }';
  } else {
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    $dateID = $res->fetch_row()[0];

    $sql = "SELECT ci_intervals.ci_interval
      FROM ci_spots
      INNER JOIN ci_intervals
      ON ci_spots.interval_id = ci_intervals.id
      WHERE ci_spots.date_id=$dateID
      AND ci_spots.available>0;";

    $res = $GLOBALS['conn']->query($sql)->fetch_all();

    $res = array_map(function ($val) {
      return $val[0];
    }, $res);

    return json_encode($res);
  }
}

function getIntervalsByDateId($date_id = NULL) {
  if (NULL === $date_id) {
    return;
  }

  $sql = "SELECT ci_spots.id, ci_intervals.start_time, ci_intervals.end_time
    FROM ci_spots
    INNER JOIN ci_intervals
    ON ci_spots.interval_id = ci_intervals.id
    WHERE ci_spots.date_id = ?
    AND ci_spots.available > 0;";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("i", $date_id);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_all();
  $stmt->close();

  $res = array_map(function ($val) {
    return [$val[0], (substr($val[1], 0, 5) . " - " . substr($val[2], 0, 5))];
  }, $res);

  return json_encode($res);
}

function getUnits() {
  return getColumn("units", "unit_name");
}

function getActiveUnits() {
  $sql = "SELECT units.id, units.unit_name
    FROM units
    INNER JOIN ci_dates
    ON units.id = ci_dates.unit_id
    WHERE ci_dates.is_active=1";

  $res = $GLOBALS['conn']->query($sql)->fetch_all();

  return json_encode($res);
}

function getDistricts() {
  return getColumn("districts", "district_name");
}

function getClinics($districtId = NULL) {
  if (NULL === $districtId) {
    return;
  }

  $sql = "SELECT id, clinic_name FROM clinics WHERE district_id=?";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("i", $districtId);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_all();
  $stmt->close();

  return json_encode($res);
}
