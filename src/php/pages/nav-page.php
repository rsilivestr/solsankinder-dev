<?php namespace ProcessWire;

$list = '';

foreach ($page->children as $child) {
  $list .= "<li><a href='$child->url'>$child->title</a></li>";
}

$content = "<section class='section section--width_m'>
  <h1>$title</h1>
  <ul class='nav-list'>
    $list
  </ul>
</section>";
