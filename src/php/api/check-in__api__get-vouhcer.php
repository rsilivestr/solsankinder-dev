<?php

include_once './check-in/insert-data.php';

$fio = $_POST['fio'];
$phone = $_POST['phone'];

$applicantId = insertApplicant($fio, $phone);

switch ($applicantId) {
  case -3:
    echo '{"status": "error", "message": "Неизвестная ошибка. Попробуйте повторить отправку формы позже."}';
    break;
  case -2:
    echo '{"status": "error", "message": "Ошибка валидации телефона"}';
    break;
  case -1:
    echo '{"status": "error", "message": "Ошибка валидации ФИО"}';
    break;
  default:
    echo '{"status": "success", "message": "Заявка на путёвку зарегистрирована"}';
}

$GLOBALS['conn']->close();
