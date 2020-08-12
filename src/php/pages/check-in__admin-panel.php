<?php

if (!$user->hasRole('check-in')) {
  $content = '<section class="section section--basic">
    <h2>Доступ запрещен. Войдите под учетной записью с соответствующими 
    правами доступа для просмотра данной страницы.</h2>
    <a class="check-in-login-btn" href="/adm/">Войти</a>
  </section>';
} else {
  include_once('../modules/SolCheckIn/conn.php');
  include_once('../modules/SolCheckIn/table_actions.php');

  $dates = getAvailDates($conn);

  $dateList = '';
  
  foreach($dates as $date) {
    $dateList .= '<li class="check-in-form__dropdown-item">' . $date . '</li>';
  }
  
  $showPersonsForm = '
  <form id="checkin-show-persons" class="check-in-form">
    <div class="check-in-form__label check-in-form__label--dropdown">Показать записи на дату
      <input
        type="text"
        name="chosenDate"
        id="checkin-show-persons--date-input"
        class="check-in-form__input check-in-form__input--dropdown"
        placeholder="дата заезда"
        readonly>
      <ul class="check-in-form__dropdown dropdown-hidden">'
      . $dateList .
      '</ul>
    </div>
    <div class="check-in-form__label check-in-form__label--dropdown">Показать записи на время
      <input
        type="text"
        name="chosenHours"
        id="checkin-show-persons--hours-input"
        class="check-in-form__input check-in-form__input--dropdown"
        placeholder="время заезда"
        readonly>
      <ul class="check-in-form__dropdown dropdown-hidden">
        <li class="check-in-form__dropdown-item"></li>
        <li class="check-in-form__dropdown-item">09.00 - 11.00</li>
        <li class="check-in-form__dropdown-item">11.00 - 13.00</li>
        <li class="check-in-form__dropdown-item">13.00 - 15.00</li>
        <li class="check-in-form__dropdown-item">15.00 - 16.00</li>
      </ul>
    </div>
    <input type="submit" id="checkin-show-persons--submit" class="check-in-form__submit" value="Показать">
  </form>';
  
  $addDateForm = '
  <form id="checkin-add-date" class="check-in-form">
    <label class="check-in-form__label">Добавить дату заезда
      <input type="date" name="checkInDate" id="checkin-add-date--date-input" class="check-in-form__input" placeholder="новая дата заезда">
    </label>
    <input type="submit" id="checkin-add-date--submit" class="check-in-form__submit" value="Добавить">
  </form>';

  $closeDateForm = '
  <form id="check-in-close-date" class="check-in-form">
    <div class="check-in-form__label check-in-form__label--dropdown">Закрыть заезд на дату
      <input
        type="text"
        name="chosenDate"
        id="check-in-close-date--input"
        class="check-in-form__input check-in-form__input--dropdown"
        placeholder="дата заезда"
        readonly>
      <ul class="check-in-form__dropdown dropdown-hidden">'
        . $dateList .
      '</ul>
    </div>
    <input type="submit" id="check-in-close-date--submit" class="check-in-form__submit" value="Закрыть">
  </form>';
  
  $conn->close();

  $content = '
    <div class="check-in-form-panel">'
      . $showPersonsForm
      . $addDateForm
      . $closeDateForm
    . '</div>
    <div id="check-in-display-panel" class="check-in-display-panel"></div>
    <script src="' . $config->urls->siteModules . 'SolCheckIn/scripts/check-in.0ab6b5ca198c948fd0fbc63c9f8e9c92.js"></script>
    <script>
      SolCheckIn.handleAdminPanel();
    </script>';
}
