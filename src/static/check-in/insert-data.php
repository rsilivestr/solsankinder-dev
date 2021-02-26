<?php

include_once "conn.php";
include_once "validate-data.php";
include_once "make-pdf.php";

function insertSpots($date, $maxSpotsArray) {
  // get date id
  $sql = "SELECT id FROM ci_dates WHERE ci_date = ?";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("s", $date);
  $stmt->execute();
  $resDateId = $stmt->get_result();
  $stmt->close();

  $dateId = $resDateId->fetch_row()[0];

  // get active interval ids
  $sql = "SELECT id FROM ci_intervals WHERE is_active = 1";
  $resIntervals = $GLOBALS['conn']->query($sql)->fetch_all(MYSQLI_NUM);

  $intervalIds = [];
  foreach ($resIntervals as $row) {
    array_push($intervalIds, $row[0]);
  }

  // for each active interval id
  foreach ($intervalIds as $index => $ivlId) {
    // check existing spots
    $sql = "SELECT id FROM ci_spots
      WHERE date_id = ?
      AND interval_id = ?";

    $stmt = $GLOBALS['conn']->prepare($sql);
    $stmt->bind_param("ii", $dateId, $ivlId);
    $stmt->execute();
    $match = $stmt->get_result()->num_rows;
    $stmt->close();

    if (0 === $match) {
      // insert spots
      $sql = "INSERT INTO ci_spots
        (date_id, interval_id, total, available)
        VALUES (?, ?, ?, ?)";
      $stmt = $GLOBALS['conn']->prepare($sql);
      $stmt->bind_param(
        "iiii",
        $dateId,
        $ivlId,
        $maxSpotsArray[$index],
        $maxSpotsArray[$index]
      );
      $stmt->execute();
      $stmt->close();
    }
  }
}

function checkUnitActive($unitId) {
  $sql = "SELECT id FROM units WHERE id = ? AND NOT date_id = 0";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("i", $unitId);
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  return $res->num_rows > 0;
}

function makeUnitActive($dateId, $unitId) {
  $sql = "UPDATE units SET date_id = ? WHERE id = ?";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("ii", $dateId, $unitId);
  $stmt->execute();
  $stmt->close();
}

function makeUnitInactive($id) {
  $sql = "UPDATE units SET date_id = 0 WHERE id = ?";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}

function insertDate($newDate, $unitIdsArray, $maxSpotsArray) {
  // check if that date already exists
  $sql = "SELECT id FROM ci_dates WHERE ci_date = ?";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("s", $newDate);
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  // return if date already exists
  if ($res->num_rows > 0) {
    return '{ "status": "info", "message": "That date exists." }';
  }

  // check if any of the units are already active, return if it is
  foreach ($unitIdsArray as $unitId) {
    $isActive = checkUnitActive($unitId);

    if ($isActive) {
      return '{ "status": "info", "message": "Unit already in use." }';
    }
  }

  // insert date
  $sql = "INSERT INTO ci_dates
    (ci_date, unit_ids, is_active)
    VALUES (?, ?, 1)";
  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("ss", $newDate, implode(",", $unitIdsArray));
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  $dateId = $GLOBALS['conn']->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];
  // Make units active
  foreach ($unitIdsArray as $unitId) {
    makeUnitActive($dateId, $unitId);
  }

  insertSpots($newDate, $maxSpotsArray);

  return '{ "status": "success", "message": "Date was added." }';
}

// update date: set active = 0
function closeDate($date) {
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
    // Get units that were active on that date
    $sql = "SELECT unit_ids FROM ci_dates WHERE ci_date = ?";
    $stmt = $GLOBALS['conn']->prepare($sql);
    $stmt->bind_param('s', $formattedDate);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_row();
    $stmt->close();

    // Convert unit_ids string into an array
    $unitIds = explode(",", $res[0]);
    // Deactivate those units
    foreach ($unitIds as $unitId) {
      makeUnitInactive($unitId);
    }

    return TRUE;
  }

  else return FALSE;
}

function insertPatient($fio = NULL, $phone = NULL, $dob = NULL) {
  $vFio = validateFio($fio);
  $vPhone = validatePhone($phone);
  $vDob = validateDob($dob, 1, 17);

  // return on empty params
  if (NULL === $vFio) {
    return -2;
    // return '{ "status": "error", "message": "Ошибка валидации ФИО" }';
  }
  else if (NULL === $vPhone) {
    return -3;
    // return '{ "status": "error", "message": "Ошибка валидации телефона" }';
  }
  else if (NULL === $vDob) {
    return -4;
    // return '{ "status": "error", "message": "Ошибка валидации даты рождения" }';
  }
  // search patient
  $sql = "SELECT id FROM patients
    WHERE LOWER(fio) = LOWER(?) AND dob = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("ss", $vFio, $vDob);
  $stmt->execute();

  // get id of existing patient
  $res = $stmt->get_result();
  $id_existing = $res->fetch_row()[0];

  $stmt->close();

  if (!$res->num_rows > 0) {
    // if patient does not exist, add new patient
    $sql = "INSERT INTO patients (fio, phone, dob) VALUES (?, ?, ?)";

    $stmt = $GLOBALS['conn']->prepare($sql);
    $stmt->bind_param("sss", $vFio, $vPhone, $vDob);
    $stmt->execute();
    $stmt->close();
    // return last inserted id
    $id_inserted = $GLOBALS['conn']->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];
    return $id_inserted;
  }
  // return existing patient id
  return $id_existing;
}

function getEventData($id = NULL) {
  if (!$id) return;
  // Get patient fio, check-in date, start & end times by event id
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

function fillSpot($date_id, $interval_id) {
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

function insertEvent($eventData = NULL) {
  if (NULL == $eventData) {
    // Send error message if some data is missing
    return '{ "status": "error", "message": "Ошибка отправки запроса" }';
  } else if (0 == $eventData['patient_id']) {
    return '{ "status": "error", "message": "Ошибка в id пациента" }';
  } else if (0 == $eventData['unit_id']) {
    return '{ "status": "error", "message": "Ошибка в id отделения" }';
  } else if (0 == $eventData['date_id']) {
    return '{ "status": "error", "message": "Ошибка в id даты" }';
  } else if (0 == $eventData['interval_id']) {
    return '{ "status": "error", "message": "Ошибка в id интервала" }';
  } else if (0 == $eventData['district_id']) {
    return '{ "status": "error", "message": "Ошибка в id района" }';
  } else if (0 == $eventData['clinic_id']) {
    return '{ "status": "error", "message": "Ошибка в id клиники" }';
  }
  // check if event exists
  $sql = "SELECT event_id FROM ci_events WHERE patient_id = ? AND date_id = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param("ii", $eventData["patient_id"], $eventData["date_id"]);
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
      "ticketURL": "' . $ticketURL . '"
    }';
  }
  // Insert event data into ci_events
  $sql = "INSERT INTO ci_events
    (patient_id, date_id, interval_id, unit_id, clinic_id)
    VALUES (?, ?, ?, ?, ?)";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param(
    "iiiii",
    $eventData["patient_id"],
    $eventData["date_id"],
    $eventData["interval_id"],
    $eventData["unit_id"],
    $eventData["clinic_id"]
  );
  $stmt->execute();
  $stmt->close();

  $id_inserted = $GLOBALS['conn']->query("SELECT LAST_INSERT_ID()")->fetch_row()[0];

  if (0 === $id_inserted) {
    // Data was not inserted
    return '{
      "status": "error",
      "message": "Ошибка при создании события"
    }';
  }

  // All looks good
  // Fill spot
  fillSpot($eventData["date_id"], $eventData["interval_id"]);
  // Get data for PDF
  $event_data = getEventData($id_inserted);
  // Make PDF, get its url
  $ticketURL = makePDF($event_data);

  // Send positive response
  return '{
    "status": "success",
    "message": "Событие зарегистрировано",
    "ticketURL": "' . $ticketURL . '"
  }';
}

function freeSpot($id) {
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

function deleteEvent($id) {
  // Free spot BEFORE event deletion
  freeSpot($id);

  $sql = "DELETE FROM ci_events WHERE event_id = ?";

  $stmt = $GLOBALS['conn']->prepare($sql);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->affected_rows;
  $stmt->close();

  if (-1 === $res) {
    // -1 indicates that the query has returned an error
    return '{
      "status": "error",
      "message": "Событие не удалено"
    }';
  } else if (1 === $res) {
    return '{
      "status": "success",
      "message": "Событие удалено"
    }';
  }
  // Something went wrong
  return '{
    "status": "error",
    "message": "Что-то не так"
  }';
}
