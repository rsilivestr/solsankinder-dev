<?php

function validateFio($fio) {
  $names = explode(' ', $fio);
  $len = sizeof($names);
  // 2 or more cyrillic letters
  // optional second part divided by dash (Салтыков-Щедрин, Мария-Луиза)
  $re = "/^[\p{L}]{2,}(\-([\p{L}]{2,})?)?$/iu";
  $ok = TRUE;
  // Check subnames
  foreach ($names as $name) {
    if (!preg_match($re, $name)) {
      $ok = FALSE;
    }
  }

  if (!$ok) {
    // fio is invalid
    return NULL;
  } else {
    // fio is valid
    return $fio;
  }
}

function validatePhone($tel) {
  $symbols = array(
    '(',
    ')',
    '-',
    '.',
    ' ',
    '+',
  );
  // Strip symbols
  $tel = str_replace($symbols, '', $tel);
  // tel can be 7, 10 or 11 digits
  // if 11 - should start with 7 or 8
  $re = "/^((7|8)?\d{3})?\d{7}$/";

  if (!preg_match($re, $tel)) {
    // tel is invalid
    return NULL;
  } else {
    // tel is valid
    // 7 digits is OK
    if (strlen($tel === 10)) {
      // Append leading 7
      $tel = substr_replace($tel, '7', 0, 0);
    } elseif (11 === strlen($tel) && '8' === $tel[0]) {
      // Change leading 8 with 7
      $tel[0] = '7';
    }

    return $tel;
  }
}

function getFullYears($date_string) {
  $date = new DateTime($date_string);
  $today = new DateTime();
  // Get diffs
  $dayDiff = $today->format('d') - $date->format('d');
  $monthDiff = $today->format('m') - $date->format('m');
  $yearDiff = $today->format('Y') - $date->format('Y');
  // Get full months
  $fullMonths = $dayDiff < 0 ? $monthDiff - 1 : $monthDiff;
  // Get full years
  $fullYears = $fullMonths < 0 ? $yearDiff - 1 : $yearDiff;

  return $fullYears;
}

function validateDob($dob, $ageMin, $ageMax) {
  // Get age, full years
  $age = getFullYears($dob);
  // Age is invalid
  if ($age < $ageMin || $age > $ageMax) return NULL;
  // Age is valid
  return $dob;
}
