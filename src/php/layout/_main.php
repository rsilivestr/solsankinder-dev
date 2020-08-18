<?php namespace ProcessWire;

if(!isset($_SESSION)){
	session_start();
	$_SESSION['lowVision'] = false;
}

// toggle low-vision
if(isset($_POST['lowVision'])) {
	$_SESSION['lowVision'] = !($_SESSION['lowVision']);

	$status = $_SESSION['lowVision'] ? 1 : 0;

	echo '{"lowVision": "'.$status.'"}';
}

// if not an api call
if (!$config->ajax) {
	include('_head.php');

	echo $content;

	include('_foot.php');
}
