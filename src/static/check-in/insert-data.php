<?php

include_once 'conn.php';
include_once 'validate-data.php';
include_once 'make-pdf.php';

function insertSpots($date, $maxSpotsArray)
{
  $sql = 'SELECT id FROM ci_dates WHERE ci_date = ?';
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('s', $date);
  $stmt->execute();
  $resDateId = $stmt->get_result();
  $stmt->close();

  $dateId = $resDateId->fetch_row()[0];

  $sql = 'SELECT id FROM ci_intervals WHERE is_active = 1';
  $resIntervals = $GLOBALS['conn']->query($sql)->fetch_all(MYSQLI_NUM);

  $intervalIds = [];
  foreach ($resIntervals as $row) {
    array_push($intervalIds, $row[0]);
  }

  foreach ($intervalIds as $index => $ivlId) {
    $sql = "SELECT id FROM ci_spots
      WHERE date_id = ?
      AND interval_id = ?";

    $stmt = $GLOBALS['conn']->prepare($sql);
    $stmt->bind_param('ii', $dateId, $ivlId);
    $stmt->execute();
    $match = $stmt->get_result()->num_rows;
    $stmt->close();

    if (0 === $match) {
      $sql = "INSERT INTO ci_spots
        (date_id, interval_id, total, available)
        VALUES (?, ?, ?, ?)";
      $stmt = $GLOBALS['conn']->prepare($sql);
      $stmt->bind_param('iiii', $dateId, $ivlId, $maxSpotsArray[$index], $maxSpotsArray[$index]);
      $stmt->execute();
      $stmt->close();
    }
  }
}

function checkUnitActive($unitId)
{
  $sql = 'SELECT id FROM units WHERE id = ? AND NOT date_id = 0';
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('i', $unitId);
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  return $res->num_rows > 0;
}

function makeUnitActive($dateId, $unitId)
{
  $sql = 'UPDATE units SET date_id = ? WHERE id = ?';
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('ii', $dateId, $unitId);
  $stmt->execute();
  $stmt->close();
}

function makeUnitInactive($id)
{
  $sql = 'UPDATE units SET date_id = 0 WHERE id = ?';
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();
}

function insertDate($newDate, $unitIdsArray, $maxSpotsArray)
{
  $sql = 'SELECT id FROM ci_dates WHERE ci_date = ?';
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('s', $newDate);
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  if ($res->num_rows > 0) {
    return '{ "status": "info", "message": "That date exists." }';
  }

  foreach ($unitIdsArray as $unitId) {
    $isActive = checkUnitActive($unitId);

    if ($isActive) {
      return '{ "status": "info", "message": "Unit already in use." }';
    }
  }

  $sql = "INSERT INTO ci_dates
    (ci_date, unit_ids, is_active)
    VALUES (?, ?, 1)";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('ss', $newDate, implode(',', $unitIdsArray));
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  $dateId = $GLOBALS['conn']->query('SELECT LAST_INSERT_ID()')->fetch_row()[0];
  foreach ($unitIdsArray as $unitId) {
    makeUnitActive($dateId, $unitId);
  }

  insertSpots($newDate, $maxSpotsArray);

  return '{ "status": "success", "message": "Date was added." }';
}

function closeDate($date)
{
  $formattedDate = (new DateTime($date))->format('Y-m-d');

  $sql = "UPDATE ci_dates
    SET is_active = 0
    WHERE ci_date = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('s', $formattedDate);
  $stmt->execute();
  $res = $stmt->affected_rows;
  $stmt->close();

  if (1 === $res) {
    $sql = 'SELECT unit_ids FROM ci_dates WHERE ci_date = ?';
    $stmt = $GLOBALS['conn']->prepare($sql);
    $stmt->bind_param('s', $formattedDate);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_row();
    $stmt->close();

    $unitIds = explode(',', $res[0]);
    foreach ($unitIds as $unitId) {
      makeUnitInactive($unitId);
    }

    return true;
  } else {
    return false;
  }
}

function insertPatient($fio = null, $phone = null, $dob = null)
{
  $vFio = validateFio($fio);
  $vPhone = validatePhone($phone);
  $vDob = validateDob($dob, 1, 17);

  if (null === $vFio) {
    return -2;
  }
  if (null === $vPhone) {
    return -3;
  }
  if (null === $vDob) {
    return -4;
  }

  $sql = "SELECT id FROM patients
    WHERE LOWER(fio) = LOWER(?) AND dob = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('ss', $vFio, $vDob);
  $stmt->execute();

  $res = $stmt->get_result();
  $id_existing = $res->fetch_row()[0];

  $stmt->close();

  if (!$res->num_rows > 0) {
    $sql = 'INSERT INTO patients (fio, phone, dob) VALUES (?, ?, ?)';

    $stmt = $GLOBALS['conn']->prepare($sql);
    $stmt->bind_param('sss', $vFio, $vPhone, $vDob);
    $stmt->execute();
    $stmt->close();
    // return last inserted id
    $id_inserted = $GLOBALS['conn']->query('SELECT LAST_INSERT_ID()')->fetch_row()[0];
    return $id_inserted;
  }
  // return existing patient id
  return $id_existing;
}

function getEventData($id = null)
{
  if (!$id) {
    return;
  }
  $sql = "SELECT ci_events.event_id,
      patients.fio,
      ci_dates.ci_date,
      ci_intervals.start_time,
      ci_intervals.end_time
    FROM ci_events
    INNER JOIN patients ON patients.id = ci_events.patient_id
    INNER JOIN ci_dates ON ci_dates.id = ci_events.date_id
    INNER JOIN ci_intervals ON ci_intervals.id = ci_events.interval_id
    WHERE ci_events.event_id = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $person_data = $res->fetch_assoc();
  $stmt->close();

  return $person_data;
}

function fillSpot($date_id, $interval_id)
{
  $sql = "UPDATE ci_spots
  SET occupied = occupied + 1,
      available = available - 1
  WHERE date_id = ?
  AND interval_id = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('ii', $date_id, $interval_id);
  $stmt->execute();
  $stmt->close();
}

function insertEvent($eventData = null)
{
  if (null == $eventData) {
    return '{ "status": "error", "message": "Ошибка отправки запроса" }';
  } elseif (0 == $eventData['patient_id']) {
    return '{ "status": "error", "message": "Ошибка в id пациента" }';
  } elseif (0 == $eventData['unit_id']) {
    return '{ "status": "error", "message": "Ошибка в id отделения" }';
  } elseif (0 == $eventData['date_id']) {
    return '{ "status": "error", "message": "Ошибка в id даты" }';
  } elseif (0 == $eventData['interval_id']) {
    return '{ "status": "error", "message": "Ошибка в id интервала" }';
  } elseif (0 == $eventData['district_id']) {
    return '{ "status": "error", "message": "Ошибка в id района" }';
  } elseif (0 == $eventData['clinic_id']) {
    return '{ "status": "error", "message": "Ошибка в id клиники" }';
  }

  $sql = 'SELECT event_id FROM ci_events WHERE patient_id = ? AND date_id = ?';

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('ii', $eventData['patient_id'], $eventData['date_id']);
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  if ($res->num_rows > 0) {
    $eventId = $res->fetch_row()[0];
    $eventData = getEventData($eventId);
    $ticketURL = '/site/assets/files/tickets/' . getFileName($eventData) . '.pdf';

    return '{
      "status": "info",
      "message": "Данное событие уже зарегистрировано",
      "ticketURL": "' .
      $ticketURL .
      '"
    }';
  }

  $sql = "INSERT INTO ci_events
    (patient_id, date_id, interval_id, unit_id, clinic_id)
    VALUES (?, ?, ?, ?, ?)";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param(
    'iiiii',
    $eventData['patient_id'],
    $eventData['date_id'],
    $eventData['interval_id'],
    $eventData['unit_id'],
    $eventData['clinic_id']
  );
  $stmt->execute();
  $stmt->close();

  $id_inserted = $GLOBALS['conn']->query('SELECT LAST_INSERT_ID()')->fetch_row()[0];

  if (0 === $id_inserted) {
    return '{
      "status": "error",
      "message": "Ошибка при создании события"
    }';
  }

  // All looks good
  // Fill spot
  fillSpot($eventData['date_id'], $eventData['interval_id']);
  $event_data = getEventData($id_inserted);
  $ticketURL = makePDF($event_data);

  return '{
    "status": "success",
    "message": "Событие зарегистрировано",
    "ticketURL": "' .
    $ticketURL .
    '"
  }';
}

function freeSpot($id)
{
  $sql = "UPDATE ci_spots
    INNER JOIN ci_events
    ON  ci_spots.date_id = ci_events.date_id
    AND ci_spots.interval_id = ci_events.interval_id
    SET ci_spots.occupied = ci_spots.occupied - 1,
      ci_spots.available = ci_spots.available + 1
    WHERE ci_events.event_id = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();
}

function deleteEvent($id)
{
  freeSpot($id);

  $sql = 'DELETE FROM ci_events WHERE event_id = ?';

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->affected_rows;
  $stmt->close();

  if (-1 === $res) {
    return '{
      "status": "error",
      "message": "Событие не удалено"
    }';
  } elseif (1 === $res) {
    return '{
      "status": "success",
      "message": "Событие удалено"
    }';
  }
  return '{
    "status": "error",
    "message": "Что-то не так"
  }';
}

function insertApplicant($fio, $phone)
{
  $vFio = validateFio($fio);
  $vPhone = validatePhone($phone);

  if ($vFio === null) {
    return -1;
  }
  if ($vPhone === null) {
    return -2;
  }

  $sql = 'INSERT INTO applicants (fio, phone) VALUES (?, ?)';

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('ss', $vFio, $vPhone);
  $stmt->execute();
  $stmt->close();

  $id = $GLOBALS['conn']->query('SELECT LAST_INSERT_ID()')->fetch_row()[0];
  return $id;
}
