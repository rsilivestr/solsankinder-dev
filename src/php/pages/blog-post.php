<?php namespace ProcessWire;

$content = "<section class='blog-post-full'><h1>$title</h1>$page->body<p>$page->postDate</p>";
// галерея
if($page->renderGallery && $page->images->count()) {
  $content .= "<div class='basic-gallery'><div class='gallery-thumbs'>";
  foreach($page->images as $image) {
    $thumb = $image->height('120');
    $xs = $image->width('400');
    $sm = $image->width('600');
    $md = $image->width('800');
    $content .= "<img 
      class='gallery-thumb lazy'
      src='$image->url'
      data-srcset='$thumb->url 160w,
        $xs->url 400w,
        $sm->url 600w,
        $md->url 800w'
      sizes='(min-width: 320px) 160px'
      data-caption='$image->description'>";
  }
  $content .= "</div><img width='100%'
    class='gallery-current'
    src='{$page->images->first()->width("600")->url}'
    sizes='(max-width: 400px) 400px,
    (max-width: 624px) 600px,
    (min-width: 625px) 800px'>";
    
  if($page->images->first()->description) {
    $content .= "<figcaption>" . $page->images->first()->description . "</figcaption>";
  }
  $content .= "</div>";
}
// $content .= "<h1>";
// if ($page->headline) { $content .= $page->headline; } 
// else {$content .= $page->title;}
// $content .= "</h1>";

// foreach ($page->images as $image) {
//   $content .= "<img
//     class='align_left'
//     src='$image->url'
//   >"; 
// }

$content .= "</section>";