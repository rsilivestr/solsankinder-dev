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

  <!-- Styles which normally are inserted by javascript -->
  <noscript>
    <!-- Icon font stylesheet -->
    <link rel="stylesheet" type="text/css" href="<?php echo $config->urls
      ->templates; ?>styles/fontello.min.css" />
    <!-- Montserrat font CDN -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,200;0,400;0,700;1,200;1,400;1,700&display=swap" >
  </noscript>

  <link
    rel="stylesheet"
    type="text/css"
    href="<?php echo $config->urls->templates . $manifest['main.css']; ?>"
  />

  <?php
  $pageTemplate = $page->template->name;
  if ('check-in__user-form' === $pageTemplate || 'check-in__admin-panel' === $pageTemplate) {
    $fileName = $manifest['checkin.css'];
    echo "<link rel='stylesheet' href='{$config->urls->templates}{$fileName}'>";
  }
  ?>

  <?php if ($page->template->name === 'home') {
    $homeScriptUrl = $config->urls->templates . $manifest['home.js'];
    echo "<script defer src='$homeScriptUrl'></script>";
  } ?>

  <script defer src="<?php echo $config->urls->templates . $manifest['main.js']; ?>"></script>
</head>
