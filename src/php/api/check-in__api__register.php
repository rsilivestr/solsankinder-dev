<?php

include_once './check-in/insert-data.php';

$fio = $_POST["patient_fio"];
$dob = $_POST["patient_dob"];
$phone = $_POST["patient_phone"];
$unitId = $_POST["unit_id"];
$dateId = $_POST["date_id"];
$intervalId = $_POST["interval_id"];
$districtId = $_POST["district_id"];
$clinicId = $_POST["clinic_id"];

// Register patient
$patientId = insertPatient($fio, $phone, $dob);

switch ($patientId) {
  case -4:
    return '{ "status": "error", "message": "Ошибка валидации даты рождения" }';
    break;

  case -3:
    return '{ "status": "error", "message": "Ошибка валидации телефона" }';
    break;

  case -2:
    return '{ "status": "error", "message": "Ошибка валидации ФИО" }';
    break;

  default:
    // Compose event data
    $eventData = [
      "patient_id" => $patientId,
      "unit_id" => $unitId,
      "date_id" => $dateId,
      "interval_id" => $intervalId,
      "district_id" => $districtId,
      "clinic_id" => $clinicId,
    ];

    // Register check-in event
    echo insertEvent($eventData);
    break;
}

$GLOBALS['conn']->close();
