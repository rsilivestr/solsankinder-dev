<?php include_once './check-in/get-data.php';

$content = "
<section class='section section--width_m'>
  $page->body
  <h3>Заявка кандидата на размещение в санатории рассматривается при наличии места, в случае отказа пациента, направленного поликлиникой по разнарядке</h3
</section>";

$formHTML = file_get_contents('./check-in/get-voucher-form.html');
$content .= $formHTML;

$scriptName = $manifest['getVoucher.js'];
$content .= "<script src='{$config->urls->templates}{$scriptName}'></script>";
