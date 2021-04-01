<div class="bread">
  <?php foreach ($page->parents as $breadItem) {
    echo "<a class='bread__link' href='$breadItem->url'>$breadItem->title</a><span class='bread__separator'>/</span>";
  } ?>
  <span class="bread__current"><?php echo $page->title; ?></span>
  <!-- Edit in admin panel link -->
  <?php if ($page->editable()) {
    echo "<a class='edit-link' href='$page->editUrl'><i class='icon-pencil edit-link__icon'></i><span class='edit-link__text'> Править</span></a>";
  } ?>
</div>