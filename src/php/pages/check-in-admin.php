<?php

include_once "./check-in/create-tables.php";
include_once "./check-in/autofill-tables.php";
include_once "./check-in/get-data.php";
include_once "./check-in/insert-data.php";

// POST actions
if (isset($_POST["password"])) {
  if (
    !password_verify(
      $_POST["password"],
      '$2y$10$/Z7qAz6Ax0Vbbo8F6b5//u3T4HS2A2ScVSV1KGADnZ3UPTYfZz4BS'
      // '$2y$10$e9B9cyaX5p7V1OQKnf.TDOAhJDRdc6M.biI27bVsICBfjuUK2fPo6'
    )) {
    // Password is wrong: redirect to warning
    header("Location: ?success=0");
    return;
  } else {
    // Password is right:
    // Reset tables according to POSTed checkboxes
    if (isset($_POST["districts"])) {
      dropTable("districts");
      createTable("districts", $districtSchema);
      fillDistrictsTable($districts);
    }

    if (isset($_POST["clinics"])) {
      dropTable("clinics");
      createTable("clinics", $clinicSchema);
      fillClinicsTable();
    }

    if (isset($_POST["units"])) {
      dropTable("units");
      createTable("units", $unitSchema);
      fillUnitsTable();
    }

    if (isset($_POST["ci_dates"])) {
      dropTable("ci_dates");
      createTable("ci_dates", $dateSchema);
    }

    if (isset($_POST["ci_intervals"])) {
      dropTable("ci_intervals");
      createTable("ci_intervals", $intervalSchema);
      fillIntervalsTable();
    }

    if (isset($_POST["ci_spots"])) {
      dropTable("ci_spots");
      createTable("ci_spots", $spotSchema);
    }

    if (isset($_POST["patients"])) {
      dropTable("patients");
      createTable("patients", $patientSchema);
    }

    if (isset($_POST["ci_events"])) {
      dropTable("ci_events");
      createTable("ci_events", $eventSchema);
    }

    // Redirect to success message
    header("Location: ?success=1");
  }
}

// Insert new date
if (
  ($newDate = $_POST["add_date"]) &&
  ($unitId = $_POST["add_date_unit"]) &&
  ($maxSpots = $_POST["add_date_spots"])
) {
  insertDate($newDate, $unitId, $maxSpots);
}

// Close date
if ($_POST["close_date"]) {
  if (closeDate($_POST["close_date"])) {
    header("Location: ?success=1");
  } else {
    header("Location: ?success=0");
  }
}

// Forms HTML
$dbResetForm = '
<form class="ci-form" method="POST">
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
  <br />
  <div>
    <label><span class="ci-form__label-text">Пароль</span><input class="ci-form__input" name="password" type="password"></label>
    <input class="ci-form__button" type="submit" value="Сбросить">
  </div>
</form>';

// Get units for the next form, generate options list
$unitOptions = json_decode(getUnits());
$unitOptionsHTML = "";
foreach ($unitOptions as $index => $option) {
$unitOptionsHTML .= "<option value='$option[0]'>$option[1]</option>";
}

// Add new event date
$addDateForm = '
<form class="ci-form" method="POST">
  <h2 class="ci-form__heading">Добавить заезд</h2>

  <label class="ci-form__label">
    <span class="ci-form__label-text">Дата</span>
    <input class="ci-form__input" type="date" name="add_date">
  </label>

  <label class="ci-form__label">
    <span class="ci-form__label-text">Отделение</span>
    <select class="ci-form__input" name="add_date_unit">
      <option disabled selected value="">Выберите отделение</option>'
      . $unitOptionsHTML .
    '</select>
  </label>

  <label class="ci-form__label">
    <span class="ci-form__label-text">Мест на интервал</span>
    <input class="ci-form__input" type="number" name="add_date_spots" value="40">
  </label>

  <input class="ci-form__button" type="submit" value="Добавить">
</form>';

// Make all event dates list
$eventDatesDatalist = '';
$eventDates = json_decode(getAllDates());
foreach ($eventDates as $date) {
  $ru_date = (new DateTime($date[1]))->format('d.m.Y');
  $eventDatesDatalist .= '<option>'.$ru_date.'</option>';
}

// Show registered events on certain date
$showEventsForm = '
<form class="ci-form" id="show-events-form" method="POST">
  <h2 class="ci-form__heading">Показать записи за дату</h2>

  <label class="ci-form__label">
    <span class="ci-form__label-text">Дата</span>
    <input class="ci-form__input" type="text" name="show_date" list="event-dates-list">
    <datalist id="event-dates-list">' . $eventDatesDatalist . '</datalist>
  </label>

  <input class="ci-form__button" type="submit" value="Показать">
</form>';

// Make active event dates list
$activeDatesDatalist = '';
$activeDates = json_decode(getDates());
foreach ($activeDates as $date) {
  $ru_date = (new DateTime($date[1]))->format('d.m.Y');
  $activeDatesDatalist .= '<option>'.$ru_date.'</option>';
}

// Close active event by date
$closeEventForm = '
<form class="ci-form" id="close-event-form" method="POST">
  <h2 class="ci-form__heading">Закрыть заезд на дату</h2>

  <label class="ci-form__label">
    <span class="ci-form__label-text">Дата</span>
    <input class="ci-form__input" type="text" name="close_date" list="active-dates-list">
    <datalist id="active-dates-list">' . $activeDatesDatalist . '</datalist>
  </label>

  <input class="ci-form__button" type="submit" value="Закрыть">
</form>';

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
  $content = '
    <section class="section section--width_w">
      <h1>Панель регистратора</h1>
      <div class="ci-admin-panel">'
        // .$dbResetForm
        .$addDateForm
        .$showEventsForm
        .$closeEventForm
      .'</div>
    </section>
    <section class="section section--width_m ci-table-wrap"></section>';

  $content .= '<script src="'.$urls->templates.'scripts/check-in-admin.js"></script>';
}
