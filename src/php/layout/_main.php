<?php namespace ProcessWire;

if (!isset($_SESSION)) {
  session_start();
  $_SESSION['lowVision'] = false;
}

// toggle low-vision
if (isset($_POST['lowVision'])) {
  $_SESSION['lowVision'] = !$_SESSION['lowVision'];

  $status = $_SESSION['lowVision'] ? 1 : 0;

  echo '{"lowVision": "' . $status . '"}';
}

// if not an api call show content
if (!$config->ajax) {
  echo '<!DOCTYPE html><html lang="ru">';

  include '_head.php';

  $bodyClass = $page->template->name;
  $bodyClass .= $isLowVisionActive && ' low-vision';
  echo "<body class='$bodyClass'>";

  include '_header.php';

  include '_bread.php';

  echo "<main class='main'>$content</main>";

  include '_foot.php';

  include '_scripts.php';

  echo '</body></html>';
}
