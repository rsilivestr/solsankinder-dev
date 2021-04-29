<?php include_once './check-in/get-data.php';

$content = "<div class='section section--width_m'>
  $page->body
</div>";

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
