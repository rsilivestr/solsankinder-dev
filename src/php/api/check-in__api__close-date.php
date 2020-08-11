<?php

include_once('../modules/SolCheckIn/conn.php');
include_once('../modules/SolCheckIn/table_actions.php');

if (!isset($_POST["chosenDate"])) {
  echo '{ "status": "error", "message": "Дата не задана" }';
} else {
  $date = $_POST["chosenDate"];

  $sql = "UPDATE checkin_dates
    SET is_active = FALSE
    WHERE checkin_date=?";
  
  $stmt = $conn->prepare($sql);

  if (!$stmt) {
    echo '{ "status": "error", "message": "Ошибка при подготовке SQL запроса" }';
  } else {
    $stmt->bind_param("s", $date);

    if (!$stmt->execute()) {
      echo '{ "status": "error", "message": "Ошибка при выполнении SQL запроса." }';
    } else {
      echo '{ "status": "success", "message": "Заезд на дату '.$date.' закрыт." }';
    }
  }

  $stmt->close();
}

$conn->close();
