<?php

include_once('../modules/SolCheckIn/conn.php');
include_once('../modules/SolCheckIn/table_actions.php');

if(!isset($_POST['chosenDate'])) {
  $conn->close();
  return;
} else {
  $date = $_POST['chosenDate'];
  $date_table = "checkin_" . str_replace(".", "_", $date);
  $sql = "SELECT 
    id, fio, age, tel, unit, district, clinic, checkin_date, checkin_time 
    FROM $date_table";

  // $res;

  if (!isset($_POST['chosenHours'])) {
    // $sql .= " ORDER BY checkin_time, fio ASC";
    $sql .= " ORDER BY fio ASC";
    $res = $conn->query($sql);
  } else {
    $hours = $_POST['chosenHours'];
    // $sql .= " WHERE checkin_time=? ORDER BY checkin_time, fio ASC";
    $sql .= " WHERE checkin_time=? ORDER BY fio ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hours);
    $stmt->execute();

    $res = $stmt->get_result();
  }

  if (!$res) {
    echo '<h2 class="check-in-table-heading">Ошибка обращения к базе данных.</h2>';
    return;
  }

  $out = '<div class="check-in-table-wrapper"><div id="float-message-box"></div><h2 class="check-in-table-heading">Список заезжающих на ' . $date . '</h2>
  <table class="check-in-table">
    <thead>
      <tr>
        <th>№</th>
        <th>ФИО</th>
        <th>Возр</th>
        <th>Тел</th>
        <th>Отд</th>
        <th>Район</th>
        <th>Поликлиника</th>
        <th>Часы</th>
        <th>Удалить</th>
      </tr>
    </thead>
    <tbody>';

  if ($res->num_rows > 0) {
    $i = 1;
    while ($row = $res->fetch_assoc()) {
      $out .= "<tr>
        <td>" . $i . "</td>
        <td>" . $row["fio"] . "</td>
        <td>" . $row["age"] . "</td>
        <td>" . $row["tel"] . "</td>
        <td>" . $row["unit"] . "</td>
        <td>" . $row["district"] . "</td>
        <td>" . $row["clinic"] . "</td>
        <td>" . $row["checkin_time"] . "</td>
        <td>
          <button 
            class='check-in-table__del-person'
            data-id=" . $row["id"] . "
            data-date=" . $row["checkin_date"] . "
          >Удалить</button>
        </td>
      </tr>";

      $i += 1;
    }
  }

  $out .= "</tbody></table>";

  echo $out;
}

$conn->close();
