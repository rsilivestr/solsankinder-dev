<?php namespace ProcessWire;

$content = "<section><h1>$title</h1><a href='/sitemap.xml'>XML карта сайта</a>".renderNavTree($homepage, 4)."</section>";