<?php namespace ProcessWire;

if(!isset($_SESSION)){
	session_start();
	$_SESSION['lowVision'] = false;
}

if(isset($_POST['lowVision'])) {
	$_SESSION['lowVision'] = !($_SESSION['lowVision']);
}

if (!$config->ajax) {
	include('_head.php');

	echo $content;

	include('_foot.php');
}
