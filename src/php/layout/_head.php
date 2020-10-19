<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="<?php echo $page->summary; ?>">

  <title><?php echo $title; ?></title>

  <!-- Nerp favicon -->
  <link
    rel="icon"
    type="image/svg"
    href="<?php echo $config->urls->assets; ?>images/nerp_plain.svg"
  >

  <!-- Glidejs slider stylesheet -->
  <link
    rel="stylesheet"
    type="text/css"
    href="<?php echo $config->urls->templates; ?>styles/glide.core.min.css"
  >

  <!-- Styles which normally are inserted by javascript -->
  <noscript>
    <!-- Icon font stylesheet -->
    <link rel="stylesheet" type="text/css" href="<?php echo $config->urls->templates; ?>styles/fontello.min.css" />
    <!-- Montserrat font CDN -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,200;0,400;0,700;1,200;1,400;1,700&display=swap" >
    <!-- Reserved fonts -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Bad+Script&family=Caveat&family=Kelly+Slab&family=Lobster&family=Neucha&family=Pacifico&family=Poiret+One&family=Press+Start+2P&family=Ruslan+Display&family=Underdog&display=swap" rel="stylesheet"> -->
  </noscript>

  <!-- Main stylesheet -->
  <link
    rel="stylesheet"
    type="text/css"
    href="<?php echo $config->urls->templates . $manifest['main.css']; ?>"
  />

  <!-- Check-in stylesheet -->
  <?php
    if ($page->template->name === 'check-in__user-form'
      || $page->template->name === 'check-in__admin-panel'
    ) echo "<link rel='stylesheet' href='{$config->urls->siteModules}SolCheckIn/styles/checkin.css'>";
  ?>

  <!-- Check-in dev stylesheet -->
  <?php
    if ($page->template->name === 'check-in'
      || $page->template->name === 'check-in-admin'
    ) echo "<link rel='stylesheet' href='{$config->urls->templates}styles/check-in.css'>";
  ?>

  <!-- Glidejs slider script -->
  <?php
  if ($page->template->name === 'home') {
    echo "<script defer src='{$config->urls->templates}scripts/glide.min.js'></script>";
  } ?>

  <!-- Main script -->
  <script defer src="<?php echo $config->urls->templates . $manifest['main.js']; ?>"></script>
</head>

<body class="<?php echo $page->template->name . ($isLowVisionActive ? ' low-vision' : ''); ?>">
  <header class="main-header<?php if ($page->template->name === "home") echo " main-header--home" ?>">
    <!-- Main navigation bar -->
    <nav class="nav-primary<?php if ($page->template->name === "home") echo " nav-primary--home" ?>">
      <!-- Logo -->
      <img
        class="nav-primary__logo"
        src='<?php echo $config->urls->assets ?>images/logo_plain.svg'
        alt="Лого санатория"
      >
      <!-- Navlinks -->
      <?php
        foreach($homepage->and($homepage->children) as $navItem) {
          // Set top-level navigation link classes
          $class='nav-primary__link';
          if($navItem !== $homepage && count($navItem->children) > 0) $class .= ' nav-primary__link--has-subnav';
          if($navItem->id == $page->rootParent->id) $class .= ' nav-primary__link--active';
          // Render top-level navigation links
          echo "<a class='$class' href='$navItem->url'>$navItem->title</a>";
          // Render subnav if exists
          if($navItem !== $homepage && $navItem->children->count) {
            // Subnav
            echo "<nav class='np-subnav'>";
            // Subnav columns
            foreach($navItem->children as $subnavTitle) {
              echo "<div class='np-subnav__column'><h4 class='np-subnav__column-heading'>$subnavTitle->title</h4>";
              // Subnav links
              foreach($subnavTitle->children as $subnavItem) {
                $icon = "";
                if ($subnavItem->template->name === "external-link") {
                  $icon = "<i class='icon-link-ext'></i>";
                }
                $subLinkClass = "np-subnav__link";
                if ($page->id === $subnavItem->id) {
                  $subLinkClass .= " active";
                }
                echo "<a class='$subLinkClass' href='$subnavItem->url'>$subnavItem->title $icon</a>";
              }
              // End subnav columns
              echo "</div>";
            }
            // End subnav
            echo "</nav>";
          }
        }
        $lowVisionText = $_SESSION['lowVision'] ? ' обычная версия' : ' версия для слабовидящих';
      ?>
      <!-- Low-vision button -->
      <a href='<?php echo $page->url ?>' class="nav-primary__link toggle-low-vision hide-sm">
        <i class='icon-low-vision'></i>
        <span class='toggle-low-vision__span'><?php echo $lowVisionText ?></span>
      </a>
    </nav>

    <!-- Homepage logo -->
    <?php if ($page->template->name === "home"): ?>
      <div class="main-header__ugly">
        <img
          class="main-header__logo"
          src='<?php echo $config->urls->assets ?>images/logo_plain_text_vertical.svg'
          alt="Лого санатория"
          width="384"
        >
      </div>
    <?php endif; ?>

    <!-- Navigation menu button (mobile) -->
    <button class="menu-btn" aria-label="Открыть меню навигации">
      <i class="menu-btn__icon icon-menu"></i>
    </button>

  </header>
  <!-- Breadcrumbs (parents pages) -->
  <div class="bread">
  <?php
    foreach($page->parents as $breadItem) {
      echo "<a class='bread__link' href='$breadItem->url'>$breadItem->title</a><span class='bread__separator'>/</span>";
    }
  ?>
    <span class="bread__current"><?php echo $page->title ?></span>
    <!-- Edit in admin panel link -->
    <?php if($page->editable()) echo "<a class='edit-link' href='$page->editUrl'><i class='icon-pencil edit-link__icon'></i><span class='edit-link__text'> Править</span></a>"; ?>
  </div>

  <main class="main">
