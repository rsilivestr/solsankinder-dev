<?php namespace ProcessWire;

$content = "<section class='section section--width_m'>
  <h1>$title</h1>
  <p class='mb-3'><a href='/sitemap.xml'>XML карта сайта</a></p>"
  .renderNavTree($homepage, 4)
."</section>";