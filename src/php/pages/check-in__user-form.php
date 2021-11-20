<?php include_once './check-in/get-data.php';

$content = "
<section class='section section--width_m'>
  $page->body
  <h3>Нет путёвки? <a href='/get-voucher'>Оставить заявку</a></h3>
</section>";

$DISTRICT_DATALIST = '';
$districts = json_decode(getDistricts());
foreach ($districts as $d) {
  $DISTRICT_DATALIST .= '<option>' . $d[1] . '</option>';
}

$formHTML = file_get_contents('./check-in/form.html');
$formHTML = str_ireplace('<span>DISTRICTS</span>', $DISTRICT_DATALIST, $formHTML);

$content .= $formHTML;

$scriptName = $manifest['checkin.js'];
$content .= "<script src='{$config->urls->templates}{$scriptName}'></script>";
