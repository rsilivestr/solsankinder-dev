<?php

include_once('../modules/SolCheckIn/conn.php');
include_once('../modules/SolCheckIn/table_actions.php');

// global $conn;
// function delPerson($id, $date) {

//   $table_name = "checkin_" . str_replace(".", "_", $date);

//   $sql = "SELECT fio FROM $table_name WHERE id=?";

//   // return $sql;

//   $stmt = $conn->prepare($sql);
  
//   // if(!$stmt = $conn->prepare($sql)) {
//   //   return '{
//   //     "status": "error",
//   //     "message": "Ошибка подготовки SQL запроса"
//   //   }';
//   // } else {
//   //   // $stmt->bind_param("i", 64);

//   //   // $stmt->execute();
  
//   //   // $res = $stmt->get_result();
//   //   // $row = $res->fetch_assoc();
    
  
//   //   return '{ 
//   //     "status": "info",
//   //     "message": "Конец функции"
//   //   }';
//   // }
// }


if(!isset($_POST['regId']) || !isset($_POST['regDate'])) {
  return '{
    "status": "error",
    "message": "Ошибка запроса"
  }';
} else {
  $regId = intval($_POST['regId'], 10);
  $regDate = $_POST['regDate'];

  $table_name = "checkin_" . str_replace(".", "_", $regDate);

  $sql = "SELECT fio FROM $table_name WHERE id=?";

  if (!$stmt = $conn->prepare($sql)) {
    return '{
      "status": "error",
      "message": "Ошибка подготовки SQL запроса"
    }';
  } else {
    $stmt->bind_param("i", $regId);

    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if (!$res->num_rows > 0) {
      return '{
        "status": "info",
        "message": "Данный пациент уже был удален ранее"
      }';
    } else {
      $row = $res->fetch_assoc();
      $fio = $row["fio"];

      $sql = "DELETE FROM $table_name WHERE id=?";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $regId);
      $stmt->execute();
      $res = $stmt->get_result();
      $stmt->close();

      return '{
        "status": "success",
        "message": "Пациент ' . $fio = $row["fio"] . ' удален"
      }';
    }
  }

  return '{
    "status": "info",
    "message": "Конец функции"
  }';
}

$conn->close();
