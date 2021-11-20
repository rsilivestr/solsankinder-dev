<?php include_once './check-in/get-data.php';

$content = '';

$formHTML = file_get_contents('./check-in/get-voucher-form.html');
$content .= $formHTML;

$scriptName = $manifest['get-voucher.js'];
$content .= "<script src='{$config->urls->templates}{$scriptName}'></script>";
