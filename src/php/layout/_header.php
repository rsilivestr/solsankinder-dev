<?php
$headerClass = 'main-header';
$isHomepage = $page->template->name === 'home';
$headerClass .= $isHomepage ? ' main-header--home' : '';
?>

<header class="<?php echo $headerClass; ?>">
  <!-- Main navigation bar -->
  <nav class="nav-primary<?php if ($page->template->name === 'home') {
    echo ' nav-primary--home';
  } ?>">
    <!-- Logo -->
    <img
      class="nav-primary__logo"
      src='<?php echo $config->urls->assets; ?>images/logo_plain.svg'
      alt="Лого санатория"
    >
    <!-- Navlinks -->
    <?php
    foreach ($homepage->and($homepage->children) as $navItem) {
      // Set top-level navigation link classes
      $class = 'nav-primary__link';
      if ($navItem !== $homepage && count($navItem->children) > 0) {
        $class .= ' nav-primary__link--has-subnav';
      }
      if ($navItem->id == $page->rootParent->id) {
        $class .= ' nav-primary__link--active';
      }
      // Top-level navigation links
      echo "<a class='$class' href='$navItem->url'>$navItem->title</a>";
      // Render subnav if exists
      if ($navItem !== $homepage && $navItem->children->count) {
        // Subnav
        echo "<nav class='np-subnav'>";
        // Subnav columns
        foreach ($navItem->children as $subnavTitle) {
          echo "<div class='np-subnav__column'><h4 class='np-subnav__column-heading'>$subnavTitle->title</h4>";
          // Subnav links
          foreach ($subnavTitle->children as $subnavItem) {
            $icon = '';
            if ($subnavItem->template->name === 'external-link') {
              $icon = "<i class='icon-link-ext'></i>";
            }
            $subLinkClass = 'np-subnav__link';
            if ($page->id === $subnavItem->id) {
              $subLinkClass .= ' active';
            }
            echo "<a class='$subLinkClass' href='$subnavItem->url'>$subnavItem->title $icon</a>";
          }
          // End subnav columns
          echo '</div>';
        }
        // End subnav
        echo '</nav>';
      }
    }
    $lowVisionText = $_SESSION['lowVision'] ? ' обычная версия' : ' версия для слабовидящих';
    ?>
    <!-- Low-vision button -->
    <a href='<?php echo $page->url; ?>' class="nav-primary__link toggle-low-vision hide-sm">
      <i class='icon-low-vision'></i>
      <span class='toggle-low-vision__span'><?php echo $lowVisionText; ?></span>
    </a>
  </nav>

  <!-- Homepage logo -->
  <?php if ($page->template->name === 'home'): ?>
    <div class="main-header__ugly">
      <img
        class="main-header__logo"
        src='<?php echo $config->urls->assets; ?>images/logo_plain_text_vertical.svg'
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