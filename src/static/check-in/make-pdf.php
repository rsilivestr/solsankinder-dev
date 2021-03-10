<?php

include_once $urls->root . '/vendor/mpdf/mpdf/src/Mpdf.php';

function formatDate($dateString)
{
  return (new DateTime($dateString))->format('d.m.Y');
}

function getFileName($event_data)
{
  $fioArr = explode(' ', $event_data['fio']);
  // Get family name
  $familyName = $fioArr[0];
  // Get initials
  $initials =
    mb_substr($fioArr[1], 0, 1, 'utf-8') . '.' . mb_substr($fioArr[2], 0, 1, 'utf-8') . '.';

  return $familyName . ' ' . $initials . ' ' . formatDate($event_data['ci_date']);
}

function makePDF($event_data)
{
  // $fioArr = explode(' ', $event_data['fio']);
  // // Get family name
  // $familyName = $fioArr[0];
  // // Get initials
  // $initials = mb_substr($fioArr[1], 0, 1, 'utf-8')
  //   . '.'
  //   . mb_substr($fioArr[2], 0, 1, 'utf-8')
  //   . '.';
  // Set file name (Иванов И.И. 01.01.2020)
  // $fileName = $familyName . ' ' . $initials . ' ' . $ru_date;
  $fileName = getFileName($event_data);
  // Format date
  $ru_date = formatDate($event_data['ci_date']);
  // Trim seconds
  $start = mb_substr($event_data['start_time'], 0, 5, 'utf-8');
  $end = mb_substr($event_data['end_time'], 0, 5, 'utf-8');
  // Make PDF
  $mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'defaultCssFile' => '../modules/SolCheckIn/styles/checkin-pdf.css',
    'default_font_size' => 14,
    'default_font' => 'helvetica',
  ]);
  // Add html
  $mpdf->WriteHTML(
    '<img class="check-in-ticket__qr-code" src="../assets/files/tickets/solQr1.svg">'
  );
  $mpdf->WriteHTML(
    '<p class="check-in-ticket__header">СПб ГБУЗ "Детский санаторий "Солнечное"</p>'
  );
  $mpdf->WriteHTML('<h1 class="check-in-ticket__title">Талон регистрации</h1>');
  $mpdf->WriteHTML('<p class="check-in-ticket__text">Выдан на имя: ' . $event_data['fio'] . '</p>');
  $mpdf->WriteHTML('<p class="check-in-ticket__text">Дата заезда: ' . $ru_date . '</p>');
  $mpdf->WriteHTML(
    '<p class="check-in-ticket__text">Часы заезда: ' . $start . ' - ' . $end . '</p>'
  );
  $mpdf->WriteHTML(
    '<p class="check-in-ticket__info">Распечатайте или сохраните пропуск.<br/>Просьба не опаздывать.</p>'
  );
  // Write to disc
  $mpdf->Output('../assets/files/tickets/' . $fileName . '.pdf', 'F');

  return '/site/assets/files/tickets/' . $fileName . '.pdf';
}
