<?php

include_once './check-in/create-tables.php';
include_once './check-in/autofill-tables.php';
include_once './check-in/get-data.php';
include_once './check-in/insert-data.php';

// Reset tables
if (isset($_POST['password'])) {
  $passwordHash = '$2y$10$/Z7qAz6Ax0Vbbo8F6b5//u3T4HS2A2ScVSV1KGADnZ3UPTYfZz4BS';

  $isPasswordCorrect = password_verify($_POST['password'], $passwordHash);

  if (!$isPasswordCorrect) {
    header('Location: ?success=0');
    return;
  }

  if (isset($_POST['districts'])) {
    dropTable('districts');
    createTable('districts', $districtSchema);
    fillDistrictsTable($districts);
  }

  if (isset($_POST['clinics'])) {
    dropTable('clinics');
    createTable('clinics', $clinicSchema);
    fillClinicsTable();
  }

  if (isset($_POST['units'])) {
    dropTable('units');
    createTable('units', $unitSchema);
    fillUnitsTable();
  }

  if (isset($_POST['ci_dates'])) {
    dropTable('ci_dates');
    createTable('ci_dates', $dateSchema);
  }

  if (isset($_POST['ci_intervals'])) {
    dropTable('ci_intervals');
    createTable('ci_intervals', $intervalSchema);
    fillIntervalsTable();
  }

  if (isset($_POST['ci_spots'])) {
    dropTable('ci_spots');
    createTable('ci_spots', $spotSchema);
  }

  if (isset($_POST['patients'])) {
    dropTable('patients');
    createTable('patients', $patientSchema);
  }

  if (isset($_POST['ci_events'])) {
    dropTable('ci_events');
    createTable('ci_events', $eventSchema);
  }

  $applicantSchema = "(
    id INT PRIMARY KEY AUTO_INCREMENT,
    fio VARCHAR(128) NOT NULL,
    phone VARCHAR(20) NOT NULL
  )";

  if (isset($_POST['applicants'])) {
    dropTable('applicants');
    createTable('applicants', $applicantSchema);
  }

  header('Location: ?success=1');
}

// Insert new date
if (
  ($newDate = $_POST['add_date']) &&
  ($unitIdsArray = $_POST['add_date_unit']) &&
  ($maxSpotsArray = $_POST['add_date_spots'])
) {
  insertDate($newDate, $unitIdsArray, $maxSpotsArray);
  header('Location: .');
}

// Close date
if ($_POST['close_date']) {
  if (closeDate($_POST['close_date'])) {
    header('Location: ?success=1');
  } else {
    header('Location: ?success=0');
  }
}

// Forms HTML
$dbResetForm = '
<form class="ci-form" id="reset-tables-form" method="POST">
  <h2 class="ci-form__heading">Сбросить базу данных</h2>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="districts"> districts
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="clinics"> clinics
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="units"> units
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="patients"> patients
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="ci_dates"> ci_dates
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="ci_intervals"> ci_intervals
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="ci_spots"> ci_spots
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="ci_events"> ci_events
  </label>
  <label class="ci-form__label mt-8">
    <input type="checkbox" name="applicants"> applicants
  </label>
  <br />
  <div>
    <label>
      <span class="ci-form__label-text">Пароль</span>
      <input class="ci-form__input" name="password" type="password">
    </label>
    <input class="ci-form__button" type="submit" value="Сбросить">
  </div>
</form>';

// Get units
$units = json_decode(getUnits());
$unitsHTML = '';
foreach ($units as $unit) {
  $unitValue = $unit[0];
  $unitLabel = $unit[1];
  $unitsHTML .= "
    <label class='ci-form__label mt-8'>
      <input type='checkbox' name='add_date_unit[]' value='{$unitValue}'>
      {$unitLabel}
    </label>";
}

// Get intervals
$intervals = json_decode(getIntervals());
$intervalInputsHTML = '';
foreach ($intervals as $ivl) {
  $intervalStart = substr($ivl[1], 0, 5);
  $intervalEnd = substr($ivl[2], 0, 5);
  $intervalInputsHTML .= "
  <label class='ci-form__label'>
    <span class='ci-form__label-text'>Мест на {$intervalStart} - {$intervalEnd}</span>
    <input class='ci-form__input' type='number' name='add_date_spots[]' value='40'>
  </label>";
}

// Add new event date
$addDateForm = "
<form class='ci-form' method='POST' id='add-date-form'>
  <h2 class='ci-form__heading'>Добавить заезд</h2>

  <label class='ci-form__label' style='margin-bottom: 2rem'>
    <span class='ci-form__label-text'>Дата</span>
    <input class='ci-form__input' type='date' name='add_date'>
  </label>

  {$unitsHTML}
  {$intervalInputsHTML}

  <input class='ci-form__button' type='submit' value='Добавить'>
</form>";

// Make all event dates list
$eventDatesDatalist = '';
$eventDates = json_decode(getAllDates());
foreach ($eventDates as $date) {
  $ru_date = (new DateTime($date[1]))->format('d.m.Y');

  $eventDatesDatalist .= "<option>{$ru_date}</option>";
}

// Create interval option list
$intervalOptionsHTML = '';
foreach ($intervals as $option) {
  $optionValue = $option[0];
  $intervalStart = substr($option[1], 0, 5);
  $intervalEnd = substr($option[2], 0, 5);

  $intervalOptionsHTML .= "<option value='{$optionValue}'>{$intervalStart} - {$intervalEnd}</option>";
}

// Show registered events on certain date
$showEventsForm = "
<form class='ci-form' id='show-events-form' method='POST'>
  <h2 class='ci-form__heading'>Показать записи за дату</h2>

  <label class='ci-form__label'>
    <span class='ci-form__label-text'>Дата</span>
    <input class='ci-form__input js-search-date-input' type='text' name='show_date' list='event-dates-list'>
    <datalist id='event-dates-list'>
      {$eventDatesDatalist}
    </datalist>
  </label>

  <label class='ci-form__label'>
    <span class='ci-form__label-text'>Интервал</span>
    <select class='ci-form__input js-filter-interval-input'>
      <option selected value=''>Выберите интервал</option>'
      {$intervalOptionsHTML}
    </select>
  </label>

  <input class='ci-form__button' type='submit' value='Показать'>
</form>";

// Make active event dates list
$activeDatesHTML = '';
$activeDates = json_decode(getDates());
foreach ($activeDates as $date) {
  $ru_date = (new DateTime($date[1]))->format('d.m.Y');
  $activeDatesHTML .= '<option>' . $ru_date . '</option>';
}

// Close active event by date
$closeEventForm = "
<form class='ci-form' id='close-event-form' method='POST'>
  <h2 class='ci-form__heading'>Закрыть заезд на дату</h2>

  <label class='ci-form__label'>
    <span class='ci-form__label-text'>Дата</span>
    <select class='ci-form__input' type='text' name='close_date'>
      <option selected value=''></option>
        {$activeDatesHTML}
    </select>
  </label>
  <input class='ci-form__button' type='submit' value='Закрыть'>
</form>";

$showApplicantsForm = '
<form class="ci-form" id="show-applicants-form" method="POST">
  <h2 class="ci-form__heading">Поданные заявки</h2>
  <input type="hidden" value="show-applicants" />
  <input class="ci-form__button" type="submit" value="Показать" />
</form>
';

// Page render starts here
if (!$user->hasRole('check-in')) {
  // User is not authorized to view this page
  $content = '
    <section class="section section--width_m">
      <h2>Доступ запрещен. Войдите под учетной записью с соответствующими
      правами доступа для просмотра данной страницы.</h2>
      <a class="action-btn action-btn--color_blue" href="/adm/">Войти</a>
    </section>';
} else {
  // Show forms
  $content = "
    <section class='ci-admin-panel section section--width_w'>
      <h1>Панель регистратора</h1>
      <div class='ci-form-panel'>
        {$addDateForm}
        {$showEventsForm}
        {$closeEventForm}
        {$showApplicantsForm}
      </div>
    </section>
    <section class='section section--width_w ci-table-wrap'></section>";

  $scriptName = $manifest['checkinAdmin.js'];
  $content .= "<script src='{$config->urls->templates}{$scriptName}'></script>";
}
