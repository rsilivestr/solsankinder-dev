<?php

function validateFio($fio)
{
  $names = explode(' ', $fio);
  $len = sizeof($names);
  // 2 or more cyrillic letters
  // optional second part divided by dash (Салтыков-Щедрин, Мария-Луиза)
  $re = "/^[\p{L}]{2,}(\-([\p{L}]{2,})?)?$/iu";
  $ok = true;

  foreach ($names as $name) {
    if (!preg_match($re, $name)) {
      $ok = false;
    }
  }

  return $ok ? $fio : null;
}

function validatePhone($tel)
{
  $symbols = ['(', ')', '-', '.', ' ', '+'];
  // Strip symbols
  $tel = str_replace($symbols, '', $tel);
  // tel can be 7, 10 or 11 digits
  // if 11 - should start with 7 or 8
  $re = "/^((7|8)?\d{3})?\d{7}$/";

  if (!preg_match($re, $tel)) {
    // tel is invalid
    return null;
  } else {
    if (strlen($tel === 10)) {
      $tel = substr_replace($tel, '7', 0, 0);
    } elseif (11 === strlen($tel) && '8' === $tel[0]) {
      $tel[0] = '7';
    }

    return $tel;
  }
}

function getFullYears($date_string)
{
  $date = new DateTime($date_string);
  $today = new DateTime();

  $dayDiff = $today->format('d') - $date->format('d');
  $monthDiff = $today->format('m') - $date->format('m');
  $yearDiff = $today->format('Y') - $date->format('Y');

  $fullMonths = $dayDiff < 0 ? $monthDiff - 1 : $monthDiff;

  $fullYears = $fullMonths < 0 ? $yearDiff - 1 : $yearDiff;

  return $fullYears;
}

function validateDob($dob, $ageMin, $ageMax)
{
  $age = getFullYears($dob);

  if ($age < $ageMin || $age > $ageMax) {
    return null;
  }

  return $dob;
}
