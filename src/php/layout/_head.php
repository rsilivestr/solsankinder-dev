<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="<?php echo $page->summary; ?>">

  <title><?php echo $title; ?></title>

  <link
    rel="icon"
    type="image/svg"
    href="<?php echo $config->urls->assets; ?>images/nerp_plain.svg"
  >

  <link
    rel="stylesheet"
    type="text/css"
    href="<?php echo $config->urls->templates; ?>styles/glide.core.min.css"
  >

  <link rel="stylesheet" type="text/css" href="<?php echo $config->urls
    ->templates; ?>styles/fontello.min.css" />
  <link rel="stylesheet" href="<?php echo $config->urls->templates; ?>styles/montserrat.css">

  <link
    rel="stylesheet"
    type="text/css"
    href="<?php echo $config->urls->templates . $manifest['main.css']; ?>"
  />

  <?php
  $pageTemplate = $page->template->name;
  $isCheckIn =
    $pageTemplate === 'check-in__user-form' ||
    $pageTemplate === 'check-in__admin-panel' ||
    $pageTemplate === 'check-in__get-voucher';
  if ($isCheckIn) {
    $fileName = $manifest['checkin.css'];
    echo "<link rel='stylesheet' href='{$config->urls->templates}{$fileName}'>";
  }
  ?>

  <?php if ($page->template->name === 'home') {
    echo '<style>';
    echo file_get_contents('./gu-styles.css');
    echo '</style>';

    $homeScriptUrl = $config->urls->templates . $manifest['home.js'];
    echo "<script defer src='$homeScriptUrl'></script>";
  } ?>

  <script defer src="<?php echo $config->urls->templates . $manifest['main.js']; ?>"></script>
</head>
