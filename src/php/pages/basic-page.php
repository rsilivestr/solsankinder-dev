<?php namespace ProcessWire;

$aClass = "article article--basic";
if ($page->wideContent) $aClass .= " article--wide";

$content = "<article class='$aClass'>
  <h1>$title</h1>
  $page->body
</article>";

// basic-page gallery
if($page->renderGallery && $page->images->count()) {
  $content .= "<div class='basic-gallery'><div class='gallery-thumbs'>";

  foreach($page->images as $image) {
    $thumb = $image->size('160', '120');
    $xs = $image->size('400', '300');
    $sm = $image->size('600', '450');
    $md = $image->size('800', '600');
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
    src='{$page->images->first()->size("600", "450")->url}'
    sizes='(max-width: 400px) 400px,
    (max-width: 624px) 600px,
    (min-width: 625px) 800px'>";
    
  $content .= "<figcaption>" . $page->images->first()->description . "</figcaption></div>";
}
