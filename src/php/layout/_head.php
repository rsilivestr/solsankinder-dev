<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo $title; ?></title>
  <meta name="description" content="<?php echo $page->summary; ?>" />
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat&display=swap"> -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,200;0,400;0,700;1,200;1,400;1,700&display=swap" > 
  <link rel="stylesheet" type="text/css" href="<?php echo $config->urls->templates; ?>styles/fontello.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $config->urls->templates; ?>styles/styles.min.css" />
  <?php
    if ($page->template->name === 'check-in__user-form'
      || $page->template->name === 'check-in__admin-panel'
    ) {
      echo '<link rel="stylesheet" href="'.$config->urls->siteModules.'SolCheckIn/styles/checkin.css">';
    }
  ?>
  <link rel="icon" type="image/png" href="<?php echo $config->urls->assets; ?>images/favicon.png" />
  <script async src="<?php echo $config->urls->templates; ?>scripts/app.min.js"></script>
</head>
<body class="<?php echo $page->template . ' ' . $isLowVisionActive; ?>">
  <header class="main-header<?php if ($page->template->name === "home") echo " main-header--home" ?>">
    <!-- Main navigation bar -->
    <nav class="nav-primary<?php if ($page->template->name === "home") echo " nav-primary--home" ?>">
      <img 
        class="nav-primary__logo"
        src='<?php echo $config->urls->assets ?>images/logo2.svg'
        alt="Лого санатория"
      >
      <?php
        foreach($homepage->and($homepage->children) as $navItem) {
          // Set top-level navigation link classes
          $class='nav-primary__link';
          if($navItem !== $homepage && count($navItem->children) > 0) $class .= ' has-subnav';
          if($navItem->id == $page->rootParent->id) $class .= ' active';
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
                  $icon = '<svg class="np-subnav__icon" viewBox="0 0 25 25" style="height: 0.8rem"><path d="M 0.05037779,13.876397 V 3.3777271 H 5.2997108 10.549048 v 1.56633 1.56634 H 6.866044 3.1830438 v 7.3659999 7.366 h 7.3660042 7.366 v -3.64067 -3.64067 h 1.56633 1.56633 v 5.207 5.207 H 10.549048 0.05037779 Z m 11.28143021,-0.78277 -1.12153,-1.12153 4.38125,-4.3818499 4.38124,-4.38185 h -2.51853 -2.51853 v -1.56634 -1.56633005 h 5.207 5.207 V 5.3250571 10.574397 h -1.56633 -1.56633 v -2.5608699 -2.56086 l -4.38185,4.3812504 -4.38186,4.3812395 z" style="fill:#303030;stroke-width:0.08466666" /></svg>';
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

    <!-- Navigation menu button (mobile) -->
    <button class="menu-btn hide-lg">
      <i class="menu-btn__icon icon-menu"></i>
    </button>

    <!-- Homepage register button (desktop) -->
    <?php if ($page->template->name === "home"): ?>
      <a class="main-header__register-btn action-btn btn-color-second hide-sm" href="/check-in-form/">Записаться на заезд</a>
    <?php endif; ?>

  </header>
  <!-- Breadcrumbs (parents pages) -->
  <div id="bread" class="bread">
  <?php
    foreach($page->parents as $breadItem) {
      echo "<a class='bread__link' href='$breadItem->url'>$breadItem->title</a><span class='bread__separator'>/</span>";
    }
  ?>
    <span class="bread__current"><?php echo $page->title ?></span>
    <!-- Edit in admin panel link -->
    <?php if($page->editable()) echo "<a class='page-edit-link' href='$page->editUrl'>Править</a>"; ?>
  </div>

  <main class="main">

