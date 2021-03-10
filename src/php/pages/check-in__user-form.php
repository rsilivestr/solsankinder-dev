<?php include_once './check-in/get-data.php';

$DISTRICT_DATALIST = '';
$districts = json_decode(getDistricts());
foreach ($districts as $d) {
  $DISTRICT_DATALIST .= '<option>' . $d[1] . '</option>';
}
// Get form html from file
$formHTML = file_get_contents('./check-in/form.html');
// Insert district datalist html
$formHTML = str_ireplace('<span>DISTRICTS</span>', $DISTRICT_DATALIST, $formHTML);

$content .= $formHTML;
$content .= '<script src="' . $config->urls->templates . 'scripts/check-in.js"></script>';
