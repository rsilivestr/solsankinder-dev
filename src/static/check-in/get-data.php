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

  $sql = "SELECT ci_dates.id, ci_dates.ci_date FROM ci_dates
    INNER JOIN units
    ON units.date_id = ci_dates.id
    WHERE units.id = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("i", $unit_id);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_row();
  $stmt->close();

  return json_encode($res);
}

function getIntervals() {
  // get all intervals
  $sql = "SELECT id, start_time, end_time FROM ci_intervals";
  $res = $GLOBALS['conn']->query($sql)->fetch_all();

  return json_encode($res);
}

function getIntervalsByDateId($date_id = NULL) {
  if (NULL === $date_id) {
    return;
  }

  $sql = "SELECT ci_intervals.id, ci_intervals.start_time, ci_intervals.end_time
    FROM ci_intervals
    INNER JOIN ci_spots
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
  $sql = "SELECT id, unit_name
    FROM units
    WHERE date_id > 0";

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

function getEvents($date, $interval_id) {
  $sql = "SELECT ci_events.event_id,
      patients.fio,
      patients.phone,
      patients.dob,
      ci_intervals.start_time,
      ci_intervals.end_time,
      units.unit_name,
      districts.district_name
    FROM ci_events
    INNER JOIN patients ON patients.id = ci_events.patient_id
    INNER JOIN ci_dates ON ci_dates.id = ci_events.date_id
    INNER JOIN ci_intervals ON ci_intervals.id = ci_events.interval_id
    INNER JOIN units ON units.id = ci_events.unit_id
    INNER JOIN clinics ON clinics.id = ci_events.clinic_id
    INNER JOIN districts ON districts.id = clinics.district_id
    WHERE ci_dates.ci_date=?";

  if (0 !== $interval_id) {
    // Add interval condition
    $sql .= " AND ci_intervals.id=?";
  }

  $stmt = $GLOBALS['conn']->prepare($sql);

  // Bind parameters as needed
  if (0 !== $interval_id) {
    $stmt->bind_param("si", $date, $interval_id);
  } else {
    $stmt->bind_param("s", $date);
  }

  $stmt->execute();
  $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  return json_encode($res);
}
