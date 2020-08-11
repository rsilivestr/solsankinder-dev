<?php namespace ProcessWire;

$content = "<section><h1>$title</h1><ul class='nav-list'>";

foreach($page->children as $child){
  $content .= "<li><a href='$child->url'>$child->title</a></li>";
}
$content .= "</ul></section>";
